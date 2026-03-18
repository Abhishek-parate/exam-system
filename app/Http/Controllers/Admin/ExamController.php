<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamCategory;
use App\Models\Question;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = Exam::with(['examCategory', 'creator']);

        if ($request->filled('category_id')) {
            $query->where('exam_category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $now = now();
            switch ($request->status) {
                case 'upcoming':
                    $query->where('start_time', '>', $now);
                    break;
                case 'ongoing':
                    $query->where('start_time', '<=', $now)->where('end_time', '>=', $now);
                    break;
                case 'expired':
                    $query->where('end_time', '<', $now);
                    break;
            }
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $exams = $query->latest()->paginate(15);
        $categories = ExamCategory::where('is_active', true)->get();

        return view('admin.exams.index', compact('exams', 'categories'));
    }

    public function create()
    {
        $categories = ExamCategory::where('is_active', true)->get();
        return view('admin.exams.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'exam_category_id' => 'required|exists:exam_categories,id',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'result_release_time' => 'nullable|date',
            'total_marks' => 'required|numeric|min:0',
            'randomize_questions' => 'boolean',
            'randomize_options' => 'boolean',
            'show_results_immediately' => 'boolean',
            'allow_resume' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['exam_code'] = 'EXM-' . strtoupper(Str::random(8));
        $validated['created_by'] = auth()->id();
        $validated['total_questions'] = 0; // Will be updated when questions are added
        
        $exam = Exam::create($validated);

        return redirect()->route('admin.exams.show', $exam)
                       ->with('success', 'Exam created successfully! Now add questions to this exam.');
    }

    public function show(Exam $exam)
    {
        $exam->load(['examCategory', 'creator', 'questions.subject', 'questions.difficulty', 'enrolledStudents.user']);
        
        $stats = [
            'total_questions' => $exam->questions->count(),
            'enrolled_students' => $exam->enrolledStudents->count(),
            'total_attempts' => $exam->attempts->count(),
            'completed_attempts' => $exam->attempts()->whereIn('status', ['submitted', 'auto_submitted'])->count(),
        ];

        return view('admin.exams.show', compact('exam', 'stats'));
    }

    public function edit(Exam $exam)
    {
        $categories = ExamCategory::where('is_active', true)->get();
        return view('admin.exams.edit', compact('exam', 'categories'));
    }

    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'exam_category_id' => 'required|exists:exam_categories,id',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'result_release_time' => 'nullable|date',
            'total_marks' => 'required|numeric|min:0',
            'randomize_questions' => 'boolean',
            'randomize_options' => 'boolean',
            'show_results_immediately' => 'boolean',
            'allow_resume' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $exam->update($validated);

        return redirect()->route('admin.exams.show', $exam)
                       ->with('success', 'Exam updated successfully!');
    }

    public function destroy(Exam $exam)
    {
        // Check if exam has attempts
        if ($exam->attempts()->count() > 0) {
            return back()->with('error', 'Cannot delete exam with existing attempts!');
        }

        $exam->delete();

        return redirect()->route('admin.exams.index')
                       ->with('success', 'Exam deleted successfully!');
    }
}
