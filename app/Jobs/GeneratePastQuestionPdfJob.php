<?php

namespace App\Jobs;

use App\Models\PastQuestion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GeneratePastQuestionPdfJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct( public int $pastQuestionId )
    {
        //
    }

    /**
     * Execute the job.
     */
public function handle(): void
    {
        $pastQuestion = PastQuestion::with([
            'course',
            'semester',
            'school',
            'sections',
            'questions.children',
            'questions.options',
            'questions.answers',
            'questions.media'
        ])->findOrFail($this->pastQuestionId);

        $hasQuestions = $pastQuestion->sections()
            ->where(function ($query) {
                $query->whereHas('questions')
                    ->orWhereHas('groups.questions');
            })
            ->exists();

        if (! $hasQuestions) {
            return;
        }

        $pdf = Pdf::loadView('pdf.past_question', [
            'pastQuestion' => $pastQuestion,
            'domain'       => parse_url(config('app.url'), PHP_URL_HOST),
            'siteName'     => config('app.name'),
            'tagline'      => 'Download verified past questions. Study smarter. Score higher.',
            'cta'          => 'Get more past questions and exam resources',
        ])->setPaper('a4');

        $path = 'past_questions/'
            . Str::slug($pastQuestion->school->acronym ?? $pastQuestion->school->name  ) . '/'
            . Str::slug($pastQuestion->course->code) . '/'
            . Str::slug($pastQuestion->course->code). '- '
            . str_replace('/', '-', $pastQuestion->session)
            . $pastQuestion->semester->code
            . ' Past question'
            . '.pdf';

        Storage::disk('public')->put($path, $pdf->output());

        $pastQuestion->update([
            'source_file'      => $path,
            'pdf_generated_at' => now(),
        ]);
    }
}


