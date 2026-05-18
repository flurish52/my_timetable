<?php

use App\Http\Controllers\DeviceTokenController;
use App\Http\Controllers\PastQuestionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TimeTableController;
use App\Models\Programme;
use App\Models\TimetableSlot;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    $user = Auth::user();
    if ($user) {
        return Inertia::render('PastQuestions', [
            'timetable' => TimetableSlot::with('programme', 'course', 'level')
                ->where('programme_id', $user->programme_id)
                ->where('level_id', $user->leve_id)
                ->get(),
            'user' => $user,
        ]);
    } else {
        return Inertia::render('PastQuestions', []);
    }
});

Route::post('/store-token', [DeviceTokenController::class, 'store'])->name('save-token');

Route::middleware(['auth', 'verified'])->group(function () {
Route::get('/setup', [ProfileController::class, 'createSetupAccount'])->name('setup.index');
Route::put('/setup', [ProfileController::class, 'storeSetUpAccount'])->name('setup.store');
});

Route::middleware(['auth', 'verified', 'profile.setup'])->group(callback: function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        return Inertia::render('Welcome', [
            'timetable' => TimetableSlot::with('programme', 'course')
                ->where('programme_id', $user->programme_id)
                ->where('level_id', $user->level_id)
                ->get(),
            'user' => $user,
            'programme' => Programme::where('id', $user->programme_id)->first()
        ]);
    })->name('dashboard');
    Route::get('/full_timetable', [TimeTableController::class, 'index'])->name('view.full_timetable')
    ->name('view.past_questions_per_course');
    Route::get('/pastquestions', [PastQuestionController::class, 'index'])->name('view.past_questions');
    Route::get('/pastquestions/{course_title}', [PastQuestionController::class, 'showCoursePapers']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
});


Route::middleware('auth')->group(function () {
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__ . '/auth.php';
