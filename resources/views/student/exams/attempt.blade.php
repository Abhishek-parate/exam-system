@extends('layouts.student')

@section('title', 'Exam: ' . $attempt->exam->title)

@section('content')

<style>
    /* ── Base ── */
    *, *::before, *::after { box-sizing: border-box; }

    body { overflow: hidden; }

    .option-btn:hover  { border-color: #3b82f6; background: #eff6ff; }
    .option-selected   { border-color: #2563eb !important; background: #dbeafe !important; }

    .question-content img, .option-content img {
        max-width: 100%; height: auto; border-radius: 6px; margin: 8px 0; display: inline-block;
    }
    .question-content p, .option-content p { margin: 0; line-height: 1.6; }

    .timer-warning { color: #d97706; }
    .timer-danger  { color: #dc2626; animation: blink 1s infinite; }
    @keyframes blink { 0%,100%{opacity:1}50%{opacity:.5} }

    .nav-time-badge { display:block; font-size:9px; font-weight:600; line-height:1; margin-top:2px; opacity:.75; }
    .nav-btn-active { outline: 2px solid #6366f1; outline-offset: 1px; }

    .subj-textarea {
        width:100%; padding:12px 16px; border:2px solid #d8b4fe; border-radius:10px;
        background:#faf5ff; color:#1f2937; font-size:14px; line-height:1.6;
        resize:vertical; min-height:110px; font-family:inherit;
        transition:border-color .2s,background .2s,box-shadow .2s;
    }
    .subj-textarea:focus { outline:none; border-color:#7c3aed; background:#fff; box-shadow:0 0 0 3px rgba(124,58,237,.12); }
    .subj-textarea.answered { border-color:#7c3aed; background:#fff; }
    .subj-wrapper { padding:16px; background:#f5f3ff; border:2px solid #c4b5fd; border-radius:12px; }
    .subj-label {
        display:flex; align-items:center; gap:6px; font-size:12px; font-weight:700;
        color:#6d28d9; text-transform:uppercase; letter-spacing:.05em; margin-bottom:10px;
    }
    .subj-hint { font-size:11px; color:#8b5cf6; margin-top:6px; }

    /* ── EXAM WRAPPER ── */
    #exam-wrap {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 4rem); /* subtract student nav height */
        overflow: hidden;
        background: #f3f4f6;
    }

    /* ── TOP BAR ── */
    #exam-topbar {
        background: #fff;
        border-bottom: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0,0,0,.07);
        flex-shrink: 0;
        z-index: 30;
    }
    #exam-topbar-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .625rem 1rem;
        gap: .75rem;
    }
    #exam-title { font-size: .9375rem; font-weight: 700; color: #111827; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    #exam-cat   { font-size: .6875rem; color: #9ca3af; margin: 0; }
    #progress-bar-wrap { height: 3px; background: #f3f4f6; }
    #progress-bar { height: 3px; background: #2563eb; transition: width .4s; }

    .timer-block { text-align: center; line-height: 1.1; flex-shrink: 0; }
    .timer-label { font-size: .625rem; color: #9ca3af; display: block; }
    #timer              { font-size: 1.125rem; font-weight: 700; font-family: monospace; color: #16a34a; }
    #question-timer-strip { font-size: .9375rem; font-weight: 700; font-family: monospace; color: #6366f1; }

    .btn-submit-top {
        background: #dc2626; color: #fff; border: none; font-size: .75rem;
        font-weight: 600; padding: .4rem .875rem; border-radius: .5rem; cursor: pointer;
        white-space: nowrap; transition: background .15s; flex-shrink: 0;
    }
    .btn-submit-top:hover { background: #b91c1c; }

    /* ── BODY ── */
    #exam-body {
        display: flex;
        flex: 1;
        overflow: hidden;
    }

    /* ── QUESTION AREA ── */
    #question-scroll-area {
        flex: 1;
        overflow-y: auto;
        padding: .875rem;
        -webkit-overflow-scrolling: touch;
    }

    /* ── DESKTOP NAVIGATOR SIDEBAR ── */
    #desktop-nav {
        width: 14rem;
        flex-shrink: 0;
        overflow-y: auto;
        background: #fff;
        border-left: 1px solid #e5e7eb;
        padding: .875rem;
        display: flex;
        flex-direction: column;
        gap: .75rem;
    }

    /* ── MOBILE NAVIGATOR BOTTOM DRAWER ── */
    #mob-nav-backdrop {
        display: none;
        position: fixed; inset: 0; background: rgba(0,0,0,.45);
        z-index: 60;
    }
    #mob-nav-backdrop.open { display: block; }

    #mob-nav-drawer {
        position: fixed;
        bottom: 0; left: 0; right: 0;
        background: #fff;
        border-radius: 1.25rem 1.25rem 0 0;
        box-shadow: 0 -4px 24px rgba(0,0,0,.15);
        z-index: 61;
        max-height: 75vh;
        overflow-y: auto;
        padding: 1rem;
        transform: translateY(100%);
        transition: transform .3s cubic-bezier(.4,0,.2,1);
    }
    #mob-nav-drawer.open { transform: translateY(0); }

    /* ── MOBILE NAV TOGGLE BUTTON ── */
    #mob-nav-btn {
        display: none;
        position: fixed;
        bottom: 1.25rem; right: 1.25rem;
        width: 3.25rem; height: 3.25rem;
        background: #4f46e5;
        color: #fff;
        border: none; border-radius: 50%;
        font-size: .6875rem; font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(79,70,229,.45);
        z-index: 59;
        display: flex; flex-direction: column; align-items: center; justify-content: center; line-height: 1.1;
        transition: background .15s;
    }
    #mob-nav-btn .mob-nav-count { font-size: 1rem; font-weight: 800; }
    #mob-nav-btn:hover { background: #4338ca; }

    /* ── NAV GRID / LEGEND / SUMMARY — shared ── */
    .nav-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: .375rem; }
    .nav-btn {
        height: 2.625rem; width: 100%; font-size: .6875rem; font-weight: 700;
        border-radius: .5rem; border: 2px solid; cursor: pointer; background: transparent;
        display: flex; flex-direction: column; align-items: center; justify-content: center; line-height: 1;
        transition: all .15s;
    }
    .nav-h3 { font-size: .6875rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: .06em; margin: 0 0 .5rem; }

    .nav-legend { display: flex; flex-direction: column; gap: .375rem; font-size: .75rem; color: #6b7280; border-top: 1px solid #f3f4f6; padding-top: .625rem; }
    .nav-legend-row { display: flex; align-items: center; gap: .375rem; }
    .nav-dot { width: .75rem; height: .75rem; border-radius: .25rem; flex-shrink: 0; }

    .nav-summary { background: #f9fafb; border-radius: .625rem; padding: .625rem .75rem; font-size: .75rem; }
    .nav-summary-row { display: flex; justify-content: space-between; padding: .125rem 0; }

    .btn-submit-nav {
        width: 100%; background: #dc2626; color: #fff; border: none; padding: .5rem;
        border-radius: .5rem; font-size: .8125rem; font-weight: 600; cursor: pointer;
        transition: background .15s;
    }
    .btn-submit-nav:hover { background: #b91c1c; }

    /* ── Drawer handle ── */
    .drawer-handle {
        width: 2.5rem; height: .25rem; background: #d1d5db; border-radius: 9999px;
        margin: 0 auto .875rem;
    }

    /* ── RESPONSIVE ── */
    @media (min-width: 768px) {
        #mob-nav-btn     { display: none !important; }
        #mob-nav-backdrop { display: none !important; }
        #mob-nav-drawer  { display: none !important; }
        #desktop-nav     { display: flex !important; }
        #exam-topbar-inner { padding: .625rem 1.25rem; }
        #exam-title      { font-size: 1rem; }
        #timer           { font-size: 1.25rem; }
    }

    @media (max-width: 767px) {
        #desktop-nav     { display: none !important; }
        #mob-nav-btn     { display: flex !important; }
        #question-scroll-area { padding-bottom: 5rem; } /* space for FAB */
        #exam-topbar-inner { padding: .5rem .875rem; gap: .5rem; }
        #exam-title      { font-size: .875rem; }
        #timer           { font-size: 1rem; }
        #question-timer-strip { display: none; }
    }
</style>

<div id="exam-wrap">

    {{-- ═══════════════════════
         TOP BAR
    ═══════════════════════ --}}
    <div id="exam-topbar">
        <div id="exam-topbar-inner">
            <div style="min-width:0;flex:1;">
                <h1 id="exam-title">{{ $attempt->exam->title }}</h1>
                <p id="exam-cat">{{ $attempt->exam->examCategory?->name ?? 'General' }}</p>
            </div>

            <div style="display:flex;align-items:center;gap:.75rem;flex-shrink:0;">
                <div class="timer-block">
                    <div id="timer" class="text-green-600">--:--</div>
                    <span class="timer-label">Time Left</span>
                </div>
                <div class="timer-block" id="question-timer-strip-wrap">
                    <div id="question-timer-strip" style="font-size:.9375rem;font-weight:700;font-family:monospace;color:#6366f1;">0:00</div>
                    <span class="timer-label">This Question</span>
                </div>
                <button onclick="confirmSubmit()" class="btn-submit-top">Submit Exam</button>
            </div>
        </div>
        <div id="progress-bar-wrap">
            <div id="progress-bar" style="width:0%"></div>
        </div>
    </div>

    {{-- ═══════════════════════
         BODY
    ═══════════════════════ --}}
    <div id="exam-body">

        {{-- Question Area --}}
        <div id="question-scroll-area">

            @foreach($questions as $index => $question)
            @php
                $answer       = $attempt->answers->firstWhere('question_id', $question->id);
                $storedType   = $question->question_type ?? null;
                $questionType = $storedType ?: ($question->options->isEmpty() ? 'subjective' : 'mcq');
            @endphp

            <div id="question-{{ $question->id }}"
                 class="question-block bg-white rounded-xl shadow p-4 {{ $index > 0 ? 'hidden' : '' }}"
                 data-question-id="{{ $question->id }}"
                 data-index="{{ $index }}"
                 data-question-type="{{ $questionType }}">

                {{-- Q Header --}}
                <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:.5rem;margin-bottom:.75rem;">
                    <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
                        <span style="font-size:.8125rem;font-weight:600;color:#6b7280;">
                            Question {{ $index + 1 }} / {{ $questions->count() }}
                        </span>
                        <span style="display:inline-flex;align-items:center;gap:.25rem;font-size:.6875rem;font-weight:600;color:#6366f1;background:#eef2ff;border:1px solid #e0e7ff;border-radius:9999px;padding:.2rem .5rem;">
                            <svg style="width:.75rem;height:.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="q-inline-timer" data-qid="{{ $question->id }}">0s</span>
                        </span>
                        @if($questionType === 'subjective')
                        <span style="display:inline-flex;align-items:center;gap:.25rem;font-size:.6875rem;font-weight:600;color:#7c3aed;background:#f5f3ff;border:1px solid #ddd6fe;border-radius:9999px;padding:.2rem .5rem;">
                            <svg style="width:.75rem;height:.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Subjective
                        </span>
                        @endif
                    </div>
                    <div style="display:flex;gap:.375rem;flex-wrap:wrap;">
                        @php $dn = strtolower($question->difficulty?->name ?? ''); @endphp
                        <span style="padding:.15rem .5rem;font-size:.6875rem;border-radius:9999px;font-weight:600;
                            {{ $dn === 'easy' ? 'background:#dcfce7;color:#166534;' : ($dn === 'medium' ? 'background:#fef9c3;color:#92400e;' : 'background:#fee2e2;color:#991b1b;') }}">
                            {{ $question->difficulty?->name ?? 'N/A' }}
                        </span>
                        <span style="padding:.15rem .5rem;font-size:.6875rem;background:#dbeafe;color:#1e40af;border-radius:9999px;font-weight:600;">+{{ $question->marks }}</span>
                        @if($question->negative_marks > 0)
                        <span style="padding:.15rem .5rem;font-size:.6875rem;background:#fee2e2;color:#dc2626;border-radius:9999px;font-weight:600;">-{{ $question->negative_marks }}</span>
                        @endif
                    </div>
                </div>

                {{-- Question text --}}
                <div class="question-content" style="color:#111827;font-size:.9375rem;font-weight:500;margin-bottom:1rem;line-height:1.7;">
                    {!! $question->question_text !!}
                </div>

                {{-- Answer input --}}
                @if($questionType === 'subjective')
                    <div class="subj-wrapper">
                        <div class="subj-label">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
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
                    @if($question->options->count() > 0)
                        <div style="display:flex;flex-direction:column;gap:.5rem;">
                            @foreach($question->options as $option)
                            <div id="option-label-{{ $option->id }}"
                                 onclick="selectOption({{ $question->id }}, {{ $option->id }}, this)"
                                 class="option-btn {{ ($answer && $answer->selected_option_id == $option->id) ? 'option-selected' : 'border-gray-200' }}"
                                 style="display:flex;align-items:flex-start;gap:.75rem;padding:.75rem;border:2px solid;border-color:{{ ($answer && $answer->selected_option_id == $option->id) ? '#2563eb' : '#e5e7eb' }};border-radius:.625rem;cursor:pointer;transition:all .15s;background:{{ ($answer && $answer->selected_option_id == $option->id) ? '#dbeafe' : '#fff' }};">
                                <input type="radio"
                                       name="q{{ $question->id }}"
                                       value="{{ $option->id }}"
                                       {{ ($answer && $answer->selected_option_id == $option->id) ? 'checked' : '' }}
                                       style="margin-top:.125rem;flex-shrink:0;width:1rem;height:1rem;pointer-events:none;">
                                <div class="option-content" style="color:#1f2937;font-size:.9375rem;line-height:1.6;min-width:0;flex:1;">
                                    {!! $option->option_text !!}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div style="padding:1rem;background:#fffbeb;border:1px solid #fde68a;border-radius:.625rem;">
                            <p style="color:#92400e;font-size:.875rem;">No answer options available for this question.</p>
                        </div>
                    @endif
                @endif

                {{-- Footer: review + clear --}}
                <div style="margin-top:1rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem;">
                    <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.875rem;color:#6b7280;user-select:none;"
                           onclick="toggleReview({{ $question->id }}, this)">
                        <input type="checkbox" id="review-{{ $question->id }}"
                               {{ ($answer && $answer->is_marked_for_review) ? 'checked' : '' }}
                               style="border-radius:.25rem;width:1rem;height:1rem;pointer-events:none;">
                        <span>Mark for Review</span>
                    </label>
                    <button onclick="clearAnswer({{ $question->id }})"
                            style="font-size:.75rem;color:#9ca3af;background:none;border:none;cursor:pointer;transition:color .15s;padding:0;"
                            onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#9ca3af'">
                        Clear Answer
                    </button>
                </div>

                {{-- Prev / Next --}}
                <div style="margin-top:.875rem;display:flex;justify-content:space-between;align-items:center;border-top:1px solid #f3f4f6;padding-top:.875rem;">
                    <button onclick="goToQuestion({{ $index - 1 }})"
                            {{ $index === 0 ? 'disabled' : '' }}
                            style="padding:.5rem 1rem;font-size:.875rem;font-weight:500;border-radius:.5rem;border:1px solid #d1d5db;background:#fff;cursor:pointer;transition:background .15s;opacity:{{ $index === 0 ? '.3' : '1' }};">
                        ← Prev
                    </button>
                    <span style="font-size:.75rem;color:#9ca3af;">{{ $index + 1 }} / {{ $questions->count() }}</span>
                    @if($index < $questions->count() - 1)
                        <button onclick="goToQuestion({{ $index + 1 }})"
                                style="padding:.5rem 1rem;font-size:.875rem;font-weight:500;background:#2563eb;color:#fff;border:none;border-radius:.5rem;cursor:pointer;transition:background .15s;"
                                onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                            Next →
                        </button>
                    @else
                        <button onclick="confirmSubmit()"
                                style="padding:.5rem 1rem;font-size:.875rem;font-weight:500;background:#16a34a;color:#fff;border:none;border-radius:.5rem;cursor:pointer;transition:background .15s;"
                                onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
                            Submit →
                        </button>
                    @endif
                </div>

            </div>
            @endforeach
        </div>

        {{-- ═══ DESKTOP NAVIGATOR SIDEBAR ═══ --}}
        <div id="desktop-nav">
            <h3 class="nav-h3">Navigator</h3>

            <div class="nav-grid">
                @foreach($questions as $index => $question)
                @php
                    $ans        = $attempt->answers->firstWhere('question_id', $question->id);
                    $isAnswered = $ans && ($ans->selected_option_id || ($ans->text_answer && trim($ans->text_answer) !== ''));
                    $isReview   = $ans && $ans->is_marked_for_review;
                @endphp
                <button id="nav-btn-{{ $question->id }}"
                        onclick="goToQuestion({{ $index }})"
                        title="Q{{ $index + 1 }}"
                        class="nav-btn {{ $isReview ? 'bg-yellow-100 border-yellow-400 text-yellow-800' : ($isAnswered ? 'bg-green-100 border-green-400 text-green-800' : 'bg-gray-50 border-gray-200 text-gray-600') }} {{ $index === 0 ? 'nav-btn-active' : '' }}">
                    <span>{{ $index + 1 }}</span>
                    <span class="nav-time-badge" id="nav-time-{{ $question->id }}">0s</span>
                </button>
                @endforeach
            </div>

            <div class="nav-legend">
                <div class="nav-legend-row"><div class="nav-dot" style="background:#dcfce7;border:1px solid #86efac;"></div><span>Answered</span></div>
                <div class="nav-legend-row"><div class="nav-dot" style="background:#fef9c3;border:1px solid #fde047;"></div><span>For Review</span></div>
                <div class="nav-legend-row"><div class="nav-dot" style="background:#f9fafb;border:1px solid #d1d5db;"></div><span>Not Answered</span></div>
                <div class="nav-legend-row" style="border-top:1px solid #f3f4f6;padding-top:.375rem;margin-top:.125rem;">
                    <svg style="width:.75rem;height:.75rem;color:#818cf8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span style="color:#818cf8;">Time per question</span>
                </div>
            </div>

            <div class="nav-summary">
                <div class="nav-summary-row"><span style="color:#6b7280;">Answered</span><span id="summary-answered" style="font-weight:700;color:#16a34a;">0</span></div>
                <div class="nav-summary-row"><span style="color:#6b7280;">Unanswered</span><span id="summary-unanswered" style="font-weight:700;color:#ef4444;">{{ $questions->count() }}</span></div>
                <div class="nav-summary-row"><span style="color:#6b7280;">For Review</span><span id="summary-review" style="font-weight:700;color:#d97706;">0</span></div>
                <div class="nav-summary-row" style="border-top:1px solid #e5e7eb;padding-top:.25rem;margin-top:.25rem;">
                    <span style="color:#6b7280;">Time Used</span>
                    <span id="summary-time-used" style="font-weight:700;color:#6366f1;">0s</span>
                </div>
            </div>

            <button onclick="confirmSubmit()" class="btn-submit-nav">Submit Exam</button>
        </div>

    </div>{{-- end #exam-body --}}
</div>{{-- end #exam-wrap --}}

{{-- ═══ MOBILE: Navigator FAB + Drawer ═══ --}}
<button id="mob-nav-btn" onclick="openMobNav()" aria-label="Question Navigator">
    <span class="mob-nav-count" id="mob-nav-count">1</span>
    <span style="font-size:.5625rem;opacity:.85;">Nav</span>
</button>

<div id="mob-nav-backdrop" onclick="closeMobNav()"></div>

<div id="mob-nav-drawer">
    <div class="drawer-handle"></div>

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.875rem;">
        <h3 class="nav-h3" style="margin:0;">Question Navigator</h3>
        <button onclick="closeMobNav()" style="background:none;border:none;cursor:pointer;font-size:1.25rem;color:#9ca3af;line-height:1;">✕</button>
    </div>

    {{-- Mobile nav grid (same buttons, different IDs) --}}
    <div class="nav-grid" style="margin-bottom:.875rem;">
        @foreach($questions as $index => $question)
        @php
            $ans2        = $attempt->answers->firstWhere('question_id', $question->id);
            $isAnswered2 = $ans2 && ($ans2->selected_option_id || ($ans2->text_answer && trim($ans2->text_answer) !== ''));
            $isReview2   = $ans2 && $ans2->is_marked_for_review;
        @endphp
        <button id="mob-nav-btn-{{ $question->id }}"
                onclick="goToQuestionMob({{ $index }})"
                title="Q{{ $index + 1 }}"
                class="nav-btn {{ $isReview2 ? 'bg-yellow-100 border-yellow-400 text-yellow-800' : ($isAnswered2 ? 'bg-green-100 border-green-400 text-green-800' : 'bg-gray-50 border-gray-200 text-gray-600') }} {{ $index === 0 ? 'nav-btn-active' : '' }}">
            <span>{{ $index + 1 }}</span>
        </button>
        @endforeach
    </div>

    {{-- Summary in drawer --}}
    <div class="nav-summary" style="margin-bottom:.875rem;">
        <div class="nav-summary-row"><span style="color:#6b7280;">Answered</span><span id="mob-summary-answered" style="font-weight:700;color:#16a34a;">0</span></div>
        <div class="nav-summary-row"><span style="color:#6b7280;">Unanswered</span><span id="mob-summary-unanswered" style="font-weight:700;color:#ef4444;">{{ $questions->count() }}</span></div>
        <div class="nav-summary-row"><span style="color:#6b7280;">For Review</span><span id="mob-summary-review" style="font-weight:700;color:#d97706;">0</span></div>
    </div>

    <div style="display:flex;gap:.5rem;">
        <button onclick="closeMobNav()" style="flex:1;padding:.625rem;background:#f3f4f6;color:#374151;border:none;border-radius:.5rem;font-size:.875rem;font-weight:600;cursor:pointer;">Close</button>
        <button onclick="confirmSubmit()" style="flex:1;padding:.625rem;background:#dc2626;color:#fff;border:none;border-radius:.5rem;font-size:.875rem;font-weight:600;cursor:pointer;">Submit Exam</button>
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

    var currentIndex  = 0;
    var timerInterval = null;
    var questionIds   = [];
    var questionTimes = {};
    var currentQStart = Date.now();
    var textDebounce  = {};

    document.querySelectorAll('.question-block').forEach(function(el) {
        questionIds.push(parseInt(el.dataset.questionId));
        questionTimes[parseInt(el.dataset.questionId)] = 0;
    });

    document.addEventListener('DOMContentLoaded', function() {
        startTimer(); updateSummary(); updateProgressBar(); startStatusPoll(); startQTimerDisplay();
    });

    // ── Mobile nav drawer ──────────────────────────────
    function openMobNav()  {
        document.getElementById('mob-nav-backdrop').classList.add('open');
        document.getElementById('mob-nav-drawer').classList.add('open');
    }
    function closeMobNav() {
        document.getElementById('mob-nav-backdrop').classList.remove('open');
        document.getElementById('mob-nav-drawer').classList.remove('open');
    }
    function goToQuestionMob(idx) { closeMobNav(); goToQuestion(idx); }

    // ── Timer ──────────────────────────────────────────
    function startTimer() {
        var s = REMAINING_SECS;
        renderTimer(s);
        timerInterval = setInterval(function() { s--; renderTimer(s); if(s<=0){clearInterval(timerInterval);autoSubmit();} }, 1000);
    }
    function renderTimer(s) {
        if(s<0) s=0;
        var h=Math.floor(s/3600),m=Math.floor((s%3600)/60),sec=s%60;
        var str = h>0 ? pad(h)+':'+pad(m)+':'+pad(sec) : pad(m)+':'+pad(sec);
        var el=document.getElementById('timer');
        el.textContent=str;
        el.className='text-xl font-bold font-mono tabular-nums '+(s<=300?'timer-danger':s<=600?'timer-warning':'text-green-600');
    }
    function pad(n){return n<10?'0'+n:''+n;}

    // ── Per-question timer ─────────────────────────────
    function startQTimerDisplay() {
        setInterval(function() {
            var qId=questionIds[currentIndex]; if(!qId)return;
            var elapsed=Math.floor((Date.now()-currentQStart)/1000);
            var total=questionTimes[qId]+elapsed;
            var strip=document.getElementById('question-timer-strip');
            if(strip) strip.textContent=fmtTime(total);
            var badge=document.querySelector('.q-inline-timer[data-qid="'+qId+'"]');
            if(badge) badge.textContent=fmtTime(total);
            var navBadge=document.getElementById('nav-time-'+qId);
            if(navBadge) navBadge.textContent=fmtTime(total);
            var used=0; for(var id in questionTimes){used+=questionTimes[id];} used+=elapsed;
            var su=document.getElementById('summary-time-used'); if(su) su.textContent=fmtTime(used);
        },1000);
    }
    function fmtTime(sec){if(sec<60)return sec+'s';if(sec<3600)return Math.floor(sec/60)+'m '+(sec%60)+'s';return Math.floor(sec/3600)+'h '+Math.floor((sec%3600)/60)+'m';}
    function flushCurrentQTime(){
        var qId=questionIds[currentIndex]; if(!qId)return 0;
        var delta=Math.floor((Date.now()-currentQStart)/1000);
        if(delta>0){questionTimes[qId]=(questionTimes[qId]||0)+delta;trackTime(qId,delta);}
        return delta;
    }

    // ── Navigation ────────────────────────────────────
    function goToQuestion(idx) {
        if(idx<0||idx>=TOTAL_QUESTIONS)return;
        flushCurrentQTime();
        document.querySelectorAll('.question-block').forEach(function(el){el.classList.add('hidden');});
        var oldBtn=document.getElementById('nav-btn-'+questionIds[currentIndex]);
        var oldMobBtn=document.getElementById('mob-nav-btn-'+questionIds[currentIndex]);
        if(oldBtn) oldBtn.classList.remove('nav-btn-active');
        if(oldMobBtn) oldMobBtn.classList.remove('nav-btn-active');
        document.querySelectorAll('.question-block')[idx].classList.remove('hidden');
        var sa=document.getElementById('question-scroll-area');
        if(sa) sa.scrollTo({top:0,behavior:'smooth'});
        var newBtn=document.getElementById('nav-btn-'+questionIds[idx]);
        var newMobBtn=document.getElementById('mob-nav-btn-'+questionIds[idx]);
        if(newBtn) newBtn.classList.add('nav-btn-active');
        if(newMobBtn) newMobBtn.classList.add('nav-btn-active');
        currentIndex=idx; currentQStart=Date.now();
        // Update FAB number
        var fab=document.getElementById('mob-nav-count');
        if(fab) fab.textContent=idx+1;
        updateProgressBar();
    }

    // ── MCQ select ────────────────────────────────────
    function selectOption(qId,optId,clicked){
        var container=document.getElementById('question-'+qId);
        container.querySelectorAll('[id^="option-label-"]').forEach(function(el){
            el.classList.remove('option-selected');el.classList.add('border-gray-200');
            el.style.borderColor='#e5e7eb';el.style.background='#fff';
        });
        clicked.classList.add('option-selected');clicked.classList.remove('border-gray-200');
        clicked.style.borderColor='#2563eb';clicked.style.background='#dbeafe';
        clicked.querySelector('input[type=radio]').checked=true;
        updateNavBtn(qId,'answered');saveAnswer(qId,optId,undefined);
    }

    // ── Clear answer ──────────────────────────────────
    function clearAnswer(qId){
        var container=document.getElementById('question-'+qId);
        var qType=container.dataset.questionType||'mcq';
        if(qType==='subjective'){
            var ta=document.getElementById('text-answer-'+qId);
            if(ta){ta.value='';ta.classList.remove('answered');}
            saveTextAnswer(qId,'');
        } else {
            container.querySelectorAll('[id^="option-label-"]').forEach(function(el){
                el.classList.remove('option-selected');el.classList.add('border-gray-200');
                el.style.borderColor='#e5e7eb';el.style.background='#fff';
            });
            container.querySelectorAll('input[type=radio]').forEach(function(r){r.checked=false;});
            saveAnswer(qId,null,undefined);
        }
        updateNavBtn(qId,'unanswered');
    }

    // ── Review toggle ─────────────────────────────────
    function toggleReview(qId,lbl){
        setTimeout(function(){
            var cb=document.getElementById('review-'+qId);
            var isChecked=cb.checked;
            var container=document.getElementById('question-'+qId);
            var qType=container.dataset.questionType||'mcq';
            var hasAnswer=false;
            if(qType==='subjective'){var ta=document.getElementById('text-answer-'+qId);hasAnswer=ta&&ta.value.trim().length>0;}
            else {hasAnswer=!!container.querySelector('input[type=radio]:checked');}
            updateNavBtn(qId,isChecked?'review':(hasAnswer?'answered':'unanswered'));
            saveAnswer(qId,undefined,isChecked);
        },10);
    }

    // ── Subjective ────────────────────────────────────
    function handleTextAnswer(qId,text){
        var ta=document.getElementById('text-answer-'+qId);
        if(ta){
            if(text.trim().length>0){ta.classList.add('answered');updateNavBtn(qId,'answered');}
            else{ta.classList.remove('answered');updateNavBtn(qId,'unanswered');}
        }
        clearTimeout(textDebounce[qId]);
        textDebounce[qId]=setTimeout(function(){saveTextAnswer(qId,text);},800);
    }
    function saveTextAnswer(qId,text){
        fetch(URL_SAVE,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},
            body:JSON.stringify({question_id:qId,text_answer:text})}).catch(function(){});
    }

    // ── MCQ save ─────────────────────────────────────
    function saveAnswer(qId,optId,isReview){
        var body={question_id:qId};
        if(optId!==undefined) body.option_id=optId;
        if(isReview!==undefined) body.is_marked_for_review=isReview;
        fetch(URL_SAVE,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},
            body:JSON.stringify(body)}).catch(function(){});
    }

    // ── Track time ────────────────────────────────────
    function trackTime(qId,delta){
        if(delta<=0)return;
        fetch(URL_TRACK,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},
            body:JSON.stringify({question_id:qId,time_spent:delta})}).catch(function(){});
    }
    setInterval(function(){
        var qId=questionIds[currentIndex]; if(!qId)return;
        var delta=Math.floor((Date.now()-currentQStart)/1000);
        if(delta>0){questionTimes[qId]=(questionTimes[qId]||0)+delta;trackTime(qId,delta);currentQStart=Date.now();}
    },30000);

    // ── Status poll ───────────────────────────────────
    function startStatusPoll(){
        setInterval(function(){
            fetch(URL_STATUS,{headers:{'Accept':'application/json'}})
                .then(function(r){return r.json();})
                .then(function(d){if(d.time_expired){clearInterval(timerInterval);window.location.href=d.redirect_url;}})
                .catch(function(){});
        },30000);
    }

    // ── Submit ────────────────────────────────────────
    function confirmSubmit(){
        updateSummary();
        var answered=parseInt(document.getElementById('summary-answered').textContent)||0;
        var unanswered=parseInt(document.getElementById('summary-unanswered').textContent)||0;
        var review=parseInt(document.getElementById('summary-review').textContent)||0;
        if(confirm('Submit the exam?\n\nAnswered: '+answered+'\nUnanswered: '+unanswered+'\nFor Review: '+review)){doSubmit();}
    }
    function autoSubmit(){alert('Time is up! Submitting your exam now.');doSubmit();}
    function doSubmit(){
        clearInterval(timerInterval);flushCurrentQTime();
        fetch(URL_SUBMIT,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'}})
            .then(function(r){return r.json();})
            .then(function(d){if(d.success)window.location.href=d.redirect_url;else alert(d.message||'Submission failed.');})
            .catch(function(){alert('Network error. Please try again.');});
    }

    // ── UI helpers ────────────────────────────────────
    function updateNavBtn(qId,state){
        // Update both desktop and mobile nav buttons
        ['nav-btn-','mob-nav-btn-'].forEach(function(prefix){
            var btn=document.getElementById(prefix+qId);
            if(!btn)return;
            var isActive=btn.classList.contains('nav-btn-active');
            btn.className='nav-btn ';
            if(state==='review')       btn.className+='bg-yellow-100 border-yellow-400 text-yellow-800';
            else if(state==='answered') btn.className+='bg-green-100 border-green-400 text-green-800';
            else                        btn.className+='bg-gray-50 border-gray-200 text-gray-600';
            if(isActive) btn.classList.add('nav-btn-active');
        });
        updateSummary();
    }
    function updateSummary(){
        var allBtns=document.querySelectorAll('[id^="nav-btn-"]');
        var ans=0,rev=0;
        allBtns.forEach(function(b){
            if(b.classList.contains('bg-green-100'))ans++;
            if(b.classList.contains('bg-yellow-100'))rev++;
        });
        // Desktop
        var da=document.getElementById('summary-answered');      if(da)da.textContent=ans;
        var du=document.getElementById('summary-unanswered');    if(du)du.textContent=TOTAL_QUESTIONS-ans-rev;
        var dr=document.getElementById('summary-review');        if(dr)dr.textContent=rev;
        // Mobile drawer
        var ma=document.getElementById('mob-summary-answered');  if(ma)ma.textContent=ans;
        var mu=document.getElementById('mob-summary-unanswered');if(mu)mu.textContent=TOTAL_QUESTIONS-ans-rev;
        var mr=document.getElementById('mob-summary-review');    if(mr)mr.textContent=rev;
    }
    function updateProgressBar(){
        document.getElementById('progress-bar').style.width=((currentIndex+1)/TOTAL_QUESTIONS*100)+'%';
    }

    window.addEventListener('beforeunload',function(e){
        flushCurrentQTime();e.preventDefault();e.returnValue='Your exam is in progress. Leave?';
    });

    function sendCheatLog(type,count){
        fetch("{{ url('student/exams/'.$attempt->attempt_token.'/cheat-log') }}",{
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN},
            body:JSON.stringify({type:type,count:count})
        }).then(res=>res.json()).then(data=>{if(data.force_submit){alert(data.message||'Exam auto-submitted.');doSubmit();}}).catch(()=>{});
    }
</script>

@endsection