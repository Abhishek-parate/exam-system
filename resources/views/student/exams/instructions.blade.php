@extends('layouts.student')

@section('title', 'Exam Instructions')

@section('content')
<style>
    *, *::before, *::after { box-sizing: border-box; }
    .instr-wrap { max-width: 42rem; margin: 0 auto; padding: 1.25rem 1rem 2rem; }

    .back-link  { display:inline-flex; align-items:center; gap:.375rem; font-size:.875rem; color:#6b7280; text-decoration:none; margin-bottom:1.125rem; transition:color .15s; }
    .back-link:hover { color:#111827; }

    /* ── Exam header banner ── */
    .exam-banner {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        border-radius: .875rem; padding: 1.25rem; color:#fff;
        margin-bottom: 1rem; box-shadow: 0 4px 16px rgba(37,99,235,.3);
    }
    .banner-top   { display:flex; align-items:flex-start; justify-content:space-between; gap:.75rem; flex-wrap:wrap; }
    .banner-title { font-size:1.25rem; font-weight:700; margin:0 0 .25rem; }
    .banner-cat   { font-size:.8125rem; opacity:.75; margin:0; }
    .banner-code  { background:rgba(255,255,255,.2); border-radius:9999px; padding:.35rem .875rem; font-size:.8125rem; font-weight:600; white-space:nowrap; flex-shrink:0; }

    /* ── Info cards grid ── */
    .info-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:.75rem; margin-bottom:1rem; }
    .info-card { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:.875rem; text-align:center; }
    .info-val  { font-size:1.5rem; font-weight:700; line-height:1; }
    .info-lbl  { font-size:.6875rem; color:#9ca3af; margin-top:.375rem; }

    /* ── White cards ── */
    .card      { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:1.125rem; margin-bottom:1rem; }
    .card-title { font-size:1rem; font-weight:700; color:#111827; margin:0 0 .875rem; }

    /* ── Timing row ── */
    .timing-grid { display:grid; grid-template-columns:1fr; gap:.625rem; }
    .timing-row  { display:flex; align-items:center; gap:.75rem; padding:.75rem; border-radius:.625rem; }
    .timing-row-green { background:#f0fdf4; }
    .timing-row-red   { background:#fef2f2; }
    .timing-icon { font-size:1.125rem; flex-shrink:0; }
    .timing-label { font-size:.6875rem; color:#9ca3af; margin:0; }
    .timing-val   { font-size:.9375rem; font-weight:600; color:#111827; margin:0; }

    /* ── Marking table ── */
    .mktbl-wrap { overflow-x:auto; }
    .mktbl      { width:100%; border-collapse:collapse; min-width:280px; font-size:.875rem; }
    .mktbl th   { padding:.5rem .75rem; border-bottom:1px solid #e5e7eb; font-weight:600; color:#6b7280; }
    .mktbl td   { padding:.5rem .75rem; border-bottom:1px solid #f3f4f6; }
    .mktbl tr:last-child td { border-bottom:none; }

    /* ── Instructions list ── */
    .instr-list { display:flex; flex-direction:column; gap:.625rem; }
    .instr-item { display:flex; align-items:flex-start; gap:.5rem; font-size:.875rem; color:#374151; }
    .instr-dot  { flex-shrink:0; margin-top:.1875rem; font-size:.75rem; }

    /* ── Start section ── */
    .start-section { background:#fff; border-radius:.875rem; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:1.125rem; }
    .check-label   { display:flex; align-items:flex-start; gap:.75rem; cursor:pointer; margin-bottom:1rem; user-select:none; }
    .check-label input { margin-top:.125rem; width:1.125rem; height:1.125rem; flex-shrink:0; accent-color:#16a34a; }
    .check-label span  { font-size:.9375rem; color:#374151; line-height:1.5; }

    .start-btns { display:flex; gap:.75rem; }
    #start-btn  { flex:1; border:none; border-radius:.625rem; font-size:1rem; font-weight:700; padding:.875rem; cursor:pointer; transition:background .15s; color:#fff; }
    .cancel-btn { display:inline-flex; align-items:center; justify-content:center; background:#e5e7eb; color:#374151; font-size:.9375rem; font-weight:600; padding:.75rem 1.25rem; border-radius:.625rem; text-decoration:none; transition:background .15s; white-space:nowrap; }
    .cancel-btn:hover { background:#d1d5db; }
    .start-note { font-size:.75rem; color:#9ca3af; text-align:center; margin-top:.75rem; }

    @media (min-width: 480px) {
        .info-grid    { grid-template-columns: repeat(4,1fr); }
        .timing-grid  { grid-template-columns: 1fr 1fr; }
    }
    @media (min-width: 768px) {
        .instr-wrap   { padding: 2rem 1.5rem; }
        .banner-title { font-size:1.5rem; }
        .card         { padding:1.5rem; }
        .card-title   { font-size:1.125rem; }
    }
</style>

<div class="instr-wrap">

    <a href="{{ route('student.exams.index') }}" class="back-link">
        ← Back to My Exams
    </a>

    {{-- Exam Banner --}}
    <div class="exam-banner">
        <div class="banner-top">
            <div>
                <h1 class="banner-title">{{ $exam->title }}</h1>
                <p class="banner-cat">{{ $exam->examCategory?->name ?? 'General' }}</p>
            </div>
            <span class="banner-code">Code: {{ $exam->exam_code }}</span>
        </div>
    </div>

    {{-- Info Cards --}}
    <div class="info-grid">
        <div class="info-card">
            <p class="info-val" style="color:#2563eb;">{{ $exam->duration_minutes }}</p>
            <p class="info-lbl">Minutes</p>
        </div>
        <div class="info-card">
            <p class="info-val" style="color:#9333ea;">{{ $exam->total_questions }}</p>
            <p class="info-lbl">Questions</p>
        </div>
        <div class="info-card">
            <p class="info-val" style="color:#16a34a;">{{ $exam->total_marks }}</p>
            <p class="info-lbl">Total Marks</p>
        </div>
        <div class="info-card">
            <p class="info-val" style="color:#ea580c;">
                {{ $exam->end_time->gt(now()) ? (int)$exam->end_time->diffInMinutes(now()) : '—' }}
            </p>
            <p class="info-lbl">Mins Left</p>
        </div>
    </div>

    {{-- Timing --}}
    <div class="card">
        <h2 class="card-title">⏰ Exam Timing</h2>
        <div class="timing-grid">
            <div class="timing-row timing-row-green">
                <span class="timing-icon">▶</span>
                <div>
                    <p class="timing-label">Start Time</p>
                    <p class="timing-val">{{ $exam->start_time->format('d M Y, h:i A') }}</p>
                </div>
            </div>
            <div class="timing-row timing-row-red">
                <span class="timing-icon">⏹</span>
                <div>
                    <p class="timing-label">End Time</p>
                    <p class="timing-val">{{ $exam->end_time->format('d M Y, h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Marking Scheme --}}
    @if($exam->markingSchemes->count() > 0)
    <div class="card">
        <h2 class="card-title">📊 Marking Scheme</h2>
        <div class="mktbl-wrap">
            <table class="mktbl">
                <thead>
                    <tr>
                        <th style="text-align:left;">Subject</th>
                        <th style="text-align:center;color:#16a34a;">Correct</th>
                        <th style="text-align:center;color:#dc2626;">Wrong</th>
                        <th style="text-align:center;">Unattempted</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exam->markingSchemes as $scheme)
                    <tr>
                        <td style="font-weight:500;">{{ $scheme->subject?->name ?? 'All Subjects' }}</td>
                        <td style="text-align:center;color:#16a34a;font-weight:700;">+{{ $scheme->correct_marks }}</td>
                        <td style="text-align:center;color:#dc2626;font-weight:700;">-{{ $scheme->wrong_marks }}</td>
                        <td style="text-align:center;color:#9ca3af;">{{ $scheme->unattempted_marks }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Instructions --}}
    <div class="card">
        <h2 class="card-title">📋 Instructions</h2>
        <div class="instr-list">
            <div class="instr-item">
                <span class="instr-dot" style="color:#3b82f6;">•</span>
                <span>The exam will automatically submit when the time runs out.</span>
            </div>
            <div class="instr-item">
                <span class="instr-dot" style="color:#3b82f6;">•</span>
                <span>Do not refresh or close the browser during the exam.</span>
            </div>
            @if($exam->randomize_questions)
            <div class="instr-item">
                <span class="instr-dot" style="color:#3b82f6;">•</span>
                <span>Questions are presented in a randomized order.</span>
            </div>
            @endif
            @if($exam->allow_resume)
            <div class="instr-item">
                <span class="instr-dot" style="color:#16a34a;">•</span>
                <span>If disconnected, you can resume from where you left off.</span>
            </div>
            @else
            <div class="instr-item">
                <span class="instr-dot" style="color:#dc2626;">•</span>
                <span>This exam does <strong>NOT</strong> allow resuming after disconnection.</span>
            </div>
            @endif
            @if($exam->description)
            <div class="instr-item" style="background:#eff6ff;border-radius:.625rem;padding:.75rem;">
                <span class="instr-dot" style="color:#3b82f6;">ℹ</span>
                <span>{{ $exam->description }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Start Section --}}
    <div class="start-section">
        <label class="check-label" onclick="toggleStart()">
            <input type="checkbox" id="confirm-checkbox" style="pointer-events:none;">
            <span>I have read and understood all the instructions. I am ready to begin the exam.</span>
        </label>

        <div class="start-btns">
            <button id="start-btn" onclick="startExam()" disabled
                    style="background-color:#9ca3af;cursor:not-allowed;">
                🚀 Start Exam Now
            </button>
            <a href="{{ route('student.exams.index') }}" class="cancel-btn">Cancel</a>
        </div>

        <p class="start-note">Once started, the timer cannot be paused.</p>
    </div>
</div>

<script>
    var examStarted = false;

    function toggleStart() {
        setTimeout(function() {
            var checkbox = document.getElementById('confirm-checkbox');
            var btn      = document.getElementById('start-btn');
            if (checkbox.checked) {
                btn.disabled = false;
                btn.style.backgroundColor = '#16a34a';
                btn.style.cursor = 'pointer';
            } else {
                btn.disabled = true;
                btn.style.backgroundColor = '#9ca3af';
                btn.style.cursor = 'not-allowed';
            }
        }, 10);
    }

    function startExam() {
        if (examStarted) return;
        var checkbox = document.getElementById('confirm-checkbox');
        if (!checkbox.checked) { alert('Please tick the checkbox first.'); return; }

        examStarted = true;
        var btn = document.getElementById('start-btn');
        btn.disabled = true;
        btn.textContent = '⏳ Starting...';

        fetch("{{ route('student.exams.start', $exam) }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                window.location.href = data.redirect_url;
            } else {
                alert(data.message || 'Failed to start exam. Please try again.');
                examStarted = false; btn.disabled = false;
                btn.textContent = '🚀 Start Exam Now';
                btn.style.backgroundColor = '#16a34a';
            }
        })
        .catch(function() {
            alert('Network error. Please try again.');
            examStarted = false; btn.disabled = false;
            btn.textContent = '🚀 Start Exam Now';
            btn.style.backgroundColor = '#16a34a';
        });
    }
</script>
@endsection