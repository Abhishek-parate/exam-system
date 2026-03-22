<?php

namespace App\Services;

use App\Models\ExamAttempt;
use App\Models\ExamResult;
use App\Models\ExamResultSubject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResultCalculationService
{
    public function calculate(ExamAttempt $attempt): ExamResult
    {
        // Idempotent — return existing result if already calculated
        $existing = ExamResult::where('attempt_id', $attempt->id)->first();
        if ($existing) {
            return $existing;
        }

        DB::beginTransaction();
        try {
            // ✅ FIX: Eager-load everything needed so no lazy-load queries
            //    fire inside the loop (prevents N+1 and stale-transaction issues).
            //    Added 'question.options' so isCorrect() uses the collection,
            //    not a new SQL query inside the transaction.
            $exam    = $attempt->exam()->with(['markingSchemes', 'questions'])->first();
            $answers = $attempt->answers()
                               ->with([
                                   'question.subject',
                                   'question.options', // ✅ needed for isCorrect()
                                   'selectedOption',
                               ])
                               ->get();

            $correctCount         = 0;
            $wrongCount           = 0;
            $unattemptedCount     = 0;
            $markedForReviewCount = 0;
            $obtainedMarks        = 0.0;
            $subjectWiseData      = [];

            foreach ($answers as $answer) {
                $question = $answer->question;

                // Guard: question deleted after attempt started
                if (! $question) {
                    Log::warning("ResultCalc: answer {$answer->id} has no question — skipped.");
                    continue;
                }

                $subject     = $question->subject;
                $subjectId   = $subject?->id   ?? 0;
                $subjectName = $subject?->name ?? 'Uncategorised';

                // Initialise subject bucket
                if (! isset($subjectWiseData[$subjectId])) {
                    $subjectWiseData[$subjectId] = [
                        'subject_id'      => $subjectId,
                        'total_questions' => 0,
                        'correct'         => 0,
                        'wrong'           => 0,
                        'unattempted'     => 0,
                        'marks'           => 0.0,
                        'total_time'      => 0,
                    ];
                }

                $subjectWiseData[$subjectId]['total_questions']++;
                $subjectWiseData[$subjectId]['total_time'] += (int) $answer->time_spent_seconds;

                if ($answer->is_marked_for_review) {
                    $markedForReviewCount++;
                }

                // Marking scheme: per-subject → fallback to question's own marks → fallback to 1/0
                $markingScheme = $subjectId
                    ? $exam->markingSchemes->where('subject_id', $subjectId)->first()
                    : null;

                $correctMarks  = (float) ($markingScheme?->correct_marks  ?? $question->marks          ?? 1);
                $negativeMarks = (float) ($markingScheme?->wrong_marks     ?? $question->negative_marks ?? 0);

                // ✅ PRIMARY BUG FIX: isAttempted() now exists on ExamAnswer model
                if ($answer->isAttempted()) {
                    if ($answer->isCorrect()) {
                        $correctCount++;
                        $subjectWiseData[$subjectId]['correct']++;
                        $obtainedMarks                        += $correctMarks;
                        $subjectWiseData[$subjectId]['marks'] += $correctMarks;
                    } else {
                        $wrongCount++;
                        $subjectWiseData[$subjectId]['wrong']++;
                        $obtainedMarks                        -= $negativeMarks;
                        $subjectWiseData[$subjectId]['marks'] -= $negativeMarks;
                    }
                } else {
                    $unattemptedCount++;
                    $subjectWiseData[$subjectId]['unattempted']++;
                }
            }

            $totalQuestions = $correctCount + $wrongCount + $unattemptedCount;
            $accuracyPct    = $totalQuestions > 0
                              ? ($correctCount / $totalQuestions) * 100
                              : 0.0;

            // ✅ FIX: Guard against missing show_results_immediately column —
            //    use null-coalescing so it degrades to false, not a fatal error.
            $showImmediately = (bool) ($exam->show_results_immediately ?? false);

            $result = ExamResult::create([
                'attempt_id'          => $attempt->id,
                'exam_id'             => $exam->id,
                'student_id'          => $attempt->student_id,
                'total_marks'         => (float) ($exam->total_marks ?? 0),
                'obtained_marks'      => max(0.0, round($obtainedMarks, 2)),
                'correct_answers'     => $correctCount,
                'wrong_answers'       => $wrongCount,
                'unattempted'         => $unattemptedCount,
                'marked_for_review'   => $markedForReviewCount,
                'accuracy_percentage' => round($accuracyPct, 2),
                'is_published'        => $showImmediately,
                'published_at'        => $showImmediately ? now() : null,
            ]);

            // Subject-wise breakdown (skip uncategorised bucket id=0)
            foreach ($subjectWiseData as $subjectId => $data) {
                if ($subjectId === 0) {
                    continue;
                }

                $subTotal    = $data['total_questions'];
                $subAccuracy = $subTotal > 0 ? ($data['correct'] / $subTotal) * 100 : 0.0;
                $avgTime     = $subTotal > 0 ? $data['total_time'] / $subTotal : 0;

                ExamResultSubject::create([
                    'result_id'                 => $result->id,
                    'subject_id'                => $data['subject_id'],
                    'total_questions'           => $data['total_questions'],
                    'correct_answers'           => $data['correct'],
                    'wrong_answers'             => $data['wrong'],
                    'unattempted'               => $data['unattempted'],
                    'marks_obtained'            => max(0.0, round($data['marks'], 2)),
                    'accuracy_percentage'       => round($subAccuracy, 2),
                    'average_time_per_question' => (int) round($avgTime),
                ]);
            }

            // Recalculate rank for all students in this exam
            $this->calculateRank($result);

            DB::commit();

            Log::info("ResultCalc: attempt {$attempt->id} → result {$result->id} | "
                . "correct={$correctCount} wrong={$wrongCount} unattempted={$unattemptedCount} "
                . "marks={$result->obtained_marks}/{$result->total_marks}");

            return $result;

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("ResultCalculationService failed for attempt {$attempt->id}: {$e->getMessage()}", [
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Recalculate rank for every result in the exam so ranks stay
     * accurate when multiple students submit close together.
     */
    private function calculateRank(ExamResult $newResult): void
    {
        $allResults = ExamResult::where('exam_id', $newResult->exam_id)
                                ->orderByDesc('obtained_marks')
                                ->get();

        $total = $allResults->count();

        foreach ($allResults as $rank => $result) {
            $result->update([
                'rank'               => $rank + 1,
                'total_participants' => $total,
            ]);
        }
    }
}