<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ExamController as AdminExamController;
use App\Http\Controllers\Admin\ChapterController;
use App\Http\Controllers\Admin\TopicController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboard;
use App\Http\Controllers\Teacher\ExamController as TeacherExamController;
use App\Http\Controllers\Teacher\StudentController as TeacherStudentController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Student\ExamAttemptController;
use App\Http\Controllers\Parent\DashboardController as ParentDashboardController;

// ── Public ───────────────────────────────────────────────────
Route::get('/', fn() => redirect('/login'));
Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout',[LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // ── Subjects ──────────────────────────────────────────────
    Route::resource('subjects', SubjectController::class);

    // ── Questions — AJAX helpers BEFORE resource ──────────────
    Route::get('questions/subjects/{category}', [QuestionController::class, 'getSubjectsByCategory'])->name('questions.subjects');
    Route::get('questions/chapters/{subject}',  [QuestionController::class, 'getChaptersBySubject'])->name('questions.chapters');
    Route::get('questions/topics/{chapter}',    [QuestionController::class, 'getTopicsByChapter'])->name('questions.topics');
    Route::resource('questions', QuestionController::class);
    Route::post('questions/import', [QuestionController::class, 'bulkImport'])->name('questions.import');

    // ── Users ─────────────────────────────────────────────────
    Route::resource('users', UserController::class);

    // ── Chapters — AJAX helper BEFORE resource ────────────────
    Route::get('chapters/by-subject/{subjectId}', [ChapterController::class, 'bySubject'])->name('chapters.by-subject');
    Route::resource('chapters', ChapterController::class);

    // ── Topics — AJAX helper BEFORE resource ──────────────────
    Route::get('topics/by-chapter/{chapterId}', [TopicController::class, 'byChapter'])->name('topics.by-chapter');
    Route::resource('topics', TopicController::class);

    // ── Exam custom routes (ALL before resource) ───────────────

    // Question assignment
    Route::get('exams/{exam}/questions/search',        [AdminExamController::class, 'searchQuestions'])  ->name('exams.questions.search');
    Route::post('exams/{exam}/questions/bulk-add',     [AdminExamController::class, 'bulkAddQuestions']) ->name('exams.questions.bulk-add');
    Route::post('exams/{exam}/questions/{question}',   [AdminExamController::class, 'addQuestion'])      ->name('exams.questions.add');
    Route::delete('exams/{exam}/questions/{question}', [AdminExamController::class, 'removeQuestion'])   ->name('exams.questions.remove');

    // Student enrollment
    Route::post('exams/{exam}/students/enroll-all',    [AdminExamController::class, 'enrollAllStudents'])->name('exams.students.enroll-all');
    Route::post('exams/{exam}/students/{student}',     [AdminExamController::class, 'enrollStudent'])    ->name('exams.students.enroll');
    Route::delete('exams/{exam}/students/{student}',   [AdminExamController::class, 'unenrollStudent'])  ->name('exams.students.unenroll');

    // Results
    Route::post('exams/{exam}/publish-results',                 [AdminExamController::class, 'publishResults'])     ->name('exams.publish-results');
    Route::post('exams/{exam}/recalculate-results',             [AdminExamController::class, 'recalculateResults']) ->name('exams.recalculate-results');
    Route::post('exams/{exam}/results/{result}/publish',        [AdminExamController::class, 'publishSingleResult'])->name('exams.results.publish-single');

    // Attempt detail — BEFORE resource
    Route::get('exams/{exam}/attempts/{attempt}',               [AdminExamController::class, 'showAttempt'])        ->name('exams.attempts.show');

    // Exams resource (after all custom routes)
    Route::resource('exams', AdminExamController::class);
});

/*
|--------------------------------------------------------------------------
| Teacher Routes
|--------------------------------------------------------------------------
*/
Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'role:teacher'])->group(function () {

    Route::get('/dashboard', [TeacherDashboard::class, 'index'])->name('dashboard');
    Route::get('/reports',   [TeacherDashboard::class, 'reports'])->name('reports.index');

    // Question search helper — MUST be before exams/{id}
    Route::get('exams/questions/search', [TeacherExamController::class, 'searchQuestions'])->name('exams.questions.search');

    Route::get('exams',           [TeacherExamController::class, 'index'])->name('exams.index');
    Route::get('exams/create',    [TeacherExamController::class, 'create'])->name('exams.create');
    Route::post('exams',          [TeacherExamController::class, 'store'])->name('exams.store');
    Route::get('exams/{id}',      [TeacherExamController::class, 'show'])->name('exams.show');
    Route::get('exams/{id}/edit', [TeacherExamController::class, 'edit'])->name('exams.edit');
    Route::put('exams/{id}',      [TeacherExamController::class, 'update'])->name('exams.update');
    Route::delete('exams/{id}',   [TeacherExamController::class, 'destroy'])->name('exams.destroy');

    Route::post('exams/{exam}/enroll-students', [TeacherExamController::class, 'enrollStudents'])->name('exams.enroll');
    Route::post('exams/{exam}/publish-results', [TeacherExamController::class, 'publishResults'])->name('exams.publish');

    // Attempt detail — BEFORE exams/{id} wildcard
    Route::get('exams/{exam}/attempts/{attempt}', [TeacherExamController::class, 'showAttempt'])->name('exams.attempts.show');

    Route::get('students',      [TeacherStudentController::class, 'index'])->name('students.index');
    Route::get('students/{id}', [TeacherStudentController::class, 'show'])->name('students.show');
});

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/
Route::prefix('student')->name('student.')->middleware(['auth', 'role:student'])->group(function () {

    Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');

    Route::get('exams',                             [ExamAttemptController::class, 'index'])->name('exams.index');
    Route::get('exams/{exam}/instructions',         [ExamAttemptController::class, 'instructions'])->name('exams.instructions');
    Route::post('exams/{exam}/start',               [ExamAttemptController::class, 'start'])->name('exams.start');

    Route::get('exams/{attemptToken}/attempt',      [ExamAttemptController::class, 'attempt'])->name('exams.attempt');
    Route::post('exams/{attemptToken}/save-answer', [ExamAttemptController::class, 'saveAnswer'])->name('exams.save-answer');
    Route::post('exams/{attemptToken}/track-time',  [ExamAttemptController::class, 'trackTime'])->name('exams.track-time');
    Route::get('exams/{attemptToken}/status',       [ExamAttemptController::class, 'getStatus'])->name('exams.status');
    Route::post('exams/{attemptToken}/submit',      [ExamAttemptController::class, 'submit'])->name('exams.submit');

    Route::get('results', [StudentDashboard::class, 'results'])->name('results');
    Route::get('profile', [StudentDashboard::class, 'profile'])->name('profile');
});

/*
|--------------------------------------------------------------------------
| Parent Routes
|--------------------------------------------------------------------------
*/
Route::prefix('parent')->name('parent.')->middleware(['auth', 'role:parent'])->group(function () {
    Route::get('/dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/children/{student}/performance', [ParentDashboardController::class, 'performance'])->name('children.performance');
});
