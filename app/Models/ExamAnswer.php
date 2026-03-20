<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'selected_option_id',
        'text_answer',          // ← NEW: for subjective questions
        'is_marked_for_review',
        'time_spent_seconds',
        'visit_count',
        'first_answered_at',
        'last_answered_at',
    ];

    protected $casts = [
        'is_marked_for_review' => 'boolean',
        'first_answered_at'    => 'datetime',
        'last_answered_at'     => 'datetime',
    ];

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function selectedOption()
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }

    /**
     * Check if this answer is correct.
     * Works for both MCQ and Subjective question types.
     */
    public function isCorrect(): bool
    {
        $question = $this->question;

        if (! $question) return false;

        if (($question->question_type ?? 'mcq') === 'subjective') {
            // Case-insensitive, trim-safe comparison
            if (! $this->text_answer || ! $question->correct_answer) return false;
            return strtolower(trim($this->text_answer)) === strtolower(trim($question->correct_answer));
        }

        // MCQ
        if (! $this->selected_option_id) return false;
        return $question->options()
                        ->where('id', $this->selected_option_id)
                        ->where('is_correct', true)
                        ->exists();
    }
}