<?php

use App\Http\Controllers\CourseOfferingController;
use App\Http\Controllers\DeviceTokenController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PastQuestionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionAttemptController;
use App\Http\Controllers\TimeTableController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PastQuestionController::class, 'index'])->name('pastquestions.get');
    Route::get('/pastquestions', [PastQuestionController::class, 'index'])->name('view.past_questions');
    Route::get('/pastquestions/{slug}', [PastQuestionController::class, 'showCoursePapers'])
        ->name('view.past_questions_per_course');

Route::post('/store-token', [DeviceTokenController::class, 'store'])->name('save-token');

Route::middleware(['auth', 'verified'])->group(function () {
Route::get('/setup', [ProfileController::class, 'createSetupAccount'])->name('setup.index');
Route::put('/setup', [ProfileController::class, 'storeSetUpAccount'])->name('setup.store');
});

Route::middleware(['auth', 'verified', 'profile.setup'])->group(callback: function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
//    Route::get('/storage/{file}');
    Route::get('/full_timetable', [TimeTableController::class, 'index'])->name('view.full_timetable');

    Route::get('/pastquestions/{slug}/{question_slug}', [PastQuestionController::class, 'startPractice'])
        ->name('view.start_practice');

    Route::post('/practice/submit', [QuestionAttemptController::class, 'store'])
        ->name('exam.submit');

    Route::get('/practice/{past_question}/start', [PastQuestionController::class, 'practice'])
        ->name('view.practice');

    Route::get('/course_offerings', [CourseOfferingController::class, 'index'])
        ->name('course_offering.index');

    Route::post('/course_offering', [CourseOfferingController::class, 'store'])
        ->name('course_offering.store');

    Route::delete('/course_offering/{course_offering}', [CourseOfferingController::class, 'destroy'])
        ->name('course_offering.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
});

Route::middleware('auth')->group(function () {
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
