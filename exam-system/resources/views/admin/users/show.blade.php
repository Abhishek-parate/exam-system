@extends('layouts.admin')

@section('page-title', 'User Details')
@section('page-description', $user->name)

@section('content')
<div class="container mx-auto max-w-4xl">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Users
        </a>
        
        <a href="{{ route('admin.users.edit', $user->id) }}" 
           class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
            Edit User
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <!-- User Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-8 text-white">
            <div class="flex items-center">
                <div class="h-20 w-20 rounded-full {{ $user->isAdmin() ? 'bg-red-500' : ($user->isTeacher() ? 'bg-blue-500' : ($user->isStudent() ? 'bg-green-500' : 'bg-purple-500')) }} flex items-center justify-center text-2xl font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="ml-6">
                    <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                    <p class="text-blue-100">{{ $user->email }}</p>
                    <div class="mt-2 flex items-center gap-2">
                        <span class="px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm">
                            {{ $user->getRoleName() }}
                        </span>
                        <span class="px-3 py-1 {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }} rounded-full text-sm">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Details -->
        <div class="p-6">
            <h3 class="text-lg font-bold mb-4">Account Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">User ID</label>
                    <p class="text-gray-900">#{{ $user->id }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Join Date</label>
                    <p class="text-gray-900">{{ $user->created_at->format('F d, Y') }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Last Updated</label>
                    <p class="text-gray-900">{{ $user->updated_at->format('F d, Y h:i A') }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Email Verified</label>
                    <p class="text-gray-900">
                        @if($user->email_verified_at)
                            <span class="text-green-600">✓ Verified</span>
                        @else
                            <span class="text-red-600">✗ Not Verified</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Role-Specific Details -->
            @if($user->isStudent() && $user->student)
                <div class="border-t pt-6">
                    <h3 class="text-lg font-bold mb-4">Student Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Enrollment Number</label>
                            <p class="text-gray-900">{{ $user->student->enrollment_number ?? 'N/A' }}</p>
                        </div>
                        @if($user->student->schoolClass)
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Class</label>
                                <p class="text-gray-900">{{ $user->student->schoolClass->name }} {{ $user->student->schoolClass->section }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if($user->isTeacher() && $user->teacher)
                <div class="border-t pt-6">
                    <h3 class="text-lg font-bold mb-4">Teacher Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Employee ID</label>
                            <p class="text-gray-900">{{ $user->teacher->employee_id ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Assigned Classes</label>
                            <p class="text-gray-900">{{ $user->teacher->classes()->count() }} {{ Str::plural('Class', $user->teacher->classes()->count()) }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
