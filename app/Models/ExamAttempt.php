<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'student_id',
        'attempt_token',
        'started_at',
        'submitted_at',
        'auto_submitted_at',
        'time_taken_seconds',
        'status',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'started_at'        => 'datetime',
        'submitted_at'      => 'datetime',
        'auto_submitted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attempt) {
            $attempt->attempt_token = Str::uuid();
        });
    }

    // ── Relationships ──────────────────────────────────────────

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class, 'attempt_id');
    }

    public function result()
    {
        return $this->hasOne(ExamResult::class, 'attempt_id');
    }

    // ── Status Helpers ─────────────────────────────────────────

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isSubmitted(): bool
    {
        return in_array($this->status, ['submitted', 'auto_submitted']);
    }

    /**
     * Get the score percentage (delegates to ExamResult if present).
     * Used in blade: $attempt->score
     */
    public function getScoreAttribute(): ?float
    {
        // If result is already loaded
        if ($this->relationLoaded('result') && $this->result) {
            $r = $this->result;
            if ($r->total_marks > 0) {
                return round(($r->obtained_marks / $r->total_marks) * 100, 2);
            }
        }

        // Lazy load result
        $result = $this->result()->first();
        if ($result && $result->total_marks > 0) {
            return round(($result->obtained_marks / $result->total_marks) * 100, 2);
        }

        return null;
    }

    /**
     * Remaining time in seconds for the student's exam timer.
     */
    public function getRemainingTimeSeconds(): int
    {
        if (!$this->started_at) {
            return $this->exam->duration_minutes * 60;
        }

        $elapsed = now()->diffInSeconds($this->started_at);
        $total   = $this->exam->duration_minutes * 60;

        return max(0, $total - $elapsed);
    }
}
