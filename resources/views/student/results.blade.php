@extends('layouts.student')

@section('title', 'Results - Student')

@section('content')
<style>
    *, *::before, *::after { box-sizing: border-box; }

    .results-wrap { max-width: 72rem; margin: 0 auto; padding: 1.25rem 1rem 2rem; }

    .page-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem; margin-bottom:1.25rem; }
    .page-title  { font-size:1.375rem; font-weight:700; color:#111827; margin:0; }
    .page-sub    { font-size:.875rem; color:#9ca3af; }

    .card        { background:#fff; border-radius:.875rem; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:1.25rem; }
    .card-title  { font-size:1rem; font-weight:700; color:#111827; margin:0 0 1rem; }

    /* ── Desktop table ── */
    .tbl-wrap    { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    table        { width:100%; border-collapse:collapse; min-width:560px; }
    thead        { background:#f9fafb; }
    th           { padding:.625rem .875rem; text-align:left; font-size:.8125rem; font-weight:600; color:#6b7280; white-space:nowrap; border-bottom:1px solid #e5e7eb; }
    td           { padding:.75rem .875rem; font-size:.875rem; color:#374151; border-bottom:1px solid #f3f4f6; vertical-align:middle; }
    tr:hover td  { background:#f9fafb; }
    tr:last-child td { border-bottom:none; }

    .badge       { display:inline-block; padding:.275rem .625rem; border-radius:9999px; font-size:.75rem; font-weight:600; white-space:nowrap; }
    .badge-green  { background:#dcfce7; color:#166534; }
    .badge-yellow { background:#fef9c3; color:#92400e; }
    .badge-blue   { background:#dbeafe; color:#1e40af; }

    .score-good  { font-weight:700; color:#16a34a; }
    .score-pend  { color:#9ca3af; }

    /* ── Mobile cards (< 600px) ── */
    .mob-list    { display:none; flex-direction:column; gap:.75rem; }
    .mob-card    { background:#f9fafb; border-radius:.75rem; padding:.875rem; border:1px solid #e5e7eb; }
    .mob-top     { display:flex; align-items:flex-start; justify-content:space-between; gap:.5rem; margin-bottom:.5rem; }
    .mob-name    { font-size:.9375rem; font-weight:700; color:#111827; }
    .mob-cat     { font-size:.75rem; color:#9ca3af; margin-top:.125rem; }
    .mob-grid    { display:grid; grid-template-columns:1fr 1fr; gap:.375rem .75rem; margin-top:.625rem; }
    .mob-lbl     { font-size:.6875rem; color:#9ca3af; margin-bottom:.125rem; }
    .mob-val     { font-size:.8125rem; color:#374151; font-weight:500; }
    .mob-actions { display:flex; gap:.5rem; padding-top:.625rem; border-top:1px solid #e5e7eb; margin-top:.625rem; }

    .btn-view    { display:inline-flex; align-items:center; gap:.375rem; padding:.4rem .875rem; background:#4f46e5; color:#fff; border-radius:.5rem; font-size:.8125rem; font-weight:600; text-decoration:none; transition:background .15s; }
    .btn-view:hover { background:#4338ca; }

    .pager       { margin-top:1rem; }
    .empty-state { text-align:center; padding:2.5rem 1rem; color:#9ca3af; font-size:.9375rem; }

    @media (min-width: 768px) {
        .results-wrap { padding: 2rem 1.5rem; }
        .page-title   { font-size:1.875rem; }
        .card         { padding:1.5rem; }
        .card-title   { font-size:1.125rem; }
    }
    @media (max-width: 599px) {
        .tbl-wrap  { display: none; }
        .mob-list  { display: flex; }
    }
</style>

<div class="results-wrap">

    <div class="page-header">
        <h1 class="page-title">My Results</h1>
        <span class="page-sub">Exam performance overview</span>
    </div>

    <div class="card">
        <h2 class="card-title">Completed Exams</h2>

        {{-- ── Desktop Table ── --}}
        <div class="tbl-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Exam</th>
                        <th>Category</th>
                        <th>Submitted At</th>
                        <th>Score</th>
                        <th>Accuracy</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attempts as $attempt)
                    <tr>
                        <td style="font-weight:600;color:#111827;">{{ $attempt->exam->title }}</td>
                        <td>{{ $attempt->exam->examCategory->name ?? 'N/A' }}</td>
                        <td style="white-space:nowrap;">
                            {{ optional($attempt->submitted_at ?? $attempt->auto_submitted_at)->format('d M Y, h:i A') }}
                        </td>
                        <td>
                            @if($attempt->result)
                                <span class="score-good">
                                    {{ $attempt->result->obtained_marks }} / {{ $attempt->result->total_marks }}
                                    ({{ number_format($attempt->result->percentage, 2) }}%)
                                </span>
                            @else
                                <span class="score-pend">Pending</span>
                            @endif
                        </td>
                        <td>
                            @if($attempt->result)
                                {{ number_format($attempt->result->accuracy_percentage ?? 0, 2) }}%
                            @else
                                <span style="color:#9ca3af;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($attempt->result && $attempt->result->is_published)
                                <span class="badge badge-green">Published</span>
                            @else
                                <span class="badge badge-yellow">Under Review</span>
                            @endif
                        </td>
                        <td>
                            @if($attempt->result && $attempt->result->is_published)
                                <a href="#" class="btn-view">View Details</a>
                            @else
                                <span style="font-size:.75rem;color:#9ca3af;">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            No results available yet. Complete an exam to see your results here.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── Mobile Cards ── --}}
        <div class="mob-list">
            @forelse($attempts as $attempt)
            <div class="mob-card">
                <div class="mob-top">
                    <div>
                        <div class="mob-name">{{ $attempt->exam->title }}</div>
                        <div class="mob-cat">{{ $attempt->exam->examCategory->name ?? 'N/A' }}</div>
                    </div>
                    @if($attempt->result && $attempt->result->is_published)
                        <span class="badge badge-green">Published</span>
                    @else
                        <span class="badge badge-yellow">Under Review</span>
                    @endif
                </div>

                <div class="mob-grid">
                    <div>
                        <div class="mob-lbl">Submitted At</div>
                        <div class="mob-val">{{ optional($attempt->submitted_at ?? $attempt->auto_submitted_at)->format('d M Y, h:i A') }}</div>
                    </div>
                    <div>
                        <div class="mob-lbl">Accuracy</div>
                        <div class="mob-val">
                            @if($attempt->result)
                                {{ number_format($attempt->result->accuracy_percentage ?? 0, 2) }}%
                            @else
                                —
                            @endif
                        </div>
                    </div>
                    <div style="grid-column:1/-1;">
                        <div class="mob-lbl">Score</div>
                        <div class="mob-val">
                            @if($attempt->result)
                                <span class="score-good">
                                    {{ $attempt->result->obtained_marks }} / {{ $attempt->result->total_marks }}
                                    ({{ number_format($attempt->result->percentage, 2) }}%)
                                </span>
                            @else
                                <span class="score-pend">Pending</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($attempt->result && $attempt->result->is_published)
                <div class="mob-actions">
                    <a href="#" class="btn-view">View Details</a>
                </div>
                @endif
            </div>
            @empty
            <div class="empty-state">
                No results available yet. Complete an exam to see your results here.
            </div>
            @endforelse
        </div>

        @if($attempts->hasPages())
            <div class="pager">{{ $attempts->links() }}</div>
        @endif
    </div>
</div>
@endsection