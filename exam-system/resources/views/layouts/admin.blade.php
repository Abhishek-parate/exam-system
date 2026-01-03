<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Exam Portal')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- ✅ QUILL EDITOR --}}
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-8">
                    <a href="/admin/dashboard" class="text-2xl font-bold text-blue-600">
                        🎓 Exam Portal
                    </a>
                  <div class="hidden md:flex space-x-6">
    <a href="{{ route('admin.dashboard') }}"
       class="text-gray-700 hover:text-blue-600 font-medium transition">
        Dashboard
    </a>

    <a href="{{ route('admin.exam-categories.index') }}"
       class="text-gray-700 hover:text-blue-600 font-medium transition">
        Exam Categories
    </a>

    <a href="{{ route('admin.subjects.index') }}"
       class="text-gray-700 hover:text-blue-600 font-medium transition">
        Subjects
    </a>

    <a href="{{ route('admin.questions.index') }}"
       class="text-gray-700 hover:text-blue-600 font-medium transition">
        Questions
    </a>

    <a href="{{ route('admin.exams.index') }}"
       class="text-gray-700 hover:text-blue-600 font-medium transition">
        Exams
    </a>

    <a href="{{ route('admin.users.index') }}"
       class="text-gray-700 hover:text-blue-600 font-medium transition">
        Users
    </a>

    <a href="{{ route('admin.reports') }}"
       class="text-gray-700 hover:text-blue-600 font-medium transition">
        Reports
    </a>
</div>

                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden md:block">
                            <p class="text-sm font-semibold text-gray-700">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">Administrator</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium transition">Logout</button>
                    </form>
                </div>
            </div>
        </div>
        
    </nav>

    <!-- Main Content -->
    <main>
        @if(session('success'))
            <div class="container mx-auto px-4 mt-4">
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container mx-auto px-4 mt-4">
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12 py-6">
        <div class="container mx-auto px-4 text-center text-gray-600">
            <p>&copy; {{ date('Y') }} Exam Portal. All rights reserved.</p>
        </div>
    </footer>
    
    @stack('scripts')
            <script src="https://cdn.tailwindcss.com"></script>

</body>
</html>
