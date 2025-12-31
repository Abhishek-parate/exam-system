// app/Http/Controllers/Admin/QuestionController.php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\ExamCategory;
use App\Models\Subject;
use App\Models\Chapter;
use App\Models\Topic;
use App\Models\QuestionDifficulty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\QuestionsImport;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with(['examCategory', 'subject', 'difficulty', 'options']);
        
        if ($request->exam_category_id) {
            $query->where('exam_category_id', $request->exam_category_id);
        }
        
        if ($request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }
        
        if ($request->difficulty_id) {
            $query->where('difficulty_id', $request->difficulty_id);
        }
        
        $questions = $query->latest()->paginate(20);
        
        $examCategories = ExamCategory::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        $difficulties = QuestionDifficulty::all();
        
        return view('admin.questions.index', compact('questions', 'examCategories', 'subjects', 'difficulties'));
    }

    public function create()
    {
        $examCategories = ExamCategory::where('is_active', true)->get();
        $difficulties = QuestionDifficulty::all();
        
        return view('admin.questions.create', compact('examCategories', 'difficulties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_category_id' => 'required|exists:exam_categories,id',
            'subject_id' => 'required|exists:subjects,id',
            'chapter_id' => 'nullable|exists:chapters,id',
            'topic_id' => 'nullable|exists:topics,id',
            'difficulty_id' => 'required|exists:question_difficulties,id',
            'question_text' => 'required',
            'question_image' => 'nullable|image|max:2048',
            'marks' => 'required|numeric|min:0',
            'negative_marks' => 'required|numeric|min:0',
            'explanation' => 'nullable|string',
            'explanation_image' => 'nullable|image|max:2048',
            'options' => 'required|array|min:2|max:4',
            'options.*.text' => 'required|string',
            'options.*.image' => 'nullable|image|max:1024',
            'options.*.is_correct' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Handle question image upload
            if ($request->hasFile('question_image')) {
                $validated['question_image'] = $request->file('question_image')
                    ->store('questions', 'public');
            }

            if ($request->hasFile('explanation_image')) {
                $validated['explanation_image'] = $request->file('explanation_image')
                    ->store('explanations', 'public');
            }

            $validated['created_by'] = auth()->id();
            
            $question = Question::create($validated);

            // Create options
            foreach ($request->options as $key => $optionData) {
                $optionImage = null;
                
                if (isset($optionData['image']) && $optionData['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $optionImage = $optionData['image']->store('options', 'public');
                }

                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_key' => chr(65 + $key), // A, B, C, D
                    'option_text' => $optionData['text'],
                    'option_image' => $optionImage,
                    'is_correct' => isset($optionData['is_correct']) && $optionData['is_correct'] == '1',
                ]);
            }

            DB::commit();
            
            return redirect()->route('admin.questions.index')
                ->with('success', 'Question created successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create question: ' . $e->getMessage());
        }
    }

    public function edit(Question $question)
    {
        $question->load(['options', 'examCategory', 'subject', 'chapter', 'topic', 'difficulty']);
        
        $examCategories = ExamCategory::where('is_active', true)->get();
        $subjects = Subject::where('exam_category_id', $question->exam_category_id)->get();
        $chapters = Chapter::where('subject_id', $question->subject_id)->get();
        $topics = Topic::where('chapter_id', $question->chapter_id)->get();
        $difficulties = QuestionDifficulty::all();
        
        return view('admin.questions.edit', compact('question', 'examCategories', 'subjects', 'chapters', 'topics', 'difficulties'));
    }

    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'exam_category_id' => 'required|exists:exam_categories,id',
            'subject_id' => 'required|exists:subjects,id',
            'chapter_id' => 'nullable|exists:chapters,id',
            'topic_id' => 'nullable|exists:topics,id',
            'difficulty_id' => 'required|exists:question_difficulties,id',
            'question_text' => 'required',
            'question_image' => 'nullable|image|max:2048',
            'marks' => 'required|numeric|min:0',
            'negative_marks' => 'required|numeric|min:0',
            'explanation' => 'nullable|string',
            'explanation_image' => 'nullable|image|max:2048',
            'options' => 'required|array|min:2|max:4',
            'options.*.text' => 'required|string',
            'options.*.image' => 'nullable|image|max:1024',
            'options.*.is_correct' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('question_image')) {
                if ($question->question_image) {
                    Storage::disk('public')->delete($question->question_image);
                }
                $validated['question_image'] = $request->file('question_image')
                    ->store('questions', 'public');
            }

            if ($request->hasFile('explanation_image')) {
                if ($question->explanation_image) {
                    Storage::disk('public')->delete($question->explanation_image);
                }
                $validated['explanation_image'] = $request->file('explanation_image')
                    ->store('explanations', 'public');
            }

            $question->update($validated);

            // Delete old options
            $question->options()->delete();

            // Create new options
            foreach ($request->options as $key => $optionData) {
                $optionImage = null;
                
                if (isset($optionData['image']) && $optionData['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $optionImage = $optionData['image']->store('options', 'public');
                }

                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_key' => chr(65 + $key),
                    'option_text' => $optionData['text'],
                    'option_image' => $optionImage,
                    'is_correct' => isset($optionData['is_correct']) && $optionData['is_correct'] == '1',
                ]);
            }

            DB::commit();
            
            return redirect()->route('admin.questions.index')
                ->with('success', 'Question updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update question: ' . $e->getMessage());
        }
    }

    public function destroy(Question $question)
    {
        try {
            $question->delete();
            return response()->json(['success' => true, 'message' => 'Question deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete question'], 500);
        }
    }

    public function bulkImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
            'exam_category_id' => 'required|exists:exam_categories,id',
        ]);

        try {
            Excel::import(new QuestionsImport($request->exam_category_id), $request->file('file'));
            
            return redirect()->route('admin.questions.index')
                ->with('success', 'Questions imported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    // AJAX - Get subjects by exam category
    public function getSubjectsByCategory($categoryId)
    {
        $subjects = Subject::where('exam_category_id', $categoryId)
                          ->where('is_active', true)
                          ->get(['id', 'name']);
        
        return response()->json($subjects);
    }

    // AJAX - Get chapters by subject
    public function getChaptersBySubject($subjectId)
    {
        $chapters = Chapter::where('subject_id', $subjectId)
                          ->where('is_active', true)
                          ->get(['id', 'name']);
        
        return response()->json($chapters);
    }

    // AJAX - Get topics by chapter
    public function getTopicsByChapter($chapterId)
    {
        $topics = Topic::where('chapter_id', $chapterId)
                      ->where('is_active', true)
                      ->get(['id', 'name']);
        
        return response()->json($topics);
    }
}
