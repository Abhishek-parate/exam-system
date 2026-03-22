@extends('layouts.parent')

@section('title', 'Parent Dashboard')

@section('content')
<!-- Header -->
<div class="mb-12">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Parent Dashboard</h1>
            <p class="text-lg text-gray-600">Welcome back, {{ auth()->user()->name }}</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-4 py-2 bg-blue-100 text-blue-800 text-sm font-medium rounded-lg">Active Children: {{ $children->count() }}</span>
        </div>
    </div>
</div>

@if($children->isNotEmpty())
    <!-- Children Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-12">
        @foreach($children as $child)
            @php 
                $latestResult = $child->results->where('is_published', true)->first();
                $totalResults = $child->results->where('is_published', true)->count();
            @endphp
            
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 p-8">
                <!-- Child Info -->
                <div class="flex items-start mb-6 gap-4">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-xl flex-shrink-0">
                        {{ strtoupper(substr($child->user->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xl font-semibold text-gray-900 mb-1 truncate">{{ $child->user->name }}</h3>
                        <p class="text-sm font-medium text-gray-500 mb-1">{{ $child->enrollment_number }}</p>
                        <p class="text-sm text-gray-600">{{ $child->class ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Stats Row -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <div class="text-2xl font-bold text-blue-600">{{ $totalResults }}</div>
                        <div class="text-sm text-gray-500">Results</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-600">{{ $child->examAttempts->count() }}</div>
                        <div class="text-sm text-gray-500">Exams</div>
                    </div>
                </div>

                @if($latestResult)
                    <!-- Latest Result -->
                    <div class="border-t border-gray-100 pt-6 mb-6">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-700">Latest Result</span>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-center">
                                <div class="text-xl font-bold text-emerald-600">{{ round($latestResult->percentage, 1) }}%</div>
                                <div class="text-xs text-gray-500">Score</div>
                            </div>
                            <div class="text-center">
                                <div class="text-xl font-bold text-blue-600">{{ $latestResult->rank ?? '-' }}</div>
                                <div class="text-xs text-gray-500">Rank</div>
                            </div>
                            <div class="text-center">
                                <div class="text-xl font-bold text-purple-600">{{ round($latestResult->accuracy_percentage ?? 0, 1) }}%</div>
                                <div class="text-xs text-gray-500">Accuracy</div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Button -->
                <a href="{{ route('parent.children.performance', $child->id) }}" 
                   class="w-full block text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 border border-blue-600 hover:border-blue-700">
                    View Performance
                </a>
            </div>
        @endforeach
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="text-3xl font-bold text-emerald-600 mb-2">{{ $children->count() }}</div>
            <div class="text-sm font-medium text-gray-600">Total Children</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ $children->sum(fn($child) => $child->results->where('is_published', true)->count()) }}</div>
            <div class="text-sm font-medium text-gray-600">Total Results</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="text-3xl font-bold text-indigo-600 mb-2">{{ $children->sum(fn($child) => $child->examAttempts->count()) }}</div>
            <div class="text-sm font-medium text-gray-600">Exams Taken</div>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="text-3xl font-bold text-orange-600 mb-2">{{ round($children->avg(fn($child) => $child->results->where('is_published', true)->avg('percentage') ?? 0), 1) }}%</div>
            <div class="text-sm font-medium text-gray-600">Average Score</div>
        </div>
    </div>

@else
    <!-- Empty State -->
    <div class="text-center py-20 max-w-2xl mx-auto">
        <div class="w-24 h-24 mx-auto mb-8 bg-gray-100 rounded-2xl flex items-center justify-center">
            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-4">No Children Linked</h2>
        <p class="text-lg text-gray-600 mb-8">Please contact your school administrator to link your child's account to your parent profile.</p>
        <div class="bg-amber-50 border-l-4 border-amber-400 p-6 rounded-xl">
            <p class="text-amber-800 font-medium">Once linked, you'll see all your child's exam results and performance data here.</p>
        </div>
    </div>
@endif
@endsection
