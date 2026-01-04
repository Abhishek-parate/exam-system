@extends('layouts.admin')

@section('title', 'Add New Question')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Add New Question</h1>
            <p class="text-gray-600 text-sm">Create a comprehensive question with multiple choice answers</p>
        </div>
        <a href="{{ route('admin.questions.index') }}" 
           class="flex items-center gap-2 px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Questions
        </a>
    </div>

    <!-- Error Alert -->
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 flex items-start gap-3">
            <svg class="w-6 h-6 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <p class="font-semibold">Error</p>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.questions.store') }}" enctype="multipart/form-data" id="questionForm">
        @csrf

        <!-- Section 1: Question Classification -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 hover:shadow-md transition-shadow">
            <h2 class="text-lg font-semibold text-gray-900 mb-5 flex items-center">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
                Question Classification
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Exam Category -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Exam Category
                        <span class="text-xs font-normal text-gray-500 ml-1">(Optional)</span>
                    </label>
                    <div class="relative">
                        <select name="exam_category_id" id="exam_category_id" 
                                class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg 
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                       transition duration-200 appearance-none bg-white hover:border-gray-400 cursor-pointer">
                            <option value="">Select Category</option>
                            @foreach($examCategories as $category)
                                <option value="{{ $category->id }}" {{ old('exam_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    @error('exam_category_id')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Subject -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="subject_id" id="subject_id" required 
                                class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg 
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                       transition duration-200 appearance-none bg-white hover:border-gray-400 cursor-pointer">
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    @error('subject_id')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Chapter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Chapter
                        <span class="text-xs font-normal text-gray-500 ml-1">(Optional)</span>
                    </label>
                    <div class="relative">
                        <select name="chapter_id" id="chapter_id" 
                                class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg 
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                       transition duration-200 appearance-none bg-white hover:border-gray-400 cursor-pointer">
                            <option value="">Select Chapter</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Topic -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Topic
                        <span class="text-xs font-normal text-gray-500 ml-1">(Optional)</span>
                    </label>
                    <div class="relative">
                        <select name="topic_id" id="topic_id" 
                                class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg 
                                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                       transition duration-200 appearance-none bg-white hover:border-gray-400 cursor-pointer">
                            <option value="">Select Topic</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Question Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 hover:shadow-md transition-shadow">
            <h2 class="text-lg font-semibold text-gray-900 mb-5 flex items-center">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                Question Details
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Difficulty -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Difficulty Level <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="difficulty_id" required 
                                class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg 
                                       focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent
                                       transition duration-200 appearance-none bg-white hover:border-gray-400 cursor-pointer">
                            <option value="">Select Difficulty</option>
                            @foreach($difficulties as $difficulty)
                                <option value="{{ $difficulty->id }}" {{ old('difficulty_id') == $difficulty->id ? 'selected' : '' }}>
                                    {{ $difficulty->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    @error('difficulty_id')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <!-- Marks -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Marks <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-green-600 font-bold text-lg">+</span>
                        <input type="number" name="marks" step="0.01" min="0" value="{{ old('marks', 1) }}" required 
                               class="w-full pl-9 pr-4 py-3 border border-gray-300 rounded-lg 
                                      focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent
                                      transition duration-200 hover:border-gray-400">
                    </div>
                    @error('marks')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                
                <!-- Negative Marks -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Negative Marks <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-red-600 font-bold text-lg">-</span>
                        <input type="number" name="negative_marks" step="0.01" min="0" value="{{ old('negative_marks', 0) }}" required 
                               class="w-full pl-9 pr-4 py-3 border border-gray-300 rounded-lg 
                                      focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent
                                      transition duration-200 hover:border-gray-400">
                    </div>
                    @error('negative_marks')
                        <p class="text-red-500 text-xs mt-1.5 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Section 3: Question Content -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 hover:shadow-md transition-shadow">
            <h2 class="text-lg font-semibold text-gray-900 mb-5 flex items-center">
                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                Question Content
            </h2>
            
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    Question Text <span class="text-red-500">*</span>
                </label>
                <div class="border-2 border-gray-200 rounded-lg overflow-hidden hover:border-indigo-300 transition-colors duration-200 shadow-sm">
                    <div id="question_editor" class="bg-white" style="min-height: 300px;"></div>
                </div>
                <input type="hidden" name="question_text" id="question_text" required>
                <p class="text-xs text-gray-500 mt-2 flex items-center">
                    <svg class="w-4 h-4 mr-1.5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Format text, add images, formulas, and more using the toolbar above
                </p>
                @error('question_text')
                    <p class="text-red-500 text-xs mt-1.5 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Optional Question Image -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Additional Question Image
                    <span class="text-xs font-normal text-gray-500 ml-1">(Optional)</span>
                </label>
                <div class="relative">
                    <input type="file" name="question_image" accept="image/*" id="question_image"
                           class="block w-full text-sm text-gray-500
                                  file:mr-4 file:py-3 file:px-6
                                  file:rounded-lg file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-indigo-50 file:text-indigo-700
                                  hover:file:bg-indigo-100
                                  file:cursor-pointer cursor-pointer
                                  border border-gray-300 rounded-lg
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <p class="text-xs text-gray-500 mt-1.5">You can also insert images directly in the editor above</p>
                @error('question_image')
                    <p class="text-red-500 text-xs mt-1.5 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        <!-- Section 4: Answer Options - 2x2 Grid with Radio Buttons -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-center mb-5">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    Answer Options
                </h2>
                <span class="text-xs text-white bg-green-600 px-3 py-1.5 rounded-full font-semibold">
                    Select ONLY ONE correct answer
                </span>
            </div>
            
            <!-- 2x2 Grid Layout for Options -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @for($i = 0; $i < 4; $i++)
                <div class="border-2 border-gray-200 rounded-xl p-5 bg-gradient-to-br from-gray-50 to-white hover:border-green-300 hover:shadow-md transition-all duration-200">
                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center">
                            <span class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-white font-bold text-lg mr-3 shadow-md">
                                {{ chr(65 + $i) }}
                            </span>
                            <span class="font-semibold text-gray-700 text-lg">Option {{ chr(65 + $i) }}</span>
                        </div>
                        
                        <!-- Radio Button for Single Selection -->
                        <label class="relative inline-flex items-center cursor-pointer group">
                            <input type="radio" name="correct_answer" value="{{ $i }}" class="w-6 h-6 text-green-600 bg-gray-100 border-gray-300 focus:ring-green-500 focus:ring-2 cursor-pointer" id="correct_{{ $i }}">
                            <span class="ml-3 text-sm font-semibold text-gray-700 group-hover:text-green-700 transition-colors">
                                Mark as Correct
                            </span>
                        </label>
                    </div>
                    
                    <div class="border-2 border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm hover:border-gray-300 transition-colors">
                        <div id="option_editor_{{ $i }}" style="min-height: 180px;"></div>
                    </div>
                    <input type="hidden" name="options[{{ $i }}][text]" id="option_text_{{ $i }}" required>
                    <input type="hidden" name="options[{{ $i }}][is_correct]" id="is_correct_{{ $i }}" value="0">
                </div>
                @endfor
            </div>

            <div class="mt-6 p-4 bg-green-50 border-2 border-green-300 rounded-lg">
                <p class="text-sm text-green-800 flex items-start font-medium">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <span><strong>Important:</strong> You must select exactly ONE correct answer using the radio button. Only one option can be marked as correct.</span>
                </p>
            </div>
        </div>

        <!-- Section 5: Explanation -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 hover:shadow-md transition-shadow">
            <h2 class="text-lg font-semibold text-gray-900 mb-5 flex items-center">
                <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                Explanation
                <span class="ml-3 px-2.5 py-1 text-xs font-semibold bg-gray-100 text-gray-600 rounded-full">Optional</span>
            </h2>
            
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-3">
                    Detailed Explanation
                    <span class="text-xs font-normal text-gray-500 ml-1">(Help students understand the correct answer)</span>
                </label>
                <div class="border-2 border-gray-200 rounded-lg overflow-hidden hover:border-amber-300 transition-colors duration-200 shadow-sm">
                    <div id="explanation_editor" class="bg-white" style="min-height: 200px;"></div>
                </div>
                <input type="hidden" name="explanation" id="explanation">
            </div>

            <!-- Optional Explanation Image -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Explanation Image
                    <span class="text-xs font-normal text-gray-500 ml-1">(Optional)</span>
                </label>
                <div class="relative">
                    <input type="file" name="explanation_image" accept="image/*" id="explanation_image"
                           class="block w-full text-sm text-gray-500
                                  file:mr-4 file:py-3 file:px-6
                                  file:rounded-lg file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-amber-50 file:text-amber-700
                                  hover:file:bg-amber-100
                                  file:cursor-pointer cursor-pointer
                                  border border-gray-300 rounded-lg
                                  focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                </div>
                @error('explanation_image')
                    <p class="text-red-500 text-xs mt-1.5 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        <!-- Sticky Action Bar -->
        <div class="sticky bottom-0 bg-white border-t-2 border-gray-200 rounded-xl shadow-lg p-6 mt-8 backdrop-blur-sm bg-white/95">
            <div class="flex gap-4 justify-between items-center flex-wrap">
                <a href="{{ route('admin.questions.index') }}" 
                   class="text-gray-600 hover:text-gray-900 font-medium flex items-center transition-colors group">
                    <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Cancel & Go Back
                </a>
                
                <div class="flex gap-3">
                    <button type="submit" 
                            class="px-8 py-3.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 
                                   text-white font-semibold rounded-lg transition-all duration-200
                                   shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 active:translate-y-0
                                   flex items-center gap-2.5 group">
                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Save & Publish Question
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<style>
/* Enhanced Quill Editor Styling */
.ql-toolbar {
    border-top-left-radius: 0.5rem;
    border-top-right-radius: 0.5rem;
    background: #f9fafb;
    border-bottom: 2px solid #e5e7eb;
}

.ql-container {
    font-family: system-ui, -apple-system, sans-serif;
    border-bottom-left-radius: 0.5rem;
    border-bottom-right-radius: 0.5rem;
}

.ql-editor {
    color: #1f2937;
    font-size: 0.95rem;
    line-height: 1.7;
    padding: 1rem;
    min-height: inherit;
}

.ql-editor.ql-blank::before {
    color: #9ca3af;
    font-style: italic;
}

.ql-snow .ql-stroke {
    stroke: #4b5563;
}

.ql-snow .ql-fill {
    fill: #4b5563;
}

.ql-toolbar button:hover,
.ql-toolbar button:focus {
    background: #dbeafe !important;
    border-radius: 0.375rem;
}

.ql-toolbar button.ql-active {
    background: #bfdbfe !important;
    border-radius: 0.375rem;
}

.ql-snow.ql-toolbar button:hover .ql-stroke,
.ql-snow .ql-toolbar button:hover .ql-fill,
.ql-snow.ql-toolbar button.ql-active .ql-stroke,
.ql-snow .ql-toolbar button.ql-active .ql-fill {
    stroke: #2563eb;
    fill: #2563eb;
}

/* Loading state */
.ql-toolbar.disabled {
    pointer-events: none;
    opacity: 0.5;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ============================================
    // Initialize Quill Editors
    // ============================================
    
    // Quill for Question Text
    var questionQuill = new Quill('#question_editor', {
        theme: 'snow',
        placeholder: 'Type your question here... You can format text, add images, formulas, and more.',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'script': 'sub'}, { 'script': 'super' }],
                ['blockquote', 'code-block'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'align': [] }],
                ['link', 'image'],
                ['clean']
            ]
        }
    });

    // Quill for Options
    var optionQuills = [];
    for (let i = 0; i < 4; i++) {
        optionQuills[i] = new Quill('#option_editor_' + i, {
            theme: 'snow',
            placeholder: 'Enter option ' + String.fromCharCode(65 + i) + ' content...',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'script': 'sub'}, { 'script': 'super' }],
                    ['link', 'image'],
                    ['clean']
                ]
            }
        });
    }

    // Quill for Explanation
    var explanationQuill = new Quill('#explanation_editor', {
        theme: 'snow',
        placeholder: 'Explain why the correct answer is right and help students understand the concept...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'color': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link', 'image'],
                ['clean']
            ]
        }
    });

    // ============================================
    // Radio Button Handler for Single Selection
    // ============================================
    
    const radioButtons = document.querySelectorAll('input[name="correct_answer"]');
    radioButtons.forEach((radio, index) => {
        radio.addEventListener('change', function() {
            // Reset all is_correct values
            for (let i = 0; i < 4; i++) {
                document.getElementById('is_correct_' + i).value = '0';
            }
            // Set the selected one to 1
            if (this.checked) {
                document.getElementById('is_correct_' + this.value).value = '1';
            }
        });
    });

    // ============================================
    // Helper Functions
    // ============================================
    
    function hasContent(quill) {
        var text = quill.getText().trim();
        var html = quill.root.innerHTML.trim();
        var hasText = text.length > 0;
        var hasImage = html.includes('<img') || html.includes('<iframe') || html.includes('<video');
        return hasText || hasImage;
    }

    function showError(message) {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-lg z-50 max-w-md animate-slide-in';
        toast.innerHTML = `
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <p class="font-semibold">Validation Error</p>
                    <p class="text-sm">${message}</p>
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }

    // ============================================
    // Form Submission & Validation
    // ============================================
    
    document.getElementById('questionForm').addEventListener('submit', function(e) {
        // Validate Question
        var questionHTML = questionQuill.root.innerHTML;
        document.getElementById('question_text').value = questionHTML;

        if (!hasContent(questionQuill)) {
            e.preventDefault();
            showError('Please enter question text or insert an image');
            questionQuill.focus();
            return false;
        }

        // Validate Options
        for (let i = 0; i < 4; i++) {
            var optionHTML = optionQuills[i].root.innerHTML;
            document.getElementById('option_text_' + i).value = optionHTML;

            if (!hasContent(optionQuills[i])) {
                e.preventDefault();
                showError('Please enter text or insert an image for Option ' + String.fromCharCode(65 + i));
                optionQuills[i].focus();
                return false;
            }
        }

        // Get explanation (optional)
        var explanationHTML = explanationQuill.root.innerHTML;
        document.getElementById('explanation').value = explanationHTML;

        // Validate exactly one correct answer selected
        var selectedRadio = document.querySelector('input[name="correct_answer"]:checked');
        if (!selectedRadio) {
            e.preventDefault();
            showError('Please select exactly ONE correct answer using the radio button');
            document.getElementById('correct_0').scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Saving Question...
        `;

        return true;
    });

    // ============================================
    // Dynamic Dropdowns with AJAX
    // ============================================
    
    const categorySelect = document.getElementById('exam_category_id');
    const subjectSelect = document.getElementById('subject_id');
    const chapterSelect = document.getElementById('chapter_id');
    const topicSelect = document.getElementById('topic_id');
    
    const allSubjects = @json($subjects);
    
    // Category change
    categorySelect.addEventListener('change', function() {
        const categoryId = this.value;
        
        if (categoryId) {
            subjectSelect.innerHTML = '<option value="">Loading...</option>';
            
            fetch(`/admin/questions/subjects/${categoryId}`)
                .then(response => response.json())
                .then(subjects => {
                    subjectSelect.innerHTML = '<option value="">Select Subject</option>';
                    subjects.forEach(subject => {
                        subjectSelect.innerHTML += `<option value="${subject.id}">${subject.name}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    loadAllSubjects();
                });
        } else {
            loadAllSubjects();
        }
        
        chapterSelect.innerHTML = '<option value="">Select Chapter</option>';
        topicSelect.innerHTML = '<option value="">Select Topic</option>';
    });
    
    function loadAllSubjects() {
        subjectSelect.innerHTML = '<option value="">Select Subject</option>';
        allSubjects.forEach(subject => {
            subjectSelect.innerHTML += `<option value="${subject.id}">${subject.name}</option>`;
        });
    }
    
    // Subject change
    subjectSelect.addEventListener('change', function() {
        const subjectId = this.value;
        
        if (subjectId) {
            chapterSelect.innerHTML = '<option value="">Loading...</option>';
            
            fetch(`/admin/questions/chapters/${subjectId}`)
                .then(response => response.json())
                .then(chapters => {
                    chapterSelect.innerHTML = '<option value="">Select Chapter</option>';
                    chapters.forEach(chapter => {
                        chapterSelect.innerHTML += `<option value="${chapter.id}">${chapter.name}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    chapterSelect.innerHTML = '<option value="">Error loading chapters</option>';
                });
        } else {
            chapterSelect.innerHTML = '<option value="">Select Chapter</option>';
        }
        
        topicSelect.innerHTML = '<option value="">Select Topic</option>';
    });
    
    // Chapter change
    chapterSelect.addEventListener('change', function() {
        const chapterId = this.value;
        
        if (chapterId) {
            topicSelect.innerHTML = '<option value="">Loading...</option>';
            
            fetch(`/admin/questions/topics/${chapterId}`)
                .then(response => response.json())
                .then(topics => {
                    topicSelect.innerHTML = '<option value="">Select Topic</option>';
                    topics.forEach(topic => {
                        topicSelect.innerHTML += `<option value="${topic.id}">${topic.name}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    topicSelect.innerHTML = '<option value="">Error loading topics</option>';
                });
        } else {
            topicSelect.innerHTML = '<option value="">Select Topic</option>';
        }
    });
});
</script>
@endpush
