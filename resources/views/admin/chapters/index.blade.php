@extends('layouts.admin')
@section('title', 'Chapters')

@section('content')
<style>
    .page-header { display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; flex-wrap:wrap; margin-bottom:1.25rem; }
    .page-title  { font-size:1.375rem; font-weight:700; color:#111827; margin:0; }
    .page-sub    { font-size:.8125rem; color:#9ca3af; margin:.25rem 0 0; }

    .btn { display:inline-flex; align-items:center; gap:.375rem; padding:.5rem 1.125rem; border-radius:.5rem; font-size:.875rem; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:background .15s; white-space:nowrap; }
    .btn-primary { background:#2563eb; color:#fff; }
    .btn-primary:hover { background:#1d4ed8; }
    .btn-yellow  { background:#f59e0b; color:#fff; }
    .btn-yellow:hover { background:#d97706; }
    .btn-red     { background:#ef4444; color:#fff; }
    .btn-red:hover { background:#dc2626; }
    .btn-sm      { padding:.35rem .75rem; font-size:.75rem; }

    .flash { padding:.75rem 1rem; border-radius:.625rem; font-size:.875rem; font-weight:500; margin-bottom:1rem; }
    .flash-s { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
    .flash-e { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }

    .badge { display:inline-block; padding:.25rem .625rem; border-radius:9999px; font-size:.75rem; font-weight:600; white-space:nowrap; }
    .badge-blue   { background:#dbeafe; color:#1e40af; }
    .badge-purple { background:#f3e8ff; color:#7e22ce; }
    .badge-green  { background:#dcfce7; color:#166534; }
    .badge-red    { background:#fee2e2; color:#991b1b; }

    /* ── Desktop table ── */
    .card  { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); overflow:hidden; }
    .table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    table  { width:100%; border-collapse:collapse; min-width:560px; }
    thead  { background:#f9fafb; border-bottom:1px solid #e5e7eb; }
    th     { padding:.75rem 1.125rem; text-align:left; font-size:.6875rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; white-space:nowrap; }
    td     { padding:.875rem 1.125rem; font-size:.875rem; color:#374151; border-bottom:1px solid #f3f4f6; vertical-align:middle; }
    tr:last-child td { border-bottom:none; }
    tr:hover td { background:#f9fafb; }
    .act-row { display:flex; align-items:center; gap:.5rem; }

    /* ── Mobile cards (< 600px) ── */
    .mob-list { display:none; flex-direction:column; gap:.75rem; }
    .mob-card { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:1rem; }
    .mob-top  { display:flex; align-items:flex-start; justify-content:space-between; gap:.5rem; margin-bottom:.625rem; }
    .mob-name { font-size:.9375rem; font-weight:700; color:#111827; }
    .mob-meta { display:flex; flex-wrap:wrap; gap:.375rem; margin-bottom:.75rem; }
    .mob-foot { display:flex; gap:.5rem; padding-top:.625rem; border-top:1px solid #f3f4f6; }

    .pager { padding:1rem 1.125rem; border-top:1px solid #e5e7eb; }

    @media (max-width: 599px) {
        .table-wrap { display:none; }
        .mob-list   { display:flex; }
        .pager      { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:1rem; margin-top:.75rem; }
    }
    @media (min-width: 768px) {
        .page-title { font-size:1.875rem; }
    }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">📚 Chapters</h1>
        <p class="page-sub">Manage chapters grouped by subject</p>
    </div>
    <a href="{{ route('admin.chapters.create') }}" class="btn btn-primary">
        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Chapter
    </a>
</div>

@if(session('success'))
    <div class="flash flash-s">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="flash flash-e">❌ {{ session('error') }}</div>
@endif

{{-- ── Desktop table ── --}}
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Chapter</th>
                    <th>Subject</th>
                    <th>No.</th>
                    <th>Topics</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($chapters as $chapter)
                <tr>
                    <td style="color:#9ca3af;font-family:monospace;font-size:.75rem;">{{ $loop->iteration }}</td>
                    <td style="font-weight:600;color:#111827;">{{ $chapter->name }}</td>
                    <td><span class="badge badge-blue">{{ $chapter->subject?->name ?? 'N/A' }}</span></td>
                    <td style="color:#6b7280;">{{ $chapter->chapter_number ? 'Ch. '.$chapter->chapter_number : '—' }}</td>
                    <td><span class="badge badge-purple">{{ $chapter->topics_count ?? $chapter->topics->count() }} topics</span></td>
                    <td>
                        @if($chapter->is_active)
                            <span class="badge badge-green">Active</span>
                        @else
                            <span class="badge badge-red">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div class="act-row">
                            <a href="{{ route('admin.chapters.edit', $chapter) }}" class="btn btn-yellow btn-sm">✏️ Edit</a>
                            <form action="{{ route('admin.chapters.destroy', $chapter) }}" method="POST"
                                  onsubmit="return confirm('Delete this chapter?')" style="margin:0;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-red btn-sm">🗑 Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:3rem 1rem;color:#9ca3af;">
                        <p style="font-size:2rem;margin-bottom:.5rem;">📭</p>
                        <p>No chapters found. <a href="{{ route('admin.chapters.create') }}" style="color:#2563eb;font-weight:600;text-decoration:none;">Add one</a>.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($chapters->hasPages())
        <div class="pager">{{ $chapters->links() }}</div>
    @endif
</div>

{{-- ── Mobile card list ── --}}
<div class="mob-list">
    @forelse($chapters as $chapter)
    <div class="mob-card">
        <div class="mob-top">
            <div>
                <div class="mob-name">{{ $chapter->name }}</div>
                <div style="font-size:.75rem;color:#9ca3af;margin-top:.125rem;">
                    {{ $chapter->chapter_number ? 'Ch. '.$chapter->chapter_number : '' }}
                </div>
            </div>
            @if($chapter->is_active)
                <span class="badge badge-green">Active</span>
            @else
                <span class="badge badge-red">Inactive</span>
            @endif
        </div>
        <div class="mob-meta">
            <span class="badge badge-blue">{{ $chapter->subject?->name ?? 'N/A' }}</span>
            <span class="badge badge-purple">{{ $chapter->topics_count ?? $chapter->topics->count() }} topics</span>
        </div>
        <div class="mob-foot">
            <a href="{{ route('admin.chapters.edit', $chapter) }}" class="btn btn-yellow btn-sm">✏️ Edit</a>
            <form action="{{ route('admin.chapters.destroy', $chapter) }}" method="POST"
                  onsubmit="return confirm('Delete this chapter?')" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-red btn-sm">🗑 Delete</button>
            </form>
        </div>
    </div>
    @empty
    <div style="background:#fff;border-radius:.75rem;padding:2.5rem 1rem;text-align:center;color:#9ca3af;box-shadow:0 1px 4px rgba(0,0,0,.08);">
        <p style="font-size:2rem;margin-bottom:.5rem;">📭</p>
        <p>No chapters found. <a href="{{ route('admin.chapters.create') }}" style="color:#2563eb;font-weight:600;text-decoration:none;">Add one</a>.</p>
    </div>
    @endforelse

    @if($chapters->hasPages())
        <div class="pager">{{ $chapters->links() }}</div>
    @endif
</div>

@endsection