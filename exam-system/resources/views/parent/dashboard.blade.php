@extends('layouts.parent')

@section('title', 'Parent Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Parent Dashboard</h1>
        <span class="text-gray-600">Welcome, {{ auth()->user()->name }}</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($children as $child)
            <div class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
                <div class="flex items-center mb-4">
                    <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                        {{ strtoupper(substr($child->user->name, 0, 1)) }}
                    </div>
                    <div class="ml-4">
                        <h3 class="text-xl font-bold text-gray-900">{{ $child->user->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $child->enrollment_number }}</p>
                    </div>
                </div>

                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Class:</span>
                        <span class="font-semibold">{{ $child->class ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Target Exam:</span>
                        <span class="font-semibold">{{ $child->target_exam ?? 'Not Set' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Total Exams:</span>
                        <span class="font-semibold">{{ $child->examAttempts->count() }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Results:</span>
                        <span class="font-semibold">{{ $child->results->where('is_published', true)->count() }}</span>
                    </div>
                </div>

                @if($child->results->where('is_published', true)->count() > 0)
                    @php
                        $latestResult = $child->results->where('is_published', true)->first();
                    @endphp
                    <div class="border-t pt-4 mb-4">
                        <p class="text-sm text-gray-600 mb-2">Latest Performance:</p>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="text-center p-2 bg-blue-50 rounded">
                                <p class="text-lg font-bold text-blue-600">{{ round($latestResult->percentage, 1) }}%</p>
                                <p class="text-xs text-gray-600">Score</p>
                            </div>
                            <div class="text-center p-2 bg-green-50 rounded">
                                <p class="text-lg font-bold text-green-600">{{ $latestResult->rank }}</p>
                                <p class="text-xs text-gray-600">Rank</p>
                            </div>
                            <div class="text-center p-2 bg-orange-50 rounded">
                                <p class="text-lg font-bold text-orange-600">{{ round($latestResult->accuracy_percentage, 1) }}%</p>
                                <p class="text-xs text-gray-600">Accuracy</p>
                            </div>
                        </div>
                    </div>
                @endif

                <a href="{{ route('parent.children.performance', $child) }}" 
                   class="block text-center bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                    View Detailed Performance →
                </a>
            </div>
        @endforeach
    </div>

    @if($children->isEmpty())
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <svg class="w-24 h-24 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Children Linked</h3>
            <p class="text-gray-600">Please contact the administrator to link your child's account.</p>
        </div>
    @endif
</div>
@endsection
