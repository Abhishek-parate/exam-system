<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Teacher Panel') - Exam System</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white">
            <div class="p-6 bg-gray-900">
                <h1 class="text-xl font-bold">Teacher Panel</h1>
                <p class="text-sm text-gray-400 mt-1">Exam Management System</p>
            </div>
            
            <nav class="mt-6">
                <a href="{{ route('teacher.dashboard') }}" 
                   class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('teacher.dashboard') ? 'bg-gray-700 text-white border-l-4 border-blue-500' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('teacher.exams.index') }}" 
                   class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('teacher.exams.*') ? 'bg-gray-700 text-white border-l-4 border-blue-500' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Exams
                </a>

                <a href="{{ route('teacher.students.index') }}" 
                   class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('teacher.students.*') ? 'bg-gray-700 text-white border-l-4 border-blue-500' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    Students
                </a>

                <a href="{{ route('teacher.reports.index') }}" 
                   class="flex items-center px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white {{ request()->routeIs('teacher.reports.*') ? 'bg-gray-700 text-white border-l-4 border-blue-500' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Reports
                </a>

                <div class="mt-auto pt-6 border-t border-gray-700">
                    <!-- NEW: Show Assigned Classes -->
                    @php
                        $teacher = \App\Models\Teacher::where('user_id', auth()->id())->first();
                        $assignedClasses = $teacher ? $teacher->classes : collect();
                    @endphp
                    
                    @if($assignedClasses->count() > 0)
                        <div class="px-6 py-3 mb-3">
                            <p class="text-xs text-gray-400 mb-2">My Classes:</p>
                            <div class="space-y-1">
                                @foreach($assignedClasses as $class)
                                    <div class="text-xs bg-gray-700 text-white px-2 py-1 rounded">
                                        {{ $class->name }} {{ $class->section ? '- ' . $class->section : '' }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="px-6 py-3">
                        <p class="text-sm text-gray-400">Logged in as</p>
                        <p class="text-white font-semibold">{{ auth()->user()->name }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-6 py-3 text-gray-300 hover:bg-gray-700 hover:text-white">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1">
            <!-- Header -->
            <header class="bg-white shadow">
                <div class="px-6 py-4">
                    <h1 class="text-2xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                    @if(View::hasSection('page-description'))
                        <p class="text-sm text-gray-600 mt-1">@yield('page-description')</p>
                    @endif
                </div>
            </header>

            <!-- Content Area -->
            <div class="p-6">
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
