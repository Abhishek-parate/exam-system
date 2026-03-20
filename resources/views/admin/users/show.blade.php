@extends('layouts.admin')
@section('title', 'User Details')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">User Details</h1>
            <p class="text-gray-500 text-sm mt-1">Viewing profile of <span class="font-semibold text-gray-700">{{ $user->name }}</span></p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.edit', $user) }}"
               class="flex items-center gap-2 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            <a href="{{ route('admin.users.index') }}"
               class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left Card --}}
        <div class="lg:col-span-1 space-y-5">

            {{-- Profile Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4 shadow-lg">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h2 class="text-lg font-bold text-gray-900">{{ $user->name }}</h2>
                <p class="text-gray-500 text-sm mt-0.5">{{ $user->email }}</p>

                @php
                    $roleColors = [
                        'admin'   => 'bg-red-100 text-red-700',
                        'teacher' => 'bg-blue-100 text-blue-700',
                        'student' => 'bg-green-100 text-green-700',
                        'parent'  => 'bg-purple-100 text-purple-700',
                    ];
                    $rc = $roleColors[$user->role?->name] ?? 'bg-gray-100 text-gray-700';
                @endphp

                <div class="flex items-center justify-center gap-2 mt-4">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $rc }}">
                        {{ ucfirst($user->role?->display_name ?? $user->role?->name ?? 'N/A') }}
                    </span>
                    @if($user->is_active)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Inactive
                        </span>
                    @endif
                </div>

                <div class="mt-5 pt-5 border-t border-gray-100 grid grid-cols-2 gap-3 text-center">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500 mb-0.5">Joined</p>
                        <p class="text-xs font-semibold text-gray-800">{{ $user->created_at->format('d M Y') }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500 mb-0.5">Verified</p>
                        <p class="text-xs font-semibold {{ $user->email_verified_at ? 'text-green-600' : 'text-red-500' }}">
                            {{ $user->email_verified_at ? 'Yes' : 'No' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Danger Zone --}}
            @if($user->id !== auth()->id())
            <div class="bg-white rounded-xl shadow-sm border border-red-100 p-5">
                <p class="text-xs font-semibold text-red-600 uppercase mb-3">Danger Zone</p>
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                      onsubmit="return confirm('Permanently delete {{ $user->name }}?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 px-4 py-2.5 rounded-lg text-sm font-semibold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete This User
                    </button>
                </form>
            </div>
            @endif

        </div>

        {{-- Right Details --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Basic Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2 uppercase tracking-wide">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Basic Information
                </h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach([
                        ['Full Name',   $user->name],
                        ['Email',       $user->email],
                        ['Mobile',      $user->mobile ?? '—'],
                        ['Role',        ucfirst($user->role?->display_name ?? 'N/A')],
                        ['Email Verified', $user->email_verified_at ? '✓ '.$user->email_verified_at->format('d M Y') : 'Not verified'],
                        ['Last Updated', $user->updated_at->format('d M Y, h:i A')],
                    ] as [$label, $value])
                    <div class="bg-gray-50 rounded-lg px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-400 uppercase mb-1">{{ $label }}</dt>
                        <dd class="text-sm font-semibold text-gray-800">{{ $value }}</dd>
                    </div>
                    @endforeach
                </dl>
            </div>

            {{-- Student Profile --}}
            @if($user->role?->name === 'student' && $user->student)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2 uppercase tracking-wide">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422A12.083 12.083 0 0121 12c0 3.866-4.03 7-9 7s-9-3.134-9-7a12.083 12.083 0 012.84-1.422L12 14z"/>
                    </svg>
                    Student Profile
                </h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach([
                        ['Roll Number', $user->student->roll_number ?? '—'],
                        ['Gender',      ucfirst($user->student->gender ?? '—')],
                        ['Date of Birth', $user->student->date_of_birth ? \Carbon\Carbon::parse($user->student->date_of_birth)->format('d M Y') : '—'],
                        ['Address',     $user->student->address ?? '—'],
                    ] as [$label, $value])
                    <div class="bg-green-50 rounded-lg px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-400 uppercase mb-1">{{ $label }}</dt>
                        <dd class="text-sm font-semibold text-gray-800">{{ $value }}</dd>
                    </div>
                    @endforeach
                </dl>
            </div>
            @endif

            {{-- Teacher Profile --}}
            @if($user->role?->name === 'teacher' && $user->teacher)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2 uppercase tracking-wide">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    Teacher Profile
                </h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach([
                        ['Qualification',  $user->teacher->qualification ?? '—'],
                        ['Specialization', $user->teacher->specialization ?? '—'],
                        ['Experience',     $user->teacher->experience_years ? $user->teacher->experience_years.' years' : '—'],
                        ['Bio',            $user->teacher->bio ?? '—'],
                    ] as [$label, $value])
                    <div class="bg-blue-50 rounded-lg px-4 py-3">
                        <dt class="text-xs font-semibold text-gray-400 uppercase mb-1">{{ $label }}</dt>
                        <dd class="text-sm font-semibold text-gray-800">{{ $value }}</dd>
                    </div>
                    @endforeach
                </dl>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
