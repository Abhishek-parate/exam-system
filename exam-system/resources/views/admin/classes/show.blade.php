@extends('layouts.admin')

@section('page-title', 'Class Details')
@section('page-description', $class->name . ($class->section ? ' - ' . $class->section : ''))

@section('content')
<div class="container mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('admin.classes.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Classes
        </a>
        
        <div class="space-x-2">
            <a href="{{ route('admin.classes.edit', $class->id) }}" 
               class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                Edit Class
            </a>
        </div>
    </div>

    <!-- Class Info Card -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $class->name }} {{ $class->section }}</h2>
                @if($class->description)
                    <p class="text-gray-600 mt-2">{{ $class->description }}</p>
                @endif
            </div>
            <span class="px-3 py-1 text-sm font-semibold rounded-full 
                {{ $class->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $class->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <div class="ml-3">
                        <p class="text-2xl font-bold text-blue-600">{{ $statistics['total_teachers'] }}</p>
                        <p class="text-sm text-gray-600">Teachers</p>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <div class="ml-3">
                        <p class="text-2xl font-bold text-green-600">{{ $statistics['total_exams'] }}</p>
                        <p class="text-sm text-gray-600">Total Exams</p>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="ml-3">
                        <p class="text-2xl font-bold text-yellow-600">{{ $statistics['active_exams'] }}</p>
                        <p class="text-sm text-gray-600">Active Exams</p>
                    </div>
                </div>
            </div>

            <div class="bg-purple-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <div class="ml-3">
                        <p class="text-2xl font-bold text-purple-600">{{ $statistics['total_students'] }}</p>
                        <p class="text-sm text-gray-600">Students</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Assigned Teachers -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-lg font-bold mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Assigned Teachers ({{ $class->teachers->count() }})
            </h3>
            
            @if($class->teachers->count() > 0)
                <div class="space-y-3">
                    @foreach($class->teachers as $teacher)
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold">
                                {{ strtoupper(substr($teacher->user->name, 0, 1)) }}
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="font-medium text-gray-900">{{ $teacher->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $teacher->user->email }}</p>
                            </div>
                            @if($teacher->employee_id)
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                    ID: {{ $teacher->employee_id }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No teachers assigned to this class yet.</p>
            @endif
        </div>

        <!-- Recent Exams -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h3 class="text-lg font-bold mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Recent Exams
            </h3>
            
            @if($class->exams->count() > 0)
                <div class="space-y-3">
                    @foreach($class->exams->take(5) as $exam)
                        <div class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $exam->title }}</p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Created by: {{ $exam->creator->name ?? 'Unknown' }}
                                    </p>
                                </div>
                                <span class="text-xs px-2 py-1 rounded {{ $exam->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $exam->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="mt-2 flex items-center text-xs text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $exam->duration_minutes }} mins
                                <span class="mx-2">•</span>
                                {{ $exam->total_marks }} marks
                            </div>
                        </div>
                    @endforeach
                    
                    @if($class->exams->count() > 5)
                        <p class="text-sm text-blue-600 text-center pt-2">
                            And {{ $class->exams->count() - 5 }} more exams...
                        </p>
                    @endif
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No exams created for this class yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection