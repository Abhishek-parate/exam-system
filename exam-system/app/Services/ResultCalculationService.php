// app/Services/ResultCalculationService.php
<?php

namespace App\Services;

use App\Models\ExamAttempt;
use App\Models\ExamResult;
use App\Models\ExamResultSubject;
use Illuminate\Support\Facades\DB;

class ResultCalculationService
{
    public function calculate(ExamAttempt $attempt)
    {
        DB::beginTransaction();
        try {
            $exam = $attempt->exam;
            $answers = $attempt->answers()->with(['question.subject', 'selectedOption'])->get();
            
            $correctCount = 0;
            $wrongCount = 0;
            $unattemptedCount = 0;
            $markedForReviewCount = 0;
            $obtainedMarks = 0;
            
            $subjectWiseData = [];

            foreach ($answers as $answer) {
                $question = $answer->question;
                $subject = $question->subject;
                
                // Initialize subject data
                if (!isset($subjectWiseData[$subject->id])) {
                    $subjectWiseData[$subject->id] = [
                        'subject_id' => $subject->id,
                        'total_questions' => 0,
                        'correct' => 0,
                        'wrong' => 0,
                        'unattempted' => 0,
                        'marks' => 0,
                        'total_time' => 0,
                    ];
                }
                
                $subjectWiseData[$subject->id]['total_questions']++;
                $subjectWiseData[$subject->id]['total_time'] += $answer->time_spent_seconds;
                
                // Get marking scheme for this subject
                $markingScheme = $exam->markingSchemes()
                                     ->where('subject_id', $subject->id)
                                     ->first();
                
                if (!$markingScheme) {
                    continue;
                }
                
                if ($answer->isMarked_for_review) {
                    $markedForReviewCount++;
                }
                
                if ($answer->isAttempted()) {
                    if ($answer->isCorrect()) {
                        $correctCount++;
                        $subjectWiseData[$subject->id]['correct']++;
                        $obtainedMarks += $markingScheme->correct_marks;
                        $subjectWiseData[$subject->id]['marks'] += $markingScheme->correct_marks;
                    } else {
                        $wrongCount++;
                        $subjectWiseData[$subject->id]['wrong']++;
                        $obtainedMarks -= $markingScheme->wrong_marks;
                        $subjectWiseData[$subject->id]['marks'] -= $markingScheme->wrong_marks;
                    }
                } else {
                    $unattemptedCount++;
                    $subjectWiseData[$subject->id]['unattempted']++;
                }
            }

            $totalQuestions = $correctCount + $wrongCount + $unattemptedCount;
            $accuracyPercentage = $totalQuestions > 0 ? ($correctCount / $totalQuestions) * 100 : 0;

            // Create main result
            $result = ExamResult::create([
                'attempt_id' => $attempt->id,
                'exam_id' => $exam->id,
                'student_id' => $attempt->student_id,
                'total_marks' => $exam->total_marks,
                'obtained_marks' => max(0, $obtainedMarks),
                'correct_answers' => $correctCount,
                'wrong_answers' => $wrongCount,
                'unattempted' => $unattemptedCount,
                'marked_for_review' => $markedForReviewCount,
                'accuracy_percentage' => round($accuracyPercentage, 2),
                'is_published' => $exam->show_results_immediately,
                'published_at' => $exam->show_results_immediately ? now() : null,
            ]);

            // Create subject-wise results
            foreach ($subjectWiseData as $subjectData) {
                $subjectTotal = $subjectData['total_questions'];
                $subjectAccuracy = $subjectTotal > 0 
                                 ? ($subjectData['correct'] / $subjectTotal) * 100 
                                 : 0;
                                 
                $avgTimePerQuestion = $subjectTotal > 0 
                                    ? $subjectData['total_time'] / $subjectTotal 
                                    : 0;

                ExamResultSubject::create([
                    'result_id' => $result->id,
                    'subject_id' => $subjectData['subject_id'],
                    'total_questions' => $subjectData['total_questions'],
                    'correct_answers' => $subjectData['correct'],
                    'wrong_answers' => $subjectData['wrong'],
                    'unattempted' => $subjectData['unattempted'],
                    'marks_obtained' => max(0, $subjectData['marks']),
                    'accuracy_percentage' => round($subjectAccuracy, 2),
                    'average_time_per_question' => round($avgTimePerQuestion),
                ]);
            }

            // Calculate rank
            $this->calculateRank($result);

            DB::commit();
            
            return $result;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function calculateRank(ExamResult $result)
    {
        $betterResults = ExamResult::where('exam_id', $result->exam_id)
                                   ->where('obtained_marks', '>', $result->obtained_marks)
                                   ->count();

        $totalParticipants = ExamResult::where('exam_id', $result->exam_id)->count();

        $result->update([
            'rank' => $betterResults + 1,
            'total_participants' => $totalParticipants,
        ]);
    }
}
