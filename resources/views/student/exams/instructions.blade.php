@extends('layouts.student')

@section('title', 'Exam Instructions')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">

    <a href="{{ route('student.exams.index') }}"
       class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6 transition">
        ← Back to My Exams
    </a>

    {{-- Exam Header --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-8 text-white mb-6 shadow-lg">
        <div class="flex justify-between items-start flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold mb-1">{{ $exam->title }}</h1>
                <p class="text-blue-200 text-sm">{{ $exam->examCategory?->name ?? 'General' }}</p>
            </div>
            <span class="px-4 py-2 bg-white bg-opacity-20 rounded-full text-sm font-semibold">
                Code: {{ $exam->exam_code }}
            </span>
        </div>
    </div>

    {{-- Exam Info Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $exam->duration_minutes }}</p>
            <p class="text-xs text-gray-500 mt-1">Minutes</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-purple-600">{{ $exam->total_questions }}</p>
            <p class="text-xs text-gray-500 mt-1">Questions</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $exam->total_marks }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Marks</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-2xl font-bold text-orange-500">
                {{ $exam->end_time->gt(now()) ? (int)$exam->end_time->diffInMinutes(now()) : '—' }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Mins Left</p>
        </div>
    </div>

    {{-- Timing --}}
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">⏰ Exam Timing</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg">
                <span class="text-green-600 text-xl">▶</span>
                <div>
                    <p class="text-gray-500 text-xs">Start Time</p>
                    <p class="font-semibold text-gray-800">{{ $exam->start_time->format('d M Y, h:i A') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 p-3 bg-red-50 rounded-lg">
                <span class="text-red-600 text-xl">⏹</span>
                <div>
                    <p class="text-gray-500 text-xs">End Time</p>
                    <p class="font-semibold text-gray-800">{{ $exam->end_time->format('d M Y, h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Marking Scheme --}}
    @if($exam->markingSchemes->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">📊 Marking Scheme</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-2 px-3 text-gray-600">Subject</th>
                        <th class="text-center py-2 px-3 text-green-600">Correct</th>
                        <th class="text-center py-2 px-3 text-red-500">Wrong</th>
                        <th class="text-center py-2 px-3 text-gray-500">Unattempted</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exam->markingSchemes as $scheme)
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-2 px-3 font-medium text-gray-800">{{ $scheme->subject?->name ?? 'All Subjects' }}</td>
                        <td class="py-2 px-3 text-center text-green-600 font-semibold">+{{ $scheme->correct_marks }}</td>
                        <td class="py-2 px-3 text-center text-red-500 font-semibold">-{{ $scheme->wrong_marks }}</td>
                        <td class="py-2 px-3 text-center text-gray-500">{{ $scheme->unattempted_marks }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Instructions --}}
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">📋 Instructions</h2>
        <ul class="space-y-3 text-sm text-gray-700">
            <li class="flex items-start gap-2">
                <span class="text-blue-500 shrink-0 mt-0.5">•</span>
                <span>The exam will automatically submit when the time runs out.</span>
            </li>
            <li class="flex items-start gap-2">
                <span class="text-blue-500 shrink-0 mt-0.5">•</span>
                <span>Do not refresh or close the browser during the exam.</span>
            </li>
            @if($exam->randomize_questions)
            <li class="flex items-start gap-2">
                <span class="text-blue-500 shrink-0 mt-0.5">•</span>
                <span>Questions are presented in a randomized order.</span>
            </li>
            @endif
            @if($exam->allow_resume)
            <li class="flex items-start gap-2">
                <span class="text-green-500 shrink-0 mt-0.5">•</span>
                <span>If disconnected, you can resume from where you left off.</span>
            </li>
            @else
            <li class="flex items-start gap-2">
                <span class="text-red-500 shrink-0 mt-0.5">•</span>
                <span>This exam does NOT allow resuming after disconnection.</span>
            </li>
            @endif
            @if($exam->description)
            <li class="flex items-start gap-2 p-3 bg-blue-50 rounded-lg">
                <span class="text-blue-500 shrink-0 mt-0.5">ℹ</span>
                <span>{{ $exam->description }}</span>
            </li>
            @endif
        </ul>
    </div>

    {{-- Confirmation & Start --}}
    <div class="bg-white rounded-lg shadow-md p-6">

        {{-- ✅ Checkbox uses onclick inline handler — no event listener dependency --}}
        <label class="flex items-start gap-3 cursor-pointer mb-6 select-none"
               onclick="toggleStart()">
            <input type="checkbox"
                   id="confirm-checkbox"
                   class="mt-1 w-5 h-5 rounded cursor-pointer"
                   style="pointer-events:none;">
            <span class="text-sm text-gray-700">
                I have read and understood all the instructions. I am ready to begin the exam.
            </span>
        </label>

        <div class="flex gap-4">
            {{-- ✅ Start button — enabled/disabled via JS toggleStart() --}}
            <button id="start-btn"
                    onclick="startExam()"
                    disabled
                    class="flex-1 text-white font-bold py-3 px-8 rounded-lg transition text-lg"
                    style="background-color: #9ca3af; cursor: not-allowed;"
                    id="start-btn">
                🚀 Start Exam Now
            </button>
            <a href="{{ route('student.exams.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-3 px-6 rounded-lg transition">
                Cancel
            </a>
        </div>

        <p class="text-xs text-gray-400 mt-3 text-center">
            Once started, the timer cannot be paused.
        </p>
    </div>

</div>

{{-- ✅ Inline script — no @push dependency, runs immediately when page loads --}}
<script>
    var examStarted = false;

    function toggleStart() {
        // Small delay so checkbox state updates first
        setTimeout(function() {
            var checkbox = document.getElementById('confirm-checkbox');
            var btn      = document.getElementById('start-btn');

            if (checkbox.checked) {
                btn.disabled             = false;
                btn.style.backgroundColor = '#16a34a'; // green-600
                btn.style.cursor          = 'pointer';
            } else {
                btn.disabled             = true;
                btn.style.backgroundColor = '#9ca3af'; // gray-400
                btn.style.cursor          = 'not-allowed';
            }
        }, 10);
    }

    function startExam() {
        if (examStarted) return;

        var checkbox = document.getElementById('confirm-checkbox');
        if (!checkbox.checked) {
            alert('Please tick the checkbox first.');
            return;
        }

        examStarted = true;
        var btn     = document.getElementById('start-btn');
        btn.disabled    = true;
        btn.textContent = '⏳ Starting...';

        fetch("{{ route('student.exams.start', $exam) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept':       'application/json',
            }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                window.location.href = data.redirect_url;
            } else {
                alert(data.message || 'Failed to start exam. Please try again.');
                examStarted          = false;
                btn.disabled         = false;
                btn.textContent      = '🚀 Start Exam Now';
                btn.style.backgroundColor = '#16a34a';
            }
        })
        .catch(function() {
            alert('Network error. Please try again.');
            examStarted              = false;
            btn.disabled             = false;
            btn.textContent          = '🚀 Start Exam Now';
            btn.style.backgroundColor = '#16a34a';
        });
    }
</script>
@endsection