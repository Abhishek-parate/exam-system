@extends('layouts.admin')

@section('title', 'Exam Categories')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Exam Categories</h1>
        <a href="{{ route('admin.exam-categories.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
            + Add Category
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-4 mb-4">
        <form method="GET" action="{{ route('admin.exam-categories.index') }}" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by name..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 rounded-lg">
                Search
            </button>
            <a href="{{ route('admin.exam-categories.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-4 rounded-lg">
               Clear
            </a>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($categories as $category)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            {{ $category->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ Str::limit($category->description, 80) }}
                        </td>
                        <td class="px-6 py-4">
                            @if($category->is_active)
                                <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('admin.exam-categories.edit', $category) }}"
                               class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                            <form action="{{ route('admin.exam-categories.destroy', $category) }}"
                                  method="POST" class="inline"
                                  onsubmit="return confirm('Delete this category?');">
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
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            No exam categories found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
        <div class="mt-4">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection
