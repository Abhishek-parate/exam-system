@extends('layouts.admin')

@section('title', 'Add New Question')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Add New Question</h1>
        <a href="{{ route('admin.questions.index') }}" class="text-gray-600 hover:text-gray-900">
            ← Back to Questions
        </a>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.questions.store') }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-8" id="questionForm">
        @csrf

        <!-- Category & Subject -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Exam Category (Optional)</label>
                <select name="exam_category_id" id="exam_category_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">Select Category</option>
                    @foreach($examCategories as $category)
                        <option value="{{ $category->id }}" {{ old('exam_category_id') == $category->id ? 'selected' : '' }}>
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
                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
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
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Topic (Optional)</label>
                <select name="topic_id" id="topic_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">Select Topic</option>
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
                        <option value="{{ $difficulty->id }}" {{ old('difficulty_id') == $difficulty->id ? 'selected' : '' }}>
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
                <input type="number" name="marks" step="0.01" min="0" value="{{ old('marks', 1) }}" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                @error('marks')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Negative Marks *</label>
                <input type="number" name="negative_marks" step="0.01" min="0" value="{{ old('negative_marks', 0) }}" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                @error('negative_marks')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
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
            <input type="file" name="question_image" accept="image/*" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            <p class="text-xs text-gray-500 mt-1">You can also insert images directly in the editor above</p>
            @error('question_image')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Answer Options with Quill Editor -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-4">Answer Options *</label>
            
            @for($i = 0; $i < 4; $i++)
            <div class="mb-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
                <div class="flex justify-between items-center mb-3">
                    <label class="font-semibold text-gray-700 text-lg">Option {{ chr(65 + $i) }}</label>
                    <label class="flex items-center px-4 py-2 bg-green-100 rounded-lg cursor-pointer hover:bg-green-200 transition">
                        <input type="checkbox" name="options[{{ $i }}][is_correct]" value="1" class="mr-2 w-4 h-4">
                        <span class="text-sm font-medium text-green-800">✓ Correct Answer</span>
                    </label>
                </div>
                <div id="option_editor_{{ $i }}" class="bg-white border border-gray-300 rounded-lg" style="min-height: 150px;"></div>
                <input type="hidden" name="options[{{ $i }}][text]" id="option_text_{{ $i }}" required>
            </div>
            @endfor

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
            <input type="file" name="explanation_image" accept="image/*" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            @error('explanation_image')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Buttons -->
        <div class="flex gap-4 pt-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg transition shadow-lg">
                💾 Save Question
            </button>
            <a href="{{ route('admin.questions.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-8 rounded-lg transition">
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

    // Initialize Quill for Options
    var optionQuills = [];
    for (let i = 0; i < 4; i++) {
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

    // ============================================
    // Helper Function to Check if Editor Has Content
    // ============================================
    function hasContent(quill) {
        // Get plain text (without HTML tags)
        var text = quill.getText().trim();
        
        // Get HTML content
        var html = quill.root.innerHTML.trim();
        
        // Check if there's text OR if there's an image/embed
        // Images show as <img> tags, even if no text
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
        for (let i = 0; i < 4; i++) {
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
    
    // Category change event - filter subjects or show all
    categorySelect.addEventListener('change', function() {
        const categoryId = this.value;
        
        if (categoryId) {
            // Fetch subjects for selected category via AJAX
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
                    // Fallback to all subjects if error
                    loadAllSubjects();
                });
        } else {
            // No category selected, show all subjects
            loadAllSubjects();
        }
        
        // Reset dependent dropdowns
        chapterSelect.innerHTML = '<option value="">Select Chapter</option>';
        topicSelect.innerHTML = '<option value="">Select Topic</option>';
    });
    
    // Helper function to load all subjects
    function loadAllSubjects() {
        subjectSelect.innerHTML = '<option value="">Select Subject</option>';
        allSubjects.forEach(subject => {
            subjectSelect.innerHTML += `<option value="${subject.id}">${subject.name}</option>`;
        });
    }
    
    // Subject change event - fetch chapters
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
        
        // Reset topic dropdown
        topicSelect.innerHTML = '<option value="">Select Topic</option>';
    });
    
    // Chapter change event - fetch topics
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
