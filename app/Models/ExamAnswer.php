<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id', 'question_id', 'selected_option_id',
        'is_marked_for_review', 'time_spent_seconds', 'visit_count',
        'first_answered_at', 'last_answered_at'
    ];

    protected $casts = [
        'is_marked_for_review' => 'boolean',
        'first_answered_at'    => 'datetime',
        'last_answered_at'     => 'datetime',
    ];

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function selectedOption()
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }

    public function timeLogs()
    {
        return $this->hasMany(ExamAnswerTimeLog::class, 'answer_id');
    }

    /**
     * ✅ FIXED: null-safe — selectedOption may be null if the option was deleted.
     */
    public function isCorrect(): bool
    {
        if (! $this->selected_option_id) {
            return false;
        }

        // selectedOption relationship may return null if the option row was deleted
        return (bool) ($this->selectedOption?->is_correct ?? false);
    }

    public function isAttempted(): bool
    {
        return $this->selected_option_id !== null;
    }

    public function getStatusAttribute(): string
    {
        if ($this->is_marked_for_review) {
            return 'review';
        }

        if ($this->isAttempted()) {
            return 'attempted';
        }

        return 'not_attempted';
    }
}