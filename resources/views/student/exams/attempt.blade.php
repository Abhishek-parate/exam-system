@extends('layouts.student')

@section('title', 'Exam: ' . $attempt->exam->title)

@section('content')

<style>
    body { overflow-x: hidden; }

    .option-btn:hover  { border-color: #3b82f6; background: #eff6ff; }
    .option-selected   { border-color: #2563eb !important; background: #dbeafe !important; }

    .question-content img, .option-content img {
        max-width: 100%; height: auto; border-radius: 6px;
        margin: 8px 0; display: inline-block;
    }
    .question-content p, .option-content p { margin: 0; line-height: 1.6; }

    .timer-warning { color: #d97706; }
    .timer-danger  { color: #dc2626; animation: blink 1s infinite; }

    @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }

    .nav-time-badge {
        display: block; font-size: 9px; font-weight: 600;
        line-height: 1; margin-top: 2px; opacity: 0.75;
    }

    .nav-btn-active { outline: 2px solid #6366f1; outline-offset: 1px; }

    /* Subjective textarea */
    .subj-textarea {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #d8b4fe;
        border-radius: 10px;
        background: #faf5ff;
        color: #1f2937;
        font-size: 14px;
        line-height: 1.6;
        resize: vertical;
        min-height: 110px;
        font-family: inherit;
        transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
    }
    .subj-textarea:focus {
        outline: none;
        border-color: #7c3aed;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.12);
    }
    .subj-textarea.answered {
        border-color: #7c3aed;
        background: #ffffff;
    }

    /* Subjective wrapper */
    .subj-wrapper {
        padding: 16px;
        background: #f5f3ff;
        border: 2px solid #c4b5fd;
        border-radius: 12px;
    }
    .subj-label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 700;
        color: #6d28d9;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 10px;
    }
    .subj-hint {
        font-size: 11px;
        color: #8b5cf6;
        margin-top: 6px;
    }
</style>

<div class="flex flex-col h-screen overflow-hidden bg-gray-100">

    {{-- TOP BAR --}}
    <div class="bg-white border-b border-gray-200 shadow-sm shrink-0 z-50">
        <div class="flex items-center justify-between px-4 py-3">
            <div class="min-w-0">
                <h1 class="text-base font-bold text-gray-900 truncate">{{ $attempt->exam->title }}</h1>
                <p class="text-xs text-gray-500">{{ $attempt->exam->examCategory?->name ?? 'General' }}</p>
            </div>
            <div class="flex items-center gap-4 shrink-0">
                <div class="text-center">
                    <div id="timer" class="text-xl font-bold font-mono text-green-600 tabular-nums">--:--</div>
                    <p class="text-xs text-gray-400">Time Left</p>
                </div>
                <div class="text-center hidden sm:block">
                    <div id="question-timer-strip" class="text-lg font-bold font-mono text-indigo-500 tabular-nums">0:00</div>
                    <p class="text-xs text-gray-400">This Question</p>
                </div>
                <button onclick="confirmSubmit()"
                        class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition text-sm">
                    Submit Exam
                </button>
            </div>
        </div>
        <div class="h-1 bg-gray-100">
            <div id="progress-bar" class="h-1 bg-blue-500 transition-all duration-500" style="width:0%"></div>
        </div>
    </div>

    {{-- BODY --}}
    <div class="flex flex-1 overflow-hidden">

        {{-- Question Area --}}
        <div class="flex-1 overflow-y-auto p-4" id="question-scroll-area">

            @foreach($questions as $index => $question)
            @php
                $answer = $attempt->answers->firstWhere('question_id', $question->id);

                /*
                 * Robust type detection:
                 * 1. Use stored question_type if available and set
                 * 2. If null/empty: infer from whether options exist
                 *    - No options => subjective (text input)
                 *    - Has options => mcq (radio buttons)
                 */
                $storedType   = $question->question_type ?? null;
                $questionType = $storedType ?: ($question->options->isEmpty() ? 'subjective' : 'mcq');
            @endphp

            <div id="question-{{ $question->id }}"
                 class="question-block bg-white rounded-xl shadow p-5 {{ $index > 0 ? 'hidden' : '' }}"
                 data-question-id="{{ $question->id }}"
                 data-index="{{ $index }}"
                 data-question-type="{{ $questionType }}">

                {{-- Q Header --}}
                <div class="flex justify-between items-start mb-3 flex-wrap gap-2">
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="text-sm font-semibold text-gray-500">
                            Question {{ $index + 1 }} / {{ $questions->count() }}
                        </span>
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-500 bg-indigo-50 border border-indigo-100 rounded-full px-2.5 py-0.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="q-inline-timer" data-qid="{{ $question->id }}">0s</span>
                        </span>
                        @if($questionType === 'subjective')
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-purple-600 bg-purple-50 border border-purple-200 rounded-full px-2.5 py-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Subjective
                            </span>
                        @endif
                    </div>
                    <div class="flex gap-2 flex-wrap">
                        @php $dn = strtolower($question->difficulty?->name ?? ''); @endphp
                        <span class="px-2 py-0.5 text-xs rounded-full font-medium
                            @if($dn === 'easy') bg-green-100 text-green-700
                            @elseif($dn === 'medium') bg-yellow-100 text-yellow-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ $question->difficulty?->name ?? 'N/A' }}
                        </span>
                        <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-700 rounded-full font-medium">+{{ $question->marks }}</span>
                        @if($question->negative_marks > 0)
                            <span class="px-2 py-0.5 text-xs bg-red-50 text-red-600 rounded-full font-medium">-{{ $question->negative_marks }}</span>
                        @endif
                    </div>
                </div>

                {{-- Question text --}}
                <div class="question-content text-gray-900 text-sm font-medium mb-5 leading-relaxed">
                    {!! $question->question_text !!}
                </div>

                {{-- ====================================================
                     ANSWER INPUT -- subjective textarea OR mcq radio
                ==================================================== --}}
                @if($questionType === 'subjective')

                    {{-- SUBJECTIVE TEXT INPUT --}}
                    <div class="subj-wrapper">
                        <div class="subj-label">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Write your answer below
                        </div>
                        <textarea
                            id="text-answer-{{ $question->id }}"
                            oninput="handleTextAnswer({{ $question->id }}, this.value)"
                            placeholder="Type your answer here..."
                            class="subj-textarea {{ ($answer && $answer->text_answer && trim($answer->text_answer) !== '') ? 'answered' : '' }}"
                        >{{ ($answer && $answer->text_answer) ? $answer->text_answer : '' }}</textarea>
                        <p class="subj-hint">Your answer is saved automatically as you type.</p>
                    </div>

                @else

                    {{-- MCQ RADIO OPTIONS --}}
                    @if($question->options->count() > 0)
                        <div class="space-y-2">
                            @foreach($question->options as $option)
                            <div id="option-label-{{ $option->id }}"
                                 onclick="selectOption({{ $question->id }}, {{ $option->id }}, this)"
                                 class="option-btn flex items-start gap-3 p-3 border-2 rounded-lg cursor-pointer transition
                                        {{ ($answer && $answer->selected_option_id == $option->id) ? 'option-selected' : 'border-gray-200' }}">
                                <input type="radio"
                                       name="q{{ $question->id }}"
                                       value="{{ $option->id }}"
                                       {{ ($answer && $answer->selected_option_id == $option->id) ? 'checked' : '' }}
                                       class="mt-0.5 shrink-0 w-4 h-4 text-blue-600 pointer-events-none">
                                <div class="option-content text-gray-800 text-sm leading-relaxed min-w-0 flex-1">
                                    {!! $option->option_text !!}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Safety net: no options found at all --}}
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-yellow-700 text-sm">No answer options available for this question.</p>
                        </div>
                    @endif

                @endif
                {{-- ==================================================== --}}

                {{-- Footer: review + clear --}}
                <div class="mt-4 flex justify-between items-center flex-wrap gap-2">
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-600 select-none"
                           onclick="toggleReview({{ $question->id }}, this)">
                        <input type="checkbox"
                               id="review-{{ $question->id }}"
                               {{ ($answer && $answer->is_marked_for_review) ? 'checked' : '' }}
                               class="rounded w-4 h-4 pointer-events-none">
                        <span>Mark for Review</span>
                    </label>
                    <button onclick="clearAnswer({{ $question->id }})"
                            class="text-xs text-gray-400 hover:text-red-500 transition">
                        Clear Answer
                    </button>
                </div>

                {{-- Prev / Next --}}
                <div class="mt-4 flex justify-between items-center border-t border-gray-100 pt-4">
                    <button onclick="goToQuestion({{ $index - 1 }})"
                            {{ $index === 0 ? 'disabled' : '' }}
                            class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition">
                        &larr; Prev
                    </button>
                    <span class="text-xs text-gray-400">{{ $index + 1 }} / {{ $questions->count() }}</span>
                    @if($index < $questions->count() - 1)
                        <button onclick="goToQuestion({{ $index + 1 }})"
                                class="px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                            Next &rarr;
                        </button>
                    @else
                        <button onclick="confirmSubmit()"
                                class="px-4 py-2 text-sm font-medium bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                            Submit &rarr;
                        </button>
                    @endif
                </div>

            </div>
            @endforeach

        </div>

        {{-- NAVIGATOR SIDEBAR --}}
        <div class="w-56 shrink-0 overflow-y-auto bg-white border-l border-gray-200 p-3">
            <h3 class="text-xs font-bold text-gray-600 mb-3 uppercase tracking-wide">Navigator</h3>

            <div class="grid grid-cols-5 gap-1.5 mb-4">
                @foreach($questions as $index => $question)
                @php
                    $ans        = $attempt->answers->firstWhere('question_id', $question->id);
                    $isAnswered = $ans && ($ans->selected_option_id || ($ans->text_answer && trim($ans->text_answer) !== ''));
                    $isReview   = $ans && $ans->is_marked_for_review;
                @endphp
                <button id="nav-btn-{{ $question->id }}"
                        onclick="goToQuestion({{ $index }})"
                        title="Q{{ $index + 1 }}"
                        class="h-10 w-full text-xs font-bold rounded-lg border-2 transition flex flex-col items-center justify-center leading-none
                               {{ $isReview
                                   ? 'bg-yellow-100 border-yellow-400 text-yellow-800'
                                   : ($isAnswered
                                       ? 'bg-green-100 border-green-400 text-green-800'
                                       : 'bg-gray-50 border-gray-200 text-gray-600') }}
                               {{ $index === 0 ? 'nav-btn-active' : '' }}">
                    <span>{{ $index + 1 }}</span>
                    <span class="nav-time-badge" id="nav-time-{{ $question->id }}">0s</span>
                </button>
                @endforeach
            </div>

            {{-- Legend --}}
            <div class="space-y-1 text-xs text-gray-500 border-t border-gray-100 pt-3 mb-4">
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded bg-green-100 border border-green-400 shrink-0"></div>
                    <span>Answered</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded bg-yellow-100 border border-yellow-400 shrink-0"></div>
                    <span>For Review</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded bg-gray-50 border border-gray-200 shrink-0"></div>
                    <span>Not Answered</span>
                </div>
                <div class="flex items-center gap-1.5 mt-1 pt-1 border-t border-gray-100">
                    <svg class="w-3 h-3 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-indigo-400">Time per question</span>
                </div>
            </div>

            {{-- Summary --}}
            <div class="bg-gray-50 rounded-lg p-2 text-xs space-y-1 mb-4">
                <div class="flex justify-between">
                    <span class="text-gray-500">Answered</span>
                    <span id="summary-answered" class="font-bold text-green-600">0</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Unanswered</span>
                    <span id="summary-unanswered" class="font-bold text-red-500">{{ $questions->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">For Review</span>
                    <span id="summary-review" class="font-bold text-yellow-600">0</span>
                </div>
                <div class="flex justify-between border-t border-gray-200 pt-1 mt-1">
                    <span class="text-gray-500">Time Used</span>
                    <span id="summary-time-used" class="font-bold text-indigo-500">0s</span>
                </div>
            </div>

            <button onclick="confirmSubmit()"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition text-xs">
                Submit Exam
            </button>
        </div>

    </div>
</div>

<script>
    var ATTEMPT_TOKEN   = '{{ $attempt->attempt_token }}';
    var TOTAL_QUESTIONS = {{ $questions->count() }};
    var REMAINING_SECS  = {{ max(0, $attempt->getRemainingTimeSeconds()) }};
    var CSRF_TOKEN      = '{{ csrf_token() }}';
    var URL_SAVE   = "{{ url('student/exams/' . $attempt->attempt_token . '/save-answer') }}";
    var URL_TRACK  = "{{ url('student/exams/' . $attempt->attempt_token . '/track-time') }}";
    var URL_STATUS = "{{ url('student/exams/' . $attempt->attempt_token . '/status') }}";
    var URL_SUBMIT = "{{ url('student/exams/' . $attempt->attempt_token . '/submit') }}";

    var currentIndex     = 0;
    var timerInterval    = null;
    var questionIds      = [];
    var questionTimes    = {};
    var currentQStart    = Date.now();
    var textDebounce     = {};

    document.querySelectorAll('.question-block').forEach(function (el) {
        var qId = parseInt(el.dataset.questionId);
        questionIds.push(qId);
        questionTimes[qId] = 0;
    });

    document.addEventListener('DOMContentLoaded', function () {
        startTimer();
        updateSummary();
        updateProgressBar();
        startStatusPoll();
        startQTimerDisplay();
    });

    // ---- Exam countdown ------------------------------------------
    function startTimer() {
        var s = REMAINING_SECS;
        renderTimer(s);
        timerInterval = setInterval(function () {
            s--;
            renderTimer(s);
            if (s <= 0) { clearInterval(timerInterval); autoSubmit(); }
        }, 1000);
    }

    function renderTimer(s) {
        if (s < 0) s = 0;
        var h = Math.floor(s / 3600), m = Math.floor((s % 3600) / 60), sec = s % 60;
        var str = h > 0 ? pad(h) + ':' + pad(m) + ':' + pad(sec) : pad(m) + ':' + pad(sec);
        var el = document.getElementById('timer');
        el.textContent = str;
        el.className = 'text-xl font-bold font-mono tabular-nums ' +
            (s <= 300 ? 'timer-danger' : s <= 600 ? 'timer-warning' : 'text-green-600');
    }

    function pad(n) { return n < 10 ? '0' + n : '' + n; }

    // ---- Per-question timer display ------------------------------
    function startQTimerDisplay() {
        setInterval(function () {
            var qId = questionIds[currentIndex];
            if (!qId) return;
            var elapsed = Math.floor((Date.now() - currentQStart) / 1000);
            var total   = questionTimes[qId] + elapsed;

            var strip = document.getElementById('question-timer-strip');
            if (strip) strip.textContent = fmtTime(total);

            var badge = document.querySelector('.q-inline-timer[data-qid="' + qId + '"]');
            if (badge) badge.textContent = fmtTime(total);

            var navBadge = document.getElementById('nav-time-' + qId);
            if (navBadge) navBadge.textContent = fmtTime(total);

            var used = 0;
            for (var id in questionTimes) { used += questionTimes[id]; }
            used += elapsed;
            var su = document.getElementById('summary-time-used');
            if (su) su.textContent = fmtTime(used);
        }, 1000);
    }

    function fmtTime(sec) {
        if (sec < 60)   return sec + 's';
        if (sec < 3600) return Math.floor(sec / 60) + 'm ' + (sec % 60) + 's';
        return Math.floor(sec / 3600) + 'h ' + Math.floor((sec % 3600) / 60) + 'm';
    }

    function flushCurrentQTime() {
        var qId = questionIds[currentIndex];
        if (!qId) return 0;
        var delta = Math.floor((Date.now() - currentQStart) / 1000);
        if (delta > 0) {
            questionTimes[qId] = (questionTimes[qId] || 0) + delta;
            trackTime(qId, delta);
        }
        return delta;
    }

    // ---- Navigation ----------------------------------------------
    function goToQuestion(idx) {
        if (idx < 0 || idx >= TOTAL_QUESTIONS) return;
        flushCurrentQTime();

        document.querySelectorAll('.question-block').forEach(function (el) { el.classList.add('hidden'); });

        var oldBtn = document.getElementById('nav-btn-' + questionIds[currentIndex]);
        if (oldBtn) oldBtn.classList.remove('nav-btn-active');

        document.querySelectorAll('.question-block')[idx].classList.remove('hidden');
        var sa = document.getElementById('question-scroll-area');
        if (sa) sa.scrollTo({ top: 0, behavior: 'smooth' });

        var newBtn = document.getElementById('nav-btn-' + questionIds[idx]);
        if (newBtn) newBtn.classList.add('nav-btn-active');

        currentIndex  = idx;
        currentQStart = Date.now();
        updateProgressBar();
    }

    // ---- MCQ option select ---------------------------------------
    function selectOption(qId, optId, clicked) {
        var container = document.getElementById('question-' + qId);
        container.querySelectorAll('[id^="option-label-"]').forEach(function (el) {
            el.classList.remove('option-selected');
            el.classList.add('border-gray-200');
        });
        clicked.classList.add('option-selected');
        clicked.classList.remove('border-gray-200');
        clicked.querySelector('input[type=radio]').checked = true;
        updateNavBtn(qId, 'answered');
        saveAnswer(qId, optId, undefined);
    }

    // ---- Clear answer (both types) --------------------------------
    function clearAnswer(qId) {
        var container = document.getElementById('question-' + qId);
        var qType     = container.dataset.questionType || 'mcq';

        if (qType === 'subjective') {
            var ta = document.getElementById('text-answer-' + qId);
            if (ta) { ta.value = ''; ta.classList.remove('answered'); }
            saveTextAnswer(qId, '');
        } else {
            container.querySelectorAll('[id^="option-label-"]').forEach(function (el) {
                el.classList.remove('option-selected'); el.classList.add('border-gray-200');
            });
            container.querySelectorAll('input[type=radio]').forEach(function (r) { r.checked = false; });
            saveAnswer(qId, null, undefined);
        }
        updateNavBtn(qId, 'unanswered');
    }

    // ---- Toggle review -------------------------------------------
    function toggleReview(qId, lbl) {
        setTimeout(function () {
            var cb        = document.getElementById('review-' + qId);
            var isChecked = cb.checked;
            var container = document.getElementById('question-' + qId);
            var qType     = container.dataset.questionType || 'mcq';
            var hasAnswer = false;

            if (qType === 'subjective') {
                var ta = document.getElementById('text-answer-' + qId);
                hasAnswer = ta && ta.value.trim().length > 0;
            } else {
                hasAnswer = !!container.querySelector('input[type=radio]:checked');
            }
            updateNavBtn(qId, isChecked ? 'review' : (hasAnswer ? 'answered' : 'unanswered'));
            saveAnswer(qId, undefined, isChecked);
        }, 10);
    }

    // ---- Subjective text answer ----------------------------------
    function handleTextAnswer(qId, text) {
        var ta = document.getElementById('text-answer-' + qId);
        if (ta) {
            if (text.trim().length > 0) {
                ta.classList.add('answered');
                updateNavBtn(qId, 'answered');
            } else {
                ta.classList.remove('answered');
                updateNavBtn(qId, 'unanswered');
            }
        }
        clearTimeout(textDebounce[qId]);
        textDebounce[qId] = setTimeout(function () {
            saveTextAnswer(qId, text);
        }, 800);
    }

    function saveTextAnswer(qId, text) {
        fetch(URL_SAVE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            body: JSON.stringify({ question_id: qId, text_answer: text })
        }).catch(function () {});
    }

    // ---- AJAX save MCQ -------------------------------------------
    function saveAnswer(qId, optId, isReview) {
        var body = { question_id: qId };
        if (optId    !== undefined) body.option_id            = optId;
        if (isReview !== undefined) body.is_marked_for_review = isReview;
        fetch(URL_SAVE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            body: JSON.stringify(body)
        }).catch(function () {});
    }

    // ---- Track time ----------------------------------------------
    function trackTime(qId, delta) {
        if (delta <= 0) return;
        fetch(URL_TRACK, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            body: JSON.stringify({ question_id: qId, time_spent: delta })
        }).catch(function () {});
    }

    setInterval(function () {
        var qId = questionIds[currentIndex];
        if (!qId) return;
        var delta = Math.floor((Date.now() - currentQStart) / 1000);
        if (delta > 0) {
            questionTimes[qId] = (questionTimes[qId] || 0) + delta;
            trackTime(qId, delta);
            currentQStart = Date.now();
        }
    }, 30000);

    function startStatusPoll() {
        setInterval(function () {
            fetch(URL_STATUS, { headers: { 'Accept': 'application/json' } })
                .then(function (r) { return r.json(); })
                .then(function (d) {
                    if (d.time_expired) { clearInterval(timerInterval); window.location.href = d.redirect_url; }
                }).catch(function () {});
        }, 30000);
    }

    // ---- Submit --------------------------------------------------
    function confirmSubmit() {
        updateSummary();
        var answered   = parseInt(document.getElementById('summary-answered').textContent)   || 0;
        var unanswered = parseInt(document.getElementById('summary-unanswered').textContent) || 0;
        var review     = parseInt(document.getElementById('summary-review').textContent)     || 0;
        if (confirm('Submit the exam?\n\nAnswered: ' + answered + '\nUnanswered: ' + unanswered + '\nFor Review: ' + review)) {
            doSubmit();
        }
    }

    function autoSubmit() { alert('Time is up! Submitting your exam now.'); doSubmit(); }

    function doSubmit() {
        clearInterval(timerInterval);
        flushCurrentQTime();
        fetch(URL_SUBMIT, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        }).then(function (r) { return r.json(); })
          .then(function (d) { if (d.success) window.location.href = d.redirect_url; else alert(d.message || 'Submission failed.'); })
          .catch(function () { alert('Network error. Please try again.'); });
    }

    // ---- UI helpers ----------------------------------------------
    function updateNavBtn(qId, state) {
        var btn = document.getElementById('nav-btn-' + qId);
        if (!btn) return;
        var isActive = btn.classList.contains('nav-btn-active');
        btn.className = 'h-10 w-full text-xs font-bold rounded-lg border-2 transition flex flex-col items-center justify-center leading-none ';
        if      (state === 'review')   btn.className += 'bg-yellow-100 border-yellow-400 text-yellow-800';
        else if (state === 'answered') btn.className += 'bg-green-100 border-green-400 text-green-800';
        else                           btn.className += 'bg-gray-50 border-gray-200 text-gray-600';
        if (isActive) btn.classList.add('nav-btn-active');
        updateSummary();
    }

    function updateSummary() {
        var allBtns = document.querySelectorAll('[id^="nav-btn-"]');
        var ans = 0, rev = 0;
        allBtns.forEach(function (b) {
            if (b.classList.contains('bg-green-100'))  ans++;
            if (b.classList.contains('bg-yellow-100')) rev++;
        });
        document.getElementById('summary-answered').textContent   = ans;
        document.getElementById('summary-unanswered').textContent = TOTAL_QUESTIONS - ans - rev;
        document.getElementById('summary-review').textContent     = rev;
    }

    function updateProgressBar() {
        document.getElementById('progress-bar').style.width = ((currentIndex + 1) / TOTAL_QUESTIONS * 100) + '%';
    }

    window.addEventListener('beforeunload', function (e) {
        flushCurrentQTime();
        e.preventDefault();
        e.returnValue = 'Your exam is in progress. Leave?';
    });
</script>

@endsection