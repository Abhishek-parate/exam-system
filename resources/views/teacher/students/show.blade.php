@extends('layouts.teacher')

@section('title', 'Student Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Student Details</h1>
                <p class="text-gray-600 mt-1">View comprehensive student information and performance</p>
            </div>
            <a href="{{ route('teacher.students.index') }}" 
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Back to Students
            </a>
        </div>

        <!-- Student Info Card -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center">
                    <div class="h-20 w-20 rounded-full bg-blue-500 flex items-center justify-center text-white text-3xl font-bold">
                        {{ strtoupper(substr($student->user->name ?? 'S', 0, 1)) }}
                    </div>
                    <div class="ml-6">
                        <h2 class="text-2xl font-bold text-gray-800">{{ $student->user->name ?? 'N/A' }}</h2>
                        <p class="text-gray-600">Enrollment Number: {{ $student->enrollment_number ?? 'N/A' }}</p>
                        <p class="text-gray-600">Class: {{ $student->class ?? 'N/A' }}</p>
                        <p class="text-gray-600">Target Exam: {{ $student->target_exam ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="text-right">
                    @if(isset($student->is_active))
                        @if($student->is_active)
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        @else
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Inactive
                            </span>
                        @endif
                    @else
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            Status N/A
                        </span>
                    @endif
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="border-l-4 border-blue-500 pl-4">
                    <p class="text-sm text-gray-600">Email</p>
                    <p class="text-gray-800 font-medium">{{ $student->user->email ?? 'N/A' }}</p>
                </div>
                <div class="border-l-4 border-green-500 pl-4">
                    <p class="text-sm text-gray-600">Phone</p>
                    <p class="text-gray-800 font-medium">{{ $student->user->phone ?? 'N/A' }}</p>
                </div>
                <div class="border-l-4 border-purple-500 pl-4">
                    <p class="text-sm text-gray-600">Date of Birth</p>
                    <p class="text-gray-800 font-medium">
                        {{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d M Y') : 'N/A' }}
                    </p>
                </div>
            </div>

            @if($student->address)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-sm font-medium text-gray-700 mb-2">Address</p>
                    <p class="text-gray-600">{{ $student->address }}</p>
                </div>
            @endif
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Exams</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $statistics['total_exams'] }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Completed</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $statistics['completed_exams'] }}</p>
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
                        <p class="text-gray-500 text-sm font-medium">In Progress</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ $statistics['in_progress'] }}</p>
                    </div>
                    <div class="bg-yellow-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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
                    <div class="bg-purple-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Exam Attempts -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Exam Attempts</h2>
            
            @if(isset($recentAttempts) && $recentAttempts->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exam</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Started At</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentAttempts as $attempt)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $attempt->exam->title ?? 'N/A' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $attempt->exam->exam_code ?? '' }}
                                        </div>
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
                                        @if(isset($attempt->obtained_marks) && isset($attempt->exam->total_marks) && $attempt->exam->total_marks > 0)
                                            {{ number_format(($attempt->obtained_marks / $attempt->exam->total_marks) * 100, 2) }}%
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No exam attempts yet</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
