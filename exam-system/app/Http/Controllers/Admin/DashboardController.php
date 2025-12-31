<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Student;
use App\Models\ExamAttempt;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_students' => Student::count(),
            'total_teachers' => User::whereHas('role', function($q) {
                $q->where('name', 'teacher');
            })->count(),
            'total_exams' => Exam::count(),
            'total_questions' => Question::count(),
            'ongoing_exams' => Exam::where('start_time', '<=', now())
                                   ->where('end_time', '>=', now())
                                   ->count(),
            'total_attempts' => ExamAttempt::count(),
        ];

        $recentExams = Exam::with('examCategory')->latest()->take(5)->get();
        $recentAttempts = ExamAttempt::with(['student.user', 'exam'])
                                     ->latest()
                                     ->take(10)
                                     ->get();

        return view('admin.dashboard', compact('stats', 'recentExams', 'recentAttempts'));
    }
}
