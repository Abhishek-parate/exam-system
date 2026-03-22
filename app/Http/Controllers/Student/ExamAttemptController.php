<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use App\Models\ExamAnswerTimeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ExamAttemptController extends Controller
{
    // -------------------------------------------------------
    // My Exams list page
    // -------------------------------------------------------
    public function index()
    {
        $student = auth()->user()->student;
        $now     = now();

        $allExams = $this->getAllAccessibleExams($student);

        $availableExams = $allExams->filter(fn($e) =>
            $e->is_active
            && $e->start_time->lte($now)
            && $e->end_time->gte($now)
            && ! $student->hasAttemptedExam($e->id)
        )->values();

        $upcomingExams = $allExams->filter(fn($e) => $e->start_time->gt($now))
                                   ->sortBy('start_time')->values();

        $completedExams = $student->examAttempts()
                                   ->whereIn('status', ['submitted', 'auto_submitted'])
                                   ->with(['exam.examCategory', 'result'])
                                   ->latest()
                                   ->get();

        return view('student.exams.index', compact('availableExams', 'upcomingExams', 'completedExams'));
    }

    // -------------------------------------------------------
    // Instructions page
    // -------------------------------------------------------
    public function instructions(Exam $exam)
    {
        $student = auth()->user()->student;

        if (! $this->studentCanAccessExam($exam, $student)) {
            abort(403, 'You do not have access to this exam.');
        }
        if ($student->hasAttemptedExam($exam->id)) {
            return redirect()->route('student.exams.index')
                             ->with('error', 'You have already attempted this exam.');
        }
        if (! $exam->canBeAttempted()) {
            return redirect()->route('student.exams.index')
                             ->with('error', 'This exam is not currently available.');
        }

        $exam->load(['examCategory', 'markingSchemes.subject']);
        return view('student.exams.instructions', compact('exam'));
    }

    // -------------------------------------------------------
    // Start exam
    // -------------------------------------------------------
    public function start(Exam $exam)
    {
        $student = auth()->user()->student;

        if (! $this->studentCanAccessExam($exam, $student)) {
            return response()->json(['success' => false, 'message' => 'Access denied.'], 403);
        }
        if (! $exam->canBeAttempted()) {
            return response()->json(['success' => false, 'message' => 'Exam is not available.'], 403);
        }
        if ($student->hasAttemptedExam($exam->id)) {
            return response()->json(['success' => false, 'message' => 'Already attempted.'], 403);
        }

        DB::beginTransaction();
        try {
            $attempt = ExamAttempt::create([
                'exam_id'    => $exam->id,
                'student_id' => $student->id,
                'started_at' => now(),
                'status'     => 'in_progress',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            foreach ($exam->questions as $question) {
                ExamAnswer::create([
                    'attempt_id'  => $attempt->id,
                    'question_id' => $question->id,
                ]);
            }

            DB::commit();
            return response()->json([
                'success'      => true,
                'redirect_url' => route('student.exams.attempt', $attempt->attempt_token),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Exam start failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to start exam.'], 500);
        }
    }

    // -------------------------------------------------------
    // Attempt page
    // -------------------------------------------------------
    public function attempt($attemptToken)
    {
        $attempt = ExamAttempt::where('attempt_token', $attemptToken)
                              ->with(['exam.questions.options', 'answers'])
                              ->firstOrFail();

$user = auth()->user();

if (!$user || !$user->student || $attempt->student_id !== $user->student->id) {
    abort(403);
}
        if ($attempt->isSubmitted()) {
            return redirect()->route('student.exams.index')->with('info', 'Exam already submitted.');
        }
        if ($attempt->getRemainingTimeSeconds() <= 0) {
            $this->autoSubmit($attempt);
            return redirect()->route('student.exams.index')->with('info', 'Time expired. Auto-submitted.');
        }

        $questions = $attempt->exam->randomize_questions
                   ? $attempt->exam->questions->shuffle()
                   : $attempt->exam->questions;

        return view('student.exams.attempt', compact('attempt', 'questions'));
    }

    // -------------------------------------------------------
    // AJAX — Save answer (handles both MCQ option_id & subjective text_answer)
    // -------------------------------------------------------
    public function saveAnswer(Request $request, $attemptToken)
    {
        $attempt = ExamAttempt::where('attempt_token', $attemptToken)->firstOrFail();

        if ($attempt->student_id !== auth()->user()->student->id || $attempt->isSubmitted()) {
            return response()->json(['success' => false, 'message' => 'Invalid attempt.'], 403);
        }

        $request->validate([
            'question_id'          => 'required|exists:questions,id',
            'option_id'            => 'nullable|exists:question_options,id',
            'text_answer'          => 'nullable|string|max:5000',
            'is_marked_for_review' => 'nullable|boolean',
        ]);

        try {
            $answer = ExamAnswer::where('attempt_id', $attempt->id)
                                ->where('question_id', $request->question_id)
                                ->first();

            if (! $answer) {
                return response()->json(['success' => false, 'message' => 'Invalid question.'], 400);
            }

            $updateData = [
                'is_marked_for_review' => $request->is_marked_for_review ?? false,
                'last_answered_at'     => now(),
                'first_answered_at'    => $answer->first_answered_at ?? now(),
            ];

            // MCQ — option_id present in request
            if ($request->has('option_id')) {
                $updateData['selected_option_id'] = $request->option_id;
            }

            // Subjective — text_answer present in request
            if ($request->has('text_answer')) {
                $updateData['text_answer'] = $request->text_answer
                    ? trim($request->text_answer)
                    : null;
            }

            $answer->update($updateData);

            return response()->json(['success' => true, 'status' => $answer->status ?? 'saved']);

        } catch (\Exception $e) {
            Log::error('Save answer failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to save answer.'], 500);
        }
    }

    // -------------------------------------------------------
// 🚨 CHEAT LOG (ANTI-CHEAT)
// -------------------------------------------------------
    public function cheatLog(Request $request, $attemptToken)
    {
        $attempt = ExamAttempt::where('attempt_token', $attemptToken)->firstOrFail();

        if ($attempt->student_id !== auth()->user()->student->id || $attempt->isSubmitted()) {
            return response()->json(['success' => false], 403);
        }

        $request->validate([
            'type'  => 'required|string|in:tab_switch,fullscreen_exit,copy',
            'count' => 'nullable|integer|min:0'
        ]);

        try {
            switch ($request->type) {

                case 'tab_switch':
                    $attempt->tab_switch_count = $request->count ?? ($attempt->tab_switch_count + 1);
                    break;

                case 'fullscreen_exit':
                    $attempt->fullscreen_exit_count = $request->count ?? ($attempt->fullscreen_exit_count + 1);
                    break;

                case 'copy':
                    $attempt->copy_attempts = $attempt->copy_attempts + 1;
                    break;
            }

            $attempt->save();

            // 🔥 SERVER SIDE AUTO-SUBMIT (IMPORTANT SECURITY)
            if (
                $attempt->tab_switch_count > 1 ||
                $attempt->fullscreen_exit_count > 1 ||
                $attempt->copy_attempts > 0
            ) {
                $this->autoSubmit($attempt);

                return response()->json([
                    'success' => true,
                    'force_submit' => true,
                    'message' => 'Cheating detected. Exam auto-submitted.'
                ]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Cheat log failed: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    // -------------------------------------------------------
    // AJAX — Track time
    // -------------------------------------------------------
    public function trackTime(Request $request, $attemptToken)
    {
        $attempt = ExamAttempt::where('attempt_token', $attemptToken)->firstOrFail();

        if ($attempt->student_id !== auth()->user()->student->id || $attempt->isSubmitted()) {
            return response()->json(['success' => false], 403);
        }

        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'time_spent'  => 'required|integer|min:0',
        ]);

        try {
            $answer = ExamAnswer::where('attempt_id', $attempt->id)
                                ->where('question_id', $request->question_id)
                                ->first();

            if ($answer) {
                $answer->increment('time_spent_seconds', $request->time_spent);
                $answer->increment('visit_count');

                ExamAnswerTimeLog::create([
                    'answer_id'        => $answer->id,
                    'entered_at'       => now()->subSeconds($request->time_spent),
                    'exited_at'        => now(),
                    'duration_seconds' => $request->time_spent,
                ]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
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

        return view('student.exams.attempt-detail', compact('exam', 'attempt', 'questions', 'summaryStats'));
    }

    // -------------------------------------------------------
    // AJAX — Status polling
    // -------------------------------------------------------
    public function getStatus($attemptToken)
    {
        $attempt = ExamAttempt::where('attempt_token', $attemptToken)->firstOrFail();

        if ($attempt->student_id !== auth()->user()->student->id) {
            return response()->json(['success' => false], 403);
        }

        $remaining = $attempt->getRemainingTimeSeconds();

        if ($remaining <= 0 && $attempt->isInProgress()) {
            $this->autoSubmit($attempt);
            return response()->json([
                'success'      => true,
                'time_expired' => true,
                'redirect_url' => route('student.exams.index'),
            ]);
        }

        return response()->json([
            'success'           => true,
            'remaining_seconds' => $remaining,
            'time_expired'      => false,
        ]);
    }

    // -------------------------------------------------------
    // Submit
    // -------------------------------------------------------
    public function submit(Request $request, $attemptToken)
    {
        $attempt = ExamAttempt::where('attempt_token', $attemptToken)->firstOrFail();

        if ($attempt->student_id !== auth()->user()->student->id || $attempt->isSubmitted()) {
            return response()->json(['success' => false, 'message' => 'Invalid attempt.'], 403);
        }

        DB::beginTransaction();
        try {
            $attempt->update([
                'submitted_at'       => now(),
                'status'             => 'submitted',
                'time_taken_seconds' => now()->diffInSeconds($attempt->started_at),
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Exam submission failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Submission failed. Please try again.'], 500);
        }

        try {
            $this->calculateResult($attempt);
        } catch (\Exception $e) {
            Log::error('Result calculation failed for attempt ' . $attempt->id . ': ' . $e->getMessage());
        }

        return response()->json([
            'success'      => true,
            'message'      => 'Exam submitted successfully!',
            'redirect_url' => route('student.exams.index'),
        ]);
    }

    // -------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------
    private function getAllAccessibleExams($student)
    {
        $hasEnrollmentType = Schema::hasColumn('exams', 'enrollment_type');

        if (! $hasEnrollmentType) {
            return Exam::where('is_active', true)->with('examCategory')->get();
        }

        $openExams = Exam::where(function ($q) {
            $q->where('enrollment_type', 'open')->orWhereNull('enrollment_type');
        })->with('examCategory')->get();

        $enrolledOnlyExams = $student->enrolledExams()
                                      ->where('enrollment_type', 'enrolled')
                                      ->with('examCategory')->get();

        return $openExams->merge($enrolledOnlyExams)->unique('id');
    }

    private function studentCanAccessExam(Exam $exam, $student): bool
    {
        $hasEnrollmentType = Schema::hasColumn('exams', 'enrollment_type');
        if (! $hasEnrollmentType) return true;
        if (is_null($exam->enrollment_type) || $exam->enrollment_type === 'open') return true;
        return $exam->enrolledStudents()
                    ->where('student_id', $student->id)
                    ->where('is_enrolled', true)
                    ->exists();
    }

    private function autoSubmit(ExamAttempt $attempt): void
    {
        DB::beginTransaction();
        try {
            $attempt->update([
                'auto_submitted_at'  => now(),
                'status'             => 'auto_submitted',
                'time_taken_seconds' => $attempt->exam->duration_minutes * 60,
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Auto-submit failed: ' . $e->getMessage());
        }

        try {
            $this->calculateResult($attempt);
        } catch (\Exception $e) {
            Log::error('Result calculation failed on auto-submit: ' . $e->getMessage());
        }
    }

    private function calculateResult(ExamAttempt $attempt): void
    {
        app(\App\Services\ResultCalculationService::class)->calculate($attempt);
    }
}