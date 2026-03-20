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

    // ── Relationships ──────────────────────────────────────────

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

    // ── Helper Methods ─────────────────────────────────────────

    /**
     * Did the student actually pick an option?
     */
    public function isAttempted(): bool
    {
        return !is_null($this->selected_option_id);
    }

    /**
     * Did the student pick the CORRECT option?
     */
    public function isCorrect(): bool
    {
        if (!$this->isAttempted()) {
            return false;
        }

        // Use loaded relation if available (avoid extra query)
        if ($this->relationLoaded('selectedOption') && $this->selectedOption) {
            return (bool) $this->selectedOption->is_correct;
        }

        return QuestionOption::where('id', $this->selected_option_id)
                             ->where('is_correct', true)
                             ->exists();
    }
}
