@extends('layouts.admin')
@section('title', 'Edit User')

@section('content')
<style>
    .page-header { display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; margin-bottom:1.25rem; }
    .page-title  { font-size:1.25rem; font-weight:700; color:#111827; margin:0; }
    .page-sub    { font-size:.8125rem; color:#9ca3af; margin:.125rem 0 0; }
    .back-link   { display:inline-flex; align-items:center; gap:.375rem; font-size:.875rem; font-weight:500; color:#6b7280; text-decoration:none; background:#f3f4f6; padding:.5rem 1rem; border-radius:.5rem; transition:background .15s; white-space:nowrap; flex-shrink:0; }
    .back-link:hover { background:#e5e7eb; color:#111827; }

    .sec-card  { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:1.25rem; margin-bottom:1rem; }
    .sec-title { display:flex; align-items:center; gap:.625rem; font-size:.9375rem; font-weight:600; color:#111827; margin:0 0 1.125rem; }
    .sec-icon  { width:2rem; height:2rem; border-radius:.5rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .sec-icon svg { width:1rem; height:1rem; }
    .ic-blue-bg   { background:#dbeafe; color:#2563eb; }
    .ic-yellow-bg { background:#fef9c3; color:#d97706; }

    .form-grid { display:grid; grid-template-columns:1fr; gap:.875rem; }
    .form-group { }
    .form-label { display:block; font-size:.875rem; font-weight:600; color:#374151; margin-bottom:.5rem; }
    .form-ctrl  { width:100%; padding:.625rem .875rem; border:1px solid #d1d5db; border-radius:.5rem; font-size:.875rem; color:#374151; background:#fff; outline:none; box-sizing:border-box; transition:border .15s, box-shadow .15s; }
    .form-ctrl:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.1); }
    .form-ctrl-yellow:focus { border-color:#f59e0b; box-shadow:0 0 0 3px rgba(245,158,11,.1); }
    .form-error { font-size:.75rem; color:#dc2626; margin-top:.375rem; }
    .pass-hint  { font-size:.6875rem; color:#9ca3af; margin:.25rem 0 .875rem .125rem; }

    .sel-wrap { position:relative; }
    .sel-arrow { position:absolute; right:.75rem; top:50%; transform:translateY(-50%); pointer-events:none; color:#9ca3af; }

    .check-row  { display:flex; align-items:center; gap:.625rem; cursor:pointer; margin-top:.75rem; }
    .check-row input { width:1.125rem; height:1.125rem; accent-color:#2563eb; cursor:pointer; }

    .action-bar { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:1rem 1.25rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem; }

    .btn { display:inline-flex; align-items:center; gap:.375rem; padding:.625rem 1.5rem; border-radius:.5rem; font-size:.875rem; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:background .15s; white-space:nowrap; }
    .btn-primary { background:#2563eb; color:#fff; }
    .btn-primary:hover { background:#1d4ed8; }
    .btn-cancel  { background:none; border:none; color:#6b7280; font-size:.875rem; font-weight:500; cursor:pointer; padding:0; text-decoration:none; display:inline-flex; align-items:center; gap:.375rem; transition:color .15s; }
    .btn-cancel:hover { color:#111827; }

    .err-box { background:#fef2f2; border-left:4px solid #ef4444; color:#991b1b; padding:.875rem 1rem; border-radius:.5rem; margin-bottom:1rem; font-size:.875rem; }
    .err-box strong { display:block; margin-bottom:.375rem; }
    .err-box ul { margin:.25rem 0 0 1rem; padding:0; }

    @media (min-width: 600px) {
        .form-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (min-width: 768px) {
        .page-title { font-size:1.5rem; }
        .sec-card   { padding:1.5rem; }
    }
</style>

<div style="max-width:44rem;">
    <div class="page-header">
        <div>
            <h1 class="page-title">Edit User</h1>
            <p class="page-sub">Updating profile of <strong style="color:#374151;">{{ $user->name }}</strong></p>
        </div>
        <a href="{{ route('admin.users.show', $user) }}" class="back-link">
            <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    @if($errors->any())
    <div class="err-box">
        <strong>Please fix the following errors:</strong>
        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf @method('PUT')

        {{-- Basic Info --}}
        <div class="sec-card">
            <h2 class="sec-title">
                <div class="sec-icon ic-blue-bg">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                Basic Information
            </h2>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Full Name <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="name" value="{{ old('name',$user->name) }}" required class="form-ctrl">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address <span style="color:#dc2626;">*</span></label>
                    <input type="email" name="email" value="{{ old('email',$user->email) }}" required class="form-ctrl">
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Mobile</label>
                    <input type="text" name="mobile" value="{{ old('mobile',$user->mobile) }}" placeholder="e.g. 9876543210" class="form-ctrl">
                    @error('mobile') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Role <span style="color:#dc2626;">*</span></label>
                    <div class="sel-wrap">
                        <select name="role_name" required class="form-ctrl" style="padding-right:2.5rem;">
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ old('role_name',$user->role?->name)===$role->name ? 'selected' : '' }}>
                                    {{ ucfirst($role->display_name ?? $role->name) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="sel-arrow"><svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></div>
                    </div>
                    @error('role_name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <label class="check-row">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active',$user->is_active) ? 'checked' : '' }}>
                <span class="form-label" style="margin:0;">Active Account</span>
            </label>
        </div>

        {{-- Change Password --}}
        <div class="sec-card">
            <h2 class="sec-title">
                <div class="sec-icon ic-yellow-bg">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                Change Password
            </h2>
            <p class="pass-hint">Leave blank to keep the current password.</p>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" autocomplete="new-password" placeholder="Min. 8 characters"
                           class="form-ctrl form-ctrl-yellow">
                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password" placeholder="Repeat new password"
                           class="form-ctrl form-ctrl-yellow">
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="action-bar">
            <a href="{{ route('admin.users.show', $user) }}" class="btn-cancel">
                <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Update User
            </button>
        </div>
    </form>
</div>
@endsection