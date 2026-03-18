<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamCategory;
use App\Models\ExamResult;
use App\Models\Question;
use App\Models\QuestionDifficulty;
use App\Models\Student;
use App\Models\Subject;
use App\Services\ResultCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ExamController extends Controller
{
    private function hasEnrollmentType(): bool
    {
        return Schema::hasColumn('exams', 'enrollment_type');
    }

    // =========================================================
    // INDEX
    // =========================================================
    public function index(Request $request)
    {
        $query = Exam::with(['examCategory', 'creator']);

        if ($request->filled('category_id')) {
            $query->where('exam_category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $now = now();
            switch ($request->status) {
                case 'upcoming': $query->where('start_time', '>', $now); break;
                case 'ongoing':  $query->where('start_time', '<=', $now)->where('end_time', '>=', $now); break;
                case 'expired':  $query->where('end_time', '<', $now); break;
            }
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $exams      = $query->latest()->paginate(15);
        $categories = ExamCategory::where('is_active', true)->get();

        return view('admin.exams.index', compact('exams', 'categories'));
    }

    // =========================================================
    // CREATE / STORE
    // =========================================================
    public function create()
    {
        $categories = ExamCategory::where('is_active', true)->get();
        return view('admin.exams.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $hasEnrollmentType = $this->hasEnrollmentType();

        $rules = [
            'title'               => 'required|string|max:255',
            'exam_category_id'    => 'required|exists:exam_categories,id',
            'description'         => 'nullable|string',
            'duration_minutes'    => 'required|integer|min:1',
            'start_time'          => 'required|date',
            'end_time'            => 'required|date|after:start_time',
            'result_release_time' => 'nullable|date',
            'total_marks'         => 'required|numeric|min:0',
        ];

        if ($hasEnrollmentType) {
            $rules['enrollment_type'] = 'required|in:open,enrolled';
        }

        $validated = $request->validate($rules);

        $validated['exam_code']                = 'EXM-' . strtoupper(Str::random(8));
        $validated['created_by']               = auth()->id();
        $validated['total_questions']          = 0;
        $validated['randomize_questions']      = $request->has('randomize_questions')      ? 1 : 0;
        $validated['randomize_options']        = $request->has('randomize_options')        ? 1 : 0;
        $validated['show_results_immediately'] = $request->has('show_results_immediately') ? 1 : 0;
        $validated['allow_resume']             = $request->has('allow_resume')             ? 1 : 0;
        $validated['is_active']                = $request->has('is_active')                ? 1 : 0;

        if ($hasEnrollmentType) {
            $validated['enrollment_type'] = $request->input('enrollment_type', 'open');
        }

        $exam = Exam::create($validated);

        return redirect()->route('admin.exams.show', $exam)
                         ->with('success', 'Exam created! Add questions and manage enrollment below.');
    }

    // =========================================================
    // SHOW  — lists attempts + results
    // =========================================================
    public function show(Exam $exam)
    {
        $exam->load([
            'examCategory', 'creator',
            'questions.subject', 'questions.difficulty', 'questions.options',
            'enrolledStudents.user',
        ]);

        $attempts = $exam->attempts()
                         ->with(['student.user', 'result'])
                         ->whereIn('status', ['submitted', 'auto_submitted'])
                         ->latest('submitted_at')
                         ->get();

        $noResultCount = $attempts->filter(fn($a) => is_null($a->result))->count();

        $stats = [
            'total_questions'     => $exam->questions->count(),
            'enrolled_students'   => $exam->enrolledStudents->count(),
            'total_attempts'      => $exam->attempts()->count(),
            'completed_attempts'  => $attempts->count(),
            'published_results'   => $attempts->filter(fn($a) => $a->result?->is_published)->count(),
            'unpublished_results' => $attempts->filter(fn($a) => $a->result && ! $a->result->is_published)->count(),
            'no_result_count'     => $noResultCount,
        ];

        $subjects            = Subject::orderBy('name')->get();
        $difficulties        = QuestionDifficulty::orderBy('level')->get();
        $assignedQuestionIds = $exam->questions->pluck('id')->toArray();
        $enrolledStudentIds  = $exam->enrolledStudents->pluck('id')->toArray();

        $availableStudents = Student::with('user')
                                    ->whereNotIn('id', $enrolledStudentIds)
                                    ->whereHas('user')
                                    ->get();

        return view('admin.exams.show', compact(
            'exam', 'stats', 'attempts',
            'subjects', 'difficulties', 'assignedQuestionIds',
            'enrolledStudentIds', 'availableStudents'
        ));
    }

    // =========================================================
    // ✅ NEW: SHOW ATTEMPT DETAIL — full question-by-question preview
    // =========================================================
    public function showAttempt(Exam $exam, ExamAttempt $attempt)
    {
        // Ensure the attempt belongs to this exam
        if ($attempt->exam_id !== $exam->id) {
            abort(404);
        }

        $attempt->load([
            'student.user',
            'result.subjectWiseResults.subject',
            'answers.question.subject',
            'answers.question.options',
            'answers.selectedOption',
        ]);

        // Build an ordered list: question + student's answer + correct option
        $questions = $attempt->answers->map(function ($answer) {
            $question      = $answer->question;
            $options       = $question?->options ?? collect();
            $correctOption = $options->firstWhere('is_correct', true);
            $selectedOpt   = $answer->selectedOption;

            $answerStatus = 'unattempted';
            if ($answer->isAttempted()) {
                $answerStatus = $answer->isCorrect() ? 'correct' : 'wrong';
            }
            if ($answer->is_marked_for_review && ! $answer->isAttempted()) {
                $answerStatus = 'review';
            }

            return [
                'answer'         => $answer,
                'question'       => $question,
                'options'        => $options,
                'correct_option' => $correctOption,
                'selected_opt'   => $selectedOpt,
                'status'         => $answerStatus,
                'time_spent'     => $answer->time_spent_seconds ?? 0,
            ];
        })->sortBy(fn($item) => $item['question']?->pivot?->display_order ?? 0)->values();

        $summaryStats = [
            'total'       => $questions->count(),
            'correct'     => $questions->where('status', 'correct')->count(),
            'wrong'       => $questions->where('status', 'wrong')->count(),
            'unattempted' => $questions->whereIn('status', ['unattempted', 'review'])->count(),
        ];

        return view('admin.exams.attempt-detail', compact('exam', 'attempt', 'questions', 'summaryStats'));
    }

    // =========================================================
    // EDIT / UPDATE
    // =========================================================
    public function edit(Exam $exam)
    {
        $categories = ExamCategory::where('is_active', true)->get();
        return view('admin.exams.edit', compact('exam', 'categories'));
    }

    public function update(Request $request, Exam $exam)
    {
        $hasEnrollmentType = $this->hasEnrollmentType();

        $rules = [
            'title'               => 'required|string|max:255',
            'exam_category_id'    => 'required|exists:exam_categories,id',
            'description'         => 'nullable|string',
            'duration_minutes'    => 'required|integer|min:1',
            'start_time'          => 'required|date',
            'end_time'            => 'required|date|after:start_time',
            'result_release_time' => 'nullable|date',
            'total_marks'         => 'required|numeric|min:0',
        ];

        if ($hasEnrollmentType) {
            $rules['enrollment_type'] = 'required|in:open,enrolled';
        }

        $validated = $request->validate($rules);

        $validated['randomize_questions']      = $request->has('randomize_questions')      ? 1 : 0;
        $validated['randomize_options']        = $request->has('randomize_options')        ? 1 : 0;
        $validated['show_results_immediately'] = $request->has('show_results_immediately') ? 1 : 0;
        $validated['allow_resume']             = $request->has('allow_resume')             ? 1 : 0;
        $validated['is_active']                = $request->has('is_active')                ? 1 : 0;

        if ($hasEnrollmentType) {
            $validated['enrollment_type'] = $request->input('enrollment_type', 'open');
        }

        $exam->update($validated);

        return redirect()->route('admin.exams.show', $exam)
                         ->with('success', 'Exam updated successfully!');
    }

    // =========================================================
    // DESTROY
    // =========================================================
    public function destroy(Exam $exam)
    {
        if ($exam->attempts()->count() > 0) {
            return back()->with('error', 'Cannot delete exam with existing attempts!');
        }
        $exam->delete();
        return redirect()->route('admin.exams.index')->with('success', 'Exam deleted successfully!');
    }

    // =========================================================
    // ✅ PUBLISH ALL RESULTS
    // =========================================================
    public function publishResults(Exam $exam)
    {
        $published = ExamResult::where('exam_id', $exam->id)
                               ->where('is_published', false)
                               ->update([
                                   'is_published' => true,
                                   'published_at' => now(),
                               ]);

        return back()->with('success', "{$published} result(s) published successfully! Students can now see their scores.");
    }

    // =========================================================
    // ✅ PUBLISH SINGLE RESULT (AJAX)
    // =========================================================
    public function publishSingleResult(Exam $exam, ExamResult $result)
    {
        if ($result->exam_id !== $exam->id) {
            return response()->json(['message' => 'Result does not belong to this exam.'], 422);
        }

        $result->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        return response()->json(['message' => 'Result published successfully!']);
    }

    // =========================================================
    // ✅ RECALCULATE RESULTS
    // =========================================================
    public function recalculateResults(Exam $exam)
    {
        $missingAttempts = $exam->attempts()
                                ->whereIn('status', ['submitted', 'auto_submitted'])
                                ->whereDoesntHave('result')
                                ->get();

        if ($missingAttempts->isEmpty()) {
            return back()->with('success', 'All attempts already have results. Nothing to recalculate.');
        }

        $service   = app(ResultCalculationService::class);
        $succeeded = 0;
        $failed    = 0;

        foreach ($missingAttempts as $attempt) {
            try {
                $service->calculate($attempt);
                $succeeded++;
            } catch (\Throwable $e) {
                $failed++;
                Log::error("Admin recalculate: attempt {$attempt->id} failed — " . $e->getMessage());
            }
        }

        $msg = "{$succeeded} result(s) calculated successfully.";
        if ($failed > 0) {
            $msg .= " {$failed} attempt(s) still failed — check laravel.log for details.";
        }

        return back()->with($failed > 0 ? 'error' : 'success', $msg);
    }

    // =========================================================
    // QUESTION ASSIGNMENT
    // =========================================================
    public function searchQuestions(Request $request, Exam $exam)
    {
        try {
            $assignedIds = $exam->questions()->pluck('questions.id');

            $query = Question::with(['subject', 'difficulty', 'options'])
                ->where('is_active', true)
                ->whereNotIn('id', $assignedIds);

            if ($request->filled('subject_id'))    $query->where('subject_id',    $request->subject_id);
            if ($request->filled('difficulty_id')) $query->where('difficulty_id', $request->difficulty_id);
            if ($request->filled('search'))        $query->where('question_text', 'like', '%' . $request->search . '%');

            $paginator = $query->latest()->paginate(15);

            $questions = $paginator->getCollection()->map(fn($q) => [
                'id'               => $q->id,
                'question_text'    => $q->question_text,
                'subject'          => $q->subject?->name ?? 'N/A',
                'difficulty'       => $q->difficulty?->name ?? 'N/A',
                'difficulty_color' => $this->difficultyColor($q->difficulty?->name),
                'marks'            => $q->marks,
                'negative_marks'   => $q->negative_marks,
                'options_count'    => $q->options->count(),
            ]);

            return response()->json([
                'questions'    => $questions,
                'total'        => $paginator->total(),
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()], 500);
        }
    }

    public function addQuestion(Exam $exam, Question $question)
    {
        if ($exam->questions()->where('questions.id', $question->id)->exists()) {
            return response()->json(['message' => 'Question already added.'], 422);
        }
        $maxOrder = $exam->questions()->max('exam_questions.display_order') ?? 0;
        $exam->questions()->attach($question->id, ['display_order' => $maxOrder + 1]);
        $exam->update(['total_questions' => $exam->questions()->count()]);
        return response()->json(['message' => 'Question added!', 'total_questions' => $exam->questions()->count()]);
    }

    public function removeQuestion(Exam $exam, Question $question)
    {
        $exam->questions()->detach($question->id);
        $remaining = $exam->questions()->orderBy('exam_questions.display_order')->get();
        foreach ($remaining as $i => $q) {
            $exam->questions()->updateExistingPivot($q->id, ['display_order' => $i + 1]);
        }
        $exam->update(['total_questions' => $exam->questions()->count()]);
        return response()->json(['message' => 'Question removed.', 'total_questions' => $exam->questions()->count()]);
    }

    public function bulkAddQuestions(Request $request, Exam $exam)
    {
        $request->validate(['question_ids' => 'required|array', 'question_ids.*' => 'exists:questions,id']);
        $assignedIds = $exam->questions()->pluck('questions.id')->toArray();
        $toAdd       = array_diff($request->question_ids, $assignedIds);
        if (!empty($toAdd)) {
            $maxOrder = $exam->questions()->max('exam_questions.display_order') ?? 0;
            $syncData = [];
            foreach (array_values($toAdd) as $i => $qId) {
                $syncData[$qId] = ['display_order' => $maxOrder + $i + 1];
            }
            $exam->questions()->attach($syncData);
            $exam->update(['total_questions' => $exam->questions()->count()]);
        }
        return response()->json([
            'message'         => count($toAdd) . ' question(s) added.',
            'total_questions' => $exam->questions()->count(),
        ]);
    }

    // =========================================================
    // STUDENT ENROLLMENT
    // =========================================================
    public function enrollStudent(Exam $exam, Student $student)
    {
        if ($exam->enrolledStudents()->where('student_id', $student->id)->exists()) {
            return response()->json(['message' => 'Student already enrolled.'], 422);
        }
        $exam->enrolledStudents()->attach($student->id, ['is_enrolled' => true]);
        return response()->json([
            'message'        => 'Student enrolled!',
            'enrolled_count' => $exam->enrolledStudents()->count(),
            'student'        => [
                'id'                => $student->id,
                'name'              => $student->user?->name ?? 'Unknown',
                'enrollment_number' => $student->enrollment_number ?? 'N/A',
            ],
        ]);
    }

    public function unenrollStudent(Exam $exam, Student $student)
    {
        $exam->enrolledStudents()->detach($student->id);
        return response()->json([
            'message'        => 'Student removed.',
            'enrolled_count' => $exam->enrolledStudents()->count(),
        ]);
    }

    public function enrollAllStudents(Exam $exam)
    {
        $allIds   = Student::whereHas('user')->pluck('id')->toArray();
        $enrolled = $exam->enrolledStudents()->pluck('students.id')->toArray();
        $toEnroll = array_diff($allIds, $enrolled);

        if (!empty($toEnroll)) {
            $syncData = [];
            foreach ($toEnroll as $sid) {
                $syncData[$sid] = ['is_enrolled' => true];
            }
            $exam->enrolledStudents()->attach($syncData);
        }

        return response()->json([
            'message'        => count($toEnroll) . ' student(s) enrolled.',
            'enrolled_count' => $exam->enrolledStudents()->count(),
        ]);
    }

    // =========================================================
    // Helper
    // =========================================================
    private function difficultyColor(?string $name): string
    {
        return match (strtolower($name ?? '')) {
            'easy'   => 'green',
            'medium' => 'yellow',
            'hard'   => 'red',
            default  => 'gray',
        };
    }
}