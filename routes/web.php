<?php

use App\Http\Controllers\PastQuestionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TimeTableController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


    Route::get('/full_timetable', [TimeTableController::class, 'index'])->name('view.full_timetable');
    Route::get('/pastquestions', [PastQuestionController::class, 'index'])->name('view.past_questions');
    Route::get('/pastquestions/{course_title}', [PastQuestionController::class, 'showCoursePapers'])
        ->name('view.past_questions_per_course');



Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::post('/store-token', function () {
    $file = storage_path('app/fcm_tokens.json');

    $tokens = file_exists($file)
        ? json_decode(file_get_contents($file), true)
        : [];

    $newToken = request('token');

    if (!in_array($newToken, $tokens)) {
        $tokens[] = $newToken;
    }

    file_put_contents($file, json_encode($tokens));

    return response()->json(['ok' => true]);
});

require __DIR__.'/auth.php';
