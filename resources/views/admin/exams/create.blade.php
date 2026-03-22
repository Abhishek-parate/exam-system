@extends('layouts.admin')

@section('title', 'Create New Exam')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Create New Exam</h1>
        <a href="{{ route('admin.exams.index') }}" class="text-gray-600 hover:text-gray-900">
            ← Back to Exams
        </a>
    </div>

    {{-- Validation errors summary --}}
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-700 font-semibold mb-2">Please fix the following errors:</p>
            <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.exams.store') }}"
          class="bg-white rounded-lg shadow-md p-8 space-y-6">
        @csrf

        {{-- Title --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Exam Title *</label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                   placeholder="Enter exam title">
            @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Category --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Exam Category *</label>
            <select name="exam_category_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                            {{ old('exam_category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('exam_category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea name="description" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                      placeholder="Exam instructions and details...">{{ old('description') }}</textarea>
        </div>

        {{-- Duration & Marks --}}
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Duration (Minutes) *</label>
                <input type="number" name="duration_minutes"
                       value="{{ old('duration_minutes', 60) }}" required min="1"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                @error('duration_minutes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Total Marks *</label>
                <input type="number" name="total_marks"
                       value="{{ old('total_marks', 100) }}" required min="0" step="0.01"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                @error('total_marks')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Start & End Time --}}
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Time *</label>
                <input type="datetime-local" name="start_time"
                       value="{{ old('start_time') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                @error('start_time')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">End Time *</label>
                <input type="datetime-local" name="end_time"
                       value="{{ old('end_time') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                @error('end_time')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Result Release Time --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Result Release Time (Optional)</label>
            <input type="datetime-local" name="result_release_time"
                   value="{{ old('result_release_time') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            <p class="text-xs text-gray-500 mt-1">Leave blank to release results immediately after submission</p>
        </div>

        {{-- ✅ ENROLLMENT TYPE — this was the missing field causing the silent failure --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">Enrollment Type *</label>
            <div class="grid grid-cols-2 gap-4">

                <label id="label-open"
                       class="flex items-start gap-3 p-4 border-2 rounded-lg cursor-pointer transition border-green-500 bg-green-50">
                    <input type="radio" name="enrollment_type" value="open"
                           {{ old('enrollment_type', 'open') === 'open' ? 'checked' : '' }}
                           class="mt-0.5" onchange="highlightEnrollment()">
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">🌐 Open to All</p>
                        <p class="text-xs text-gray-500 mt-1">All active students can see and attempt this exam automatically — no enrollment needed.</p>
                    </div>
                </label>

                <label id="label-enrolled"
                       class="flex items-start gap-3 p-4 border-2 rounded-lg cursor-pointer transition border-gray-200">
                    <input type="radio" name="enrollment_type" value="enrolled"
                           {{ old('enrollment_type') === 'enrolled' ? 'checked' : '' }}
                           class="mt-0.5" onchange="highlightEnrollment()">
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">🔒 Enrolled Only</p>
                        <p class="text-xs text-gray-500 mt-1">Only students you manually enroll from the exam page can access this exam.</p>
                    </div>
                </label>

            </div>
            @error('enrollment_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Checkboxes --}}
        <div class="space-y-3 pt-2 border-t border-gray-100">
            <p class="text-sm font-medium text-gray-700 pt-2">Exam Settings</p>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="randomize_questions" value="1"
                       {{ old('randomize_questions') ? 'checked' : '' }} class="rounded w-4 h-4">
                <span class="text-sm text-gray-700">Randomize question order for each student</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="randomize_options" value="1"
                       {{ old('randomize_options') ? 'checked' : '' }} class="rounded w-4 h-4">
                <span class="text-sm text-gray-700">Randomize answer options order</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="show_results_immediately" value="1" checked
                       class="rounded w-4 h-4">
                <span class="text-sm text-gray-700">Show results immediately after submission</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="allow_resume" value="1" checked
                       class="rounded w-4 h-4">
                <span class="text-sm text-gray-700">Allow students to resume if disconnected</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked
                       class="rounded w-4 h-4">
                <span class="text-sm text-gray-700">Activate this exam immediately</span>
            </label>
        </div>

        {{-- Actions --}}
        <div class="flex gap-4 pt-2">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-8 rounded-lg transition">
                💾 Create Exam
            </button>
            <a href="{{ route('admin.exams.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg transition">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
function highlightEnrollment() {
    const isOpen = document.querySelector('input[name="enrollment_type"][value="open"]').checked;

    const lo = document.getElementById('label-open');
    const le = document.getElementById('label-enrolled');

    lo.classList.toggle('border-green-500',  isOpen);
    lo.classList.toggle('bg-green-50',       isOpen);
    lo.classList.toggle('border-gray-200',   !isOpen);

    le.classList.toggle('border-purple-500', !isOpen);
    le.classList.toggle('bg-purple-50',      !isOpen);
    le.classList.toggle('border-gray-200',   isOpen);
}
</script>
@endsection