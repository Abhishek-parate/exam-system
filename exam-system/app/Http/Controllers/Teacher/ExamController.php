<?php

namespace App\Http\Controllers\Teacher;

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
     * Get current logged-in teacher
     */
    private function getCurrentTeacher()
    {
        return Teacher::where('user_id', auth()->id())->firstOrFail();
    }

    /**
     * Get teacher's subject IDs
     */
    private function getTeacherSubjectIds()
    {
        $teacher = $this->getCurrentTeacher();
        return DB::table('teacher_subject')
            ->where('teacher_id', $teacher->id)
            ->pluck('subject_id')
            ->toArray();
    }

    /**
     * Display a listing of exams created by this teacher
     */
    public function index()
    {
        $teacher = $this->getCurrentTeacher();
        
        $exams = Exam::with(['examCategory', 'creator', 'schoolClass'])
            ->where('assigned_teacher_id', $teacher->id)
            ->orWhere('created_by', auth()->id())
            ->latest()
            ->paginate(15);

        return view('teacher.exams.index', compact('exams'));
    }

    /**
     * Show the form for creating a new exam
     */
    public function create()
    {
        $teacher = $this->getCurrentTeacher();
        $subjectIds = $this->getTeacherSubjectIds();

        // Check if teacher has any subjects assigned
        if (empty($subjectIds)) {
            return redirect()->route('teacher.exams.index')
                ->with('error', 'You do not have any subjects assigned. Please contact the administrator.');
        }

        $examCategories = ExamCategory::where('is_active', true)->get();
        $subjects = Subject::whereIn('id', $subjectIds)->where('is_active', true)->get();
        $classes = SchoolClass::where('is_active', true)->get();

        // Get questions only from teacher's subjects
        $questions = Question::with(['subject', 'examCategory'])
            ->whereIn('subject_id', $subjectIds)
            ->where('is_active', true)
            ->whereHas('subject')
            ->whereHas('examCategory')
            ->get();

        // Load teacher with subjects
        $teacherWithSubjects = Teacher::with('user')->find($teacher->id);
        $teacherWithSubjects->subjects = Subject::whereIn('id', $subjectIds)->get();

        Log::info('Teacher creating exam:', [
            'teacher_id' => $teacher->id,
            'subject_ids' => $subjectIds,
            'questions_count' => $questions->count()
        ]);

        return view('teacher.exams.create', compact(
            'examCategories',
            'subjects',
            'questions',
            'classes',
            'teacher',
            'teacherWithSubjects'
        ));
    }

    /**
     * Store a newly created exam
     */
    public function store(Request $request)
    {
        $teacher = $this->getCurrentTeacher();
        $subjectIds = $this->getTeacherSubjectIds();

        // Validate that selected questions belong to teacher's subjects
        $selectedQuestions = $request->input('selected_questions', []);
        $invalidQuestions = Question::whereIn('id', $selectedQuestions)
            ->whereNotIn('subject_id', $subjectIds)
            ->count();

        if ($invalidQuestions > 0) {
            return back()
                ->with('error', 'You can only select questions from your assigned subjects!')
                ->withInput();
        }

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
            'selected_questions' => 'required|array|min:1',
            'selected_questions.*' => 'exists:questions,id',
            'marking_schemes' => 'nullable|array',
        ]);

        // Convert empty string to null
        if (empty($validated['result_release_time'])) {
            $validated['result_release_time'] = null;
        }

        $validated['exam_code'] = 'EXM-' . strtoupper(Str::random(8));
        $validated['created_by'] = auth()->id();
        $validated['assigned_teacher_id'] = $teacher->id;

        DB::beginTransaction();
        try {
            $exam = Exam::create($validated);

            // Attach selected questions
            if (!empty($request->selected_questions)) {
                $displayOrder = 1;
                foreach ($request->selected_questions as $questionId) {
                    $exam->questions()->attach($questionId, ['display_order' => $displayOrder++]);
                }

                // Create marking schemes for each subject
                $selectedQuestionSubjects = Question::whereIn('id', $request->selected_questions)
                    ->pluck('subject_id')
                    ->unique()
                    ->toArray();

                $correctMarks = $request->input('marking_schemes.0.correct_marks', 1);
                $wrongMarks = $request->input('marking_schemes.0.wrong_marks', 0.25);

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
            
            Log::info('Teacher created exam successfully:', [
                'teacher_id' => $teacher->id,
                'exam_id' => $exam->id,
                'exam_code' => $exam->exam_code
            ]);
            
            return redirect()->route('teacher.exams.show', $exam)
                ->with('success', 'Exam created successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Teacher exam creation failed:', [
                'teacher_id' => $teacher->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to create exam: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified exam
     */
    public function show(Exam $exam)
    {
        $teacher = $this->getCurrentTeacher();
        
        // Verify teacher owns this exam
        if ($exam->assigned_teacher_id != $teacher->id && $exam->created_by != auth()->id()) {
            abort(403, 'Unauthorized access to this exam.');
        }

        $exam->load(['examCategory', 'schoolClass', 'creator', 'questions.subject', 'markingSchemes.subject']);
        
        // Calculate statistics
        $stats = [
            'total_questions' => $exam->questions->count(),
            'total_enrolled' => $exam->enrolledStudents()->count(),
            'total_attempts' => $exam->attempts()->count(),
            'total_completed' => $exam->attempts()->where('status', 'completed')->count(),
        ];
        
        return view('teacher.exams.show', compact('exam', 'stats'));
    }

    /**
     * Show the form for editing the specified exam
     */
    public function edit(Exam $exam)
    {
        $teacher = $this->getCurrentTeacher();
        
        // Verify teacher owns this exam
        if ($exam->assigned_teacher_id != $teacher->id && $exam->created_by != auth()->id()) {
            abort(403, 'Unauthorized access to this exam.');
        }

        $subjectIds = $this->getTeacherSubjectIds();
        
        $examCategories = ExamCategory::where('is_active', true)->get();
        $subjects = Subject::whereIn('id', $subjectIds)->where('is_active', true)->get();
        $classes = SchoolClass::where('is_active', true)->get();

        $questions = Question::with(['subject', 'examCategory'])
            ->whereIn('subject_id', $subjectIds)
            ->where('is_active', true)
            ->get();

        $exam->load(['questions', 'schoolClass', 'examCategory', 'markingSchemes']);

        // 🔥 ADDED: Pass selected question IDs to view
        $selectedQuestionIds = $exam->questions->pluck('id')->toArray();

        return view('teacher.exams.edit', compact('exam', 'examCategories', 'subjects', 'questions', 'classes', 'selectedQuestionIds'));
    }

    /**
     * Update the specified exam
     */
    public function update(Request $request, Exam $exam)
    {
        $teacher = $this->getCurrentTeacher();
        
        // Verify teacher owns this exam
        if ($exam->assigned_teacher_id != $teacher->id && $exam->created_by != auth()->id()) {
            abort(403, 'Unauthorized access to this exam.');
        }

        $subjectIds = $this->getTeacherSubjectIds();

        // 🔥 ADDED: Validate that selected questions belong to teacher's subjects
        if ($request->has('selected_questions')) {
            $selectedQuestions = $request->input('selected_questions', []);
            $invalidQuestions = Question::whereIn('id', $selectedQuestions)
                ->whereNotIn('subject_id', $subjectIds)
                ->count();

            if ($invalidQuestions > 0) {
                return back()
                    ->with('error', 'You can only select questions from your assigned subjects!')
                    ->withInput();
            }
        }

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
            'selected_questions' => 'nullable|array',
            'selected_questions.*' => 'exists:questions,id',
            'marking_schemes' => 'nullable|array',
        ]);

        if (empty($validated['result_release_time'])) {
            $validated['result_release_time'] = null;
        }

        DB::beginTransaction();
        try {
            // 🔥 UPDATE: Update exam basic details
            $exam->update($validated);

            // 🔥 ADDED: SYNC QUESTIONS - This will add/remove questions based on selection
            if ($request->has('selected_questions') && !empty($request->selected_questions)) {
                $displayOrder = 1;
                $syncData = [];
                foreach ($request->selected_questions as $questionId) {
                    $syncData[$questionId] = ['display_order' => $displayOrder++];
                }
                // sync() will automatically add new and remove old questions
                $exam->questions()->sync($syncData);

                // 🔥 ADDED: Update marking schemes for new questions
                $selectedQuestionSubjects = Question::whereIn('id', $request->selected_questions)
                    ->pluck('subject_id')
                    ->unique()
                    ->toArray();

                // Delete old marking schemes
                $exam->markingSchemes()->delete();

                // Create new marking schemes
                if ($request->has('marking_schemes')) {
                    foreach ($request->marking_schemes as $schemeData) {
                        if (!empty($schemeData['subject_id'])) {
                            ExamMarkingScheme::create([
                                'exam_id' => $exam->id,
                                'subject_id' => $schemeData['subject_id'],
                                'correct_marks' => $schemeData['correct_marks'] ?? 1,
                                'wrong_marks' => $schemeData['wrong_marks'] ?? 0.25,
                                'unattempted_marks' => 0,
                            ]);
                        }
                    }
                } else {
                    // Create default marking schemes for each subject
                    $correctMarks = 1;
                    $wrongMarks = 0.25;
                    
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
            } elseif ($request->has('selected_questions') && empty($request->selected_questions)) {
                // If no questions selected, detach all
                $exam->questions()->detach();
                $exam->markingSchemes()->delete();
            }

            DB::commit();
            
            Log::info('Teacher updated exam successfully:', [
                'teacher_id' => $teacher->id,
                'exam_id' => $exam->id,
                'questions_count' => $request->has('selected_questions') ? count($request->selected_questions) : 0
            ]);
            
            return redirect()->route('teacher.exams.show', $exam)
                ->with('success', 'Exam updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Teacher exam update failed:', [
                'teacher_id' => $teacher->id,
                'exam_id' => $exam->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Failed to update exam: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified exam
     */
    public function destroy(Exam $exam)
    {
        $teacher = $this->getCurrentTeacher();
        
        // Verify teacher owns this exam
        if ($exam->assigned_teacher_id != $teacher->id && $exam->created_by != auth()->id()) {
            abort(403, 'Unauthorized access to this exam.');
        }

        try {
            DB::beginTransaction();

            $exam->questions()->detach();
            $exam->markingSchemes()->delete();
            $exam->delete();

            DB::commit();
            return redirect()->route('teacher.exams.index')
                ->with('success', 'Exam deleted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete exam: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Get teacher's questions
     */
    public function getTeacherQuestions(Request $request)
    {
        try {
            $subjectIds = $this->getTeacherSubjectIds();

            if (empty($subjectIds)) {
                return response()->json([
                    'success' => true,
                    'subject_ids' => [],
                    'subjects' => [],
                    'questions' => [],
                    'questions_count' => 0,
                    'message' => 'No subjects assigned to you'
                ]);
            }

            $subjects = Subject::whereIn('id', $subjectIds)->get();

            $questions = Question::with(['subject', 'examCategory'])
                ->whereIn('subject_id', $subjectIds)
                ->where('is_active', true)
                ->get();

            return response()->json([
                'success' => true,
                'subject_ids' => $subjectIds,
                'subjects' => $subjects,
                'questions' => $questions,
                'questions_count' => $questions->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching teacher questions:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading questions: ' . $e->getMessage()
            ], 500);
        }
    }
}
