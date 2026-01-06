@extends('layouts.admin')

@section('title', 'Create New Exam')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Create New Exam</h1>
        <a href="{{ route('admin.exams.index') }}" class="text-blue-600 hover:text-blue-800">
            ← Back to Exams
        </a>
    </div>

    <!-- ✅ Display ALL Validation Errors -->
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <h4 class="font-bold mb-2">❌ Please fix the following errors:</h4>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <strong>Error:</strong> {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- DEBUG: Teacher Subjects (Remove after testing) -->
    <div class="bg-yellow-50 border border-yellow-300 p-4 mb-4 rounded text-sm">
        <h4 class="font-bold mb-2">🔍 Debug: Teacher Subjects</h4>
        @foreach($teachers as $teacher)
            <div class="mb-1">
                <strong>{{ $teacher->user->name ?? 'Unknown' }}</strong> (Teacher ID: {{ $teacher->id }}):
                @php
                    $assignedSubjects = collect([]);
                    try {
                        if (isset($teacher->subjects) && is_object($teacher->subjects) && method_exists($teacher->subjects, 'count')) {
                            $assignedSubjects = $teacher->subjects;
                        }
                    } catch (\Exception $e) {
                        echo '<span class="text-red-600">⚠️ Exception: ' . htmlspecialchars($e->getMessage()) . '</span>';
                    }
                @endphp
                @if($assignedSubjects->count() > 0)
                    <span class="text-green-600">
                        ✓ {{ $assignedSubjects->pluck('name')->implode(', ') }}
                        (IDs: {{ $assignedSubjects->pluck('id')->implode(', ') }})
                    </span>
                @else
                    <span class="text-red-600">❌ No subjects assigned</span>
                @endif
            </div>
        @endforeach
    </div>

    <!-- ✅ UPDATED: Added onsubmit validation -->
    <form action="{{ route('admin.exams.store') }}" method="POST" class="bg-white shadow-md rounded-lg p-6" onsubmit="return validateForm()">
        @csrf
        <input type="hidden" name="debug" value="1">

        <!-- Exam Title -->
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                Exam Title <span class="text-red-500">*</span>
            </label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror" 
                placeholder="Enter exam title" required>
            @error('title')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Exam Category -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="exam_category_id">
                    Exam Category <span class="text-red-500">*</span>
                </label>
                <select name="exam_category_id" id="exam_category_id" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('exam_category_id') border-red-500 @enderror" required>
                    <option value="">Select Category</option>
                    @foreach($examCategories as $category)
                        <option value="{{ $category->id }}" {{ old('exam_category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('exam_category_id')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Class Selection -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="school_class_id">
                    Class
                </label>
                <select name="school_class_id" id="school_class_id" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Class (Optional)</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('school_class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }} {{ $class->section }}
                        </option>
                    @endforeach
                </select>
                @error('school_class_id')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Assign Teacher -->
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="assigned_teacher_id">
                Assign Teacher <span class="text-red-500">*</span>
            </label>
            <select name="assigned_teacher_id" id="assigned_teacher_id" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('assigned_teacher_id') border-red-500 @enderror" 
                required onchange="loadTeacherQuestions()">
                <option value="">-- Select Teacher --</option>
                @foreach($teachers as $teacher)
                    @php
                        $teacherAssignedSubjects = collect([]);
                        try {
                            if (isset($teacher->subjects) && is_object($teacher->subjects) && method_exists($teacher->subjects, 'count')) {
                                $teacherAssignedSubjects = $teacher->subjects;
                            }
                        } catch (\Exception $e) {
                            // Silently fail
                        }
                    @endphp
                    <option value="{{ $teacher->id }}" {{ old('assigned_teacher_id') == $teacher->id ? 'selected' : '' }}>
                        {{ $teacher->user->name ?? 'Unknown' }} 
                        @if($teacher->employee_code ?? false)
                            ({{ $teacher->employee_code }})
                        @endif
                        @if($teacherAssignedSubjects->count() > 0)
                            - {{ $teacherAssignedSubjects->pluck('name')->implode(', ') }}
                        @endif
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">Only questions from teacher's assigned subjects will be shown</p>
            @error('assigned_teacher_id')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                Description
            </label>
            <textarea name="description" id="description" rows="3" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" 
                placeholder="Exam instructions and details...">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Duration -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="duration_minutes">
                    Duration (Minutes) <span class="text-red-500">*</span>
                </label>
                <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', 60) }}" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('duration_minutes') border-red-500 @enderror" 
                    placeholder="60" required min="1">
                @error('duration_minutes')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Total Questions -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="total_questions">
                    Total Questions <span class="text-red-500">*</span>
                </label>
                <input type="number" name="total_questions" id="total_questions" value="{{ old('total_questions', 100) }}" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('total_questions') border-red-500 @enderror" 
                    placeholder="100" required min="1">
                @error('total_questions')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- 🎯 ADDED: Total Marks & Passing Marks -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Total Marks -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="total_marks">
                    Total Marks <span class="text-red-500">*</span>
                </label>
                <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks', 100) }}" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('total_marks') border-red-500 @enderror" 
                    placeholder="100" required min="0" step="0.01">
                <p class="text-xs text-gray-500 mt-1">Maximum marks for this exam</p>
                @error('total_marks')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Passing Marks -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="passing_marks">
                    Passing Marks <span class="text-red-500">*</span>
                </label>
                <input type="number" name="passing_marks" id="passing_marks" value="{{ old('passing_marks', 40) }}" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('passing_marks') border-red-500 @enderror" 
                    placeholder="40" required min="0" step="0.01">
                <p class="text-xs text-gray-500 mt-1">Minimum marks required to pass</p>
                @error('passing_marks')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Start Time -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="start_time">
                    Start Time <span class="text-red-500">*</span>
                </label>
                <input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time') }}" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('start_time') border-red-500 @enderror" required>
                @error('start_time')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- End Time -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="end_time">
                    End Time <span class="text-red-500">*</span>
                </label>
                <input type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time') }}" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('end_time') border-red-500 @enderror" required>
                @error('end_time')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Result Release Time -->
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="result_release_time">
                Result Release Time <span class="text-gray-500 text-xs">(Optional)</span>
            </label>
            <input type="datetime-local" name="result_release_time" id="result_release_time" value="{{ old('result_release_time') }}" 
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
            <p class="text-xs text-gray-500 mt-1">Leave blank to use "Show results immediately" setting below</p>
            @error('result_release_time')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Checkboxes -->
        <div class="mb-6 space-y-3">
            <label class="flex items-center">
                <input type="checkbox" name="show_results_immediately" value="1" class="form-checkbox h-5 w-5 text-blue-600" {{ old('show_results_immediately', true) ? 'checked' : '' }}>
                <span class="ml-2 text-gray-700">Show results immediately after submission</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="randomize_questions" value="1" class="form-checkbox h-5 w-5 text-blue-600" {{ old('randomize_questions') ? 'checked' : '' }}>
                <span class="ml-2 text-gray-700">Randomize question order</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="randomize_options" value="1" class="form-checkbox h-5 w-5 text-blue-600" {{ old('randomize_options') ? 'checked' : '' }}>
                <span class="ml-2 text-gray-700">Randomize answer options</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="allow_resume" value="1" class="form-checkbox h-5 w-5 text-blue-600" {{ old('allow_resume', true) ? 'checked' : '' }}>
                <span class="ml-2 text-gray-700">Allow students to resume exam</span>
            </label>
        </div>

        <!-- Marking Scheme -->
        <div class="mb-6">
            <h3 class="text-lg font-bold mb-4">Marking Scheme <span class="text-gray-500 text-sm font-normal">(Applied to all subjects)</span></h3>
            <div class="border rounded p-4 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold mb-2" for="marking_schemes[0][correct_marks]">
                            Marks for Correct Answer <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="marking_schemes[0][correct_marks]" id="correct_marks" step="0.01" min="0" 
                            value="{{ old('marking_schemes.0.correct_marks', 1) }}" 
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('marking_schemes.0.correct_marks') border-red-500 @enderror" required>
                        @error('marking_schemes.0.correct_marks')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-2" for="marking_schemes[0][wrong_marks]">
                            Marks Deducted for Wrong Answer <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="marking_schemes[0][wrong_marks]" id="wrong_marks" step="0.01" min="0" 
                            value="{{ old('marking_schemes.0.wrong_marks', 0.25) }}" 
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('marking_schemes.0.wrong_marks') border-red-500 @enderror" required>
                        <p class="text-xs text-gray-500 mt-1">Enter 0 for no negative marking</p>
                        @error('marking_schemes.0.wrong_marks')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Select Questions (AJAX Loaded) -->
        <div class="mb-6">
            <h3 class="text-lg font-bold mb-4">Select Questions <span class="text-red-500">*</span></h3>
            <div id="questions-info" class="mb-2 text-sm"></div>
            @error('selected_questions')
                <p class="text-red-500 text-xs italic mb-2">{{ $message }}</p>
            @enderror
            <div class="border rounded p-4 max-h-96 overflow-y-auto" id="questions-container">
                <div class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="mt-2">Please select a teacher first to see available questions</p>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between border-t pt-6">
            <a href="{{ route('admin.exams.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                Cancel
            </a>
            <button type="submit" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg focus:outline-none focus:shadow-outline transition">
                Create Exam
            </button>
        </div>
    </form>
</div>

<script>
console.log('🔧 Script loaded');

// 🎯 AJAX function to load teacher questions
function loadTeacherQuestions() {
    const teacherSelect = document.getElementById('assigned_teacher_id');
    const questionsContainer = document.getElementById('questions-container');
    const questionsInfo = document.getElementById('questions-info');
    const teacherId = teacherSelect.value;
    
    console.log('📝 Selected Teacher ID:', teacherId);
    
    if (!teacherId) {
        questionsContainer.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="mt-2">Please select a teacher first to see available questions</p>
            </div>
        `;
        questionsInfo.innerHTML = '';
        return;
    }
    
    // Show loading
    questionsContainer.innerHTML = `
        <div class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-3 text-gray-600">Loading questions...</span>
        </div>
    `;
    questionsInfo.innerHTML = '<span class="text-blue-600">⏳ Loading...</span>';
    
    // Fetch teacher questions via AJAX
    const url = `/admin/exams/teacher-questions?teacher_id=${teacherId}`;
    console.log('🌐 Fetching from:', url);
    
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => {
        console.log('📡 Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('✅ Response data:', data);
        
        if (!data.success) {
            throw new Error(data.message || 'Failed to load questions');
        }
        
        if (data.questions && data.questions.length > 0) {
            questionsInfo.innerHTML = `<span class="text-green-600">✓ Found ${data.questions_count} question(s) from: ${data.subjects.map(s => s.name).join(', ')}</span>`;
            
            let html = '<div class="space-y-3">';
            data.questions.forEach((question, index) => {
                html += `
                    <div class="p-3 border rounded hover:bg-gray-50">
                        <label class="flex items-start cursor-pointer">
                            <input type="checkbox" name="selected_questions[]" value="${question.id}" 
                                class="form-checkbox h-5 w-5 text-blue-600 mt-1 question-checkbox">
                            <div class="ml-3 flex-1">
                                <div class="font-medium text-gray-900">${question.question_text}</div>
                                <div class="text-sm text-gray-500 mt-1">
                                    <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs mr-2">${question.subject.name}</span>
                                    <span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded text-xs">${question.exam_category.name}</span>
                                </div>
                            </div>
                        </label>
                    </div>
                `;
            });
            html += '</div>';
            questionsContainer.innerHTML = html;
        } else {
            questionsInfo.innerHTML = '<span class="text-orange-600">⚠️ No questions available</span>';
            questionsContainer.innerHTML = `
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="mt-2 text-gray-500">⚠️ No questions available for this teacher's subjects</p>
                    <p class="text-sm text-gray-400 mt-1">Please add questions first or assign subjects to this teacher</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('❌ Error:', error);
        questionsInfo.innerHTML = '<span class="text-red-600">❌ Error loading questions</span>';
        questionsContainer.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded p-4">
                <p class="text-red-700 font-medium">❌ Error loading questions</p>
                <p class="text-red-600 text-sm mt-1">${error.message}</p>
                <p class="text-gray-600 text-sm mt-2">Please check:</p>
                <ul class="list-disc list-inside text-gray-600 text-sm mt-1">
                    <li>The route is properly configured</li>
                    <li>The controller method exists</li>
                    <li>Check browser console for more details</li>
                </ul>
            </div>
        `;
    });
}

// ✅ Form validation function
function validateForm() {
    console.log('=== FORM VALIDATION START ===');
    
    // Check title
    const title = document.getElementById('title').value.trim();
    if (!title) {
        alert('❌ Exam title is required');
        document.getElementById('title').focus();
        return false;
    }
    console.log('✓ Title:', title);
    
    // Check category
    const category = document.getElementById('exam_category_id').value;
    if (!category) {
        alert('❌ Exam category is required');
        document.getElementById('exam_category_id').focus();
        return false;
    }
    console.log('✓ Category:', category);
    
    // Check teacher
    const teacher = document.getElementById('assigned_teacher_id').value;
    if (!teacher) {
        alert('❌ Teacher selection is required');
        document.getElementById('assigned_teacher_id').focus();
        return false;
    }
    console.log('✓ Teacher:', teacher);
    
    // Check times
    const startTime = document.getElementById('start_time').value;
    const endTime = document.getElementById('end_time').value;
    if (!startTime || !endTime) {
        alert('❌ Start time and End time are required!');
        if (!startTime) document.getElementById('start_time').focus();
        else document.getElementById('end_time').focus();
        return false;
    }
    
    // Validate end time is after start time
    if (new Date(endTime) <= new Date(startTime)) {
        alert('❌ End time must be after start time!');
        document.getElementById('end_time').focus();
        return false;
    }
    console.log('✓ Start Time:', startTime);
    console.log('✓ End Time:', endTime);
    
    // Check total marks
    const totalMarks = parseFloat(document.getElementById('total_marks').value);
    if (isNaN(totalMarks) || totalMarks <= 0) {
        alert('❌ Total marks must be greater than 0');
        document.getElementById('total_marks').focus();
        return false;
    }
    console.log('✓ Total Marks:', totalMarks);
    
    // Check passing marks
    const passingMarks = parseFloat(document.getElementById('passing_marks').value);
    if (isNaN(passingMarks) || passingMarks < 0) {
        alert('❌ Passing marks cannot be negative');
        document.getElementById('passing_marks').focus();
        return false;
    }
    if (passingMarks > totalMarks) {
        alert('❌ Passing marks cannot be greater than total marks');
        document.getElementById('passing_marks').focus();
        return false;
    }
    console.log('✓ Passing Marks:', passingMarks);
    
    // Check questions
    const checkedQuestions = document.querySelectorAll('.question-checkbox:checked');
    if (checkedQuestions.length === 0) {
        alert('❌ Please select at least one question!');
        document.getElementById('questions-container').scrollIntoView({ behavior: 'smooth' });
        return false;
    }
    console.log('✓ Selected Questions:', checkedQuestions.length);
    
    // Check marks
    const correctMarks = parseFloat(document.getElementById('correct_marks').value);
    const wrongMarks = parseFloat(document.getElementById('wrong_marks').value);
    if (isNaN(correctMarks) || correctMarks <= 0) {
        alert('❌ Marks for correct answer must be greater than 0');
        document.getElementById('correct_marks').focus();
        return false;
    }
    if (isNaN(wrongMarks) || wrongMarks < 0) {
        alert('❌ Marks for wrong answer cannot be negative');
        document.getElementById('wrong_marks').focus();
        return false;
    }
    console.log('✓ Correct Marks:', correctMarks);
    console.log('✓ Wrong Marks:', wrongMarks);
    
    console.log('=== ✅ FORM VALIDATION PASSED ===');
    console.log('📤 Submitting form...');
    return true;
}

// Load on page ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM Content Loaded');
    
    const teacherSelect = document.getElementById('assigned_teacher_id');
    
    // Load questions if teacher is already selected (e.g., after validation error)
    if (teacherSelect && teacherSelect.value) {
        console.log('🔄 Auto-loading questions for pre-selected teacher');
        loadTeacherQuestions();
    }
});
</script>
@endsection
