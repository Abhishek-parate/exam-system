<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamCategory;
use Illuminate\Http\Request;

class ExamCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ExamCategory::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        $categories = $query->latest()->paginate(20);

        return view('admin.exam_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.exam_categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:exam_categories,name',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        ExamCategory::create($validated);

        return redirect()->route('admin.exam-categories.index')
            ->with('success', 'Exam category created successfully.');
    }

    public function edit(ExamCategory $exam_category)
    {
        return view('admin.exam_categories.edit', compact('exam_category'));
    }

    public function update(Request $request, ExamCategory $exam_category)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:exam_categories,name,'.$exam_category->id,
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $exam_category->update($validated);

        return redirect()->route('admin.exam-categories.index')
            ->with('success', 'Exam category updated successfully.');
    }

    public function destroy(ExamCategory $exam_category)
    {
        $exam_category->delete();

        return redirect()->route('admin.exam-categories.index')
            ->with('success', 'Exam category deleted successfully.');
    }
}
