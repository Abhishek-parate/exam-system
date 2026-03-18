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
        // If a result already exists for this attempt, return it (idempotent)
        $existing = ExamResult::where('attempt_id', $attempt->id)->first();
        if ($existing) {
            return $existing;
        }

        DB::beginTransaction();
        try {
            $exam    = $attempt->exam;
            $answers = $attempt->answers()
                               ->with(['question.subject', 'question.options', 'selectedOption'])
                               ->get();

            $correctCount         = 0;
            $wrongCount           = 0;
            $unattemptedCount     = 0;
            $markedForReviewCount = 0;
            $obtainedMarks        = 0.0;

            $subjectWiseData = [];

            foreach ($answers as $answer) {
                $question = $answer->question;

                // ✅ FIX 1: Guard against orphaned answer (question deleted after attempt started)
                if (! $question) {
                    Log::warning("ResultCalc: answer {$answer->id} has no question — skipped.");
                    continue;
                }

                $subject = $question->subject;

                // ✅ FIX 2: Guard against question with no subject
                $subjectId   = $subject?->id   ?? 0;   // 0 = "uncategorised"
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

                // ✅ FIX 3: Correct property name  (was $answer->isMarked_for_review)
                if ($answer->is_marked_for_review) {
                    $markedForReviewCount++;
                }

                // Try to get marking scheme for this subject; fall back to question's own marks
                $markingScheme = $subjectId
                    ? $exam->markingSchemes()->where('subject_id', $subjectId)->first()
                    : null;

                // ✅ FIX 4: Fallback marks when no marking scheme exists
                $correctMarks  = $markingScheme?->correct_marks  ?? (float) ($question->marks          ?? 1);
                $negativeMarks = $markingScheme?->wrong_marks     ?? (float) ($question->negative_marks ?? 0);

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

            $totalQuestions    = $correctCount + $wrongCount + $unattemptedCount;
            $accuracyPct       = $totalQuestions > 0
                                 ? ($correctCount / $totalQuestions) * 100
                                 : 0.0;

            // Create main result record
            $result = ExamResult::create([
                'attempt_id'          => $attempt->id,
                'exam_id'             => $exam->id,
                'student_id'          => $attempt->student_id,
                'total_marks'         => (float) $exam->total_marks,
                'obtained_marks'      => max(0.0, round($obtainedMarks, 2)),
                'correct_answers'     => $correctCount,
                'wrong_answers'       => $wrongCount,
                'unattempted'         => $unattemptedCount,
                'marked_for_review'   => $markedForReviewCount,
                'accuracy_percentage' => round($accuracyPct, 2),
                'is_published'        => (bool) $exam->show_results_immediately,
                'published_at'        => $exam->show_results_immediately ? now() : null,
            ]);

            // Create subject-wise result rows
            // Skip the "uncategorised" bucket (id=0) if you don't want a junk row,
            // or keep it — up to you.  Here we skip id=0.
            foreach ($subjectWiseData as $subjectId => $data) {
                if ($subjectId === 0) {
                    continue; // skip questions that had no subject
                }

                $subTotal    = $data['total_questions'];
                $subAccuracy = $subTotal > 0
                               ? ($data['correct'] / $subTotal) * 100
                               : 0.0;
                $avgTime     = $subTotal > 0
                               ? $data['total_time'] / $subTotal
                               : 0;

                ExamResultSubject::create([
                    'result_id'                  => $result->id,
                    'subject_id'                 => $data['subject_id'],
                    'total_questions'            => $data['total_questions'],
                    'correct_answers'            => $data['correct'],
                    'wrong_answers'              => $data['wrong'],
                    'unattempted'                => $data['unattempted'],
                    'marks_obtained'             => max(0.0, round($data['marks'], 2)),
                    'accuracy_percentage'        => round($subAccuracy, 2),
                    'average_time_per_question'  => round($avgTime),
                ]);
            }

            // Recalculate ranks for everyone in this exam
            $this->calculateRank($result);

            DB::commit();

            return $result;

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("ResultCalculationService failed for attempt {$attempt->id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    // =========================================================
    // Recalculate rank for every published/unpublished result in the exam
    // so rank stays accurate when multiple students submit close together.
    // =========================================================
    private function calculateRank(ExamResult $newResult): void
    {
        // Grab all results for this exam ordered by obtained_marks DESC
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