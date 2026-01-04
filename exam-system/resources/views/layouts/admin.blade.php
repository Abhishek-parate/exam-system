<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Exam Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                        <span class="text-2xl">🎓</span>
                        <span class="text-xl font-bold text-blue-600">Exam Portal</span>
                    </a>
                    
                    <div class="hidden md:flex space-x-6">
                        <a href="{{ route('admin.dashboard') }}" 
                           class="text-gray-700 hover:text-blue-600 font-medium {{ request()->routeIs('admin.dashboard') ? 'text-blue-600' : '' }}">
                            Dashboard
                        </a>
                        
                        <a href="{{ route('admin.subjects.index') }}" 
                           class="text-gray-700 hover:text-blue-600 font-medium {{ request()->routeIs('admin.subjects.*') ? 'text-blue-600' : '' }}">
                            📚 Subjects
                        </a>
                        
                        <a href="{{ route('admin.questions.index') }}" 
                           class="text-gray-700 hover:text-blue-600 font-medium {{ request()->routeIs('admin.questions.*') ? 'text-blue-600' : '' }}">
                            Questions
                        </a>
                        
                        <a href="{{ route('admin.exams.index') }}" 
                           class="text-gray-700 hover:text-blue-600 font-medium {{ request()->routeIs('admin.exams.*') ? 'text-blue-600' : '' }}">
                            Exams
                        </a>
                        
                        <a href="{{ route('admin.users.index') }}" 
                           class="text-gray-700 hover:text-blue-600 font-medium {{ request()->routeIs('admin.users.*') ? 'text-blue-600' : '' }}">
                            Users
                        </a>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">Administrator</p>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
