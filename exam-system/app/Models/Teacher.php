<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'employee_code', 'qualification', 'subjects'
    ];

    protected $casts = [
        'subjects' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'created_by', 'user_id');
    }
}
