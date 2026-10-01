<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Contracts\QuestionExtractorContract::class,
            \App\Services\GeminiQuestionExtractor::class
        );

        $this->app->bind(
            \App\Contracts\NotesQuestionGeneratorContract::class,
            \App\Services\GeminiNotesQuestionGenerator::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        User::observe(UserObserver::class);
    }
}
