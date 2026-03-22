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
        'text_answer',
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
        'time_spent_seconds'   => 'integer',
        'visit_count'          => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────

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

    // ── Status Helpers ─────────────────────────────────────────

    /**
     * ✅ FIX — PRIMARY BUG: This method was completely missing from the model.
     * ResultCalculationService calls $answer->isAttempted() for every answer
     * in the loop. PHP throws BadMethodCallException → caught silently →
     * "0 result(s) calculated. 1 attempt(s) still failed."
     *
     * An answer is "attempted" when:
     *  - MCQ:        a selected_option_id has been saved
     *  - Subjective: a non-empty text_answer has been saved
     */
    public function isAttempted(): bool
    {
        $type = $this->question?->question_type ?? 'mcq';

        if ($type === 'subjective') {
            return ! empty(trim((string) $this->text_answer));
        }

        // MCQ / default
        return ! is_null($this->selected_option_id);
    }

    /**
     * Check if this answer is correct.
     *
     * ✅ FIX — SECONDARY BUG: The MCQ branch previously called
     * $question->options()->where(...)->exists() — this fires a brand-new
     * SQL query even when options are already eager-loaded, and more
     * importantly it bypasses the in-memory collection, meaning it can
     * return stale results inside the same transaction.
     * Now we use the already-loaded 'options' collection when available,
     * falling back to a query only if the relation wasn't eager-loaded.
     */
    public function isCorrect(): bool
    {
        $question = $this->question;

        if (! $question) {
            return false;
        }

        $type = $question->question_type ?? 'mcq';

        // ── Subjective ─────────────────────────────────────────
        if ($type === 'subjective') {
            if (empty($this->text_answer) || empty($question->correct_answer)) {
                return false;
            }
            return strtolower(trim($this->text_answer))
                === strtolower(trim($question->correct_answer));
        }

        // ── MCQ ────────────────────────────────────────────────
        if (is_null($this->selected_option_id)) {
            return false;
        }

        // Use eager-loaded collection to avoid extra queries
        if ($question->relationLoaded('options')) {
            return $question->options
                ->where('id', $this->selected_option_id)
                ->where('is_correct', true)
                ->isNotEmpty();
        }

        // Fallback: query (only if options weren't eager-loaded)
        return $question->options()
                        ->where('id', $this->selected_option_id)
                        ->where('is_correct', true)
                        ->exists();
    }
}