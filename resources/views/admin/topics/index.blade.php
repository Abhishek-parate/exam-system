@extends('layouts.admin')
@section('title', 'Topics')

@section('content')
<style>
    .page-header { display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; flex-wrap:wrap; margin-bottom:1.25rem; }
    .page-title  { font-size:1.375rem; font-weight:700; color:#111827; margin:0; }
    .page-sub    { font-size:.8125rem; color:#9ca3af; margin:.25rem 0 0; }

    .btn { display:inline-flex; align-items:center; gap:.375rem; padding:.5rem 1.125rem; border-radius:.5rem; font-size:.875rem; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:background .15s; white-space:nowrap; }
    .btn-purple { background:#7c3aed; color:#fff; }
    .btn-purple:hover { background:#6d28d9; }
    .btn-yellow { background:#f59e0b; color:#fff; }
    .btn-yellow:hover { background:#d97706; }
    .btn-red    { background:#ef4444; color:#fff; }
    .btn-red:hover { background:#dc2626; }
    .btn-sm     { padding:.35rem .75rem; font-size:.75rem; }

    .flash { padding:.75rem 1rem; border-radius:.625rem; font-size:.875rem; font-weight:500; margin-bottom:1rem; }
    .flash-s { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
    .flash-e { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }

    .badge        { display:inline-block; padding:.25rem .625rem; border-radius:9999px; font-size:.75rem; font-weight:600; white-space:nowrap; }
    .badge-blue   { background:#dbeafe; color:#1e40af; }
    .badge-green  { background:#dcfce7; color:#166534; }
    .badge-red    { background:#fee2e2; color:#991b1b; }
    .badge-active { background:#dcfce7; color:#166534; }
    .badge-inact  { background:#fee2e2; color:#991b1b; }

    /* ── Desktop table ── */
    .card { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); overflow:hidden; }
    .tbl-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    table { width:100%; border-collapse:collapse; min-width:500px; }
    thead { background:#f9fafb; border-bottom:1px solid #e5e7eb; }
    th    { padding:.75rem 1.125rem; text-align:left; font-size:.6875rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; white-space:nowrap; }
    td    { padding:.875rem 1.125rem; font-size:.875rem; color:#374151; border-bottom:1px solid #f3f4f6; vertical-align:middle; }
    tr:last-child td { border-bottom:none; }
    tr:hover td { background:#f9fafb; }
    .act-row { display:flex; align-items:center; gap:.5rem; }

    /* ── Mobile cards ── */
    .mob-list { display:none; flex-direction:column; gap:.75rem; }
    .mob-card { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:1rem; }
    .mob-top  { display:flex; align-items:flex-start; justify-content:space-between; gap:.5rem; margin-bottom:.625rem; }
    .mob-name { font-size:.9375rem; font-weight:700; color:#111827; }
    .mob-meta { display:flex; flex-wrap:wrap; gap:.375rem; }
    .mob-foot { display:flex; gap:.5rem; padding-top:.625rem; border-top:1px solid #f3f4f6; }

    .pager { padding:1rem 1.125rem; border-top:1px solid #e5e7eb; }

    @media (max-width: 599px) {
        .tbl-wrap { display:none; }
        .mob-list { display:flex; }
        .pager    { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:1rem; margin-top:.75rem; }
    }
    @media (min-width: 768px) {
        .page-title { font-size:1.875rem; }
    }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">🏷️ Topics</h1>
        <p class="page-sub">Manage topics grouped by chapter</p>
    </div>
    <a href="{{ route('admin.topics.create') }}" class="btn btn-purple">
        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Topic
    </a>
</div>

@if(session('success'))
    <div class="flash flash-s">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="flash flash-e">❌ {{ session('error') }}</div>
@endif

{{-- Desktop table --}}
<div class="card">
    <div class="tbl-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Topic</th>
                    <th>Chapter</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topics as $topic)
                <tr>
                    <td style="color:#9ca3af;font-family:monospace;font-size:.75rem;">{{ $loop->iteration }}</td>
                    <td style="font-weight:600;color:#111827;">{{ $topic->name }}</td>
                    <td><span class="badge badge-blue">{{ $topic->chapter?->name ?? 'N/A' }}</span></td>
                    <td><span class="badge badge-green">{{ $topic->chapter?->subject?->name ?? 'N/A' }}</span></td>
                    <td>
                        @if($topic->is_active)
                            <span class="badge badge-active">Active</span>
                        @else
                            <span class="badge badge-inact">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div class="act-row">
                            <a href="{{ route('admin.topics.edit', $topic) }}" class="btn btn-yellow btn-sm">✏️ Edit</a>
                            <form action="{{ route('admin.topics.destroy', $topic) }}" method="POST"
                                  onsubmit="return confirm('Delete this topic?')" style="margin:0;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-red btn-sm">🗑 Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:3rem 1rem;color:#9ca3af;">
                        <p style="font-size:2rem;margin-bottom:.5rem;">📭</p>
                        <p>No topics found. <a href="{{ route('admin.topics.create') }}" style="color:#7c3aed;font-weight:600;text-decoration:none;">Add one</a>.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($topics->hasPages())
        <div class="pager">{{ $topics->links() }}</div>
    @endif
</div>

{{-- Mobile cards --}}
<div class="mob-list">
    @forelse($topics as $topic)
    <div class="mob-card">
        <div class="mob-top">
            <div>
                <div class="mob-name">{{ $topic->name }}</div>
            </div>
            @if($topic->is_active)
                <span class="badge badge-active">Active</span>
            @else
                <span class="badge badge-inact">Inactive</span>
            @endif
        </div>
        <div class="mob-meta">
            <span class="badge badge-blue">{{ $topic->chapter?->name ?? 'N/A' }}</span>
            <span class="badge badge-green">{{ $topic->chapter?->subject?->name ?? 'N/A' }}</span>
        </div>
        <div class="mob-foot">
            <a href="{{ route('admin.topics.edit', $topic) }}" class="btn btn-yellow btn-sm">✏️ Edit</a>
            <form action="{{ route('admin.topics.destroy', $topic) }}" method="POST"
                  onsubmit="return confirm('Delete this topic?')" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-red btn-sm">🗑 Delete</button>
            </form>
        </div>
    </div>
    @empty
    <div style="background:#fff;border-radius:.75rem;padding:2.5rem 1rem;text-align:center;color:#9ca3af;box-shadow:0 1px 4px rgba(0,0,0,.08);">
        <p style="font-size:2rem;margin-bottom:.5rem;">📭</p>
        <p>No topics found. <a href="{{ route('admin.topics.create') }}" style="color:#7c3aed;font-weight:600;text-decoration:none;">Add one</a>.</p>
    </div>
    @endforelse

    @if($topics->hasPages())
        <div class="pager">{{ $topics->links() }}</div>
    @endif
</div>

@endsection