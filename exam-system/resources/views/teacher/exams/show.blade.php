@extends('layouts.teacher')

@section('title', 'Exam Details')
@section('page-title', $exam->title)
@section('page-description', 'View and manage exam details')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header with Action Buttons -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $exam->title }}</h1>
            <p class="text-sm text-gray-600 mt-1">Exam Code: <span class="font-mono font-semibold">{{ $exam->exam_code }}</span></p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('teacher.exams.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                ← Back to Exams
            </a>
            <a href="{{ route('teacher.exams.edit', $exam) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                ✏️ Edit Exam
            </a>
            <form action="{{ route('teacher.exams.destroy', $exam) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this exam?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                    🗑️ Delete
                </button>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Questions</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['total_questions'] }}</p>
                </div>
                <div class="p-3 bg-white bg-opacity-20 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Enrolled</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['total_enrolled'] }}</p>
                </div>
                <div class="p-3 bg-white bg-opacity-20 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Attempts</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['total_attempts'] }}</p>
                </div>
                <div class="p-3 bg-white bg-opacity-20 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Completed</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['total_completed'] }}</p>
                </div>
                <div class="p-3 bg-white bg-opacity-20 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Exam Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">📋 Basic Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Exam Title</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $exam->title }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Exam Code</p>
                            <p class="text-lg font-semibold font-mono text-blue-600">{{ $exam->exam_code }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Category</p>
                            <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                                {{ $exam->examCategory->name ?? 'N/A' }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Class</p>
                            <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $exam->schoolClass->name ?? 'Not Assigned' }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Status</p>
                            @if($exam->is_active)
                                <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                    ✓ Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                    ✗ Inactive
                                </span>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Created By</p>
                            <p class="text-sm font-medium text-gray-900">{{ $exam->creator->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    
                    @if($exam->description)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <p class="text-sm text-gray-600 mb-2">Description</p>
                        <p class="text-sm text-gray-800">{{ $exam->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Exam Configuration -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">⚙️ Configuration</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-2">Duration</p>
                            <p class="text-3xl font-bold text-blue-600">{{ $exam->duration_minutes }}</p>
                            <p class="text-xs text-gray-500 mt-1">minutes</p>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-2">Questions</p>
                            <p class="text-3xl font-bold text-purple-600">{{ $exam->total_questions }}</p>
                            <p class="text-xs text-gray-500 mt-1">total</p>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-2">Total Marks</p>
                            <p class="text-3xl font-bold text-green-600">{{ $exam->total_marks }}</p>
                            <p class="text-xs text-gray-500 mt-1">marks</p>
                        </div>
                        <div class="text-center p-4 bg-orange-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-2">Passing</p>
                            <p class="text-3xl font-bold text-orange-600">{{ $exam->passing_marks }}</p>
                            <p class="text-xs text-gray-500 mt-1">marks</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">📅 Schedule</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm text-gray-600 mb-2">Start Time</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ \Carbon\Carbon::parse($exam->start_time)->format('d M Y') }}
                            </p>
                            <p class="text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($exam->start_time)->format('h:i A') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-2">End Time</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ \Carbon\Carbon::parse($exam->end_time)->format('d M Y') }}
                            </p>
                            <p class="text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($exam->end_time)->format('h:i A') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-2">Result Release</p>
                            @if($exam->result_release_time)
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::parse($exam->result_release_time)->format('d M Y') }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($exam->result_release_time)->format('h:i A') }}
                                </p>
                            @else
                                <p class="text-sm text-gray-500 italic">Not set</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Marking Schemes -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">📊 Marking Schemes</h2>
                </div>
                <div class="p-6">
                    @if($exam->markingSchemes->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Correct</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Wrong</th>
                                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Unattempted</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($exam->markingSchemes as $scheme)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $scheme->subject->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-green-600 font-semibold">+{{ $scheme->correct_marks }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-red-600 font-semibold">-{{ $scheme->wrong_marks }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="text-gray-600 font-semibold">{{ $scheme->unattempted_marks }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-gray-500 py-4">No marking schemes defined.</p>
                    @endif
                </div>
            </div>

            <!-- Questions List -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">❓ Questions ({{ $exam->questions->count() }})</h2>
                </div>
                <div class="p-6">
                    @if($exam->questions->count() > 0)
                        <div class="space-y-4">
                            @foreach($exam->questions as $index => $question)
                            <div class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                <div class="flex items-start">
                                    <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-blue-100 text-blue-600 rounded-full font-semibold text-sm">
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="ml-4 flex-1">
                                        <div class="text-sm text-gray-900 mb-2">
                                            {!! Str::limit(strip_tags($question->question_text), 200) !!}
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $question->subject->name ?? 'N/A' }}
                                            </span>
                                            @if($question->difficulty_level)
                                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full 
                                                {{ $question->difficulty_level == 'easy' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $question->difficulty_level == 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $question->difficulty_level == 'hard' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ ucfirst($question->difficulty_level) }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-gray-500 py-8">No questions added to this exam.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Settings & Actions -->
        <div class="space-y-6">
            <!-- Exam Settings -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-teal-500 to-teal-600 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">🔧 Settings</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-700">Randomize Questions</span>
                            @if($exam->randomize_questions)
                                <span class="text-green-600 font-semibold">✓ Yes</span>
                            @else
                                <span class="text-gray-400">✗ No</span>
                            @endif
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-700">Randomize Options</span>
                            @if($exam->randomize_options)
                                <span class="text-green-600 font-semibold">✓ Yes</span>
                            @else
                                <span class="text-gray-400">✗ No</span>
                            @endif
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-700">Show Results Immediately</span>
                            @if($exam->show_results_immediately)
                                <span class="text-green-600 font-semibold">✓ Yes</span>
                            @else
                                <span class="text-gray-400">✗ No</span>
                            @endif
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm text-gray-700">Allow Resume</span>
                            @if($exam->allow_resume)
                                <span class="text-green-600 font-semibold">✓ Yes</span>
                            @else
                                <span class="text-gray-400">✗ No</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-pink-500 to-pink-600 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">⚡ Quick Actions</h2>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('teacher.exams.edit', $exam) }}" 
                       class="block w-full px-4 py-3 bg-yellow-500 text-white text-center rounded-lg hover:bg-yellow-600 transition font-semibold">
                        ✏️ Edit Exam
                    </a>
                    
                    <button type="button" 
                            class="block w-full px-4 py-3 bg-green-500 text-white text-center rounded-lg hover:bg-green-600 transition font-semibold">
                        👥 Enroll Students
                    </button>
                    
                    <button type="button" 
                            class="block w-full px-4 py-3 bg-blue-500 text-white text-center rounded-lg hover:bg-blue-600 transition font-semibold">
                        📊 View Reports
                    </button>
                    
                    <form action="{{ route('teacher.exams.destroy', $exam) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this exam?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="block w-full px-4 py-3 bg-red-500 text-white text-center rounded-lg hover:bg-red-600 transition font-semibold">
                            🗑️ Delete Exam
                        </button>
                    </form>
                </div>
            </div>

            <!-- Exam Timeline -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">🕐 Timeline</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Created</p>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $exam->created_at->format('d M Y, h:i A') }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $exam->created_at->diffForHumans() }}</p>
                        </div>
                        
                        @if($exam->updated_at != $exam->created_at)
                        <div class="pt-4 border-t border-gray-200">
                            <p class="text-xs text-gray-500 mb-1">Last Updated</p>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $exam->updated_at->format('d M Y, h:i A') }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $exam->updated_at->diffForHumans() }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
