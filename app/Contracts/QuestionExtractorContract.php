<?php

namespace App\Contracts;

interface QuestionExtractorContract
{
    /**
     * Analyze one or more page images of a past question paper and
     * return the extracted structure as a plain PHP array matching
     * the agreed schema (see GeminiQuestionExtractor::PROMPT).
     *
     * @param  array<int, string>  $imagePaths  Absolute paths to temp-stored images, in page order.
     * @return array{
     *     is_valid_question_paper: bool,
     *     confidence: float,
     *     rejection_reason: ?string,
     *     course_guess: ?string,
     *     questions: array<int, array>
     * }
     */
    public function extract(array $imagePaths): array;
}
