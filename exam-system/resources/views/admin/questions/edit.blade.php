@extends('layouts.admin')

@section('title', 'Edit Question')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Question #{{ $question->id }}</h1>
        <a href="{{ route('admin.questions.show', $question) }}" class="text-gray-600 hover:text-gray-900">
            ← Back to Question
        </a>
    </div>

    <form method="POST" action="{{ route('admin.questions.update', $question) }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-8" id="questionForm">
        @csrf
        @method('PUT')

        <!-- Category & Subject -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Exam Category *</label>
                <select name="exam_category_id" id="exam_category_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">Select Category</option>
                    @foreach($examCategories as $category)
                        <option value="{{ $category->id }}" {{ $question->exam_category_id == $category->id ? 'selected' : '' }}>
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
                        <option value="{{ $subject->id }}" {{ $question->subject_id == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
                @error('subject_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Chapter (Optional)</label>
                <select name="chapter_id" id="chapter_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">Select Chapter</option>
                    @foreach($chapters as $chapter)
                        <option value="{{ $chapter->id }}" {{ $question->chapter_id == $chapter->id ? 'selected' : '' }}>
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
                        <option value="{{ $topic->id }}" {{ $question->topic_id == $topic->id ? 'selected' : '' }}>
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
                        <option value="{{ $difficulty->id }}" {{ $question->difficulty_id == $difficulty->id ? 'selected' : '' }}>
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
                <input type="number" name="marks" step="0.01" min="0" value="{{ $question->marks }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                @error('marks')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Negative Marks *</label>
                <input type="number" name="negative_marks" step="0.01" min="0" value="{{ $question->negative_marks }}" required
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
            <input type="hidden" name="question_text" id="question_text" value="{{ $question->question_text }}" required>
            @error('question_text')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Question Image -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Question Image (Optional)</label>
            @if($question->question_image)
                <div class="mb-3">
                    <img src="{{ Storage::url($question->question_image) }}" alt="Current Question Image" class="max-w-xs rounded-lg shadow-md">
                    <p class="text-sm text-gray-600 mt-2">Current image (will be replaced if you upload a new one)</p>
                </div>
            @endif
            <input type="file" name="question_image" accept="image/*"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            @error('question_image')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Note about Options -->
        <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded">
            <p class="text-sm text-yellow-800">
                ⚠️ <strong>Note:</strong> To edit answer options, please delete this question and create a new one. Option editing will be available in a future update.
            </p>
        </div>

        <!-- Display Current Options (Read-only) -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-4">Current Answer Options (Read-only)</label>
            @foreach($question->options as $option)
                <div class="mb-3 p-4 border border-gray-200 rounded-lg {{ $option->is_correct ? 'bg-green-50' : 'bg-gray-50' }}">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-semibold text-gray-700 text-lg">Option {{ $option->option_key }}</span>
                        @if($option->is_correct)
                            <span class="px-3 py-1 bg-green-500 text-white rounded-full text-xs font-bold">✓ CORRECT</span>
                        @endif
                    </div>
                    <div class="prose max-w-none">
                        {!! $option->option_text !!}
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Explanation with Quill Editor -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Explanation (Optional)</label>
            <div id="explanation_editor" class="bg-white border border-gray-300 rounded-lg" style="min-height: 200px;"></div>
            <input type="hidden" name="explanation" id="explanation" value="{{ $question->explanation }}">
        </div>

        <!-- Explanation Image -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Explanation Image (Optional)</label>
            @if($question->explanation_image)
                <div class="mb-3">
                    <img src="{{ Storage::url($question->explanation_image) }}" alt="Current Explanation Image" class="max-w-xs rounded-lg shadow-md">
                    <p class="text-sm text-gray-600 mt-2">Current image (will be replaced if you upload a new one)</p>
                </div>
            @endif
            <input type="file" name="explanation_image" accept="image/*"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            @error('explanation_image')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Active Status -->
        <div class="mb-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ $question->is_active ? 'checked' : '' }} class="mr-2 w-4 h-4">
                <span class="text-sm font-medium text-gray-700">Question is Active</span>
            </label>
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
<script>
// Initialize Quill for Question Text with existing content
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

// Set existing question text
questionQuill.root.innerHTML = {!! json_encode($question->question_text) !!};

// Initialize Quill for Explanation with existing content
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

// Set existing explanation text
@if($question->explanation)
    explanationQuill.root.innerHTML = {!! json_encode($question->explanation) !!};
@endif

// Sync Quill content to hidden inputs on form submit
document.getElementById('questionForm').addEventListener('submit', function(e) {
    // Get question text
    var questionHTML = questionQuill.root.innerHTML;
    document.getElementById('question_text').value = questionHTML;
    
    // Validate question text is not empty
    if (questionQuill.getText().trim().length === 0) {
        e.preventDefault();
        alert('Please enter question text');
        return false;
    }
    
    // Get explanation text (optional)
    var explanationHTML = explanationQuill.root.innerHTML;
    document.getElementById('explanation').value = explanationHTML;
    
    return true;
});

// Dynamic dropdowns for Category -> Subject
document.getElementById('exam_category_id').addEventListener('change', function() {
    const categoryId = this.value;
    const subjectSelect = document.getElementById('subject_id');
    
    subjectSelect.innerHTML = '<option value="">Loading...</option>';
    
    if (categoryId) {
        fetch(`/admin/questions/subjects/${categoryId}`)
            .then(response => response.json())
            .then(data => {
                subjectSelect.innerHTML = '<option value="">Select Subject</option>';
                data.forEach(subject => {
                    subjectSelect.innerHTML += `<option value="${subject.id}">${subject.name}</option>`;
                });
            })
            .catch(error => {
                console.error('Error:', error);
                subjectSelect.innerHTML = '<option value="">Error loading subjects</option>';
            });
    } else {
        subjectSelect.innerHTML = '<option value="">Select Subject</option>';
    }
    
    document.getElementById('chapter_id').innerHTML = '<option value="">Select Chapter</option>';
    document.getElementById('topic_id').innerHTML = '<option value="">Select Topic</option>';
});

// Dynamic dropdowns for Subject -> Chapter
document.getElementById('subject_id').addEventListener('change', function() {
    const subjectId = this.value;
    const chapterSelect = document.getElementById('chapter_id');
    
    if (subjectId) {
        chapterSelect.innerHTML = '<option value="">Loading...</option>';
        fetch(`/admin/questions/chapters/${subjectId}`)
            .then(response => response.json())
            .then(data => {
                chapterSelect.innerHTML = '<option value="">Select Chapter</option>';
                data.forEach(chapter => {
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
    
    document.getElementById('topic_id').innerHTML = '<option value="">Select Topic</option>';
});

// Dynamic dropdowns for Chapter -> Topic
document.getElementById('chapter_id').addEventListener('change', function() {
    const chapterId = this.value;
    const topicSelect = document.getElementById('topic_id');
    
    if (chapterId) {
        topicSelect.innerHTML = '<option value="">Loading...</option>';
        fetch(`/admin/questions/topics/${chapterId}`)
            .then(response => response.json())
            .then(data => {
                topicSelect.innerHTML = '<option value="">Select Topic</option>';
                data.forEach(topic => {
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
</script>
@endpush
