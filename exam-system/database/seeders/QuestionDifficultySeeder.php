<?php
// database/seeders/QuestionDifficultySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionDifficultySeeder extends Seeder
{
    public function run()
    {
        $difficulties = [
            ['name' => 'Easy', 'level' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Medium', 'level' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hard', 'level' => 3, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('question_difficulties')->insert($difficulties);
    }
}
