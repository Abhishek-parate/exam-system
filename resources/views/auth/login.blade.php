@extends('layouts.app')

@section('title', 'Login - Exam Portal')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-100 to-indigo-200 px-4">

    <div class="w-full max-w-md">

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight">
                Exam Portal
            </h1>
            <p class="text-gray-600 mt-2 text-sm">
                Welcome back! Please login to your account
            </p>
        </div>

        <!-- Card -->
        <div class="bg-white/80 backdrop-blur-lg shadow-xl rounded-2xl p-8 border border-gray-200">

            <!-- Alerts -->
            @if(session('error'))
                <div class="flex items-center gap-2 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                    ⚠️ {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="flex items-center gap-2 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <!-- Email -->
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Email Address
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none transition"
                        placeholder="Enter your email"
                    >
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-5 relative">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Password
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none transition"
                        placeholder="Enter your password"
                    >

                    <!-- Toggle -->
                    <button type="button" onclick="togglePassword()" 
                        class="absolute right-3 top-9 text-gray-500 text-sm">
                        👁️
                    </button>

                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember -->
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center text-sm text-gray-600">
                        <input type="checkbox" name="remember" class="mr-2 accent-blue-500">
                        Remember me
                    </label>

                    <!-- Optional future -->
                    {{-- <a href="#" class="text-sm text-blue-500 hover:underline">Forgot Password?</a> --}}
                </div>

                <!-- Button -->
                <button 
                    type="submit" 
                    id="loginBtn"
                    class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition duration-200 flex justify-center items-center"
                >
                    <span id="btnText">Sign In</span>
                    <svg id="loader" class="animate-spin ml-2 h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="white" stroke-width="4"></circle>
                        <path class="opacity-75" fill="white"
                            d="M4 12a8 8 0 018-8v8z">
                        </path>
                    </svg>
                </button>

            </form>

        </div>

        <!-- Footer -->
        <p class="text-center text-xs text-gray-500 mt-6">
            © {{ date('Y') }} Exam Portal. All rights reserved.
        </p>

    </div>
</div>

<!-- Scripts -->
<script>
function togglePassword() {
    const password = document.getElementById('password');
    password.type = password.type === 'password' ? 'text' : 'password';
}

// Button Loader
document.getElementById('loginForm').addEventListener('submit', function () {
    document.getElementById('btnText').innerText = 'Signing In...';
    document.getElementById('loader').classList.remove('hidden');
});
</script>

<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@endsection