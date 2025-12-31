<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Student;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_students' => Student::count(),
            'total_teachers' => User::whereHas('role', function($q) {
                $q->where('name', 'teacher');
            })->count(),
            'total_exams' => Exam::count(),
            'total_questions' => Question::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
