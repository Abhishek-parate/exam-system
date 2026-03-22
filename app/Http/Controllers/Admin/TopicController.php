<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Chapter;
use App\Models\Subject;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function index()
    {
        $topics = Topic::with(['chapter.subject'])
            ->orderBy('chapter_id')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.topics.index', compact('topics'));
    }

    public function create()
    {
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $chapters = collect();
        return view('admin.topics.create', compact('subjects', 'chapters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'name'       => 'required|string|max:255',
        ]);

        Topic::create([
            'chapter_id' => $request->chapter_id,
            'name'       => $request->name,
            'is_active'  => $request->has('is_active'),
        ]);

        return redirect()->route('admin.topics.index')
            ->with('success', 'Topic created successfully!');
    }

    public function edit(Topic $topic)
    {
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $chapters = Chapter::where('subject_id', $topic->chapter->subject_id)
            ->where('is_active', true)
            ->orderBy('chapter_number')
            ->get();

        return view('admin.topics.edit', compact('topic', 'subjects', 'chapters'));
    }

    public function update(Request $request, Topic $topic)
    {
        $request->validate([
            'chapter_id' => 'required|exists:chapters,id',
            'name'       => 'required|string|max:255',
        ]);

        $topic->update([
            'chapter_id' => $request->chapter_id,
            'name'       => $request->name,
            'is_active'  => $request->has('is_active'),
        ]);

        return redirect()->route('admin.topics.index')
            ->with('success', 'Topic updated successfully!');
    }

    public function destroy(Topic $topic)
    {
        $topic->delete();
        return redirect()->route('admin.topics.index')
            ->with('success', 'Topic deleted successfully!');
    }

    // AJAX endpoint
    public function byChapter($chapterId)
    {
        $topics = Topic::where('chapter_id', $chapterId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($topics);
    }
}
