@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <span class="text-gray-600">Welcome, <strong>{{ auth()->user()->name }}</strong></span>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-semibold">Total Students</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['total_students'] ?? 0 }}</p>
                </div>
                <div class="bg-white bg-opacity-30 p-4 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-semibold">Total Teachers</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['total_teachers'] ?? 0 }}</p>
                </div>
                <div class="bg-white bg-opacity-30 p-4 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-semibold">Total Exams</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['total_exams'] ?? 0 }}</p>
                </div>
                <div class="bg-white bg-opacity-30 p-4 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-semibold">Total Questions</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['total_questions'] ?? 0 }}</p>
                </div>
                <div class="bg-white bg-opacity-30 p-4 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-semibold">Ongoing Exams</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['ongoing_exams'] ?? 0 }}</p>
                </div>
                <div class="bg-white bg-opacity-30 p-4 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-sm font-semibold">Total Attempts</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['total_attempts'] ?? 0 }}</p>
                </div>
                <div class="bg-white bg-opacity-30 p-4 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold mb-4 text-gray-800">⚡ Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.exam-categories.create') }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-4 rounded-lg text-center transition shadow-md hover:shadow-lg">
                📚 Add Category
            </a>
            <a href="{{ route('admin.subjects.create') }}" 
               class="bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-4 rounded-lg text-center transition shadow-md hover:shadow-lg">
                📖 Add Subject
            </a>
            <a href="{{ route('admin.questions.create') }}" 
               class="bg-purple-500 hover:bg-purple-600 text-white font-semibold py-3 px-4 rounded-lg text-center transition shadow-md hover:shadow-lg">
                ➕ Add Question
            </a>
            <a href="{{ route('admin.users.create') }}" 
               class="bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-lg text-center transition shadow-md hover:shadow-lg">
                👤 Add User
            </a>
            <a href="{{ route('admin.exams.create') }}" 
               class="bg-pink-500 hover:bg-pink-600 text-white font-semibold py-3 px-4 rounded-lg text-center transition shadow-md hover:shadow-lg">
                📝 Create Exam
            </a>
            <a href="{{ route('admin.reports') }}" 
               class="bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-3 px-4 rounded-lg text-center transition shadow-md hover:shadow-lg">
                📊 View Reports
            </a>
            <a href="{{ route('admin.exam-categories.index') }}" 
               class="bg-teal-500 hover:bg-teal-600 text-white font-semibold py-3 px-4 rounded-lg text-center transition shadow-md hover:shadow-lg">
                📋 Manage Categories
            </a>
            <a href="{{ route('admin.subjects.index') }}" 
               class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-3 px-4 rounded-lg text-center transition shadow-md hover:shadow-lg">
                🗂️ Manage Subjects
            </a>
        </div>
    </div>

    <!-- Recent Activity Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Exams -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">📝 Recent Exams</h2>
                <a href="{{ route('admin.exams.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All →</a>
            </div>
            <div class="space-y-3">
                @forelse($recentExams ?? [] as $exam)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition cursor-pointer">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">{{ $exam->title }}</p>
                            <p class="text-sm text-gray-600">{{ $exam->examCategory->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                📅 {{ $exam->start_datetime ? $exam->start_datetime->format('M d, Y h:i A') : 'Not scheduled' }}
                            </p>
                        </div>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                            @if($exam->status === 'ongoing') bg-green-100 text-green-800
                            @elseif($exam->status === 'upcoming') bg-blue-100 text-blue-800
                            @elseif($exam->status === 'completed') bg-gray-100 text-gray-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst($exam->status ?? 'draft') }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="text-6xl mb-3">📝</div>
                        <p class="text-gray-500 font-medium">No exams yet</p>
                        <a href="{{ route('admin.exams.create') }}" class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">
                            Create your first exam →
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Exam Attempts -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">🎯 Recent Exam Attempts</h2>
                <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All →</a>
            </div>
            <div class="space-y-3">
                @forelse($recentAttempts ?? [] as $attempt)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">{{ $attempt->student->user->name ?? 'Unknown' }}</p>
                            <p class="text-sm text-gray-600">{{ $attempt->exam->title ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                ⏱️ {{ $attempt->created_at ? $attempt->created_at->diffForHumans() : '-' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                @if($attempt->status === 'submitted') bg-green-100 text-green-800
                                @elseif($attempt->status === 'in_progress') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $attempt->status ?? 'unknown')) }}
                            </span>
                            @if(isset($attempt->score))
                                <p class="text-sm font-bold text-gray-700 mt-1">{{ $attempt->score }}%</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="text-6xl mb-3">🎯</div>
                        <p class="text-gray-500 font-medium">No exam attempts yet</p>
                        <p class="text-gray-400 text-sm mt-1">Student attempts will appear here</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- System Overview (Optional - add at bottom) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">📊 System Health</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Database</span>
                    <span class="text-green-600 font-semibold">✓ Online</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Storage</span>
                    <span class="text-green-600 font-semibold">✓ Available</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Cache</span>
                    <span class="text-green-600 font-semibold">✓ Active</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">🔥 Popular Categories</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Engineering</span>
                    <span class="font-semibold text-blue-600">{{ $stats['total_questions'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Medical</span>
                    <span class="font-semibold text-blue-600">0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Commerce</span>
                    <span class="font-semibold text-blue-600">0</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">⏰ Upcoming</h3>
            <div class="space-y-2">
                <p class="text-sm text-gray-600">Next scheduled exam in:</p>
                <p class="text-2xl font-bold text-blue-600">2 days</p>
                <p class="text-xs text-gray-500">Check exams section for details</p>
            </div>
        </div>
    </div>
</div>
@endsection
