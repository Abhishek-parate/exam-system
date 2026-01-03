@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">User Details</h1>
        <div class="flex gap-3">
            <a href="{{ route('admin.users.edit', $user) }}" 
               class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                ✏️ Edit
            </a>
            <a href="{{ route('admin.users.index') }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition">
                ← Back
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <!-- User Avatar & Basic Info -->
        <div class="flex items-center mb-8 pb-6 border-b">
            <div class="flex-shrink-0 h-24 w-24 rounded-full bg-blue-500 flex items-center justify-center text-white text-4xl font-bold">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="ml-6">
                <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                <p class="text-gray-600">{{ $user->email }}</p>
                <div class="flex gap-3 mt-3">
                    <span class="px-3 py-1 text-sm font-semibold rounded-full
                        @if($user->role == 'admin') bg-purple-100 text-purple-800
                        @elseif($user->role == 'instructor') bg-blue-100 text-blue-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($user->role) }}
                    </span>
                    @if($user->is_active)
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                            ✓ Active
                        </span>
                    @else
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                            ✗ Inactive
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- User Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600">User ID</p>
                    <p class="text-lg font-semibold text-gray-900">#{{ $user->id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Email Address</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Role</p>
                    <p class="text-lg font-semibold text-gray-900">{{ ucfirst($user->role) }}</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $user->is_active ? 'Active' : 'Inactive' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Joined Date</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $user->created_at->format('F d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Last Updated</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $user->updated_at->format('F d, Y h:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 mt-8 pt-6 border-t">
            <a href="{{ route('admin.users.edit', $user) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                ✏️ Edit User
            </a>
            @if($user->id !== auth()->id())
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" 
                      onsubmit="return confirm('Are you sure you want to delete this user?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                        🗑️ Delete User
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
