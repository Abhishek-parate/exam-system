@extends('layouts.admin')
@section('title', 'Chapters')

@section('content')
<div class="container mx-auto px-4 py-8">

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">📚 Chapters</h1>
            <p class="text-gray-500 text-sm mt-1">Manage chapters grouped by subject</p>
        </div>
        <a href="{{ route('admin.chapters.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg transition flex items-center gap-2">
            + Add Chapter
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg border border-green-200">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg border border-red-200">❌ {{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Chapter</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Subject</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">No.</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Topics</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($chapters as $chapter)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 text-gray-400 font-mono text-xs">{{ $loop->iteration }}</td>
                    <td class="px-5 py-3 font-semibold text-gray-800">{{ $chapter->name }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                            {{ $chapter->subject?->name ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-500">
                        {{ $chapter->chapter_number ? 'Ch. ' . $chapter->chapter_number : '—' }}
                    </td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-1 bg-purple-50 text-purple-700 rounded-full text-xs font-medium">
                            {{ $chapter->topics_count ?? $chapter->topics->count() }} topics
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        @if($chapter->is_active)
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Active</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-600 rounded-full text-xs font-semibold">Inactive</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.chapters.edit', $chapter) }}"
                               class="text-xs bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1.5 rounded-lg font-medium transition">
                                ✏️ Edit
                            </a>
                            <form action="{{ route('admin.chapters.destroy', $chapter) }}" method="POST"
                                  onsubmit="return confirm('Delete this chapter?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-xs bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg font-medium transition">
                                    🗑 Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center text-gray-400">
                        <p class="text-4xl mb-2">📭</p>
                        <p class="font-medium">No chapters found. <a href="{{ route('admin.chapters.create') }}" class="text-blue-600 hover:underline">Add one</a>.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $chapters->links() }}
        </div>
    </div>
</div>
@endsection
