<?php

namespace App\Services;

use App\Jobs\GeneratePastQuestionPdfJob;
use App\Models\PastQuestion;
use Illuminate\Support\Facades\Storage;

class PastQuestionService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function resolvePdf(PastQuestion $pastQuestion): ?string
    {
        $path = $pastQuestion->source_file;

        // File exists
        if ($path && Storage::disk('public')->exists($path)) {
            return Storage::url($path);
        }

        // File missing or no source_file in DB
        GeneratePastQuestionPdfJob::dispatch($pastQuestion->id);

        return null;
    }
}
