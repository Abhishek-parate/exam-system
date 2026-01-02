<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use App\Models\ExamAnswerTimeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExamAttemptController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        
        $availableExams = $student->enrolledExams()
                                 ->where('is_active', true)
                                 ->where('start_time', '<=', now())
                                 ->where('end_time', '>=', now())
                                 ->with('examCategory')
                                 ->get();

        $upcomingExams = $student->enrolledExams()
                                ->where('is_active', true)
                                ->where('start_time', '>', now())
                                ->with('examCategory')
                                ->get();

        $completedExams = $student->examAttempts()
                                 ->whereIn('status', ['submitted', 'auto_submitted'])
                                 ->with(['exam.examCategory', 'result'])
                                 ->latest()
                                 ->get();

        return view('student.exams.index', compact('availableExams', 'upcomingExams', 'completedExams'));
    }

    public function instructions(Exam $exam)
    {
        $student = auth()->user()->student;

        // Check if student is enrolled
        if (!$exam->enrolledStudents()->where('student_id', $student->id)->exists()) {
            abort(403, 'You are not enrolled in this exam.');
        }

        // Check if already attempted
        if ($student->hasAttemptedExam($exam->id)) {
            return redirect()->route('student.exams.index')
                           ->with('error', 'You have already attempted this exam.');
        }

        // Check if exam is available
        if (!$exam->canBeAttempted()) {
            return redirect()->route('student.exams.index')
                           ->with('error', 'This exam is not available for attempt.');
        }

        $exam->load(['examCategory', 'markingSchemes.subject']);

        return view('student.exams.instructions', compact('exam'));
    }

    public function start(Exam $exam)
    {
        $student = auth()->user()->student;

        if (!$exam->canBeAttempted()) {
            return response()->json([
                'success' => false,
                'message' => 'Exam is not available'
            ], 403);
        }

        if ($student->hasAttemptedExam($exam->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Already attempted'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $attempt = ExamAttempt::create([
                'exam_id' => $exam->id,
                'student_id' => $student->id,
                'started_at' => now(),
                'status' => 'in_progress',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Create answer records for all questions
            $questions = $exam->questions;
            foreach ($questions as $question) {
                ExamAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'redirect_url' => route('student.exams.attempt', $attempt->attempt_token)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to start exam'
            ], 500);
        }
    }

    public function attempt($attemptToken)
    {
        $attempt = ExamAttempt::where('attempt_token', $attemptToken)
                              ->with(['exam.questions.options', 'answers'])
                              ->firstOrFail();

        // Verify ownership
        if ($attempt->student_id !== auth()->user()->student->id) {
            abort(403);
        }

        // Check if already submitted
        if ($attempt->isSubmitted()) {
            return redirect()->route('student.exams.index')
                           ->with('info', 'Exam already submitted');
        }

        // Check if exam time expired
        if ($attempt->getRemainingTimeSeconds() <= 0) {
            $this->autoSubmit($attempt);
            return redirect()->route('student.exams.index')
                           ->with('info', 'Exam time expired. Auto-submitted.');
        }

        $questions = $attempt->exam->randomize_questions 
                   ? $attempt->exam->questions->shuffle() 
                   : $attempt->exam->questions;

        return view('student.exams.attempt', compact('attempt', 'questions'));
    }

    // AJAX - Save answer
    public function saveAnswer(Request $request, $attemptToken)
    {
        $attempt = ExamAttempt::where('attempt_token', $attemptToken)->firstOrFail();

        if ($attempt->student_id !== auth()->user()->student->id || $attempt->isSubmitted()) {
            return response()->json(['success' => false, 'message' => 'Invalid attempt'], 403);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'option_id' => 'nullable|exists:question_options,id',
            'is_marked_for_review' => 'nullable|boolean',
        ]);

        try {
            $answer = ExamAnswer::where('attempt_id', $attempt->id)
                               ->where('question_id', $request->question_id)
                               ->first();

            if (!$answer) {
                return response()->json(['success' => false, 'message' => 'Invalid question'], 400);
            }

            $answer->update([
                'selected_option_id' => $request->option_id,
                'is_marked_for_review' => $request->is_marked_for_review ?? false,
                'last_answered_at' => now(),
                'first_answered_at' => $answer->first_answered_at ?? now(),
            ]);

            return response()->json([
                'success' => true,
                'status' => $answer->status
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to save answer'], 500);
        }
    }

    // AJAX - Track time on question
    public function trackTime(Request $request, $attemptToken)
    {
        $attempt = ExamAttempt::where('attempt_token', $attemptToken)->firstOrFail();

        if ($attempt->student_id !== auth()->user()->student->id || $attempt->isSubmitted()) {
            return response()->json(['success' => false], 403);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'time_spent' => 'required|integer|min:0',
        ]);

        try {
            $answer = ExamAnswer::where('attempt_id', $attempt->id)
                               ->where('question_id', $request->question_id)
                               ->first();

            if ($answer) {
                $answer->increment('time_spent_seconds', $request->time_spent);
                $answer->increment('visit_count');

                // Log time entry
                ExamAnswerTimeLog::create([
                    'answer_id' => $answer->id,
                    'entered_at' => now()->subSeconds($request->time_spent),
                    'exited_at' => now(),
                    'duration_seconds' => $request->time_spent,
                ]);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    // AJAX - Get exam status (for polling)
    public function getStatus($attemptToken)
    {
        $attempt = ExamAttempt::where('attempt_token', $attemptToken)->firstOrFail();

        if ($attempt->student_id !== auth()->user()->student->id) {
            return response()->json(['success' => false], 403);
        }

        $remainingSeconds = $attempt->getRemainingTimeSeconds();

        // Auto-submit if time expired
        if ($remainingSeconds <= 0 && $attempt->isInProgress()) {
            $this->autoSubmit($attempt);
            
            return response()->json([
                'success' => true,
                'time_expired' => true,
                'redirect_url' => route('student.exams.index')
            ]);
        }

        return response()->json([
            'success' => true,
            'remaining_seconds' => $remainingSeconds,
            'time_expired' => false,
        ]);
    }

    public function submit(Request $request, $attemptToken)
    {
        $attempt = ExamAttempt::where('attempt_token', $attemptToken)->firstOrFail();

        if ($attempt->student_id !== auth()->user()->student->id || $attempt->isSubmitted()) {
            return response()->json(['success' => false, 'message' => 'Invalid attempt'], 403);
        }

        DB::beginTransaction();
        try {
            $attempt->update([
                'submitted_at' => now(),
                'status' => 'submitted',
                'time_taken_seconds' => now()->diffInSeconds($attempt->started_at),
            ]);

            // Calculate result
            $this->calculateResult($attempt);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Exam submitted successfully',
                'redirect_url' => route('student.exams.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to submit exam'], 500);
        }
    }

    private function autoSubmit(ExamAttempt $attempt)
    {
        DB::beginTransaction();
        try {
            $attempt->update([
                'auto_submitted_at' => now(),
                'status' => 'auto_submitted',
                'time_taken_seconds' => $attempt->exam->duration_minutes * 60,
            ]);

            $this->calculateResult($attempt);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }

    private function calculateResult(ExamAttempt $attempt)
    {
        // Import result calculation service
        app(\App\Services\ResultCalculationService::class)->calculate($attempt);
    }
}
