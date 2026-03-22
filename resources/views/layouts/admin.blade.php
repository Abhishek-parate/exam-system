<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Exam Portal</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* ────────────────────────────────────────
           BASE RESETS
        ──────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }

        /* Chevron icon — size locked via CSS, rotation via Alpine :class */
        .chevron {
            width: .875rem !important;
            height: .875rem !important;
            min-width: .875rem;
            min-height: .875rem;
            flex-shrink: 0;
            transition: transform .2s ease;
            display: block;
        }
        .chevron.rotated { transform: rotate(180deg); }

        body { margin: 0; background: #f9fafb; font-family: ui-sans-serif, system-ui, sans-serif; }

        /* ────────────────────────────────────────
           HEADER  (always full-width, fixed top)
        ──────────────────────────────────────── */
        #main-header {
            position: fixed; top: 0; left: 0; right: 0; z-index: 50;
            height: 4rem;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            display: flex; align-items: center;
            padding: 0 1rem;
            gap: .75rem;
        }

        #header-left  { display: flex; align-items: center; gap: .625rem; flex: 1; min-width: 0; }
        #header-right { display: flex; align-items: center; gap: .5rem; flex-shrink: 0; }

        /* ────────────────────────────────────────
           SIDEBAR  (desktop — always visible ≥768)
        ──────────────────────────────────────── */
        #desktop-sidebar {
            display: none; /* hidden on mobile by default */
            position: fixed;
            top: 4rem; left: 0;
            width: 16rem;
            height: calc(100vh - 4rem);
            background: #fff;
            border-right: 1px solid #e5e7eb;
            flex-direction: column;
            z-index: 30;
            overflow-y: auto;
        }

        /* ────────────────────────────────────────
           MOBILE SIDEBAR  (slide-over)
        ──────────────────────────────────────── */
        #mob-backdrop {
            display: none;
            position: fixed; top: 4rem; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,.5);
            z-index: 44;
        }
        #mob-backdrop.open { display: block; }

        #mob-sidebar {
            position: fixed;
            top: 4rem; left: 0;
            width: 17rem;
            height: calc(100vh - 4rem);
            background: #fff;
            border-right: 1px solid #e5e7eb;
            display: flex; flex-direction: column;
            z-index: 45;
            transform: translateX(-100%);
            transition: transform .3s cubic-bezier(.4,0,.2,1);
            box-shadow: 6px 0 24px rgba(0,0,0,.12);
            overflow-y: auto;
        }
        #mob-sidebar.open { transform: translateX(0); }

        /* ────────────────────────────────────────
           MAIN CONTENT
        ──────────────────────────────────────── */
        #main-content {
            padding-top: 4rem;
            min-height: 100vh;
        }
        #main-inner {
            padding: 1rem;
        }

        /* ────────────────────────────────────────
           HAMBURGER
        ──────────────────────────────────────── */
        #ham-btn {
            display: flex; flex-direction: column; justify-content: center;
            width: 2.25rem; height: 2.25rem;
            border: none; background: transparent;
            border-radius: .5rem; cursor: pointer;
            padding: .5rem;
            flex-shrink: 0;
            transition: background .2s;
            gap: 4px;
        }
        #ham-btn:hover { background: #f3f4f6; }
        #ham-btn span  {
            display: block; width: 18px; height: 2px;
            background: #374151; border-radius: 2px;
            transition: all .28s ease;
            transform-origin: center;
        }
        #ham-btn.x span:nth-child(1) { transform: translateY(6px) rotate(45deg); }
        #ham-btn.x span:nth-child(2) { opacity: 0; transform: scaleX(0); }
        #ham-btn.x span:nth-child(3) { transform: translateY(-6px) rotate(-45deg); }

        /* ────────────────────────────────────────
           SIDEBAR NAV LINKS
        ──────────────────────────────────────── */
        .sl {
            display: flex; align-items: center; gap: .75rem;
            padding: .75rem 1rem;
            border-radius: .5rem;
            font-size: .875rem; font-weight: 500;
            text-decoration: none;
            color: #374151;
            transition: background .2s, transform .2s;
            cursor: pointer; border: none; width: 100%; text-align: left;
            background: transparent;
        }
        .sl:hover { background: #f9fafb; transform: translateX(3px); }
        .sl.act   { background: #eff6ff; color: #1d4ed8; box-shadow: inset 4px 0 0 #2563eb; }

        .sl-sub {
            display: flex; align-items: center; gap: .625rem;
            padding: .625rem .75rem;
            border-radius: .5rem;
            font-size: .8125rem; font-weight: 500;
            text-decoration: none; color: #4b5563;
            transition: background .2s;
        }
        .sl-sub:hover { background: #f9fafb; }
        .sl-sub.act   { background: #eff6ff; color: #1d4ed8; }

        /* ────────────────────────────────────────
           LOGO TEXT
        ──────────────────────────────────────── */
        .logo-text {
            font-weight: 700; font-size: 1.125rem;
            background: linear-gradient(to right, #2563eb, #1e40af);
            -webkit-background-clip: text; background-clip: text;
            -webkit-text-fill-color: transparent;
            white-space: nowrap;
        }

        /* ────────────────────────────────────────
           DESKTOP  ≥ 768px
        ──────────────────────────────────────── */
        @media (min-width: 768px) {
            #ham-btn         { display: none !important; }   /* hide hamburger */
            #desktop-sidebar { display: flex !important; }   /* show sidebar */
            #main-content    { padding-left: 16rem; }        /* offset for sidebar */
            #main-inner      { padding: 1.5rem 2rem; }
            #header-right    { gap: .75rem; }
            #user-name-block { display: block !important; }
            .logout-text     { display: inline !important; }
            #main-header     { padding: 0 1.5rem; }
        }

        @media (min-width: 1024px) {
            #main-inner { padding: 2rem 2.5rem; }
        }

        /* ────────────────────────────────────────
           MOBILE  < 768px
        ──────────────────────────────────────── */
        #user-name-block { display: none; }
        .logout-text     { display: none; }

        /* ────────────────────────────────────────
           FLASH MESSAGES
        ──────────────────────────────────────── */
        .flash { padding: .75rem 1rem; border-radius: .75rem; font-size: .875rem; font-weight: 500; margin-bottom: 1rem; }
        .flash-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .flash-error   { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .flash-info    { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }
    </style>
</head>
<body>

{{-- ═══════════════════════════
     HEADER
═══════════════════════════ --}}
<header id="main-header">

    <div id="header-left">
        {{-- Hamburger (mobile only — hidden via CSS on desktop) --}}
        <button id="ham-btn" onclick="toggleSidebar()" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>

        {{-- Logo --}}
        <a href="{{ route('admin.dashboard') }}" style="display:flex;align-items:center;gap:.5rem;text-decoration:none;flex-shrink:0;">
            <div style="width:2.25rem;height:2.25rem;background:linear-gradient(135deg,#2563eb,#1e40af);border-radius:.5rem;display:flex;align-items:center;justify-content:center;box-shadow:0 1px 3px rgba(0,0,0,.2);flex-shrink:0;">
                <svg style="width:1.1rem;height:1.1rem;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422A12.083 12.083 0 0121 12c0 3.866-4.03 7-9 7s-9-3.134-9-7a12.083 12.083 0 012.84-1.422L12 14z"/>
                </svg>
            </div>
            <span class="logo-text">Exam Portal</span>
        </a>
    </div>

    <div id="header-right">
        {{-- User name (hidden on mobile, shown on desktop via CSS) --}}
        <div id="user-name-block" style="text-align:right;line-height:1.2;">
            <p style="font-size:.875rem;font-weight:600;color:#1f2937;margin:0;">{{ auth()->user()->name }}</p>
            <p style="font-size:.75rem;color:#6b7280;margin:0;">Administrator</p>
        </div>

        {{-- Avatar --}}
        <div style="width:2.25rem;height:2.25rem;background:linear-gradient(135deg,#2563eb,#1d4ed8);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.875rem;box-shadow:0 1px 3px rgba(0,0,0,.2);flex-shrink:0;">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
            @csrf
            <button style="display:flex;align-items:center;gap:.375rem;background:#dc2626;color:#fff;border:none;padding:.5rem .75rem;border-radius:.5rem;font-size:.8125rem;font-weight:500;cursor:pointer;transition:background .2s;white-space:nowrap;"
                    onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                <svg style="width:1rem;height:1rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span class="logout-text">Logout</span>
            </button>
        </form>
    </div>
</header>

{{-- ═══════════════════════════
     DESKTOP SIDEBAR
═══════════════════════════ --}}
<aside id="desktop-sidebar">
    @php $ca = request()->routeIs('admin.chapters.*') || request()->routeIs('admin.topics.*'); @endphp

    <nav style="flex:1;padding:1rem;display:flex;flex-direction:column;gap:.25rem;">

        <a href="{{ route('admin.dashboard') }}"
           class="sl {{ request()->routeIs('admin.dashboard') ? 'act' : '' }}">
            <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard
        </a>

        <a href="{{ route('admin.subjects.index') }}"
           class="sl {{ request()->routeIs('admin.subjects.*') ? 'act' : '' }}">
            <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            Subjects
        </a>

        {{-- Content dropdown --}}
        <div x-data="{ open: {{ $ca ? 'true' : 'false' }} }">
            <button @click="open=!open" class="sl {{ $ca ? 'act' : '' }}"
                    style="justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Content
                </div>
                <svg :class="open ? 'chevron rotated' : 'chevron'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-transition style="margin-left:1rem;border-left:2px solid #dbeafe;padding-left:.75rem;display:flex;flex-direction:column;gap:.25rem;margin-top:.25rem;">
                <a href="{{ route('admin.chapters.index') }}" class="sl-sub {{ request()->routeIs('admin.chapters.*') ? 'act' : '' }}">
                    <svg style="width:1rem;height:1rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Chapters
                </a>
                <a href="{{ route('admin.topics.index') }}" class="sl-sub {{ request()->routeIs('admin.topics.*') ? 'act' : '' }}">
                    <svg style="width:1rem;height:1rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                    Topics
                </a>
            </div>
        </div>

        <a href="{{ route('admin.questions.index') }}"
           class="sl {{ request()->routeIs('admin.questions.*') ? 'act' : '' }}">
            <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Questions
        </a>

        <a href="{{ route('admin.exams.index') }}"
           class="sl {{ request()->routeIs('admin.exams.*') ? 'act' : '' }}">
            <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            Exams
        </a>

        <a href="{{ route('admin.users.index') }}"
           class="sl {{ request()->routeIs('admin.users.*') ? 'act' : '' }}">
            <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Users
        </a>
    </nav>

    <div style="padding:1rem;border-top:1px solid #e5e7eb;flex-shrink:0;">
        <div style="display:flex;align-items:center;gap:.75rem;padding:.75rem;background:#eff6ff;border-radius:.75rem;">
            <div style="width:2rem;height:2rem;background:#2563eb;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.75rem;font-weight:700;flex-shrink:0;">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div style="flex:1;min-width:0;">
                <p style="font-size:.75rem;font-weight:600;color:#1f2937;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ auth()->user()->name }}</p>
                <p style="font-size:.75rem;color:#6b7280;margin:0;">Admin</p>
            </div>
        </div>
    </div>
</aside>

{{-- ═══════════════════════════
     MOBILE SIDEBAR
═══════════════════════════ --}}
<div id="mob-backdrop" onclick="closeSidebar()"></div>

<aside id="mob-sidebar">
    @php $ca2 = request()->routeIs('admin.chapters.*') || request()->routeIs('admin.topics.*'); @endphp
    <nav style="flex:1;padding:1rem;display:flex;flex-direction:column;gap:.25rem;overflow-y:auto;">

        <a href="{{ route('admin.dashboard') }}" onclick="closeSidebar()"
           class="sl {{ request()->routeIs('admin.dashboard') ? 'act' : '' }}">
            <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
        <a href="{{ route('admin.subjects.index') }}" onclick="closeSidebar()"
           class="sl {{ request()->routeIs('admin.subjects.*') ? 'act' : '' }}">
            <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            Subjects
        </a>

        <div x-data="{ open: {{ $ca2 ? 'true' : 'false' }} }">
            <button @click="open=!open" class="sl {{ $ca2 ? 'act' : '' }}" style="justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Content
                </div>
                <svg :class="open?'chevron rotated':'chevron'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open" x-transition style="margin-left:1rem;border-left:2px solid #dbeafe;padding-left:.75rem;display:flex;flex-direction:column;gap:.25rem;margin-top:.25rem;">
                <a href="{{ route('admin.chapters.index') }}" onclick="closeSidebar()" class="sl-sub {{ request()->routeIs('admin.chapters.*') ? 'act' : '' }}">
                    <svg style="width:1rem;height:1rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Chapters
                </a>
                <a href="{{ route('admin.topics.index') }}" onclick="closeSidebar()" class="sl-sub {{ request()->routeIs('admin.topics.*') ? 'act' : '' }}">
                    <svg style="width:1rem;height:1rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                    Topics
                </a>
            </div>
        </div>

        <a href="{{ route('admin.questions.index') }}" onclick="closeSidebar()"
           class="sl {{ request()->routeIs('admin.questions.*') ? 'act' : '' }}">
            <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Questions
        </a>
        <a href="{{ route('admin.exams.index') }}" onclick="closeSidebar()"
           class="sl {{ request()->routeIs('admin.exams.*') ? 'act' : '' }}">
            <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            Exams
        </a>
        <a href="{{ route('admin.users.index') }}" onclick="closeSidebar()"
           class="sl {{ request()->routeIs('admin.users.*') ? 'act' : '' }}">
            <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            Users
        </a>
    </nav>

    <div style="padding:1rem;border-top:1px solid #e5e7eb;flex-shrink:0;">
        <div style="display:flex;align-items:center;gap:.75rem;padding:.75rem;background:#eff6ff;border-radius:.75rem;">
            <div style="width:2rem;height:2rem;background:#2563eb;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.75rem;font-weight:700;flex-shrink:0;">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div style="flex:1;min-width:0;">
                <p style="font-size:.75rem;font-weight:600;color:#1f2937;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ auth()->user()->name }}</p>
                <p style="font-size:.75rem;color:#6b7280;margin:0;">Admin</p>
            </div>
        </div>
    </div>
</aside>

{{-- ═══════════════════════════
     MAIN CONTENT
═══════════════════════════ --}}
<main id="main-content">
    <div id="main-inner">
        @if(session('success'))
            <div class="flash flash-success">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash flash-error">❌ {{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div class="flash flash-info">ℹ️ {{ session('info') }}</div>
        @endif

        @yield('content')
    </div>
</main>

<script>
    var sidebarOpen = false;

    function toggleSidebar() {
        sidebarOpen = !sidebarOpen;
        document.getElementById('mob-sidebar').classList.toggle('open', sidebarOpen);
        document.getElementById('mob-backdrop').classList.toggle('open', sidebarOpen);
        document.getElementById('ham-btn').classList.toggle('x', sidebarOpen);
    }
    function closeSidebar() {
        sidebarOpen = false;
        document.getElementById('mob-sidebar').classList.remove('open');
        document.getElementById('mob-backdrop').classList.remove('open');
        document.getElementById('ham-btn').classList.remove('x');
    }
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 768) closeSidebar();
    });
</script>

@stack('scripts')
</body>
</html>