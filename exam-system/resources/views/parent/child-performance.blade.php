@extends('layouts.parent')

@section('title', $student->user->name . ' - Performance')

@section('content')
<!-- Back Header -->
<div class="flex items-center mb-8">
    <a href="{{ route('parent.dashboard') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium mb-4 md:mb-0">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Dashboard
    </a>
</div>

<!-- Student Header -->
<div class="text-center mb-12">
    <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-2xl mx-auto mb-4">
        {{ strtoupper(substr($student->user->name, 0, 1)) }}
    </div>
    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $student->user->name }}</h1>
    <div class="flex flex-wrap gap-2 justify-center text-sm text-gray-600 mb-4">
        <span>{{ $student->enrollment_number }}</span>
        <span>•</span>
        <span>Class {{ $student->class ?? 'N/A' }}</span>
    </div>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
    <div class="bg-white border border-gray-200 rounded-xl p-6 text-center shadow-sm">
        <div class="text-3xl font-bold text-blue-600 mb-2">{{ $stats['total_exams'] ?? 0 }}</div>
        <div class="text-sm font-medium text-gray-600">Total Exams</div>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-6 text-center shadow-sm">
        <div class="text-3xl font-bold text-emerald-600 mb-2">{{ $stats['published_results'] ?? 0 }}</div>
        <div class="text-sm font-medium text-gray-600">Results</div>
    </div>
    <div class="bg-white border border-gray-200 rounded-xl p-6 text-center shadow-sm">
        <div class="text-3xl font-bold text-purple-600 mb-2">{{ round($stats['avg_score'] ?? 0, 1) }}%</div>
        <div class="text-sm font-medium text-gray-600">Avg Score</div>
    </div>
    @if(isset($stats['best_score']))
    <div class="bg-white border border-gray-200 rounded-xl p-6 text-center shadow-sm">
        <div class="text-3xl font-bold text-orange-600 mb-2">{{ round($stats['best_score'], 1) }}%</div>
        <div class="text-sm font-medium text-gray-600">Best Score</div>
    </div>
    @endif
</div>

<!-- Recent Results -->
<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mb-12">
    <div class="p-6 border-b border-gray-100">
        <h2 class="text-xl font-semibold text-gray-900">Recent Results</h2>
        <p class="text-sm text-gray-500">Latest {{ $results->count() }} published results</p>
    </div>
    @if($results->isNotEmpty())
        <div class="divide-y divide-gray-100">
            @foreach($results->take(10) as $result)
                <div class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 mb-1">{{ $result->exam->title ?? 'Exam' }}</h4>
                            <div class="flex items-center gap-4 text-sm text-gray-500">
                                <span>{{ $result->created_at->format('M d, Y') }}</span>
                                @if($result->exam->examCategory)
                                    <span class="px-2 py-1 bg-gray-100 text-xs rounded-full">{{ $result->exam->examCategory->name }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <div class="text-2xl font-bold text-emerald-600 mb-1">{{ round($result->percentage, 1) }}%</div>
                            <div class="flex items-center justify-end gap-2 text-sm">
                                <span class="font-medium text-gray-900">Rank {{ $result->rank ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <p class="text-lg text-gray-500 font-medium">No results available yet</p>
            <p class="text-sm text-gray-400">Results will appear here once exams are completed and published</p>
        </div>
    @endif
</div>

<!-- Action Buttons -->
<div class="flex flex-col sm:flex-row gap-4 justify-center">
    <a href="{{ route('parent.dashboard') }}" 
       class="flex-1 bg-gray-900 hover:bg-gray-800 text-white font-semibold py-3 px-6 rounded-lg text-center transition-colors shadow-sm hover:shadow-md">
        Back to Dashboard
    </a>
</div>
@endsection
