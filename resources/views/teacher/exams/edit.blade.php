@extends('layouts.teacher')

@section('title', 'Edit Exam')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Edit Exam</h1>
            <p class="text-gray-600 mt-2">Update exam details - <span class="font-mono">{{ $exam->exam_code }}</span></p>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Exam Form -->
        <form action="{{ route('teacher.exams.update', $exam->id) }}" method="POST" id="examForm" class="bg-white shadow-md rounded px-8 pt-6 pb-8">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Basic Information</h2>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                        Exam Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" value="{{ old('title', $exam->title) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('title') border-red-500 @enderror"
                        placeholder="Enter exam title" required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="exam_category_id">
                        Exam Category <span class="text-red-500">*</span>
                    </label>
                    <select name="exam_category_id" id="exam_category_id"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('exam_category_id') border-red-500 @enderror"
                        required>
                        <option value="">Select Category</option>
                        @foreach($examCategories as $category)
                            <option value="{{ $category->id }}" {{ old('exam_category_id', $exam->exam_category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="3"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        placeholder="Enter exam description">{{ old('description', $exam->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="duration_minutes">
                            Duration (Minutes) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            min="1" required>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="total_questions">
                            Total Questions <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="total_questions" id="total_questions" value="{{ old('total_questions', $exam->total_questions) }}"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            min="1" required>
                    </div>
                </div>
            </div>

            <!-- Schedule -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Schedule</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="start_time">
                            Start Time <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="start_time" id="start_time" 
                            value="{{ old('start_time', $exam->start_time->format('Y-m-d\TH:i')) }}"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            required>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="end_time">
                            End Time <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="end_time" id="end_time" 
                            value="{{ old('end_time', $exam->end_time->format('Y-m-d\TH:i')) }}"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="result_release_time">
                        Result Release Time
                    </label>
                    <input type="datetime-local" name="result_release_time" id="result_release_time" 
                        value="{{ old('result_release_time', $exam->result_release_time ? $exam->result_release_time->format('Y-m-d\TH:i') : '') }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>

            <!-- Settings -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Exam Settings</h2>
                
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="show_results_immediately" value="1" 
                            {{ old('show_results_immediately', $exam->show_results_immediately) ? 'checked' : '' }}
                            class="mr-2">
                        <span class="text-gray-700">Show results immediately after submission</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="randomize_questions" value="1" 
                            {{ old('randomize_questions', $exam->randomize_questions) ? 'checked' : '' }}
                            class="mr-2">
                        <span class="text-gray-700">Randomize question order</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="randomize_options" value="1" 
                            {{ old('randomize_options', $exam->randomize_options) ? 'checked' : '' }}
                            class="mr-2">
                        <span class="text-gray-700">Randomize option order</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="allow_resume" value="1" 
                            {{ old('allow_resume', $exam->allow_resume) ? 'checked' : '' }}
                            class="mr-2">
                        <span class="text-gray-700">Allow students to resume exam</span>
                    </label>
                </div>
            </div>

            <!-- Marking Schemes -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Marking Schemes</h2>
                <div id="marking-schemes">
                    @foreach($exam->markingSchemes as $index => $scheme)
                        <div class="marking-scheme-item border rounded p-4 mb-4 bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">
                                        Subject <span class="text-red-500">*</span>
                                    </label>
                                    <select name="marking_schemes[{{ $index }}][subject_id]" 
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
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
                                    <label class="block text-gray-700 text-sm font-bold mb-2">
                                        Correct Marks <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="marking_schemes[{{ $index }}][correct_marks]" 
                                        step="0.01" min="0" value="{{ $scheme->correct_marks }}"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        required>
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">
                                        Wrong Marks (Negative) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="marking_schemes[{{ $index }}][wrong_marks]" 
                                        step="0.01" min="0" value="{{ $scheme->wrong_marks }}"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        required>
                                </div>
                            </div>
                            @if($index > 0)
                                <button type="button" onclick="this.parentElement.remove(); updateSelectedCount();" 
                                    class="mt-2 bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                                    Remove
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-marking-scheme" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    + Add Another Subject
                </button>
            </div>

            <!-- Question Selection -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Select Questions <span class="text-red-500">*</span></h2>
                
                <!-- Filter Section -->
                <div class="mb-4 p-4 bg-gray-50 rounded border">
                    <h3 class="font-semibold mb-3">Filter Questions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <select id="filter_subject" class="shadow border rounded py-2 px-3 text-gray-700">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        <select id="filter_category" class="shadow border rounded py-2 px-3 text-gray-700">
                            <option value="">All Categories</option>
                            @foreach($examCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <input type="text" id="filter_search" placeholder="Search questions..." 
                            class="shadow border rounded py-2 px-3 text-gray-700">
                    </div>
                </div>

                <!-- Available Questions -->
                <div class="border rounded p-4 mb-4 bg-white max-h-96 overflow-y-auto">
                    <h3 class="font-semibold mb-3">Available Questions (Click to select)</h3>
                    <div id="available-questions" class="space-y-2">
                        @foreach($questions as $question)
                            <div class="question-item p-3 border rounded hover:bg-blue-50 cursor-pointer {{ in_array($question->id, $selectedQuestionIds) ? 'bg-blue-100 border-blue-500' : '' }}" 
                                data-question-id="{{ $question->id }}"
                                data-subject-id="{{ $question->subject_id }}"
                                data-category-id="{{ $question->exam_category_id }}">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium">{{ Str::limit($question->question_text, 100) }}</p>
                                        <p class="text-xs text-gray-600 mt-1">
                                            Subject: {{ $question->subject->name ?? 'N/A' }} | 
                                            Category: {{ $question->examCategory->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <span class="select-indicator text-green-600 {{ in_array($question->id, $selectedQuestionIds) ? '' : 'hidden' }}">✓ Selected</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Selected Questions Display -->
                <div class="border rounded p-4 bg-blue-50">
                    <h3 class="font-semibold mb-3">Selected Questions: <span id="selected-count">{{ count($selectedQuestionIds) }}</span></h3>
                    <div id="selected-questions-display" class="space-y-2"></div>
                </div>

                <!-- Hidden inputs for selected questions -->
                <div id="selected-questions-inputs"></div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-between">
                <a href="{{ route('teacher.exams.show', $exam->id) }}" 
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Cancel
                </a>
                <button type="submit" 
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update Exam
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Marking scheme management
    let markingSchemeCount = {{ $exam->markingSchemes->count() }};
    
    document.getElementById('add-marking-scheme').addEventListener('click', function() {
        const container = document.getElementById('marking-schemes');
        const newScheme = `
            <div class="marking-scheme-item border rounded p-4 mb-4 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Subject <span class="text-red-500">*</span></label>
                        <select name="marking_schemes[${markingSchemeCount}][subject_id]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            <option value="">Select Subject</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Correct Marks <span class="text-red-500">*</span></label>
                        <input type="number" name="marking_schemes[${markingSchemeCount}][correct_marks]" step="0.01" min="0" value="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Wrong Marks <span class="text-red-500">*</span></label>
                        <input type="number" name="marking_schemes[${markingSchemeCount}][wrong_marks]" step="0.01" min="0" value="0.25" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                </div>
                <button type="button" onclick="this.parentElement.remove(); updateSelectedCount();" class="mt-2 bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">Remove</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newScheme);
        markingSchemeCount++;
    });

    // Question selection management
    let selectedQuestions = new Set(@json($selectedQuestionIds));

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
                    const questionText = questionItem.querySelector('p').textContent;
                    const subjectInfo = questionItem.querySelector('.text-xs').textContent;
                    
                    const div = document.createElement('div');
                    div.className = 'p-2 bg-white border rounded flex justify-between items-center';
                    div.innerHTML = `
                        <div class="flex-1">
                            <p class="text-sm">${questionText}</p>
                            <p class="text-xs text-gray-600">${subjectInfo}</p>
                        </div>
                        <button type="button" class="text-red-600 hover:text-red-800 ml-2" onclick="removeQuestion(${questionId})">
                            ✕
                        </button>
                    `;
                    display.appendChild(div);
                }
            });
        }
    }

    function removeQuestion(questionId) {
        selectedQuestions.delete(questionId);
        const questionItem = document.querySelector(`[data-question-id="${questionId}"]`);
        if (questionItem) {
            questionItem.classList.remove('bg-blue-100', 'border-blue-500');
            questionItem.querySelector('.select-indicator').classList.add('hidden');
        }
        updateSelectedCount();
        updateSelectedDisplay();
        updateSelectedQuestionsInputs();
    }

    // Question item click handlers
    document.querySelectorAll('.question-item').forEach(item => {
        item.addEventListener('click', function() {
            const questionId = parseInt(this.dataset.questionId);
            const indicator = this.querySelector('.select-indicator');
            
            if (selectedQuestions.has(questionId)) {
                selectedQuestions.delete(questionId);
                this.classList.remove('bg-blue-100', 'border-blue-500');
                indicator.classList.add('hidden');
            } else {
                selectedQuestions.add(questionId);
                this.classList.add('bg-blue-100', 'border-blue-500');
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
            const questionText = item.querySelector('p').textContent.toLowerCase();

            const matchesSubject = !subjectId || itemSubjectId === subjectId;
            const matchesCategory = !categoryId || itemCategoryId === categoryId;
            const matchesSearch = !searchTerm || questionText.includes(searchTerm);

            if (matchesSubject && matchesCategory && matchesSearch) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Form validation
    document.getElementById('examForm').addEventListener('submit', function(e) {
        if (selectedQuestions.size === 0) {
            e.preventDefault();
            alert('Please select at least one question for the exam.');
            return false;
        }
    });

    // Initialize on page load
    updateSelectedDisplay();
    updateSelectedQuestionsInputs();
</script>
@endsection
