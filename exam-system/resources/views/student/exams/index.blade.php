@extends('layouts.student')

@section('title', 'My Exams - Student')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Exams</h1>
        <span class="text-gray-600">Enrolled & Available Exams</span>
    </div>

    {{-- Available Exams (Can Take Now) --}}
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold mb-4 text-green-600">
            <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Available Now ({{ $availableExams->count() }})
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($availableExams as $exam)
            <div class="border border-green-200 rounded-lg p-6 hover:shadow-lg transition bg-green-50">
                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $exam->title }}</h3>
                <div class="space-y-2 text-sm text-gray-600 mb-4">
                    <p><strong>Category:</strong> {{ $exam->examCategory->name ?? 'N/A' }}</p>
                    <p><strong>Duration:</strong> {{ $exam->duration_minutes }} minutes</p>
                    <p><strong>Total Marks:</strong> {{ $exam->total_marks }}</p>
                    <p><strong>Start:</strong> {{ $exam->start_time->format('d M Y, h:i A') }}</p>
                    <p><strong>End:</strong> {{ $exam->end_time->format('d M Y, h:i A') }}</p>
                </div>
                <a href="{{ route('student.exams.instructions', $exam) }}" 
                   class="block w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg text-center transition">
                    Start Exam
                </a>
            </div>
            @empty
            <div class="col-span-full text-center py-8 text-gray-500">
                No exams available to take right now.
            </div>
            @endforelse
        </div>
    </div>

    {{-- Upcoming Exams --}}
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold mb-4 text-blue-600">
            <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Upcoming Exams ({{ $upcomingExams->count() }})
        </h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Exam Title</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Category</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Start Time</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Duration</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Marks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($upcomingExams as $exam)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $exam->title }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $exam->examCategory->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $exam->start_time->format('d M Y, h:i A') }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $exam->duration_minutes }} min</td>
                        <td class="px-4 py-3 text-gray-700">{{ $exam->total_marks }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">No upcoming exams scheduled.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Completed Exams --}}
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4 text-purple-600">
            <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Completed Exams ({{ $completedExams->count() }})
        </h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Exam Title</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Category</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Submitted</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Time Taken</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Score</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($completedExams as $attempt)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $attempt->exam->title }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $attempt->exam->examCategory->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $attempt->submitted_at->format('d M Y, h:i A') }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ round($attempt->time_taken_seconds / 60) }} min</td>
                        <td class="px-4 py-3">
                            @if($attempt->result)
                            <span class="font-semibold text-green-600">
                                {{ number_format($attempt->result->percentage, 2) }}%
                            </span>
                            @else
                            <span class="text-gray-400">Pending</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($attempt->result && $attempt->result->is_published)
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Published
                            </span>
                            @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Under Review
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No completed exams yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
