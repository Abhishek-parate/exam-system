<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of students
     */
    public function index(Request $request)
    {
        // Only load 'user' relationship (class is a string column, not relationship)
        $query = Student::with(['user']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            })->orWhere('enrollment_number', 'LIKE', "%{$search}%")
              ->orWhere('class', 'LIKE', "%{$search}%");
        }

        // Filter by class (string column)
        if ($request->filled('class')) {
            $query->where('class', $request->class);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $students = $query->latest()->paginate(15);

        // Get distinct class values for filter dropdown (no ClassModel needed)
        $classes = Student::select('class')
            ->whereNotNull('class')
            ->where('class', '!=', '')
            ->distinct()
            ->orderBy('class')
            ->pluck('class');

        return view('teacher.students.index', compact('students', 'classes'));
    }

    /**
     * Display the specified student
     */
    public function show($id)
    {
        // Only load existing relationships
        $student = Student::with([
            'user',
            'examAttempts.exam',
            'results.exam',
            'parents.user' // Load parents if needed
        ])->findOrFail($id);

        // Calculate statistics (safe handling)
        $statistics = [
            'total_exams' => $this->getExamAttemptsCount($student, 'all'),
            'completed_exams' => $this->getExamAttemptsCount($student, 'completed'),
            'in_progress' => $this->getExamAttemptsCount($student, 'in_progress'),
            'average_score' => $this->calculateAverageScore($student),
        ];

        // Get recent exam attempts (safe handling)
        $recentAttempts = $this->getRecentAttempts($student);

        return view('teacher.students.show', compact('student', 'statistics', 'recentAttempts'));
    }

    /**
     * Get exam attempts count safely
     */
    private function getExamAttemptsCount($student, $type)
    {
        try {
            if ($type === 'all') {
                return $student->examAttempts()->count();
            } elseif ($type === 'completed') {
                return $student->examAttempts()
                    ->whereIn('status', ['submitted', 'auto_submitted'])
                    ->count();
            } elseif ($type === 'in_progress') {
                return $student->examAttempts()
                    ->where('status', 'in_progress')
                    ->count();
            }
        } catch (\Exception $e) {
            return 0;
        }
        return 0;
    }

    /**
     * Get recent exam attempts safely
     */
    private function getRecentAttempts($student)
    {
        try {
            return $student->examAttempts()
                ->with('exam')
                ->latest()
                ->take(10)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Calculate average score for a student
     */
    private function calculateAverageScore($student): float
    {
        try {
            // Try from results table first
            $results = $student->results()->where('is_published', true)->get();
            
            if ($results->count() > 0) {
                $totalPercentage = 0;
                $validResults = 0;
                
                foreach ($results as $result) {
                    if (isset($result->exam) && $result->exam->total_marks > 0) {
                        $totalPercentage += ($result->obtained_marks / $result->exam->total_marks) * 100;
                        $validResults++;
                    }
                }
                
                return $validResults > 0 ? round($totalPercentage / $validResults, 2) : 0;
            }

            // Fallback to exam attempts
            $completedAttempts = $student->examAttempts()
                ->whereIn('status', ['submitted', 'auto_submitted'])
                ->whereNotNull('obtained_marks')
                ->get();
            
            if ($completedAttempts->count() > 0) {
                $totalPercentage = 0;
                $validAttempts = 0;
                
                foreach ($completedAttempts as $attempt) {
                    if (isset($attempt->exam) && $attempt->exam->total_marks > 0) {
                        $totalPercentage += ($attempt->obtained_marks / $attempt->exam->total_marks) * 100;
                        $validAttempts++;
                    }
                }
                
                return $validAttempts > 0 ? round($totalPercentage / $validAttempts, 2) : 0;
            }
        } catch (\Exception $e) {
            // Silently fail and return 0
        }
        
        return 0;
    }
}
