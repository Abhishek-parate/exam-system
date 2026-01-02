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

            foreach ($request->marking_schemes as $scheme) {
                ExamMarkingScheme::create([
                    'exam_id' => $exam->id,
                    'subject_id' => $scheme['subject_id'],
                    'correct_marks' => $scheme['correct_marks'],
                    'wrong_marks' => $scheme['wrong_marks'],
                    'unattempted_marks' => 0,
                ]);
            }

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

    public function show($id)
    {
        $exam = Exam::with([
            'examCategory', 
            'questions.subject', 
            'questions.options',
            'markingSchemes.subject', 
            'attempts.student.user'
        ])->where('created_by', auth()->id())->findOrFail($id);

        $statistics = [
            'total_questions' => $exam->questions()->count(),
            'total_attempts' => $exam->attempts()->count(),
            'completed_attempts' => $exam->attempts()->whereIn('status', ['submitted', 'auto_submitted'])->count(),
            'in_progress' => $exam->attempts()->where('status', 'in_progress')->count(),
            'average_score' => $this->calculateAverageScore($exam),
        ];

        // Get exam status
        $now = now();
        if ($now->lt($exam->start_time)) {
            $status = 'Scheduled';
        } elseif ($now->between($exam->start_time, $exam->end_time)) {
            $status = 'Ongoing';
        } else {
            $status = 'Completed';
        }

        return view('teacher.exams.show', compact('exam', 'statistics', 'status'));
    }

    /**
     * Calculate average score for an exam
     */
    private function calculateAverageScore($exam)
    {
        try {
            // Try to get from exam_results table
            if (method_exists($exam, 'results') && $exam->results()->exists()) {
                $avgMarks = $exam->results()
                    ->where('is_published', true)
                    ->avg('obtained_marks');
                
                if ($avgMarks && $exam->total_marks > 0) {
                    return round(($avgMarks / $exam->total_marks) * 100, 2);
                }
            }
            
            // Try to calculate from exam_attempts if it has obtained_marks
            $completedAttempts = $exam->attempts()
                ->whereIn('status', ['submitted', 'auto_submitted'])
                ->get();
            
            if ($completedAttempts->count() > 0 && $exam->total_marks > 0) {
                // Check if obtained_marks column exists
                $firstAttempt = $completedAttempts->first();
                if (isset($firstAttempt->obtained_marks)) {
                    $avgMarks = $completedAttempts->avg('obtained_marks');
                    return round(($avgMarks / $exam->total_marks) * 100, 2);
                }
            }
        } catch (\Exception $e) {
            // Silently fail and return 0
        }
        
        return 0;
    }

    public function edit($id)
    {
        $exam = Exam::with(['questions', 'markingSchemes'])
            ->where('created_by', auth()->id())
            ->findOrFail($id);

        $examCategories = ExamCategory::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        $questions = Question::with(['subject', 'examCategory'])
            ->where('is_active', true)
            ->get();

        $selectedQuestionIds = $exam->questions->pluck('id')->toArray();

        return view('teacher.exams.edit', compact('exam', 'examCategories', 'subjects', 'questions', 'selectedQuestionIds'));
    }

    public function update(Request $request, $id)
    {
        $exam = Exam::where('created_by', auth()->id())->findOrFail($id);

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

        $selectedQuestions = array_filter($request->selected_questions, function($qId) {
            return !empty($qId) && is_numeric($qId);
        });

        if (empty($selectedQuestions)) {
            return back()->with('error', 'Please select at least one question for the exam.')->withInput();
        }

        DB::beginTransaction();

        try {
            $validated['show_results_immediately'] = $request->has('show_results_immediately');
            $validated['randomize_questions'] = $request->has('randomize_questions');
            $validated['randomize_options'] = $request->has('randomize_options');
            $validated['allow_resume'] = $request->has('allow_resume');

            $totalMarks = 0;
            foreach ($request->marking_schemes as $scheme) {
                $questionCount = count(array_filter($selectedQuestions, function($qId) use ($scheme) {
                    $question = Question::find($qId);
                    return $question && $question->subject_id == $scheme['subject_id'];
                }));
                $totalMarks += $questionCount * $scheme['correct_marks'];
            }

            $validated['total_marks'] = $totalMarks;
            $exam->update($validated);

            $exam->markingSchemes()->delete();
            foreach ($request->marking_schemes as $scheme) {
                ExamMarkingScheme::create([
                    'exam_id' => $exam->id,
                    'subject_id' => $scheme['subject_id'],
                    'correct_marks' => $scheme['correct_marks'],
                    'wrong_marks' => $scheme['wrong_marks'],
                    'unattempted_marks' => 0,
                ]);
            }

            $exam->questions()->detach();
            $displayOrder = 1;
            foreach ($selectedQuestions as $questionId) {
                if (!empty($questionId) && is_numeric($questionId)) {
                    $exam->questions()->attach($questionId, ['display_order' => $displayOrder++]);
                }
            }

            DB::commit();

            return redirect()->route('teacher.exams.show', $exam->id)
                ->with('success', 'Exam updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update exam: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $exam = Exam::where('created_by', auth()->id())->findOrFail($id);

        try {
            if ($exam->attempts()->count() > 0) {
                return back()->with('error', 'Cannot delete exam that has student attempts.');
            }

            DB::beginTransaction();
            
            $exam->markingSchemes()->delete();
            $exam->questions()->detach();
            $exam->delete();
            
            DB::commit();

            return redirect()->route('teacher.exams.index')
                ->with('success', 'Exam deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete exam: ' . $e->getMessage());
        }
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
