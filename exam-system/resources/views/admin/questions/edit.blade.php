@extends('layouts.admin')

@section('title', 'Edit Question')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Question #{{ $question->id }}</h1>
        <a href="{{ route('admin.questions.show', $question) }}" class="text-gray-600 hover:text-gray-900">
            ← Back to Question
        </a>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.questions.update', $question) }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-8" id="questionForm">
        @csrf
        @method('PUT')

        <!-- Category & Subject -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Exam Category (Optional)</label>
                <select name="exam_category_id" id="exam_category_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">Select Category</option>
                    @foreach($examCategories as $category)
                        <option value="{{ $category->id }}" {{ old('exam_category_id', $question->exam_category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('exam_category_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                <select name="subject_id" id="subject_id" required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">Select Subject</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ old('subject_id', $question->subject_id) == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
                @error('subject_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Chapter & Topic -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Chapter (Optional)</label>
                <select name="chapter_id" id="chapter_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">Select Chapter</option>
                    @foreach($chapters as $chapter)
                        <option value="{{ $chapter->id }}" {{ old('chapter_id', $question->chapter_id) == $chapter->id ? 'selected' : '' }}>
                            {{ $chapter->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Topic (Optional)</label>
                <select name="topic_id" id="topic_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">Select Topic</option>
                    @foreach($topics as $topic)
                        <option value="{{ $topic->id }}" {{ old('topic_id', $question->topic_id) == $topic->id ? 'selected' : '' }}>
                            {{ $topic->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Difficulty & Marks -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty *</label>
                <select name="difficulty_id" required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">Select Difficulty</option>
                    @foreach($difficulties as $difficulty)
                        <option value="{{ $difficulty->id }}" {{ old('difficulty_id', $question->difficulty_id) == $difficulty->id ? 'selected' : '' }}>
                            {{ $difficulty->name }}
                        </option>
                    @endforeach
                </select>
                @error('difficulty_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Marks *</label>
                <input type="number" name="marks" step="0.01" min="0" value="{{ old('marks', $question->marks) }}" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                @error('marks')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Negative Marks *</label>
                <input type="number" name="negative_marks" step="0.01" min="0" value="{{ old('negative_marks', $question->negative_marks) }}" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                @error('negative_marks')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Status -->
        <div class="mb-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $question->is_active) ? 'checked' : '' }} 
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <span class="ml-2 text-sm font-medium text-gray-700">Active</span>
            </label>
        </div>

        <!-- Question Text with Quill Editor -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Question Text *</label>
            <div id="question_editor" class="bg-white border border-gray-300 rounded-lg" style="min-height: 300px;"></div>
            <input type="hidden" name="question_text" id="question_text" required>
            <p class="text-xs text-gray-500 mt-1">You can add text, images, formulas, and formatting</p>
            @error('question_text')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Question Image (Optional) -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Question Image (Optional)</label>
            
            @if($question->question_image)
                <div class="mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-sm text-gray-600 mb-2">Current image:</p>
                    <img src="{{ asset('storage/' . $question->question_image) }}" alt="Current Question Image" class="max-w-xs h-auto rounded border border-gray-300">
                    <p class="text-xs text-gray-500 mt-2">Upload a new image to replace this one</p>
                </div>
            @endif
            
            <input type="file" name="question_image" accept="image/*" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            @error('question_image')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Answer Options with Quill Editor -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-4">Answer Options *</label>
            
            @foreach($question->options as $index => $option)
            <div class="mb-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
                <div class="flex justify-between items-center mb-3">
                    <label class="font-semibold text-gray-700 text-lg">Option {{ chr(65 + $index) }}</label>
                    <label class="flex items-center px-4 py-2 bg-green-100 rounded-lg cursor-pointer hover:bg-green-200 transition">
                        <input type="checkbox" name="options[{{ $index }}][is_correct]" value="1" 
                               {{ old('options.'.$index.'.is_correct', $option->is_correct) ? 'checked' : '' }}
                               class="mr-2 w-4 h-4">
                        <span class="text-sm font-medium text-green-800">✓ Correct Answer</span>
                    </label>
                </div>
                <div id="option_editor_{{ $index }}" class="bg-white border border-gray-300 rounded-lg" style="min-height: 150px;"></div>
                <input type="hidden" name="options[{{ $index }}][text]" id="option_text_{{ $index }}" required>
            </div>
            @endforeach

            <p class="text-xs text-gray-500 mt-2">✓ Check the box next to the correct answer(s)</p>
        </div>

        <!-- Explanation with Quill Editor -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Explanation (Optional)</label>
            <div id="explanation_editor" class="bg-white border border-gray-300 rounded-lg" style="min-height: 200px;"></div>
            <input type="hidden" name="explanation" id="explanation">
        </div>

        <!-- Explanation Image (Optional) -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Explanation Image (Optional)</label>
            
            @if($question->explanation_image)
                <div class="mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-sm text-gray-600 mb-2">Current image:</p>
                    <img src="{{ asset('storage/' . $question->explanation_image) }}" alt="Current Explanation Image" class="max-w-xs h-auto rounded border border-gray-300">
                    <p class="text-xs text-gray-500 mt-2">Upload a new image to replace this one</p>
                </div>
            @endif
            
            <input type="file" name="explanation_image" accept="image/*" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            @error('explanation_image')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Buttons -->
        <div class="flex gap-4 pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition shadow-lg">
                💾 Update Question
            </button>
            <a href="{{ route('admin.questions.show', $question) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-8 rounded-lg transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ============================================
    // Initialize Quill Editors
    // ============================================
    
    // Initialize Quill for Question Text
    var questionQuill = new Quill('#question_editor', {
        theme: 'snow',
        placeholder: 'Enter the question text here...',
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

    // Set existing question content
    questionQuill.root.innerHTML = {!! json_encode($question->question_text) !!};

    // Initialize Quill for Options
    var optionQuills = [];
    var existingOptions = @json($question->options);
    
    for (let i = 0; i < existingOptions.length; i++) {
        optionQuills[i] = new Quill('#option_editor_' + i, {
            theme: 'snow',
            placeholder: 'Enter option ' + String.fromCharCode(65 + i) + ' text...',
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
        
        // Set existing option content
        optionQuills[i].root.innerHTML = existingOptions[i].option_text;
    }

    // Initialize Quill for Explanation
    var explanationQuill = new Quill('#explanation_editor', {
        theme: 'snow',
        placeholder: 'Explain the correct answer (optional)...',
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

    // Set existing explanation content
    @if($question->explanation)
        explanationQuill.root.innerHTML = {!! json_encode($question->explanation) !!};
    @endif

    // ============================================
    // Helper Function to Check if Editor Has Content
    // ============================================
    function hasContent(quill) {
        // Get plain text (without HTML tags)
        var text = quill.getText().trim();
        
        // Get HTML content
        var html = quill.root.innerHTML.trim();
        
        // Check if there's text OR if there's an image/embed
        var hasText = text.length > 0;
        var hasImage = html.includes('<img') || html.includes('<iframe') || html.includes('<video');
        
        return hasText || hasImage;
    }

    // ============================================
    // Form Submission & Validation
    // ============================================
    
    document.getElementById('questionForm').addEventListener('submit', function(e) {
        // Get question HTML content
        var questionHTML = questionQuill.root.innerHTML;
        document.getElementById('question_text').value = questionHTML;

        // ✅ FIXED: Validate question has content (text OR image)
        if (!hasContent(questionQuill)) {
            e.preventDefault();
            alert('Please enter question text or insert an image');
            return false;
        }

        // Get options text and validate
        for (let i = 0; i < optionQuills.length; i++) {
            var optionHTML = optionQuills[i].root.innerHTML;
            document.getElementById('option_text_' + i).value = optionHTML;

            // ✅ FIXED: Validate option has content (text OR image)
            if (!hasContent(optionQuills[i])) {
                e.preventDefault();
                alert('Please enter text or insert an image for Option ' + String.fromCharCode(65 + i));
                return false;
            }
        }

        // Get explanation text (optional)
        var explanationHTML = explanationQuill.root.innerHTML;
        document.getElementById('explanation').value = explanationHTML;

        // Check if at least one correct answer is selected
        var checkboxes = document.querySelectorAll('input[name*="is_correct"]:checked');
        if (checkboxes.length === 0) {
            e.preventDefault();
            alert('Please select at least one correct answer');
            return false;
        }

        return true;
    });

    // ============================================
    // Dynamic Dropdowns with AJAX
    // ============================================
    
    const categorySelect = document.getElementById('exam_category_id');
    const subjectSelect = document.getElementById('subject_id');
    const chapterSelect = document.getElementById('chapter_id');
    const topicSelect = document.getElementById('topic_id');
    
    // Store all subjects for filtering
    const allSubjects = @json($subjects);
    
    // Category change event
    categorySelect.addEventListener('change', function() {
        const categoryId = this.value;
        
        if (categoryId) {
            subjectSelect.innerHTML = '<option value="">Loading...</option>';
            
            fetch(`/admin/questions/subjects/${categoryId}`)
                .then(response => response.json())
                .then(subjects => {
                    subjectSelect.innerHTML = '<option value="">Select Subject</option>';
                    subjects.forEach(subject => {
                        const selected = subject.id == {{ $question->subject_id }} ? 'selected' : '';
                        subjectSelect.innerHTML += `<option value="${subject.id}" ${selected}>${subject.name}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    loadAllSubjects();
                });
        } else {
            loadAllSubjects();
        }
        
        // Reset dependent dropdowns if category changes
        if (categoryId != {{ $question->exam_category_id ?? 'null' }}) {
            chapterSelect.innerHTML = '<option value="">Select Chapter</option>';
            topicSelect.innerHTML = '<option value="">Select Topic</option>';
        }
    });
    
    // Helper function to load all subjects
    function loadAllSubjects() {
        subjectSelect.innerHTML = '<option value="">Select Subject</option>';
        allSubjects.forEach(subject => {
            const selected = subject.id == {{ $question->subject_id }} ? 'selected' : '';
            subjectSelect.innerHTML += `<option value="${subject.id}" ${selected}>${subject.name}</option>`;
        });
    }
    
    // Subject change event
    subjectSelect.addEventListener('change', function() {
        const subjectId = this.value;
        
        if (subjectId) {
            chapterSelect.innerHTML = '<option value="">Loading...</option>';
            
            fetch(`/admin/questions/chapters/${subjectId}`)
                .then(response => response.json())
                .then(chapters => {
                    chapterSelect.innerHTML = '<option value="">Select Chapter</option>';
                    chapters.forEach(chapter => {
                        const selected = chapter.id == {{ $question->chapter_id ?? 'null' }} ? 'selected' : '';
                        chapterSelect.innerHTML += `<option value="${chapter.id}" ${selected}>${chapter.name}</option>`;
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    chapterSelect.innerHTML = '<option value="">Error loading chapters</option>';
                });
        } else {
            chapterSelect.innerHTML = '<option value="">Select Chapter</option>';
        }
        
        // Reset topic if subject changes
        if (subjectId != {{ $question->subject_id }}) {
            topicSelect.innerHTML = '<option value="">Select Topic</option>';
        }
    });
    
    // Chapter change event
    chapterSelect.addEventListener('change', function() {
        const chapterId = this.value;
        
        if (chapterId) {
            topicSelect.innerHTML = '<option value="">Loading...</option>';
            
            fetch(`/admin/questions/topics/${chapterId}`)
                .then(response => response.json())
                .then(topics => {
                    topicSelect.innerHTML = '<option value="">Select Topic</option>';
                    topics.forEach(topic => {
                        const selected = topic.id == {{ $question->topic_id ?? 'null' }} ? 'selected' : '';
                        topicSelect.innerHTML += `<option value="${topic.id}" ${selected}>${topic.name}</option>`;
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
