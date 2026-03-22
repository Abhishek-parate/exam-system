<?php
// database/seeders/AdminUserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Get role IDs
        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        $teacherRoleId = DB::table('roles')->where('name', 'teacher')->value('id');
        $studentRoleId = DB::table('roles')->where('name', 'student')->value('id');
        $parentRoleId = DB::table('roles')->where('name', 'parent')->value('id');

        // Create Admin User
        $adminId = DB::table('users')->insertGetId([
            'name' => 'Admin User',
            'email' => 'admin@exam.com',
            'mobile' => '9876543210',
            'password' => Hash::make('password'),
            'role_id' => $adminRoleId,
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Teacher User
        $teacherId = DB::table('users')->insertGetId([
            'name' => 'Test Teacher',
            'email' => 'teacher@exam.com',
            'mobile' => '9876543211',
            'password' => Hash::make('password'),
            'role_id' => $teacherRoleId,
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('teachers')->insert([
            'user_id' => $teacherId,
            'employee_code' => 'TCH001',
            'qualification' => 'M.Sc. Physics',
            'subjects' => json_encode(['Physics', 'Mathematics']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Student User
        $studentId = DB::table('users')->insertGetId([
            'name' => 'Test Student',
            'email' => 'student@exam.com',
            'mobile' => '9876543212',
            'password' => Hash::make('password'),
            'role_id' => $studentRoleId,
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $studentRecordId = DB::table('students')->insertGetId([
            'user_id' => $studentId,
            'enrollment_number' => 'STU2025001',
            'class' => '12th',
            'date_of_birth' => '2007-01-15',
            'address' => 'Nagpur, Maharashtra',
            'target_exam' => 'JEE',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Parent User
        $parentId = DB::table('users')->insertGetId([
            'name' => 'Test Parent',
            'email' => 'parent@exam.com',
            'mobile' => '9876543213',
            'password' => Hash::make('password'),
            'role_id' => $parentRoleId,
            'is_active' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $parentRecordId = DB::table('parents')->insertGetId([
            'user_id' => $parentId,
            'relation' => 'Father',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Link parent to student
        DB::table('parent_student')->insert([
            'parent_id' => $parentRecordId,
            'student_id' => $studentRecordId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "\n✅ Demo users created:\n";
        echo "Admin: admin@exam.com / password\n";
        echo "Teacher: teacher@exam.com / password\n";
        echo "Student: student@exam.com / password\n";
        echo "Parent: parent@exam.com / password\n\n";
    }
}
