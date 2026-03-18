@extends('layouts.student')

@section('title', 'Exam: ' . $attempt->exam->title)

@section('content')
{{-- Override padding from student layout --}}
<style>
    /* Remove default layout padding for exam page */
    body { overflow-x: hidden; }

    .option-btn:hover  { border-color: #3b82f6; background: #eff6ff; }
    .option-selected   { border-color: #2563eb !important; background: #dbeafe !important; }

    /* Render images inside questions properly */
    .question-content img,
    .option-content img {
        max-width: 100%;
        height: auto;
        border-radius: 6px;
        margin: 8px 0;
        display: inline-block;
    }

    /* Clean up stray <p> tags from question HTML */
    .question-content p,
    .option-content p {
        margin: 0;
        line-height: 1.6;
    }

    .timer-warning { color: #d97706; }
    .timer-danger  { color: #dc2626; animation: pulse 1s infinite; }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50%       { opacity: 0.6; }
    }
</style>

<div class="flex flex-col h-screen overflow-hidden bg-gray-100">

    {{-- ===== TOP BAR ===== --}}
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

    {{-- ===== BODY ===== --}}
    <div class="flex flex-1 overflow-hidden">

        {{-- Question Area --}}
        <div class="flex-1 overflow-y-auto p-4">
            @foreach($questions as $index => $question)
            @php $answer = $attempt->answers->firstWhere('question_id', $question->id); @endphp

            <div id="question-{{ $question->id }}"
                 class="question-block bg-white rounded-xl shadow p-5 {{ $index > 0 ? 'hidden' : '' }}"
                 data-question-id="{{ $question->id }}"
                 data-index="{{ $index }}">

                {{-- Q Header --}}
                <div class="flex justify-between items-start mb-3 flex-wrap gap-2">
                    <span class="text-sm font-semibold text-gray-500">
                        Question {{ $index + 1 }} / {{ $questions->count() }}
                    </span>
                    <div class="flex gap-2 flex-wrap">
                        @php $dn = strtolower($question->difficulty?->name ?? ''); @endphp
                        <span class="px-2 py-0.5 text-xs rounded-full font-medium
                            @if($dn==='easy') bg-green-100 text-green-700
                            @elseif($dn==='medium') bg-yellow-100 text-yellow-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ $question->difficulty?->name ?? 'N/A' }}
                        </span>
                        <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-700 rounded-full font-medium">
                            +{{ $question->marks }}
                        </span>
                        @if($question->negative_marks > 0)
                        <span class="px-2 py-0.5 text-xs bg-red-50 text-red-600 rounded-full font-medium">
                            -{{ $question->negative_marks }}
                        </span>
                        @endif
                    </div>
                </div>

                {{-- ✅ Question text — use {!! !!} to render HTML/images --}}
                <div class="question-content text-gray-900 text-sm font-medium mb-5 leading-relaxed">
                    {!! $question->question_text !!}
                </div>

                {{-- ✅ Options — use {!! !!} to render HTML/images --}}
                <div class="space-y-2">
                    @foreach($question->options as $option)
                    <div id="option-label-{{ $option->id }}"
                         onclick="selectOption({{ $question->id }}, {{ $option->id }}, this)"
                         class="option-btn flex items-start gap-3 p-3 border-2 rounded-lg cursor-pointer transition
                                {{ $answer && $answer->selected_option_id == $option->id ? 'option-selected' : 'border-gray-200' }}">
                        <input type="radio"
                               name="q{{ $question->id }}"
                               value="{{ $option->id }}"
                               {{ $answer && $answer->selected_option_id == $option->id ? 'checked' : '' }}
                               class="mt-0.5 shrink-0 w-4 h-4 text-blue-600 pointer-events-none">
                        {{-- ✅ Option text — render HTML/images --}}
                        <div class="option-content text-gray-800 text-sm leading-relaxed min-w-0 flex-1">
                            {!! $option->option_text !!}
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Footer --}}
                <div class="mt-4 flex justify-between items-center flex-wrap gap-2">
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-600 select-none"
                           onclick="toggleReview({{ $question->id }}, this)">
                        <input type="checkbox"
                               id="review-{{ $question->id }}"
                               {{ $answer && $answer->is_marked_for_review ? 'checked' : '' }}
                               class="rounded w-4 h-4 pointer-events-none">
                        <span>Mark for Review</span>
                    </label>
                    <button onclick="clearAnswer({{ $question->id }})"
                            class="text-xs text-gray-400 hover:text-red-500 transition">
                        Clear Answer
                    </button>
                </div>

                {{-- Navigation --}}
                <div class="mt-4 flex justify-between items-center border-t border-gray-100 pt-4">
                    <button onclick="goToQuestion({{ $index - 1 }})"
                            {{ $index === 0 ? 'disabled' : '' }}
                            class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300
                                   hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition">
                        ← Prev
                    </button>
                    <span class="text-xs text-gray-400">{{ $index + 1 }} / {{ $questions->count() }}</span>
                    @if($index < $questions->count() - 1)
                    <button onclick="goToQuestion({{ $index + 1 }})"
                            class="px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        Next →
                    </button>
                    @else
                    <button onclick="confirmSubmit()"
                            class="px-4 py-2 text-sm font-medium bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                        Submit →
                    </button>
                    @endif
                </div>

            </div>
            @endforeach
        </div>

        {{-- ===== NAVIGATOR SIDEBAR ===== --}}
        <div class="w-56 shrink-0 overflow-y-auto bg-white border-l border-gray-200 p-3">
            <h3 class="text-xs font-bold text-gray-600 mb-3 uppercase tracking-wide">Navigator</h3>

            <div class="grid grid-cols-5 gap-1.5 mb-4">
                @foreach($questions as $index => $question)
                @php
                    $ans = $attempt->answers->firstWhere('question_id', $question->id);
                    $isAnswered = $ans && $ans->selected_option_id;
                    $isReview   = $ans && $ans->is_marked_for_review;
                @endphp
                <button id="nav-btn-{{ $question->id }}"
                        onclick="goToQuestion({{ $index }})"
                        class="w-9 h-9 text-xs font-bold rounded-lg border-2 transition
                               {{ $isReview   ? 'bg-yellow-100 border-yellow-400 text-yellow-800' :
                                  ($isAnswered ? 'bg-green-100 border-green-400 text-green-800' :
                                                 'bg-gray-50 border-gray-200 text-gray-600') }}">
                    {{ $index + 1 }}
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
    var URL_SAVE        = "{{ url('student/exams/' . $attempt->attempt_token . '/save-answer') }}";
    var URL_TRACK       = "{{ url('student/exams/' . $attempt->attempt_token . '/track-time') }}";
    var URL_STATUS      = "{{ url('student/exams/' . $attempt->attempt_token . '/status') }}";
    var URL_SUBMIT      = "{{ url('student/exams/' . $attempt->attempt_token . '/submit') }}";

    var currentIndex   = 0;
    var timeOnQuestion = 0;
    var timerInterval  = null;
    var questionIds    = [];

    document.querySelectorAll('.question-block').forEach(function(el) {
        questionIds.push(parseInt(el.dataset.questionId));
    });

    // ── Timer ──────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        startTimer();
        updateSummary();
        updateProgressBar();
        startStatusPoll();
    });

    function startTimer() {
        var s = REMAINING_SECS;
        renderTimer(s);
        timerInterval = setInterval(function() {
            s--;
            renderTimer(s);
            if (s <= 0) { clearInterval(timerInterval); autoSubmit(); }
        }, 1000);
    }

    function renderTimer(s) {
        if (s < 0) s = 0;
        var h = Math.floor(s / 3600);
        var m = Math.floor((s % 3600) / 60);
        var sec = s % 60;
        var str = h > 0 ? pad(h)+':'+pad(m)+':'+pad(sec) : pad(m)+':'+pad(sec);
        var el = document.getElementById('timer');
        el.textContent = str;
        el.className = 'text-xl font-bold font-mono tabular-nums ' +
            (s <= 300 ? 'timer-danger' : s <= 600 ? 'timer-warning' : 'text-green-600');
    }

    function pad(n) { return n < 10 ? '0'+n : ''+n; }

    // ── Navigation ─────────────────────────────────────────
    function goToQuestion(idx) {
        if (idx < 0 || idx >= TOTAL_QUESTIONS) return;
        if (questionIds[currentIndex]) trackTime(questionIds[currentIndex], timeOnQuestion);
        timeOnQuestion = 0;
        document.querySelectorAll('.question-block').forEach(function(el) {
            el.classList.add('hidden');
        });
        var blocks = document.querySelectorAll('.question-block');
        blocks[idx].classList.remove('hidden');
        blocks[idx].closest('.overflow-y-auto, .flex-1')?.scrollTo({ top: 0, behavior: 'smooth' });
        currentIndex = idx;
        updateProgressBar();
    }

    setInterval(function() { timeOnQuestion++; }, 1000);

    // ── Select option ──────────────────────────────────────
    function selectOption(qId, optId, clicked) {
        var container = document.getElementById('question-' + qId);
        container.querySelectorAll('[id^="option-label-"]').forEach(function(el) {
            el.classList.remove('option-selected');
            el.classList.add('border-gray-200');
        });
        clicked.classList.add('option-selected');
        clicked.classList.remove('border-gray-200');
        clicked.querySelector('input[type=radio]').checked = true;

        updateNavBtn(qId, 'answered');
        saveAnswer(qId, optId, undefined);
    }

    function clearAnswer(qId) {
        var container = document.getElementById('question-' + qId);
        container.querySelectorAll('[id^="option-label-"]').forEach(function(el) {
            el.classList.remove('option-selected');
            el.classList.add('border-gray-200');
        });
        container.querySelectorAll('input[type=radio]').forEach(function(r) { r.checked = false; });
        updateNavBtn(qId, 'unanswered');
        saveAnswer(qId, null, undefined);
    }

    function toggleReview(qId, lbl) {
        setTimeout(function() {
            var cb = document.getElementById('review-' + qId);
            var isChecked = cb.checked;
            var container = document.getElementById('question-' + qId);
            var hasAnswer = !!container.querySelector('input[type=radio]:checked');
            updateNavBtn(qId, isChecked ? 'review' : (hasAnswer ? 'answered' : 'unanswered'));
            saveAnswer(qId, undefined, isChecked);
        }, 10);
    }

    // ── AJAX ───────────────────────────────────────────────
    function saveAnswer(qId, optId, isReview) {
        var body = { question_id: qId };
        if (optId   !== undefined) body.option_id            = optId;
        if (isReview !== undefined) body.is_marked_for_review = isReview;
        fetch(URL_SAVE, {
            method: 'POST',
            headers: { 'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json' },
            body: JSON.stringify(body)
        }).catch(function(){});
    }

    function trackTime(qId, t) {
        if (t <= 0) return;
        fetch(URL_TRACK, {
            method: 'POST',
            headers: { 'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json' },
            body: JSON.stringify({ question_id: qId, time_spent: t })
        }).catch(function(){});
    }

    function startStatusPoll() {
        setInterval(function() {
            fetch(URL_STATUS, { headers: { 'Accept':'application/json' } })
            .then(function(r){ return r.json(); })
            .then(function(d){ if (d.time_expired) { clearInterval(timerInterval); window.location.href = d.redirect_url; } })
            .catch(function(){});
        }, 30000);
    }

    // ── Submit ─────────────────────────────────────────────
    function confirmSubmit() {
        updateSummary();
        var answered   = parseInt(document.getElementById('summary-answered').textContent)   || 0;
        var unanswered = parseInt(document.getElementById('summary-unanswered').textContent) || 0;
        var review     = parseInt(document.getElementById('summary-review').textContent)     || 0;
        var msg = 'Submit the exam?\n\n✅ Answered: ' + answered +
                  '\n⬜ Unanswered: ' + unanswered +
                  '\n⭐ For Review: ' + review;
        if (confirm(msg)) doSubmit();
    }

    function autoSubmit() { alert('Time is up! Submitting your exam now.'); doSubmit(); }

    function doSubmit() {
        clearInterval(timerInterval);
        if (questionIds[currentIndex]) trackTime(questionIds[currentIndex], timeOnQuestion);
        fetch(URL_SUBMIT, {
            method: 'POST',
            headers: { 'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json' }
        }).then(function(r){ return r.json(); })
          .then(function(d){ if (d.success) window.location.href = d.redirect_url; else alert(d.message || 'Submission failed.'); })
          .catch(function(){ alert('Network error. Please try again.'); });
    }

    // ── UI helpers ─────────────────────────────────────────
    function updateNavBtn(qId, state) {
        var btn = document.getElementById('nav-btn-' + qId);
        if (!btn) return;
        btn.className = 'w-9 h-9 text-xs font-bold rounded-lg border-2 transition ';
        if      (state === 'review')    btn.className += 'bg-yellow-100 border-yellow-400 text-yellow-800';
        else if (state === 'answered')  btn.className += 'bg-green-100 border-green-400 text-green-800';
        else                            btn.className += 'bg-gray-50 border-gray-200 text-gray-600';
        updateSummary();
    }

    function updateSummary() {
        var answered  = document.querySelectorAll('[id^="nav-btn-"].bg-green-100').length  +
                        document.querySelectorAll('[id^="nav-btn-"].bg-green-100.border-green-400').length;
        // Count cleanly
        var allBtns   = document.querySelectorAll('[id^="nav-btn-"]');
        var ans = 0, rev = 0;
        allBtns.forEach(function(b) {
            if (b.classList.contains('bg-green-100'))  ans++;
            if (b.classList.contains('bg-yellow-100')) rev++;
        });
        document.getElementById('summary-answered').textContent   = ans;
        document.getElementById('summary-unanswered').textContent = TOTAL_QUESTIONS - ans - rev;
        document.getElementById('summary-review').textContent     = rev;
    }

    function updateProgressBar() {
        var pct = ((currentIndex + 1) / TOTAL_QUESTIONS) * 100;
        document.getElementById('progress-bar').style.width = pct + '%';
    }

    window.addEventListener('beforeunload', function(e) {
        e.preventDefault();
        e.returnValue = 'Your exam is in progress. Leave?';
    });
</script>
@endsection