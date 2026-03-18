@extends('layouts.admin')

@section('title', 'Subjects Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">📚 Subjects Management</h1>
        <a href="{{ route('admin.subjects.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition shadow-lg">
            ➕ Add New Subject
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            <p class="font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('admin.subjects.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Exam Category</label>
                <select name="exam_category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Categories</option>
                    @foreach($examCategories as $category)
                        <option value="{{ $category->id }}" {{ request('exam_category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search subjects..." 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
                    🔍 Filter
                </button>
                <a href="{{ route('admin.subjects.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-6 rounded-lg">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Subjects Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Questions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($subjects as $subject)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                #{{ $subject->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $subject->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $subject->code ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $subject->examCategory ? $subject->examCategory->name : 'No Category' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                                    {{ $subject->questions->count() }} Questions
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($subject->is_active)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                        ✓ Active
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">
                                        ✗ Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.subjects.show', $subject) }}" 
                                       class="text-blue-600 hover:text-blue-900">View</a>
                                    <a href="{{ route('admin.subjects.edit', $subject) }}" 
                                       class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                    <form action="{{ route('admin.subjects.destroy', $subject) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this subject?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <p class="text-lg mb-2">No subjects found</p>
                                    <a href="{{ route('admin.subjects.create') }}" 
                                       class="text-blue-600 hover:text-blue-800">
                                        Create your first subject
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($subjects->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $subjects->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
