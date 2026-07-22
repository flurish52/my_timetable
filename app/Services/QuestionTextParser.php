<?php

namespace App\Services;

/**
 * Parses the Q:/Type:/A) B).../Answer:/Tip: block format used by
 * ImportQuestionsStep.vue, so paste-import, Word import, and PDF import
 * all go through one parsing implementation instead of three.
 *
 * Blocks are separated by a blank line, optionally with a "---" divider.
 */
class QuestionTextParser
{
    /**
     * @return array<int, array{question:string,type:string,options:array,answer:?string,tip:?string,errors:array}>
     */
    /**
     * @param bool $strict When true, a block with no "Q:" line is rejected
     *   instead of falling back to treating its first line as the question.
     *   Use strict mode for blind document uploads (Word/PDF) where the file
     *   could contain anything; the lenient fallback is only safe for the
     *   paste flow, where the user is typing on purpose and sees a live preview.
     */
    public function parse(string $text, bool $strict = false): array
    {
        $blocks = preg_split('/\n\s*(?:-{3,})?\s*\n/', trim($text)) ?: [];

        return collect($blocks)
            ->map(fn ($block) => trim($block))
            ->filter(fn ($block) => $block !== '')
            ->map(fn ($block) => $this->parseBlock($block, $strict))
            ->filter()
            ->values()
            ->all();
    }

    /** Only the blocks that pass validation — safe to hand to the DB layer. */
    public function parseValid(string $text, bool $strict = false): array
    {
        return array_values(array_filter(
            $this->parse($text, $strict),
            fn ($q) => empty($q['errors'])
        ));
    }

    private function parseBlock(string $block, bool $strict = false): ?array
    {
        $lines = array_values(array_filter(array_map('trim', explode("\n", $block)), fn ($l) => $l !== ''));
        if (empty($lines)) {
            return null;
        }

        $result = [
            'question' => '',
            'type' => 'short',
            'options' => [],
            'answer' => null,
            'tip' => null,
            'errors' => [],
        ];

        foreach ($lines as $line) {
            if (preg_match('/^Q:\s*(.+)$/i', $line, $m)) {
                $result['question'] = trim($m[1]);
            } elseif (preg_match('/^Type:\s*(mcq|short|essay|tf)\s*$/i', $line, $m)) {
                $result['type'] = strtolower($m[1]);
            } elseif (preg_match('/^([A-D])\)\s*(.+)$/i', $line, $m)) {
                $result['options'][] = trim($m[2]);
            } elseif (preg_match('/^Answer:\s*(.+)$/i', $line, $m)) {
                $result['answer'] = trim($m[1]);
            } elseif (preg_match('/^Tip:\s*(.+)$/i', $line, $m)) {
                $result['tip'] = trim($m[1]);
            } elseif (! $strict && $result['question'] === '') {
                // tolerate a bare first line with no "Q:" prefix — paste flow only
                $result['question'] = $line;
            }
        }

        if ($result['question'] === '') {
            $result['errors'][] = 'Missing question text.';
        }

        if ($result['type'] === 'mcq') {
            if (count($result['options']) < 2) {
                $result['errors'][] = 'MCQ needs at least 2 options.';
            }
            if (empty($result['answer'])) {
                $result['errors'][] = 'MCQ needs an answer letter.';
            }
        }

        if ($result['type'] === 'tf' && $result['answer'] && ! preg_match('/^(true|false)$/i', $result['answer'])) {
            $result['errors'][] = 'True/False answer must be True or False.';
        }

        return $result;
    }
}
