@extends('layouts.admin')
@section('title', 'User Details')

@section('content')
<style>
    .page-header { display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; flex-wrap:wrap; margin-bottom:1.25rem; }
    .page-title  { font-size:1.25rem; font-weight:700; color:#111827; margin:0; }
    .page-sub    { font-size:.8125rem; color:#9ca3af; margin:.125rem 0 0; }
    .hdr-btns    { display:flex; gap:.5rem; flex-wrap:wrap; flex-shrink:0; }
    .btn { display:inline-flex; align-items:center; gap:.375rem; padding:.5rem 1rem; border-radius:.5rem; font-size:.8125rem; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:background .15s; white-space:nowrap; }
    .btn-yellow { background:#f59e0b; color:#fff; }
    .btn-yellow:hover { background:#d97706; }
    .btn-back   { background:#f3f4f6; color:#374151; }
    .btn-back:hover { background:#e5e7eb; }

    /* 2-col layout: stacks on mobile, side-by-side on lg */
    .show-grid { display:grid; grid-template-columns:1fr; gap:1rem; }

    .card { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:1.25rem; }
    .sec-title { display:flex; align-items:center; gap:.5rem; font-size:.75rem; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.06em; margin:0 0 1rem; }
    .sec-title svg { width:1rem; height:1rem; flex-shrink:0; }

    /* Profile card */
    .profile-center { text-align:center; }
    .avatar-lg { width:4.5rem; height:4.5rem; border-radius:50%; background:linear-gradient(135deg,#3b82f6,#1d4ed8); display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.75rem; font-weight:700; margin:0 auto 1rem; box-shadow:0 4px 12px rgba(37,99,235,.3); }
    .profile-name  { font-size:1.125rem; font-weight:700; color:#111827; margin:0 0 .25rem; }
    .profile-email { font-size:.875rem; color:#9ca3af; margin:0 0 .875rem; }
    .badge-row { display:flex; align-items:center; justify-content:center; gap:.5rem; flex-wrap:wrap; margin-bottom:.875rem; }
    .profile-stats { display:grid; grid-template-columns:1fr 1fr; gap:.5rem; border-top:1px solid #f3f4f6; padding-top:.875rem; }
    .pstat { background:#f9fafb; border-radius:.5rem; padding:.625rem; text-align:center; }
    .pstat-label { font-size:.6875rem; color:#9ca3af; margin-bottom:.125rem; }
    .pstat-val   { font-size:.8125rem; font-weight:600; color:#111827; }

    /* Info grid */
    .info-grid { display:grid; grid-template-columns:1fr; gap:.5rem; }
    .info-item { background:#f9fafb; border-radius:.5rem; padding:.625rem .875rem; }
    .info-lbl  { font-size:.6875rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:.04em; margin-bottom:.25rem; }
    .info-val  { font-size:.875rem; font-weight:600; color:#111827; }

    /* Badges */
    .badge { display:inline-block; padding:.25rem .625rem; border-radius:9999px; font-size:.6875rem; font-weight:600; white-space:nowrap; }
    .badge-admin   { background:#fee2e2; color:#991b1b; }
    .badge-teacher { background:#dbeafe; color:#1e40af; }
    .badge-student { background:#dcfce7; color:#166534; }
    .badge-parent  { background:#f3e8ff; color:#7e22ce; }
    .badge-gray    { background:#f3f4f6; color:#6b7280; }
    .badge-active  { display:inline-flex; align-items:center; gap:.375rem; padding:.25rem .625rem; border-radius:9999px; font-size:.6875rem; font-weight:600; background:#dcfce7; color:#166534; white-space:nowrap; }
    .badge-inact   { display:inline-flex; align-items:center; gap:.375rem; padding:.25rem .625rem; border-radius:9999px; font-size:.6875rem; font-weight:600; background:#fee2e2; color:#991b1b; white-space:nowrap; }
    .dot { width:.375rem; height:.375rem; border-radius:50%; }
    .dot-g { background:#16a34a; }
    .dot-r { background:#dc2626; }

    /* Danger zone */
    .danger-card { background:#fff; border-radius:.75rem; box-shadow:0 1px 4px rgba(0,0,0,.08); border:1px solid #fee2e2; padding:1.125rem; }
    .danger-label { font-size:.6875rem; font-weight:700; color:#dc2626; text-transform:uppercase; letter-spacing:.06em; margin:0 0 .75rem; }
    .btn-danger   { width:100%; display:inline-flex; align-items:center; justify-content:center; gap:.5rem; background:#fef2f2; border:1px solid #fecaca; color:#dc2626; border-radius:.5rem; padding:.625rem; font-size:.875rem; font-weight:600; cursor:pointer; transition:background .15s; }
    .btn-danger:hover { background:#fee2e2; }

    @media (min-width: 640px) {
        .info-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (min-width: 900px) {
        .show-grid { grid-template-columns: 18rem 1fr; }
        .page-title { font-size:1.5rem; }
    }
</style>

<div style="max-width:56rem;">
    <div class="page-header">
        <div>
            <h1 class="page-title">User Details</h1>
            <p class="page-sub">Viewing profile of <strong style="color:#374151;">{{ $user->name }}</strong></p>
        </div>
        <div class="hdr-btns">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-yellow">
                <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-back">
                <svg style="width:.875rem;height:.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
        </div>
    </div>

    <div class="show-grid">

        {{-- Left column --}}
        <div style="display:flex;flex-direction:column;gap:1rem;">

            {{-- Profile card --}}
            <div class="card profile-center">
                <div class="avatar-lg">{{ strtoupper(substr($user->name,0,1)) }}</div>
                <h2 class="profile-name">{{ $user->name }}</h2>
                <p class="profile-email">{{ $user->email }}</p>

                @php
                    $rn = $user->role?->name ?? '';
                    $roleClass = match($rn){ 'admin'=>'badge-admin','teacher'=>'badge-teacher','student'=>'badge-student','parent'=>'badge-parent', default=>'badge-gray' };
                @endphp
                <div class="badge-row">
                    <span class="badge {{ $roleClass }}">{{ ucfirst($user->role?->display_name ?? $user->role?->name ?? 'N/A') }}</span>
                    @if($user->is_active)
                        <span class="badge-active"><span class="dot dot-g"></span> Active</span>
                    @else
                        <span class="badge-inact"><span class="dot dot-r"></span> Inactive</span>
                    @endif
                </div>

                <div class="profile-stats">
                    <div class="pstat">
                        <div class="pstat-label">Joined</div>
                        <div class="pstat-val">{{ $user->created_at->format('d M Y') }}</div>
                    </div>
                    <div class="pstat">
                        <div class="pstat-label">Verified</div>
                        <div class="pstat-val" style="{{ $user->email_verified_at ? 'color:#16a34a;' : 'color:#dc2626;' }}">
                            {{ $user->email_verified_at ? 'Yes' : 'No' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Danger zone --}}
            @if($user->id !== auth()->id())
            <div class="danger-card">
                <p class="danger-label">Danger Zone</p>
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                      onsubmit="return confirm('Permanently delete {{ $user->name }}?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger">
                        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete This User
                    </button>
                </form>
            </div>
            @endif
        </div>

        {{-- Right column --}}
        <div style="display:flex;flex-direction:column;gap:1rem;">

            {{-- Basic Info --}}
            <div class="card">
                <h3 class="sec-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#3b82f6;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Basic Information
                </h3>
                <div class="info-grid">
                    @foreach([
                        ['Full Name',      $user->name],
                        ['Email',          $user->email],
                        ['Mobile',         $user->mobile ?? '—'],
                        ['Role',           ucfirst($user->role?->display_name ?? 'N/A')],
                        ['Email Verified', $user->email_verified_at ? '✓ '.$user->email_verified_at->format('d M Y') : 'Not verified'],
                        ['Last Updated',   $user->updated_at->format('d M Y, h:i A')],
                    ] as [$lbl,$val])
                    <div class="info-item">
                        <div class="info-lbl">{{ $lbl }}</div>
                        <div class="info-val">{{ $val }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Student Profile --}}
            @if($user->role?->name === 'student' && $user->student)
            <div class="card">
                <h3 class="sec-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#16a34a;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0121 12c0 3.866-4.03 7-9 7s-9-3.134-9-7a12.083 12.083 0 012.84-1.422L12 14z"/></svg>
                    Student Profile
                </h3>
                <div class="info-grid">
                    @foreach([
                        ['Roll Number',  $user->student->roll_number ?? '—'],
                        ['Gender',       ucfirst($user->student->gender ?? '—')],
                        ['Date of Birth',$user->student->date_of_birth ? \Carbon\Carbon::parse($user->student->date_of_birth)->format('d M Y') : '—'],
                        ['Address',      $user->student->address ?? '—'],
                    ] as [$lbl,$val])
                    <div class="info-item" style="background:#f0fdf4;">
                        <div class="info-lbl">{{ $lbl }}</div>
                        <div class="info-val">{{ $val }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Teacher Profile --}}
            @if($user->role?->name === 'teacher' && $user->teacher)
            <div class="card">
                <h3 class="sec-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#2563eb;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    Teacher Profile
                </h3>
                <div class="info-grid">
                    @foreach([
                        ['Qualification',  $user->teacher->qualification ?? '—'],
                        ['Specialization', $user->teacher->specialization ?? '—'],
                        ['Experience',     $user->teacher->experience_years ? $user->teacher->experience_years.' years' : '—'],
                        ['Bio',            $user->teacher->bio ?? '—'],
                    ] as [$lbl,$val])
                    <div class="info-item" style="background:#eff6ff;">
                        <div class="info-lbl">{{ $lbl }}</div>
                        <div class="info-val">{{ $val }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection