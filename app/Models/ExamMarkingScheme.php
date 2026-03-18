<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamMarkingScheme extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id', 'subject_id', 'correct_marks', 
        'wrong_marks', 'unattempted_marks'
    ];

    protected $casts = [
        'correct_marks' => 'decimal:2',
        'wrong_marks' => 'decimal:2',
        'unattempted_marks' => 'decimal:2',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
