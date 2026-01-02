@extends('layouts.teacher')

@section('title', 'Exam Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $exam->title }}</h1>
                <p class="text-gray-600 mt-1">Exam Code: <span class="font-mono">{{ $exam->exam_code }}</span></p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('teacher.exams.index') }}" 
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    ← Back to Exams
                </a>
                @if($status === 'Scheduled')
                    <a href="{{ route('teacher.exams.edit', $exam->id) }}" 
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Edit Exam
                    </a>
                @endif
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Status Badge -->
        <div class="mb-6">
            @php
                if ($status === 'Scheduled') {
                    $statusColor = 'bg-yellow-100 text-yellow-800 border-yellow-300';
                } elseif ($status === 'Ongoing') {
                    $statusColor = 'bg-green-100 text-green-800 border-green-300';
                } else {
                    $statusColor = 'bg-gray-100 text-gray-800 border-gray-300';
                }
            @endphp
            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold border {{ $statusColor }}">
                <span class="w-2 h-2 mr-2 rounded-full {{ $status === 'Ongoing' ? 'bg-green-500 animate-pulse' : 'bg-current' }}"></span>
                {{ $status }}
            </span>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Questions</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $statistics['total_questions'] }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Attempts</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $statistics['total_attempts'] }}</p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Completed</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $statistics['completed_attempts'] }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Average Score</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($statistics['average_score'], 1) }}%</p>
                    </div>
                    <div class="bg-yellow-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exam Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Main Details -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Exam Information</h2>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Category</p>
                            <p class="text-gray-800 font-medium">{{ $exam->examCategory->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Duration</p>
                            <p class="text-gray-800 font-medium">{{ $exam->duration_minutes }} minutes</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Questions</p>
                            <p class="text-gray-800 font-medium">{{ $exam->total_questions }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Marks</p>
                            <p class="text-gray-800 font-medium">{{ $exam->total_marks }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Start Time</p>
                            <p class="text-gray-800 font-medium">{{ $exam->start_time->format('d M Y, h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">End Time</p>
                            <p class="text-gray-800 font-medium">{{ $exam->end_time->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>

                    @if($exam->description)
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Description</p>
                            <p class="text-gray-800">{{ $exam->description }}</p>
                        </div>
                    @endif

                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-2">Settings</p>
                        <div class="flex flex-wrap gap-2">
                            @if($exam->show_results_immediately)
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">Show Results Immediately</span>
                            @endif
                            @if($exam->randomize_questions)
                                <span class="px-3 py-1 bg-purple-100 text-purple-800 text-sm rounded-full">Randomize Questions</span>
                            @endif
                            @if($exam->randomize_options)
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full">Randomize Options</span>
                            @endif
                            @if($exam->allow_resume)
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm rounded-full">Allow Resume</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Marking Schemes -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Marking Schemes</h2>
                
                <div class="space-y-3">
                    @foreach($exam->markingSchemes as $scheme)
                        <div class="border rounded-lg p-3">
                            <p class="font-medium text-gray-800 mb-2">{{ $scheme->subject->name }}</p>
                            <div class="text-sm space-y-1">
                                <p class="text-gray-600">✓ Correct: <span class="font-medium text-green-600">+{{ $scheme->correct_marks }}</span></p>
                                <p class="text-gray-600">✗ Wrong: <span class="font-medium text-red-600">-{{ $scheme->wrong_marks }}</span></p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Questions List -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Questions ({{ $exam->questions->count() }})</h2>
            
            <div class="space-y-4">
                @foreach($exam->questions as $index => $question)
                    <div class="border rounded-lg p-4 hover:bg-gray-50">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Q{{ $index + 1 }}</span>
                                    <span class="text-xs text-gray-500">{{ $question->subject->name ?? 'N/A' }}</span>
                                </div>
                                <p class="text-gray-800 font-medium mb-2">{{ $question->question_text }}</p>
                                
                                @if($question->options && $question->options->count() > 0)
                                    <div class="mt-2 space-y-1">
                                        @foreach($question->options as $option)
                                            <div class="flex items-center text-sm {{ $option->is_correct ? 'text-green-600 font-medium' : 'text-gray-600' }}">
                                                <span class="mr-2">{{ $option->is_correct ? '✓' : '○' }}</span>
                                                <span>{{ $option->option_text }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Attempts -->
        @if($exam->attempts->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Attempts</h2>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Started At</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($exam->attempts->take(10) as $attempt)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $attempt->student->user->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $attempt->started_at ? $attempt->started_at->format('d M Y, h:i A') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'in_progress' => 'bg-yellow-100 text-yellow-800',
                                                'submitted' => 'bg-green-100 text-green-800',
                                                'auto_submitted' => 'bg-blue-100 text-blue-800',
                                            ];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$attempt->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $attempt->score ? number_format($attempt->score, 1) . '%' : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="#" class="text-blue-600 hover:text-blue-900">View Details</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Delete Button -->
        @if($status === 'Scheduled' && $exam->attempts->count() === 0)
            <div class="mt-8 border-t pt-6">
                <form action="{{ route('teacher.exams.destroy', $exam->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this exam? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Delete Exam
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
