<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\ExamCategory;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of subjects
     */
    public function index(Request $request)
    {
        $query = Subject::query();

        // Apply filters
        if ($request->filled('exam_category_id')) {
            $query->where('exam_category_id', $request->exam_category_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $subjects = $query->with('examCategory')->latest()->paginate(20);
        $examCategories = ExamCategory::where('is_active', true)->get();

        return view('admin.subjects.index', compact('subjects', 'examCategories'));
    }

    /**
     * Show the form for creating a new subject
     */
    public function create()
    {
        $examCategories = ExamCategory::where('is_active', true)->get();
        return view('admin.subjects.create', compact('examCategories'));
    }

    /**
     * Store a newly created subject
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_category_id' => 'nullable|exists:exam_categories,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:subjects,code',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Convert empty strings to NULL
        $validated['exam_category_id'] = !empty($validated['exam_category_id']) ? $validated['exam_category_id'] : null;
        $validated['code'] = !empty($validated['code']) ? $validated['code'] : null;
        $validated['description'] = !empty($validated['description']) ? $validated['description'] : null;
        $validated['is_active'] = $request->has('is_active') ? true : false;

        Subject::create($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject created successfully!');
    }

    /**
     * Display the specified subject
     */
    public function show(Subject $subject)
    {
        $subject->load(['examCategory', 'chapters', 'questions']);
        
        // Get statistics
        $stats = [
            'total_questions' => $subject->questions()->count(),
            'active_questions' => $subject->questions()->where('is_active', true)->count(),
            'total_chapters' => $subject->chapters()->count(),
        ];

        return view('admin.subjects.show', compact('subject', 'stats'));
    }

    /**
     * Show the form for editing the specified subject
     */
    public function edit(Subject $subject)
    {
        $examCategories = ExamCategory::where('is_active', true)->get();
        return view('admin.subjects.edit', compact('subject', 'examCategories'));
    }

    /**
     * Update the specified subject
     */
    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'exam_category_id' => 'nullable|exists:exam_categories,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:subjects,code,' . $subject->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Convert empty strings to NULL
        $validated['exam_category_id'] = !empty($validated['exam_category_id']) ? $validated['exam_category_id'] : null;
        $validated['code'] = !empty($validated['code']) ? $validated['code'] : null;
        $validated['description'] = !empty($validated['description']) ? $validated['description'] : null;
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $subject->update($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject updated successfully!');
    }

    /**
     * Remove the specified subject
     */
    public function destroy(Subject $subject)
    {
        try {
            // Check if subject has questions
            if ($subject->questions()->count() > 0) {
                return back()->with('error', 'Cannot delete subject with existing questions. Please delete all questions first.');
            }

            $subject->delete();

            return redirect()->route('admin.subjects.index')
                ->with('success', 'Subject deleted successfully!');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete subject: ' . $e->getMessage());
        }
    }
}
