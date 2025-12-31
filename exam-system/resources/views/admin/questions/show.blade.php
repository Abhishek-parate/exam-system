@extends('layouts.admin')

@section('title', 'Question Details')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Question Details</h1>
        <div class="flex gap-3">
            <a href="{{ route('admin.questions.edit', $question) }}" 
               class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                ✏️ Edit
            </a>
            <a href="{{ route('admin.questions.index') }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition">
                ← Back
            </a>
        </div>
    </div>

    <!-- Question Details Card -->
    <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
        <!-- Question ID & Status -->
        <div class="flex justify-between items-center mb-6 pb-4 border-b">
            <div>
                <p class="text-sm text-gray-600">Question ID</p>
                <p class="text-2xl font-bold text-gray-900">#{{ $question->id }}</p>
            </div>
            <div class="flex gap-3">
                @if($question->is_active)
                    <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full font-semibold text-sm">
                        ✓ Active
                    </span>
                @else
                    <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full font-semibold text-sm">
                        ✗ Inactive
                    </span>
                @endif
                <span class="px-4 py-2 
                    @if($question->difficulty->level == 1) bg-green-100 text-green-800
                    @elseif($question->difficulty->level == 2) bg-yellow-100 text-yellow-800
                    @else bg-red-100 text-red-800
                    @endif
                    rounded-full font-semibold text-sm">
                    {{ $question->difficulty->name }}
                </span>
            </div>
        </div>

        <!-- Question Metadata -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="space-y-3">
                <div class="flex">
                    <span class="w-32 text-gray-600 font-medium">Category:</span>
                    <span class="text-gray-900 font-semibold">{{ $question->examCategory->name }}</span>
                </div>
                <div class="flex">
                    <span class="w-32 text-gray-600 font-medium">Subject:</span>
                    <span class="text-gray-900 font-semibold">{{ $question->subject->name }}</span>
                </div>
                @if($question->chapter)
                    <div class="flex">
                        <span class="w-32 text-gray-600 font-medium">Chapter:</span>
                        <span class="text-gray-900">{{ $question->chapter->name }}</span>
                    </div>
                @endif
                @if($question->topic)
                    <div class="flex">
                        <span class="w-32 text-gray-600 font-medium">Topic:</span>
                        <span class="text-gray-900">{{ $question->topic->name }}</span>
                    </div>
                @endif
            </div>

            <div class="space-y-3">
                <div class="flex">
                    <span class="w-32 text-gray-600 font-medium">Marks:</span>
                    <span class="text-green-600 font-bold text-lg">+{{ $question->marks }}</span>
                </div>
                @if($question->negative_marks > 0)
                    <div class="flex">
                        <span class="w-32 text-gray-600 font-medium">Negative:</span>
                        <span class="text-red-600 font-bold text-lg">-{{ $question->negative_marks }}</span>
                    </div>
                @endif
                <div class="flex">
                    <span class="w-32 text-gray-600 font-medium">Created By:</span>
                    <span class="text-gray-900">{{ $question->creator->name }}</span>
                </div>
                <div class="flex">
                    <span class="w-32 text-gray-600 font-medium">Created:</span>
                    <span class="text-gray-900">{{ $question->created_at->format('d M Y, h:i A') }}</span>
                </div>
            </div>
        </div>

        <!-- Question Text -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Question</h2>
            <div class="prose max-w-none bg-gray-50 p-6 rounded-lg border-l-4 border-blue-500">
                {!! $question->question_text !!}
            </div>
            @if($question->question_image)
                <div class="mt-4">
                    <img src="{{ Storage::url($question->question_image) }}" 
                         alt="Question Image" 
                         class="max-w-md rounded-lg shadow-md">
                </div>
            @endif
        </div>

        <!-- Answer Options -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Answer Options</h2>
            <div class="space-y-3">
                @foreach($question->options as $option)
                    <div class="flex items-start p-4 rounded-lg border-2 
                        {{ $option->is_correct ? 'bg-green-50 border-green-500' : 'bg-gray-50 border-gray-300' }}">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full 
                            {{ $option->is_correct ? 'bg-green-500' : 'bg-gray-400' }} 
                            flex items-center justify-center text-white font-bold mr-4">
                            {{ $option->option_key }}
                        </div>
                        <div class="flex-1">
                            <div class="prose max-w-none">
                                {!! $option->option_text !!}
                            </div>
                            @if($option->option_image)
                                <img src="{{ Storage::url($option->option_image) }}" 
                                     alt="Option {{ $option->option_key }}" 
                                     class="mt-2 max-w-xs rounded-lg shadow-md">
                            @endif
                        </div>
                        @if($option->is_correct)
                            <div class="flex-shrink-0 ml-4">
                                <span class="px-3 py-1 bg-green-500 text-white rounded-full text-xs font-bold">
                                    ✓ CORRECT
                                </span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Explanation -->
        @if($question->explanation)
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Explanation</h2>
                <div class="prose max-w-none bg-blue-50 p-6 rounded-lg border-l-4 border-blue-500">
                    {!! $question->explanation !!}
                </div>
                @if($question->explanation_image)
                    <div class="mt-4">
                        <img src="{{ Storage::url($question->explanation_image) }}" 
                             alt="Explanation Image" 
                             class="max-w-md rounded-lg shadow-md">
                    </div>
                @endif
            </div>
        @endif

        <!-- Correct Answer Summary -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="font-semibold text-green-800">
                Correct Answer(s): 
                @foreach($question->options->where('is_correct', true) as $correct)
                    <span class="inline-block px-3 py-1 bg-green-500 text-white rounded-full text-sm font-bold mr-2">
                        {{ $correct->option_key }}
                    </span>
                @endforeach
            </p>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-between items-center">
        <div class="flex gap-3">
            <a href="{{ route('admin.questions.edit', $question) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                ✏️ Edit Question
            </a>
            <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" 
                  onsubmit="return confirm('Are you sure you want to delete this question?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                    🗑️ Delete Question
                </button>
            </form>
        </div>
        <a href="{{ route('admin.questions.index') }}" 
           class="text-gray-600 hover:text-gray-900 font-medium">
            ← Back to Questions List
        </a>
    </div>
</div>
@endsection
