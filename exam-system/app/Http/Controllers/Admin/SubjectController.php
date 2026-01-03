<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\ExamCategory;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::with('examCategory');

        if ($request->filled('exam_category_id')) {
            $query->where('exam_category_id', $request->exam_category_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $subjects = $query->latest()->paginate(20);
        $categories = ExamCategory::orderBy('name')->get();

        return view('admin.subjects.index', compact('subjects', 'categories'));
    }

    public function create()
    {
        $categories = ExamCategory::where('is_active', true)->orderBy('name')->get();

        return view('admin.subjects.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_category_id' => 'required|exists:exam_categories,id',
            'name'             => 'required|string|max:255',
            'code'             => 'nullable|string|max:50',
            'description'      => 'nullable|string',
            'is_active'        => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Subject::create($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    public function edit(Subject $subject)
    {
        $categories = ExamCategory::where('is_active', true)->orderBy('name')->get();

        return view('admin.subjects.edit', compact('subject', 'categories'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'exam_category_id' => 'required|exists:exam_categories,id',
            'name'             => 'required|string|max:255',
            'code'             => 'nullable|string|max:50',
            'description'      => 'nullable|string',
            'is_active'        => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $subject->update($validated);

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }
}
