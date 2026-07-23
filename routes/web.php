<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ContributorController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseOfferingController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DeviceTokenController;
use App\Http\Controllers\HeartbeatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PastQuestionController;
use App\Http\Controllers\PastQuestionImportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgrammeLevelSemesterController;
use App\Http\Controllers\QuestionAttemptController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\RoleRequestController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SiteSettingController;
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
    Route::post('/heartbeat', [HeartbeatController::class, 'store'])->name('heartbeat');
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
    Route::get('/contributors', [ContributorController::class, 'index'])->name('contributors.public');
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

    Route::get('/become-contributor', [RoleRequestController::class, 'create'])
        ->name('role-requests.create');
    Route::post('/become-contributor', [RoleRequestController::class, 'store'])
        ->name('role-requests.store');
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
| Accessible by: contributor, admin (lecturer/student excluded)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'profile.setup', 'role:admin,contributor'])->group(function () {

    Route::get('/contributor', [ContributorController::class, 'dashboard'])
        ->name('contributor.dashboard');

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

//        IMPORT SECTIONS

    Route::get('/contributor/past-questions/{pastQuestion}/import', [PastQuestionImportController::class, 'index'])
        ->name('past-questions.import');

    Route::post('/contributor/past-questions/{pastQuestion}/import', [PastQuestionImportController::class, 'importQuestions'])
        ->name('past-questions.import-excel');

    Route::post('/contributor/past-questions/{pastQuestion}/import2', [PastQuestionImportController::class, 'importDocument'])
        ->name('past-questions.import-document');



    Route::get('storage/contributor/past-questions/import-template', [PastQuestionImportController::class, 'downloadImportTemplate'])
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


    Route::get('/contributor/semester', [ProgrammeLevelSemesterController::class, 'contributorShow']);
    Route::put('/contributor/semester', [ProgrammeLevelSemesterController::class, 'contributorUpdate']);

    Route::get('/contributor/setting', [SiteSettingController::class, 'contributorIndex'])
        ->name('settings.contributor.index');

    Route::put('/contributor/setting', [SiteSettingController::class, 'contributorUpdateCurrentSemester'])
        ->name('settings.contributor.update-current-semester');

});

/*
|--------------------------------------------------------------------------
| Admin routes
| Accessible by: admin only
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/papers', [AdminController::class, 'papers'])->name('papers.index');
    Route::patch('/papers/{paper}/approve', [AdminController::class, 'approvePaper'])->name('papers.approve');
    Route::patch('/papers/{paper}/reject', [AdminController::class, 'rejectPaper'])->name('papers.reject');
    Route::patch('/papers/{paper}/unpublish', [AdminController::class, 'unpublishPaper'])->name('papers.unpublish');

    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/{user}/history', [AdminController::class, 'contributorHistory'])->name('users.history');
    Route::patch('/users/{user}/role', [AdminController::class, 'updateUserRole'])
        ->name('users.updateRole');

    // role requests from earlier
    Route::get('/role-requests', [RoleRequestController::class, 'index'])->name('role-requests.index');
    Route::patch('/role-requests/{roleRequest}/approve', [RoleRequestController::class, 'approve'])->name('role-requests.approve');
    Route::patch('/role-requests/{roleRequest}/reject', [RoleRequestController::class, 'reject'])
        ->name('role-requests.reject');

    Route::patch('/users/bulk-role', [AdminController::class, 'bulkUpdateRole'])->name('users.bulkUpdateRole');

    Route::get('/admin/settings', [SiteSettingController::class, 'adminIndex'])
        ->name('settings.index');

    Route::put('/admin/settings/current-semester', [SiteSettingController::class, 'adminUpdateCurrentSemester'])
        ->name('settings.update-current-semester');


    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');

    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');

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
