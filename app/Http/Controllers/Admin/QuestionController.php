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

        $questions     = $query->latest()->paginate(20);
        $examCategories = ExamCategory::where('is_active', true)->get();
        $subjects      = Subject::where('is_active', true)->get();
        $difficulties  = QuestionDifficulty::all();

        return view('admin.questions.index', compact('questions', 'examCategories', 'subjects', 'difficulties'));
    }

    public function create()
    {
        $examCategories = ExamCategory::where('is_active', true)->get();
        $subjects       = Subject::where('is_active', true)->get();
        $difficulties   = QuestionDifficulty::all();

        return view('admin.questions.create', compact('examCategories', 'subjects', 'difficulties'));
    }

    public function store(Request $request)
    {
        $questionType = $request->input('question_type', 'mcq');

        // ── Base validation (always) ──────────────────────────────────────────
        $rules = [
            'exam_category_id'  => 'nullable|exists:exam_categories,id',
            'subject_id'        => 'required|exists:subjects,id',
            'chapter_id'        => 'nullable|exists:chapters,id',
            'topic_id'          => 'nullable|exists:topics,id',
            'difficulty_id'     => 'required|exists:question_difficulties,id',
            'question_text'     => 'required|string',
            'question_image'    => 'nullable|image|max:2048',
            'marks'             => 'required|numeric|min:0',
            'negative_marks'    => 'required|numeric|min:0',
            'explanation'       => 'nullable|string',
            'explanation_image' => 'nullable|image|max:2048',
            'question_type'     => 'required|in:mcq,subjective',
        ];

        // ── Conditional validation per type ──────────────────────────────────
        if ($questionType === 'mcq') {
            $rules['options']              = 'required|array|min:2';
            $rules['options.*.text']       = 'required|string';
            $rules['options.*.is_correct'] = 'nullable|boolean';
            $rules['options.*.image']      = 'nullable|image|max:2048';
        } else {
            // Subjective
            $rules['correct_answer'] = 'required|string|max:5000';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            if ($request->hasFile('question_image')) {
                $validated['question_image'] = $request->file('question_image')->store('questions', 'public');
            }
            if ($request->hasFile('explanation_image')) {
                $validated['explanation_image'] = $request->file('explanation_image')->store('explanations', 'public');
            }

            $validated['exam_category_id'] = !empty($validated['exam_category_id']) ? $validated['exam_category_id'] : null;
            $validated['chapter_id']        = !empty($validated['chapter_id'])       ? $validated['chapter_id']       : null;
            $validated['topic_id']          = !empty($validated['topic_id'])         ? $validated['topic_id']         : null;
            $validated['explanation']       = !empty($validated['explanation'])       ? $validated['explanation']      : null;
            $validated['question_type']     = $questionType;
            $validated['correct_answer']    = ($questionType === 'subjective') ? $request->correct_answer : null;
            $validated['created_by']        = auth()->id();
            $validated['is_active']         = true;

            // Remove options array from validated data before creating question
            $optionsData = $validated['options'] ?? [];
            unset($validated['options']);

            $question = Question::create($validated);

            // ── Create options only for MCQ ───────────────────────────────────
            if ($questionType === 'mcq') {
                foreach ($request->options as $index => $optionData) {
                    $optionImage = null;
                    if (isset($optionData['image']) && $optionData['image'] instanceof \Illuminate\Http\UploadedFile) {
                        $optionImage = $optionData['image']->store('options', 'public');
                    }
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_key'  => chr(65 + $index),
                        'option_text' => $optionData['text'],
                        'option_image' => $optionImage,
                        'is_correct'  => isset($optionData['is_correct']) ? (bool)$optionData['is_correct'] : false,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.questions.index')
                ->with('success', 'Question created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create question: ' . $e->getMessage())->withInput();
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
            ->when($question->exam_category_id, function ($q) use ($question) {
                return $q->where('exam_category_id', $question->exam_category_id);
            })->get();

        $chapters = Chapter::where('is_active', true)
            ->where('subject_id', $question->subject_id)->get();

        $topics = Topic::where('is_active', true)
            ->when($question->chapter_id, function ($q) use ($question) {
                return $q->where('chapter_id', $question->chapter_id);
            })->get();

        $difficulties = QuestionDifficulty::all();
        $question->load('options');

        return view('admin.questions.edit', compact(
            'question', 'examCategories', 'subjects', 'chapters', 'topics', 'difficulties'
        ));
    }

    public function update(Request $request, Question $question)
    {
        $questionType = $request->input('question_type', 'mcq');

        // ── Base validation ───────────────────────────────────────────────────
        $rules = [
            'exam_category_id'  => 'nullable|exists:exam_categories,id',
            'subject_id'        => 'required|exists:subjects,id',
            'chapter_id'        => 'nullable|exists:chapters,id',
            'topic_id'          => 'nullable|exists:topics,id',
            'difficulty_id'     => 'required|exists:question_difficulties,id',
            'question_text'     => 'required|string',
            'question_image'    => 'nullable|image|max:2048',
            'marks'             => 'required|numeric|min:0',
            'negative_marks'    => 'required|numeric|min:0',
            'explanation'       => 'nullable|string',
            'explanation_image' => 'nullable|image|max:2048',
            'is_active'         => 'boolean',
            'question_type'     => 'required|in:mcq,subjective',
        ];

        if ($questionType === 'mcq') {
            $rules['options']              = 'required|array|min:2';
            $rules['options.*.text']       = 'required|string';
            $rules['options.*.is_correct'] = 'nullable|boolean';
            $rules['options.*.image']      = 'nullable|image|max:2048';
        } else {
            $rules['correct_answer'] = 'required|string|max:5000';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            if ($request->hasFile('question_image')) {
                if ($question->question_image) Storage::disk('public')->delete($question->question_image);
                $validated['question_image'] = $request->file('question_image')->store('questions', 'public');
            }
            if ($request->hasFile('explanation_image')) {
                if ($question->explanation_image) Storage::disk('public')->delete($question->explanation_image);
                $validated['explanation_image'] = $request->file('explanation_image')->store('explanations', 'public');
            }

            $validated['exam_category_id'] = !empty($validated['exam_category_id']) ? $validated['exam_category_id'] : null;
            $validated['chapter_id']        = !empty($validated['chapter_id'])       ? $validated['chapter_id']       : null;
            $validated['topic_id']          = !empty($validated['topic_id'])         ? $validated['topic_id']         : null;
            $validated['explanation']       = !empty($validated['explanation'])       ? $validated['explanation']      : null;
            $validated['question_type']     = $questionType;
            $validated['correct_answer']    = ($questionType === 'subjective') ? $request->correct_answer : null;

            unset($validated['options']);
            $question->update($validated);

            // ── Delete old options ────────────────────────────────────────────
            foreach ($question->options as $oldOption) {
                if ($oldOption->option_image) Storage::disk('public')->delete($oldOption->option_image);
                $oldOption->delete();
            }

            // ── Create new options only for MCQ ───────────────────────────────
            if ($questionType === 'mcq') {
                foreach ($request->options as $index => $optionData) {
                    $optionImage = null;
                    if (isset($optionData['image']) && $optionData['image'] instanceof \Illuminate\Http\UploadedFile) {
                        $optionImage = $optionData['image']->store('options', 'public');
                    }
                    QuestionOption::create([
                        'question_id'  => $question->id,
                        'option_key'   => chr(65 + $index),
                        'option_text'  => $optionData['text'],
                        'option_image' => $optionImage,
                        'is_correct'   => isset($optionData['is_correct']) ? (bool)$optionData['is_correct'] : false,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.questions.index')
                ->with('success', 'Question updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update question: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Question $question)
    {
        try {
            if ($question->question_image)    Storage::disk('public')->delete($question->question_image);
            if ($question->explanation_image) Storage::disk('public')->delete($question->explanation_image);
            foreach ($question->options as $option) {
                if ($option->option_image) Storage::disk('public')->delete($option->option_image);
            }
            $question->delete();
            return redirect()->route('admin.questions.index')->with('success', 'Question deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete question: ' . $e->getMessage());
        }
    }

    // ── AJAX helpers ──────────────────────────────────────────────────────────

    public function getSubjectsByCategory($categoryId)
    {
        $subjects = Subject::where('exam_category_id', $categoryId)
            ->where('is_active', true)->get(['id', 'name']);
        return response()->json($subjects);
    }

    public function getChaptersBySubject($subjectId)
    {
        $chapters = Chapter::where('subject_id', $subjectId)
            ->where('is_active', true)->get(['id', 'name']);
        return response()->json($chapters);
    }

    public function getTopicsByChapter($chapterId)
    {
        $topics = Topic::where('chapter_id', $chapterId)
            ->where('is_active', true)->get(['id', 'name']);
        return response()->json($topics);
    }

    public function bulkImport(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,xlsx,xls']);
        return back()->with('info', 'Bulk import feature coming soon!');
    }
}