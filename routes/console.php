<?php

use App\Console\Commands\SelectQuestionsOfTheDay;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('timetable:check')->timezone('Africa/Lagos')->everyMinute();

Schedule::call(function () {
    User::where('is_online', true)
        ->where(function ($q) {
            $q->whereNull('last_seen_at')
                ->orWhere('last_seen_at', '<', now()->subMinutes(2));
        })->update(['is_online' => false]);
})->everyMinute();

Schedule::command(SelectQuestionsOfTheDay::class)->dailyAt('00:05');
Schedule::command(SendQuestionOfTheDayPush::class)->dailyAt('00:15');
