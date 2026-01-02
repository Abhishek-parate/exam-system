<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ParentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Display parent dashboard
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $parent = $user->parent;
            
            // If no parent profile exists, return empty collection
            $children = collect();
            
            if ($parent) {
                $children = $parent->students()
                    ->with([
                        'user',
                        'examAttempts',
                        'results' => function($query) {
                            $query->where('is_published', true)->latest()->limit(1);
                        }
                    ])
                    ->orderBy('class')
                    ->get();
            }

            return view('parent.dashboard', compact('children'));
            
        } catch (\Exception $e) {
            Log::error('Parent Dashboard Error: ' . $e->getMessage());
            return view('parent.dashboard', ['children' => collect()]);
        }
    }

    /**
     * Display child performance details
     */
    public function performance(Student $student)
    {
        try {
            $user = Auth::user();
            $parent = $user->parent;
            
            // Verify parent-child relationship
            if (!$parent || !$parent->students()->where('id', $student->id)->exists()) {
                abort(404, 'Child not found or access denied');
            }

            // Load performance data
            $results = $student->results()
                ->where('is_published', true)
                ->with(['exam.examCategory'])
                ->latest()
                ->limit(10)
                ->get();

            $stats = [
                'total_exams' => $student->examAttempts()->count(),
                'published_results' => $results->count(),
                'avg_score' => round($results->avg('percentage') ?? 0, 1),
                'best_score' => round($results->max('percentage') ?? 0, 1),
                'latest_result' => $results->first(),
            ];

            return view('parent.child-performance', compact('student', 'results', 'stats'));
            
        } catch (\Exception $e) {
            Log::error('Child Performance Error: ' . $e->getMessage());
            abort(404, 'Performance data not available');
        }
    }
}
