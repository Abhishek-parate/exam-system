@extends('layouts.admin')

@section('title', 'Add New Question')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Add New Question</h1>
        <a href="{{ route('admin.questions.index') }}" class="text-gray-600 hover:text-gray-900">
            ← Back to Questions
        </a>
    </div>

    <form method="POST" action="{{ route('admin.questions.store') }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow-md p-8">
        @csrf

        <!-- Category & Subject -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Exam Category *</label>
                <select name="exam_category_id" id="exam_category_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">Select Category</option>
                    @foreach($examCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
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
                        <option value="{{ $difficulty->id }}">{{ $difficulty->name }}</option>
                    @endforeach
                </select>
                @error('difficulty_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Marks *</label>
                <input type="number" name="marks" step="0.01" min="0" value="1" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                @error('marks')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Negative Marks *</label>
                <input type="number" name="negative_marks" step="0.01" min="0" value="0" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                @error('negative_marks')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Question Text -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Question Text *</label>
            <textarea name="question_text" rows="4" required
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                      placeholder="Enter the question text..."></textarea>
            @error('question_text')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Question Image (Optional) -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Question Image (Optional)</label>
            <input type="file" name="question_image" accept="image/*"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            @error('question_image')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Options -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Answer Options *</label>
            <div id="options-container">
                @for($i = 0; $i < 4; $i++)
                    <div class="flex gap-3 mb-3 items-start">
                        <div class="flex-1">
                            <input type="text" name="options[{{ $i }}][text]" required
                                   placeholder="Option {{ chr(65 + $i) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                        </div>
                        <label class="flex items-center px-4 py-2 bg-gray-100 rounded-lg cursor-pointer hover:bg-gray-200">
                            <input type="checkbox" name="options[{{ $i }}][is_correct]" value="1" class="mr-2">
                            <span class="text-sm">Correct</span>
                        </label>
                    </div>
                @endfor
            </div>
            <p class="text-xs text-gray-500 mt-2">Check the box next to the correct answer(s)</p>
        </div>

        <!-- Explanation -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Explanation (Optional)</label>
            <textarea name="explanation" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                      placeholder="Explain the correct answer..."></textarea>
        </div>

        <!-- Submit Buttons -->
        <div class="flex gap-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                💾 Save Question
            </button>
            <a href="{{ route('admin.questions.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg transition">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
// Dynamic dropdowns
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
            });
    } else {
        subjectSelect.innerHTML = '<option value="">Select Subject</option>';
    }
    
    document.getElementById('chapter_id').innerHTML = '<option value="">Select Chapter</option>';
    document.getElementById('topic_id').innerHTML = '<option value="">Select Topic</option>';
});

document.getElementById('subject_id').addEventListener('change', function() {
    const subjectId = this.value;
    const chapterSelect = document.getElementById('chapter_id');
    
    if (subjectId) {
        fetch(`/admin/questions/chapters/${subjectId}`)
            .then(response => response.json())
            .then(data => {
                chapterSelect.innerHTML = '<option value="">Select Chapter</option>';
                data.forEach(chapter => {
                    chapterSelect.innerHTML += `<option value="${chapter.id}">${chapter.name}</option>`;
                });
            });
    } else {
        chapterSelect.innerHTML = '<option value="">Select Chapter</option>';
    }
    
    document.getElementById('topic_id').innerHTML = '<option value="">Select Topic</option>';
});

document.getElementById('chapter_id').addEventListener('change', function() {
    const chapterId = this.value;
    const topicSelect = document.getElementById('topic_id');
    
    if (chapterId) {
        fetch(`/admin/questions/topics/${chapterId}`)
            .then(response => response.json())
            .then(data => {
                topicSelect.innerHTML = '<option value="">Select Topic</option>';
                data.forEach(topic => {
                    topicSelect.innerHTML += `<option value="${topic.id}">${topic.name}</option>`;
                });
            });
    } else {
        topicSelect.innerHTML = '<option value="">Select Topic</option>';
    }
});
</script>
@endsection
