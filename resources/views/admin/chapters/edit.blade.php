@extends('layouts.admin')
@section('title', 'Edit Chapter')

@section('content')
<style>
    .page-header { display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; margin-bottom:1.25rem; }
    .page-title  { font-size:1.25rem; font-weight:700; color:#111827; margin:0; }
    .back-link   { display:inline-flex; align-items:center; gap:.25rem; font-size:.875rem; font-weight:500; color:#6b7280; text-decoration:none; background:#f3f4f6; padding:.5rem 1rem; border-radius:.5rem; transition:background .15s; white-space:nowrap; }
    .back-link:hover { background:#e5e7eb; color:#111827; }

    .form-card   { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:1.25rem; }
    .form-group  { margin-bottom:1.125rem; }
    .form-label  { display:block; font-size:.875rem; font-weight:600; color:#374151; margin-bottom:.5rem; }
    .form-ctrl   { width:100%; padding:.625rem .875rem; border:1px solid #d1d5db; border-radius:.5rem; font-size:.875rem; color:#374151; background:#fff; outline:none; box-sizing:border-box; transition:border .15s, box-shadow .15s; }
    .form-ctrl:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.1); }
    .form-error  { font-size:.75rem; color:#dc2626; margin-top:.375rem; }

    .check-row   { display:flex; align-items:center; gap:.625rem; cursor:pointer; }
    .check-row input { width:1.125rem; height:1.125rem; accent-color:#2563eb; cursor:pointer; }

    .btn-row { display:flex; gap:.75rem; flex-wrap:wrap; padding-top:.375rem; }
    .btn     { display:inline-flex; align-items:center; gap:.375rem; padding:.625rem 1.5rem; border-radius:.5rem; font-size:.875rem; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:background .15s; white-space:nowrap; }
    .btn-primary { background:#2563eb; color:#fff; box-shadow:0 1px 3px rgba(37,99,235,.3); }
    .btn-primary:hover { background:#1d4ed8; }
    .btn-gray    { background:#e5e7eb; color:#374151; }
    .btn-gray:hover { background:#d1d5db; }

    @media (min-width: 768px) {
        .page-title { font-size:1.5rem; }
        .form-card  { padding:1.75rem; }
        .form-group { margin-bottom:1.375rem; }
    }
</style>

<div style="max-width:38rem;">
    <div class="page-header">
        <h1 class="page-title">✏️ Edit Chapter</h1>
        <a href="{{ route('admin.chapters.index') }}" class="back-link">← Back</a>
    </div>

    <div class="form-card">
        <form action="{{ route('admin.chapters.update', $chapter) }}" method="POST">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label">Subject <span style="color:#dc2626;">*</span></label>
                <select name="subject_id" required class="form-ctrl">
                    <option value="">Select Subject</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}"
                            {{ old('subject_id', $chapter->subject_id) == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>
                @error('subject_id') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Chapter Name <span style="color:#dc2626;">*</span></label>
                <input type="text" name="name" value="{{ old('name', $chapter->name) }}" required
                       class="form-ctrl">
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">
                    Chapter Number
                    <span style="font-size:.75rem;color:#9ca3af;font-weight:400;">(Optional)</span>
                </label>
                <input type="number" name="chapter_number"
                       value="{{ old('chapter_number', $chapter->chapter_number) }}" min="1"
                       class="form-ctrl" style="max-width:10rem;">
                @error('chapter_number') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label class="check-row">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $chapter->is_active) ? 'checked' : '' }}>
                    <span class="form-label" style="margin:0;">Active</span>
                </label>
            </div>

            <div class="btn-row">
                <button type="submit" class="btn btn-primary">✅ Update Chapter</button>
                <a href="{{ route('admin.chapters.index') }}" class="btn btn-gray">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection