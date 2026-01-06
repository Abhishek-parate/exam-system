<?php
// app/Http/Controllers/Admin/ClassController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{
    public function index()
    {
        $classes = SchoolClass::with('teachers.user')
                    ->withCount('exams')
                    ->latest()
                    ->paginate(15);
        
        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        $teachers = Teacher::with('user')->get();
        return view('admin.classes.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'section' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'teacher_ids' => 'nullable|array',
            'teacher_ids.*' => 'exists:teachers,id',
        ]);

        DB::beginTransaction();
        try {
            $class = SchoolClass::create([
                'name' => $validated['name'],
                'section' => $validated['section'] ?? null,
                'description' => $validated['description'] ?? null,
                'is_active' => $request->has('is_active'),
            ]);

            // Attach teachers to class
            if (!empty($validated['teacher_ids'])) {
                $class->teachers()->attach($validated['teacher_ids']);
            }

            DB::commit();
            return redirect()->route('admin.classes.index')
                           ->with('success', 'Class created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create class: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function show($id)
    {
        $class = SchoolClass::with(['teachers.user', 'exams.creator', 'students.user'])
                           ->findOrFail($id);
        
        $statistics = [
            'total_teachers' => $class->teachers()->count(),
            'total_exams' => $class->exams()->count(),
            'total_students' => $class->students()->count(),
            'active_exams' => $class->exams()->where('is_active', true)->count(),
        ];
        
        return view('admin.classes.show', compact('class', 'statistics'));
    }

    public function edit($id)
    {
        $class = SchoolClass::with('teachers')->findOrFail($id);
        $teachers = Teacher::with('user')->get();
        $assignedTeacherIds = $class->teachers->pluck('id')->toArray();
        
        return view('admin.classes.edit', compact('class', 'teachers', 'assignedTeacherIds'));
    }

    public function update(Request $request, $id)
    {
        $class = SchoolClass::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'section' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'teacher_ids' => 'nullable|array',
            'teacher_ids.*' => 'exists:teachers,id',
        ]);

        DB::beginTransaction();
        try {
            $class->update([
                'name' => $validated['name'],
                'section' => $validated['section'] ?? null,
                'description' => $validated['description'] ?? null,
                'is_active' => $request->has('is_active'),
            ]);

            // Sync teachers
            $class->teachers()->sync($validated['teacher_ids'] ?? []);

            DB::commit();
            return redirect()->route('admin.classes.index')
                           ->with('success', 'Class updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update class: ' . $e->getMessage())
                        ->withInput();
        }
    }

    public function destroy($id)
    {
        $class = SchoolClass::findOrFail($id);
        
        try {
            // Check if class has exams
            if ($class->exams()->count() > 0) {
                return back()->with('error', 'Cannot delete class that has associated exams.');
            }

            DB::beginTransaction();
            $class->teachers()->detach(); // Remove teacher assignments
            $class->delete();
            DB::commit();

            return redirect()->route('admin.classes.index')
                           ->with('success', 'Class deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete class: ' . $e->getMessage());
        }
    }
}
