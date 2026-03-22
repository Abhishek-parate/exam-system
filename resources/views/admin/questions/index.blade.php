@extends('layouts.admin')

@section('title', 'Questions - Admin')

@section('content')
<style>
    .page-header { display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; flex-wrap:wrap; margin-bottom:1.25rem; }
    .page-title  { font-size:1.375rem; font-weight:700; color:#111827; margin:0; }
    .page-sub    { font-size:.8125rem; color:#9ca3af; margin:.25rem 0 0; }
    .btn { display:inline-flex; align-items:center; gap:.375rem; padding:.5rem 1.125rem; border-radius:.5rem; font-size:.875rem; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:background .15s; white-space:nowrap; }
    .btn-primary { background:#2563eb; color:#fff; }
    .btn-primary:hover { background:#1d4ed8; }
    .btn-dark { background:#1f2937; color:#fff; }
    .btn-dark:hover { background:#111827; }
    .btn-gray { background:#e5e7eb; color:#374151; }
    .btn-gray:hover { background:#d1d5db; }
    .filter-card { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:1.125rem; margin-bottom:1.25rem; }
    .filter-grid { display:grid; grid-template-columns:1fr; gap:.75rem; }
    .form-ctrl { width:100%; padding:.5rem .75rem; border:1px solid #d1d5db; border-radius:.5rem; font-size:.875rem; color:#374151; background:#fff; outline:none; box-sizing:border-box; }
    .form-ctrl:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.1); }
    .btn-row-filter { display:flex; gap:.5rem; }
    .card { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); overflow:hidden; }
    .tbl-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    table { width:100%; border-collapse:collapse; min-width:640px; }
    thead { background:#f9fafb; }
    th { padding:.625rem 1rem; text-align:left; font-size:.6875rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; border-bottom:1px solid #e5e7eb; white-space:nowrap; }
    td { padding:.875rem 1rem; font-size:.875rem; color:#374151; border-bottom:1px solid #f3f4f6; vertical-align:middle; }
    tr:last-child td { border-bottom:none; }
    tr:hover td { background:#f9fafb; }
    .badge { display:inline-block; padding:.2rem .5rem; border-radius:9999px; font-size:.6875rem; font-weight:600; white-space:nowrap; }
    .bd-blue   { background:#dbeafe; color:#1e40af; }
    .bd-gray   { background:#f3f4f6; color:#6b7280; }
    .bd-green  { background:#dcfce7; color:#166534; }
    .bd-yellow { background:#fef9c3; color:#92400e; }
    .bd-red    { background:#fee2e2; color:#991b1b; }
    .bd-active { background:#dcfce7; color:#166534; }
    .bd-inact  { background:#fee2e2; color:#991b1b; }
    .act-row { display:flex; align-items:center; gap:.375rem; }
    .act-icon { background:none; border:none; cursor:pointer; padding:.25rem; border-radius:.375rem; transition:background .15s; display:inline-flex; text-decoration:none; }
    .act-icon:hover { background:#f3f4f6; }
    .act-icon svg { width:1.125rem; height:1.125rem; }
    .ic-blue   { color:#2563eb; }
    .ic-yellow { color:#d97706; }
    .ic-red    { color:#dc2626; }
    .mob-list { display:none; flex-direction:column; gap:.75rem; }
    .mob-card { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:1rem; }
    .mob-top  { display:flex; align-items:flex-start; justify-content:space-between; gap:.5rem; margin-bottom:.625rem; }
    .mob-id   { font-size:.75rem; color:#9ca3af; font-family:monospace; font-weight:600; }
    .mob-q    { font-size:.875rem; font-weight:600; color:#111827; line-height:1.4; margin:.25rem 0 .375rem; }
    .mob-opts { font-size:.6875rem; color:#9ca3af; }
    .mob-badges { display:flex; flex-wrap:wrap; gap:.375rem; margin-bottom:.625rem; }
    .mob-foot { display:flex; align-items:center; justify-content:space-between; padding-top:.625rem; border-top:1px solid #f3f4f6; }
    .empty-state { text-align:center; padding:3rem 1rem; }
    .pager { padding:1rem; border-top:1px solid #e5e7eb; }
    @media (min-width: 480px) { .filter-grid { grid-template-columns:1fr 1fr; } }
    @media (min-width: 768px) {
        .page-title  { font-size:1.875rem; }
        .filter-grid { grid-template-columns:1fr 1fr 1fr 1fr auto; align-items:end; }
        .filter-card { padding:1.5rem; }
    }
    @media (max-width: 639px) {
        .tbl-wrap { display:none; }
        .mob-list { display:flex; }
    }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">Questions Bank</h1>
        <p class="page-sub">Manage all examination questions</p>
    </div>
    <a href="{{ route('admin.questions.create') }}" class="btn btn-primary">
        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Question
    </a>
</div>

<div class="filter-card">
    <form method="GET" action="{{ route('admin.questions.index') }}">
        <div class="filter-grid">
            <div><input type="text" name="search" value="{{ request('search') }}" placeholder="Search questions..." class="form-ctrl"></div>
            <div>
                <select name="exam_category_id" class="form-ctrl">
                    <option value="">All Categories</option>
                    @foreach($examCategories as $category)
                        <option value="{{ $category->id }}" {{ request('exam_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="subject_id" class="form-ctrl">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="difficulty_id" class="form-ctrl">
                    <option value="">All Difficulties</option>
                    @foreach($difficulties as $difficulty)
                        <option value="{{ $difficulty->id }}" {{ request('difficulty_id') == $difficulty->id ? 'selected' : '' }}>{{ $difficulty->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="btn-row-filter">
                <button type="submit" class="btn btn-dark" style="flex:1;">Filter</button>
                <a href="{{ route('admin.questions.index') }}" class="btn btn-gray">Reset</a>
            </div>
        </div>
    </form>
</div>

@if($questions->count() > 0)

<div class="card">
    <div class="tbl-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>Question</th><th>Category</th><th>Subject</th>
                    <th>Difficulty</th><th>Marks</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($questions as $question)
                <tr>
                    <td style="font-weight:600;font-family:monospace;">#{{ $question->id }}</td>
                    <td>
                        <div style="max-width:18rem;">
                            <p style="font-weight:500;color:#111827;margin:0;">{{ Str::limit(strip_tags($question->question_text), 70) }}</p>
                            <p style="font-size:.6875rem;color:#9ca3af;margin:.25rem 0 0;">{{ $question->options->count() }} options</p>
                        </div>
                    </td>
                    <td>
                        @if($question->examCategory)
                            <span class="badge bd-blue">{{ $question->examCategory->name }}</span>
                        @else
                            <span class="badge bd-gray">No Category</span>
                        @endif
                    </td>
                    <td>{{ $question->subject->name ?? 'N/A' }}</td>
                    <td>
                        @if($question->difficulty)
                            @php $lvl = $question->difficulty->level ?? 99; @endphp
                            <span class="badge {{ $lvl == 1 ? 'bd-green' : ($lvl == 2 ? 'bd-yellow' : 'bd-red') }}">{{ $question->difficulty->name }}</span>
                        @else
                            <span class="badge bd-gray">N/A</span>
                        @endif
                    </td>
                    <td>
                        <span style="color:#16a34a;font-weight:700;">+{{ $question->marks }}</span>
                        @if($question->negative_marks > 0)
                            <span style="color:#dc2626;font-weight:700;"> -{{ $question->negative_marks }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $question->is_active ? 'bd-active' : 'bd-inact' }}">
                            {{ $question->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <div class="act-row">
                            <a href="{{ route('admin.questions.show', $question) }}" class="act-icon ic-blue" title="View">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('admin.questions.edit', $question) }}" class="act-icon ic-yellow" title="Edit">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('admin.questions.destroy', $question) }}" method="POST" onsubmit="return confirm('Delete this question?')" style="margin:0;display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="act-icon ic-red" title="Delete">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pager">{{ $questions->links() }}</div>
</div>

<div class="mob-list">
    @foreach($questions as $question)
    <div class="mob-card">
        <div class="mob-top">
            <div style="min-width:0;flex:1;">
                <div class="mob-id">#{{ $question->id }}</div>
                <p class="mob-q">{{ Str::limit(strip_tags($question->question_text), 90) }}</p>
                <p class="mob-opts">{{ $question->options->count() }} options</p>
            </div>
            <span class="badge {{ $question->is_active ? 'bd-active' : 'bd-inact' }}" style="flex-shrink:0;">
                {{ $question->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
        <div class="mob-badges">
            @if($question->examCategory)<span class="badge bd-blue">{{ $question->examCategory->name }}</span>@endif
            @if($question->subject)<span class="badge bd-gray">{{ $question->subject->name }}</span>@endif
            @if($question->difficulty)
                @php $lvl = $question->difficulty->level ?? 99; @endphp
                <span class="badge {{ $lvl==1?'bd-green':($lvl==2?'bd-yellow':'bd-red') }}">{{ $question->difficulty->name }}</span>
            @endif
        </div>
        <div class="mob-foot">
            <div>
                <span style="color:#16a34a;font-weight:700;font-size:.875rem;">+{{ $question->marks }}</span>
                @if($question->negative_marks > 0)<span style="color:#dc2626;font-weight:700;font-size:.875rem;"> -{{ $question->negative_marks }}</span>@endif
            </div>
            <div class="act-row">
                <a href="{{ route('admin.questions.show', $question) }}" class="act-icon ic-blue" title="View">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </a>
                <a href="{{ route('admin.questions.edit', $question) }}" class="act-icon ic-yellow" title="Edit">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </a>
                <form action="{{ route('admin.questions.destroy', $question) }}" method="POST" onsubmit="return confirm('Delete?')" style="margin:0;display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="act-icon ic-red">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
    <div style="background:#fff;border-radius:.75rem;padding:1rem;box-shadow:0 1px 4px rgba(0,0,0,.08);">{{ $questions->links() }}</div>
</div>

@else
<div class="card">
    <div class="empty-state">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:4rem;height:4rem;color:#d1d5db;margin:0 auto 1rem;display:block;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <h3 style="font-size:1.125rem;font-weight:600;color:#111827;margin:0 0 .5rem;">No Questions Found</h3>
        <p style="color:#9ca3af;margin:0 0 1.25rem;">Get started by creating a new question.</p>
        <a href="{{ route('admin.questions.create') }}" class="btn btn-primary">
            <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create First Question
        </a>
    </div>
</div>
@endif

@endsection