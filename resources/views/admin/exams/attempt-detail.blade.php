@extends('layouts.admin')

@section('title', 'Attempt Preview')

@section('content')
@php
    $studentName = $attempt->student?->user?->name ?? 'Unknown';
    $enrollNo    = $attempt->student?->enrollment_number ?? 'N/A';
    $result      = $attempt->result;
    $timeTaken   = $attempt->time_taken_seconds
                    ? floor($attempt->time_taken_seconds / 60) . 'm ' . ($attempt->time_taken_seconds % 60) . 's'
                    : '—';
    $submittedAt = ($attempt->submitted_at ?? $attempt->auto_submitted_at)?->format('d M Y, h:i A') ?? '—';
@endphp

<div class="container mx-auto px-4 py-8 max-w-5xl">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">👁 Attempt Preview</h1>
            <p class="text-gray-500 text-sm mt-1">
                <span class="font-semibold text-indigo-600">{{ $studentName }}</span>
                &nbsp;·&nbsp; {{ $exam->title }}
                &nbsp;·&nbsp; Submitted {{ $submittedAt }}
            </p>
        </div>
        <a href="{{ route('admin.exams.show', $exam) }}"
           class="inline-flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-5 rounded-lg transition text-sm">
            ← Back to Exam
        </a>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow p-4 text-center col-span-1">
            <p class="text-2xl font-bold text-gray-700">{{ $summaryStats['total'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Total</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center col-span-1">
            <p class="text-2xl font-bold text-green-600">{{ $summaryStats['correct'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Correct</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center col-span-1">
            <p class="text-2xl font-bold text-red-500">{{ $summaryStats['wrong'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Wrong</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center col-span-1">
            <p class="text-2xl font-bold text-gray-400">{{ $summaryStats['unattempted'] }}</p>
            <p class="text-xs text-gray-500 mt-1">Skipped</p>
        </div>
        @if($result)
        <div class="bg-white rounded-xl shadow p-4 text-center col-span-1">
            <p class="text-2xl font-bold text-indigo-600">{{ number_format($result->obtained_marks, 1) }}<span class="text-sm text-gray-400"> / {{ number_format($result->total_marks, 1) }}</span></p>
            <p class="text-xs text-gray-500 mt-1">Score</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4 text-center col-span-1">
            <p class="text-2xl font-bold text-purple-600">{{ number_format($result->accuracy_percentage, 1) }}%</p>
            <p class="text-xs text-gray-500 mt-1">Accuracy</p>
        </div>
        @else
        <div class="bg-white rounded-xl shadow p-4 text-center col-span-2">
            <p class="text-sm text-red-400 font-medium mt-2">⚠ No result calculated yet</p>
        </div>
        @endif
    </div>

    {{-- STUDENT & ATTEMPT META --}}
    <div class="bg-white rounded-xl shadow-md p-5 mb-8 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div>
            <p class="text-xs text-gray-400 uppercase font-semibold mb-1">Student</p>
            <p class="font-semibold text-gray-800">{{ $studentName }}</p>
            <p class="text-gray-500 text-xs">{{ $enrollNo }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase font-semibold mb-1">Submitted</p>
            <p class="text-gray-700">{{ $submittedAt }}</p>
            @if($attempt->status === 'auto_submitted')
                <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded font-medium">Auto-submitted</span>
            @endif
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase font-semibold mb-1">Time Taken</p>
            <p class="text-gray-700">{{ $timeTaken }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-400 uppercase font-semibold mb-1">Result Status</p>
            @if(!$result)
                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-600">⚠ No Result</span>
            @elseif($result->is_published)
                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">✅ Published</span>
                <p class="text-xs text-gray-400 mt-1">{{ $result->published_at?->format('d M, h:i A') }}</p>
            @else
                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">⏳ Under Review</span>
            @endif
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="flex flex-wrap gap-2 mb-6">
        <button onclick="filterQuestions('all')"      id="filter-all"         class="filter-btn active-filter px-4 py-1.5 rounded-full text-sm font-medium border border-gray-300 bg-gray-800 text-white transition">All ({{ $summaryStats['total'] }})</button>
        <button onclick="filterQuestions('correct')"  id="filter-correct"     class="filter-btn px-4 py-1.5 rounded-full text-sm font-medium border border-green-300 text-green-700 bg-green-50 hover:bg-green-100 transition">✓ Correct ({{ $summaryStats['correct'] }})</button>
        <button onclick="filterQuestions('wrong')"    id="filter-wrong"       class="filter-btn px-4 py-1.5 rounded-full text-sm font-medium border border-red-300 text-red-600 bg-red-50 hover:bg-red-100 transition">✗ Wrong ({{ $summaryStats['wrong'] }})</button>
        <button onclick="filterQuestions('unattempted','review')" id="filter-unattempted" class="filter-btn px-4 py-1.5 rounded-full text-sm font-medium border border-gray-300 text-gray-500 bg-gray-50 hover:bg-gray-100 transition">– Skipped ({{ $summaryStats['unattempted'] }})</button>
    </div>

    {{-- QUESTIONS LIST --}}
    <div id="questions-container" class="space-y-6">

        @forelse($questions as $index => $item)
            @php
                $q             = $item['question'];
                $options       = $item['options'];
                $correctOption = $item['correct_option'];
                $selectedOpt   = $item['selected_opt'];
                $status        = $item['status'];
                $timeSpent     = $item['time_spent'];

                $borderColor = match($status) {
                    'correct'     => 'border-l-green-500',
                    'wrong'       => 'border-l-red-500',
                    'review'      => 'border-l-yellow-400',
                    default       => 'border-l-gray-300',
                };
                $badgeBg = match($status) {
                    'correct'  => 'bg-green-100 text-green-700',
                    'wrong'    => 'bg-red-100 text-red-600',
                    'review'   => 'bg-yellow-100 text-yellow-700',
                    default    => 'bg-gray-100 text-gray-500',
                };
                $badgeLabel = match($status) {
                    'correct'  => '✓ Correct',
                    'wrong'    => '✗ Wrong',
                    'review'   => '🔖 Marked for Review',
                    default    => '– Not Attempted',
                };
            @endphp

            <div class="question-card bg-white rounded-xl shadow-md border-l-4 {{ $borderColor }} overflow-hidden"
                 data-status="{{ $status }}">

                {{-- Question Header --}}
                <div class="px-6 py-4 flex items-start justify-between gap-4 border-b border-gray-100">
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="text-sm font-bold text-gray-400">Q{{ $index + 1 }}</span>
                        @if($q?->subject)
                            <span class="text-xs px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full font-medium">{{ $q->subject->name }}</span>
                        @endif
                        @if($q?->difficulty)
                            @php $dn = strtolower($q->difficulty?->name ?? ''); @endphp
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                @if($dn==='easy') bg-green-100 text-green-700
                                @elseif($dn==='medium') bg-yellow-100 text-yellow-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ $q->difficulty->name }}
                            </span>
                        @endif
                        <span class="text-xs px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-full font-medium">+{{ $q?->marks ?? 0 }} marks</span>
                        @if(($q?->negative_marks ?? 0) > 0)
                            <span class="text-xs px-2 py-0.5 bg-red-50 text-red-600 rounded-full font-medium">-{{ $q->negative_marks }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        @if($timeSpent > 0)
                            <span class="text-xs text-gray-400">⏱ {{ floor($timeSpent/60) > 0 ? floor($timeSpent/60).'m ' : '' }}{{ $timeSpent % 60 }}s</span>
                        @endif
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badgeBg }}">{{ $badgeLabel }}</span>
                    </div>
                </div>

                {{-- Question Text --}}
                <div class="px-6 py-4">
                    <div class="text-gray-800 text-sm leading-relaxed prose prose-sm max-w-none">
                        {!! $q?->question_text ?? '<em class="text-gray-400">Question text unavailable</em>' !!}
                    </div>
                </div>

                {{-- Options --}}
                @if($options->isNotEmpty())
                <div class="px-6 pb-5 space-y-2">
                    @foreach($options as $opt)
                        @php
                            $isSelected = $selectedOpt && $selectedOpt->id === $opt->id;
                            $isCorrect  = $opt->is_correct;

                            if ($isCorrect && $isSelected) {
                                // Student picked correct answer
                                $optBg     = 'bg-green-50 border-green-400';
                                $optLabel  = 'bg-green-500 text-white';
                                $optIcon   = '✓';
                            } elseif ($isCorrect && ! $isSelected) {
                                // Correct answer student did NOT pick
                                $optBg     = 'bg-green-50 border-green-300';
                                $optLabel  = 'bg-green-200 text-green-800';
                                $optIcon   = '✓';
                            } elseif ($isSelected && ! $isCorrect) {
                                // Student picked wrong answer
                                $optBg     = 'bg-red-50 border-red-400';
                                $optLabel  = 'bg-red-500 text-white';
                                $optIcon   = '✗';
                            } else {
                                // Neutral option
                                $optBg     = 'bg-gray-50 border-gray-200';
                                $optLabel  = 'bg-gray-200 text-gray-600';
                                $optIcon   = chr(65 + $loop->index); // A, B, C, D
                            }
                        @endphp

                        <div class="flex items-start gap-3 p-3 rounded-lg border {{ $optBg }} transition">
                            <span class="w-7 h-7 rounded-full {{ $optLabel }} flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
                                {{ $optIcon }}
                            </span>
                            <div class="flex-1 text-sm text-gray-800 leading-relaxed prose prose-sm max-w-none">
                                {!! $opt->option_text !!}
                            </div>
                            <div class="shrink-0 flex flex-col items-end gap-1">
                                @if($isSelected)
                                    <span class="text-xs font-semibold {{ $isCorrect ? 'text-green-600' : 'text-red-500' }}">
                                        {{ $isCorrect ? '✓ Student\'s answer' : '✗ Student\'s answer' }}
                                    </span>
                                @endif
                                @if($isCorrect && ! $isSelected)
                                    <span class="text-xs font-semibold text-green-600">✓ Correct answer</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                @else
                    <div class="px-6 pb-5 text-sm text-gray-400 italic">No options found for this question.</div>
                @endif

                {{-- Not attempted note --}}
                @if($status === 'unattempted' || $status === 'review')
                    <div class="px-6 pb-4">
                        <p class="text-sm text-gray-400 italic">
                            @if($correctOption)
                                Correct answer was:
                                <span class="font-semibold text-green-600 not-italic">
                                    {!! strip_tags($correctOption->option_text) !!}
                                </span>
                            @endif
                        </p>
                    </div>
                @endif

            </div>

        @empty
            <div class="text-center py-20 text-gray-400">
                <p class="text-5xl mb-4">📭</p>
                <p class="text-lg font-medium">No answers found for this attempt.</p>
            </div>
        @endforelse

    </div>

    {{-- BACK BUTTON BOTTOM --}}
    <div class="mt-10 text-center">
        <a href="{{ route('admin.exams.show', $exam) }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-8 rounded-xl transition shadow">
            ← Back to Exam
        </a>
    </div>

</div>

@push('scripts')
<script>
    let activeFilters = ['all'];

    function filterQuestions(...statuses) {
        activeFilters = statuses;

        // Update button styles
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('bg-gray-800', 'text-white', 'bg-green-600', 'bg-red-600', 'bg-gray-600');
        });

        const btnId = statuses.includes('all') ? 'filter-all'
                    : statuses.includes('correct') ? 'filter-correct'
                    : statuses.includes('wrong') ? 'filter-wrong'
                    : 'filter-unattempted';

        const btn = document.getElementById(btnId);
        if (btn) btn.classList.add('bg-gray-800', 'text-white');

        // Show / hide cards
        document.querySelectorAll('.question-card').forEach(card => {
            const s = card.dataset.status;
            const visible = statuses.includes('all') || statuses.includes(s);
            card.style.display = visible ? '' : 'none';
        });
    }
</script>
@endpush
@endsection