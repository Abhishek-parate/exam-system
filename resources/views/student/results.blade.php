@extends('layouts.student')

@section('title', 'Results - Student')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Results</h1>
        <span class="text-gray-600">Exam performance overview</span>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4">Completed Exams</h2>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Exam</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Category</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Submitted At</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Score</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Accuracy</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($attempts as $attempt)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">
                            {{ $attempt->exam->title }}
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            {{ $attempt->exam->examCategory->name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            {{ optional($attempt->submitted_at ?? $attempt->auto_submitted_at)->format('d M Y, h:i A') }}
                        </td>
                        <td class="px-4 py-3">
                            @if($attempt->result)
                                <span class="font-semibold text-green-600">
                                    {{ $attempt->result->obtained_marks }} / {{ $attempt->result->total_marks }}
                                    ({{ number_format($attempt->result->percentage, 2) }}%)
                                </span>
                            @else
                                <span class="text-gray-400">Pending</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            @if($attempt->result)
                                {{ number_format($attempt->result->accuracy_percentage ?? 0, 2) }}%
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($attempt->result && $attempt->result->is_published)
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Published
                                </span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Under Review
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            No results available yet. Complete an exam to see your results here.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $attempts->links() }}
        </div>
    </div>
</div>
@endsection
