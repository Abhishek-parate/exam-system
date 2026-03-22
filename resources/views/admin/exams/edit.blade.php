@extends('layouts.admin')

@section('title', 'Edit Exam')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">✏️ Edit Exam</h1>
        <a href="{{ route('admin.exams.show', $exam) }}" class="text-gray-600 hover:text-gray-900">
            ← Back to Exam
        </a>
    </div>

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

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
    @endif

    {{-- ✅ UPDATE FORM — Delete form is completely separate below --}}
    <form id="update-form"
          method="POST"
          action="{{ route('admin.exams.update', $exam) }}"
          class="bg-white rounded-lg shadow-md p-8 space-y-6">
        @csrf
        @method('PUT')

        {{-- Title --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Exam Title *</label>
            <input type="text" name="title" required
                   value="{{ old('title', $exam->title) }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
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
                            {{ old('exam_category_id', $exam->exam_category_id) == $category->id ? 'selected' : '' }}>
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
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">{{ old('description', $exam->description) }}</textarea>
        </div>

        {{-- Duration & Marks --}}
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Duration (Minutes) *</label>
                <input type="number" name="duration_minutes" required min="1"
                       value="{{ old('duration_minutes', $exam->duration_minutes) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                @error('duration_minutes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Total Marks *</label>
                <input type="number" name="total_marks" required min="0" step="0.01"
                       value="{{ old('total_marks', $exam->total_marks) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                @error('total_marks')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Start & End Time --}}
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Time *</label>
                <input type="datetime-local" name="start_time" required
                       value="{{ old('start_time', $exam->start_time->format('Y-m-d\TH:i')) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                @error('start_time')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">End Time *</label>
                <input type="datetime-local" name="end_time" required
                       value="{{ old('end_time', $exam->end_time->format('Y-m-d\TH:i')) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                @error('end_time')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Result Release Time --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Result Release Time (Optional)</label>
            <input type="datetime-local" name="result_release_time"
                   value="{{ old('result_release_time', $exam->result_release_time?->format('Y-m-d\TH:i')) }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            <p class="text-xs text-gray-500 mt-1">Leave blank to release results immediately</p>
        </div>

        {{-- Exam Code (read-only) --}}
        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
            <p class="text-sm text-gray-600">
                Exam Code: <span class="font-mono font-bold text-gray-900">{{ $exam->exam_code }}</span>
                <span class="text-xs text-gray-400 ml-2">(cannot be changed)</span>
            </p>
        </div>

        {{-- Enrollment Type --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">Enrollment Type *</label>
            @php $currentType = old('enrollment_type', $exam->enrollment_type ?? 'open'); @endphp
            <div class="grid grid-cols-2 gap-4">
                <label id="label-open"
                       class="flex items-start gap-3 p-4 border-2 rounded-lg cursor-pointer transition
                           {{ $currentType === 'open' ? 'border-green-500 bg-green-50' : 'border-gray-200' }}">
                    <input type="radio" name="enrollment_type" value="open"
                           {{ $currentType === 'open' ? 'checked' : '' }}
                           class="mt-0.5" onchange="highlightEnrollment()">
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">🌐 Open to All</p>
                        <p class="text-xs text-gray-500 mt-1">All active students can see and attempt this exam automatically.</p>
                    </div>
                </label>
                <label id="label-enrolled"
                       class="flex items-start gap-3 p-4 border-2 rounded-lg cursor-pointer transition
                           {{ $currentType === 'enrolled' ? 'border-purple-500 bg-purple-50' : 'border-gray-200' }}">
                    <input type="radio" name="enrollment_type" value="enrolled"
                           {{ $currentType === 'enrolled' ? 'checked' : '' }}
                           class="mt-0.5" onchange="highlightEnrollment()">
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">🔒 Enrolled Only</p>
                        <p class="text-xs text-gray-500 mt-1">Only students you manually enroll can access this exam.</p>
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
                       {{ old('randomize_questions', $exam->randomize_questions) ? 'checked' : '' }}
                       class="rounded w-4 h-4">
                <span class="text-sm text-gray-700">Randomize question order for each student</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="randomize_options" value="1"
                       {{ old('randomize_options', $exam->randomize_options) ? 'checked' : '' }}
                       class="rounded w-4 h-4">
                <span class="text-sm text-gray-700">Randomize answer options order</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="show_results_immediately" value="1"
                       {{ old('show_results_immediately', $exam->show_results_immediately) ? 'checked' : '' }}
                       class="rounded w-4 h-4">
                <span class="text-sm text-gray-700">Show results immediately after submission</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="allow_resume" value="1"
                       {{ old('allow_resume', $exam->allow_resume) ? 'checked' : '' }}
                       class="rounded w-4 h-4">
                <span class="text-sm text-gray-700">Allow students to resume if disconnected</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $exam->is_active) ? 'checked' : '' }}
                       class="rounded w-4 h-4">
                <span class="text-sm text-gray-700">Activate this exam</span>
            </label>
        </div>

        {{-- ✅ ACTIONS — only Update and Cancel inside this form, NO delete button here --}}
        <div class="flex gap-4 pt-2">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-8 rounded-lg transition">
                💾 Update Exam
            </button>
            <a href="{{ route('admin.exams.show', $exam) }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg transition">
                Cancel
            </a>
        </div>

    </form>{{-- END UPDATE FORM --}}

    {{-- ✅ DELETE FORM — completely separate from update form, outside it --}}
    <div class="mt-4 pt-4 border-t border-gray-200">
        <form id="delete-form"
              action="{{ route('admin.exams.destroy', $exam) }}"
              method="POST"
              onsubmit="return confirm('Are you sure you want to permanently delete this exam? This cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                🗑️ Delete This Exam
            </button>
        </form>
    </div>

</div>

<script>
function highlightEnrollment() {
    const isOpen = document.querySelector('input[name="enrollment_type"][value="open"]').checked;
    const lo = document.getElementById('label-open');
    const le = document.getElementById('label-enrolled');
    lo.classList.toggle('border-green-500', isOpen);
    lo.classList.toggle('bg-green-50',      isOpen);
    lo.classList.toggle('border-gray-200',  !isOpen);
    le.classList.toggle('border-purple-500',!isOpen);
    le.classList.toggle('bg-purple-50',     !isOpen);
    le.classList.toggle('border-gray-200',  isOpen);
}
</script>
@endsection