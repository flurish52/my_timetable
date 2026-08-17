<?php

namespace App\Services;

use App\Contracts\QuestionExtractorContract;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GeminiQuestionExtractor implements QuestionExtractorContract
{
    private const PROMPT = <<<'PROMPT'
You are analyzing one or more images that should together make up a single past exam question paper.

First, check whether ALL images belong to the SAME paper — same course/subject, same header, consistent formatting. Students sometimes accidentally upload pages from different papers in one batch. Do NOT merge unrelated papers into one result.

Then extract what you can. Return ONLY valid JSON, no other text, no markdown fences, in this exact structure:
{
  "is_valid_question_paper": true or false,
  "is_single_paper": true or false — false if the images appear to belong to more than one distinct paper/course/subject,
  "confidence": 0.0 to 1.0,
  "rejection_reason": "string or null — explain briefly if is_valid_question_paper or is_single_paper is false",
  "course_guess": "string or null — e.g. 'GST 111 - Use of English' if a course code/title is visible on the page, else null",
  "questions": [
    {
      "question_number": "as shown in the image, or your own sequence if unnumbered — e.g. '1', '13', '1(iv)' for sub-items sharing one stem",
      "section_label": "the section heading exactly as printed, e.g. 'Section A' or 'Section B'. If the paper has no explicit sections, use 'Section A' for every question.",
      "section_instructions": "any printed instructions for this section, e.g. 'Answer four questions'. Include this ONLY on the first question of each section, null otherwise.",
      "question_type": "objective" or "true_false" or "fill_blank" or "short_answer" or "essay",
      "question_text": "the full question, in plain text (use LaTeX-style notation like $x^2$ for any math)",
      "topic_tag": "a short topic label for this question, e.g. 'Parts of Speech' or 'Sentence Structure', or null if unclear",
      "options": ["A) ...", "B) ...", "C) ...", "D) ..."] or null — ONLY populate for question_type \"objective\" or \"true_false\", otherwise null,
      "answer": "the correct option letter for objective/true_false, or the worked-out answer text for short_answer/fill_blank/essay",
      "answer_source": "from_image" or "ai_generated",
      "answer_confidence": "high", "medium", or "low"
    }
  ]
}

Rules:
- If the image(s) are not a question paper (random photo, blank page, unrelated document), set is_valid_question_paper to false, leave questions as an empty array, and explain briefly in rejection_reason.
- If the images belong to more than one distinct paper (different course codes, different subjects, inconsistent headers), set is_single_paper to false, is_valid_question_paper to false, leave questions empty, and explain in rejection_reason. Never merge unrelated papers into one set of questions.
- A single question with lettered/numbered sub-parts sharing one stem (e.g. "1. Another name for parts of speech is... (i)... (ii)... (iii)...") should be extracted as SEPARATE question entries, each with its own question_number like "1(i)", "1(ii)", each carrying the shared context needed to answer it on its own.
- If no answer is visible on the page, work out the correct answer yourself using your own knowledge, but set answer_source to "ai_generated" and be honest about your confidence.
- If some questions are unclear or cut off, still include them but lower your confidence score.
- Never invent an answer you can't actually see on the page and mark it as "from_image" — use "ai_generated" instead.
- If multiple images are provided and they DO belong to the same paper, treat them as consecutive pages and number questions continuously across all pages.
PROMPT;

    public function extract(array $imagePaths): array
    {
        $parts = [['text' => self::PROMPT]];
        foreach ($imagePaths as $path) {
            $parts[] = [
                'inline_data' => [
                    'mime_type' => mime_content_type($path),
                    'data' => base64_encode(file_get_contents($path)),
                ],
            ];
        }

        $response = Http::timeout(60)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post(
                sprintf(
                    'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
                    config('services.gemini.model'),
                    config('services.gemini.key')
                ),
                [
                    'contents' => [
                        [
                            'parts' => $parts,
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'response_mime_type' => 'application/json',
                    ],
                ]
            );

        Log::info('Gemini raw response', [
            'status' => $response->status(),
            'successful' => $response->successful(),
            'failed' => $response->failed(),
            'body' => $response->body(),
            'json' => $response->json(),
        ]);

        if ($response->status() === 429) {
            Log::warning('Gemini rate limited', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \App\Exceptions\AiServiceRateLimitedException(
                'Gemini rate limit hit: ' . $response->body()
            );
        }

        if ($response->failed()) {
            throw new RuntimeException(
                'AI returned HTTP ' . $response->status() . ': ' . $response->body()
            );
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

        if (! $text) {
            throw new RuntimeException('The AI service returned an empty response, Please try again.');
        }

        return $this->parseAndValidate($text);
    }

    private function parseAndValidate(string $text): array
    {
        $cleaned = trim(preg_replace('/^```json|```$/m', '', $text));

        $data = json_decode($cleaned, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($data)) {
            Log::warning('Gemini returned malformed JSON', ['raw' => $text]);
            throw new RuntimeException('Could not parse the AI response. Please try scanning again.');
        }

        foreach (['is_valid_question_paper', 'confidence', 'questions'] as $key) {
            if (! array_key_exists($key, $data)) {
                throw new RuntimeException("AI response missing required field: {$key}");
            }
        }

        // Defense in depth — don't rely solely on the model setting is_single_paper
        // correctly. If it forgot to flag a mismatch but the extracted questions
        // still carry wildly inconsistent topic_tags/course signals, this is where
        // a future heuristic check could go. For now, default missing flag to true.
        $data['is_single_paper'] = $data['is_single_paper'] ?? true;

        return $data;
    }

    private function mimeTypeFor(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'png' => 'image/png',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };
    }
}
