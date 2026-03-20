@extends('layouts.teacher')

@section('title', 'Attempt Detail')

@section('content')
@php
    $studentName = $attempt->student?->user?->name ?? 'Unknown';
    $enrollNo    = $attempt->student?->enrollment_number ?? 'N/A';
    $result      = $attempt->result;

    $rawSecs   = $attempt->time_taken_seconds ?? 0;
    $timeTaken = $rawSecs > 0
        ? (floor($rawSecs/3600) > 0 ? floor($rawSecs/3600).'h ' : '')
          . floor(($rawSecs%3600)/60).'m '.($rawSecs%60).'s'
        : '—';

    $submittedAt = ($attempt->submitted_at ?? $attempt->auto_submitted_at)?->format('d M Y, h:i:s A') ?? '—';

    // Time analytics
    $totalTrackedSecs = $questions->sum(fn($i) => $i['time_spent']);
    $maxTimeSecs      = $questions->max(fn($i) => $i['time_spent']) ?: 1;
    $avgTimeSecs      = $questions->count() > 0 ? round($totalTrackedSecs / $questions->count()) : 0;

    $fastestItem = $questions->where('time_spent', '>', 0)->sortBy('time_spent')->first();
    $slowestItem = $questions->sortByDesc('time_spent')->first();
    $fastestIdx  = $fastestItem ? ($questions->search(fn($i) => $i === $fastestItem) + 1) : null;
    $slowestIdx  = $slowestItem ? ($questions->search(fn($i) => $i === $slowestItem) + 1) : null;

function teacherFmtS(int $s): string {
    if ($s <= 0) return '0 sec';
    return $s . ' sec';
}

@endphp

<div class="min-h-screen bg-slate-50">

    {{-- ═══ STICKY TOP BAR ═══ --}}
    <div class="sticky top-0 z-30 bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('teacher.exams.show', $exam->id) }}"
                    class="shrink-0 text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div class="h-4 w-px bg-slate-200 shrink-0"></div>
                <div class="min-w-0">
                    <h1 class="text-base font-semibold text-slate-800 truncate">Attempt Detail</h1>
                    <p class="text-xs text-slate-400 mt-0.5 truncate">
                        {{ $studentName }} &nbsp;·&nbsp; {{ $exam->title }}
                    </p>
                </div>
            </div>
            @php
                $scMap = [
                    'submitted'      => ['label'=>'Submitted',     'class'=>'bg-emerald-50 text-emerald-700 border-emerald-200'],
                    'auto_submitted' => ['label'=>'Auto-Submitted','class'=>'bg-blue-50 text-blue-700 border-blue-200'],
                    'in_progress'    => ['label'=>'In Progress',   'class'=>'bg-amber-50 text-amber-700 border-amber-200'],
                ];
                $sc = $scMap[$attempt->status] ?? ['label'=>ucfirst($attempt->status),'class'=>'bg-slate-100 text-slate-600 border-slate-200'];
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $sc['class'] }}">
                {{ $sc['label'] }}
            </span>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-6 py-8 space-y-6">

        {{-- ═══ STUDENT CARD + SCORE STATS ═══ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Student Info --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center gap-4 mb-5">
                    <div class="w-12 h-12 rounded-full bg-indigo-500 text-white flex items-center justify-center text-xl font-bold shrink-0">
                        {{ strtoupper(substr($studentName, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-base font-semibold text-slate-800 leading-none">{{ $studentName }}</p>
                        <p class="text-xs text-slate-400 mt-1">{{ $enrollNo }}</p>
                    </div>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-start gap-2">
                        <span class="text-slate-400 shrink-0">Started</span>
                        <span class="font-medium text-slate-700 text-right text-xs">
                            {{ $attempt->started_at?->format('d M Y') }}<br>
                            <span class="text-slate-500">{{ $attempt->started_at?->format('h:i:s A') ?? '—' }}</span>
                        </span>
                    </div>
                    <div class="flex justify-between items-start gap-2">
                        <span class="text-slate-400 shrink-0">Submitted</span>
                        <span class="font-medium text-slate-700 text-right text-xs">
                            {{ ($attempt->submitted_at ?? $attempt->auto_submitted_at)?->format('d M Y') ?? '—' }}<br>
                            <span class="text-slate-500">{{ ($attempt->submitted_at ?? $attempt->auto_submitted_at)?->format('h:i:s A') ?? '' }}</span>
                        </span>
                    </div>
                    <div class="flex justify-between border-t border-slate-100 pt-3">
                        <span class="text-slate-400">Total Duration</span>
                        <span class="font-bold text-indigo-600">{{ $timeTaken }}</span>
                    </div>
                    @if($attempt->status === 'auto_submitted')
                    <div class="flex justify-between">
                        <span class="text-slate-400">Submitted by</span>
                        <span class="text-xs font-semibold text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full">Auto-timer</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-slate-400">Result</span>
                        @if(!$result)
                            <span class="text-xs font-semibold text-red-500">⚠ Not calculated</span>
                        @elseif($result->is_published)
                            <span class="text-xs font-semibold text-emerald-600">✅ Published</span>
                        @else
                            <span class="text-xs font-semibold text-amber-600">⏳ Under Review</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Answer + Score Stats --}}
            <div class="lg:col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
                    <p class="text-2xl font-bold text-slate-700">{{ $summaryStats['total'] }}</p>
                    <p class="text-xs text-slate-400 mt-1">Total Qs</p>
                </div>
                <div class="bg-white rounded-2xl border border-emerald-200 shadow-sm p-4 text-center">
                    <p class="text-2xl font-bold text-emerald-600">{{ $summaryStats['correct'] }}</p>
                    <p class="text-xs text-slate-400 mt-1">Correct</p>
                </div>
                <div class="bg-white rounded-2xl border border-red-200 shadow-sm p-4 text-center">
                    <p class="text-2xl font-bold text-red-500">{{ $summaryStats['wrong'] }}</p>
                    <p class="text-xs text-slate-400 mt-1">Wrong</p>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
                    <p class="text-2xl font-bold text-slate-400">{{ $summaryStats['unattempted'] }}</p>
                    <p class="text-xs text-slate-400 mt-1">Skipped</p>
                </div>

                @if($result)
                <div class="bg-white rounded-2xl border border-indigo-200 shadow-sm p-4 text-center col-span-2">
                    <p class="text-2xl font-bold text-indigo-600">
                        {{ number_format($result->obtained_marks, 1) }}
                        <span class="text-sm font-normal text-slate-400">/ {{ number_format($result->total_marks, 1) }}</span>
                    </p>
                    <p class="text-xs text-slate-400 mt-1">Score</p>
                    @php $pct = $result->total_marks > 0 ? ($result->obtained_marks / $result->total_marks) * 100 : 0; @endphp
                    <div class="mt-2 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $pct>=60?'bg-emerald-500':($pct>=40?'bg-amber-400':'bg-red-400') }}"
                             style="width:{{ min($pct,100) }}%"></div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-violet-200 shadow-sm p-4 text-center col-span-2">
                    <p class="text-2xl font-bold text-violet-600">{{ number_format($result->accuracy_percentage ?? $pct, 1) }}%</p>
                    <p class="text-xs text-slate-400 mt-1">Accuracy</p>
                    <div class="mt-2 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                        @php $acc = $result->accuracy_percentage ?? $pct; @endphp
                        <div class="h-full rounded-full {{ $acc>=60?'bg-emerald-500':($acc>=40?'bg-amber-400':'bg-red-400') }}"
                             style="width:{{ min($acc,100) }}%"></div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- ═══ TIME ANALYTICS ═══ --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-indigo-200 shadow-sm p-5 text-center">
                <div class="w-8 h-8 rounded-xl bg-indigo-100 flex items-center justify-center mx-auto mb-2">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-xl font-bold text-indigo-600">{{ teacherFmtS($totalTrackedSecs) }}</p>
                <p class="text-xs text-slate-400 mt-1">Time Tracked</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 text-center">
                <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-2">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10"/>
                    </svg>
                </div>
                <p class="text-xl font-bold text-slate-700">{{ teacherFmtS($avgTimeSecs) }}</p>
                <p class="text-xs text-slate-400 mt-1">Avg / Question</p>
            </div>
            <div class="bg-white rounded-2xl border border-emerald-200 shadow-sm p-5 text-center">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center mx-auto mb-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <p class="text-xl font-bold text-emerald-600">{{ $fastestItem ? teacherFmtS($fastestItem['time_spent']) : '—' }}</p>
                <p class="text-xs text-slate-400 mt-1">Fastest {{ $fastestIdx ? "(Q{$fastestIdx})" : '' }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-amber-200 shadow-sm p-5 text-center">
                <div class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center mx-auto mb-2">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-xl font-bold text-amber-600">{{ $slowestItem ? teacherFmtS($slowestItem['time_spent']) : '—' }}</p>
                <p class="text-xs text-slate-400 mt-1">Slowest {{ $slowestIdx ? "(Q{$slowestIdx})" : '' }}</p>
            </div>
        </div>

        {{-- ═══ SUBJECT-WISE RESULT BREAKDOWN ═══ --}}
        @if($result && $result->subjectWiseResults && $result->subjectWiseResults->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100">
                <h2 class="text-base font-semibold text-slate-800">Subject-wise Breakdown</h2>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($result->subjectWiseResults as $sw)
                @php
                    $swPct = $sw->total_marks > 0 ? round(($sw->obtained_marks / $sw->total_marks) * 100, 1) : 0;
                @endphp
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-sm font-semibold text-slate-700 mb-2">{{ $sw->subject?->name ?? 'N/A' }}</p>
                    <div class="flex items-end justify-between mb-2">
                        <p class="text-lg font-bold text-slate-800">{{ number_format($sw->obtained_marks, 1) }}<span class="text-xs text-slate-400 font-normal"> / {{ number_format($sw->total_marks, 1) }}</span></p>
                        <p class="text-sm font-bold {{ $swPct>=60?'text-emerald-600':($swPct>=40?'text-amber-600':'text-red-600') }}">{{ $swPct }}%</p>
                    </div>
                    <div class="h-1.5 bg-slate-200 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $swPct>=60?'bg-emerald-400':($swPct>=40?'bg-amber-400':'bg-red-400') }}"
                             style="width: {{ min($swPct, 100) }}%"></div>
                    </div>
                    <div class="mt-2 grid grid-cols-3 gap-1 text-xs text-center">
                        <div class="bg-emerald-50 rounded py-1"><span class="font-bold text-emerald-600">{{ $sw->correct_count ?? '—' }}</span><p class="text-slate-400">Correct</p></div>
                        <div class="bg-red-50 rounded py-1"><span class="font-bold text-red-500">{{ $sw->wrong_count ?? '—' }}</span><p class="text-slate-400">Wrong</p></div>
                        <div class="bg-slate-100 rounded py-1"><span class="font-bold text-slate-500">{{ $sw->unattempted_count ?? '—' }}</span><p class="text-slate-400">Skipped</p></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ═══ FILTER BAR ═══ --}}
        <div class="flex flex-wrap gap-2">
            <button onclick="filterQs('all')" id="f-all"
                class="qfilter-btn px-4 py-1.5 rounded-full text-sm font-medium border bg-slate-800 text-white border-slate-800">
                All ({{ $summaryStats['total'] }})
            </button>
            <button onclick="filterQs('correct')" id="f-correct"
                class="qfilter-btn px-4 py-1.5 rounded-full text-sm font-medium border border-emerald-300 text-emerald-700 bg-emerald-50 hover:bg-emerald-100">
                ✓ Correct ({{ $summaryStats['correct'] }})
            </button>
            <button onclick="filterQs('wrong')" id="f-wrong"
                class="qfilter-btn px-4 py-1.5 rounded-full text-sm font-medium border border-red-300 text-red-600 bg-red-50 hover:bg-red-100">
                ✗ Wrong ({{ $summaryStats['wrong'] }})
            </button>
            <button onclick="filterQs('unattempted','review')" id="f-skip"
                class="qfilter-btn px-4 py-1.5 rounded-full text-sm font-medium border border-slate-300 text-slate-500 bg-slate-50 hover:bg-slate-100">
                – Skipped ({{ $summaryStats['unattempted'] }})
            </button>
        </div>

        {{-- ═══ QUESTIONS ═══ --}}
        <div class="space-y-4" id="questions-container">
            @forelse($questions as $index => $item)
            @php
                $q          = $item['question'];
                $options    = $item['options'];
                $corOpt     = $item['correct_option'];
                $selOpt     = $item['selected_opt'];
                $status     = $item['status'];
                $timeSpent  = $item['time_spent'];
                $ans        = $item['answer'];

                $visitCount = $ans->visit_count ?? 0;
                $firstAt    = $ans->first_answered_at;
                $lastAt     = $ans->last_answered_at;

                $barPct   = $maxTimeSecs > 0 ? round(($timeSpent / $maxTimeSecs) * 100) : 0;

                $leftBorder = match($status) {
                    'correct'  => 'border-l-emerald-500',
                    'wrong'    => 'border-l-red-500',
                    'review'   => 'border-l-amber-400',
                    default    => 'border-l-slate-300',
                };
                $timeBadge = match($status) {
                    'correct'  => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                    'wrong'    => 'bg-red-50 text-red-600 border-red-100',
                    default    => 'bg-slate-50 text-slate-500 border-slate-200',
                };
                $barColor = match($status) {
                    'correct'  => 'bg-emerald-400',
                    'wrong'    => 'bg-red-400',
                    default    => 'bg-slate-300',
                };
                $dn = strtolower($q?->difficulty?->name ?? '');
            @endphp

            <div class="question-card bg-white rounded-2xl shadow-sm border border-slate-200 border-l-4 {{ $leftBorder }} overflow-hidden"
                 data-status="{{ $status }}">

                {{-- Header row --}}
                <div class="px-6 py-4 flex items-start justify-between gap-3 border-b border-slate-100 flex-wrap">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-sm font-bold text-slate-400">Q{{ $index + 1 }}</span>
                        @if($q?->subject)
                        <span class="text-xs px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full border border-blue-100 font-medium">
                            {{ $q->subject->name }}
                        </span>
                        @endif
                        @if($q?->difficulty)
                        <span class="text-xs px-2 py-0.5 rounded-full border font-medium
                            @if($dn==='easy') bg-emerald-50 text-emerald-700 border-emerald-100
                            @elseif($dn==='medium') bg-amber-50 text-amber-700 border-amber-100
                            @else bg-red-50 text-red-700 border-red-100 @endif">
                            {{ $q->difficulty->name }}
                        </span>
                        @endif
                        <span class="text-xs px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-full border border-indigo-100 font-semibold">
                            +{{ $q?->marks ?? 0 }}
                        </span>
                        @if(($q?->negative_marks ?? 0) > 0)
                        <span class="text-xs px-2 py-0.5 bg-red-50 text-red-600 rounded-full border border-red-100 font-semibold">
                            −{{ $q->negative_marks }}
                        </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 flex-wrap justify-end">
                        {{-- Visit count --}}
                        @if($visitCount > 0)
                        <span class="inline-flex items-center gap-1 text-xs text-slate-500 bg-slate-100 border border-slate-200 rounded-full px-2.5 py-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            {{ $visitCount }}×
                        </span>
                        @endif

                        {{-- Time badge --}}
                        <span class="inline-flex items-center gap-1.5 text-sm font-bold px-3 py-1.5 rounded-xl border {{ $timeBadge }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ teacherFmtS($timeSpent) }}
                        </span>

                        {{-- Result icon --}}
                        @if($status === 'correct')
                        <span class="w-7 h-7 rounded-full bg-emerald-100 border border-emerald-200 flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        @elseif($status === 'wrong')
                        <span class="w-7 h-7 rounded-full bg-red-100 border border-red-200 flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                        @else
                        <span class="w-7 h-7 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 text-xs font-bold">—</span>
                        @endif
                    </div>
                </div>

                {{-- Time bar --}}
                <div class="px-6 pt-3 pb-1 flex items-center gap-3">
                    <span class="text-xs text-slate-400 w-20 shrink-0">Time used</span>
                    <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $barColor }}" style="width: {{ $barPct }}%"></div>
                    </div>
                    <span class="text-xs text-slate-400 w-10 text-right shrink-0">{{ $barPct }}%</span>
                </div>

                {{-- Timestamps --}}
                @if($firstAt || $lastAt || $visitCount > 1)
                <div class="px-6 py-2 flex flex-wrap gap-x-6 gap-y-1 text-xs text-slate-400 border-b border-slate-50">
                    @if($firstAt)
                    <span class="flex items-center gap-1">
                        <svg class="w-3 h-3 text-indigo-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        First answered: <strong class="text-slate-600 ml-0.5">{{ \Carbon\Carbon::parse($firstAt)->format('h:i:s A') }}</strong>
                    </span>
                    @endif
                    @if($lastAt && $lastAt != $firstAt)
                    <span class="flex items-center gap-1">
                        <svg class="w-3 h-3 text-amber-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Last changed: <strong class="text-slate-600 ml-0.5">{{ \Carbon\Carbon::parse($lastAt)->format('h:i:s A') }}</strong>
                    </span>
                    @endif
                    @if($visitCount > 1)
                    <span class="flex items-center gap-1">
                        Revisited <strong class="text-slate-600 ml-0.5">{{ $visitCount }} times</strong>
                    </span>
                    @endif
                </div>
                @endif

                {{-- Question text --}}
                <div class="px-6 py-4">
                    <div class="text-sm text-slate-800 font-medium leading-relaxed prose prose-sm max-w-none">
                        {!! $q?->question_text ?? '<em class="text-slate-400">Question unavailable</em>' !!}
                    </div>
                </div>

                {{-- Options --}}
                @if($options->isNotEmpty())
                <div class="px-6 pb-5 space-y-2">
                    @foreach($options as $opt)
                    @php
                        $isSel  = $selOpt && $selOpt->id === $opt->id;
                        $isCor  = $opt->is_correct;
                        $optCls = match(true) {
                            $isCor && $isSel   => 'bg-emerald-50 border-emerald-400',
                            $isCor && !$isSel  => 'bg-emerald-50 border-emerald-300',
                            !$isCor && $isSel  => 'bg-red-50 border-red-400',
                            default            => 'bg-slate-50 border-slate-200',
                        };
                        $dotCls = match(true) {
                            $isCor && $isSel  => 'bg-emerald-500 text-white',
                            $isCor && !$isSel => 'bg-emerald-200 text-emerald-800',
                            !$isCor && $isSel => 'bg-red-500 text-white',
                            default           => 'bg-slate-200 text-slate-600',
                        };
                        $dotLbl = match(true) {
                            ($isCor && $isSel) || ($isCor && !$isSel) => '✓',
                            !$isCor && $isSel => '✗',
                            default => chr(65 + $loop->index),
                        };
                    @endphp
                    <div class="flex items-start gap-3 p-3 rounded-xl border {{ $optCls }}">
                        <span class="w-7 h-7 rounded-full {{ $dotCls }} flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
                            {{ $dotLbl }}
                        </span>
                        <div class="flex-1 text-sm text-slate-800 leading-relaxed prose prose-sm max-w-none">
                            {!! $opt->option_text !!}
                        </div>
                        <div class="shrink-0 text-xs font-semibold">
                            @if($isSel)
                                <span class="{{ $isCor ? 'text-emerald-600' : 'text-red-500' }}">
                                    {{ $isCor ? "✓ Student's answer" : "✗ Student's answer" }}
                                </span>
                            @elseif($isCor)
                                <span class="text-emerald-600">✓ Correct answer</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Skipped note --}}
                @if(in_array($status, ['unattempted','review']) && $corOpt)
                <div class="mx-6 mb-5 px-4 py-2.5 bg-emerald-50 border border-emerald-200 rounded-xl text-sm">
                    <span class="font-semibold text-emerald-700">Correct answer: </span>
                    <span class="text-slate-700">{!! strip_tags($corOpt->option_text) !!}</span>
                </div>
                @endif

            </div>
            @empty
            <div class="text-center py-16 text-slate-400">
                <p class="text-4xl mb-3">📭</p>
                <p class="font-medium">No answers found for this attempt.</p>
            </div>
            @endforelse
        </div>

        {{-- Back button --}}
        <div class="text-center pt-4">
            <a href="{{ route('teacher.exams.show', $exam->id) }}"
               class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-8 rounded-xl transition shadow">
                ← Back to Exam
            </a>
        </div>
    </div>
</div>

<style>
    .prose img { max-width: 100%; height: auto; border-radius: 6px; margin: 4px 0; }
    .prose p   { margin: 0; line-height: 1.6; }
</style>

@push('scripts')
<script>
    function filterQs(...statuses) {
        document.querySelectorAll('.qfilter-btn').forEach(b => {
            b.classList.remove('bg-slate-800','text-white','border-slate-800');
        });
        const map = { all:'f-all', correct:'f-correct', wrong:'f-wrong', unattempted:'f-skip' };
        const key  = statuses.includes('all') ? 'all'
                   : statuses.includes('correct') ? 'correct'
                   : statuses.includes('wrong') ? 'wrong' : 'unattempted';
        document.getElementById(map[key])?.classList.add('bg-slate-800','text-white','border-slate-800');
        document.querySelectorAll('.question-card').forEach(c => {
            c.style.display = (statuses.includes('all') || statuses.includes(c.dataset.status)) ? '' : 'none';
        });
    }
    document.addEventListener('DOMContentLoaded', () => filterQs('all'));
</script>
@endpush
@endsection
