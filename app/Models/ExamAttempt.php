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
        // ✅ FIX: cheat-tracking columns added to fillable
        'tab_switch_count',
        'fullscreen_exit_count',
        'copy_attempts',
    ];

    protected $casts = [
        'started_at'            => 'datetime',
        'submitted_at'          => 'datetime',
        'auto_submitted_at'     => 'datetime',
        // ✅ FIX: cast IDs to integer so strict !== comparisons work correctly
        //    PDO can return integer columns as strings; explicit cast prevents
        //    the false 403 on the attempt page.
        'exam_id'               => 'integer',
        'student_id'            => 'integer',
        'time_taken_seconds'    => 'integer',
        'tab_switch_count'      => 'integer',
        'fullscreen_exit_count' => 'integer',
        'copy_attempts'         => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attempt) {
            $attempt->attempt_token = Str::uuid();

            // ✅ Ensure cheat counters start at 0, not null
            $attempt->tab_switch_count      = $attempt->tab_switch_count      ?? 0;
            $attempt->fullscreen_exit_count = $attempt->fullscreen_exit_count ?? 0;
            $attempt->copy_attempts         = $attempt->copy_attempts         ?? 0;
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