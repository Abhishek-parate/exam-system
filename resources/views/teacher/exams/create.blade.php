@extends('layouts.teacher')

@section('title', 'Create New Exam')

@section('content')
<div class="min-h-screen bg-slate-50">

    {{-- ═══ TOP STICKY NAVBAR ═══ --}}
    <div class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between sticky top-0 z-30 shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('teacher.exams.index') }}"
                class="text-slate-400 hover:text-slate-700 transition p-1.5 rounded-lg hover:bg-slate-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="h-4 w-px bg-slate-200"></div>
            <div>
                <h1 class="text-base font-semibold text-slate-800 leading-none">Create New Exam</h1>
                <p class="text-xs text-slate-400 mt-0.5">Fill all required fields and select questions</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('teacher.exams.index') }}"
                class="text-sm text-slate-500 hover:text-slate-800 px-4 py-2 rounded-lg border border-slate-200 hover:border-slate-300 transition bg-white">
                Cancel
            </a>
            <button type="submit" form="examForm"
                class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-semibold px-5 py-2 rounded-lg shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Publish Exam
            </button>
        </div>
    </div>

    <div class="flex max-w-7xl mx-auto px-4 py-8 gap-8">

        {{-- ═══ LEFT STICKY SIDEBAR ═══ --}}
        <aside class="hidden lg:block w-64 shrink-0">
            <div class="sticky top-20">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-4">Form Sections</p>
                    <nav class="space-y-1">
                        <a href="#sec-basics" data-section="sec-basics"
                            class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition">
                            <span class="nav-dot w-6 h-6 rounded-full bg-slate-100 group-hover:bg-indigo-100 flex items-center justify-center text-xs font-bold text-slate-400 group-hover:text-indigo-600 shrink-0 transition">1</span>
                            Basic Information
                        </a>
                        <a href="#sec-schedule" data-section="sec-schedule"
                            class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition">
                            <span class="nav-dot w-6 h-6 rounded-full bg-slate-100 group-hover:bg-indigo-100 flex items-center justify-center text-xs font-bold text-slate-400 group-hover:text-indigo-600 shrink-0 transition">2</span>
                            Schedule
                        </a>
                        <a href="#sec-settings" data-section="sec-settings"
                            class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition">
                            <span class="nav-dot w-6 h-6 rounded-full bg-slate-100 group-hover:bg-indigo-100 flex items-center justify-center text-xs font-bold text-slate-400 group-hover:text-indigo-600 shrink-0 transition">3</span>
                            Exam Settings
                        </a>
                        <a href="#sec-marking" data-section="sec-marking"
                            class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition">
                            <span class="nav-dot w-6 h-6 rounded-full bg-slate-100 group-hover:bg-indigo-100 flex items-center justify-center text-xs font-bold text-slate-400 group-hover:text-indigo-600 shrink-0 transition">4</span>
                            Marking Schemes
                        </a>
                        <a href="#sec-questions" data-section="sec-questions" id="nav-questions"
                            class="nav-link group items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition hidden">
                            <span class="nav-dot w-6 h-6 rounded-full bg-slate-100 group-hover:bg-indigo-100 flex items-center justify-center text-xs font-bold text-slate-400 group-hover:text-indigo-600 shrink-0 transition">5</span>
                            Questions
                        </a>
                    </nav>

                    {{-- Progress --}}
                    <div class="mt-6 pt-5 border-t border-slate-100">
                        <p class="text-xs text-slate-400 font-medium mb-2">Questions Selected</p>
                        <div class="flex items-center gap-2">
                            <span id="sidebar-count" class="text-2xl font-bold text-indigo-600 transition-colors duration-200">0</span>
                            <span class="text-sm text-slate-400">/ <span id="sidebar-total">{{ old('total_questions', 10) }}</span> required</span>
                        </div>
                        <div class="mt-2 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                            <div id="progress-bar" class="h-full bg-indigo-500 rounded-full transition-all duration-300" style="width:0%"></div>
                        </div>
                        {{-- Limit label --}}
                        <p id="limit-label" class="hidden mt-1.5 text-xs font-semibold text-red-500 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            Limit reached
                        </p>
                    </div>

                    {{-- Hint shown when questions section is hidden --}}
                    <div id="questions-hint" class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                        <p class="text-xs text-amber-700 font-medium flex items-start gap-1.5">
                            <svg class="w-3.5 h-3.5 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Select a subject in Marking Schemes to unlock Questions
                        </p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ═══ MAIN FORM CONTENT ═══ --}}
        <main class="flex-1 min-w-0">

            {{-- Alerts --}}
            @if(session('success'))
            <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl mb-6 text-sm">
                <svg class="w-5 h-5 shrink-0 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error') || $errors->any())
            <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl mb-6 text-sm">
                <svg class="w-5 h-5 shrink-0 text-red-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    @if(session('error'))<p>{{ session('error') }}</p>@endif
                    @if($errors->any())
                    <ul class="list-disc list-inside space-y-0.5 mt-1">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                    @endif
                </div>
            </div>
            @endif

            <form action="{{ route('teacher.exams.store') }}" method="POST" id="examForm" novalidate>
                @csrf

                {{-- ═══ SECTION 1: BASIC INFORMATION ═══ --}}
                <section id="sec-basics" class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 overflow-hidden scroll-mt-20">
                    <div class="flex items-center gap-4 px-6 py-5 border-b border-slate-100">
                        <div class="w-9 h-9 rounded-xl bg-indigo-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-800">Basic Information</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Title, category and exam structure</p>
                        </div>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5" for="title">
                                Exam Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}"
                                class="w-full rounded-xl border @error('title') border-red-400 bg-red-50 focus:ring-red-400 @else border-slate-200 focus:ring-indigo-400 @enderror px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:border-transparent transition"
                                placeholder="e.g. JEE Mock Test — Physics Unit 3" required>
                            @error('title')
                            <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>{{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5" for="exam_category_id">
                                Exam Category <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select name="exam_category_id" id="exam_category_id"
                                    class="w-full appearance-none rounded-xl border @error('exam_category_id') border-red-400 bg-red-50 @else border-slate-200 @enderror px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition pr-10" required>
                                    <option value="">Select a category</option>
                                    @foreach($examCategories as $category)
                                        <option value="{{ $category->id }}" {{ old('exam_category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5" for="duration_minutes">
                                Duration <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', 60) }}"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition pr-20"
                                    min="1" required>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-medium">minutes</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5" for="total_questions">
                                Total Questions <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="total_questions" id="total_questions" value="{{ old('total_questions', 10) }}"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition"
                                min="1" required>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5" for="description">
                                Description <span class="font-normal text-slate-400">(optional)</span>
                            </label>
                            <textarea name="description" id="description" rows="3"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition resize-none"
                                placeholder="Describe this exam for your students…">{{ old('description') }}</textarea>
                        </div>

                    </div>
                </section>

                {{-- ═══ SECTION 2: SCHEDULE ═══ --}}
                <section id="sec-schedule" class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 overflow-hidden scroll-mt-20">
                    <div class="flex items-center gap-4 px-6 py-5 border-b border-slate-100">
                        <div class="w-9 h-9 rounded-xl bg-violet-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-800">Schedule</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Set start, end and result release timings</p>
                        </div>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5" for="start_time">
                                Start Time <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time') }}"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-transparent transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5" for="end_time">
                                End Time <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time') }}"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-transparent transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5" for="result_release_time">
                                Result Release <span class="font-normal text-slate-400">(optional)</span>
                            </label>
                            <input type="datetime-local" name="result_release_time" id="result_release_time" value="{{ old('result_release_time') }}"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-transparent transition">
                        </div>
                    </div>
                </section>

                {{-- ═══ SECTION 3: SETTINGS ═══ --}}
                <section id="sec-settings" class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 overflow-hidden scroll-mt-20">
                    <div class="flex items-center gap-4 px-6 py-5 border-b border-slate-100">
                        <div class="w-9 h-9 rounded-xl bg-teal-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-800">Exam Settings</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Configure behavior and options for this exam</p>
                        </div>
                    </div>
                    <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @php
                            $settings = [
                                ['name' => 'show_results_immediately', 'label' => 'Show results immediately',  'desc' => 'Student sees score right after submitting'],
                                ['name' => 'randomize_questions',      'label' => 'Randomize question order',  'desc' => 'Questions appear in random order per attempt'],
                                ['name' => 'randomize_options',        'label' => 'Randomize option order',    'desc' => 'MCQ options are shuffled per question'],
                                ['name' => 'allow_resume',             'label' => 'Allow exam resume',         'desc' => 'Students can continue from where they left'],
                            ];
                        @endphp
                        @foreach($settings as $s)
                        <label class="flex items-start gap-4 p-4 rounded-xl border border-slate-200 hover:border-teal-300 hover:bg-teal-50/50 cursor-pointer transition group has-[:checked]:border-teal-400 has-[:checked]:bg-teal-50">
                            <div class="mt-0.5">
                                <input type="checkbox" name="{{ $s['name'] }}" value="1"
                                    {{ old($s['name']) ? 'checked' : '' }}
                                    class="w-4 h-4 text-teal-500 border-slate-300 rounded focus:ring-teal-400 focus:ring-offset-0">
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-700 group-hover:text-teal-800 transition">{{ $s['label'] }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $s['desc'] }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </section>

                {{-- ═══ SECTION 4: MARKING SCHEMES ═══ --}}
                <section id="sec-marking" class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 overflow-hidden scroll-mt-20">
                    <div class="flex items-center gap-4 px-6 py-5 border-b border-slate-100">
                        <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-800">Marking Schemes</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Define marks per subject — correct & negative marking</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="hidden md:grid grid-cols-12 gap-4 px-4 mb-2">
                            <div class="col-span-5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Subject</div>
                            <div class="col-span-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Correct (+)</div>
                            <div class="col-span-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Wrong (−)</div>
                            <div class="col-span-1"></div>
                        </div>

                        <div id="marking-schemes" class="space-y-3">
                            <div class="marking-scheme-item grid grid-cols-12 gap-4 items-center bg-slate-50 border border-slate-200 rounded-xl px-4 py-3">
                                <div class="col-span-12 md:col-span-5">
                                    <div class="relative">
                                        <select name="marking_schemes[0][subject_id]"
                                            class="scheme-subject-select w-full appearance-none rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent pr-8" required>
                                            <option value="">Select Subject</option>
                                            @foreach($subjects as $subject)
                                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-span-6 md:col-span-3">
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-emerald-500 font-bold text-sm">+</span>
                                        <input type="number" name="marking_schemes[0][correct_marks]" step="1" min="0" value="4"
                                            class="w-full rounded-lg border border-slate-200 bg-white pl-7 pr-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent" required>
                                    </div>
                                </div>
                                <div class="col-span-5 md:col-span-3">
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-red-400 font-bold text-sm">−</span>
                                        <input type="number" name="marking_schemes[0][wrong_marks]" step="1" min="0" value="1"
                                            class="w-full rounded-lg border border-slate-200 bg-white pl-7 pr-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent" required>
                                    </div>
                                </div>
                                <div class="col-span-1 flex justify-end">
                                    <span class="text-slate-200 text-lg select-none">—</span>
                                </div>
                            </div>
                        </div>

                        <button type="button" id="add-marking-scheme"
                            class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-amber-600 hover:text-amber-800 border border-dashed border-amber-300 hover:border-amber-500 bg-amber-50 hover:bg-amber-100 rounded-xl px-4 py-2.5 transition w-full justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Another Subject Scheme
                        </button>
                    </div>
                </section>

                {{-- ═══ SECTION 5: QUESTIONS (hidden until subject selected) ═══ --}}
                <section id="sec-questions" class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 overflow-hidden scroll-mt-20" style="display:none; opacity:0;">
                    <div class="flex items-center gap-4 px-6 py-5 border-b border-slate-100">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-base font-semibold text-slate-800">Select Questions</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Only questions matching your selected subjects are shown</p>
                        </div>
                        <div class="flex items-center gap-2 bg-blue-50 border border-blue-200 rounded-xl px-4 py-2">
                            <span id="selected-count" class="text-xl font-bold text-blue-600 transition-colors duration-200">0</span>
                            <span class="text-sm text-blue-400">/ <span id="header-total">{{ old('total_questions', 10) }}</span></span>
                        </div>
                    </div>

                    <div class="p-6">

                        {{-- Active Subject Tags --}}
                        <div id="active-subject-tags" class="flex flex-wrap gap-2 mb-5"></div>

                        {{-- Filters --}}
                        <div class="flex flex-wrap gap-3 mb-5">
                            <div class="relative flex-1 min-w-[180px]">
                                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" id="filter_search" placeholder="Search questions…"
                                    class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition">
                            </div>
                            <div class="relative min-w-[180px]">
                                <select id="filter_subject"
                                    class="w-full appearance-none rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-400 pr-8">
                                    <option value="">All Allowed Subjects</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="relative min-w-[180px]">
                                <select id="filter_category"
                                    class="w-full appearance-none rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-400 pr-8">
                                    <option value="">All Categories</option>
                                    @foreach($examCategories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Two Panel Layout --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                            {{-- Available Questions --}}
                            <div class="border border-slate-200 rounded-2xl overflow-hidden flex flex-col">
                                <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Available Questions</span>
                                    <span id="available-count" class="text-xs text-slate-400">0 total</span>
                                </div>
                                <div id="available-questions" class="overflow-y-auto divide-y divide-slate-100" style="max-height:420px;">
                                    @forelse($questions as $question)
                                    <div class="question-item px-4 py-3.5 cursor-pointer transition-all duration-150 flex gap-3 items-start relative"
                                        data-question-id="{{ $question->id }}"
                                        data-subject-id="{{ $question->subject_id }}"
                                        data-category-id="{{ $question->exam_category_id }}"
                                        style="display:none;">
                                        <div class="shrink-0 mt-0.5">
                                            <div class="check-box w-5 h-5 rounded-md border-2 border-slate-300 flex items-center justify-center transition-all duration-150">
                                                <svg class="check-icon w-3 h-3 text-white hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm text-slate-800 leading-snug line-clamp-2 question-content">{!! $question->question_text !!}</div>
                                            <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                                <span class="inline-flex items-center text-xs bg-indigo-50 text-indigo-600 border border-indigo-100 rounded-full px-2 py-0.5 font-medium">
                                                    {{ $question->subject->name ?? 'N/A' }}
                                                </span>
                                                <span class="inline-flex items-center text-xs bg-slate-50 text-slate-500 border border-slate-200 rounded-full px-2 py-0.5">
                                                    {{ $question->examCategory->name ?? 'N/A' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="flex flex-col items-center justify-center py-16 text-center px-4">
                                        <svg class="w-12 h-12 text-slate-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="text-sm text-slate-400">No questions available</p>
                                    </div>
                                    @endforelse

                                    <div id="no-results-msg" class="hidden flex-col items-center justify-center py-16 text-center px-4">
                                        <svg class="w-10 h-10 text-slate-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                        <p class="text-sm text-slate-400">No questions match your filters</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Selected Questions --}}
                            <div id="selected-panel" class="border-2 border-dashed border-blue-200 rounded-2xl overflow-hidden flex flex-col bg-blue-50/30 transition-colors duration-300">
                                <div class="px-4 py-3 bg-blue-50 border-b border-blue-200 flex items-center justify-between">
                                    <span class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Selected Questions</span>
                                    <span class="text-xs font-bold text-blue-600">
                                        <span id="selected-count-2">0</span> added
                                    </span>
                                </div>
                                <div id="selected-questions-display" class="overflow-y-auto divide-y divide-blue-100 flex-1" style="max-height:420px;">
                                    <div id="empty-selected-state" class="flex flex-col items-center justify-center py-16 text-center px-4">
                                        <svg class="w-12 h-12 text-blue-100 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <p class="text-sm text-blue-300 font-medium">No questions selected yet</p>
                                        <p class="text-xs text-blue-200 mt-1">Click questions on the left to add</p>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- Hidden inputs --}}
                        <div id="selected-questions-inputs"></div>
                    </div>
                </section>

                {{-- ═══ BOTTOM SUBMIT BAR ═══ --}}
                <div class="flex items-center justify-between bg-white rounded-2xl border border-slate-200 shadow-sm px-6 py-4">
                    <p class="text-sm text-slate-400">
                        Fields marked <span class="text-red-500 font-semibold">*</span> are required
                    </p>
                    <div class="flex gap-3">
                        <a href="{{ route('teacher.exams.index') }}"
                            class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white text-sm font-semibold px-6 py-2.5 rounded-xl shadow-md shadow-indigo-200 transition-all duration-150">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Publish Exam
                        </button>
                    </div>
                </div>

            </form>
        </main>
    </div>
</div>

{{-- ═══ STYLES ═══ --}}
<style>
    html { scroll-behavior: smooth; }

    /* Selected state */
    .question-item.is-selected .check-box {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }
    .question-item.is-selected .check-icon { display: block; }
    .question-item.is-selected { background-color: #eff6ff; }
    .question-item:not(.is-selected):not(.is-blocked):hover { background-color: #f8fafc; }

    /* Blocked state when limit reached */
    .question-item.is-blocked {
        opacity: 0.38;
        cursor: not-allowed !important;
        pointer-events: none;
    }

    /* Active nav */
    .nav-link.is-active {
        color: #4f46e5 !important;
        background-color: #eef2ff !important;
        font-weight: 600;
    }
    .nav-link.is-active .nav-dot {
        background-color: #4f46e5 !important;
        color: white !important;
    }

    /* Shake animation */
    @keyframes shake {
        0%,100% { transform: translateX(0); }
        20%      { transform: translateX(-6px); }
        40%      { transform: translateX(6px); }
        60%      { transform: translateX(-4px); }
        80%      { transform: translateX(4px); }
    }
    .shake { animation: shake 0.4s ease; }

    /* Questions section reveal */
    #sec-questions { transition: opacity 0.3s ease, transform 0.3s ease; }
</style>

{{-- ═══ JAVASCRIPT ═══ --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    let selectedQuestions = new Set();
    let schemeIdx = 1;

    // Subject id → name map for tags
    const subjectMap = {};
    @foreach($subjects as $subject)
        subjectMap["{{ $subject->id }}"] = "{{ addslashes($subject->name) }}";
    @endforeach

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────
    function getTotal() {
        return parseInt(document.getElementById('total_questions').value) || 0;
    }

    function getAllowedSubjectIds() {
        const ids = new Set();
        document.querySelectorAll('#marking-schemes select[name*="subject_id"]').forEach(sel => {
            if (sel.value) ids.add(sel.value);
        });
        return ids;
    }

    // ─────────────────────────────────────────────
    // TOAST NOTIFICATION
    // ─────────────────────────────────────────────
    function showLimitToast(limit) {
        const existing = document.getElementById('limit-toast');
        if (existing) {
            // Bump animation if already showing
            existing.classList.remove('shake');
            void existing.offsetWidth;
            existing.classList.add('shake');
            return;
        }

        const toast = document.createElement('div');
        toast.id = 'limit-toast';
        toast.style.cssText = `
            position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(16px);
            z-index:9999; opacity:0;
            transition: opacity 0.25s ease, transform 0.25s ease;
        `;
        toast.innerHTML = `
            <div class="flex items-center gap-3 bg-slate-900 text-white text-sm font-medium px-5 py-3.5 rounded-2xl shadow-2xl border border-slate-700 min-w-[300px]">
                <div class="w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-white leading-none">Question limit reached</p>
                    <p class="text-xs text-slate-400 mt-1">Maximum <span class="text-amber-400 font-bold">${limit} question${limit > 1 ? 's' : ''}</span> allowed for this exam.</p>
                </div>
                <button onclick="dismissToast()" class="text-slate-500 hover:text-white transition shrink-0 ml-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        `;
        document.body.appendChild(toast);

        requestAnimationFrame(() => requestAnimationFrame(() => {
            toast.style.opacity   = '1';
            toast.style.transform = 'translateX(-50%) translateY(0)';
        }));

        // Auto-dismiss after 3.5s
        setTimeout(() => dismissToast(), 3500);
    }

    window.dismissToast = function () {
        const toast = document.getElementById('limit-toast');
        if (!toast) return;
        toast.style.opacity   = '0';
        toast.style.transform = 'translateX(-50%) translateY(16px)';
        setTimeout(() => toast.remove(), 300);
    };

    // ─────────────────────────────────────────────
    // SYNC UI
    // ─────────────────────────────────────────────
    function syncUI() {
        const count   = selectedQuestions.size;
        const total   = getTotal();
        const atLimit = total > 0 && count >= total;

        // Update counters
        const sidebarCount = document.getElementById('sidebar-count');
        const headerCount  = document.getElementById('selected-count');
        const panel2Count  = document.getElementById('selected-count-2');
        const limitLabel   = document.getElementById('limit-label');
        const headerTotal  = document.getElementById('header-total');

        [sidebarCount, headerCount, panel2Count].forEach(el => {
            if (!el) return;
            el.textContent = count;
        });
        if (headerTotal) headerTotal.textContent = total;

        // Color counters red at limit
        if (sidebarCount) {
            sidebarCount.className = `text-2xl font-bold transition-colors duration-200 ${atLimit ? 'text-red-500' : 'text-indigo-600'}`;
        }
        if (headerCount) {
            headerCount.className = `text-xl font-bold transition-colors duration-200 ${atLimit ? 'text-red-500' : 'text-blue-600'}`;
        }
        if (limitLabel) {
            limitLabel.classList.toggle('hidden', !atLimit);
        }

        // Sidebar total
        const st = document.getElementById('sidebar-total');
        if (st) st.textContent = total;

        // Progress bar
        const bar = document.getElementById('progress-bar');
        if (bar) {
            const pct = total > 0 ? Math.min((count / total) * 100, 100) : 0;
            bar.style.width = pct + '%';
            bar.className = `h-full rounded-full transition-all duration-300 ${atLimit ? 'bg-red-500' : 'bg-indigo-500'}`;
        }

        // Block/unblock unselected questions when at limit
        document.querySelectorAll('.question-item').forEach(item => {
            if (item.classList.contains('is-selected')) return;
            if (atLimit) {
                item.classList.add('is-blocked');
            } else {
                item.classList.remove('is-blocked');
            }
        });

        // Selected panel border flash red at limit
        const panel = document.getElementById('selected-panel');
        if (panel) {
            if (atLimit) {
                panel.classList.add('border-red-300', 'bg-red-50/20');
                panel.classList.remove('border-blue-200', 'bg-blue-50/30');
            } else {
                panel.classList.remove('border-red-300', 'bg-red-50/20');
                panel.classList.add('border-blue-200', 'bg-blue-50/30');
            }
        }

        // Hidden inputs
        const container = document.getElementById('selected-questions-inputs');
        container.innerHTML = '';
        selectedQuestions.forEach(id => {
            const inp = document.createElement('input');
            inp.type  = 'hidden';
            inp.name  = 'selected_questions[]';
            inp.value = id;
            container.appendChild(inp);
        });

        // Selected panel display
        const display    = document.getElementById('selected-questions-display');
        const emptyState = document.getElementById('empty-selected-state');
        display.querySelectorAll('.selected-entry').forEach(el => el.remove());

        if (count === 0) {
            emptyState.style.display = '';
            return;
        }
        emptyState.style.display = 'none';

        selectedQuestions.forEach(qId => {
            const src = document.querySelector(`[data-question-id="${qId}"]`);
            if (!src) return;
            const text = src.querySelector('.question-content').innerHTML;
            const tags = src.querySelector('.flex.items-center.gap-2').innerHTML;
            const div  = document.createElement('div');
            div.className = 'selected-entry px-4 py-3 flex gap-3 items-start hover:bg-blue-50/50 transition';
            div.innerHTML = `
                <div class="flex-1 min-w-0">
                    <div class="text-sm text-slate-800 leading-snug line-clamp-2">${text}</div>
                    <div class="flex items-center gap-2 mt-1.5 flex-wrap">${tags}</div>
                </div>
                <button type="button" onclick="deselect('${qId}')"
                    class="shrink-0 w-6 h-6 rounded-lg flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 transition mt-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>`;
            display.appendChild(div);
        });
    }

    window.deselect = function (qId) {
        selectedQuestions.delete(String(qId));
        const el = document.querySelector(`[data-question-id="${qId}"]`);
        if (el) {
            el.classList.remove('is-selected');
            el.classList.remove('is-blocked');
        }
        syncUI();
    };

    // ─────────────────────────────────────────────
    // QUESTION CLICK — with limit guard
    // ─────────────────────────────────────────────
    document.querySelectorAll('.question-item').forEach(item => {
        item.addEventListener('click', function () {
            const qId   = this.dataset.questionId;
            const total = getTotal();

            if (selectedQuestions.has(qId)) {
                // Deselect — always allowed
                selectedQuestions.delete(qId);
                this.classList.remove('is-selected');
                syncUI();
            } else {
                // Check limit before selecting
                if (total > 0 && selectedQuestions.size >= total) {
                    // Shake this item
                    this.classList.add('shake');
                    setTimeout(() => this.classList.remove('shake'), 500);
                    // Show toast
                    showLimitToast(total);
                    return; // BLOCK selection
                }
                selectedQuestions.add(qId);
                this.classList.add('is-selected');
                syncUI();
            }
        });
    });

    // Re-evaluate limit when total_questions changes
    document.getElementById('total_questions').addEventListener('input', syncUI);

    // ─────────────────────────────────────────────
    // SHOW/HIDE QUESTIONS SECTION
    // ─────────────────────────────────────────────
    function toggleQuestionsSection(allowed) {
        const section = document.getElementById('sec-questions');
        const navItem = document.getElementById('nav-questions');
        const hint    = document.getElementById('questions-hint');

        if (allowed.size > 0) {
            hint.style.display = 'none';
            navItem.classList.remove('hidden');
            navItem.style.display = 'flex';

            if (section.style.display === 'none' || section.style.display === '') {
                section.style.display = 'block';
                section.style.transform = 'translateY(12px)';
                section.style.opacity   = '0';
                requestAnimationFrame(() => requestAnimationFrame(() => {
                    section.style.opacity   = '1';
                    section.style.transform = 'translateY(0)';
                }));
            }
        } else {
            hint.style.display = '';
            navItem.classList.add('hidden');
            navItem.style.display = 'none';
            section.style.opacity   = '0';
            section.style.transform = 'translateY(12px)';
            setTimeout(() => { section.style.display = 'none'; }, 300);
        }
    }

    // ─────────────────────────────────────────────
    // SUBJECT TAGS
    // ─────────────────────────────────────────────
    function renderSubjectTags(allowed) {
        const container = document.getElementById('active-subject-tags');
        container.innerHTML = '';
        if (allowed.size === 0) return;

        const label = document.createElement('span');
        label.className = 'text-xs text-slate-500 font-medium self-center';
        label.textContent = 'Showing for:';
        container.appendChild(label);

        allowed.forEach(id => {
            const tag = document.createElement('span');
            tag.className = 'inline-flex items-center gap-1.5 text-xs font-semibold bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-full px-3 py-1';
            tag.innerHTML = `
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                </svg>
                ${subjectMap[id] || 'Unknown'}`;
            container.appendChild(tag);
        });
    }

    // ─────────────────────────────────────────────
    // APPLY QUESTION LOCK (based on marking scheme subjects)
    // ─────────────────────────────────────────────
    function applyQuestionLock() {
        const allowed = getAllowedSubjectIds();

        toggleQuestionsSection(allowed);
        renderSubjectTags(allowed);

        let visibleCount = 0;

        document.querySelectorAll('.question-item').forEach(item => {
            const subId     = item.dataset.subjectId;
            const isAllowed = allowed.size > 0 && allowed.has(subId);

            if (isAllowed) {
                item.style.display = '';
                visibleCount++;
            } else {
                // Deselect if was selected
                const qId = item.dataset.questionId;
                if (selectedQuestions.has(qId)) {
                    selectedQuestions.delete(qId);
                    item.classList.remove('is-selected');
                }
                item.classList.remove('is-blocked');
                item.style.display = 'none';
            }
        });

        document.getElementById('available-count').textContent = visibleCount + ' total';

        const noResults = document.getElementById('no-results-msg');
        noResults.style.display = (visibleCount === 0 && allowed.size > 0) ? 'flex' : 'none';

        filterQuestions();
        syncUI();
    }

    // ─────────────────────────────────────────────
    // MARKING SCHEMES
    // ─────────────────────────────────────────────
    function buildSubjectOptions() {
        let opts = '<option value="">Select Subject</option>';
        @foreach($subjects as $subject)
        opts += `<option value="{{ $subject->id }}">{{ addslashes($subject->name) }}</option>`;
        @endforeach
        return opts;
    }

    function attachSchemeListener(sel) {
        sel.addEventListener('change', applyQuestionLock);
    }

    attachSchemeListener(document.querySelector('#marking-schemes .scheme-subject-select'));

    document.getElementById('add-marking-scheme').addEventListener('click', function () {
        const i = schemeIdx++;
        const html = `
        <div class="marking-scheme-item grid grid-cols-12 gap-4 items-center bg-slate-50 border border-slate-200 rounded-xl px-4 py-3">
            <div class="col-span-12 md:col-span-5">
                <div class="relative">
                    <select name="marking_schemes[${i}][subject_id]"
                        class="scheme-subject-select w-full appearance-none rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent pr-8" required>
                        ${buildSubjectOptions()}
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="col-span-6 md:col-span-3">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-emerald-500 font-bold text-sm">+</span>
                    <input type="number" name="marking_schemes[${i}][correct_marks]" step="1" min="0" value="4"
                        class="w-full rounded-lg border border-slate-200 bg-white pl-7 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent" required>
                </div>
            </div>
            <div class="col-span-5 md:col-span-3">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-red-400 font-bold text-sm">−</span>
                    <input type="number" name="marking_schemes[${i}][wrong_marks]" step="1" min="0" value="1"
                        class="w-full rounded-lg border border-slate-200 bg-white pl-7 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent" required>
                </div>
            </div>
            <div class="col-span-1 flex justify-end">
                <button type="button" onclick="removeScheme(this)"
                    class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>`;

        document.getElementById('marking-schemes').insertAdjacentHTML('beforeend', html);
        const all = document.querySelectorAll('#marking-schemes .scheme-subject-select');
        attachSchemeListener(all[all.length - 1]);
    });

    window.removeScheme = function (btn) {
        btn.closest('.marking-scheme-item').remove();
        applyQuestionLock();
    };

    // ─────────────────────────────────────────────
    // FILTERS
    // ─────────────────────────────────────────────
    function filterQuestions() {
        const allowed = getAllowedSubjectIds();
        const sub = document.getElementById('filter_subject').value;
        const cat = document.getElementById('filter_category').value;
        const q   = document.getElementById('filter_search').value.toLowerCase().trim();
        let visible = 0;

        document.querySelectorAll('.question-item').forEach(item => {
            const subId     = item.dataset.subjectId;
            const isAllowed = allowed.size > 0 && allowed.has(subId);
            if (!isAllowed) { item.style.display = 'none'; return; }

            const matchSub = !sub || subId === sub;
            const matchCat = !cat || item.dataset.categoryId === cat;
            const matchQ   = !q   || item.querySelector('.question-content').textContent.toLowerCase().includes(q);
            const show     = matchSub && matchCat && matchQ;
            item.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        document.getElementById('available-count').textContent = visible + ' total';
        const noResults = document.getElementById('no-results-msg');
        noResults.style.display = (visible === 0 && allowed.size > 0) ? 'flex' : 'none';
    }

    ['filter_subject', 'filter_category'].forEach(id =>
        document.getElementById(id).addEventListener('change', filterQuestions));
    document.getElementById('filter_search').addEventListener('input', filterQuestions);

    // ─────────────────────────────────────────────
    // ACTIVE NAV (IntersectionObserver)
    // ─────────────────────────────────────────────
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.toggle('is-active', link.dataset.section === entry.target.id);
                });
            }
        });
    }, { threshold: 0.3 });
    document.querySelectorAll('section[id]').forEach(sec => observer.observe(sec));

    // ─────────────────────────────────────────────
    // FORM VALIDATION
    // ─────────────────────────────────────────────
    document.getElementById('examForm').addEventListener('submit', function (e) {
        if (selectedQuestions.size === 0) {
            e.preventDefault();
            document.getElementById('sec-questions').scrollIntoView({ behavior: 'smooth' });
            const panel = document.getElementById('selected-panel');
            if (panel) {
                panel.classList.add('border-red-400');
                setTimeout(() => panel.classList.remove('border-red-400'), 2500);
            }
        }
    });

    // ─────────────────────────────────────────────
    // INIT
    // ─────────────────────────────────────────────
    applyQuestionLock();
    syncUI();
});
</script>

@endsection
