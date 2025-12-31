<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionDifficulty extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'level'];

    public function questions()
    {
        return $this->hasMany(Question::class, 'difficulty_id');
    }
}
