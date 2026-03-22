<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResultSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'result_id', 'subject_id', 'total_questions',
        'correct_answers', 'wrong_answers', 'unattempted',
        'marks_obtained', 'accuracy_percentage', 'average_time_per_question'
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'accuracy_percentage' => 'decimal:2',
    ];

    public function result()
    {
        return $this->belongsTo(ExamResult::class, 'result_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
