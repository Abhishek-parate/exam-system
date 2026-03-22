@extends('layouts.admin')

@section('title', 'Subject Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">📚 {{ $subject->name }}</h1>
        <div class="flex gap-3">
            <a href="{{ route('admin.subjects.edit', $subject) }}" 
               class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-6 rounded-lg transition">
                ✏️ Edit Subject
            </a>
            <a href="{{ route('admin.subjects.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg transition">
                ← Back to Subjects
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Questions</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_questions'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Active Questions</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['active_questions'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Chapters</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['total_chapters'] }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Subject Details -->
    <div class="bg-white rounded-lg shadow-md p-8 mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Subject Information</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Subject ID</p>
                <p class="text-lg text-gray-900">#{{ $subject->id }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Subject Code</p>
                <p class="text-lg text-gray-900">{{ $subject->code ?? 'N/A' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Exam Category</p>
                <p class="text-lg text-gray-900">{{ $subject->examCategory ? $subject->examCategory->name : 'No Category' }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Status</p>
                @if($subject->is_active)
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                        ✓ Active
                    </span>
                @else
                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                        ✗ Inactive
                    </span>
                @endif
            </div>

            @if($subject->description)
            <div class="md:col-span-2">
                <p class="text-sm font-medium text-gray-500 mb-1">Description</p>
                <p class="text-gray-900">{{ $subject->description }}</p>
            </div>
            @endif

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Created At</p>
                <p class="text-gray-900">{{ $subject->created_at->format('M d, Y h:i A') }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Last Updated</p>
                <p class="text-gray-900">{{ $subject->updated_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Quick Actions</h2>
        <div class="flex gap-4">
            <a href="{{ route('admin.questions.create') }}?subject_id={{ $subject->id }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                ➕ Add Question to this Subject
            </a>
            <a href="{{ route('admin.questions.index') }}?subject_id={{ $subject->id }}" 
               class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                📋 View All Questions
            </a>
        </div>
    </div>
</div>
@endsection
