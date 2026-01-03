<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Question;
use App\Models\ExamCategory;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Overall Statistics
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_questions' => Question::count(),
            'active_questions' => Question::where('is_active', true)->count(),
            'total_categories' => ExamCategory::count(),
            'total_subjects' => Subject::count(),
        ];

        // Users by Role
        $usersByRole = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->get()
            ->pluck('count', 'role');

        // Questions by Category
        $questionsByCategory = Question::select('exam_categories.name', DB::raw('count(*) as count'))
            ->join('exam_categories', 'questions.exam_category_id', '=', 'exam_categories.id')
            ->groupBy('exam_categories.name', 'exam_categories.id')
            ->get();

        // Questions by Difficulty
        $questionsByDifficulty = Question::select('question_difficulties.name', DB::raw('count(*) as count'))
            ->join('question_difficulties', 'questions.difficulty_id', '=', 'question_difficulties.id')
            ->groupBy('question_difficulties.name', 'question_difficulties.id')
            ->get();

        // Recent Activity - Latest Questions
        $recentQuestions = Question::with(['examCategory', 'subject', 'difficulty'])
            ->latest()
            ->take(5)
            ->get();

        // Recent Users
        $recentUsers = User::latest()
            ->take(5)
            ->get();

        return view('admin.reports.index', compact(
            'stats',
            'usersByRole',
            'questionsByCategory',
            'questionsByDifficulty',
            'recentQuestions',
            'recentUsers'
        ));
    }

    public function export(Request $request)
    {
        // TODO: Implement CSV/Excel export
        return back()->with('info', 'Export feature coming soon!');
    }
}
