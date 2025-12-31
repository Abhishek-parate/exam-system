@extends('layouts.student')

@section('title', 'Student Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Student Dashboard</h1>
            <p class="text-gray-600 mt-1">Welcome back, {{ auth()->user()->name }}</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-600">Enrollment: <span class="font-semibold">{{ auth()->user()->student->enrollment_number }}</span></p>
            <p class="text-sm text-gray-600">Target: <span class="font-semibold">{{ auth()->user()->student->target_exam ?? 'Not Set' }}</span></p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Enrolled Exams</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['total_enrolled'] }}</p>
                </div>
                <div class="bg-white bg-opacity-30 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Available Now</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['available_now'] }}</p>
                </div>
                <div class="bg-white bg-opacity-30 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Upcoming</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['upcoming'] }}</p>
                </div>
                <div class="bg-white bg-opacity-30 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">Completed</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['total_completed'] }}</p>
                </div>
                <div class="bg-white bg-opacity-30 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Exams NOW -->
    @if($availableExams->count() > 0)
    <div class="bg-green-50 border-l-4 border-green-500 p-6 mb-8 rounded-lg">
        <div class="flex items-center mb-4">
            <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h2 class="text-xl font-bold text-green-800">⚡ Exams Available Now</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($availableExams as $exam)
                <div class="bg-white rounded-lg shadow-md p-6 border-2 border-green-200">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $exam->title }}</h3>
                            <p class="text-sm text-gray-600">{{ $exam->examCategory->name }}</p>
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full animate-pulse">
                            LIVE NOW
                        </span>
                    </div>
                    <div class="space-y-2 mb-4">
                        <p class="text-sm text-gray-600">⏱️ Duration: <strong>{{ $exam->duration_minutes }} minutes</strong></p>
                        <p class="text-sm text-gray-600">📝 Questions: <strong>{{ $exam->total_questions }}</strong></p>
                        <p class="text-sm text-gray-600">📊 Total Marks: <strong>{{ $exam->total_marks }}</strong></p>
                        <p class="text-sm text-gray-600">⏰ Ends: <strong>{{ $exam->end_time->format('d M Y, h:i A') }}</strong></p>
                    </div>
                    <a href="{{ route('student.exams.instructions', $exam) }}" 
                       class="block text-center bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition">
                        Start Exam Now →
                    </a>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Upcoming Exams -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800">📅 Upcoming Exams</h2>
            <div class="space-y-3">
                @forelse($upcomingExams as $exam)
                    <div class="flex justify-between items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $exam->title }}</p>
                            <p class="text-sm text-gray-600">{{ $exam->examCategory->name }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                🕐 Starts: {{ $exam->start_time->format('d M Y, h:i A') }}
                            </p>
                        </div>
                        <span class="px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded-full">
                            {{ $exam->start_time->diffForHumans() }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p>No upcoming exams</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Results -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800">📊 Recent Results</h2>
            <div class="space-y-3">
                @forelse($completedAttempts as $attempt)
                    @if($attempt->result && $attempt->result->is_published)
                        <div class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $attempt->exam->title }}</p>
                                    <p class="text-sm text-gray-600">{{ $attempt->submitted_at->format('d M Y') }}</p>
                                </div>
                                <span class="text-2xl font-bold text-blue-600">
                                    {{ round($attempt->result->obtained_marks, 2) }}/{{ $attempt->result->total_marks }}
                                </span>
                            </div>
                            <div class="grid grid-cols-3 gap-2 text-xs">
                                <div class="text-center p-2 bg-green-100 rounded">
                                    <p class="text-green-800 font-semibold">{{ $attempt->result->correct_answers }}</p>
                                    <p class="text-gray-600">Correct</p>
                                </div>
                                <div class="text-center p-2 bg-red-100 rounded">
                                    <p class="text-red-800 font-semibold">{{ $attempt->result->wrong_answers }}</p>
                                    <p class="text-gray-600">Wrong</p>
                                </div>
                                <div class="text-center p-2 bg-gray-100 rounded">
                                    <p class="text-gray-800 font-semibold">{{ $attempt->result->rank }}/{{ $attempt->result->total_participants }}</p>
                                    <p class="text-gray-600">Rank</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                            <p class="font-semibold text-gray-800">{{ $attempt->exam->title }}</p>
                            <p class="text-sm text-yellow-700 mt-1">⏳ Result will be published soon</p>
                        </div>
                    @endif
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <p>No results yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
