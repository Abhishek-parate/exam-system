@extends('layouts.admin')
@section('title', 'User Management')

@section('content')
<style>
    .page-header { display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; flex-wrap:wrap; margin-bottom:1.25rem; }
    .page-title  { font-size:1.375rem; font-weight:700; color:#111827; margin:0; }
    .page-sub    { font-size:.8125rem; color:#9ca3af; margin:.25rem 0 0; }

    .btn { display:inline-flex; align-items:center; gap:.375rem; padding:.5rem 1.125rem; border-radius:.5rem; font-size:.875rem; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:background .15s; white-space:nowrap; }
    .btn-primary { background:#2563eb; color:#fff; }
    .btn-primary:hover { background:#1d4ed8; }

    /* Flash */
    .flash { padding:.75rem 1rem; border-radius:.625rem; font-size:.875rem; font-weight:500; margin-bottom:1rem; display:flex; align-items:flex-start; gap:.625rem; }
    .flash-s { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
    .flash-e { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }
    .flash svg { flex-shrink:0; margin-top:.1rem; }

    /* Filter card */
    .filter-card { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:1.125rem; margin-bottom:1.25rem; }
    .filter-wrap { display:flex; flex-wrap:wrap; gap:.75rem; align-items:flex-end; }
    .filter-search { flex:1; min-width:180px; }
    .filter-sel    { min-width:140px; }
    .filter-btns   { display:flex; gap:.5rem; flex-shrink:0; }
    .f-label { display:block; font-size:.6875rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; margin-bottom:.375rem; }
    .f-ctrl  { width:100%; padding:.5rem .75rem; border:1px solid #d1d5db; border-radius:.5rem; font-size:.875rem; color:#374151; background:#fff; outline:none; box-sizing:border-box; }
    .f-ctrl:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.1); }
    .f-search-wrap { position:relative; }
    .f-search-icon { position:absolute; left:.625rem; top:50%; transform:translateY(-50%); color:#9ca3af; pointer-events:none; }

    /* Table */
    .card { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); overflow:hidden; }
    .tbl-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    table { width:100%; border-collapse:collapse; min-width:600px; }
    thead { background:#f9fafb; }
    th    { padding:.625rem 1.125rem; text-align:left; font-size:.6875rem; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; border-bottom:1px solid #e5e7eb; white-space:nowrap; }
    th.right { text-align:right; }
    td    { padding:.875rem 1.125rem; font-size:.875rem; color:#374151; border-bottom:1px solid #f3f4f6; vertical-align:middle; }
    tr:last-child td { border-bottom:none; }
    tr:hover td { background:#f9fafb; }

    .user-cell { display:flex; align-items:center; gap:.75rem; }
    .user-avatar { width:2.25rem; height:2.25rem; border-radius:50%; background:linear-gradient(135deg,#3b82f6,#1d4ed8); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:.875rem; flex-shrink:0; }
    .user-name { font-weight:600; color:#111827; }
    .user-id   { font-size:.75rem; color:#9ca3af; }

    .badge { display:inline-block; padding:.25rem .625rem; border-radius:9999px; font-size:.6875rem; font-weight:600; white-space:nowrap; }
    .badge-admin   { background:#fee2e2; color:#991b1b; }
    .badge-teacher { background:#dbeafe; color:#1e40af; }
    .badge-student { background:#dcfce7; color:#166534; }
    .badge-parent  { background:#f3e8ff; color:#7e22ce; }
    .badge-gray    { background:#f3f4f6; color:#6b7280; }
    .badge-active  { display:inline-flex; align-items:center; gap:.375rem; padding:.25rem .625rem; border-radius:9999px; font-size:.6875rem; font-weight:600; background:#dcfce7; color:#166534; white-space:nowrap; }
    .badge-inact   { display:inline-flex; align-items:center; gap:.375rem; padding:.25rem .625rem; border-radius:9999px; font-size:.6875rem; font-weight:600; background:#fee2e2; color:#991b1b; white-space:nowrap; }
    .dot { width:.375rem; height:.375rem; border-radius:50%; flex-shrink:0; }
    .dot-green { background:#16a34a; }
    .dot-red   { background:#dc2626; }

    .act-row { display:flex; align-items:center; justify-content:flex-end; gap:.25rem; }
    .act-icon { background:none; border:none; cursor:pointer; padding:.375rem; border-radius:.375rem; transition:background .15s; display:inline-flex; text-decoration:none; }
    .act-icon svg { width:1rem; height:1rem; }
    .act-icon:hover { background:#f3f4f6; }
    .ic-blue   { color:#2563eb; }
    .ic-yellow { color:#d97706; }
    .ic-red    { color:#dc2626; }

    /* Mobile cards */
    .mob-list { display:none; flex-direction:column; gap:.75rem; }
    .mob-card { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:1rem; }
    .mob-top  { display:flex; align-items:center; justify-content:space-between; gap:.5rem; margin-bottom:.75rem; }
    .mob-user { display:flex; align-items:center; gap:.625rem; }
    .mob-meta { display:grid; grid-template-columns:1fr 1fr; gap:.375rem .75rem; margin-bottom:.75rem; font-size:.8125rem; }
    .mob-lbl  { font-size:.6875rem; color:#9ca3af; margin-bottom:.125rem; }
    .mob-foot { display:flex; gap:.375rem; padding-top:.625rem; border-top:1px solid #f3f4f6; justify-content:flex-end; }

    .pager { padding:1rem; border-top:1px solid #e5e7eb; }

    @media (max-width: 639px) {
        .tbl-wrap { display:none; }
        .mob-list { display:flex; }
        .filter-search { flex:100%; }
    }
    @media (min-width: 768px) {
        .page-title { font-size:1.875rem; }
        .filter-card { padding:1.25rem; }
    }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">User Management</h1>
        <p class="page-sub">Manage all system users and their roles</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add New User
    </a>
</div>

@if(session('success'))
    <div class="flash flash-s">
        <svg style="width:1.125rem;height:1.125rem;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="flash flash-e">
        <svg style="width:1.125rem;height:1.125rem;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
        {{ session('error') }}
    </div>
@endif

{{-- Filters --}}
<div class="filter-card">
    <form method="GET" action="{{ route('admin.users.index') }}">
        <div class="filter-wrap">
            <div class="filter-search">
                <label class="f-label">Search</label>
                <div class="f-search-wrap">
                    <svg class="f-search-icon" style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email..."
                           class="f-ctrl" style="padding-left:2rem;">
                </div>
            </div>
            <div class="filter-sel">
                <label class="f-label">Role</label>
                <select name="role" class="f-ctrl">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->display_name ?? $role->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-sel">
                <label class="f-label">Status</label>
                <select name="status" class="f-ctrl">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="filter-btns">
                <button type="submit" class="btn btn-primary" style="padding:.5rem 1rem;">
                    <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                    Filter
                </button>
                <a href="{{ route('admin.users.index') }}" style="display:inline-flex;align-items:center;gap:.375rem;padding:.5rem 1rem;background:#f3f4f6;color:#374151;border-radius:.5rem;font-size:.875rem;font-weight:600;text-decoration:none;transition:background .15s;" onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                    <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Clear
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Desktop Table --}}
<div class="card">
    <div class="tbl-wrap">
        <table>
            <thead>
                <tr>
                    <th>User</th><th>Contact</th><th>Role</th>
                    <th>Status</th><th>Joined</th><th class="right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar">{{ strtoupper(substr($user->name,0,1)) }}</div>
                            <div>
                                <div class="user-name">{{ $user->name }}</div>
                                <div class="user-id">#{{ $user->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="color:#374151;">{{ $user->email }}</div>
                        <div style="font-size:.75rem;color:#9ca3af;margin-top:.125rem;">{{ $user->mobile ?? 'No mobile' }}</div>
                    </td>
                    <td>
                        @php $rn = $user->role?->name ?? ''; @endphp
                        <span class="badge {{ $rn==='admin'?'badge-admin':($rn==='teacher'?'badge-teacher':($rn==='student'?'badge-student':($rn==='parent'?'badge-parent':'badge-gray'))) }}">
                            {{ ucfirst($user->role?->display_name ?? $user->role?->name ?? 'N/A') }}
                        </span>
                    </td>
                    <td>
                        @if($user->is_active)
                            <span class="badge-active"><span class="dot dot-green"></span> Active</span>
                        @else
                            <span class="badge-inact"><span class="dot dot-red"></span> Inactive</span>
                        @endif
                    </td>
                    <td style="color:#6b7280;white-space:nowrap;">{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="act-row">
                            <a href="{{ route('admin.users.show', $user) }}" class="act-icon ic-blue" title="View">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <a href="{{ route('admin.users.edit', $user) }}" class="act-icon ic-yellow" title="Edit">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete {{ $user->name }}?')" style="margin:0;display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="act-icon ic-red" title="Delete">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:3rem 1rem;color:#9ca3af;">
                        <svg style="width:3.5rem;height:3.5rem;margin:0 auto .75rem;display:block;color:#d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <p style="font-weight:600;font-size:1rem;margin-bottom:.25rem;">No users found</p>
                        <p style="font-size:.875rem;">Try adjusting filters or <a href="{{ route('admin.users.create') }}" style="color:#2563eb;font-weight:600;text-decoration:none;">create a new user</a></p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="pager">{{ $users->links() }}</div>
    @endif
</div>

{{-- Mobile Cards --}}
<div class="mob-list">
    @forelse($users as $user)
    <div class="mob-card">
        <div class="mob-top">
            <div class="mob-user">
                <div class="user-avatar" style="width:2rem;height:2rem;font-size:.75rem;">{{ strtoupper(substr($user->name,0,1)) }}</div>
                <div>
                    <div class="user-name" style="font-size:.9375rem;">{{ $user->name }}</div>
                    <div class="user-id">#{{ $user->id }}</div>
                </div>
            </div>
            @if($user->is_active)
                <span class="badge-active"><span class="dot dot-green"></span> Active</span>
            @else
                <span class="badge-inact"><span class="dot dot-red"></span> Inactive</span>
            @endif
        </div>
        <div class="mob-meta">
            <div>
                <div class="mob-lbl">Email</div>
                <div style="font-size:.8125rem;color:#374151;word-break:break-word;">{{ $user->email }}</div>
            </div>
            <div>
                <div class="mob-lbl">Mobile</div>
                <div style="font-size:.8125rem;color:#374151;">{{ $user->mobile ?? '—' }}</div>
            </div>
            <div>
                <div class="mob-lbl">Role</div>
                @php $rn = $user->role?->name ?? ''; @endphp
                <span class="badge {{ $rn==='admin'?'badge-admin':($rn==='teacher'?'badge-teacher':($rn==='student'?'badge-student':($rn==='parent'?'badge-parent':'badge-gray'))) }}">
                    {{ ucfirst($user->role?->display_name ?? $user->role?->name ?? 'N/A') }}
                </span>
            </div>
            <div>
                <div class="mob-lbl">Joined</div>
                <div style="font-size:.8125rem;color:#374151;">{{ $user->created_at->format('d M Y') }}</div>
            </div>
        </div>
        <div class="mob-foot">
            <a href="{{ route('admin.users.show', $user) }}" class="act-icon ic-blue" title="View"><svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a>
            <a href="{{ route('admin.users.edit', $user) }}" class="act-icon ic-yellow" title="Edit"><svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
            @if($user->id !== auth()->id())
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete {{ $user->name }}?')" style="margin:0;display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="act-icon ic-red"><svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div style="background:#fff;border-radius:.75rem;padding:2.5rem 1rem;text-align:center;color:#9ca3af;box-shadow:0 1px 4px rgba(0,0,0,.08);">
        <p style="font-weight:600;margin-bottom:.25rem;">No users found</p>
        <a href="{{ route('admin.users.create') }}" style="color:#2563eb;font-weight:600;text-decoration:none;">Create a new user</a>
    </div>
    @endforelse
    @if($users->hasPages())
        <div style="background:#fff;border-radius:.75rem;padding:1rem;box-shadow:0 1px 4px rgba(0,0,0,.08);">{{ $users->links() }}</div>
    @endif
</div>

@endsection