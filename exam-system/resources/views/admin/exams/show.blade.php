@extends('layouts.admin')

@section('title', $exam->title . ' - Exam Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $exam->title }}</h1>
            <p class="text-gray-600 mt-1">{{ $exam->exam_code }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.exams.edit', $exam) }}" 
               class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                ✏️ Edit Exam
            </a>
            <a href="{{ route('admin.exams.index') }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition">
                ← Back to Exams
            </a>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="mb-6">
        <span class="px-4 py-2 text-sm font-semibold rounded-full 
            @if($exam->status === 'ongoing') bg-green-100 text-green-800
            @elseif($exam->status === 'upcoming') bg-blue-100 text-blue-800
            @else bg-gray-100 text-gray-800
            @endif">
            {{ ucfirst($exam->status) }}
        </span>
        @if($exam->is_active)
            <span class="ml-2 px-4 py-2 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                Active
            </span>
        @else
            <span class="ml-2 px-4 py-2 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                Inactive
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
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Enrolled Students</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['enrolled_students'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Completed</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $stats['completed_attempts'] }}</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Left Column: Exam Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">Exam Information</h2>
                <div class="space-y-3">
                    <div class="flex border-b pb-2">
                        <span class="w-48 text-gray-600">Category:</span>
                        <span class="font-semibold">{{ $exam->examCategory->name }}</span>
                    </div>
                    <div class="flex border-b pb-2">
                        <span class="w-48 text-gray-600">Duration:</span>
                        <span class="font-semibold">{{ $exam->duration_minutes }} minutes</span>
                    </div>
                    <div class="flex border-b pb-2">
                        <span class="w-48 text-gray-600">Total Marks:</span>
                        <span class="font-semibold">{{ $exam->total_marks }}</span>
                    </div>
                    <div class="flex border-b pb-2">
                        <span class="w-48 text-gray-600">Start Time:</span>
                        <span class="font-semibold">{{ $exam->start_time->format('d M Y, h:i A') }}</span>
                    </div>
                    <div class="flex border-b pb-2">
                        <span class="w-48 text-gray-600">End Time:</span>
                        <span class="font-semibold">{{ $exam->end_time->format('d M Y, h:i A') }}</span>
                    </div>
                    @if($exam->result_release_time)
                        <div class="flex border-b pb-2">
                            <span class="w-48 text-gray-600">Result Release:</span>
                            <span class="font-semibold">{{ $exam->result_release_time->format('d M Y, h:i A') }}</span>
                        </div>
                    @endif
                    <div class="flex border-b pb-2">
                        <span class="w-48 text-gray-600">Created By:</span>
                        <span class="font-semibold">{{ $exam->creator->name }}</span>
                    </div>
                </div>
            </div>

            <!-- Description -->
            @if($exam->description)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold mb-4">Description</h2>
                    <p class="text-gray-700">{{ $exam->description }}</p>
                </div>
            @endif

            <!-- Exam Settings -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">Settings</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-center">
                        @if($exam->randomize_questions)
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        @endif
                        <span class="text-sm">Randomize Questions</span>
                    </div>
                    
                    <div class="flex items-center">
                        @if($exam->randomize_options)
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        @endif
                        <span class="text-sm">Randomize Options</span>
                    </div>

                    <div class="flex items-center">
                        @if($exam->show_results_immediately)
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        @endif
                        <span class="text-sm">Show Results Immediately</span>
                    </div>

                    <div class="flex items-center">
                        @if($exam->allow_resume)
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        @endif
                        <span class="text-sm">Allow Resume</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Actions & Quick Info -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
                <div class="space-y-3">
                    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition text-sm">
                        📝 Add Questions
                    </button>
                    <button class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition text-sm">
                        👥 Enroll Students
                    </button>
                    <button class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg transition text-sm">
                        📊 View Results
                    </button>
                    <button class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 px-4 rounded-lg transition text-sm">
                        📄 Generate Report
                    </button>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-red-50 border border-red-200 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-red-800 mb-4">Danger Zone</h2>
                <form method="POST" action="{{ route('admin.exams.destroy', $exam) }}" 
                      onsubmit="return confirm('Are you sure you want to delete this exam? This action cannot be undone!');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg transition text-sm">
                        🗑️ Delete Exam
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Questions List -->
    @if($exam->questions->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">Questions ({{ $exam->questions->count() }})</h2>
            <div class="space-y-3">
                @foreach($exam->questions as $index => $question)
                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">Q{{ $index + 1 }}. {{ Str::limit(strip_tags($question->question_text), 100) }}</p>
                            <div class="flex gap-4 mt-2 text-xs text-gray-600">
                                <span>📚 {{ $question->subject->name }}</span>
                                <span>⚡ {{ $question->difficulty->name }}</span>
                                <span>+{{ $question->marks }} marks</span>
                            </div>
                        </div>
                        <a href="{{ route('admin.questions.show', $question) }}" 
                           class="text-blue-600 hover:text-blue-800 ml-4">
                            View →
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <svg class="mx-auto h-12 w-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <h3 class="mt-2 text-lg font-medium text-yellow-800">No questions added yet</h3>
            <p class="mt-1 text-sm text-yellow-600">Add questions to this exam to make it available for students.</p>
            <div class="mt-4">
                <button class="bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-6 rounded-lg transition">
                    Add Questions Now
                </button>
            </div>
        </div>
    @endif

    <!-- Enrolled Students -->
    @if($exam->enrolledStudents->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-xl font-bold mb-4">Enrolled Students ({{ $exam->enrolledStudents->count() }})</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enrollment No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($exam->enrolledStudents as $student)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $student->user->name }}</td>
                                <td class="px-4 py-3">{{ $student->enrollment_number }}</td>
                                <td class="px-4 py-3">{{ $student->user->email }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Enrolled
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
