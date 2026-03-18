@extends('layouts.admin')

@section('title', 'Add New User')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Add New User</h1>
        <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900">
            ← Back
        </a>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}" class="bg-white rounded-lg shadow-md p-8">
        @csrf

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
            <select name="role" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                <option value="">Select Role</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="instructor" {{ old('role') == 'instructor' ? 'selected' : '' }}>Instructor</option>
                <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
            </select>
            @error('role')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
            <input type="password" name="password" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
            <input type="password" name="password_confirmation" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
        </div>

        <div class="mb-6">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 text-blue-600">
                <span class="ml-2 text-sm font-medium text-gray-700">Active</span>
            </label>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                💾 Create User
            </button>
            <a href="{{ route('admin.users.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
