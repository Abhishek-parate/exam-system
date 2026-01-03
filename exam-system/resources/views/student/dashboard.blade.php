@extends('layouts.student')

@section('title', 'Student Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Student Dashboard</h1>
        <span class="text-sm text-gray-600">
            Welcome, <strong>{{ auth()->user()->name }}</strong>
        </span>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-blue-600 text-white rounded-lg p-5 shadow">
            <p class="text-xs uppercase tracking-wide text-blue-100">Upcoming Exams</p>
            <p class="text-3xl font-bold mt-2">{{ $stats['upcoming_exams'] }}</p>
        </div>

        <div class="bg-green-600 text-white rounded-lg p-5 shadow">
            <p class="text-xs uppercase tracking-wide text-green-100">Ongoing Exams</p>
            <p class="text-3xl font-bold mt-2">{{ $stats['ongoing_exams'] }}</p>
        </div>

        <div class="bg-gray-700 text-white rounded-lg p-5 shadow">
            <p class="text-xs uppercase tracking-wide text-gray-200">Completed Exams</p>
            <p class="text-3xl font-bold mt-2">{{ $stats['completed_exams'] }}</p>
        </div>

        <div class="bg-indigo-600 text-white rounded-lg p-5 shadow">
            <p class="text-xs uppercase tracking-wide text-indigo-100">Total Attempts</p>
            <p class="text-3xl font-bold mt-2">{{ $stats['total_attempts'] }}</p>
        </div>
    </div>

    <!-- Upcoming/Ongoing exams & recent attempts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upcoming/Ongoing Exams -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-800">Upcoming & Ongoing Exams</h2>
                <a href="{{ route('student.exams.index') }}" class="text-blue-600 text-sm hover:text-blue-800">
                    View all →
                </a>
            </div>
            <div class="space-y-3">
                @forelse($nextExams as $exam)
                    <div class="p-3 rounded-lg bg-gray-50 flex justify-between items-center hover:bg-gray-100 transition">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $exam->title }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $exam->examCategory->name ?? '' }}
                                • {{ $exam->duration_minutes }} mins
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                Starts: {{ $exam->start_datetime?->format('d M Y, h:i A') ?? 'TBD' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 text-xs rounded-full font-semibold
                                @if($exam->status === 'ongoing') bg-green-100 text-green-800
                                @elseif($exam->status === 'upcoming') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-700
                                @endif">
                                {{ ucfirst($exam->status) }}
                            </span>
                            @if($exam->status === 'ongoing' || $exam->status === 'upcoming')
                                <a href="{{ route('student.exams.instructions', $exam) }}"
                                   class="mt-2 inline-block text-xs text-blue-600 hover:text-blue-800 font-semibold">
                                    View instructions
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4 text-sm">
                        No upcoming exams. Check back later.
                    </p>
                @endforelse
            </div>
        </div>

        <!-- Recent attempts -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Recent Attempts</h2>
            <div class="space-y-3">
                @forelse($recentAttempts as $attempt)
                    <div class="p-3 rounded-lg bg-gray-50 flex justify-between items-center hover:bg-gray-100 transition">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $attempt->exam->title ?? 'Exam' }}</p>
                            <p class="text-xs text-gray-500">
                                Attempted: {{ $attempt->created_at->format('d M Y, h:i A') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 text-xs rounded-full font-semibold
                                @if($attempt->status === 'submitted') bg-green-100 text-green-800
                                @elseif($attempt->status === 'in_progress') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-700
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                            </span>
                            @if(!is_null($attempt->score))
                                <p class="text-sm font-bold text-gray-700 mt-1">
                                    Score: {{ $attempt->score }}%
                                </p>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 py-4 text-sm">
                        You have not attempted any exams yet.
                    </p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
