<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student - Exam Portal')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; background: #f9fafb; font-family: ui-sans-serif, system-ui, sans-serif; }

        /* ── NAVBAR ── */
        #s-nav {
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
            position: sticky; top: 0; z-index: 50;
        }
        #s-nav-inner {
            max-width: 80rem; margin: 0 auto;
            padding: 0 1rem;
            display: flex; align-items: center; justify-content: space-between;
            height: 4rem;
        }

        /* Desktop links — hidden on mobile by default */
        #s-desktop-links { display: none; align-items: center; gap: .25rem; }
        #s-desktop-right  { display: none; align-items: center; gap: .75rem; }

        /* Mobile controls */
        #s-mob-right { display: flex; align-items: center; gap: .5rem; }

        /* Mobile dropdown */
        #s-mob-menu {
            max-height: 0; overflow: hidden;
            border-top: 1px solid #f3f4f6;
            transition: max-height .3s cubic-bezier(.4,0,.2,1);
        }
        #s-mob-menu.open { max-height: 400px; }

        /* Nav links */
        .s-link {
            display: flex; align-items: center; gap: .5rem;
            padding: .5rem .75rem; border-radius: .5rem;
            font-size: .875rem; font-weight: 500;
            text-decoration: none; color: #4b5563;
            transition: background .15s;
        }
        .s-link:hover { background: #f3f4f6; }
        .s-link.act { background: #f5f3ff; color: #7c3aed; }

        .s-mob-link {
            display: flex; align-items: center; gap: .75rem;
            padding: .75rem 1rem; border-radius: .5rem;
            font-size: .875rem; font-weight: 500;
            text-decoration: none; color: #374151;
            transition: background .15s;
        }
        .s-mob-link:hover { background: #f9fafb; }
        .s-mob-link.act { background: #f5f3ff; color: #7c3aed; }

        /* Hamburger */
        .shb { display:flex; flex-direction:column; justify-content:center; gap:4px;
               width:2.25rem; height:2.25rem; background:transparent; border:none;
               border-radius:.5rem; cursor:pointer; padding:.5rem; transition:background .2s; }
        .shb:hover { background: #f3f4f6; }
        .shb span { display:block; width:18px; height:2px; background:#374151; border-radius:2px; transition: all .28s ease; }
        .shb.x span:nth-child(1) { transform: translateY(6px) rotate(45deg); }
        .shb.x span:nth-child(2) { opacity:0; transform:scaleX(0); }
        .shb.x span:nth-child(3) { transform: translateY(-6px) rotate(-45deg); }

        /* Flash */
        .flash-wrap { max-width:80rem; margin:1rem auto; padding:0 1rem; }
        .flash { padding:.75rem 1rem; border-radius:.75rem; font-size:.875rem; font-weight:500; display:flex; align-items:center; gap:.5rem; }
        .flash-s { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
        .flash-e { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }
        .flash-i { background:#eff6ff; border:1px solid #bfdbfe; color:#1e40af; }

        /* Footer */
        footer { background:#fff; border-top:1px solid #e5e7eb; padding:1.25rem 1rem; text-align:center; font-size:.875rem; color:#6b7280; }

        /* ── DESKTOP ≥ 768px ── */
        @media (min-width: 768px) {
            #s-nav-inner       { padding: 0 1.5rem; }
            #s-desktop-links   { display: flex; }
            #s-desktop-right   { display: flex; }
            #s-mob-right       { display: none; }
            #s-mob-menu        { display: none !important; }
        }
    </style>
</head>
<body style="display:flex;flex-direction:column;min-height:100vh;">

{{-- ═══ NAVBAR ═══ --}}
<nav id="s-nav">
    <div id="s-nav-inner">

        {{-- Logo --}}
        <a href="/student/dashboard" style="display:flex;align-items:center;gap:.5rem;text-decoration:none;flex-shrink:0;">
            <div style="width:2rem;height:2rem;background:linear-gradient(135deg,#7c3aed,#5b21b6);border-radius:.5rem;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.875rem;box-shadow:0 1px 3px rgba(0,0,0,.2);">E</div>
            <span style="font-size:1.125rem;font-weight:700;color:#7c3aed;">Exam Portal</span>
        </a>

        {{-- Desktop nav links --}}
        <div id="s-desktop-links">
            <a href="/student/dashboard"                    class="s-link {{ request()->is('student/dashboard') ? 'act' : '' }}">Dashboard</a>
            <a href="{{ route('student.exams.index') }}"    class="s-link {{ request()->routeIs('student.exams.*') ? 'act' : '' }}">My Exams</a>
            <a href="/student/results"                      class="s-link {{ request()->is('student/results') ? 'act' : '' }}">Results</a>
            <a href="/student/profile"                      class="s-link {{ request()->is('student/profile') ? 'act' : '' }}">Profile</a>
        </div>

        {{-- Desktop right --}}
        <div id="s-desktop-right">
            <div style="display:flex;align-items:center;gap:.625rem;">
                <div style="width:2.25rem;height:2.25rem;background:#7c3aed;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.875rem;box-shadow:0 1px 3px rgba(0,0,0,.2);flex-shrink:0;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div style="line-height:1.2;">
                    <p style="font-size:.875rem;font-weight:600;color:#374151;margin:0;">{{ auth()->user()->name }}</p>
                    <p style="font-size:.75rem;color:#6b7280;margin:0;">Student</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button style="background:none;border:none;font-size:.875rem;font-weight:600;color:#dc2626;cursor:pointer;padding:.5rem .75rem;border-radius:.5rem;transition:background .15s;"
                        onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='none'">
                    Logout
                </button>
            </form>
        </div>

        {{-- Mobile right (avatar + hamburger) --}}
        <div id="s-mob-right">
            <div style="width:2rem;height:2rem;background:#7c3aed;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.75rem;flex-shrink:0;">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <button id="s-ham" onclick="toggleSMenu()" class="shb">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>

    {{-- Mobile dropdown --}}
    <div id="s-mob-menu">
        <div style="padding:.5rem 1rem .75rem;display:flex;flex-direction:column;gap:.25rem;">
            <a href="/student/dashboard"                 onclick="closeSMenu()" class="s-mob-link {{ request()->is('student/dashboard') ? 'act' : '' }}">🏠 Dashboard</a>
            <a href="{{ route('student.exams.index') }}" onclick="closeSMenu()" class="s-mob-link {{ request()->routeIs('student.exams.*') ? 'act' : '' }}">📋 My Exams</a>
            <a href="/student/results"                   onclick="closeSMenu()" class="s-mob-link {{ request()->is('student/results') ? 'act' : '' }}">📊 Results</a>
            <a href="/student/profile"                   onclick="closeSMenu()" class="s-mob-link {{ request()->is('student/profile') ? 'act' : '' }}">👤 Profile</a>
            <div style="border-top:1px solid #f3f4f6;margin-top:.5rem;padding-top:.5rem;">
                <p style="font-size:.75rem;font-weight:600;color:#6b7280;margin:0 0 .5rem;">{{ auth()->user()->name }}</p>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button style="background:none;border:none;font-size:.875rem;font-weight:600;color:#dc2626;cursor:pointer;padding:0;">Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>

{{-- Flash messages --}}
@if(session('success'))
    <div class="flash-wrap"><div class="flash flash-s">✅ {{ session('success') }}</div></div>
@endif
@if(session('error'))
    <div class="flash-wrap"><div class="flash flash-e">❌ {{ session('error') }}</div></div>
@endif
@if(session('info'))
    <div class="flash-wrap"><div class="flash flash-i">ℹ️ {{ session('info') }}</div></div>
@endif

<main style="flex:1;">
    @yield('content')
</main>

<footer>&copy; {{ date('Y') }} Exam Portal. All rights reserved.</footer>

<script>
    var smOpen = false;
    function toggleSMenu() {
        smOpen = !smOpen;
        document.getElementById('s-mob-menu').classList.toggle('open', smOpen);
        document.getElementById('s-ham').classList.toggle('x', smOpen);
    }
    function closeSMenu() {
        smOpen = false;
        document.getElementById('s-mob-menu').classList.remove('open');
        document.getElementById('s-ham').classList.remove('x');
    }
    window.addEventListener('resize', function() { if(window.innerWidth >= 768) closeSMenu(); });
</script>

@stack('scripts')
</body>
</html>