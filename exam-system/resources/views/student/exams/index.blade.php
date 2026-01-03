@extends('layouts.student')

@section('title', 'My Exams')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">My Exams</h1>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($exams as $exam)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                            {{ $exam->title }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $exam->examCategory->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <span class="px-3 py-1 text-xs rounded-full font-semibold
                                @if($exam->status === 'ongoing') bg-green-100 text-green-800
                                @elseif($exam->status === 'upcoming') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-700
                                @endif">
                                {{ ucfirst($exam->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $exam->start_datetime?->format('d M Y, h:i A') ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            @if(in_array($exam->status, ['upcoming', 'ongoing']))
                                <a href="{{ route('student.exams.instructions', $exam) }}"
                                   class="text-blue-600 hover:text-blue-800 font-semibold">
                                    View / Start
                                </a>
                            @else
                                <span class="text-gray-400">Closed</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">
                            No exams assigned to you yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($exams, 'links'))
        <div class="mt-4">
            {{ $exams->links() }}
        </div>
    @endif
</div>
@endsection
