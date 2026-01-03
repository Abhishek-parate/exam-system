@extends('layouts.admin')

@section('title', 'Edit Exam Category')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Edit Exam Category</h1>
        <a href="{{ route('admin.exam-categories.index') }}" class="text-gray-600 hover:text-gray-900">
            ← Back
        </a>
    </div>

    <form method="POST" action="{{ route('admin.exam-categories.update', $exam_category) }}"
          class="bg-white rounded-lg shadow-md p-6">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
            <input type="text" name="name" value="{{ old('name', $exam_category->name) }}" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea name="description" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ old('description', $exam_category->description) }}</textarea>
        </div>

        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $exam_category->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 text-blue-600">
                <span class="ml-2 text-sm text-gray-700">Active</span>
            </label>
        </div>

        <div class="flex gap-4">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
                Update
            </button>
            <a href="{{ route('admin.exam-categories.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
