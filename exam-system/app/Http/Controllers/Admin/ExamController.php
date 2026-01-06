<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamCategory;
use App\Models\ExamMarkingScheme;
use App\Models\Question;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ExamController extends Controller
{
    /**
     * Display a listing of exams
     */
    public function index()
    {
        $exams = Exam::with(['examCategory', 'creator', 'schoolClass'])
            ->latest()
            ->paginate(15);

        return view('admin.exams.index', compact('exams'));
    }

    /**
     * Show the form for creating a new exam
     */
    public function create()
    {
        $examCategories = ExamCategory::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        $classes = SchoolClass::where('is_active', true)->get();
        
        // Load teachers with subjects using direct database query
        $teachers = Teacher::with('user')->get()->map(function($teacher) {
            // Get subjects directly from pivot table
            $subjectIds = DB::table('teacher_subject')
                ->where('teacher_id', $teacher->id)
                ->pluck('subject_id')
                ->toArray();
            
            // Load full subject details
            if (!empty($subjectIds)) {
                $teacher->subjects = Subject::whereIn('id', $subjectIds)->get();
            } else {
                $teacher->subjects = collect([]);
            }
            
            Log::info('Teacher loaded for exam create:', [
                'teacher_id' => $teacher->id,
                'user_name' => $teacher->user->name ?? 'Unknown',
                'subject_ids' => $subjectIds,
                'subject_names' => $teacher->subjects->pluck('name')->toArray()
            ]);
            
            return $teacher;
        });

        // Get all questions with their relationships
        $questions = Question::with(['subject', 'examCategory'])
            ->where('is_active', true)
            ->whereHas('subject')
            ->whereHas('examCategory')
            ->get();

        return view('admin.exams.create', compact('examCategories', 'subjects', 'questions', 'classes', 'teachers'));
    }

    /**
     * Store a newly created exam in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'exam_category_id' => 'required|exists:exam_categories,id',
            'school_class_id' => 'nullable|exists:school_classes,id',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'total_questions' => 'required|integer|min:1',
            'passing_marks' => 'required|numeric|min:0',
            'total_marks' => 'required|numeric|min:0',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'result_release_time' => 'nullable|date|after:end_time',
            'randomize_questions' => 'boolean',
            'randomize_options' => 'boolean',
            'show_results_immediately' => 'boolean',
            'allow_resume' => 'boolean',
            'is_active' => 'boolean',
            'assigned_teacher_id' => 'nullable|exists:teachers,id',
            'selected_questions' => 'required|array|min:1',
            'selected_questions.*' => 'exists:questions,id',
            'marking_schemes' => 'nullable|array',
        ]);

        // Convert empty string to null for nullable datetime fields
        if (empty($validated['result_release_time'])) {
            $validated['result_release_time'] = null;
        }

        $validated['exam_code'] = 'EXM-' . strtoupper(Str::random(8));
        $validated['created_by'] = auth()->id();

        DB::beginTransaction();
        try {
            $exam = Exam::create($validated);

            // Attach selected questions with display order
            if (!empty($request->selected_questions)) {
                $displayOrder = 1;
                foreach ($request->selected_questions as $questionId) {
                    $exam->questions()->attach($questionId, ['display_order' => $displayOrder++]);
                }

                // 🎯 FIX: Get unique subjects from selected questions
                $selectedQuestionSubjects = Question::whereIn('id', $request->selected_questions)
                    ->pluck('subject_id')
                    ->unique()
                    ->toArray();

                Log::info('Creating marking schemes for subjects:', [
                    'subject_ids' => $selectedQuestionSubjects,
                    'correct_marks' => $request->input('marking_schemes.0.correct_marks', 1),
                    'wrong_marks' => $request->input('marking_schemes.0.wrong_marks', 0.25)
                ]);

                // Get marking values from form
                $correctMarks = $request->input('marking_schemes.0.correct_marks', 1);
                $wrongMarks = $request->input('marking_schemes.0.wrong_marks', 0.25);

                // Create marking scheme for each subject
                foreach ($selectedQuestionSubjects as $subjectId) {
                    ExamMarkingScheme::create([
                        'exam_id' => $exam->id,
                        'subject_id' => $subjectId,
                        'correct_marks' => $correctMarks,
                        'wrong_marks' => $wrongMarks,
                        'unattempted_marks' => 0,
                    ]);
                }
            }

            DB::commit();
            
            Log::info('Exam created successfully:', [
                'exam_id' => $exam->id,
                'exam_code' => $exam->exam_code,
                'title' => $exam->title
            ]);
            
            return redirect()->route('admin.exams.show', $exam)
                ->with('success', 'Exam created successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Exam creation failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to create exam: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * 🎯 FIXED: Display the specified exam with statistics
     */
    public function show(Exam $exam)
    {
        $exam->load(['examCategory', 'schoolClass', 'creator', 'questions.subject', 'markingSchemes.subject']);
        
        // Calculate statistics
        $stats = [
            'total_questions' => $exam->questions->count(),
            'total_enrolled' => $exam->enrolledStudents()->count(),
            'total_attempts' => $exam->attempts()->count(),
            'total_completed' => $exam->attempts()->where('status', 'completed')->count(),
        ];
        
        return view('admin.exams.show', compact('exam', 'stats'));
    }

    /**
     * Show the form for editing the specified exam
     */
    public function edit(Exam $exam)
    {
        $examCategories = ExamCategory::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        $classes = SchoolClass::where('is_active', true)->get();
        
        // Load teachers with subjects
        $teachers = Teacher::with('user')->get()->map(function($teacher) {
            $subjectIds = DB::table('teacher_subject')
                ->where('teacher_id', $teacher->id)
                ->pluck('subject_id')
                ->toArray();
            
            if (!empty($subjectIds)) {
                $teacher->subjects = Subject::whereIn('id', $subjectIds)->get();
            } else {
                $teacher->subjects = collect([]);
            }
            
            return $teacher;
        });

        $questions = Question::with(['subject', 'examCategory'])
            ->where('is_active', true)
            ->get();

        $exam->load(['questions', 'schoolClass', 'examCategory', 'markingSchemes']);

        return view('admin.exams.edit', compact('exam', 'examCategories', 'subjects', 'questions', 'classes', 'teachers'));
    }

    /**
     * Update the specified exam in storage
     */
    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'exam_category_id' => 'required|exists:exam_categories,id',
            'school_class_id' => 'nullable|exists:school_classes,id',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'total_questions' => 'required|integer|min:1',
            'passing_marks' => 'required|numeric|min:0',
            'total_marks' => 'required|numeric|min:0',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'result_release_time' => 'nullable|date|after:end_time',
            'randomize_questions' => 'boolean',
            'randomize_options' => 'boolean',
            'show_results_immediately' => 'boolean',
            'allow_resume' => 'boolean',
            'assigned_teacher_id' => 'nullable|exists:teachers,id',
        ]);

        // Convert empty result_release_time to null
        if (empty($validated['result_release_time'])) {
            $validated['result_release_time'] = null;
        }

        DB::beginTransaction();
        try {
            $exam->update($validated);

            DB::commit();
            return redirect()->route('admin.exams.show', $exam)
                ->with('success', 'Exam updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update exam: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified exam from storage
     */
    public function destroy(Exam $exam)
    {
        try {
            DB::beginTransaction();

            // Delete related records
            $exam->questions()->detach();
            $exam->markingSchemes()->delete();

            // Delete exam
            $exam->delete();

            DB::commit();
            return redirect()->route('admin.exams.index')
                ->with('success', 'Exam deleted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete exam: ' . $e->getMessage());
        }
    }

    /**
     * 🎯 AJAX endpoint to get teacher subjects and questions
     */
    public function getTeacherQuestions(Request $request)
    {
        try {
            $teacherId = $request->input('teacher_id');

            if (!$teacherId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher ID is required'
                ], 400);
            }

            // Get teacher's subject IDs
            $subjectIds = DB::table('teacher_subject')
                ->where('teacher_id', $teacherId)
                ->pluck('subject_id')
                ->toArray();

            Log::info('Fetching questions for teacher:', [
                'teacher_id' => $teacherId,
                'subject_ids' => $subjectIds
            ]);

            if (empty($subjectIds)) {
                return response()->json([
                    'success' => true,
                    'subject_ids' => [],
                    'subjects' => [],
                    'questions' => [],
                    'questions_count' => 0,
                    'message' => 'No subjects assigned to this teacher'
                ]);
            }

            // Get subjects details
            $subjects = Subject::whereIn('id', $subjectIds)->get();

            // Get questions for these subjects
            $questions = Question::with(['subject', 'examCategory'])
                ->whereIn('subject_id', $subjectIds)
                ->where('is_active', true)
                ->get();

            Log::info('Questions fetched successfully:', [
                'questions_count' => $questions->count(),
                'subjects_count' => $subjects->count()
            ]);

            return response()->json([
                'success' => true,
                'subject_ids' => $subjectIds,
                'subjects' => $subjects,
                'questions' => $questions,
                'questions_count' => $questions->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching teacher questions:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading questions: ' . $e->getMessage()
            ], 500);
        }
    }
}
