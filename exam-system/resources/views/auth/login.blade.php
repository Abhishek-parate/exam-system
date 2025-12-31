@extends('layouts.app')

@section('title', 'Login - Exam Portal')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900">Exam Portal</h1>
            <p class="text-gray-600 mt-2">Sign in to continue</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-8">
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                        Email Address
                    </label>
                    <input type="email" name="email" id="email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                           placeholder="admin@exam.com">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                        Password
                    </label>
                    <input type="password" name="password" id="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                           placeholder="••••••••">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="mr-2">
                        <span class="text-sm text-gray-600">Remember me</span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition">
                    Sign In
                </button>
            </form>

            <div class="mt-6 border-t pt-4">
                <p class="text-sm text-gray-600 text-center">Demo Accounts:</p>
                <div class="mt-2 space-y-1 text-xs text-gray-500">
                    <p>👨‍💼 Admin: admin@exam.com / password</p>
                    <p>👨‍🏫 Teacher: teacher@exam.com / password</p>
                    <p>🎓 Student: student@exam.com / password</p>
                    <p>👨‍👩‍👧 Parent: parent@exam.com / password</p>
                </div>
            </div>
        </div>
    </div>
</div>
        <script src="https://cdn.tailwindcss.com"></script>

@endsection
