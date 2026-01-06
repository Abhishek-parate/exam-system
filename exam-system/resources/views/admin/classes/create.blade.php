@extends('layouts.admin')

@section('page-title', 'Create New Class')
@section('page-description', 'Add a new class and assign teachers')

@section('content')
<div class="container mx-auto max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('admin.classes.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Classes
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-bold mb-6">Create New Class</h2>

        <form action="{{ route('admin.classes.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                        Class Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" 
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror" 
                        placeholder="e.g., Class 10, Grade 12 Science" required>
                    @error('name')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="section">
                        Section
                    </label>
                    <input type="text" name="section" id="section" value="{{ old('section') }}" 
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" 
                        placeholder="e.g., A, B, C">
                    @error('section')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                    Description
                </label>
                <textarea name="description" id="description" rows="3" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    placeholder="Optional description about this class">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Assign Teachers
                </label>
                <div class="border rounded-lg p-4 max-h-64 overflow-y-auto bg-gray-50">
                    @forelse($teachers as $teacher)
                        <div class="mb-3 flex items-start">
                            <label class="flex items-start cursor-pointer hover:bg-white p-2 rounded transition">
                                <input type="checkbox" name="teacher_ids[]" value="{{ $teacher->id }}" 
                                    class="form-checkbox h-5 w-5 text-blue-600 mt-1"
                                    {{ in_array($teacher->id, old('teacher_ids', [])) ? 'checked' : '' }}>
                                <div class="ml-3">
                                    <span class="font-medium text-gray-900">{{ $teacher->user->name }}</span>
                                    @if($teacher->employee_id)
                                        <span class="text-gray-500 text-sm">(ID: {{ $teacher->employee_id }})</span>
                                    @endif
                                    <p class="text-xs text-gray-500">{{ $teacher->user->email }}</p>
                                </div>
                            </label>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No teachers available. Please create teachers first.</p>
                    @endforelse
                </div>
                @error('teacher_ids')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="form-checkbox h-5 w-5 text-blue-600" 
                        {{ old('is_active', true) ? 'checked' : '' }}>
                    <span class="ml-2 text-gray-700 font-medium">Active</span>
                    <span class="ml-2 text-sm text-gray-500">(Students can be enrolled and teachers can create exams)</span>
                </label>
            </div>

            <div class="flex items-center justify-between border-t pt-6">
                <a href="{{ route('admin.classes.index') }}" 
                    class="text-gray-600 hover:text-gray-800 font-medium">
                    Cancel
                </a>
                <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:shadow-outline transition">
                    Create Class
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
