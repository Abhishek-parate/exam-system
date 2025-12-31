<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\ExamResult;

class DashboardController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;

        // Available exams (enrolled, not attempted, within time)
        $availableExams = $student->enrolledExams()
            ->where('is_active', true)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->whereDoesntHave('attempts', function($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->with('examCategory')
            ->get();

        // Upcoming exams
        $upcomingExams = $student->enrolledExams()
            ->where('is_active', true)
            ->where('start_time', '>', now())
            ->with('examCategory')
            ->take(5)
            ->get();

        // Completed attempts with results
        $completedAttempts = $student->examAttempts()
            ->whereIn('status', ['submitted', 'auto_submitted'])
            ->with(['exam.examCategory', 'result'])
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'total_enrolled' => $student->enrolledExams()->count(),
            'total_completed' => $completedAttempts->count(),
            'available_now' => $availableExams->count(),
            'upcoming' => $upcomingExams->count(),
        ];

        return view('student.dashboard', compact('stats', 'availableExams', 'upcomingExams', 'completedAttempts'));
    }
}
