@extends('layouts.admin')

@section('title', 'Exams - Admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Exam Management</h1>
        <a href="{{ route('admin.exams.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
            ➕ Create New Exam
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('admin.exams.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">All Status</option>
                    <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <div class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search exam title..."
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                        🔍
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Exams Grid -->
    @if($exams->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
            @foreach($exams as $exam)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-lg font-bold text-gray-900">{{ $exam->title }}</h3>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                @if($exam->status === 'ongoing') bg-green-100 text-green-800
                                @elseif($exam->status === 'upcoming') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($exam->status) }}
                            </span>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-3">{{ $exam->examCategory->name }}</p>
                        
                        <div class="space-y-2 text-sm text-gray-700 mb-4">
                            <div class="flex items-center">
                                <span class="w-24 text-gray-500">Code:</span>
                                <span class="font-mono font-semibold">{{ $exam->exam_code }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-24 text-gray-500">Duration:</span>
                                <span>{{ $exam->duration_minutes }} minutes</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-24 text-gray-500">Questions:</span>
                                <span>{{ $exam->questions->count() }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-24 text-gray-500">Total Marks:</span>
                                <span>{{ $exam->total_marks }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-24 text-gray-500">Start Time:</span>
                                <span>{{ $exam->start_time->format('d M Y, h:i A') }}</span>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('admin.exams.show', $exam) }}" 
                               class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition">
                                View Details
                            </a>
                            <a href="{{ route('admin.exams.edit', $exam) }}" 
                               class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                                ✏️
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="bg-white rounded-lg shadow-md p-4">
            {{ $exams->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No exams found</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new exam.</p>
            <div class="mt-6">
                <a href="{{ route('admin.exams.create') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    ➕ Create New Exam
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
