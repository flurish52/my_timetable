<?php

use App\Http\Controllers\CourseOfferingController;
use App\Http\Controllers\DeviceTokenController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PastQuestionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionAttemptController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\TimeTableController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
Route::get('/', [PastQuestionController::class, 'index'])->name('pastquestions.get');
Route::get('/pastquestions', [PastQuestionController::class, 'index'])->name('view.past_questions');
Route::get('/pastquestions/{slug}', [PastQuestionController::class, 'showCoursePapers'])
    ->name('view.past_questions_per_course');

Route::post('/store-token', [DeviceTokenController::class, 'store'])->name('save-token');

/*
|--------------------------------------------------------------------------
| Shared onboarding routes (any authenticated role, before profile setup)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/setup', [ProfileController::class, 'createSetupAccount'])->name('setup.index');
    Route::put('/setup', [ProfileController::class, 'storeSetUpAccount'])->name('setup.store');
    Route::get('/schools/{slug}', [SchoolController::class, 'show'])->name('schools.show');
    Route::post('/schools/{slug}/waitlist', [SchoolController::class, 'join'])->name('schools.waitlist.join');
    Route::get('/leaderboard', [SchoolController::class, 'leaderboard'])->name('schools.leaderboard');
});

/*
|--------------------------------------------------------------------------
| Student routes
| Accessible by: student, contributor, admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'profile.setup', 'role:admin,student,contributor'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/full_timetable', [TimeTableController::class, 'viewFullTimetable'])->name('view.full_timetable');

    Route::get('/pastquestions/{slug}/{question_slug}', [PastQuestionController::class, 'startPractice'])
        ->name('view.start_practice');

    Route::post('/practice/submit', [QuestionAttemptController::class, 'store'])
        ->name('exam.submit');

    Route::get('/practice/{past_question}/start', [PastQuestionController::class, 'practice'])
        ->name('view.practice');

    Route::get('/course_offerings', [CourseOfferingController::class, 'studentCoursesOfferings'])
        ->name('course_offering.student_offerings');

    Route::post('/course_offering', [CourseOfferingController::class, 'storeStudentElective'])
        ->name('course_offering.store');

    Route::delete('/course_offering/{course_offering}', [CourseOfferingController::class, 'destroy'])
        ->name('course_offering.destroy');

    Route::get('/profile', [ProfileController::class, 'update'])->name('profile.edit');
});

/*
|--------------------------------------------------------------------------
| Lecturer routes
| Accessible by: lecturer, admin (contributor/student excluded)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'profile.setup', 'role:admin,lecturer'])->group(function () {
    // Add lecturer-specific routes here as they're built
});

/*
|--------------------------------------------------------------------------
| Contributor routes
| Accessible by: lecturer, admin (contributor/student excluded)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'profile.setup', 'role:admin,contributor'])->group(function () {

    Route::get('/contributor/past-questions', [PastQuestionController::class, 'contributorIndex'])
        ->name('past-questions.index');

    Route::get('/contributor/past-questions/create', [PastQuestionController::class, 'create'])
        ->name('past-questions.create');

    Route::post('/contributor/past-questions', [PastQuestionController::class, 'store'])
        ->name('past-questions.store');

    Route::get('/contributor/past-questions/{pastQuestion}/build', [PastQuestionController::class, 'build'])
        ->name('past-questions.build');

    Route::post('/contributor/past-questions/{pastQuestion}/questions', [QuestionController::class, 'store'])
        ->name('past-questions.questions.store');

    Route::get('/contributor/past-questions/{pastQuestion}', [PastQuestionController::class, 'show'])
        ->name('past-question.show');;

    Route::post('/contributor/past-questions/{pastQuestion}/import', [QuestionController::class, 'importQuestions'])
        ->name('past-questions.import');

    Route::get('/contributor/past-questions/import-template', [QuestionController::class, 'downloadImportTemplate'])
        ->name('past-questions.import-template');

    Route::patch('/contributor/{pastQuestion}/publish', [PastQuestionController::class, 'togglePublish'])
        ->name('past-questions.publish');

//    CourseOfferingCreation
    Route::get('/contributor/course_offerings', [CourseOfferingController::class, 'index'])
        ->name('course_offerings.index');

    Route::get('/contributor/course_offerings/create', [CourseOfferingController::class, 'create'])
        ->name('course_offerings.create');

    Route::put('/contributor/course_offerings/{courseOffering}/update', [CourseOfferingController::class, 'update'])
        ->name('course_offerings.update');

    Route::post('/contributor/course_offerings', [CourseOfferingController::class, 'store'])
        ->name('course_offerings.store');

    //Timetable creation/building urls
    Route::get('/contributor/timetable', [TimetableController::class, 'index'])
        ->name('timetable.index');

    Route::get('/contributor/timetable/build', [TimetableController::class, 'create'])
        ->name('timetable.create');

    Route::put('/contributor/timetable/{timetable}/update', [TimetableController::class, 'update'])
        ->name('timetable.update');

    Route::post('/contributor/timetable', [TimetableController::class, 'store'])
        ->name('timetable.store');

});

/*
|--------------------------------------------------------------------------
| Admin routes
| Accessible by: admin only
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return "Admin Dashboard";
    });
});

/*
|--------------------------------------------------------------------------
| Profile management (any authenticated user)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
