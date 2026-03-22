@extends('layouts.admin')

@section('title', 'Subjects Management')

@section('content')
<style>
    /* ── Page layout ── */
    .page-header { display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; margin-bottom:1.5rem; }
    .page-title  { font-size:1.5rem; font-weight:700; color:#111827; margin:0; }

    /* ── Filter card ── */
    .card { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); }
    .filter-card { padding:1.25rem; margin-bottom:1.25rem; }
    .filter-grid { display:grid; grid-template-columns:1fr; gap:.875rem; }

    .form-label  { display:block; font-size:.8125rem; font-weight:600; color:#374151; margin-bottom:.375rem; }
    .form-ctrl   { width:100%; padding:.5rem .75rem; border:1px solid #d1d5db; border-radius:.5rem; font-size:.875rem; color:#374151; background:#fff; outline:none; box-sizing:border-box; }
    .form-ctrl:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.1); }

    .btn         { display:inline-flex; align-items:center; gap:.375rem; padding:.5rem 1.25rem; border-radius:.5rem; font-size:.875rem; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:background .15s; white-space:nowrap; }
    .btn-primary { background:#2563eb; color:#fff; }
    .btn-primary:hover { background:#1d4ed8; }
    .btn-gray    { background:#e5e7eb; color:#374151; }
    .btn-gray:hover { background:#d1d5db; }
    .btn-sm      { padding:.375rem .875rem; font-size:.8125rem; }
    .btn-row     { display:flex; gap:.5rem; flex-wrap:wrap; }

    /* ── Table ── */
    .table-wrap  { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    table        { width:100%; border-collapse:collapse; min-width:640px; }
    thead        { background:#f9fafb; }
    th           { padding:.75rem 1rem; text-align:left; font-size:.75rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; border-bottom:1px solid #e5e7eb; white-space:nowrap; }
    td           { padding:.875rem 1rem; font-size:.875rem; color:#374151; border-bottom:1px solid #f3f4f6; vertical-align:middle; }
    tr:hover td  { background:#f9fafb; }
    .subject-name { font-weight:600; color:#111827; }
    .subject-desc { font-size:.75rem; color:#9ca3af; max-width:12rem; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

    /* ── Badges ── */
    .badge       { display:inline-block; padding:.25rem .625rem; border-radius:9999px; font-size:.75rem; font-weight:600; white-space:nowrap; }
    .badge-purple{ background:#f3e8ff; color:#7e22ce; }
    .badge-blue  { background:#dbeafe; color:#1e40af; }
    .badge-green { background:#dcfce7; color:#166534; }
    .badge-red   { background:#fee2e2; color:#991b1b; }

    /* ── Action links ── */
    .act-view   { color:#2563eb; font-weight:600; text-decoration:none; font-size:.8125rem; }
    .act-view:hover  { color:#1d4ed8; text-decoration:underline; }
    .act-edit   { color:#d97706; font-weight:600; text-decoration:none; font-size:.8125rem; }
    .act-edit:hover  { color:#b45309; text-decoration:underline; }
    .act-del    { color:#dc2626; font-weight:600; background:none; border:none; cursor:pointer; font-size:.8125rem; padding:0; font-family:inherit; }
    .act-del:hover   { color:#b91c1c; text-decoration:underline; }
    .act-row    { display:flex; gap:.75rem; align-items:center; }

    /* ── Mobile card list (< 640px) ── */
    .mob-list   { display:none; flex-direction:column; gap:.75rem; }
    .mob-card   { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:1rem; }
    .mob-card-top { display:flex; justify-content:space-between; align-items:flex-start; gap:.5rem; margin-bottom:.625rem; }
    .mob-card-meta { display:grid; grid-template-columns:1fr 1fr; gap:.375rem .75rem; margin-bottom:.75rem; }
    .mob-meta-label { font-size:.6875rem; color:#9ca3af; font-weight:500; }
    .mob-meta-val   { font-size:.8125rem; color:#374151; font-weight:500; }
    .mob-actions    { display:flex; gap:.75rem; padding-top:.625rem; border-top:1px solid #f3f4f6; }

    /* ── Flash ── */
    .flash { padding:.75rem 1rem; border-radius:.625rem; font-size:.875rem; font-weight:500; margin-bottom:1rem; }
    .flash-s { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
    .flash-e { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }

    /* ── Pagination ── */
    .pager { padding:1rem; border-top:1px solid #e5e7eb; }

    /* ── Responsive ── */
    @media (min-width: 480px) {
        .filter-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (min-width: 768px) {
        .page-title { font-size:1.875rem; }
        .filter-grid { grid-template-columns: 1fr 1fr 1fr auto; align-items:end; }
        .filter-card { padding:1.5rem; }
    }
    @media (max-width: 639px) {
        .table-wrap { display:none; }
        .mob-list   { display:flex; }
    }
</style>

{{-- ── Page Header ── --}}
<div class="page-header">
    <h1 class="page-title">Subjects Management</h1>
    <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary">
        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add New Subject
    </a>
</div>

{{-- Flash --}}
@if(session('success'))
    <div class="flash flash-s">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="flash flash-e">❌ {{ session('error') }}</div>
@endif

{{-- ── Filters ── --}}
<div class="card filter-card">
    <form method="GET" action="{{ route('admin.subjects.index') }}">
        <div class="filter-grid">
            <div>
                <label class="form-label">Exam Category</label>
                <select name="exam_category_id" class="form-ctrl">
                    <option value="">All Categories</option>
                    @foreach($examCategories as $category)
                        <option value="{{ $category->id }}" {{ request('exam_category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Status</label>
                <select name="status" class="form-ctrl">
                    <option value="">All Status</option>
                    <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div>
                <label class="form-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search subjects..." class="form-ctrl">
            </div>
            <div class="btn-row" style="padding-top:.25rem;">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.subjects.index') }}" class="btn btn-gray">Clear</a>
            </div>
        </div>
    </form>
</div>

{{-- ── Desktop Table ── --}}
<div class="card" style="overflow:hidden;">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Subject Name</th>
                    <th>Code</th>
                    <th>Category</th>
                    <th>Chapters</th>
                    <th>Questions</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subjects as $subject)
                <tr>
                    <td style="color:#9ca3af;">{{ $subject->id }}</td>
                    <td>
                        <div class="subject-name">{{ $subject->name }}</div>
                        @if($subject->description)
                            <div class="subject-desc">{{ $subject->description }}</div>
                        @endif
                    </td>
                    <td style="color:#6b7280;">{{ $subject->code ?? 'N/A' }}</td>
                    <td style="color:#6b7280;">{{ $subject->examCategory?->name ?? 'No Category' }}</td>
                    <td><span class="badge badge-purple">{{ $subject->chapters_count ?? 0 }} Chapters</span></td>
                    <td><span class="badge badge-blue">{{ $subject->questions_count ?? 0 }} Questions</span></td>
                    <td>
                        @if($subject->is_active)
                            <span class="badge badge-green">Active</span>
                        @else
                            <span class="badge badge-red">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <div class="act-row">
                            <a href="{{ route('admin.subjects.show', $subject) }}" class="act-view">View</a>
                            <a href="{{ route('admin.subjects.edit', $subject) }}" class="act-edit">Edit</a>
                            <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST"
                                  onsubmit="return confirm('Delete this subject?')" style="margin:0;">
                                @csrf @method('DELETE')
                                <button type="submit" class="act-del">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:3rem 1rem;color:#9ca3af;">
                        <p style="font-size:1rem;margin-bottom:.5rem;">No subjects found</p>
                        <a href="{{ route('admin.subjects.create') }}" style="color:#2563eb;font-weight:600;text-decoration:none;">Create your first subject →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($subjects->hasPages())
        <div class="pager">{{ $subjects->links() }}</div>
    @endif
</div>

{{-- ── Mobile Card List (shown < 640px via CSS) ── --}}
<div class="mob-list">
    @forelse($subjects as $subject)
    <div class="mob-card">
        <div class="mob-card-top">
            <div>
                <div class="subject-name" style="font-size:.9375rem;">{{ $subject->name }}</div>
                <div style="font-size:.75rem;color:#9ca3af;margin-top:.125rem;">
                    {{ $subject->examCategory?->name ?? 'No Category' }}
                    @if($subject->code) · {{ $subject->code }} @endif
                </div>
            </div>
            @if($subject->is_active)
                <span class="badge badge-green">Active</span>
            @else
                <span class="badge badge-red">Inactive</span>
            @endif
        </div>
        <div class="mob-card-meta">
            <div>
                <div class="mob-meta-label">Chapters</div>
                <span class="badge badge-purple" style="margin-top:.125rem;">{{ $subject->chapters_count ?? 0 }}</span>
            </div>
            <div>
                <div class="mob-meta-label">Questions</div>
                <span class="badge badge-blue" style="margin-top:.125rem;">{{ $subject->questions_count ?? 0 }}</span>
            </div>
        </div>
        @if($subject->description)
            <div style="font-size:.75rem;color:#6b7280;margin-bottom:.625rem;line-height:1.4;">{{ Str::limit($subject->description, 80) }}</div>
        @endif
        <div class="mob-actions">
            <a href="{{ route('admin.subjects.show', $subject) }}" class="act-view">View</a>
            <a href="{{ route('admin.subjects.edit', $subject) }}" class="act-edit">Edit</a>
            <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST"
                  onsubmit="return confirm('Delete this subject?')" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" class="act-del">Delete</button>
            </form>
        </div>
    </div>
    @empty
    <div class="card" style="padding:2rem;text-align:center;color:#9ca3af;">
        <p style="margin-bottom:.5rem;">No subjects found</p>
        <a href="{{ route('admin.subjects.create') }}" style="color:#2563eb;font-weight:600;text-decoration:none;">Create your first subject →</a>
    </div>
    @endforelse

    @if($subjects->hasPages())
        <div style="background:#fff;border-radius:.75rem;padding:1rem;box-shadow:0 1px 4px rgba(0,0,0,.08);">
            {{ $subjects->links() }}
        </div>
    @endif
</div>

@endsection