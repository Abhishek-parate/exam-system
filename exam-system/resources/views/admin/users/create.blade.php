@extends('layouts.admin')

@section('page-title', 'Add New User')
@section('page-description', 'Create a new user account')

@section('content')
<div class="container mx-auto max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Users
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-bold mb-6">Add New User</h2>

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror" 
                    placeholder="Enter full name" required>
                @error('name')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror" 
                    placeholder="Enter email address" required>
                @error('email')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="role_id">
                    Role <span class="text-red-500">*</span>
                </label>
                <select name="role_id" id="role_id" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('role_id') border-red-500 @enderror" 
                    required onchange="toggleSubjectSelection()">
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" data-role-name="{{ $role->name }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->display_name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Subject Selection (Hidden by default, shown for teachers) -->
            <div id="subject-selection" class="mb-4 hidden">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Assign Subjects <span class="text-red-500">*</span>
                </label>
                <div class="border rounded-lg p-4 max-h-64 overflow-y-auto bg-gray-50">
                    @forelse($subjects as $subject)
                        <div class="mb-3">
                            <label class="flex items-center cursor-pointer hover:bg-white p-2 rounded transition">
                                <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}" 
                                    class="form-checkbox h-5 w-5 text-blue-600"
                                    {{ in_array($subject->id, old('subject_ids', [])) ? 'checked' : '' }}>
                                <div class="ml-3">
                                    <span class="font-medium text-gray-900">{{ $subject->name }}</span>
                                    @if($subject->code)
                                        <span class="text-gray-500 text-sm ml-2">({{ $subject->code }})</span>
                                    @endif
                                </div>
                            </label>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No subjects available</p>
                    @endforelse
                </div>
                <p class="text-xs text-gray-500 mt-1">Select subjects this teacher will teach</p>
                @error('subject_ids')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password" id="password" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror" 
                    placeholder="Enter password" required>
                @error('password')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password_confirmation">
                    Confirm Password <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" 
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    placeholder="Confirm password" required>
            </div>

            <div class="mb-6">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="form-checkbox h-5 w-5 text-blue-600" checked>
                    <span class="ml-2 text-gray-700">Active</span>
                </label>
            </div>

            <div class="flex items-center justify-between border-t pt-6">
                <a href="{{ route('admin.users.index') }}" 
                    class="text-gray-600 hover:text-gray-800 font-medium">
                    Cancel
                </a>
                <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:shadow-outline transition">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleSubjectSelection() {
    const roleSelect = document.getElementById('role_id');
    const subjectSection = document.getElementById('subject-selection');
    const selectedOption = roleSelect.options[roleSelect.selectedIndex];
    const roleName = selectedOption.getAttribute('data-role-name');
    
    if (roleName === 'teacher') {
        subjectSection.classList.remove('hidden');
    } else {
        subjectSection.classList.add('hidden');
        // Uncheck all subjects when hiding
        document.querySelectorAll('input[name="subject_ids[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });
    }
}

// Run on page load to handle old() values
document.addEventListener('DOMContentLoaded', function() {
    toggleSubjectSelection();
});
</script>
@endsection
