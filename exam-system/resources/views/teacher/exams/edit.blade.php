@extends('layouts.teacher')

@section('title', 'Edit Exam')
@section('page-title', 'Edit Exam')
@section('page-description', 'Update exam details and configuration')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <p class="font-bold mb-2">⚠️ Please fix the following errors:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('teacher.exams.update', $exam->id) }}" method="POST" id="examForm">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6 pb-4 border-b">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Edit Exam: {{ $exam->title }}</h2>
                    <p class="text-sm text-gray-500 mt-1">Exam Code: <span class="font-mono font-semibold">{{ $exam->exam_code }}</span></p>
                </div>
                <a href="{{ route('teacher.exams.show', $exam->id) }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    ← Back to Exam
                </a>
            </div>

            <!-- Basic Information -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">📋 Basic Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Exam Title -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Exam Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title', $exam->title) }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror" 
                               placeholder="Enter exam title"
                               required>
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Exam Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Exam Category <span class="text-red-500">*</span>
                        </label>
                        <select name="exam_category_id" id="exam_category_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('exam_category_id') border-red-500 @enderror" 
                                required>
                            <option value="">Select Category</option>
                            @foreach($examCategories as $category)
                                <option value="{{ $category->id }}" {{ old('exam_category_id', $exam->exam_category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('exam_category_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Class -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Class (Optional)
                        </label>
                        <select name="school_class_id" id="school_class_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('school_class_id', $exam->school_class_id) == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Description (Optional)
                        </label>
                        <textarea name="description" id="description" rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Add exam instructions or important notes...">{{ old('description', $exam->description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Exam Configuration -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">⚙️ Exam Configuration</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Duration -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Duration (minutes) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}" 
                               min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                        @error('duration_minutes')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Total Questions -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Total Questions <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="total_questions" id="total_questions" value="{{ old('total_questions', $exam->total_questions) }}" 
                               min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-100" 
                               readonly required>
                        @error('total_questions')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Total Marks -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Total Marks <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks', $exam->total_marks) }}" 
                               min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                        @error('total_marks')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Passing Marks -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Passing Marks <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="passing_marks" id="passing_marks" value="{{ old('passing_marks', $exam->passing_marks) }}" 
                               min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                        @error('passing_marks')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Schedule -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">📅 Schedule</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Start Time -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Start Time <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="start_time" id="start_time"
                               value="{{ old('start_time', $exam->start_time->format('Y-m-d\TH:i')) }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                        @error('start_time')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- End Time -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            End Time <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="end_time" id="end_time"
                               value="{{ old('end_time', $exam->end_time->format('Y-m-d\TH:i')) }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                        @error('end_time')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Result Release Time -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Result Release Time (Optional)
                        </label>
                        <input type="datetime-local" name="result_release_time" id="result_release_time"
                               value="{{ old('result_release_time', $exam->result_release_time ? $exam->result_release_time->format('Y-m-d\TH:i') : '') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Marking Schemes -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">📊 Marking Schemes</h3>
                <div id="marking-schemes" class="space-y-4">
                    @foreach($exam->markingSchemes as $index => $scheme)
                        <div class="marking-scheme-item border border-gray-300 rounded-lg p-4 bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Subject <span class="text-red-500">*</span>
                                    </label>
                                    <select name="marking_schemes[{{ $index }}][subject_id]" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required>
                                        <option value="">Select Subject</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ $scheme->subject_id == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Correct Marks <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="marking_schemes[{{ $index }}][correct_marks]" 
                                        step="0.01" min="0" value="{{ $scheme->correct_marks }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Negative Marks <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="marking_schemes[{{ $index }}][wrong_marks]" 
                                        step="0.01" min="0" value="{{ $scheme->wrong_marks }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required>
                                </div>
                                <div class="flex items-end">
                                    @if($index > 0)
                                        <button type="button" onclick="this.closest('.marking-scheme-item').remove();" 
                                            class="w-full px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition">
                                            🗑️ Remove
                                        </button>
                                    @else
                                        <div class="w-full px-4 py-2 bg-gray-200 text-gray-500 text-center rounded-lg cursor-not-allowed">
                                            Primary Scheme
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-marking-scheme" class="mt-4 px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition">
                    ➕ Add Another Subject Scheme
                </button>
            </div>

            <!-- Question Selection -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">❓ Select Questions <span class="text-red-500">*</span></h3>
                
                <!-- Filter Section -->
                <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <h4 class="font-semibold text-gray-700 mb-3">🔍 Filter Questions</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Subject</label>
                            <select id="filter_subject" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Category</label>
                            <select id="filter_category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">All Categories</option>
                                @foreach($examCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text" id="filter_search" placeholder="Search questions..." 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Available Questions -->
                <div class="border border-gray-200 rounded-lg overflow-hidden mb-4">
                    <div class="bg-gray-100 px-4 py-3 border-b border-gray-200">
                        <span class="text-sm font-semibold text-gray-700">
                            Available Questions (Click to select/deselect)
                        </span>
                    </div>
                    
                    <div id="available-questions" class="max-h-96 overflow-y-auto">
                        @if($questions->count() > 0)
                            @foreach($questions as $question)
                            @php
                                $isSelected = in_array($question->id, old('selected_questions', $exam->questions->pluck('id')->toArray()));
                            @endphp
                            <div class="question-item p-4 border-b border-gray-200 hover:bg-gray-50 cursor-pointer transition {{ $isSelected ? 'bg-blue-50 border-l-4 border-l-blue-500' : '' }}" 
                                 data-question-id="{{ $question->id }}"
                                 data-subject-id="{{ $question->subject_id }}"
                                 data-category-id="{{ $question->exam_category_id }}">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="w-5 h-5 rounded border-2 border-blue-500 flex items-center justify-center {{ $isSelected ? 'bg-blue-500' : '' }}">
                                            <svg class="w-3 h-3 text-white {{ $isSelected ? '' : 'hidden' }} select-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <div class="text-sm text-gray-900 font-medium question-text">
                                            {!! Str::limit(strip_tags($question->question_text), 150) !!}
                                        </div>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $question->subject->name ?? 'N/A' }}
                                            </span>
                                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                                {{ $question->examCategory->name ?? 'N/A' }}
                                            </span>
                                            @if($question->difficulty_level)
                                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full 
                                                {{ $question->difficulty_level == 'easy' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $question->difficulty_level == 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $question->difficulty_level == 'hard' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ ucfirst($question->difficulty_level) }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="p-8 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="mt-2 text-sm">No questions available from your assigned subjects.</p>
                                <p class="text-xs text-gray-400 mt-1">Please contact administrator to add questions.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Selected Questions Display -->
                <div class="border border-blue-200 rounded-lg bg-blue-50 p-4">
                    <h4 class="font-semibold text-gray-800 mb-3">
                        ✅ Selected Questions: <span id="selected-count" class="text-blue-600">0</span>
                    </h4>
                    <div id="selected-questions-display" class="space-y-2">
                        <p class="text-gray-500 text-sm">No questions selected yet. Click on questions above to select them.</p>
                    </div>
                </div>

                <!-- Hidden inputs for selected questions -->
                <div id="selected-questions-inputs"></div>
                
                @error('selected_questions')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Exam Settings -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">🔧 Exam Settings</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Randomize Questions -->
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <input type="checkbox" name="randomize_questions" value="1" 
                               {{ old('randomize_questions', $exam->randomize_questions) ? 'checked' : '' }}
                               id="randomize_questions" 
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="randomize_questions" class="ml-3 text-sm text-gray-700">
                            Randomize Questions Order for Each Student
                        </label>
                    </div>

                    <!-- Randomize Options -->
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <input type="checkbox" name="randomize_options" value="1" 
                               {{ old('randomize_options', $exam->randomize_options) ? 'checked' : '' }}
                               id="randomize_options" 
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="randomize_options" class="ml-3 text-sm text-gray-700">
                            Randomize Answer Options
                        </label>
                    </div>

                    <!-- Show Results Immediately -->
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <input type="checkbox" name="show_results_immediately" value="1" 
                               {{ old('show_results_immediately', $exam->show_results_immediately) ? 'checked' : '' }}
                               id="show_results_immediately" 
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="show_results_immediately" class="ml-3 text-sm text-gray-700">
                            Show Results Immediately After Submission
                        </label>
                    </div>

                    <!-- Allow Resume -->
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <input type="checkbox" name="allow_resume" value="1" 
                               {{ old('allow_resume', $exam->allow_resume) ? 'checked' : '' }}
                               id="allow_resume" 
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="allow_resume" class="ml-3 text-sm text-gray-700">
                            Allow Students to Resume Exam
                        </label>
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center p-3 bg-green-50 rounded-lg border border-green-200">
                        <input type="checkbox" name="is_active" value="1" 
                               {{ old('is_active', $exam->is_active) ? 'checked' : '' }}
                               id="is_active" 
                               class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                        <label for="is_active" class="ml-3 text-sm text-gray-700 font-medium">
                            Exam is Active (Students can take this exam)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center pt-6 border-t">
                <a href="{{ route('teacher.exams.show', $exam->id) }}" 
                   class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition font-medium">
                    ← Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium shadow-md">
                    💾 Update Exam
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Marking scheme management
    let markingSchemeCount = {{ $exam->markingSchemes->count() }};
    
    document.getElementById('add-marking-scheme').addEventListener('click', function() {
        const container = document.getElementById('marking-schemes');
        const newScheme = `
            <div class="marking-scheme-item border border-gray-300 rounded-lg p-4 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject <span class="text-red-500">*</span></label>
                        <select name="marking_schemes[${markingSchemeCount}][subject_id]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Correct Marks <span class="text-red-500">*</span></label>
                        <input type="number" name="marking_schemes[${markingSchemeCount}][correct_marks]" step="0.01" min="0" value="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Negative Marks <span class="text-red-500">*</span></label>
                        <input type="number" name="marking_schemes[${markingSchemeCount}][wrong_marks]" step="0.01" min="0" value="0.25" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    </div>
                    <div class="flex items-end">
                        <button type="button" onclick="this.closest('.marking-scheme-item').remove();" class="w-full px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition">
                            🗑️ Remove
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newScheme);
        markingSchemeCount++;
    });

    // Question selection management
    let selectedQuestions = new Set(@json(old('selected_questions', $exam->questions->pluck('id')->toArray())));

    function updateSelectedQuestionsInputs() {
        const container = document.getElementById('selected-questions-inputs');
        container.innerHTML = '';
        
        selectedQuestions.forEach(questionId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_questions[]';
            input.value = questionId;
            container.appendChild(input);
        });
    }

    function updateSelectedCount() {
        document.getElementById('selected-count').textContent = selectedQuestions.size;
        document.getElementById('total_questions').value = selectedQuestions.size;
    }

    function updateSelectedDisplay() {
        const display = document.getElementById('selected-questions-display');
        
        if (selectedQuestions.size === 0) {
            display.innerHTML = '<p class="text-gray-500 text-sm">No questions selected yet. Click on questions above to select them.</p>';
        } else {
            display.innerHTML = '';
            selectedQuestions.forEach(questionId => {
                const questionItem = document.querySelector(`[data-question-id="${questionId}"]`);
                if (questionItem) {
                    const questionText = questionItem.querySelector('.question-text').textContent.trim();
                    const subjectInfo = questionItem.querySelector('.text-xs').textContent;
                    
                    const div = document.createElement('div');
                    div.className = 'p-3 bg-white border border-gray-200 rounded-lg flex justify-between items-start hover:shadow-sm transition';
                    div.innerHTML = `
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">${questionText}</p>
                            <p class="text-xs text-gray-600 mt-1">${subjectInfo}</p>
                        </div>
                        <button type="button" class="ml-3 text-red-600 hover:text-red-800 font-bold" onclick="removeQuestion(${questionId})">
                            ✕
                        </button>
                    `;
                    display.appendChild(div);
                }
            });
        }
    }

    window.removeQuestion = function(questionId) {
        selectedQuestions.delete(questionId);
        const questionItem = document.querySelector(`[data-question-id="${questionId}"]`);
        if (questionItem) {
            questionItem.classList.remove('bg-blue-50', 'border-l-4', 'border-l-blue-500');
            const checkbox = questionItem.querySelector('.w-5');
            checkbox.classList.remove('bg-blue-500');
            const indicator = questionItem.querySelector('.select-indicator');
            indicator.classList.add('hidden');
        }
        updateSelectedCount();
        updateSelectedDisplay();
        updateSelectedQuestionsInputs();
    }

    // Question item click handlers
    document.querySelectorAll('.question-item').forEach(item => {
        item.addEventListener('click', function() {
            const questionId = parseInt(this.dataset.questionId);
            const checkbox = this.querySelector('.w-5');
            const indicator = this.querySelector('.select-indicator');
            
            if (selectedQuestions.has(questionId)) {
                selectedQuestions.delete(questionId);
                this.classList.remove('bg-blue-50', 'border-l-4', 'border-l-blue-500');
                checkbox.classList.remove('bg-blue-500');
                indicator.classList.add('hidden');
            } else {
                selectedQuestions.add(questionId);
                this.classList.add('bg-blue-50', 'border-l-4', 'border-l-blue-500');
                checkbox.classList.add('bg-blue-500');
                indicator.classList.remove('hidden');
            }
            
            updateSelectedCount();
            updateSelectedDisplay();
            updateSelectedQuestionsInputs();
        });
    });

    // Filter functionality
    document.getElementById('filter_subject').addEventListener('change', filterQuestions);
    document.getElementById('filter_category').addEventListener('change', filterQuestions);
    document.getElementById('filter_search').addEventListener('input', filterQuestions);

    function filterQuestions() {
        const subjectId = document.getElementById('filter_subject').value;
        const categoryId = document.getElementById('filter_category').value;
        const searchTerm = document.getElementById('filter_search').value.toLowerCase();

        document.querySelectorAll('.question-item').forEach(item => {
            const itemSubjectId = item.dataset.subjectId;
            const itemCategoryId = item.dataset.categoryId;
            const questionText = item.querySelector('.question-text').textContent.toLowerCase();

            const matchesSubject = !subjectId || itemSubjectId === subjectId;
            const matchesCategory = !categoryId || itemCategoryId === categoryId;
            const matchesSearch = !searchTerm || questionText.includes(searchTerm);

            if (matchesSubject && matchesCategory && matchesSearch) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Form validation
    document.getElementById('examForm').addEventListener('submit', function(e) {
        if (selectedQuestions.size === 0) {
            e.preventDefault();
            alert('⚠️ Please select at least one question for the exam.');
            return false;
        }

        const totalMarks = parseFloat(document.getElementById('total_marks').value);
        const passingMarks = parseFloat(document.getElementById('passing_marks').value);
        
        if (passingMarks > totalMarks) {
            e.preventDefault();
            alert('⚠️ Passing marks cannot be greater than total marks.');
            return false;
        }

        const startTime = new Date(document.getElementById('start_time').value);
        const endTime = new Date(document.getElementById('end_time').value);
        
        if (endTime <= startTime) {
            e.preventDefault();
            alert('⚠️ End time must be after start time.');
            return false;
        }
    });

    // Initialize on page load
    updateSelectedDisplay();
    updateSelectedQuestionsInputs();
    updateSelectedCount();
});
</script>
@endpush
@endsection
