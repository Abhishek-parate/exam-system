@extends('layouts.student')

@section('title', 'Profile - Student')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
        <span class="text-gray-600">Account & Academic Details</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Profile Card --}}
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex flex-col items-center">
                    <div class="w-24 h-24 rounded-full bg-purple-600 flex items-center justify-center text-white text-4xl font-bold mb-4">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 text-center">{{ $user->name }}</h2>
                    <p class="text-gray-500 text-sm mt-1">{{ $user->email }}</p>
                    <span class="mt-3 px-4 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                        Student
                    </span>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>Enrollment: <strong>{{ $student->enrollment_number ?? 'N/A' }}</strong></span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <span>Class: <strong>{{ $student->class ?? 'N/A' }}</strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Details Section --}}
        <div class="md:col-span-2 space-y-6">
            {{-- Academic Information --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-4 text-gray-900 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Academic Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border-l-4 border-purple-500 pl-4 py-2">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Enrollment Number</p>
                        <p class="text-gray-900 font-medium">{{ $student->enrollment_number ?? 'N/A' }}</p>
                    </div>
                    <div class="border-l-4 border-purple-500 pl-4 py-2">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Class</p>
                        <p class="text-gray-900 font-medium">{{ $student->class ?? 'N/A' }}</p>
                    </div>
                    <div class="border-l-4 border-purple-500 pl-4 py-2">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Target Exam</p>
                        <p class="text-gray-900 font-medium">{{ $student->target_exam ?? 'N/A' }}</p>
                    </div>
                    <div class="border-l-4 border-purple-500 pl-4 py-2">
                        <p class="text-xs font-semibold text-gray-500 uppercase">Date of Birth</p>
                        <p class="text-gray-900 font-medium">
                            {{ $student->date_of_birth ? $student->date_of_birth->format('d M Y') : 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Contact Information --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold mb-4 text-gray-900 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Contact & Address
                </h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Email Address</p>
                        <p class="text-gray-900">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Residential Address</p>
                        <p class="text-gray-900 whitespace-pre-line">{{ $student->address ?? 'No address provided.' }}</p>
                    </div>
                </div>
            </div>

            {{-- Account Stats --}}
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-md p-6 text-white">
                <h3 class="text-lg font-bold mb-4">Account Statistics</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-3xl font-bold">{{ $stats['enrolledExamsCount'] }}</p>
                        <p class="text-purple-100 text-sm mt-1">Enrolled Exams</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold">{{ $stats['examAttemptsCount'] }}</p>
                        <p class="text-purple-100 text-sm mt-1">Attempts</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold">{{ $stats['publishedResultsCount'] }}</p>
                        <p class="text-purple-100 text-sm mt-1">Published Results</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold">{{ number_format($stats['avgPercentage'], 1) }}%</p>
                        <p class="text-purple-100 text-sm mt-1">Avg Score</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
