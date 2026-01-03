<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $stats = [
            'upcoming_exams' => Exam::where('status', 'upcoming')->count(),
            'ongoing_exams'  => Exam::where('status', 'ongoing')->count(),
            'completed_exams'=> Exam::where('status', 'completed')->count(),
            'total_attempts' => ExamAttempt::where('student_id', $user->id)->count(),
        ];

        $nextExams = Exam::whereIn('status', ['upcoming', 'ongoing'])
            ->orderBy('start_datetime')
            ->take(5)
            ->get();

        $recentAttempts = ExamAttempt::with('exam')
            ->where('student_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('student.dashboard', compact('stats', 'nextExams', 'recentAttempts'));
    }

}
