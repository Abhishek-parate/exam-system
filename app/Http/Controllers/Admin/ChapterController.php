<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Subject;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    public function index()
    {
        $chapters = Chapter::with('subject')
            ->withCount('topics')
            ->orderBy('subject_id')
            ->orderBy('chapter_number')
            ->paginate(20);

        return view('admin.chapters.index', compact('chapters'));
    }

    public function create()
    {
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        return view('admin.chapters.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id'     => 'required|exists:subjects,id',
            'name'           => 'required|string|max:255',
            'chapter_number' => 'nullable|integer|min:1',
        ]);

        Chapter::create([
            'subject_id'     => $request->subject_id,
            'name'           => $request->name,
            'chapter_number' => $request->chapter_number,
            'is_active'      => $request->has('is_active'),
        ]);

        return redirect()->route('admin.chapters.index')
            ->with('success', 'Chapter created successfully!');
    }

    public function edit(Chapter $chapter)
    {
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        return view('admin.chapters.edit', compact('chapter', 'subjects'));
    }

    public function update(Request $request, Chapter $chapter)
    {
        $request->validate([
            'subject_id'     => 'required|exists:subjects,id',
            'name'           => 'required|string|max:255',
            'chapter_number' => 'nullable|integer|min:1',
        ]);

        $chapter->update([
            'subject_id'     => $request->subject_id,
            'name'           => $request->name,
            'chapter_number' => $request->chapter_number,
            'is_active'      => $request->has('is_active'),
        ]);

        return redirect()->route('admin.chapters.index')
            ->with('success', 'Chapter updated successfully!');
    }

    public function destroy(Chapter $chapter)
    {
        if ($chapter->topics()->count() > 0) {
            return redirect()->route('admin.chapters.index')
                ->with('error', 'Cannot delete chapter that has topics. Delete topics first.');
        }

        $chapter->delete();
        return redirect()->route('admin.chapters.index')
            ->with('success', 'Chapter deleted successfully!');
    }

    // AJAX endpoint
    public function bySubject($subjectId)
    {
        $chapters = Chapter::where('subject_id', $subjectId)
            ->where('is_active', true)
            ->orderBy('chapter_number')
            ->orderBy('name')
            ->get(['id', 'name', 'chapter_number']);

        return response()->json($chapters);
    }
}
