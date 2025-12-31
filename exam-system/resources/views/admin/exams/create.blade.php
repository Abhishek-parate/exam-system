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

    <form method="POST" action="{{ route('admin.exams.store') }}" class="bg-white rounded-lg shadow-md p-8">
        @csrf

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Exam Title *</label>
            <input type="text" name="title" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                   placeholder="Enter exam title">
            @error('title')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Exam Category *</label>
            <select name="exam_category_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            @error('exam_category_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea name="description" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                      placeholder="Exam instructions and details..."></textarea>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Duration (Minutes) *</label>
                <input type="number" name="duration_minutes" required min="1" value="60"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                @error('duration_minutes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Total Marks *</label>
                <input type="number" name="total_marks" required min="0" step="0.01" value="100"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                @error('total_marks')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Start Time *</label>
                <input type="datetime-local" name="start_time" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                @error('start_time')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">End Time *</label>
                <input type="datetime-local" name="end_time" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                @error('end_time')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Result Release Time (Optional)</label>
            <input type="datetime-local" name="result_release_time"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            <p class="text-xs text-gray-500 mt-1">Leave blank to show results immediately after submission</p>
        </div>

        <div class="mb-6 space-y-3">
            <label class="flex items-center">
                <input type="checkbox" name="randomize_questions" value="1" class="mr-2">
                <span class="text-sm text-gray-700">Randomize question order for each student</span>
            </label>

            <label class="flex items-center">
                <input type="checkbox" name="randomize_options" value="1" class="mr-2">
                <span class="text-sm text-gray-700">Randomize answer options order</span>
            </label>

            <label class="flex items-center">
                <input type="checkbox" name="show_results_immediately" value="1" checked class="mr-2">
                <span class="text-sm text-gray-700">Show results immediately after submission</span>
            </label>

            <label class="flex items-center">
                <input type="checkbox" name="allow_resume" value="1" checked class="mr-2">
                <span class="text-sm text-gray-700">Allow students to resume if disconnected</span>
            </label>

            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" checked class="mr-2">
                <span class="text-sm text-gray-700">Activate this exam</span>
            </label>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                💾 Create Exam
            </button>
            <a href="{{ route('admin.exams.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
