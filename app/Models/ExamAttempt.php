<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id', 'student_id', 'attempt_token', 
        'started_at', 'submitted_at', 'auto_submitted_at',
        'time_taken_seconds', 'status', 'ip_address', 'user_agent'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'auto_submitted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($attempt) {
            $attempt->attempt_token = Str::uuid();
        });
    }

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

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isSubmitted()
    {
        return in_array($this->status, ['submitted', 'auto_submitted']);
    }

    public function getRemainingTimeSeconds()
    {
        if (!$this->started_at) {
            return $this->exam->duration_minutes * 60;
        }

        $elapsedSeconds = now()->diffInSeconds($this->started_at);
        $totalAllowedSeconds = $this->exam->duration_minutes * 60;
        
        return max(0, $totalAllowedSeconds - $elapsedSeconds);
    }
}
