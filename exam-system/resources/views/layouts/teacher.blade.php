<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Teacher Dashboard') - Exam Portal</title>

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
            box-shadow: inset 4px 0 0 0 #10b981;
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
    
    @stack('styles')
</head>

<body class="bg-gray-50">

<!-- ================= HEADER ================= -->
<header class="bg-white border-b border-gray-200 fixed top-0 left-0 right-0 z-50">
    <div class="flex items-center justify-between px-6 py-4">
        <div class="flex items-center gap-3">
            <span class="text-2xl">👨‍🏫</span>
            <span class="text-xl font-bold bg-gradient-to-r from-green-600 to-green-800 bg-clip-text text-transparent">
                Teacher Portal
            </span>
        </div>

        <div class="flex items-center gap-4">
            <div class="text-right">
                <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500">Teacher</p>
            </div>

            <div class="w-10 h-10 bg-gradient-to-br from-green-600 to-green-700 text-white rounded-full flex items-center justify-center font-bold shadow-md">
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
        <a href="{{ route('teacher.dashboard') }}"
           class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition-all duration-300
           {{ request()->routeIs('teacher.dashboard') ? 'active bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50' }}">
            <span class="text-xl">🏠</span>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('teacher.exams.index') }}"
           class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition-all duration-300
           {{ request()->routeIs('teacher.exams.*') ? 'active bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50' }}">
            <span class="text-xl">📝</span>
            <span>Exams</span>
        </a>

        <a href="{{ route('teacher.students.index') }}"
           class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition-all duration-300
           {{ request()->routeIs('teacher.students.*') ? 'active bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50' }}">
            <span class="text-xl">👥</span>
            <span>Students</span>
        </a>

        <a href="{{ route('teacher.reports.index') }}"
           class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-lg font-medium transition-all duration-300
           {{ request()->routeIs('teacher.reports.*') ? 'active bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50' }}">
            <span class="text-xl">📊</span>
            <span>Reports</span>
        </a>
    </nav>

    <!-- Footer Section -->
    <div class="p-4 border-t border-gray-200">
        <!-- Assigned Classes Display -->
        @php
            $teacher = \App\Models\Teacher::where('user_id', auth()->id())->first();
            $assignedClasses = $teacher ? $teacher->classes : collect();
        @endphp
        
        @if($assignedClasses->count() > 0)
            <div class="mb-3 px-4 py-3 bg-gradient-to-r from-green-50 to-green-100 rounded-lg">
                <p class="text-xs font-semibold text-gray-600 mb-2">My Classes</p>
                <div class="space-y-1">
                    @foreach($assignedClasses as $class)
                        <div class="text-xs bg-white text-gray-700 px-2 py-1 rounded shadow-sm">
                            {{ $class->name }}{{ $class->section ? ' - ' . $class->section : '' }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- User Info -->
        <div class="flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-green-50 to-green-100 rounded-lg">
            <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500">Teacher</p>
            </div>
        </div>
    </div>
</aside>

<!-- ================= MAIN CONTENT ================= -->
<main class="pt-20 md:pl-64 px-6 pb-8">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex items-start animate-fade-in" role="alert">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <p class="font-semibold">Success!</p>
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm flex items-start animate-fade-in" role="alert">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <p class="font-semibold">Error!</p>
                <p class="text-sm">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm" role="alert">
            <div class="flex items-start">
                <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <div class="flex-1">
                    <p class="font-semibold mb-2">Please fix the following errors:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    @yield('content')
</main>

<!-- Auto-hide alerts script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('[role="alert"]');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        });
    });
</script>

@stack('scripts')
</body>
</html>
