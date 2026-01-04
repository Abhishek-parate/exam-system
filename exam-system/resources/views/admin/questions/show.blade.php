@extends('layouts.admin')

@section('title', 'Question Details')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Question Details</h1>
            <p class="text-gray-600 mt-1">Question ID: #{{ $question->id }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.questions.edit', $question) }}" 
               class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg font-semibold transition">
                ✏️ Edit
            </a>
            <a href="{{ route('admin.questions.index') }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-semibold transition">
                ← Back
            </a>
        </div>
    </div>

    <!-- Question Metadata -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">📋 Question Information</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Category -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Exam Category</label>
                @if($question->examCategory)
                    <span class="inline-block px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
                        {{ $question->examCategory->name }}
                    </span>
                @else
                    <span class="inline-block px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm font-semibold">
                        No Category
                    </span>
                @endif
            </div>

            <!-- Subject -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Subject</label>
                <span class="inline-block px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-semibold">
                    {{ $question->subject->name ?? 'N/A' }}
                </span>
            </div>

            <!-- Chapter -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Chapter</label>
                @if($question->chapter)
                    <span class="inline-block px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-semibold">
                        {{ $question->chapter->name }}
                    </span>
                @else
                    <span class="text-gray-500 text-sm">Not Assigned</span>
                @endif
            </div>

            <!-- Topic -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Topic</label>
                @if($question->topic)
                    <span class="inline-block px-3 py-1 bg-pink-100 text-pink-800 rounded-full text-sm font-semibold">
                        {{ $question->topic->name }}
                    </span>
                @else
                    <span class="text-gray-500 text-sm">Not Assigned</span>
                @endif
            </div>

            <!-- Difficulty -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Difficulty</label>
                @if($question->difficulty)
                    @if($question->difficulty->level == 1)
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                            {{ $question->difficulty->name }}
                        </span>
                    @elseif($question->difficulty->level == 2)
                        <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                            {{ $question->difficulty->name }}
                        </span>
                    @else
                        <span class="inline-block px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                            {{ $question->difficulty->name }}
                        </span>
                    @endif
                @else
                    <span class="text-gray-500 text-sm">N/A</span>
                @endif
            </div>

            <!-- Marks -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Marks</label>
                <div class="flex gap-4">
                    <span class="text-green-600 font-bold text-lg">+{{ $question->marks }}</span>
                    @if($question->negative_marks > 0)
                        <span class="text-red-600 font-bold text-lg">-{{ $question->negative_marks }}</span>
                    @endif
                </div>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
                @if($question->is_active)
                    <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                        ✓ Active
                    </span>
                @else
                    <span class="inline-block px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                        ✗ Inactive
                    </span>
                @endif
            </div>

            <!-- Created By -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Created By</label>
                <span class="text-gray-800 text-sm">
                    {{ $question->creator->name ?? 'Unknown' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Question Content -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">❓ Question</h2>
        
        <div class="prose max-w-none mb-4">
            {!! $question->question_text !!}
        </div>

        @if($question->question_image)
            <div class="mt-4">
                <img src="{{ asset('storage/' . $question->question_image) }}" 
                     alt="Question Image" 
                     class="max-w-full h-auto rounded-lg border border-gray-300">
            </div>
        @endif
    </div>

    <!-- Answer Options -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">📝 Answer Options</h2>
        
        <div class="space-y-3">
            @foreach($question->options as $option)
                <div class="p-4 rounded-lg border-2 {{ $option->is_correct ? 'border-green-500 bg-green-50' : 'border-gray-300 bg-gray-50' }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full 
                                             {{ $option->is_correct ? 'bg-green-500 text-white' : 'bg-gray-400 text-white' }} 
                                             font-bold">
                                    {{ $option->option_key }}
                                </span>
                                @if($option->is_correct)
                                    <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full">
                                        ✓ CORRECT ANSWER
                                    </span>
                                @endif
                            </div>
                            
                            <div class="prose max-w-none ml-11">
                                {!! $option->option_text !!}
                            </div>

                            @if($option->option_image)
                                <div class="mt-3 ml-11">
                                    <img src="{{ asset('storage/' . $option->option_image) }}" 
                                         alt="Option {{ $option->option_key }}" 
                                         class="max-w-xs h-auto rounded-lg border border-gray-300">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Correct Answer Summary -->
        <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-sm font-medium text-green-800">
                <strong>Correct Answer(s):</strong>
                @foreach($question->options->where('is_correct', true) as $correct)
                    <span class="inline-block px-2 py-1 bg-green-500 text-white rounded-full text-xs font-bold ml-2">
                        {{ $correct->option_key }}
                    </span>
                @endforeach
            </p>
        </div>
    </div>

    <!-- Explanation -->
    @if($question->explanation)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">💡 Explanation</h2>
            
            <div class="prose max-w-none">
                {!! $question->explanation !!}
            </div>

            @if($question->explanation_image)
                <div class="mt-4">
                    <img src="{{ asset('storage/' . $question->explanation_image) }}" 
                         alt="Explanation Image" 
                         class="max-w-full h-auto rounded-lg border border-gray-300">
                </div>
            @endif
        </div>
    @endif

    <!-- Timestamps -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">⏰ Timestamps</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-600 font-medium">Created At:</span>
                <span class="text-gray-800 ml-2">{{ $question->created_at->format('M d, Y h:i A') }}</span>
            </div>
            <div>
                <span class="text-gray-600 font-medium">Last Updated:</span>
                <span class="text-gray-800 ml-2">{{ $question->updated_at->format('M d, Y h:i A') }}</span>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex gap-4">
        <a href="{{ route('admin.questions.edit', $question) }}" 
           class="bg-yellow-500 hover:bg-yellow-600 text-white px-8 py-3 rounded-lg font-semibold transition shadow-md">
            ✏️ Edit Question
        </a>
        
        <form action="{{ route('admin.questions.destroy', $question) }}" 
              method="POST" 
              onsubmit="return confirm('Are you sure you want to delete this question? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-semibold transition shadow-md">
                🗑️ Delete Question
            </button>
        </form>
        
        <a href="{{ route('admin.questions.index') }}" 
           class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-8 py-3 rounded-lg font-semibold transition shadow-md">
            ← Back to Questions
        </a>
    </div>
</div>
@endsection
