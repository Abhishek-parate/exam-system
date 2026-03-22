<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    // -------------------------------------------------------
    // Auto-create student profile if missing
    // -------------------------------------------------------
    private function getOrCreateStudent(): Student
    {
        $user = Auth::user();
        $user->load('student');

        if ($user->student) return $user->student;

        return DB::transaction(fn() => Student::create([
            'user_id'           => $user->id,
            'enrollment_number' => 'STU-' . str_pad($user->id, 5, '0', STR_PAD_LEFT),
        ]));
    }

    // -------------------------------------------------------
    // Get all exams accessible to this student.
    //
    // ✅ Handles 3 cases:
    //   1. enrollment_type column exists + value is 'open' or NULL → show to all
    //   2. enrollment_type column exists + value is 'enrolled' → only enrolled students
    //   3. enrollment_type column does NOT exist (migration not run) → show ALL active exams
    // -------------------------------------------------------
    private function getAccessibleExams(Student $student)
    {
        $hasEnrollmentType = Schema::hasColumn('exams', 'enrollment_type');

        if (!$hasEnrollmentType) {
            // ✅ Migration not run yet — show ALL active exams so students aren't left with nothing
            return Exam::where('is_active', true)
                        ->with('examCategory')
                        ->get();
        }

        // ✅ Open exams: enrollment_type = 'open' OR NULL (legacy rows before migration)
        $openExams = Exam::where(function ($q) {
                            $q->where('enrollment_type', 'open')
                              ->orWhereNull('enrollment_type');
                         })
                         ->with('examCategory')
                         ->get();

        // ✅ Enrolled-only exams where this student is specifically enrolled
        $enrolledOnlyExams = $student->enrolledExams()
                                      ->where('enrollment_type', 'enrolled')
                                      ->with('examCategory')
                                      ->get();

        return $openExams->merge($enrolledOnlyExams)->unique('id');
    }

    // -------------------------------------------------------
    // Dashboard
    // -------------------------------------------------------
    public function index()
    {
        $student = $this->getOrCreateStudent();
        $now     = now(); // ✅ uses app timezone from config/app.php

        $allAccessibleExams = $this->getAccessibleExams($student);

        // ✅ Available NOW: active + ongoing time window + not yet attempted
        $availableExams = $allAccessibleExams->filter(function ($exam) use ($now, $student) {
            return $exam->is_active
                && $exam->start_time->lte($now)
                && $exam->end_time->gte($now)
                && ! $student->hasAttemptedExam($exam->id);
        })->values();

        // ✅ Upcoming: future start — show regardless of is_active so students can plan ahead
        $upcomingExams = $allAccessibleExams->filter(function ($exam) use ($now) {
            return $exam->start_time->gt($now);
        })->sortBy('start_time')->values();

        // Recent completed attempts
        $completedAttempts = ExamAttempt::with(['exam', 'result'])
            ->where('student_id', $student->id)
            ->whereIn('status', ['submitted', 'auto_submitted'])
            ->latest('submitted_at')
            ->take(5)
            ->get();

        $stats = [
            'total_enrolled'  => $allAccessibleExams->count(),
            'available_now'   => $availableExams->count(),
            'upcoming'        => $upcomingExams->count(),
            'total_completed' => $completedAttempts->count(),
        ];

        return view('student.dashboard', compact(
            'stats', 'availableExams', 'upcomingExams', 'completedAttempts'
        ));
    }

    // -------------------------------------------------------
    // Results
    // -------------------------------------------------------
    public function results()
    {
        $student  = $this->getOrCreateStudent();
        $attempts = ExamAttempt::with(['exam.examCategory', 'result'])
            ->where('student_id', $student->id)
            ->whereIn('status', ['submitted', 'auto_submitted'])
            ->latest('submitted_at')
            ->paginate(10);

        return view('student.results', compact('attempts', 'student'));
    }

    // -------------------------------------------------------
    // Profile
    // -------------------------------------------------------
    public function profile()
    {
        $user    = Auth::user();
        $student = $this->getOrCreateStudent();

        $stats = [
            'enrolledExamsCount'    => $this->getAccessibleExams($student)->count(),
            'examAttemptsCount'     => $student->examAttempts()->count(),
            'publishedResultsCount' => $student->results()->where('is_published', true)->count(),
            'avgPercentage'         => $student->results()
                ->where('is_published', true)->get()
                ->avg(fn($r) => $r->max_marks > 0 ? ($r->total_marks / $r->max_marks * 100) : 0) ?? 0,
        ];

        return view('student.profile', compact('student', 'user', 'stats'));
    }
}