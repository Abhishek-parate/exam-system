@extends('layouts.teacher')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Teacher Dashboard</h1>
        <span class="text-gray-600">Welcome, {{ auth()->user()->name }}</span>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">My Exams</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['total_exams'] }}</p>
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
                    <p class="text-green-100 text-sm">Ongoing</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['ongoing_exams'] }}</p>
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
                    <p class="text-purple-100 text-sm">Students</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['total_students'] }}</p>
                </div>
                <div class="bg-white bg-opacity-30 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">Attempts</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['total_attempts'] }}</p>
                </div>
                <div class="bg-white bg-opacity-30 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('teacher.exams.create') }}" class="btn-primary text-center py-3">
                ➕ Create New Exam
            </a>
            <a href="{{ route('teacher.exams.index') }}" class="bg-purple-500 hover:bg-purple-600 text-white font-semibold py-3 px-4 rounded-lg text-center transition">
                📋 My Exams
            </a>
            <a href="/teacher/reports" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-lg text-center transition">
                📊 View Reports
            </a>
        </div>
    </div>

    <!-- Recent Exams -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4">Recent Exams</h2>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Exam Title</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Category</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Start Time</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($recentExams as $exam)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $exam->title }}</td>
                            <td class="px-4 py-3">{{ $exam->examCategory->name }}</td>
                            <td class="px-4 py-3">{{ $exam->start_time->format('d M Y, h:i A') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                    @if($exam->status === 'ongoing') bg-green-100 text-green-800
                                    @elseif($exam->status === 'upcoming') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($exam->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('teacher.exams.show', $exam) }}" class="text-blue-600 hover:text-blue-800">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                No exams created yet
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
