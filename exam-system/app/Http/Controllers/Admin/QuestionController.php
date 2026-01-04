<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\ExamCategory;
use App\Models\Question;
use App\Models\QuestionDifficulty;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with(['examCategory', 'subject', 'chapter', 'topic', 'difficulty', 'options']);

        // Apply filters
        if ($request->filled('exam_category_id')) {
            $query->where('exam_category_id', $request->exam_category_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('chapter_id')) {
            $query->where('chapter_id', $request->chapter_id);
        }

        if ($request->filled('difficulty_id')) {
            $query->where('difficulty_id', $request->difficulty_id);
        }

        if ($request->filled('search')) {
            $query->where('question_text', 'like', '%' . $request->search . '%');
        }

        $questions = $query->latest()->paginate(20);

        // Get filter options
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
            'exam_category_id' => 'nullable|exists:exam_categories,id',
            'subject_id' => 'required|exists:subjects,id',
            'chapter_id' => 'nullable|exists:chapters,id',
            'topic_id' => 'nullable|exists:topics,id',
            'difficulty_id' => 'required|exists:question_difficulties,id',
            'question_text' => 'required|string',
            'question_image' => 'nullable|image|max:2048',
            'marks' => 'required|numeric|min:0',
            'negative_marks' => 'required|numeric|min:0',
            'explanation' => 'nullable|string',
            'explanation_image' => 'nullable|image|max:2048',
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|string',
            'options.*.is_correct' => 'nullable|boolean',
            'options.*.image' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // Upload question image if provided
            if ($request->hasFile('question_image')) {
                $validated['question_image'] = $request->file('question_image')->store('questions', 'public');
            }

            // Upload explanation image if provided
            if ($request->hasFile('explanation_image')) {
                $validated['explanation_image'] = $request->file('explanation_image')->store('explanations', 'public');
            }

            // Convert empty strings to NULL for nullable fields
            $validated['exam_category_id'] = !empty($validated['exam_category_id']) ? $validated['exam_category_id'] : null;
            $validated['chapter_id'] = !empty($validated['chapter_id']) ? $validated['chapter_id'] : null;
            $validated['topic_id'] = !empty($validated['topic_id']) ? $validated['topic_id'] : null;
            $validated['explanation'] = !empty($validated['explanation']) ? $validated['explanation'] : null;

            $validated['created_by'] = auth()->id();
            $validated['is_active'] = true;

            $question = Question::create($validated);

            // Create options
            foreach ($request->options as $index => $optionData) {
                $optionImage = null;
                if (isset($optionData['image']) && $optionData['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $optionImage = $optionData['image']->store('options', 'public');
                }

                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_key' => chr(65 + $index), // A, B, C, D
                    'option_text' => $optionData['text'],
                    'option_image' => $optionImage,
                    'is_correct' => isset($optionData['is_correct']) ? (bool)$optionData['is_correct'] : false,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.questions.index')
                ->with('success', 'Question created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create question: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Question $question)
    {
        $question->load(['examCategory', 'subject', 'chapter', 'topic', 'difficulty', 'options', 'creator']);
        return view('admin.questions.show', compact('question'));
    }

    public function edit(Question $question)
    {
        $examCategories = ExamCategory::where('is_active', true)->get();
        
        $subjects = Subject::where('is_active', true)
            ->when($question->exam_category_id, function($query) use ($question) {
                return $query->where('exam_category_id', $question->exam_category_id);
            })
            ->get();

        $chapters = Chapter::where('is_active', true)
            ->where('subject_id', $question->subject_id)
            ->get();

        $topics = Topic::where('is_active', true)
            ->when($question->chapter_id, function($query) use ($question) {
                return $query->where('chapter_id', $question->chapter_id);
            })
            ->get();

        $difficulties = QuestionDifficulty::all();

        $question->load('options');

        return view('admin.questions.edit', compact('question', 'examCategories', 'subjects', 'chapters', 'topics', 'difficulties'));
    }

    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'exam_category_id' => 'nullable|exists:exam_categories,id',
            'subject_id' => 'required|exists:subjects,id',
            'chapter_id' => 'nullable|exists:chapters,id',
            'topic_id' => 'nullable|exists:topics,id',
            'difficulty_id' => 'required|exists:question_difficulties,id',
            'question_text' => 'required|string',
            'question_image' => 'nullable|image|max:2048',
            'marks' => 'required|numeric|min:0',
            'negative_marks' => 'required|numeric|min:0',
            'explanation' => 'nullable|string',
            'explanation_image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            // ✅ Added validation for options
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|string',
            'options.*.is_correct' => 'nullable|boolean',
            'options.*.image' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // Upload new question image if provided
            if ($request->hasFile('question_image')) {
                if ($question->question_image) {
                    Storage::disk('public')->delete($question->question_image);
                }
                $validated['question_image'] = $request->file('question_image')->store('questions', 'public');
            }

            // Upload new explanation image if provided
            if ($request->hasFile('explanation_image')) {
                if ($question->explanation_image) {
                    Storage::disk('public')->delete($question->explanation_image);
                }
                $validated['explanation_image'] = $request->file('explanation_image')->store('explanations', 'public');
            }

            // Convert empty strings to NULL for nullable fields
            $validated['exam_category_id'] = !empty($validated['exam_category_id']) ? $validated['exam_category_id'] : null;
            $validated['chapter_id'] = !empty($validated['chapter_id']) ? $validated['chapter_id'] : null;
            $validated['topic_id'] = !empty($validated['topic_id']) ? $validated['topic_id'] : null;
            $validated['explanation'] = !empty($validated['explanation']) ? $validated['explanation'] : null;

            $question->update($validated);

            // ✅ Delete old options and their images
            foreach ($question->options as $oldOption) {
                if ($oldOption->option_image) {
                    Storage::disk('public')->delete($oldOption->option_image);
                }
                $oldOption->delete();
            }

            // ✅ Create new options
            foreach ($request->options as $index => $optionData) {
                $optionImage = null;
                if (isset($optionData['image']) && $optionData['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $optionImage = $optionData['image']->store('options', 'public');
                }

                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_key' => chr(65 + $index), // A, B, C, D
                    'option_text' => $optionData['text'],
                    'option_image' => $optionImage,
                    'is_correct' => isset($optionData['is_correct']) ? (bool)$optionData['is_correct'] : false,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.questions.index')
                ->with('success', 'Question updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update question: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Question $question)
    {
        try {
            // Delete images
            if ($question->question_image) {
                Storage::disk('public')->delete($question->question_image);
            }

            if ($question->explanation_image) {
                Storage::disk('public')->delete($question->explanation_image);
            }

            // Delete option images
            foreach ($question->options as $option) {
                if ($option->option_image) {
                    Storage::disk('public')->delete($option->option_image);
                }
            }

            $question->delete();

            return redirect()->route('admin.questions.index')
                ->with('success', 'Question deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete question: ' . $e->getMessage());
        }
    }

    // AJAX methods for dynamic dropdowns
    public function getSubjectsByCategory($categoryId)
    {
        $subjects = Subject::where('exam_category_id', $categoryId)
            ->where('is_active', true)
            ->get(['id', 'name']);

        return response()->json($subjects);
    }

    public function getChaptersBySubject($subjectId)
    {
        $chapters = Chapter::where('subject_id', $subjectId)
            ->where('is_active', true)
            ->get(['id', 'name']);

        return response()->json($chapters);
    }

    public function getTopicsByChapter($chapterId)
    {
        $topics = Topic::where('chapter_id', $chapterId)
            ->where('is_active', true)
            ->get(['id', 'name']);

        return response()->json($topics);
    }

    public function bulkImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls'
        ]);

        // TODO: Implement bulk import logic
        return back()->with('info', 'Bulk import feature coming soon!');
    }
}
