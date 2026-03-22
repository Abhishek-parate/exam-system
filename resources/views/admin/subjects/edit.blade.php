@extends('layouts.admin')

@section('title', 'Edit Subject')

@section('content')
<style>
    .page-header  { display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; margin-bottom:1.5rem; }
    .page-title   { font-size:1.375rem; font-weight:700; color:#111827; margin:0; }
    .back-link    { font-size:.875rem; font-weight:500; color:#6b7280; text-decoration:none; display:inline-flex; align-items:center; gap:.25rem; white-space:nowrap; }
    .back-link:hover { color:#111827; }

    .form-card    { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:1.25rem; }
    .form-group   { margin-bottom:1.25rem; }
    .form-label   { display:block; font-size:.875rem; font-weight:600; color:#374151; margin-bottom:.5rem; }
    .form-ctrl    { width:100%; padding:.625rem .875rem; border:1px solid #d1d5db; border-radius:.5rem; font-size:.875rem; color:#374151; background:#fff; outline:none; box-sizing:border-box; transition:border .15s, box-shadow .15s; }
    .form-ctrl:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.1); }
    textarea.form-ctrl { resize:vertical; min-height:6rem; }
    .form-hint    { font-size:.75rem; color:#9ca3af; margin-top:.375rem; }
    .form-error   { font-size:.75rem; color:#dc2626; margin-top:.375rem; }

    .check-row    { display:flex; align-items:center; gap:.625rem; cursor:pointer; }
    .check-row input[type=checkbox] { width:1.125rem; height:1.125rem; cursor:pointer; accent-color:#2563eb; }

    .btn-row { display:flex; gap:.75rem; flex-wrap:wrap; padding-top:.5rem; }
    .btn     { display:inline-flex; align-items:center; gap:.375rem; padding:.625rem 1.5rem; border-radius:.5rem; font-size:.875rem; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:background .15s; white-space:nowrap; }
    .btn-primary { background:#2563eb; color:#fff; box-shadow:0 1px 3px rgba(37,99,235,.4); }
    .btn-primary:hover { background:#1d4ed8; }
    .btn-gray    { background:#e5e7eb; color:#374151; }
    .btn-gray:hover { background:#d1d5db; }

    @media (min-width: 768px) {
        .page-title { font-size:1.875rem; }
        .form-card  { padding:2rem; }
        .form-group { margin-bottom:1.5rem; }
    }
</style>

<div style="max-width:42rem;">
    <div class="page-header">
        <h1 class="page-title">📝 Edit Subject</h1>
        <a href="{{ route('admin.subjects.show', $subject) }}" class="back-link">
            ← Back to Subject
        </a>
    </div>

    <form method="POST" action="{{ route('admin.subjects.update', $subject) }}" class="form-card">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Exam Category <span style="color:#9ca3af;font-weight:400;">(Optional)</span></label>
            <select name="exam_category_id" class="form-ctrl">
                <option value="">Select Category</option>
                @foreach($examCategories as $category)
                    <option value="{{ $category->id }}" {{ $subject->exam_category_id == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('exam_category_id') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Subject Name <span style="color:#dc2626;">*</span></label>
            <input type="text" name="name" value="{{ old('name', $subject->name) }}" required
                   placeholder="e.g., Mathematics, Physics, Chemistry"
                   class="form-ctrl">
            @error('name') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Subject Code <span style="color:#9ca3af;font-weight:400;">(Optional)</span></label>
            <input type="text" name="code" value="{{ old('code', $subject->code) }}"
                   placeholder="e.g., MATH101, PHY201"
                   class="form-ctrl">
            @error('code') <p class="form-error">{{ $message }}</p> @enderror
            <p class="form-hint">Unique identifier for this subject</p>
        </div>

        <div class="form-group">
            <label class="form-label">Description <span style="color:#9ca3af;font-weight:400;">(Optional)</span></label>
            <textarea name="description" placeholder="Brief description of the subject..." class="form-ctrl">{{ old('description', $subject->description) }}</textarea>
            @error('description') <p class="form-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label class="check-row">
                <input type="checkbox" name="is_active" value="1" {{ $subject->is_active ? 'checked' : '' }}>
                <span class="form-label" style="margin:0;">Subject is Active</span>
            </label>
        </div>

        <div class="btn-row">
            <button type="submit" class="btn btn-primary">💾 Update Subject</button>
            <a href="{{ route('admin.subjects.show', $subject) }}" class="btn btn-gray">Cancel</a>
        </div>
    </form>
</div>
@endsection