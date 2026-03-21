@extends('layouts.admin')

@section('title', 'Add New User')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">

    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Add New User</h1>
        <a href="{{ route('admin.users.index') }}"
           class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-6 rounded-lg transition">
            ← Back
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <p class="text-gray-500 mb-6">Create a new system account</p>

        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <p class="font-semibold mb-2">Please fix the following errors:</p>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            {{-- Name --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('name') border-red-500 @enderror"
                       placeholder="Enter full name" required>
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('email') border-red-500 @enderror"
                       placeholder="Enter email address" required>
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Mobile --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                <input type="text" name="mobile" value="{{ old('mobile') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Enter mobile number (optional)">
            </div>

            {{-- Role --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Role <span class="text-red-500">*</span>
                </label>
                <select name="role_name" id="role_select"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500
                               @error('role_name') border-red-500 @enderror"
                        required onchange="toggleRoleFields()">
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role_name') === $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
                @error('role_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Student Extra Fields --}}
            <div id="student_fields" class="hidden">
                <div class="bg-blue-50 rounded-lg p-4 mb-5">
                    <p class="text-sm font-semibold text-blue-700 mb-4">Student Details (Optional)</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                            <input type="text" name="class" value="{{ old('class') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                   placeholder="e.g. 12th, B.Sc Year 1">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Target Exam</label>
                            <select name="target_exam" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="">Select Target</option>
                                <option value="JEE"     {{ old('target_exam') === 'JEE'     ? 'selected' : '' }}>JEE</option>
                                <option value="NEET"    {{ old('target_exam') === 'NEET'    ? 'selected' : '' }}>NEET</option>
                                <option value="MHT-CET" {{ old('target_exam') === 'MHT-CET' ? 'selected' : '' }}>MHT-CET</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" name="address" value="{{ old('address') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                   placeholder="Enter address">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Teacher Extra Fields --}}
            <div id="teacher_fields" class="hidden">
                <div class="bg-green-50 rounded-lg p-4 mb-5">
                    <p class="text-sm font-semibold text-green-700 mb-4">Teacher Details (Optional)</p>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Qualification</label>
                        <input type="text" name="qualification" value="{{ old('qualification') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                               placeholder="e.g. M.Sc Physics">
                    </div>
                </div>
            </div>

            {{-- Password --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Password <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('password') border-red-500 @enderror"
                       placeholder="Enter password" required>
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Confirm Password <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password_confirmation"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Confirm password" required>
            </div>

            {{-- Active Status --}}
            <div class="mb-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                           class="w-4 h-4 text-blue-600 rounded"
                           {{ old('is_active', '1') ? 'checked' : '' }}>
                    <span class="text-sm font-medium text-gray-700">Active Account</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-8 rounded-lg transition">
                    Create User
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-8 rounded-lg transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function toggleRoleFields() {
    const role = document.getElementById('role_select').value;
    document.getElementById('student_fields').classList.toggle('hidden', role !== 'student');
    document.getElementById('teacher_fields').classList.toggle('hidden', role !== 'teacher');
}
// Run on page load in case of old() values
document.addEventListener('DOMContentLoaded', toggleRoleFields);
</script>
@endsection
