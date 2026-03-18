<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Exam Portal</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Smooth transitions for sidebar */
        .sidebar-link {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar-link:hover {
            transform: translateX(4px);
        }
        
        /* Active link indicator */
        .sidebar-link.active {
            box-shadow: inset 4px 0 0 0 #2563eb;
        }
        
        /* Fade-in animation for menu items */
        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .sidebar-link {
            animation: fadeInLeft 0.4s ease-out backwards;
        }
        
        .sidebar-link:nth-child(1) { animation-delay: 0.05s; }
        .sidebar-link:nth-child(2) { animation-delay: 0.1s; }
        .sidebar-link:nth-child(3) { animation-delay: 0.15s; }
        .sidebar-link:nth-child(4) { animation-delay: 0.2s; }
        .sidebar-link:nth-child(5) { animation-delay: 0.25s; }
    </style>
</head>

<body class="bg-gray-50">

<!-- ================= HEADER ================= -->
<header class="bg-white border-b border-gray-200 fixed top-0 left-0 right-0 z-50">
    <div class="flex items-center justify-between px-6 py-4">
        <div class="flex items-center gap-3">
            <span class="text-2xl">🎓</span>
            <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                Exam Portal
            </span>
        </div>

        <div class="flex items-center gap-4">
            <div class="text-right">
                <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500">Administrator</p>
            </div>

            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-700 text-white rounded-full flex items-center justify-center font-bold shadow-md">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 shadow-sm">
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>

<!-- ================= SIDEBAR ================= -->
<aside class="fixed top-16 left-0 w-64 h-[calc(100vh-4rem)] bg-white border-r border-gray-200 hidden md:flex flex-col">
    <!-- Navigation Section -->
    <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition-all duration-300
           {{ request()->routeIs('admin.dashboard') ? 'active bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
            <span class="text-xl">🏠</span>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('admin.subjects.index') }}"
           class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition-all duration-300
           {{ request()->routeIs('admin.subjects.*') ? 'active bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
            <span class="text-xl">📚</span>
            <span>Subjects</span>
        </a>

        <a href="{{ route('admin.questions.index') }}"
           class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition-all duration-300
           {{ request()->routeIs('admin.questions.*') ? 'active bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
            <span class="text-xl">❓</span>
            <span>Questions</span>
        </a>

        <a href="{{ route('admin.exams.index') }}"
           class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition-all duration-300
           {{ request()->routeIs('admin.exams.*') ? 'active bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
            <span class="text-xl">📝</span>
            <span>Exams</span>
        </a>

        <a href="{{ route('admin.users.index') }}"
           class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition-all duration-300
           {{ request()->routeIs('admin.users.*') ? 'active bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
            <span class="text-xl">👥</span>
            <span>Users</span>
        </a>
    </nav>

    <!-- Footer Section (Optional) -->
    <div class="p-4 border-t border-gray-200">
        <div class="flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg">
            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500">Admin</p>
            </div>
        </div>
    </div>
</aside>

<!-- ================= MAIN CONTENT ================= -->
<main class="pt-20 md:pl-64 px-6 pb-8">
    @yield('content')
</main>

@stack('scripts')
</body>
</html>
