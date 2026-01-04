<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ExamController as AdminExamController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboard;
use App\Http\Controllers\Teacher\ExamController as TeacherExamController;
use App\Http\Controllers\Teacher\StudentController as TeacherStudentController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Student\ExamAttemptController;
use App\Http\Controllers\Parent\DashboardController as ParentDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/', function () {
    return redirect('/login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    
    // ✅ Subject Management
    Route::resource('subjects', SubjectController::class);
    
    // ✅ AJAX routes for dynamic dropdowns (MUST be before resource routes)
    Route::get('questions/subjects/{category}', [QuestionController::class, 'getSubjectsByCategory'])->name('questions.subjects');
    Route::get('questions/chapters/{subject}', [QuestionController::class, 'getChaptersBySubject'])->name('questions.chapters');
    Route::get('questions/topics/{chapter}', [QuestionController::class, 'getTopicsByChapter'])->name('questions.topics');
    
    // Question Management (Resource routes)
    Route::resource('questions', QuestionController::class);
    Route::post('questions/import', [QuestionController::class, 'bulkImport'])->name('questions.import');
    
    // User Management
    Route::resource('users', UserController::class);
    
    // Exam Management
    Route::resource('exams', AdminExamController::class);
});

/*
|--------------------------------------------------------------------------
| Teacher Routes
|--------------------------------------------------------------------------
*/
Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'role:teacher'])->group(function () {
    // Dashboard & Reports
    Route::get('/dashboard', [TeacherDashboard::class, 'index'])->name('dashboard');
    Route::get('/reports', [TeacherDashboard::class, 'reports'])->name('reports.index');
    
    // Exam Management
    // AJAX route for searching questions (must come before resource routes)
    Route::get('exams/questions/search', [TeacherExamController::class, 'searchQuestions'])->name('exams.questions.search');
    
    // Exam CRUD Routes
    Route::get('exams', [TeacherExamController::class, 'index'])->name('exams.index');
    Route::get('exams/create', [TeacherExamController::class, 'create'])->name('exams.create');
    Route::post('exams', [TeacherExamController::class, 'store'])->name('exams.store');
    Route::get('exams/{id}', [TeacherExamController::class, 'show'])->name('exams.show');
    Route::get('exams/{id}/edit', [TeacherExamController::class, 'edit'])->name('exams.edit');
    Route::put('exams/{id}', [TeacherExamController::class, 'update'])->name('exams.update');
    Route::delete('exams/{id}', [TeacherExamController::class, 'destroy'])->name('exams.destroy');
    
    // Additional Exam Actions
    Route::post('exams/{exam}/enroll-students', [TeacherExamController::class, 'enrollStudents'])->name('exams.enroll');
    Route::post('exams/{exam}/publish-results', [TeacherExamController::class, 'publishResults'])->name('exams.publish');
    
    // Student Management
    Route::get('students', [TeacherStudentController::class, 'index'])->name('students.index');
    Route::get('students/{id}', [TeacherStudentController::class, 'show'])->name('students.show');
});

/*
|--------------------------------------------------------------------------
| Student Routes
|--------------------------------------------------------------------------
*/
Route::prefix('student')->name('student.')->middleware(['auth', 'role:student'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');
    
    // Exam Routes
    Route::get('exams', [ExamAttemptController::class, 'index'])->name('exams.index');
    Route::get('exams/{exam}/instructions', [ExamAttemptController::class, 'instructions'])->name('exams.instructions');
    Route::post('exams/{exam}/start', [ExamAttemptController::class, 'start'])->name('exams.start');
    
    // Exam Attempt Routes
    Route::get('exams/{attemptToken}/attempt', [ExamAttemptController::class, 'attempt'])->name('exams.attempt');
    Route::post('exams/{attemptToken}/save-answer', [ExamAttemptController::class, 'saveAnswer'])->name('exams.save-answer');
    Route::post('exams/{attemptToken}/track-time', [ExamAttemptController::class, 'trackTime'])->name('exams.track-time');
    Route::get('exams/{attemptToken}/status', [ExamAttemptController::class, 'getStatus'])->name('exams.status');
    Route::post('exams/{attemptToken}/submit', [ExamAttemptController::class, 'submit'])->name('exams.submit');
    
    // Results and Profile
    Route::get('results', [StudentDashboard::class, 'results'])->name('results');
    Route::get('profile', [StudentDashboard::class, 'profile'])->name('profile');
});

/*
|--------------------------------------------------------------------------
| Parent Routes
|--------------------------------------------------------------------------
*/
Route::prefix('parent')->name('parent.')->middleware(['auth', 'role:parent'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');
    
    // Children Performance
    Route::get('/children/{student}/performance', [ParentDashboardController::class, 'performance'])->name('children.performance');
});
