<?php

namespace App\Services;

use App\Exceptions\AiServiceRateLimitedException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class GeminiClient
{
    /** Sends parts to Gemini and returns the raw text of the first candidate. */
    public function generate(array $parts, float $temperature = 0.1, int $timeout = 90): string
    {
        $response = Http::timeout($timeout)
            ->withHeaders(['x-goog-api-key' => config('services.gemini.key')])
            ->post(
                sprintf(
                    'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent',
                    config('services.gemini.model')
                ),
                [
                    'contents' => [['parts' => $parts]],
                    'generationConfig' => [
                        'temperature' => $temperature,
                        'responseMimeType' => 'application/json',
                    ],
                ]
            );

        if ($response->status() === 429) {
            Log::warning('Gemini rate limited');
            throw new AiServiceRateLimitedException('Gemini rate limit hit.');
        }

        if ($response->failed()) {
            Log::error('Gemini request failed', [
                'status' => $response->status(),
                'body' => Str::limit($response->body(), 500),
            ]);
            throw new RuntimeException('AI returned HTTP ' . $response->status());
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

        if (! $text) {
            throw new RuntimeException('The AI service returned an empty response.');
        }

        return $text;
    }

    /** Builds inline_data parts for images/PDFs on disk. */
    public function fileParts(array $paths): array
    {
        return array_map(fn (string $path) => [
            'inline_data' => [
                'mime_type' => mime_content_type($path),
                'data' => base64_encode(file_get_contents($path)),
            ],
        ], $paths);
    }

    /** Strips markdown fences and decodes JSON. Throws on malformed output. */
    public function decodeJson(string $text): array
    {
        $cleaned = trim(preg_replace('/^```json|```$/m', '', $text));
        $data = json_decode($cleaned, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($data)) {
            Log::warning('Gemini returned malformed JSON');
            throw new RuntimeException('Could not parse the AI response. Please try again.');
        }

        return $data;
    }
}
