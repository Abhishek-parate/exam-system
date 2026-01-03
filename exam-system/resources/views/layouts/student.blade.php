<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student - Exam Portal')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Top Navbar -->
    <nav class="bg-white shadow-md sticky top-0 z-40">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-4">
                <div class="flex items-center gap-6">
                    <a href="{{ route('student.dashboard') }}" class="text-2xl font-bold text-blue-600">
                        🎓 Student Panel
                    </a>
                    <div class="hidden md:flex gap-4 text-sm font-medium">
                        <a href="{{ route('student.dashboard') }}"
                           class="text-gray-700 hover:text-blue-600">
                            Dashboard
                        </a>
                        <a href="{{ route('student.exams.index') }}"
                           class="text-gray-700 hover:text-blue-600">
                            My Exams
                        </a>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="hidden md:block text-right">
                        <p class="text-sm font-semibold text-gray-800">
                            {{ auth()->user()->name }}
                        </p>
                        <p class="text-xs text-gray-500">Student</p>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="ml-2">
                        @csrf
                        <button type="submit"
                                class="text-red-600 hover:text-red-800 text-sm font-semibold">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <main>
        @if(session('success'))
            <div class="container mx-auto px-4 mt-4">
                <div class="bg-green-100 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container mx-auto px-4 mt-4">
                <div class="bg-red-100 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-white border-t mt-12 py-4">
        <div class="container mx-auto px-4 text-center text-gray-500 text-sm">
            © {{ date('Y') }} Exam Portal • Student Area
        </div>
    </footer>

    @stack('scripts')
                <script src="https://cdn.tailwindcss.com"></script>

</body>
</html>
