<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamResult;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

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

    public function reports()
    {
        $teacherId = auth()->id();
        
        $stats = [
            'totalExams' => Exam::where('created_by', $teacherId)->count(),
            'totalStudents' => Student::count(),
            'totalAttempts' => DB::table('exam_attempts')
                                   ->join('exams', 'exam_attempts.exam_id', '=', 'exams.id')
                                   ->where('exams.created_by', $teacherId)
                                   ->count(),
            'avgScore' => DB::table('exam_results')
                              ->join('exam_attempts', 'exam_results.attempt_id', '=', 'exam_attempts.id')
                              ->join('exams', 'exam_attempts.exam_id', '=', 'exams.id')
                              ->where('exams.created_by', $teacherId)
                              ->avg(DB::raw('obtained_marks')) ?? 0,
        ];
        
        // REMOVED 'status' column - only basic columns + subqueries
        $recentReports = Exam::where('created_by', $teacherId)
            ->with('examCategory')
            ->select('id', 'title', 'start_time', 'end_time')
            ->selectRaw('
                (SELECT COUNT(*) FROM exam_attempts WHERE exam_attempts.exam_id = exams.id) as attempts_count,
                COALESCE((
                    SELECT AVG((er.obtained_marks * 100.0 / er.total_marks)) 
                    FROM exam_results er 
                    JOIN exam_attempts ea ON er.attempt_id = ea.id 
                    WHERE ea.exam_id = exams.id AND er.total_marks > 0
                ), 0) as avg_score
            ')
            ->latest()
            ->take(10)
            ->get();

        return view('teacher.reports.index', compact('stats', 'recentReports'));
    }
}
