@extends('layouts.student')

@section('title', 'My Exams - Student')

@section('content')
<style>
    *, *::before, *::after { box-sizing: border-box; }

    .exams-wrap { max-width: 72rem; margin: 0 auto; padding: 1.25rem 1rem; }

    /* ── Section header ── */
    .page-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem; margin-bottom:1.25rem; }
    .page-title  { font-size:1.375rem; font-weight:700; color:#111827; margin:0; }
    .page-sub    { font-size:.875rem; color:#9ca3af; }

    /* ── Section cards ── */
    .section      { background:#fff; border-radius:.875rem; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:1.125rem; margin-bottom:1.25rem; }
    .section-title { display:flex; align-items:center; gap:.5rem; font-size:1rem; font-weight:700; margin:0 0 1rem; }
    .section-title svg { width:1.125rem; height:1.125rem; flex-shrink:0; }

    /* ── Available exam cards ── */
    .exam-grid    { display:grid; grid-template-columns:1fr; gap:.875rem; }
    .exam-card    { border:2px solid #bbf7d0; border-radius:.75rem; padding:1rem; background:#f0fdf4; transition:box-shadow .2s; }
    .exam-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.1); }
    .exam-card-title { font-size:1rem; font-weight:700; color:#111827; margin:0 0 .625rem; }
    .exam-meta    { display:grid; grid-template-columns:1fr 1fr; gap:.25rem .75rem; font-size:.8125rem; color:#4b5563; margin-bottom:.875rem; }
    .exam-meta strong { color:#111827; }
    .btn-start    { display:block; width:100%; background:#16a34a; color:#fff; font-size:.9375rem; font-weight:700; padding:.75rem; border-radius:.625rem; text-align:center; text-decoration:none; transition:background .15s; border:none; }
    .btn-start:hover { background:#15803d; }

    .empty-state  { text-align:center; padding:2rem 1rem; color:#9ca3af; font-size:.9375rem; }

    /* ── Tables ── */
    .tbl-wrap     { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    table         { width:100%; border-collapse:collapse; min-width:480px; }
    thead         { background:#f9fafb; }
    th            { padding:.625rem .875rem; text-align:left; font-size:.8125rem; font-weight:600; color:#6b7280; white-space:nowrap; border-bottom:1px solid #e5e7eb; }
    td            { padding:.75rem .875rem; font-size:.875rem; color:#374151; border-bottom:1px solid #f3f4f6; vertical-align:middle; }
    tr:hover td   { background:#f9fafb; }
    tr:last-child td { border-bottom:none; }

    .badge        { display:inline-block; padding:.25rem .625rem; border-radius:9999px; font-size:.75rem; font-weight:600; white-space:nowrap; }
    .badge-green  { background:#dcfce7; color:#166534; }
    .badge-yellow { background:#fef9c3; color:#92400e; }

    /* ── Mobile table cards (< 600px) ── */
    .mob-tbl-list { display:none; flex-direction:column; gap:.625rem; }
    .mob-tbl-card { background:#f9fafb; border-radius:.625rem; padding:.875rem; border:1px solid #e5e7eb; }
    .mob-tbl-title { font-weight:700; color:#111827; font-size:.9375rem; margin-bottom:.25rem; }
    .mob-tbl-meta  { display:grid; grid-template-columns:1fr 1fr; gap:.25rem .625rem; font-size:.75rem; color:#6b7280; margin-top:.5rem; }
    .mob-tbl-meta-label { font-size:.6875rem; color:#9ca3af; margin-bottom:.125rem; }
    .mob-tbl-meta-val   { font-size:.8125rem; color:#374151; font-weight:500; }

    @media (min-width: 480px) {
        .exam-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (min-width: 768px) {
        .exams-wrap { padding: 2rem 1.5rem; }
        .page-title { font-size:1.875rem; }
        .exam-grid  { grid-template-columns: repeat(3, 1fr); }
        .section    { padding:1.5rem; }
        .section-title { font-size:1.125rem; }
    }
    @media (max-width: 599px) {
        .tbl-wrap      { display: none; }
        .mob-tbl-list  { display: flex; }
    }
</style>

<div class="exams-wrap">
    <div class="page-header">
        <h1 class="page-title">My Exams</h1>
        <span class="page-sub">Enrolled &amp; Available Exams</span>
    </div>

    {{-- ── Available Now ── --}}
    <div class="section">
        <h2 class="section-title" style="color:#16a34a;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Available Now ({{ $availableExams->count() }})
        </h2>
        <div class="exam-grid">
            @forelse($availableExams as $exam)
            <div class="exam-card">
                <h3 class="exam-card-title">{{ $exam->title }}</h3>
                <div class="exam-meta">
                    <div><div style="font-size:.6875rem;color:#9ca3af;">Category</div><div style="font-weight:600;color:#374151;font-size:.8125rem;">{{ $exam->examCategory->name ?? 'N/A' }}</div></div>
                    <div><div style="font-size:.6875rem;color:#9ca3af;">Duration</div><div style="font-weight:600;color:#374151;font-size:.8125rem;">{{ $exam->duration_minutes }} min</div></div>
                    <div><div style="font-size:.6875rem;color:#9ca3af;">Total Marks</div><div style="font-weight:600;color:#374151;font-size:.8125rem;">{{ $exam->total_marks }}</div></div>
                    <div><div style="font-size:.6875rem;color:#9ca3af;">Ends</div><div style="font-weight:600;color:#dc2626;font-size:.8125rem;">{{ $exam->end_time->format('d M, h:i A') }}</div></div>
                </div>
                <a href="{{ route('student.exams.instructions', $exam) }}" class="btn-start">🚀 Start Exam</a>
            </div>
            @empty
            <div class="empty-state" style="grid-column:1/-1;">No exams available to take right now.</div>
            @endforelse
        </div>
    </div>

    {{-- ── Upcoming Exams ── --}}
    <div class="section">
        <h2 class="section-title" style="color:#2563eb;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Upcoming Exams ({{ $upcomingExams->count() }})
        </h2>

        {{-- Desktop table --}}
        <div class="tbl-wrap">
            <table>
                <thead><tr>
                    <th>Exam Title</th><th>Category</th><th>Start Time</th><th>Duration</th><th>Marks</th>
                </tr></thead>
                <tbody>
                    @forelse($upcomingExams as $exam)
                    <tr>
                        <td style="font-weight:600;color:#111827;">{{ $exam->title }}</td>
                        <td>{{ $exam->examCategory->name ?? 'N/A' }}</td>
                        <td style="white-space:nowrap;">{{ $exam->start_time->format('d M Y, h:i A') }}</td>
                        <td>{{ $exam->duration_minutes }} min</td>
                        <td>{{ $exam->total_marks }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="empty-state">No upcoming exams scheduled.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="mob-tbl-list">
            @forelse($upcomingExams as $exam)
            <div class="mob-tbl-card">
                <div class="mob-tbl-title">{{ $exam->title }}</div>
                <div style="font-size:.75rem;color:#9ca3af;">{{ $exam->examCategory->name ?? 'N/A' }}</div>
                <div class="mob-tbl-meta">
                    <div><div class="mob-tbl-meta-label">Start Time</div><div class="mob-tbl-meta-val">{{ $exam->start_time->format('d M Y, h:i A') }}</div></div>
                    <div><div class="mob-tbl-meta-label">Duration</div><div class="mob-tbl-meta-val">{{ $exam->duration_minutes }} min</div></div>
                    <div><div class="mob-tbl-meta-label">Marks</div><div class="mob-tbl-meta-val">{{ $exam->total_marks }}</div></div>
                </div>
            </div>
            @empty
            <div class="empty-state">No upcoming exams scheduled.</div>
            @endforelse
        </div>
    </div>

    {{-- ── Completed Exams ── --}}
    <div class="section">
        <h2 class="section-title" style="color:#9333ea;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Completed Exams ({{ $completedExams->count() }})
        </h2>

        {{-- Desktop table --}}
        <div class="tbl-wrap">
            <table>
                <thead><tr>
                    <th>Exam Title</th><th>Category</th><th>Submitted</th><th>Time Taken</th><th>Score</th><th>Status</th>
                </tr></thead>
                <tbody>
                    @forelse($completedExams as $attempt)
                    <tr>
                        <td style="font-weight:600;color:#111827;">{{ $attempt->exam->title }}</td>
                        <td>{{ $attempt->exam->examCategory->name ?? 'N/A' }}</td>
                        <td style="white-space:nowrap;">{{ $attempt->submitted_at->format('d M Y, h:i A') }}</td>
                        <td>{{ round($attempt->time_taken_seconds / 60) }} min</td>
                        <td>
                            @if($attempt->result)
                                <span style="font-weight:700;color:#16a34a;">{{ number_format($attempt->result->percentage, 2) }}%</span>
                            @else
                                <span style="color:#9ca3af;">Pending</span>
                            @endif
                        </td>
                        <td>
                            @if($attempt->result && $attempt->result->is_published)
                                <span class="badge badge-green">Published</span>
                            @else
                                <span class="badge badge-yellow">Under Review</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="empty-state">No completed exams yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="mob-tbl-list">
            @forelse($completedExams as $attempt)
            <div class="mob-tbl-card">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.5rem;margin-bottom:.375rem;">
                    <div>
                        <div class="mob-tbl-title">{{ $attempt->exam->title }}</div>
                        <div style="font-size:.75rem;color:#9ca3af;">{{ $attempt->exam->examCategory->name ?? 'N/A' }}</div>
                    </div>
                    @if($attempt->result && $attempt->result->is_published)
                        <span class="badge badge-green">Published</span>
                    @else
                        <span class="badge badge-yellow">Review</span>
                    @endif
                </div>
                <div class="mob-tbl-meta">
                    <div><div class="mob-tbl-meta-label">Submitted</div><div class="mob-tbl-meta-val">{{ $attempt->submitted_at->format('d M Y') }}</div></div>
                    <div><div class="mob-tbl-meta-label">Time Taken</div><div class="mob-tbl-meta-val">{{ round($attempt->time_taken_seconds / 60) }} min</div></div>
                    <div><div class="mob-tbl-meta-label">Score</div>
                        <div class="mob-tbl-meta-val">
                            @if($attempt->result)
                                <span style="color:#16a34a;font-weight:700;">{{ number_format($attempt->result->percentage, 2) }}%</span>
                            @else
                                <span style="color:#9ca3af;">Pending</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">No completed exams yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection