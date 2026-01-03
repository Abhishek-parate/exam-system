@extends('layouts.admin')

@section('title', 'Subjects')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Subjects</h1>
        <a href="{{ route('admin.subjects.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
            + Add Subject
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-4">
        <form method="GET" action="{{ route('admin.subjects.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by name..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <select name="exam_category_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('exam_category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 rounded-lg">
                    Filter
                </button>
                <a href="{{ route('admin.subjects.index') }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-4 rounded-lg">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($subjects as $subject)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            {{ $subject->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $subject->examCategory->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $subject->code ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($subject->is_active)
                                <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('admin.subjects.edit', $subject) }}"
                               class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                            <form action="{{ route('admin.subjects.destroy', $subject) }}"
                                  method="POST" class="inline"
                                  onsubmit="return confirm('Delete this subject?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            No subjects found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($subjects->hasPages())
        <div class="mt-4">
            {{ $subjects->links() }}
        </div>
    @endif
</div>
@endsection
