<?php
// database/seeders/RoleSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'admin', 'display_name' => 'Administrator', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'teacher', 'display_name' => 'Teacher', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'student', 'display_name' => 'Student', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'parent', 'display_name' => 'Parent', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('roles')->insert($roles);
    }
}
