<?php
// database/seeders/ExamCategorySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExamCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'JEE (Joint Entrance Examination)',
                'slug' => 'jee',
                'description' => 'Joint Entrance Examination for Engineering',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'NEET (National Eligibility cum Entrance Test)',
                'slug' => 'neet',
                'description' => 'Medical Entrance Examination',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'MHT-CET (Maharashtra Common Entrance Test)',
                'slug' => 'mht-cet',
                'description' => 'Maharashtra State Entrance Examination',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        foreach ($categories as $category) {
            $categoryId = DB::table('exam_categories')->insertGetId($category);
            
            // Add subjects based on category
            if ($category['slug'] === 'jee') {
                $subjects = [
                    ['exam_category_id' => $categoryId, 'name' => 'Physics', 'code' => 'JEE-PHY', 'is_active' => true],
                    ['exam_category_id' => $categoryId, 'name' => 'Chemistry', 'code' => 'JEE-CHE', 'is_active' => true],
                    ['exam_category_id' => $categoryId, 'name' => 'Mathematics', 'code' => 'JEE-MAT', 'is_active' => true],
                ];
            } elseif ($category['slug'] === 'neet') {
                $subjects = [
                    ['exam_category_id' => $categoryId, 'name' => 'Physics', 'code' => 'NEET-PHY', 'is_active' => true],
                    ['exam_category_id' => $categoryId, 'name' => 'Chemistry', 'code' => 'NEET-CHE', 'is_active' => true],
                    ['exam_category_id' => $categoryId, 'name' => 'Biology', 'code' => 'NEET-BIO', 'is_active' => true],
                ];
            } else {
                $subjects = [
                    ['exam_category_id' => $categoryId, 'name' => 'Physics', 'code' => 'MHT-PHY', 'is_active' => true],
                    ['exam_category_id' => $categoryId, 'name' => 'Chemistry', 'code' => 'MHT-CHE', 'is_active' => true],
                    ['exam_category_id' => $categoryId, 'name' => 'Mathematics', 'code' => 'MHT-MAT', 'is_active' => true],
                ];
            }

            foreach ($subjects as $subject) {
                $subject['created_at'] = now();
                $subject['updated_at'] = now();
                DB::table('subjects')->insert($subject);
            }
        }
    }
}
