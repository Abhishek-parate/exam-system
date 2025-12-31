<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\UserController; // ✅ ADD THIS LINE
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboard;
use App\Http\Controllers\Teacher\ExamController as TeacherExamController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Student\ExamAttemptController;
use App\Http\Controllers\Parent\DashboardController as ParentDashboardController;

// Public Routes
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    
    // Question Management
    Route::resource('questions', QuestionController::class);
    Route::post('questions/import', [QuestionController::class, 'bulkImport'])->name('questions.import');
    Route::get('questions/subjects/{category}', [QuestionController::class, 'getSubjectsByCategory']);
    Route::get('questions/chapters/{subject}', [QuestionController::class, 'getChaptersBySubject']);
    Route::get('questions/topics/{chapter}', [QuestionController::class, 'getTopicsByChapter']);
    
    // User Management
    Route::resource('users', UserController::class);
    
    // Exam Management
    Route::resource('exams', \App\Http\Controllers\Admin\ExamController::class);
});

// Teacher Routes
Route::prefix('teacher')->name('teacher.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [TeacherDashboard::class, 'index'])->name('dashboard');
    Route::resource('exams', TeacherExamController::class);
    Route::post('exams/{exam}/enroll-students', [TeacherExamController::class, 'enrollStudents'])->name('exams.enroll');
    Route::post('exams/{exam}/publish-results', [TeacherExamController::class, 'publishResults'])->name('exams.publish');
    Route::get('exams/questions/search', [TeacherExamController::class, 'searchQuestions'])->name('exams.questions.search');
});

// Student Routes
Route::prefix('student')->name('student.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');
    Route::get('exams', [ExamAttemptController::class, 'index'])->name('exams.index');
    Route::get('exams/{exam}/instructions', [ExamAttemptController::class, 'instructions'])->name('exams.instructions');
    Route::post('exams/{exam}/start', [ExamAttemptController::class, 'start'])->name('exams.start');
    Route::get('exams/{attemptToken}/attempt', [ExamAttemptController::class, 'attempt'])->name('exams.attempt');
    Route::post('exams/{attemptToken}/save-answer', [ExamAttemptController::class, 'saveAnswer'])->name('exams.save-answer');
    Route::post('exams/{attemptToken}/track-time', [ExamAttemptController::class, 'trackTime'])->name('exams.track-time');
    Route::get('exams/{attemptToken}/status', [ExamAttemptController::class, 'getStatus'])->name('exams.status');
    Route::post('exams/{attemptToken}/submit', [ExamAttemptController::class, 'submit'])->name('exams.submit');
});

// Parent Routes - FIXED
Route::prefix('parent')->name('parent.')->middleware(['auth', 'role:parent'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Parent\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/children/{student}/performance', [\App\Http\Controllers\Parent\DashboardController::class, 'performance'])->name('children.performance');
});

