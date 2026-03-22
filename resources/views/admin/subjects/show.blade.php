@extends('layouts.admin')

@section('title', 'Subject Details')

@section('content')
<style>
    /* ── Header ── */
    .page-header { display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; flex-wrap:wrap; margin-bottom:1.5rem; }
    .page-title  { font-size:1.375rem; font-weight:700; color:#111827; margin:0; word-break:break-word; }
    .hdr-actions { display:flex; gap:.625rem; flex-wrap:wrap; flex-shrink:0; }

    /* ── Cards ── */
    .card  { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); margin-bottom:1.25rem; }
    .card-body { padding:1.25rem; }
    .card-title { font-size:1rem; font-weight:700; color:#111827; margin:0 0 1rem; }

    /* ── Stat cards grid ── */
    .stat-grid { display:grid; grid-template-columns:1fr; gap:.875rem; margin-bottom:1.25rem; }
    .stat-card { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:1.125rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; }
    .stat-label { font-size:.8125rem; color:#6b7280; font-weight:500; margin-bottom:.25rem; }
    .stat-val   { font-size:1.75rem; font-weight:700; color:#111827; line-height:1; }
    .stat-icon  { width:2.75rem; height:2.75rem; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .stat-icon svg { width:1.375rem; height:1.375rem; }
    .ic-blue   { background:#dbeafe; color:#2563eb; }
    .ic-green  { background:#dcfce7; color:#16a34a; }
    .ic-purple { background:#f3e8ff; color:#9333ea; }

    /* ── Info grid ── */
    .info-grid { display:grid; grid-template-columns:1fr; gap:1rem; }
    .info-item {}
    .info-lbl  { font-size:.8125rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:.04em; margin-bottom:.25rem; }
    .info-val  { font-size:.9375rem; color:#111827; }

    /* ── Badge ── */
    .badge { display:inline-block; padding:.3rem .75rem; border-radius:9999px; font-size:.8125rem; font-weight:600; }
    .badge-green { background:#dcfce7; color:#166534; }
    .badge-red   { background:#fee2e2; color:#991b1b; }

    /* ── Buttons ── */
    .btn     { display:inline-flex; align-items:center; gap:.375rem; padding:.5rem 1.125rem; border-radius:.5rem; font-size:.8125rem; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:background .15s; white-space:nowrap; }
    .btn-yellow  { background:#f59e0b; color:#fff; }
    .btn-yellow:hover  { background:#d97706; }
    .btn-gray    { background:#e5e7eb; color:#374151; }
    .btn-gray:hover    { background:#d1d5db; }
    .btn-blue    { background:#2563eb; color:#fff; }
    .btn-blue:hover    { background:#1d4ed8; }
    .btn-green   { background:#16a34a; color:#fff; }
    .btn-green:hover   { background:#15803d; }
    .btn-row { display:flex; gap:.625rem; flex-wrap:wrap; }

    @media (min-width: 480px) {
        .stat-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (min-width: 768px) {
        .page-title  { font-size:1.875rem; }
        .stat-grid   { grid-template-columns: repeat(3, 1fr); }
        .info-grid   { grid-template-columns: 1fr 1fr; }
        .card-body   { padding:1.75rem; }
        .card-title  { font-size:1.25rem; }
        .stat-val    { font-size:2rem; }
    }
</style>

{{-- ── Header ── --}}
<div class="page-header">
    <h1 class="page-title">📚 {{ $subject->name }}</h1>
    <div class="hdr-actions">
        <a href="{{ route('admin.subjects.edit', $subject) }}" class="btn btn-yellow">✏️ Edit Subject</a>
        <a href="{{ route('admin.subjects.index') }}" class="btn btn-gray">← Back</a>
    </div>
</div>

{{-- ── Stat Cards ── --}}
<div class="stat-grid">
    <div class="stat-card">
        <div>
            <p class="stat-label">Total Questions</p>
            <p class="stat-val">{{ $stats['total_questions'] }}</p>
        </div>
        <div class="stat-icon ic-blue">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <p class="stat-label">Active Questions</p>
            <p class="stat-val" style="color:#16a34a;">{{ $stats['active_questions'] }}</p>
        </div>
        <div class="stat-icon ic-green">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
    </div>
    <div class="stat-card">
        <div>
            <p class="stat-label">Total Chapters</p>
            <p class="stat-val" style="color:#9333ea;">{{ $stats['total_chapters'] }}</p>
        </div>
        <div class="stat-icon ic-purple">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
    </div>
</div>

{{-- ── Subject Info ── --}}
<div class="card">
    <div class="card-body">
        <p class="card-title">Subject Information</p>
        <div class="info-grid">
            <div class="info-item">
                <p class="info-lbl">Subject ID</p>
                <p class="info-val">#{{ $subject->id }}</p>
            </div>
            <div class="info-item">
                <p class="info-lbl">Subject Code</p>
                <p class="info-val">{{ $subject->code ?? 'N/A' }}</p>
            </div>
            <div class="info-item">
                <p class="info-lbl">Exam Category</p>
                <p class="info-val">{{ $subject->examCategory?->name ?? 'No Category' }}</p>
            </div>
            <div class="info-item">
                <p class="info-lbl">Status</p>
                @if($subject->is_active)
                    <span class="badge badge-green">✓ Active</span>
                @else
                    <span class="badge badge-red">✗ Inactive</span>
                @endif
            </div>
            @if($subject->description)
            <div class="info-item" style="grid-column:1/-1;">
                <p class="info-lbl">Description</p>
                <p class="info-val" style="line-height:1.6;color:#374151;">{{ $subject->description }}</p>
            </div>
            @endif
            <div class="info-item">
                <p class="info-lbl">Created At</p>
                <p class="info-val">{{ $subject->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div class="info-item">
                <p class="info-lbl">Last Updated</p>
                <p class="info-val">{{ $subject->updated_at->format('d M Y, h:i A') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- ── Quick Actions ── --}}
<div class="card">
    <div class="card-body">
        <p class="card-title">Quick Actions</p>
        <div class="btn-row">
            <a href="{{ route('admin.questions.create') }}?subject_id={{ $subject->id }}" class="btn btn-blue">
                ➕ Add Question
            </a>
            <a href="{{ route('admin.questions.index') }}?subject_id={{ $subject->id }}" class="btn btn-green">
                📋 View All Questions
            </a>
        </div>
    </div>
</div>

@endsection