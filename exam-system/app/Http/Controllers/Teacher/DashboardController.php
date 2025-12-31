<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Student;

class DashboardController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();

        $stats = [
            'total_exams' => Exam::where('created_by', $teacherId)->count(),
            'ongoing_exams' => Exam::where('created_by', $teacherId)
                                  ->where('start_time', '<=', now())
                                  ->where('end_time', '>=', now())
                                  ->count(),
            'total_students' => Student::count(),
            'total_attempts' => ExamAttempt::whereHas('exam', function($q) use ($teacherId) {
                $q->where('created_by', $teacherId);
            })->count(),
        ];

        $recentExams = Exam::where('created_by', $teacherId)
                          ->with('examCategory')
                          ->latest()
                          ->take(5)
                          ->get();

        return view('teacher.dashboard', compact('stats', 'recentExams'));
    }
}
