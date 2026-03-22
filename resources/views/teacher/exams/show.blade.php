@extends('layouts.teacher')

@section('title', 'Exam Details — ' . $exam->title)

@section('content')
<div class="min-h-screen bg-slate-50">

    {{-- ═══ STICKY TOP BAR ═══ --}}
    <div class="sticky top-0 z-30 bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('teacher.exams.index') }}"
                    class="shrink-0 text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div class="h-4 w-px bg-slate-200 shrink-0"></div>
                <div class="min-w-0">
                    <h1 class="text-base font-semibold text-slate-800 leading-none truncate">{{ $exam->title }}</h1>
                    <p class="text-xs text-slate-400 mt-0.5 font-mono">{{ $exam->exam_code }}</p>
                </div>

                {{-- Status pill --}}
                @php
                    $statusCfg = match($status) {
                        'Ongoing'   => ['dot' => 'bg-emerald-500 animate-pulse', 'pill' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                        'Scheduled' => ['dot' => 'bg-amber-400',                 'pill' => 'bg-amber-50 text-amber-700 border-amber-200'],
                        default     => ['dot' => 'bg-slate-400',                 'pill' => 'bg-slate-100 text-slate-600 border-slate-200'],
                    };
                @endphp
                <span class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border {{ $statusCfg['pill'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $statusCfg['dot'] }}"></span>
                    {{ $status }}
                </span>

                
            </div>

            <div class="flex items-center gap-2 shrink-0">
                @if($status === 'Scheduled')
                <a href="{{ route('teacher.exams.edit', $exam->id) }}"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-800 border border-indigo-200 hover:border-indigo-400 bg-indigo-50 hover:bg-indigo-100 px-4 py-2 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit Exam
                </a>
                @endif
            </div>
        </div>
    </div>

    

    <div class="max-w-7xl mx-auto px-6 py-8 space-y-6">

        {{-- Alerts --}}
        @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm">
            <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
            <svg class="w-5 h-5 shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            {{ session('error') }}
        </div>
        @endif

        {{-- ═══ STAT CARDS ═══ --}}
        @php
            $totalMarks    = $exam->total_marks ?? 0;
            $passRate       = $statistics['total_attempts'] > 0
                ? round(($statistics['completed_attempts'] / $statistics['total_attempts']) * 100, 1)
                : 0;
        @endphp
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Total Questions --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Questions</p>
                    <div class="w-8 h-8 rounded-xl bg-blue-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-slate-800">{{ $statistics['total_questions'] }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ $exam->duration_minutes }} min exam</p>
            </div>

            {{-- Total Attempts --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Attempts</p>
                    <div class="w-8 h-8 rounded-xl bg-violet-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-slate-800">{{ $statistics['total_attempts'] }}</p>
                <p class="text-xs text-slate-400 mt-1">{{ $statistics['completed_attempts'] }} completed</p>
            </div>

            {{-- Average Score --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Avg Score</p>
                    <div class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-slate-800">{{ number_format($statistics['average_score'], 1) }}<span class="text-lg font-medium text-slate-400">%</span></p>
                <div class="mt-2 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full {{ $statistics['average_score'] >= 60 ? 'bg-emerald-500' : ($statistics['average_score'] >= 40 ? 'bg-amber-400' : 'bg-red-400') }}"
                         style="width: {{ min($statistics['average_score'], 100) }}%"></div>
                </div>
            </div>

            {{-- Completion Rate --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Completion</p>
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-slate-800">{{ $passRate }}<span class="text-lg font-medium text-slate-400">%</span></p>
                <div class="mt-2 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $passRate }}%"></div>
                </div>
            </div>

        </div>

        {{-- ═══ MAIN GRID: INFO + SCHEDULE + MARKING ═══ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Exam Information (span 2) --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="flex items-center gap-4 px-6 py-5 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">Exam Information</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Full configuration details</p>
                    </div>
                </div>
                <div class="p-6 space-y-5">

                    {{-- Row 1 --}}
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Category</p>
                            <p class="text-sm font-semibold text-slate-800">{{ $exam->examCategory->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Duration</p>
                            <p class="text-sm font-semibold text-slate-800">{{ $exam->duration_minutes }} minutes</p>
                        </div>
                    </div>

                    {{-- Row 2 --}}
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Total Questions</p>
                            <p class="text-sm font-semibold text-slate-800">{{ $exam->total_questions }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1">Total Marks</p>
                            <p class="text-sm font-semibold text-slate-800">{{ $exam->total_marks ?? '—' }}</p>
                        </div>
                    </div>

                    {{-- Schedule strip --}}
                    <div class="bg-slate-50 rounded-xl p-4 space-y-3 border border-slate-100">
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Schedule</p>
                        <div class="flex flex-col gap-2">

                            {{-- Start --}}
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 leading-none">Start</p>
                                    <p class="text-sm font-semibold text-slate-800 mt-0.5">{{ $exam->start_time->format('d M Y, h:i A') }}</p>
                                </div>
                            </div>

                            {{-- Connector --}}
                            <div class="ml-3.5 h-4 w-px bg-slate-200"></div>

                            {{-- End --}}
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 leading-none">End</p>
                                    <p class="text-sm font-semibold text-slate-800 mt-0.5">{{ $exam->end_time->format('d M Y, h:i A') }}</p>
                                </div>
                            </div>

                            @if($exam->result_release_time)
                            <div class="ml-3.5 h-4 w-px bg-slate-200"></div>
                            {{-- Result Release --}}
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 leading-none">Results Release</p>
                                    <p class="text-sm font-semibold text-slate-800 mt-0.5">{{ $exam->result_release_time->format('d M Y, h:i A') }}</p>
                                </div>
                            </div>
                            @endif

                        </div>

                        {{-- Duration pill --}}
                        @php
                            $durationHuman = $exam->start_time->diffForHumans($exam->end_time, true);
                        @endphp
                        <div class="flex items-center gap-2 pt-1 mt-1 border-t border-slate-200">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span class="text-xs text-slate-500">Window spans <strong class="text-slate-700">{{ $durationHuman }}</strong></span>
                        </div>
                    </div>

                    {{-- Description --}}
                    @if($exam->description)
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-1.5">Description</p>
                        <p class="text-sm text-slate-600 leading-relaxed">{{ $exam->description }}</p>
                    </div>
                    @endif

                    {{-- Settings chips --}}
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2">Active Settings</p>
                        <div class="flex flex-wrap gap-2">
                            @if($exam->show_results_immediately)
                            <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100 rounded-full">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/></svg>
                                Show Results Immediately
                            </span>
                            @endif
                            @if($exam->randomize_questions)
                            <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium bg-violet-50 text-violet-700 border border-violet-100 rounded-full">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Randomize Questions
                            </span>
                            @endif
                            @if($exam->randomize_options)
                            <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium bg-teal-50 text-teal-700 border border-teal-100 rounded-full">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Randomize Options
                            </span>
                            @endif
                            @if($exam->allow_resume)
                            <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100 rounded-full">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Allow Resume
                            </span>
                            @endif
                            @if(!$exam->show_results_immediately && !$exam->randomize_questions && !$exam->randomize_options && !$exam->allow_resume)
                            <span class="text-xs text-slate-400 italic">No special settings enabled</span>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            {{-- Marking Schemes --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="flex items-center gap-4 px-6 py-5 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">Marking Schemes</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $exam->markingSchemes->count() }} subject(s) configured</p>
                    </div>
                </div>
                <div class="p-6 space-y-3">
                    @forelse($exam->markingSchemes as $scheme)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm font-semibold text-slate-800 mb-3">{{ $scheme->subject->name }}</p>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="bg-emerald-50 border border-emerald-100 rounded-lg px-3 py-2 text-center">
                                <p class="text-lg font-bold text-emerald-600">+{{ $scheme->correct_marks }}</p>
                                <p class="text-xs text-emerald-500 font-medium">Correct</p>
                            </div>
                            <div class="bg-red-50 border border-red-100 rounded-lg px-3 py-2 text-center">
                                <p class="text-lg font-bold text-red-500">−{{ $scheme->wrong_marks }}</p>
                                <p class="text-xs text-red-400 font-medium">Wrong</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-slate-400 text-sm">No marking schemes defined</div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- ═══ QUESTIONS LIST ═══ --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between gap-4 px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">Questions</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $exam->questions->count() }} question(s) assigned</p>
                    </div>
                </div>
                <span class="text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-100 rounded-full px-3 py-1">
                    {{ $exam->questions->count() }} / {{ $exam->total_questions }} required
                </span>
            </div>

            @if($exam->questions->count() > 0)
            <div class="divide-y divide-slate-100">
                @foreach($exam->questions as $index => $question)
                @php
                    $dn = strtolower($question->difficulty?->name ?? '');
                    $diffCfg = match($dn) {
                        'easy'   => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                        'medium' => 'bg-amber-50 text-amber-700 border-amber-100',
                        default  => 'bg-red-50 text-red-700 border-red-100',
                    };
                @endphp
                <div class="px-6 py-4 hover:bg-slate-50 transition group">
                    <div class="flex items-start gap-4">

                        {{-- Number badge --}}
                        <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">
                            {{ $index + 1 }}
                        </div>

                        <div class="flex-1 min-w-0">
                            {{-- Meta badges --}}
                            <div class="flex items-center gap-2 flex-wrap mb-2">
                                <span class="text-xs font-medium text-slate-500">{{ $question->subject->name ?? 'N/A' }}</span>
                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full border {{ $diffCfg }}">
                                    {{ $question->difficulty?->name ?? 'N/A' }}
                                </span>
                                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 border border-emerald-100 rounded-full px-2 py-0.5">
                                    +{{ $question->marks }}
                                </span>
                                @if(isset($question->negative_marks) && $question->negative_marks > 0)
                                <span class="text-xs font-semibold text-red-500 bg-red-50 border border-red-100 rounded-full px-2 py-0.5">
                                    −{{ $question->negative_marks }}
                                </span>
                                @endif
                            </div>

                            {{-- Question text --}}
                            <div class="text-sm text-slate-800 font-medium leading-relaxed mb-3 question-text-clamp">
                                {!! $question->question_text !!}
                            </div>

                            {{-- Options grid --}}
                            @if($question->options && $question->options->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                @foreach($question->options as $option)
                                <div class="flex items-start gap-2 rounded-lg px-3 py-2 text-sm
                                    {{ $option->is_correct
                                        ? 'bg-emerald-50 border border-emerald-200 text-emerald-800'
                                        : 'bg-slate-50 border border-slate-200 text-slate-600' }}">
                                    <span class="shrink-0 mt-0.5 {{ $option->is_correct ? 'text-emerald-500' : 'text-slate-300' }}">
                                        @if($option->is_correct)
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        @endif
                                    </span>
                                    <div class="leading-snug">{!! $option->option_text !!}</div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-16 text-center px-4">
                <svg class="w-12 h-12 text-slate-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium text-slate-400">No questions assigned yet</p>
            </div>
            @endif
        </div>

        {{-- ═══ ATTEMPTS TABLE ═══ --}}
        @if($exam->attempts->count() > 0)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between gap-4 px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-4">
                    <div class="w-9 h-9 rounded-xl bg-violet-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-slate-800">Student Attempts</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Showing latest {{ min($exam->attempts->count(), 10) }} of {{ $exam->attempts->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">#</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Student</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Started At</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Submitted At</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Time Taken</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Score</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($exam->attempts->take(10) as $i => $attempt)
                        @php
                            $submittedAt = $attempt->submitted_at ?? $attempt->auto_submitted_at ?? null;
                            $timeTaken   = null;
                            if ($attempt->started_at && $submittedAt) {
                                $secs      = $attempt->started_at->diffInSeconds($submittedAt);
                                $timeTaken = floor($secs / 60) . 'm ' . ($secs % 60) . 's';
                            }
                            $statusMap = [
                                'in_progress'    => ['label' => 'In Progress',     'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
                                'submitted'      => ['label' => 'Submitted',        'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                                'auto_submitted' => ['label' => 'Auto-Submitted',   'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
                            ];
                            $sc = $statusMap[$attempt->status] ?? ['label' => ucfirst($attempt->status), 'class' => 'bg-slate-100 text-slate-600 border-slate-200'];
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3.5 text-slate-400 font-mono text-xs">{{ $i + 1 }}</td>

                            {{-- Student --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-indigo-500 text-white flex items-center justify-center text-xs font-bold shrink-0">
                                        {{ strtoupper(substr($attempt->student->user->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-800 text-sm leading-none">{{ $attempt->student->user->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-slate-400 mt-0.5">{{ $attempt->student->enrollment_number ?? '' }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Started --}}
                            <td class="px-5 py-3.5 text-slate-600 text-xs whitespace-nowrap">
                                @if($attempt->started_at)
                                    <p class="font-medium text-slate-700">{{ $attempt->started_at->format('d M Y') }}</p>
                                    <p class="text-slate-400">{{ $attempt->started_at->format('h:i:s A') }}</p>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>

                            {{-- Submitted --}}
                            <td class="px-5 py-3.5 text-slate-600 text-xs whitespace-nowrap">
                                @if($submittedAt)
                                    <p class="font-medium text-slate-700">{{ $submittedAt->format('d M Y') }}</p>
                                    <p class="text-slate-400">{{ $submittedAt->format('h:i:s A') }}</p>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>

                            {{-- Time Taken --}}
                            <td class="px-5 py-3.5 text-xs whitespace-nowrap">
                                @if($timeTaken)
                                <span class="inline-flex items-center gap-1 text-slate-600">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $timeTaken }}
                                </span>
                                @else
                                <span class="text-slate-300">—</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $sc['class'] }}">
                                    {{ $sc['label'] }}
                                </span>
                            </td>

                            {{-- Score --}}
                            <td class="px-5 py-3.5">
                                @if($attempt->score !== null)
                                <div>
                                    <p class="font-bold text-slate-800 text-sm">{{ number_format($attempt->score, 1) }}%</p>
                                    <div class="mt-1 h-1 w-16 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full {{ $attempt->score >= 60 ? 'bg-emerald-500' : ($attempt->score >= 40 ? 'bg-amber-400' : 'bg-red-400') }}"
                                             style="width: {{ min($attempt->score, 100) }}%"></div>
                                    </div>
                                </div>
                                @else
                                <span class="text-slate-300 text-xs">Not scored</span>
                                @endif
                            </td>

                           {{-- Actions --}}
<td class="px-5 py-3.5">
    <a href="{{ route('teacher.exams.attempts.show', [$exam->id, $attempt->id]) }}"
       class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 border border-indigo-100 px-2.5 py-1.5 rounded-lg transition">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg>
        View
    </a>
</td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ═══ DANGER ZONE ═══ --}}
        @if($status === 'Scheduled' && $exam->attempts->count() === 0)
        <div class="bg-white rounded-2xl border border-red-200 shadow-sm overflow-hidden">
            <div class="flex items-center gap-4 px-6 py-5 border-b border-red-100 bg-red-50">
                <div class="w-9 h-9 rounded-xl bg-red-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-red-800">Danger Zone</h2>
                    <p class="text-xs text-red-500 mt-0.5">Irreversible actions — proceed with caution</p>
                </div>
            </div>
            <div class="px-6 py-5 flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-800">Delete this exam</p>
                    <p class="text-xs text-slate-400 mt-0.5">This will permanently remove the exam and all its data. This action cannot be undone.</p>
                </div>
                <form action="{{ route('teacher.exams.destroy', $exam->id) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to delete this exam? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 active:scale-95 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm shadow-red-200 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Exam
                    </button>
                </form>
            </div>
        </div>
        @endif

    </div>{{-- /max-w-7xl --}}
</div>

<style>
    .question-text-clamp img { max-width: 100%; height: auto; border-radius: 6px; margin: 4px 0; }
    .question-text-clamp p  { margin: 0; line-height: 1.6; }
</style>

@endsection
