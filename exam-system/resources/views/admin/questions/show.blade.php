@extends('layouts.admin')

@section('title', 'Question Details')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex justify-between items-start flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-3xl font-bold text-gray-900">Question Details</h1>
                    @if($question->is_active)
                        <span class="px-3 py-1.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Active
                        </span>
                    @else
                        <span class="px-3 py-1.5 bg-red-100 text-red-700 text-xs font-semibold rounded-full flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            Inactive
                        </span>
                    @endif
                </div>
                <p class="text-gray-600 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                    </svg>
                    Question ID: <span class="font-semibold text-gray-800">#{{ $question->id }}</span>
                </p>
            </div>
            
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('admin.questions.edit', $question) }}" 
                   class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white rounded-lg font-semibold transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Question
                </a>
                <a href="{{ route('admin.questions.index') }}" 
                   class="flex items-center gap-2 px-5 py-2.5 bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 hover:text-gray-900 rounded-lg font-semibold transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <!-- Marks Card -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-700 text-sm font-medium mb-1">Positive Marks</p>
                    <p class="text-3xl font-bold text-green-800">+{{ $question->marks }}</p>
                </div>
                <div class="w-12 h-12 bg-green-200 rounded-lg flex items-center justify-center">
                    <svg class="w-7 h-7 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Negative Marks Card -->
        <div class="bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-xl p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-700 text-sm font-medium mb-1">Negative Marks</p>
                    <p class="text-3xl font-bold text-red-800">-{{ $question->negative_marks }}</p>
                </div>
                <div class="w-12 h-12 bg-red-200 rounded-lg flex items-center justify-center">
                    <svg class="w-7 h-7 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Difficulty Card -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-700 text-sm font-medium mb-1">Difficulty</p>
                    <p class="text-xl font-bold text-purple-800">{{ $question->difficulty->name ?? 'N/A' }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-200 rounded-lg flex items-center justify-center">
                    <svg class="w-7 h-7 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Options Count Card -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-700 text-sm font-medium mb-1">Answer Options</p>
                    <p class="text-3xl font-bold text-blue-800">{{ $question->options->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-200 rounded-lg flex items-center justify-center">
                    <svg class="w-7 h-7 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Question Metadata -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 hover:shadow-md transition-shadow">
        <h2 class="text-xl font-bold text-gray-900 mb-5 flex items-center">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            Classification & Metadata
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Exam Category -->
            <div class="bg-gradient-to-br from-gray-50 to-white p-4 rounded-lg border border-gray-200">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Exam Category</label>
                @if($question->examCategory)
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-blue-100 text-blue-800 rounded-lg text-sm font-semibold">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"></path>
                        </svg>
                        {{ $question->examCategory->name }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        Not Assigned
                    </span>
                @endif
            </div>

            <!-- Subject -->
            <div class="bg-gradient-to-br from-gray-50 to-white p-4 rounded-lg border border-gray-200">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Subject</label>
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-100 text-indigo-800 rounded-lg text-sm font-semibold">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path>
                    </svg>
                    {{ $question->subject->name ?? 'N/A' }}
                </span>
            </div>

            <!-- Difficulty -->
            <div class="bg-gradient-to-br from-gray-50 to-white p-4 rounded-lg border border-gray-200">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Difficulty Level</label>
                @if($question->difficulty)
                    @php
                        $difficultyColors = [
                            1 => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'text-green-600'],
                            2 => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'text-yellow-600'],
                            3 => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'text-red-600'],
                        ];
                        $colors = $difficultyColors[$question->difficulty->level] ?? $difficultyColors[2];
                    @endphp
                    <span class="inline-flex items-center gap-2 px-4 py-2 {{ $colors['bg'] }} {{ $colors['text'] }} rounded-lg text-sm font-semibold">
                        <svg class="w-4 h-4 {{ $colors['icon'] }}" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $question->difficulty->name }}
                    </span>
                @else
                    <span class="text-gray-500 text-sm">Not Set</span>
                @endif
            </div>

            <!-- Chapter -->
            <div class="bg-gradient-to-br from-gray-50 to-white p-4 rounded-lg border border-gray-200">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Chapter</label>
                @if($question->chapter)
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-purple-100 text-purple-800 rounded-lg text-sm font-semibold">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $question->chapter->name }}
                    </span>
                @else
                    <span class="text-gray-500 text-sm">Not Assigned</span>
                @endif
            </div>

            <!-- Topic -->
            <div class="bg-gradient-to-br from-gray-50 to-white p-4 rounded-lg border border-gray-200">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Topic</label>
                @if($question->topic)
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-pink-100 text-pink-800 rounded-lg text-sm font-semibold">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path>
                        </svg>
                        {{ $question->topic->name }}
                    </span>
                @else
                    <span class="text-gray-500 text-sm">Not Assigned</span>
                @endif
            </div>

            <!-- Created By -->
            <div class="bg-gradient-to-br from-gray-50 to-white p-4 rounded-lg border border-gray-200">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Created By</label>
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-800 rounded-lg text-sm font-semibold">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                    {{ $question->creator->name ?? 'Unknown' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Question Content -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 hover:shadow-md transition-shadow">
        <h2 class="text-xl font-bold text-gray-900 mb-5 flex items-center">
            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            Question Content
        </h2>
        
        <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-lg p-6 border-l-4 border-indigo-500">
            <div class="prose max-w-none text-gray-800 leading-relaxed">
                {!! $question->question_text !!}
            </div>

            @if($question->question_image)
                <div class="mt-6">
                    <img src="{{ asset('storage/' . $question->question_image) }}" 
                         alt="Question Image" 
                         class="max-w-full h-auto rounded-lg border-2 border-indigo-200 shadow-md">
                </div>
            @endif
        </div>
    </div>

    <!-- Answer Options -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 hover:shadow-md transition-shadow">
        <h2 class="text-xl font-bold text-gray-900 mb-5 flex items-center">
            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>
            Answer Options
        </h2>
        
        <div class="space-y-4">
            @foreach($question->options as $index => $option)
                <div class="group relative rounded-xl border-2 transition-all duration-200
                            {{ $option->is_correct 
                                ? 'border-green-400 bg-gradient-to-r from-green-50 to-emerald-50 shadow-md' 
                                : 'border-gray-200 bg-gradient-to-r from-gray-50 to-slate-50 hover:border-gray-300' }}">
                    
                    <!-- Correct Answer Indicator -->
                    @if($option->is_correct)
                        <div class="absolute -top-3 -right-3 z-10">
                            <div class="bg-green-500 text-white px-4 py-1.5 rounded-full text-xs font-bold shadow-lg flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                CORRECT
                            </div>
                        </div>
                    @endif
                    
                    <div class="p-5">
                        <div class="flex items-start gap-4">
                            <!-- Option Letter Badge -->
                            <div class="flex-shrink-0">
                                <span class="flex items-center justify-center w-11 h-11 rounded-xl font-bold text-lg shadow-sm
                                             {{ $option->is_correct 
                                                 ? 'bg-gradient-to-br from-green-500 to-green-600 text-white' 
                                                 : 'bg-gradient-to-br from-blue-500 to-blue-600 text-white' }}">
                                    {{ $option->option_key }}
                                </span>
                            </div>
                            
                            <!-- Option Content -->
                            <div class="flex-1 min-w-0">
                                <div class="prose max-w-none text-gray-800 leading-relaxed">
                                    {!! $option->option_text !!}
                                </div>

                                @if($option->option_image)
                                    <div class="mt-4">
                                        <img src="{{ asset('storage/' . $option->option_image) }}" 
                                             alt="Option {{ $option->option_key }}" 
                                             class="max-w-sm h-auto rounded-lg border-2 {{ $option->is_correct ? 'border-green-300' : 'border-gray-300' }} shadow-sm">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Correct Answer Summary -->
        <div class="mt-6 p-5 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-300 rounded-xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-green-800 mb-1">Correct Answer(s):</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($question->options->where('is_correct', true) as $correct)
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-500 text-white rounded-lg text-sm font-bold shadow-sm">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Option {{ $correct->option_key }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Explanation -->
    @if($question->explanation)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 hover:shadow-md transition-shadow">
            <h2 class="text-xl font-bold text-gray-900 mb-5 flex items-center">
                <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                Detailed Explanation
            </h2>
            
            <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-lg p-6 border-l-4 border-amber-500">
                <div class="prose max-w-none text-gray-800 leading-relaxed">
                    {!! $question->explanation !!}
                </div>

                @if($question->explanation_image)
                    <div class="mt-6">
                        <img src="{{ asset('storage/' . $question->explanation_image) }}" 
                             alt="Explanation Image" 
                             class="max-w-full h-auto rounded-lg border-2 border-amber-200 shadow-md">
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Timestamps & Metadata -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 hover:shadow-md transition-shadow">
        <h2 class="text-xl font-bold text-gray-900 mb-5 flex items-center">
            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            Timeline & Metadata
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-5 rounded-lg border border-blue-200">
                <div class="flex items-center gap-3 mb-2">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm font-semibold text-blue-700 uppercase tracking-wide">Created At</span>
                </div>
                <p class="text-gray-800 font-semibold text-lg">{{ $question->created_at->format('M d, Y') }}</p>
                <p class="text-gray-600 text-sm">{{ $question->created_at->format('h:i A') }}</p>
                <p class="text-gray-500 text-xs mt-1">{{ $question->created_at->diffForHumans() }}</p>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-5 rounded-lg border border-purple-200">
                <div class="flex items-center gap-3 mb-2">
                    <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm font-semibold text-purple-700 uppercase tracking-wide">Last Updated</span>
                </div>
                <p class="text-gray-800 font-semibold text-lg">{{ $question->updated_at->format('M d, Y') }}</p>
                <p class="text-gray-600 text-sm">{{ $question->updated_at->format('h:i A') }}</p>
                <p class="text-gray-500 text-xs mt-1">{{ $question->updated_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="sticky bottom-0 bg-white border-t-2 border-gray-200 rounded-xl shadow-lg p-6 backdrop-blur-sm bg-white/95">
        <div class="flex gap-4 justify-between items-center flex-wrap">
            <a href="{{ route('admin.questions.index') }}" 
               class="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition-colors group">
                <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Questions List
            </a>
            
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('admin.questions.edit', $question) }}" 
                   class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white rounded-lg font-semibold transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Question
                </a>
                
                <form action="{{ route('admin.questions.destroy', $question) }}" 
                      method="POST" 
                      class="inline"
                      onsubmit="return confirm('⚠️ Are you absolutely sure?\n\nThis will permanently delete:\n• The question\n• All answer options\n• Any associated data\n\nThis action CANNOT be undone!');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-lg font-semibold transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete Question
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Enhanced prose styling for rich text content */
.prose {
    color: #1f2937;
    max-width: none;
}

.prose p {
    margin-bottom: 1em;
    line-height: 1.75;
}

.prose strong {
    font-weight: 600;
    color: #111827;
}

.prose em {
    font-style: italic;
}

.prose ul, .prose ol {
    margin: 1em 0;
    padding-left: 1.5em;
}

.prose li {
    margin: 0.5em 0;
}

.prose img {
    border-radius: 0.5rem;
    margin: 1em 0;
}

.prose code {
    background-color: #f3f4f6;
    padding: 0.2em 0.4em;
    border-radius: 0.25rem;
    font-size: 0.875em;
    font-family: monospace;
}

.prose pre {
    background-color: #1f2937;
    color: #f9fafb;
    padding: 1em;
    border-radius: 0.5rem;
    overflow-x: auto;
    margin: 1em 0;
}

.prose blockquote {
    border-left: 4px solid #e5e7eb;
    padding-left: 1em;
    color: #6b7280;
    font-style: italic;
    margin: 1em 0;
}
</style>
@endsection
