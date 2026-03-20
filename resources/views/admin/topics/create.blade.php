@extends('layouts.admin')
@section('title', 'Add Topic')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">🏷️ Add Topic</h1>
        <a href="{{ route('admin.topics.index') }}"
           class="text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition">← Back</a>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6">
        <form action="{{ route('admin.topics.store') }}" method="POST">
            @csrf

            {{-- Subject (for filtering chapters) --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Subject <span class="text-red-500">*</span></label>
                <select id="subject_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Select Subject</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Chapter --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Chapter <span class="text-red-500">*</span></label>
                <select name="chapter_id" id="chapter_id" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="">Select Subject first</option>
                </select>
                @error('chapter_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Topic Name --}}
            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Topic Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="e.g. Newton's Laws of Motion"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="w-5 h-5 rounded text-purple-600">
                    <span class="text-sm font-semibold text-gray-700">Active</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-8 rounded-lg transition">
                    ✅ Save Topic
                </button>
                <a href="{{ route('admin.topics.index') }}"
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-lg transition">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('subject_id').addEventListener('change', function () {
    const subjectId = this.value;
    const chapterSelect = document.getElementById('chapter_id');
    chapterSelect.innerHTML = '<option value="">Loading...</option>';

    if (!subjectId) {
        chapterSelect.innerHTML = '<option value="">Select Subject first</option>';
        return;
    }

    fetch(`/admin/chapters/by-subject/${subjectId}`)
        .then(r => r.json())
        .then(chapters => {
            chapterSelect.innerHTML = '<option value="">Select Chapter</option>';
            chapters.forEach(c => {
                chapterSelect.innerHTML += `<option value="${c.id}">${c.name}</option>`;
            });
        });
});
</script>
@endpush
@endsection
