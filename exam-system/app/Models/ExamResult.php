// app/Models/ExamResult.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id', 'exam_id', 'student_id',
        'total_marks', 'obtained_marks', 'correct_answers',
        'wrong_answers', 'unattempted', 'marked_for_review',
        'accuracy_percentage', 'rank', 'total_participants',
        'is_published', 'published_at'
    ];

    protected $casts = [
        'total_marks' => 'decimal:2',
        'obtained_marks' => 'decimal:2',
        'accuracy_percentage' => 'decimal:2',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subjectWiseResults()
    {
        return $this->hasMany(ExamResultSubject::class, 'result_id');
    }

    public function getPercentageAttribute()
    {
        if ($this->total_marks == 0) {
            return 0;
        }
        
        return round(($this->obtained_marks / $this->total_marks) * 100, 2);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
