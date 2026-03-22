<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamCategory;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::with('examCategory')
            ->withCount(['questions', 'chapters']);

        if ($request->filled('exam_category_id')) {
            $query->where('exam_category_id', $request->exam_category_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'active' ? 1 : 0;
            $query->where('is_active', $isActive);
        }

        $subjects      = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();
        $examCategories = ExamCategory::where('is_active', true)->get();

        return view('admin.subjects.index', compact('subjects', 'examCategories'));
    }

    public function create()
    {
        $examCategories = ExamCategory::where('is_active', true)->get();
        return view('admin.subjects.create', compact('examCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_category_id' => 'nullable|exists:exam_categories,id',
            'name'             => 'required|string|max:255',
            'code'             => 'nullable|string|max:50|unique:subjects,code',
            'description'      => 'nullable|string',
            'is_active'        => 'boolean',
        ]);

        $validated['exam_category_id'] = $validated['exam_category_id'] ?? null;
        $validated['code']             = $validated['code'] ?? null;
        $validated['description']      = $validated['description'] ?? null;
        $validated['is_active']        = $request->has('is_active') ? 1 : 0;

        Subject::create($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject created successfully!');
    }

    public function show(Subject $subject)
    {
        $subject->load(['examCategory', 'chapters', 'questions']);

        $stats = [
            'total_questions'  => $subject->questions->count(),
            'active_questions' => $subject->questions->where('is_active', true)->count(),
            'total_chapters'   => $subject->chapters->count(),
        ];

        return view('admin.subjects.show', compact('subject', 'stats'));
    }

    public function edit(Subject $subject)
    {
        $examCategories = ExamCategory::where('is_active', true)->get();
        return view('admin.subjects.edit', compact('subject', 'examCategories'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'exam_category_id' => 'nullable|exists:exam_categories,id',
            'name'             => 'required|string|max:255',
            'code'             => 'nullable|string|max:50|unique:subjects,code,' . $subject->id,
            'description'      => 'nullable|string',
            'is_active'        => 'boolean',
        ]);

        $validated['exam_category_id'] = $validated['exam_category_id'] ?? null;
        $validated['code']             = $validated['code'] ?? null;
        $validated['description']      = $validated['description'] ?? null;
        $validated['is_active']        = $request->has('is_active') ? 1 : 0;

        $subject->update($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject updated successfully!');
    }

    public function destroy(Subject $subject)
    {
        try {
            if ($subject->questions()->count() > 0) {
                return back()->with('error', 'Cannot delete subject with existing questions.');
            }

            $subject->delete();

            return redirect()->route('admin.subjects.index')
                ->with('success', 'Subject deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
