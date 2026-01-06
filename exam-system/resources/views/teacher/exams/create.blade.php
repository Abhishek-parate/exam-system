@extends('layouts.teacher')

@section('title', 'Create New Exam')
@section('page-title', 'Create New Exam')
@section('page-description', 'Create an exam using questions from your assigned subjects')

@section('content')
<div class="max-w-7xl mx-auto">
    <form action="{{ route('teacher.exams.store') }}" method="POST" id="createExamForm">
        @csrf
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6 pb-4 border-b">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Create New Exam</h2>
                    <p class="text-sm text-gray-500 mt-1">Fill in the details to create a new exam for your subjects</p>
                </div>
                <a href="{{ route('teacher.exams.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    ← Back to Exams
                </a>
            </div>

            <!-- 🎯 Teacher Subject Info -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h3 class="text-sm font-semibold text-blue-800 mb-2">📚 Your Assigned Subjects:</h3>
                <div class="flex flex-wrap gap-2">
                    @if(isset($teacherWithSubjects) && $teacherWithSubjects->subjects->count() > 0)
                        @foreach($teacherWithSubjects->subjects as $subject)
                            <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $subject->name }}
                            </span>
                        @endforeach
                    @else
                        <span class="text-sm text-red-600">⚠️ No subjects assigned. Please contact administrator.</span>
                    @endif
                </div>
                <p class="text-xs text-blue-700 mt-2">
                    <strong>Note:</strong> You can only create exams with questions from these subjects.
                </p>
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
                        <input type="text" name="title" value="{{ old('title') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               placeholder="e.g., Mathematics Mid-Term Exam 2024"
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
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                required>
                            <option value="">Select Category</option>
                            @foreach($examCategories as $category)
                                <option value="{{ $category->id }}" {{ old('exam_category_id') == $category->id ? 'selected' : '' }}>
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
                                <option value="{{ $class->id }}" {{ old('school_class_id') == $class->id ? 'selected' : '' }}>
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
                        <textarea name="description" rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Add exam instructions or important notes...">{{ old('description') }}</textarea>
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
                        <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" 
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
                        <input type="number" name="total_questions" id="total_questions" value="{{ old('total_questions', 0) }}" 
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
                        <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks', 0) }}" 
                               min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-100" 
                               readonly required>
                        @error('total_marks')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Passing Marks -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Passing Marks <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="passing_marks" value="{{ old('passing_marks', 0) }}" 
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
                        <input type="datetime-local" name="start_time" value="{{ old('start_time') }}" 
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
                        <input type="datetime-local" name="end_time" value="{{ old('end_time') }}" 
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
                        <input type="datetime-local" name="result_release_time" value="{{ old('result_release_time') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Marking Scheme -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">📊 Marking Scheme</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Marks for Correct Answer <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="marking_schemes[0][correct_marks]" id="correct_marks" value="{{ old('marking_schemes.0.correct_marks', 1) }}" 
                               min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Marks Deduction for Wrong Answer <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="marking_schemes[0][wrong_marks]" value="{{ old('marking_schemes.0.wrong_marks', 0.25) }}" 
                               min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Marks for Unattempted
                        </label>
                        <input type="number" name="marking_schemes[0][unattempted_marks]" value="0" 
                               min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" 
                               readonly>
                    </div>
                </div>
            </div>

            <!-- Question Selection -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">❓ Select Questions from Your Subjects</h3>
                
                <!-- Filter Section -->
                <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Subject</label>
                            <select id="filter_subject" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Category</label>
                            <select id="filter_category" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <option value="">All Categories</option>
                                @foreach($examCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text" id="search_questions" placeholder="Search questions..." 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>

                <!-- Questions List -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-100 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                        <span class="text-sm font-semibold text-gray-700">
                            Available Questions (<span id="question_count">{{ $questions->count() }}</span>)
                        </span>
                        <button type="button" id="select_all_btn" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Select All
                        </button>
                    </div>
                    
                    <div id="questions_container" class="max-h-96 overflow-y-auto">
                        @if($questions->count() > 0)
                            @foreach($questions as $question)
                            <div class="question-item p-4 border-b border-gray-200 hover:bg-gray-50 transition" 
                                 data-subject="{{ $question->subject_id }}" 
                                 data-category="{{ $question->exam_category_id }}">
                                <label class="flex items-start cursor-pointer">
                                    <input type="checkbox" name="selected_questions[]" value="{{ $question->id }}" 
                                           class="question-checkbox mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <div class="ml-3 flex-1">
                                        <div class="text-sm text-gray-900">
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
                                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ ucfirst($question->difficulty_level) }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </label>
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
                
                @error('selected_questions')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Exam Settings -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">🔧 Exam Settings</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Randomize Questions -->
                    <div class="flex items-center">
                        <input type="checkbox" name="randomize_questions" value="1" 
                               {{ old('randomize_questions') ? 'checked' : '' }}
                               id="randomize_questions" 
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="randomize_questions" class="ml-2 text-sm text-gray-700">
                            Randomize Questions Order for Each Student
                        </label>
                    </div>

                    <!-- Randomize Options -->
                    <div class="flex items-center">
                        <input type="checkbox" name="randomize_options" value="1" 
                               {{ old('randomize_options') ? 'checked' : '' }}
                               id="randomize_options" 
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="randomize_options" class="ml-2 text-sm text-gray-700">
                            Randomize Answer Options
                        </label>
                    </div>

                    <!-- Show Results Immediately -->
                    <div class="flex items-center">
                        <input type="checkbox" name="show_results_immediately" value="1" 
                               {{ old('show_results_immediately') ? 'checked' : '' }}
                               id="show_results_immediately" 
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="show_results_immediately" class="ml-2 text-sm text-gray-700">
                            Show Results Immediately After Submission
                        </label>
                    </div>

                    <!-- Allow Resume -->
                    <div class="flex items-center">
                        <input type="checkbox" name="allow_resume" value="1" 
                               {{ old('allow_resume', true) ? 'checked' : '' }}
                               id="allow_resume" 
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="allow_resume" class="ml-2 text-sm text-gray-700">
                            Allow Students to Resume Exam
                        </label>
                    </div>

                    <!-- Active Status -->
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" 
                               {{ old('is_active', true) ? 'checked' : '' }}
                               id="is_active" 
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="is_active" class="ml-2 text-sm text-gray-700 font-medium">
                            Exam is Active (Students can take this exam)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('teacher.exams.index') }}" 
                   class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    💾 Create Exam
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const correctMarks = document.getElementById('correct_marks');
    const totalQuestionsInput = document.getElementById('total_questions');
    const totalMarksInput = document.getElementById('total_marks');
    const questionCheckboxes = document.querySelectorAll('.question-checkbox');
    const selectAllBtn = document.getElementById('select_all_btn');
    
    // Calculate totals
    function updateTotals() {
        const selectedCount = document.querySelectorAll('.question-checkbox:checked').length;
        const marksPerQuestion = parseFloat(correctMarks.value) || 0;
        
        totalQuestionsInput.value = selectedCount;
        totalMarksInput.value = (selectedCount * marksPerQuestion).toFixed(2);
    }
    
    // Event listeners
    questionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateTotals);
    });
    
    correctMarks.addEventListener('input', updateTotals);
    
    // Select All functionality
    selectAllBtn.addEventListener('click', function() {
        const visibleCheckboxes = Array.from(questionCheckboxes).filter(cb => 
            cb.closest('.question-item').style.display !== 'none'
        );
        
        const allChecked = visibleCheckboxes.every(cb => cb.checked);
        
        visibleCheckboxes.forEach(cb => {
            cb.checked = !allChecked;
        });
        
        this.textContent = allChecked ? 'Select All' : 'Deselect All';
        updateTotals();
    });
    
    // Filtering
    const filterSubject = document.getElementById('filter_subject');
    const filterCategory = document.getElementById('filter_category');
    const searchInput = document.getElementById('search_questions');
    
    function filterQuestions() {
        const subjectId = filterSubject.value;
        const categoryId = filterCategory.value;
        const searchTerm = searchInput.value.toLowerCase();
        
        let visibleCount = 0;
        
        questionCheckboxes.forEach(checkbox => {
            const item = checkbox.closest('.question-item');
            const itemSubject = item.dataset.subject;
            const itemCategory = item.dataset.category;
            const itemText = item.textContent.toLowerCase();
            
            const matchSubject = !subjectId || itemSubject === subjectId;
            const matchCategory = !categoryId || itemCategory === categoryId;
            const matchSearch = !searchTerm || itemText.includes(searchTerm);
            
            if (matchSubject && matchCategory && matchSearch) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        document.getElementById('question_count').textContent = visibleCount;
    }
    
    filterSubject.addEventListener('change', filterQuestions);
    filterCategory.addEventListener('change', filterQuestions);
    searchInput.addEventListener('input', filterQuestions);
    
    // Initial calculation
    updateTotals();
});
</script>
@endpush
@endsection
