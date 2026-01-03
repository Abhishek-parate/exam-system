@extends('layouts.admin')

@section('title', 'Reports & Analytics')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">📊 Reports & Analytics</h1>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
            🖨️ Print Report
        </button>
    </div>

    <!-- Overall Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Total Users -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Users</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['total_users'] }}</p>
                    <p class="text-blue-100 text-xs mt-2">{{ $stats['active_users'] }} active</p>
                </div>
                <div class="text-5xl opacity-50">👥</div>
            </div>
        </div>

        <!-- Total Questions -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Total Questions</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['total_questions'] }}</p>
                    <p class="text-green-100 text-xs mt-2">{{ $stats['active_questions'] }} active</p>
                </div>
                <div class="text-5xl opacity-50">❓</div>
            </div>
        </div>

        <!-- Total Categories -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Categories</p>
                    <p class="text-4xl font-bold mt-2">{{ $stats['total_categories'] }}</p>
                    <p class="text-purple-100 text-xs mt-2">{{ $stats['total_subjects'] }} subjects</p>
                </div>
                <div class="text-5xl opacity-50">📚</div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Users by Role Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Users by Role</h2>
            <div class="space-y-3">
                @foreach(['admin' => 'Admin', 'instructor' => 'Instructor', 'student' => 'Student'] as $role => $label)
                    @php
                        $count = $usersByRole[$role] ?? 0;
                        $percentage = $stats['total_users'] > 0 ? round(($count / $stats['total_users']) * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $count }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-blue-600 h-3 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Questions by Difficulty -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Questions by Difficulty</h2>
            <div class="space-y-3">
                @foreach($questionsByDifficulty as $difficulty)
                    @php
                        $percentage = $stats['total_questions'] > 0 ? round(($difficulty->count / $stats['total_questions']) * 100) : 0;
                        $color = $difficulty->name == 'Easy' ? 'green' : ($difficulty->name == 'Medium' ? 'yellow' : 'red');
                    @endphp
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">{{ $difficulty->name }}</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $difficulty->count }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-{{ $color }}-500 h-3 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Questions by Category -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Questions by Category</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Question Count</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Percentage</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Visual</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($questionsByCategory as $category)
                        @php
                            $percentage = $stats['total_questions'] > 0 ? round(($category->count / $stats['total_questions']) * 100) : 0;
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $category->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $category->count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $percentage }}%</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Questions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Recent Questions</h2>
            <div class="space-y-3">
                @forelse($recentQuestions as $question)
                    <div class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 line-clamp-2">
                                {!! strip_tags($question->question_text) !!}
                            </p>
                            <div class="flex gap-2 mt-2">
                                <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded">{{ $question->examCategory->name }}</span>
                                <span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded">{{ $question->difficulty->name }}</span>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500 ml-2">{{ $question->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No questions yet</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Recent Users</h2>
            <div class="space-y-3">
                @forelse($recentUsers as $user)
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        </div>
                        <span class="text-xs px-2 py-1 rounded
                            @if($user->role == 'admin') bg-purple-100 text-purple-800
                            @elseif($user->role == 'instructor') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No users yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
