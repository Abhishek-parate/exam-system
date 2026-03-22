<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Parent Portal - Exam System')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; background: #f9fafb; font-family: ui-sans-serif, system-ui, sans-serif; }

        #p-nav { background:#fff; border-bottom:1px solid #e5e7eb; box-shadow:0 1px 4px rgba(0,0,0,.06); position:sticky; top:0; z-index:50; }
        #p-nav-inner { max-width:80rem; margin:0 auto; padding:0 1rem; display:flex; align-items:center; justify-content:space-between; height:4rem; }

        #p-desktop-right { display:none; align-items:center; gap:.75rem; }
        #p-mob-right { display:flex; align-items:center; gap:.5rem; }

        #p-mob-menu { max-height:0; overflow:hidden; border-top:1px solid #f3f4f6; transition:max-height .3s cubic-bezier(.4,0,.2,1); }
        #p-mob-menu.open { max-height:300px; }

        .p-mob-link { display:flex; align-items:center; gap:.75rem; padding:.75rem 1rem; border-radius:.5rem; font-size:.875rem; font-weight:500; text-decoration:none; color:#374151; transition:background .15s; }
        .p-mob-link:hover { background:#f9fafb; }
        .p-mob-link.act { background:#eff6ff; color:#2563eb; }

        .phb { display:flex; flex-direction:column; justify-content:center; gap:4px; width:2.25rem; height:2.25rem; background:transparent; border:none; border-radius:.5rem; cursor:pointer; padding:.5rem; transition:background .2s; }
        .phb:hover { background:#f3f4f6; }
        .phb span { display:block; width:18px; height:2px; background:#374151; border-radius:2px; transition:all .28s ease; }
        .phb.x span:nth-child(1) { transform:translateY(6px) rotate(45deg); }
        .phb.x span:nth-child(2) { opacity:0; transform:scaleX(0); }
        .phb.x span:nth-child(3) { transform:translateY(-6px) rotate(-45deg); }

        .flash-wrap { max-width:80rem; margin:1rem auto; padding:0 1rem; }
        .flash { padding:.75rem 1rem; border-radius:.75rem; font-size:.875rem; font-weight:500; display:flex; align-items:flex-start; gap:.75rem; }
        .flash-s { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
        .flash-e { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }

        footer { background:#fff; border-top:1px solid #e5e7eb; padding:1.25rem 1rem; text-align:center; font-size:.875rem; color:#6b7280; }

        @media (min-width: 768px) {
            #p-nav-inner      { padding: 0 1.5rem; }
            #p-desktop-right  { display: flex; }
            #p-mob-right      { display: none; }
            #p-mob-menu       { display: none !important; }
        }
    </style>
</head>
<body style="display:flex;flex-direction:column;min-height:100vh;">

<nav id="p-nav">
    <div id="p-nav-inner">
        <a href="{{ route('parent.dashboard') }}" style="display:flex;align-items:center;gap:.5rem;text-decoration:none;flex-shrink:0;">
            <div style="width:2.25rem;height:2.25rem;background:linear-gradient(135deg,#2563eb,#1e40af);border-radius:.5rem;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:1rem;box-shadow:0 1px 3px rgba(0,0,0,.2);">E</div>
            <span style="font-size:1.125rem;font-weight:600;color:#111827;">Exam Portal</span>
        </a>

        {{-- Desktop right --}}
        <div id="p-desktop-right">
            <div style="display:flex;align-items:center;gap:.625rem;padding:.5rem .75rem;border-radius:.75rem;cursor:default;">
                <div style="width:2.25rem;height:2.25rem;background:linear-gradient(135deg,#3b82f6,#2563eb);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:600;font-size:.875rem;flex-shrink:0;">
                    {{ strtoupper(substr(auth()->user()->name ?? 'P', 0, 1)) }}
                </div>
                <div style="line-height:1.2;">
                    <p style="font-size:.875rem;font-weight:600;color:#111827;margin:0;max-width:8rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ auth()->user()->name }}</p>
                    <p style="font-size:.75rem;color:#6b7280;font-weight:500;margin:0;">Parent</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button style="display:flex;align-items:center;gap:.375rem;padding:.5rem 1rem;font-size:.875rem;font-weight:500;color:#374151;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:.5rem;cursor:pointer;transition:background .15s;"
                        onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                    <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>

        {{-- Mobile right --}}
        <div id="p-mob-right">
            <div style="width:2rem;height:2rem;background:linear-gradient(135deg,#3b82f6,#2563eb);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:600;font-size:.75rem;flex-shrink:0;">
                {{ strtoupper(substr(auth()->user()->name ?? 'P', 0, 1)) }}
            </div>
            <button id="p-ham" onclick="togglePMenu()" class="phb">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>

    <div id="p-mob-menu">
        <div style="padding:.5rem 1rem .75rem;display:flex;flex-direction:column;gap:.25rem;">
            <a href="{{ route('parent.dashboard') }}" onclick="closePMenu()" class="p-mob-link {{ request()->routeIs('parent.dashboard') ? 'act' : '' }}">
                🏠 Dashboard
            </a>
            <div style="border-top:1px solid #f3f4f6;margin-top:.5rem;padding-top:.5rem;">
                <p style="font-size:.75rem;font-weight:600;color:#6b7280;margin:0 0 .5rem;">{{ auth()->user()->name }}</p>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button style="display:flex;align-items:center;gap:.375rem;background:none;border:none;font-size:.875rem;font-weight:600;color:#dc2626;cursor:pointer;padding:0;">
                        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

@if(session('success'))
    <div class="flash-wrap">
        <div class="flash flash-s">
            <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;margin-top:.125rem;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    </div>
@endif
@if(session('error'))
    <div class="flash-wrap">
        <div class="flash flash-e">
            <svg style="width:1.25rem;height:1.25rem;flex-shrink:0;margin-top:.125rem;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    </div>
@endif

<main style="flex:1;max-width:80rem;width:100%;margin:0 auto;padding:1.5rem 1rem;">
    @yield('content')
</main>

<footer>&copy; {{ date('Y') }} Exam Portal. All rights reserved.</footer>

<script>
    var pmOpen = false;
    function togglePMenu() {
        pmOpen = !pmOpen;
        document.getElementById('p-mob-menu').classList.toggle('open', pmOpen);
        document.getElementById('p-ham').classList.toggle('x', pmOpen);
    }
    function closePMenu() {
        pmOpen = false;
        document.getElementById('p-mob-menu').classList.remove('open');
        document.getElementById('p-ham').classList.remove('x');
    }
    window.addEventListener('resize', function() { if(window.innerWidth >= 768) closePMenu(); });
</script>

@stack('scripts')
</body>
</html>