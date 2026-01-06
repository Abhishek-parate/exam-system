@extends('layouts.admin')

@section('title', 'Exam Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $exam->title }}</h1>
            <p class="text-sm text-gray-500 mt-1">Exam Code: <span class="font-mono font-semibold">{{ $exam->exam_code }}</span></p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.exams.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                ← Back to Exams
            </a>
            <a href="{{ route('admin.exams.edit', $exam) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                ✏️ Edit Exam
            </a>
            <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this exam?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    🗑️ Delete
                </button>
            </form>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="mb-6">
        @if($exam->is_active)
            <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                ✓ Active
            </span>
        @else
            <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                ✗ Inactive
            </span>
        @endif
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Questions</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total_questions'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Enrolled Students</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['total_enrolled'] }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Attempts</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['total_attempts'] }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Completed</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $stats['total_completed'] }}</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">📋 Basic Information</h2>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-gray-600 font-medium">Category:</dt>
                    <dd class="text-gray-900">{{ $exam->examCategory->name ?? 'N/A' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600 font-medium">Class:</dt>
                    <dd class="text-gray-900">{{ $exam->schoolClass->name ?? 'Not Assigned' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600 font-medium">Duration:</dt>
                    <dd class="text-gray-900">{{ $exam->duration_minutes }} minutes</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600 font-medium">Total Marks:</dt>
                    <dd class="text-gray-900 font-bold">{{ $exam->total_marks }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600 font-medium">Passing Marks:</dt>
                    <dd class="text-gray-900 font-bold">{{ $exam->passing_marks }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600 font-medium">Created By:</dt>
                    <dd class="text-gray-900">{{ $exam->creator->name ?? 'Unknown' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Schedule Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">📅 Schedule</h2>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-gray-600 font-medium">Start Time:</dt>
                    <dd class="text-gray-900">{{ \Carbon\Carbon::parse($exam->start_time)->format('d M Y, h:i A') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600 font-medium">End Time:</dt>
                    <dd class="text-gray-900">{{ \Carbon\Carbon::parse($exam->end_time)->format('d M Y, h:i A') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600 font-medium">Result Release:</dt>
                    <dd class="text-gray-900">
                        @if($exam->result_release_time)
                            {{ \Carbon\Carbon::parse($exam->result_release_time)->format('d M Y, h:i A') }}
                        @else
                            <span class="text-blue-600">{{ $exam->show_results_immediately ? 'Immediate' : 'Manual' }}</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Settings -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">⚙️ Exam Settings</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="flex items-center gap-2">
                @if($exam->randomize_questions)
                    <span class="text-green-600">✓</span>
                    <span class="text-gray-700">Randomize Questions</span>
                @else
                    <span class="text-gray-400">✗</span>
                    <span class="text-gray-400">Randomize Questions</span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if($exam->randomize_options)
                    <span class="text-green-600">✓</span>
                    <span class="text-gray-700">Randomize Options</span>
                @else
                    <span class="text-gray-400">✗</span>
                    <span class="text-gray-400">Randomize Options</span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if($exam->allow_resume)
                    <span class="text-green-600">✓</span>
                    <span class="text-gray-700">Allow Resume</span>
                @else
                    <span class="text-gray-400">✗</span>
                    <span class="text-gray-400">Allow Resume</span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if($exam->show_results_immediately)
                    <span class="text-green-600">✓</span>
                    <span class="text-gray-700">Show Results Immediately</span>
                @else
                    <span class="text-gray-400">✗</span>
                    <span class="text-gray-400">Show Results Immediately</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Description -->
    @if($exam->description)
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">📝 Description</h2>
        <p class="text-gray-700 whitespace-pre-wrap">{{ $exam->description }}</p>
    </div>
    @endif

    <!-- Marking Schemes -->
    @if($exam->markingSchemes->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">📊 Marking Scheme</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Correct Marks</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wrong Marks</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unattempted</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($exam->markingSchemes as $scheme)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $scheme->subject->name ?? 'Unknown' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-semibold">
                            +{{ $scheme->correct_marks }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-semibold">
                            -{{ $scheme->wrong_marks }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $scheme->unattempted_marks }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Questions List -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">❓ Questions ({{ $exam->questions->count() }})</h2>
        @if($exam->questions->count() > 0)
            <div class="space-y-4">
                @foreach($exam->questions as $index => $question)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900 mb-2">
                                <span class="text-blue-600">Q{{ $index + 1 }}.</span> 
                                {!! Str::limit(strip_tags($question->question_text), 150) !!}
                            </p>
                            <div class="flex gap-2 flex-wrap">
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $question->subject->name ?? 'Unknown' }}
                                </span>
                                @if($question->difficulty_level)
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
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
            <p class="text-gray-500 text-center py-8">No questions added to this exam yet.</p>
        @endif
    </div>
</div>
@endsection
