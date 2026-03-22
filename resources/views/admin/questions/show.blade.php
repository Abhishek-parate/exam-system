@extends('layouts.admin')

@section('title', 'Question Details')

@section('content')

<style>
/* ═══════════════════════════════════════════════════════════
   QUESTION SHOW — RESPONSIVE OVERRIDES
═══════════════════════════════════════════════════════════ */
.container { box-sizing:border-box; width:100%; }

/* Quick stats: 2 col always, 4 col on md+ */
.q-stats-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.5rem; }
/* Meta info grid */
.q-meta-grid  { display:grid; grid-template-columns:1fr; gap:.875rem; }
/* Options grid */
.q-opts-grid  { display:grid; grid-template-columns:1fr; gap:1rem; }

@media (min-width: 640px) {
    .q-meta-grid  { grid-template-columns: 1fr 1fr; }
    .q-opts-grid  { grid-template-columns: 1fr 1fr; }
}
@media (min-width: 768px) {
    .q-stats-grid { grid-template-columns: repeat(4, 1fr); }
}
</style>
<div class="container mx-auto px-4 py-8 max-w-5xl">

    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex justify-between items-start flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2 flex-wrap">
                    <h1 class="text-3xl font-bold text-gray-900">Question Details</h1>
                    @if($question->is_active)
                        <span class="px-3 py-1.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Active
                        </span>
                    @else
                        <span class="px-3 py-1.5 bg-red-100 text-red-700 text-xs font-semibold rounded-full flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            Inactive
                        </span>
                    @endif

                    @php
                        // Robust type detection: use stored value if set, otherwise infer from options
                        $qType = $question->question_type;
                        if (!$qType) {
                            $qType = $question->options->isEmpty() ? 'subjective' : 'mcq';
                        }
                    @endphp

                    @if($qType === 'subjective')
                        <span class="px-3 py-1.5 bg-purple-100 text-purple-700 text-xs font-semibold rounded-full flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Subjective
                        </span>
                    @else
                        <span class="px-3 py-1.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            MCQ
                        </span>
                    @endif
                </div>
                <p class="text-gray-600 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                    </svg>
                    Question ID: <span class="font-semibold text-gray-800">#{{ $question->id }}</span>
                </p>
            </div>

            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('admin.questions.edit', $question) }}"
                   class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white rounded-lg font-semibold transition-all shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Question
                </a>
                <a href="{{ route('admin.questions.index') }}"
                   class="flex items-center gap-2 px-5 py-2.5 bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 hover:text-gray-900 rounded-lg font-semibold transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="q-stats-grid">
        <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-5">
            <p class="text-green-700 text-sm font-medium mb-1">Positive Marks</p>
            <p class="text-3xl font-bold text-green-800">+{{ $question->marks }}</p>
        </div>
        <div class="bg-gradient-to-br from-red-50 to-red-100 border border-red-200 rounded-xl p-5">
            <p class="text-red-700 text-sm font-medium mb-1">Negative Marks</p>
            <p class="text-3xl font-bold text-red-800">-{{ $question->negative_marks }}</p>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-5">
            <p class="text-purple-700 text-sm font-medium mb-1">Difficulty</p>
            <p class="text-xl font-bold text-purple-800">{{ $question->difficulty->name ?? 'N/A' }}</p>
        </div>
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-5">
            <p class="text-blue-700 text-sm font-medium mb-1">Question Type</p>
            @if($qType === 'subjective')
                <p class="text-xl font-bold text-blue-800">Subjective</p>
                <p class="text-xs text-blue-600 mt-0.5">Text Answer</p>
            @else
                <p class="text-xl font-bold text-blue-800">MCQ</p>
                <p class="text-xs text-blue-600 mt-0.5">{{ $question->options->count() }} Options</p>
            @endif
        </div>
    </div>

    <!-- Classification & Metadata -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-5 flex items-center">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            Classification & Metadata
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Exam Category</label>
                @if($question->examCategory)
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-100 text-blue-800 rounded-lg text-sm font-semibold">{{ $question->examCategory->name }}</span>
                @else
                    <span class="text-gray-500 text-sm">Not Assigned</span>
                @endif
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Subject</label>
                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-100 text-indigo-800 rounded-lg text-sm font-semibold">{{ $question->subject->name ?? 'N/A' }}</span>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Difficulty Level</label>
                @if($question->difficulty)
                    @php
                        $diffColors = [
                            1 => 'bg-green-100 text-green-800',
                            2 => 'bg-yellow-100 text-yellow-800',
                            3 => 'bg-red-100 text-red-800',
                        ];
                        $dc = $diffColors[$question->difficulty->level] ?? 'bg-yellow-100 text-yellow-800';
                    @endphp
                    <span class="inline-flex items-center px-3 py-1.5 {{ $dc }} rounded-lg text-sm font-semibold">{{ $question->difficulty->name }}</span>
                @else
                    <span class="text-gray-500 text-sm">Not Set</span>
                @endif
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Chapter</label>
                @if($question->chapter)
                    <span class="inline-flex items-center px-3 py-1.5 bg-purple-100 text-purple-800 rounded-lg text-sm font-semibold">{{ $question->chapter->name }}</span>
                @else
                    <span class="text-gray-500 text-sm">Not Assigned</span>
                @endif
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Topic</label>
                @if($question->topic)
                    <span class="inline-flex items-center px-3 py-1.5 bg-pink-100 text-pink-800 rounded-lg text-sm font-semibold">{{ $question->topic->name }}</span>
                @else
                    <span class="text-gray-500 text-sm">Not Assigned</span>
                @endif
            </div>
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Created By</label>
                <span class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-800 rounded-lg text-sm font-semibold">{{ $question->creator->name ?? 'Unknown' }}</span>
            </div>
        </div>
    </div>

    <!-- Question Content -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-5 flex items-center">
            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            Question Content
        </h2>
        <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-lg p-6 border-l-4 border-indigo-500">
            <div class="prose max-w-none text-gray-800 leading-relaxed">
                {!! $question->question_text !!}
            </div>
            @if($question->question_image)
                <div class="mt-4">
                    <img src="{{ asset('storage/' . $question->question_image) }}"
                         alt="Question Image"
                         class="max-w-full h-auto rounded-lg border-2 border-indigo-200 shadow-md">
                </div>
            @endif
        </div>
    </div>

    <!-- ============================================================
         Answer Options -- differs by question type
    ============================================================ -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-5 flex items-center">
            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>
            Answer Options
        </h2>

        @if($qType === 'subjective')

            {{-- SUBJECTIVE: display stored correct answer --}}
            <div class="rounded-xl border-2 border-purple-300 bg-gradient-to-r from-purple-50 to-indigo-50 p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-purple-600 uppercase tracking-widest mb-1">Subjective Question</p>
                        <p class="text-xs text-purple-500 mb-4">Student types a free-text answer. Matching is case-insensitive and ignores extra spaces.</p>

                        <p class="text-sm font-semibold text-gray-700 mb-3">Correct Answer:</p>

                        @if($question->correct_answer)
                            <div class="flex items-center gap-3 p-4 bg-white border-2 border-green-400 rounded-xl shadow-sm">
                                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="text-gray-900 font-bold text-lg">{{ $question->correct_answer }}</span>
                            </div>

                            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="text-xs text-blue-700">
                                    <strong>Matching rule:</strong> Student answer
                                    <code class="bg-blue-100 px-1 py-0.5 rounded text-blue-800">"{{ strtolower(trim($question->correct_answer)) }}"</code>
                                    will be accepted as correct regardless of capitalisation or extra spaces.
                                </p>
                            </div>
                        @else
                            <div class="p-4 bg-red-50 border-2 border-red-200 rounded-xl">
                                <p class="text-red-600 font-semibold text-sm flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    No correct answer stored. Please edit this question and add the correct answer.
                                </p>
                                <a href="{{ route('admin.questions.edit', $question) }}"
                                   class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-semibold transition">
                                    Edit Question to Add Answer
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        @else

            {{-- MCQ: show all options, highlight the correct one --}}
            @if($question->options->count() > 0)
                <div class="space-y-4">
                    @foreach($question->options as $index => $option)
                        <div class="relative rounded-xl border-2 transition-all
                                    {{ $option->is_correct
                                        ? 'border-green-400 bg-gradient-to-r from-green-50 to-emerald-50 shadow-md'
                                        : 'border-gray-200 bg-gray-50' }}">
                            @if($option->is_correct)
                                <div class="absolute -top-3 -right-3 z-10">
                                    <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        CORRECT
                                    </span>
                                </div>
                            @endif
                            <div class="p-5 flex items-start gap-4">
                                <span class="flex items-center justify-center w-10 h-10 rounded-xl font-bold text-base shadow-sm flex-shrink-0
                                             {{ $option->is_correct ? 'bg-green-500 text-white' : 'bg-blue-500 text-white' }}">
                                    {{ $option->option_key }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <div class="prose max-w-none text-gray-800 leading-relaxed">
                                        {!! $option->option_text !!}
                                    </div>
                                    @if($option->option_image)
                                        <img src="{{ asset('storage/' . $option->option_image) }}"
                                             alt="Option {{ $option->option_key }}"
                                             class="mt-3 max-w-sm h-auto rounded-lg border-2 {{ $option->is_correct ? 'border-green-300' : 'border-gray-300' }} shadow-sm">
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Correct answer summary -->
                <div class="mt-5 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-300 rounded-xl flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-green-800 mb-1">Correct Answer(s):</p>
                        <div class="flex flex-wrap gap-2">
                            @forelse($question->options->where('is_correct', true) as $correct)
                                <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-500 text-white rounded-lg text-sm font-bold">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Option {{ $correct->option_key }}
                                </span>
                            @empty
                                <span class="text-red-500 text-sm font-medium">No correct option marked. Please edit this question.</span>
                            @endforelse
                        </div>
                    </div>
                </div>

            @else
                <div class="p-6 bg-yellow-50 border-2 border-yellow-200 rounded-xl text-center">
                    <p class="text-yellow-700 font-medium">No answer options found for this MCQ question.</p>
                    <a href="{{ route('admin.questions.edit', $question) }}"
                       class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm font-semibold transition">
                        Edit Question to Add Options
                    </a>
                </div>
            @endif

        @endif
    </div>

    <!-- Explanation -->
    @if($question->explanation)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-5 flex items-center">
                <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                Detailed Explanation
            </h2>
            <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-lg p-6 border-l-4 border-amber-500">
                <div class="prose max-w-none text-gray-800 leading-relaxed">
                    {!! $question->explanation !!}
                </div>
                @if($question->explanation_image)
                    <div class="mt-4">
                        <img src="{{ asset('storage/' . $question->explanation_image) }}"
                             alt="Explanation Image"
                             class="max-w-full h-auto rounded-lg border-2 border-amber-200 shadow-md">
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Timestamps -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-900 mb-5 flex items-center">
            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            Timeline & Metadata
        </h2>
        <div class="q-meta-grid">
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-5 rounded-lg border border-blue-200">
                <p class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-2">Created At</p>
                <p class="text-gray-800 font-semibold text-lg">{{ $question->created_at->format('M d, Y') }}</p>
                <p class="text-gray-500 text-sm">{{ $question->created_at->format('h:i A') }} &mdash; {{ $question->created_at->diffForHumans() }}</p>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 p-5 rounded-lg border border-purple-200">
                <p class="text-xs font-bold text-purple-600 uppercase tracking-widest mb-2">Last Updated</p>
                <p class="text-gray-800 font-semibold text-lg">{{ $question->updated_at->format('M d, Y') }}</p>
                <p class="text-gray-500 text-sm">{{ $question->updated_at->format('h:i A') }} &mdash; {{ $question->updated_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>

    <!-- Action Buttons -- regular (NOT sticky) to avoid overflow behind sidebar -->
    <div class="bg-white border-t-2 border-gray-200 rounded-xl shadow-sm p-6 mb-8">
        <div class="flex gap-4 justify-between items-center flex-wrap">
            <a href="{{ route('admin.questions.index') }}"
               class="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition-colors group">
                <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Questions List
            </a>
            <div class="flex gap-3 flex-wrap">
                <a href="{{ route('admin.questions.edit', $question) }}"
                   class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white rounded-lg font-semibold transition-all shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Question
                </a>
                <form action="{{ route('admin.questions.destroy', $question) }}"
                      method="POST"
                      class="inline"
                      onsubmit="return confirm('Permanently delete this question? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-lg font-semibold transition-all shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete Question
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

<style>
.prose { color: #1f2937; max-width: none; }
.prose p { margin-bottom: 1em; line-height: 1.75; }
.prose strong { font-weight: 600; color: #111827; }
.prose em { font-style: italic; }
.prose ul, .prose ol { margin: 1em 0; padding-left: 1.5em; }
.prose li { margin: 0.5em 0; }
.prose img { border-radius: 0.5rem; margin: 1em 0; max-width: 100%; }
.prose code { background-color: #f3f4f6; padding: 0.2em 0.4em; border-radius: 0.25rem; font-size: 0.875em; font-family: monospace; }
.prose pre { background-color: #1f2937; color: #f9fafb; padding: 1em; border-radius: 0.5rem; overflow-x: auto; margin: 1em 0; }
.prose blockquote { border-left: 4px solid #e5e7eb; padding-left: 1em; color: #6b7280; font-style: italic; margin: 1em 0; }
</style>

@endsection