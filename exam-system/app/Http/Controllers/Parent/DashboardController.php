<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;

class DashboardController extends Controller
{
    public function index()
    {
        $parent = auth()->user()->parent;
        $children = $parent->students()->with(['user', 'examAttempts.exam', 'results'])->get();

        return view('parent.dashboard', compact('children'));
    }

    public function performance(Student $student)
    {
        // Verify parent has access to this student
        $parent = auth()->user()->parent;
        
        if (!$parent->students->contains($student)) {
            abort(403, 'Unauthorized access');
        }

        $results = $student->results()
                          ->with(['exam.examCategory', 'subjectWiseResults.subject'])
                          ->where('is_published', true)
                          ->latest()
                          ->get();

        $stats = [
            'total_exams' => $student->examAttempts()->count(),
            'completed' => $student->examAttempts()->whereIn('status', ['submitted', 'auto_submitted'])->count(),
            'average_score' => $results->avg('percentage') ?? 0,
            'highest_rank' => $results->min('rank') ?? 'N/A',
        ];

        return view('parent.performance', compact('student', 'results', 'stats'));
    }
}
