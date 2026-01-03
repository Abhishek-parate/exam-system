<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Dashboard page
     */
    public function index()
    {
        $student = Auth::user()->student;

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

    /**
     * Results page - list all completed exam attempts with results
     */
    public function results()
    {
        $student = Auth::user()->student;
        $attempts = $student->examAttempts()
            ->whereIn('status', ['submitted', 'auto_submitted'])
            ->with(['exam.examCategory', 'result'])
            ->latest()
            ->paginate(10);

        return view('student.results', compact('attempts', 'student'));
    }

    /**
     * Profile page - student information
     */
    public function profile()
    {
        $student = Auth::user()->student;
        $user = Auth::user();

        // Pre-compute stats for performance and to avoid database errors
        $stats = [
            'enrolledExamsCount' => $student->enrolledExams()->count(),
            'examAttemptsCount' => $student->examAttempts()->count(),
            'publishedResultsCount' => $student->results()->where('is_published', true)->count(),
            'avgPercentage' => $student->results()
                ->where('is_published', true)
                ->get()
                ->avg(function ($result) {
                    return $result->max_marks > 0 ? ($result->total_marks / $result->max_marks * 100) : 0;
                }) ?? 0,
        ];

        return view('student.profile', compact('student', 'user', 'stats'));
    }
}
