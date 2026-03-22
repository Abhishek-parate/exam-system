@extends('layouts.admin')

@section('title', 'Add New Subject')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">📚 Add New Subject</h1>
        <a href="{{ route('admin.subjects.index') }}" class="text-gray-600 hover:text-gray-900">
            ← Back to Subjects
        </a>
    </div>

    <form method="POST" action="{{ route('admin.subjects.store') }}" class="bg-white rounded-lg shadow-md p-8">
        @csrf

        <!-- Exam Category -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Exam Category (Optional)</label>
            <select name="exam_category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                <option value="">Select Category</option>
                @foreach($examCategories as $category)
                    <option value="{{ $category->id }}" {{ old('exam_category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('exam_category_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Subject Name -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Subject Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required 
                   placeholder="e.g., Mathematics, Physics, Chemistry"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Subject Code -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Subject Code (Optional)</label>
            <input type="text" name="code" value="{{ old('code') }}" 
                   placeholder="e.g., MATH101, PHY201"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            @error('code')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
            <p class="text-xs text-gray-500 mt-1">Unique identifier for this subject</p>
        </div>

        <!-- Description -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
            <textarea name="description" rows="4" 
                      placeholder="Brief description of the subject..."
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Active Status -->
        <div class="mb-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" checked class="mr-2 w-4 h-4">
                <span class="text-sm font-medium text-gray-700">Subject is Active</span>
            </label>
        </div>

        <!-- Submit Buttons -->
        <div class="flex gap-4 pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition shadow-lg">
                💾 Save Subject
            </button>
            <a href="{{ route('admin.subjects.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-8 rounded-lg transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
