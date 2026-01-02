<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamCategory;
use App\Models\ExamMarkingScheme;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::with(['examCategory', 'questions'])
            ->where('created_by', auth()->id())
            ->latest()
            ->paginate(15);

        return view('teacher.exams.index', compact('exams'));
    }

    public function create()
    {
        $examCategories = ExamCategory::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        $questions = Question::with(['subject', 'examCategory'])
            ->where('is_active', true)
            ->get();

        return view('teacher.exams.create', compact('examCategories', 'subjects', 'questions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'exam_category_id' => 'required|exists:exam_categories,id',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'total_questions' => 'required|integer|min:1',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'result_release_time' => 'nullable|date|after:end_time',
            'show_results_immediately' => 'nullable|boolean',
            'randomize_questions' => 'nullable|boolean',
            'randomize_options' => 'nullable|boolean',
            'allow_resume' => 'nullable|boolean',
            'marking_schemes' => 'required|array',
            'marking_schemes.*.subject_id' => 'required|exists:subjects,id',
            'marking_schemes.*.correct_marks' => 'required|numeric|min:0',
            'marking_schemes.*.wrong_marks' => 'required|numeric|min:0',
            'selected_questions' => 'required|array|min:1',
        ]);

        // Filter out empty question IDs
        $selectedQuestions = array_filter($request->selected_questions, function($qId) {
            return !empty($qId) && is_numeric($qId);
        });

        if (empty($selectedQuestions)) {
            return back()->with('error', 'Please select at least one question for the exam.')->withInput();
        }

        DB::beginTransaction();

        try {
            $validated['exam_code'] = 'EXM' . strtoupper(Str::random(8));
            $validated['created_by'] = auth()->id();
            $validated['show_results_immediately'] = $request->has('show_results_immediately');
            $validated['randomize_questions'] = $request->has('randomize_questions');
            $validated['randomize_options'] = $request->has('randomize_options');
            $validated['allow_resume'] = $request->has('allow_resume');

            // Calculate total marks
            $totalMarks = 0;
            foreach ($request->marking_schemes as $scheme) {
                $questionCount = count(array_filter($selectedQuestions, function($qId) use ($scheme) {
                    $question = Question::find($qId);
                    return $question && $question->subject_id == $scheme['subject_id'];
                }));
                $totalMarks += $questionCount * $scheme['correct_marks'];
            }

            $validated['total_marks'] = $totalMarks;
            $exam = Exam::create($validated);

            // Create marking schemes
            foreach ($request->marking_schemes as $scheme) {
                ExamMarkingScheme::create([
                    'exam_id' => $exam->id,
                    'subject_id' => $scheme['subject_id'],
                    'correct_marks' => $scheme['correct_marks'],
                    'wrong_marks' => $scheme['wrong_marks'],
                    'unattempted_marks' => 0,
                ]);
            }

            // Attach questions with proper validation
            $displayOrder = 1;
            foreach ($selectedQuestions as $questionId) {
                if (!empty($questionId) && is_numeric($questionId)) {
                    $exam->questions()->attach($questionId, ['display_order' => $displayOrder++]);
                }
            }

            DB::commit();

            return redirect()->route('teacher.exams.index')
                ->with('success', 'Exam created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create exam: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Exam $exam)
    {
        $this->authorize('view', $exam);

        $exam->load(['examCategory', 'questions.subject', 'markingSchemes.subject', 'enrolledStudents']);
        $attempts = $exam->attempts()->with('student.user')->latest()->paginate(20);

        $statistics = [
            'total_enrolled' => $exam->enrolledStudents()->count(),
            'total_attempts' => $exam->attempts()->count(),
            'completed_attempts' => $exam->attempts()->whereIn('status', ['submitted', 'auto_submitted'])->count(),
            'in_progress' => $exam->attempts()->where('status', 'in_progress')->count(),
            'average_score' => $exam->results()->avg('obtained_marks') ?? 0,
        ];

        return view('teacher.exams.show', compact('exam', 'attempts', 'statistics'));
    }

    public function enrollStudents(Request $request, Exam $exam)
    {
        $this->authorize('update', $exam);

        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->student_ids as $studentId) {
                $exam->enrolledStudents()->syncWithoutDetaching([
                    $studentId => ['is_enrolled' => true]
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Students enrolled successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to enroll students'
            ], 500);
        }
    }

    public function publishResults(Exam $exam)
    {
        $this->authorize('update', $exam);

        try {
            $exam->results()->update([
                'is_published' => true,
                'published_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Results published successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to publish results'
            ], 500);
        }
    }

    public function searchQuestions(Request $request)
    {
        $query = Question::with(['subject', 'difficulty'])
            ->where('is_active', true);

        if ($request->exam_category_id) {
            $query->where('exam_category_id', $request->exam_category_id);
        }

        if ($request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->difficulty_id) {
            $query->where('difficulty_id', $request->difficulty_id);
        }

        if ($request->search) {
            $query->where('question_text', 'LIKE', '%' . $request->search . '%');
        }

        $questions = $query->limit(50)->get();

        return response()->json($questions);
    }
}
