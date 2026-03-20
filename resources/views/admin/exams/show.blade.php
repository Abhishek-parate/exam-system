@extends('layouts.admin')

@section('title', 'Exam Details')

@section('content')
<div class="container mx-auto px-4 py-8">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $exam->title }}</h1>
            <p class="text-gray-500 mt-1 flex items-center gap-2 flex-wrap">
                Code: <span class="font-mono font-bold text-blue-600">{{ $exam->exam_code }}</span>
                &nbsp;|&nbsp;
                <span class="px-2 py-1 rounded-full text-xs font-semibold
                    @if($exam->status==='ongoing') bg-green-100 text-green-800
                    @elseif($exam->status==='upcoming') bg-blue-100 text-blue-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($exam->status) }}
                </span>
                &nbsp;|&nbsp;
                @php $enrollType = $exam->enrollment_type ?? 'open'; @endphp
                @if($enrollType === 'open')
                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Open to All</span>
                @else
                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">Enrolled Only</span>
                @endif
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.exams.edit', $exam) }}"
               class="bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 px-5 rounded-lg transition">Edit</a>
            <a href="{{ route('admin.exams.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-5 rounded-lg transition">Back</a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg border border-green-200">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg border border-red-200">{{ session('error') }}</div>
    @endif

    {{-- STATS --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-5 text-center">
            <p id="stat-total-questions" class="text-3xl font-bold text-blue-600">{{ $stats['total_questions'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Questions</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5 text-center">
            <p id="stat-enrolled-students" class="text-3xl font-bold text-green-600">
                @if($enrollType === 'open') All @else {{ $stats['enrolled_students'] }} @endif
            </p>
            <p class="text-sm text-gray-500 mt-1">Students</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5 text-center">
            <p class="text-3xl font-bold text-purple-600">{{ $stats['total_attempts'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Attempts</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5 text-center">
            <p class="text-3xl font-bold text-orange-600">{{ $stats['completed_attempts'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Completed</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5 text-center">
            <p class="text-3xl font-bold text-emerald-600">{{ $stats['published_results'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Published</p>
        </div>
        <div class="bg-white rounded-lg shadow p-5 text-center">
            <p class="text-3xl font-bold text-rose-600">{{ $stats['unpublished_results'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Under Review</p>
        </div>
    </div>

    {{-- EXAM INFO --}}
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Exam Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div><span class="text-gray-500">Category:</span> <strong>{{ $exam->examCategory?->name ?? 'N/A' }}</strong></div>
            <div><span class="text-gray-500">Duration:</span> <strong>{{ $exam->duration_minutes }} min</strong></div>
            <div><span class="text-gray-500">Total Marks:</span> <strong>{{ $exam->total_marks }}</strong></div>
            <div><span class="text-gray-500">Start:</span> <strong>{{ $exam->start_time->format('d M Y, h:i A') }}</strong></div>
            <div><span class="text-gray-500">End:</span> <strong>{{ $exam->end_time->format('d M Y, h:i A') }}</strong></div>
            <div><span class="text-gray-500">Active:</span>
                <strong class="{{ $exam->is_active ? 'text-green-600' : 'text-red-500' }}">
                    {{ $exam->is_active ? 'Yes' : 'No' }}
                </strong>
            </div>
        </div>
        @if($exam->description)
            <p class="mt-4 text-gray-600 text-sm">{{ $exam->description }}</p>
        @endif
    </div>

    {{-- STUDENT ATTEMPTS & RESULTS SECTION --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">

        <div class="bg-indigo-700 px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-white font-bold text-lg">Student Attempts &amp; Results</h2>
                <p class="text-indigo-200 text-xs mt-0.5">
                    {{ $stats['completed_attempts'] }} submitted &nbsp;&middot;&nbsp;
                    {{ $stats['published_results'] }} published &nbsp;&middot;&nbsp;
                    {{ $stats['unpublished_results'] }} under review
                    @if($stats['no_result_count'] > 0)
                        &nbsp;&middot;&nbsp; <span class="text-red-300 font-semibold">{{ $stats['no_result_count'] }} no result</span>
                    @endif
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if($stats['no_result_count'] > 0)
                    <form action="{{ route('admin.exams.recalculate-results', $exam) }}" method="POST"
                          onsubmit="return confirm('Recalculate results for {{ $stats['no_result_count'] }} attempt(s) that are missing scores?')">
                        @csrf
                        <button type="submit"
                                class="bg-orange-400 hover:bg-orange-500 text-white font-bold text-sm py-2 px-4 rounded-lg transition shadow">
                            Recalculate ({{ $stats['no_result_count'] }})
                        </button>
                    </form>
                @endif
                @if($stats['unpublished_results'] > 0)
                    <form action="{{ route('admin.exams.publish-results', $exam) }}" method="POST"
                          onsubmit="return confirm('Publish ALL {{ $stats['unpublished_results'] }} unpublished result(s)? Students will immediately see their scores.')">
                        @csrf
                        <button type="submit"
                                class="bg-white text-indigo-700 hover:bg-indigo-50 font-bold text-sm py-2 px-5 rounded-lg transition shadow">
                            Publish All ({{ $stats['unpublished_results'] }})
                        </button>
                    </form>
                @else
                    @if($stats['completed_attempts'] > 0 && $stats['no_result_count'] === 0)
                        <span class="text-indigo-200 text-sm font-medium self-center">All results published</span>
                    @endif
                @endif
            </div>
        </div>

        @if($attempts->isEmpty())
            <div class="px-6 py-16 text-center text-gray-400">
                <p class="text-5xl mb-3">?</p>
                <p class="text-lg font-medium">No submissions yet</p>
                <p class="text-sm mt-1">Student attempts will appear here after they submit the exam.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Student</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Submitted At</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Time Taken</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Score</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Correct / Wrong / Skip</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Accuracy</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Rank</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($attempts as $attempt)
                            @php
                                $result      = $attempt->result;
                                $studentName = $attempt->student?->user?->name ?? 'Unknown';
                                $enrollNo    = $attempt->student?->enrollment_number ?? 'N/A';
                                $timeTaken   = $attempt->time_taken_seconds
                                                ? floor($attempt->time_taken_seconds / 60) . 'm ' . ($attempt->time_taken_seconds % 60) . 's'
                                                : '-';
                                $submittedAt = ($attempt->submitted_at ?? $attempt->auto_submitted_at)?->format('d M Y, h:i A') ?? '-';
                                $isAutoSub   = $attempt->status === 'auto_submitted';
                            @endphp
                            <tr class="hover:bg-gray-50 transition" id="attempt-row-{{ $attempt->id }}">
                                <td class="px-4 py-3 text-gray-400 font-mono text-xs">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-indigo-500 text-white flex items-center justify-center text-xs font-bold shrink-0">
                                            {{ strtoupper(substr($studentName, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $studentName }}</p>
                                            <p class="text-xs text-gray-400">{{ $enrollNo }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $submittedAt }}
                                    @if($isAutoSub)
                                        <span class="ml-1 px-1.5 py-0.5 bg-orange-100 text-orange-700 text-xs rounded">Auto</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $timeTaken }}</td>
                                <td class="px-4 py-3">
                                    @if($result)
                                        <span class="font-bold text-gray-800">{{ number_format($result->obtained_marks, 1) }}</span>
                                        <span class="text-gray-400 text-xs"> / {{ number_format($result->total_marks, 1) }}</span>
                                    @else
                                        <span class="text-red-400 italic text-xs font-medium">No Result</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($result)
                                        <span class="text-green-600 font-semibold">{{ $result->correct_answers }}</span>
                                        <span class="text-gray-300 mx-1">/</span>
                                        <span class="text-red-500 font-semibold">{{ $result->wrong_answers }}</span>
                                        <span class="text-gray-300 mx-1">/</span>
                                        <span class="text-gray-400">{{ $result->unattempted }}</span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($result)
                                        @php $acc = $result->accuracy_percentage; @endphp
                                        <div class="flex items-center gap-2">
                                            <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                                <div class="h-1.5 rounded-full
                                                    @if($acc >= 70) bg-green-500
                                                    @elseif($acc >= 40) bg-yellow-500
                                                    @else bg-red-500 @endif"
                                                    style="width: {{ min($acc, 100) }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-600">{{ number_format($acc, 1) }}%</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($result && $result->rank)
                                        <span class="font-bold
                                            @if($result->rank === 1) text-yellow-500
                                            @elseif($result->rank === 2) text-gray-400
                                            @elseif($result->rank === 3) text-amber-600
                                            @else text-gray-600 @endif">
                                            #{{ $result->rank }}
                                        </span>
                                        <span class="text-gray-400 text-xs"> / {{ $result->total_participants }}</span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if(! $result)
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-600">No Result</span>
                                    @elseif($result->is_published)
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Published</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Under Review</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="{{ route('admin.exams.attempts.show', [$exam, $attempt]) }}"
                                           class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg font-medium transition whitespace-nowrap">
                                            Preview
                                        </a>
                                        @if($result && ! $result->is_published)
                                            <button
                                                onclick="publishSingle({{ $result->id }}, this)"
                                                class="text-xs bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg font-medium transition">
                                                Publish
                                            </button>
                                        @elseif($result && $result->is_published)
                                            <span class="text-xs text-gray-400 whitespace-nowrap">
                                                {{ $result->published_at?->format('d M, h:i A') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- QUESTION ASSIGNMENT — stacked vertically for full width --}}
    <div class="space-y-6 mb-8">

        {{-- Assigned Questions --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-blue-600 px-6 py-4">
                <h2 class="text-white font-bold text-lg">
                    Assigned Questions
                    <span id="assigned-count" class="ml-2 bg-white text-blue-600 text-xs font-bold px-2 py-1 rounded-full">
                        {{ $stats['total_questions'] }}
                    </span>
                </h2>
            </div>
            {{-- Grid: 2 cols on md, 3 cols on lg, 4 cols on xl --}}
            <div id="assigned-questions-list" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 p-4 max-h-[600px] overflow-y-auto">
                @forelse($exam->questions as $question)
                    @php
                        $rawHtml     = $question->question_text ?? '';
                        $plainText   = trim(strip_tags($rawHtml));
                        $previewText = \Illuminate\Support\Str::limit($plainText, 120);
                        $imgSrc      = null;
                        if (str_contains($rawHtml, '<img')) {
                            preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $rawHtml, $m);
                            $imgSrc = $m[1] ?? null;
                        }
                        $dn = strtolower($question->difficulty?->name ?? '');
                    @endphp
                    <div id="assigned-row-{{ $question->id }}"
                         class="flex flex-col bg-gray-50 border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition relative group">
                        {{-- Number badge --}}
                        <span class="absolute top-2 left-2 z-10 bg-blue-600 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow">
                            #{{ $loop->iteration }}
                        </span>
                        {{-- Remove button --}}
                        <button onclick="removeQuestion({{ $question->id }})"
                                class="absolute top-2 right-2 z-10 bg-white text-red-400 hover:text-red-600 hover:bg-red-50 p-1 rounded-full shadow transition opacity-0 group-hover:opacity-100"
                                title="Remove">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                        {{-- Image area --}}
                        @if($imgSrc)
                            <div class="w-full bg-white border-b border-gray-200 flex items-center justify-center" style="height:180px;">
                                <img src="{{ $imgSrc }}"
                                     class="max-w-full max-h-full object-contain p-2"
                                     alt="Question image" loading="lazy">
                            </div>
                        @endif
                        {{-- Text content --}}
                        <div class="p-3 flex-1 flex flex-col justify-between">
                            <p class="text-sm text-gray-800 leading-snug mb-2
                                @if($imgSrc) line-clamp-2 @else line-clamp-4 @endif">
                                @if($plainText)
                                    {{ $previewText }}
                                @elseif($imgSrc)
                                    <span class="text-gray-400 italic">Image question</span>
                                @else
                                    <span class="text-gray-400 italic">No content</span>
                                @endif
                            </p>
                            <div class="flex gap-1 flex-wrap mt-auto">
                                <span class="text-xs text-gray-500">{{ $question->subject?->name ?? 'N/A' }}</span>
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                    @if($dn==='easy') bg-green-100 text-green-700
                                    @elseif($dn==='medium') bg-yellow-100 text-yellow-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ $question->difficulty?->name ?? 'N/A' }}
                                </span>
                                <span class="text-xs text-blue-600 font-semibold">+{{ $question->marks }}</span>
                                @if(($question->question_type ?? 'mcq') === 'subjective')
                                    <span class="text-xs px-2 py-0.5 rounded-full font-medium bg-purple-100 text-purple-700">Subjective</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div id="no-questions-msg" class="col-span-4 px-6 py-12 text-center text-gray-400">
                        <p class="text-4xl mb-2">📋</p><p>No questions assigned yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Question Bank --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-green-600 px-6 py-4 flex justify-between items-center">
                <h2 class="text-white font-bold text-lg">Question Bank</h2>
                <span id="bank-result-count" class="text-green-100 text-xs"></span>
            </div>
            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 space-y-2">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    <select id="filter-subject" class="text-sm px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500 col-span-2">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                    <select id="filter-difficulty" class="text-sm px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500">
                        <option value="">All Difficulties</option>
                        @foreach($difficulties as $diff)
                            <option value="{{ $diff->id }}">{{ $diff->name }}</option>
                        @endforeach
                    </select>
                    <div class="flex gap-2">
                        <input type="text" id="filter-search" placeholder="Search..."
                               class="flex-1 text-sm px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500">
                        <button onclick="searchQuestions(1)" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Search</button>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <label class="text-xs text-gray-500 flex items-center gap-1 cursor-pointer">
                        <input type="checkbox" id="select-all-questions" onchange="toggleSelectAll(this)" class="rounded"> Select all visible
                    </label>
                    <button onclick="bulkAdd()" class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg transition font-medium">+ Add Selected</button>
                </div>
            </div>
            {{-- Bank renders as card grid via JS --}}
            <div id="question-bank-list" class="p-4 max-h-[600px] overflow-y-auto">
                <div class="py-8 text-center text-gray-400 text-sm"><p class="text-3xl mb-2">⏳</p><p>Loading...</p></div>
            </div>
            <div id="bank-pagination" class="px-4 py-3 border-t border-gray-100 flex justify-between items-center hidden">
                <button id="btn-prev" onclick="changePage(-1)" class="text-sm text-blue-600 hover:underline disabled:opacity-40">Prev</button>
                <span id="page-info" class="text-xs text-gray-500"></span>
                <button id="btn-next" onclick="changePage(1)" class="text-sm text-blue-600 hover:underline disabled:opacity-40">Next</button>
            </div>
        </div>
    </div>

    {{-- STUDENT ENROLLMENT SECTION --}}
    <div class="mb-8">
        @if($enrollType === 'open')
            <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center">
                <p class="text-green-800 font-semibold text-lg">Open Exam -- all active students can access it automatically.</p>
                <p class="text-green-600 text-sm mt-1">Edit the exam and change Enrollment Type to "Enrolled Only" to restrict access.</p>
            </div>
        @else
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-purple-600 px-6 py-4 flex justify-between items-center">
                        <h2 class="text-white font-bold text-lg">
                            Enrolled Students
                            <span id="enrolled-count" class="ml-2 bg-white text-purple-600 text-xs font-bold px-2 py-1 rounded-full">{{ $stats['enrolled_students'] }}</span>
                        </h2>
                        <button onclick="enrollAll()" class="text-xs bg-white text-purple-700 hover:bg-purple-50 px-3 py-1.5 rounded-lg font-medium transition">+ Enroll All</button>
                    </div>
                    <div id="enrolled-students-list" class="divide-y divide-gray-100 max-h-[400px] overflow-y-auto">
                        @forelse($exam->enrolledStudents as $student)
                            @if($student->user)
                            <div id="enrolled-row-{{ $student->id }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50">
                                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0">{{ strtoupper(substr($student->user->name,0,1)) }}</div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800">{{ $student->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $student->enrollment_number ?? 'N/A' }}</p>
                                </div>
                                <button onclick="unenrollStudent({{ $student->id }})" class="shrink-0 text-red-400 hover:text-red-600 text-xs px-2 py-1 rounded transition">Remove</button>
                            </div>
                            @endif
                        @empty
                            <div id="no-enrolled-msg" class="px-6 py-12 text-center text-gray-400"><p class="text-4xl mb-2">?</p><p>No students enrolled yet.</p></div>
                        @endforelse
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="bg-indigo-600 px-6 py-4"><h2 class="text-white font-bold text-lg">Add Students</h2></div>
                    <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                        <input type="text" id="student-search-input" placeholder="Search by name or enrollment number..." oninput="filterAvailableStudents(this.value)"
                               class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500">
                    </div>
                    <div id="available-students-list" class="divide-y divide-gray-100 max-h-[430px] overflow-y-auto">
                        @forelse($availableStudents as $student)
                            @if($student->user)
                            <div id="available-row-{{ $student->id }}" class="available-student-row flex items-center gap-3 px-4 py-3 hover:bg-gray-50"
                                 data-name="{{ strtolower($student->user->name) }}" data-enrollment="{{ strtolower($student->enrollment_number ?? '') }}">
                                <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0">{{ strtoupper(substr($student->user->name,0,1)) }}</div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800">{{ $student->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $student->enrollment_number ?? 'N/A' }}</p>
                                </div>
                                <button onclick="enrollStudent({{ $student->id }},'{{ addslashes($student->user->name) }}','{{ $student->enrollment_number ?? '' }}')"
                                        id="enroll-btn-{{ $student->id }}"
                                        class="shrink-0 bg-indigo-500 hover:bg-indigo-600 text-white text-xs px-3 py-1.5 rounded-lg font-medium transition">+ Enroll</button>
                            </div>
                            @endif
                        @empty
                            <div class="px-6 py-10 text-center text-gray-400 text-sm">All students are already enrolled.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
    const URL_SEARCH       = "{{ route('admin.exams.questions.search',   $exam) }}";
    const URL_Q_BASE       = "{{ url('admin/exams/' . $exam->id . '/questions') }}";
    const URL_BULK_ADD     = "{{ route('admin.exams.questions.bulk-add', $exam) }}";
    const URL_STU_BASE     = "{{ url('admin/exams/' . $exam->id . '/students') }}";
    const URL_ENROLL_ALL   = "{{ route('admin.exams.students.enroll-all', $exam) }}";
    const URL_PUBLISH_BASE = "{{ url('admin/exams/' . $exam->id . '/results') }}";
    const CSRF_TOKEN       = "{{ csrf_token() }}";
    let currentPage = 1, lastPage = 1;

    document.addEventListener('DOMContentLoaded', () => searchQuestions(1));
    document.getElementById('filter-subject').addEventListener('change',    () => searchQuestions(1));
    document.getElementById('filter-difficulty').addEventListener('change', () => searchQuestions(1));
    document.getElementById('filter-search').addEventListener('keydown', e => { if(e.key==='Enter') searchQuestions(1); });

    function publishSingle(resultId, btn) {
        if (!confirm('Publish this result? The student will immediately see their score.')) return;
        btn.disabled = true; btn.textContent = '...';
        fetch(`${URL_PUBLISH_BASE}/${resultId}/publish`, {
            method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'}
        }).then(r=>r.json()).then(data=>{
            showToast(data.message??'Published!','green');
            const row=btn.closest('tr');
            if(row){
                row.querySelector('td:nth-last-child(2)').innerHTML='<span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Published</span>';
                btn.remove();
            }
        }).catch(()=>{showToast('Failed to publish.','red');btn.disabled=false;btn.textContent='Publish';});
    }

    function searchQuestions(page=1){
        currentPage=page;
        const params=new URLSearchParams({page});
        const s=document.getElementById('filter-subject').value;
        const d=document.getElementById('filter-difficulty').value;
        const q=document.getElementById('filter-search').value.trim();
        if(s) params.append('subject_id',s);
        if(d) params.append('difficulty_id',d);
        if(q) params.append('search',q);
        document.getElementById('question-bank-list').innerHTML='<div class="px-6 py-8 text-center text-gray-400 text-sm"><p class="text-3xl mb-2">?</p><p>Loading...</p></div>';
        fetch(`${URL_SEARCH}?${params}`,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}})
        .then(r=>{if(!r.ok)throw new Error(`HTTP ${r.status}`);return r.json();})
        .then(data=>{if(data.error)throw new Error(data.message);lastPage=data.last_page;renderBankResults(data.questions);updatePagination(data.current_page,data.last_page,data.total);document.getElementById('bank-result-count').textContent=`${data.total} found`;})
        .catch(err=>{document.getElementById('question-bank-list').innerHTML=`<div class="px-6 py-8 text-center text-red-500 text-sm"><p class="text-2xl mb-2">&#9888;</p><p>${escapeHtml(err.message)}</p><button onclick="searchQuestions(1)" class="mt-3 text-blue-600 hover:underline text-xs">Retry</button></div>`;});
    }

    /**
     * FIX: Build a clean text preview from raw HTML question_text.
     *
     * question_text comes from Quill editor and may contain:
     *  - Plain text wrapped in <p> tags
     *  - <img src="data:image/..."> base64 embedded images
     *  - Formatted HTML (bold, sup, etc.)
     *
     * We use a temporary DOM element to:
     *  1. Detect if an image exists
     *  2. Extract plain text (strip all tags)
     *  3. Build a readable preview with an IMG badge if needed
     *
     * This keeps the list clean and avoids embedding heavy base64 blobs.
     */
    function getQuestionPreview(htmlContent) {
        if (!htmlContent) return '<span class="text-gray-400 italic text-xs">No content</span>';

        // Parse HTML safely in a detached DOM node
        var tmp = document.createElement('div');
        tmp.innerHTML = htmlContent;

        var imgEl  = tmp.querySelector('img');
        var text   = (tmp.textContent || tmp.innerText || '').trim();
        var preview = text.length > 80 ? text.substring(0, 80) + '...' : text;

        // Show the actual image thumbnail if an image exists
        var imgHtml = imgEl
            ? '<img src="' + imgEl.src + '" ' +
              'style="width:80px;height:56px;object-fit:contain;border-radius:4px;border:1px solid #e5e7eb;background:#f9fafb;flex-shrink:0;" ' +
              'alt="Question image" loading="lazy">'
            : '';

        var textPart = preview
            ? '<span class="line-clamp-2 text-sm text-gray-800">' + escapeHtml(preview) + '</span>'
            : (imgEl
                ? '<span class="text-gray-400 italic text-xs">Image question</span>'
                : '<span class="text-gray-400 italic text-xs">No content</span>');

        return '<div class="flex items-start gap-2">' + imgHtml + '<div class="flex-1 min-w-0">' + textPart + '</div></div>';
    }

    function renderBankResults(questions){
        const list=document.getElementById('question-bank-list');
        if(!questions?.length){list.innerHTML='<div class="py-10 text-center text-gray-400 text-sm">No questions found.</div>';return;}
        const cm={green:'bg-green-100 text-green-700',yellow:'bg-yellow-100 text-yellow-700',red:'bg-red-100 text-red-700',gray:'bg-gray-100 text-gray-700'};

        function typeBadge(q) {
            if (q.question_type === 'subjective') {
                return '<span class="text-xs px-2 py-0.5 rounded-full font-medium bg-purple-100 text-purple-700">Subjective</span>';
            }
            return '<span class="text-xs text-gray-400">' + (q.options_count || 0) + ' opts</span>';
        }

        // Card grid layout - same style as assigned questions panel
        list.style.display = 'grid';
        list.style.gridTemplateColumns = 'repeat(auto-fill, minmax(220px, 1fr))';
        list.style.gap = '12px';

        list.innerHTML=questions.map(q=>{
            // Extract image src from question_text HTML
            var imgHtml = '';
            if (q.question_text && q.question_text.includes('<img')) {
                var tmp = document.createElement('div');
                tmp.innerHTML = q.question_text;
                var imgEl = tmp.querySelector('img');
                if (imgEl && imgEl.src) {
                    imgHtml = `<div style="height:180px;background:#fff;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;">
                        <img src="${imgEl.src}" style="max-width:100%;max-height:100%;object-fit:contain;padding:8px;" alt="Question image" loading="lazy">
                    </div>`;
                }
            }

            // Plain text preview
            var tmp2 = document.createElement('div');
            tmp2.innerHTML = q.question_text || '';
            var text = (tmp2.textContent || tmp2.innerText || '').trim();
            var preview = text.length > 120 ? text.substring(0, 120) + '...' : text;
            var textPart = preview
                ? `<p style="font-size:13px;color:#1f2937;line-height:1.4;display:-webkit-box;-webkit-line-clamp:${imgHtml?2:4};-webkit-box-orient:vertical;overflow:hidden;margin-bottom:6px;">${escapeHtml(preview)}</p>`
                : (imgHtml ? `<p style="font-size:12px;color:#9ca3af;font-style:italic;margin-bottom:6px;">Image question</p>` : `<p style="font-size:12px;color:#9ca3af;font-style:italic;margin-bottom:6px;">No content</p>`);

            return `<div id="bank-row-${q.id}"
                style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;display:flex;flex-direction:column;transition:box-shadow .15s;position:relative;"
                onmouseenter="this.style.boxShadow='0 4px 12px rgba(0,0,0,.12)'" onmouseleave="this.style.boxShadow='none'">
                <div style="position:absolute;top:8px;left:8px;z-index:10;">
                    <input type="checkbox" class="question-checkbox rounded" value="${q.id}">
                </div>
                ${imgHtml}
                <div style="padding:10px;flex:1;display:flex;flex-direction:column;justify-content:space-between;">
                    ${textPart}
                    <div style="display:flex;flex-wrap:wrap;gap:4px;align-items:center;margin-bottom:8px;">
                        <span style="font-size:11px;color:#6b7280;">${escapeHtml(q.subject)}</span>
                        <span style="font-size:11px;padding:2px 8px;border-radius:999px;font-weight:500;" class="${cm[q.difficulty_color]??cm.gray}">${escapeHtml(q.difficulty)}</span>
                        <span style="font-size:11px;color:#2563eb;font-weight:600;">+${q.marks}</span>
                        ${typeBadge(q)}
                    </div>
                    <button onclick="addQuestion(${q.id})" id="add-btn-${q.id}"
                        style="width:100%;background:#16a34a;color:#fff;font-size:12px;font-weight:600;padding:6px 0;border-radius:6px;border:none;cursor:pointer;transition:background .15s;"
                        onmouseenter="if(!this.disabled)this.style.background='#15803d'" onmouseleave="if(!this.disabled)this.style.background='#16a34a'">
                        + Add
                    </button>
                </div>
            </div>`;
        }).join('');
    }

    function updatePagination(page,last,total){
        const c=document.getElementById('bank-pagination');
        if(last<=1){c.classList.add('hidden');return;}
        c.classList.remove('hidden');
        document.getElementById('page-info').textContent=`Page ${page} of ${last} (${total})`;
        document.getElementById('btn-prev').disabled=page<=1;
        document.getElementById('btn-next').disabled=page>=last;
    }

    function changePage(d){const p=currentPage+d;if(p>=1&&p<=lastPage)searchQuestions(p);}

    function addQuestion(qId){
        const btn=document.getElementById(`add-btn-${qId}`);
        if(btn){btn.disabled=true;btn.textContent='...';}
        fetch(`${URL_Q_BASE}/${qId}`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'}})
        .then(r=>r.json()).then(data=>{
            showToast(data.message.includes('already')?'Already added.':'Question added!',data.message.includes('already')?'yellow':'green');
            if(!data.message.includes('already')){updateStatCount(data.total_questions);reloadAssignedPanel();}
            if(btn){btn.disabled=true;btn.textContent='✓ Added';btn.style.background='#9ca3af';btn.style.cursor='default';}
        }).catch(()=>{showToast('Error.','red');if(btn){btn.disabled=false;btn.textContent='+ Add';}});
    }

    function removeQuestion(qId){
        if(!confirm('Remove this question?'))return;
        fetch(`${URL_Q_BASE}/${qId}`,{method:'DELETE',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'}})
        .then(r=>r.json()).then(data=>{
            showToast('Removed.','red');updateStatCount(data.total_questions);
            document.getElementById(`assigned-row-${qId}`)?.remove();checkEmptyAssigned();
            const ab=document.getElementById(`add-btn-${qId}`);
            if(ab){ab.disabled=false;ab.textContent='+ Add';ab.classList.replace('bg-gray-400','bg-green-500');}
        }).catch(()=>showToast('Error.','red'));
    }

    function bulkAdd(){
        const ids=[...document.querySelectorAll('.question-checkbox:checked')].map(c=>c.value);
        if(!ids.length){showToast('Select at least one.','yellow');return;}
        fetch(URL_BULK_ADD,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'},body:JSON.stringify({question_ids:ids})})
        .then(r=>r.json()).then(data=>{showToast(data.message,'green');updateStatCount(data.total_questions);reloadAssignedPanel();searchQuestions(currentPage);})
        .catch(()=>showToast('Error.','red'));
    }

    function toggleSelectAll(cb){document.querySelectorAll('.question-checkbox').forEach(c=>c.checked=cb.checked);}

    function enrollStudent(studentId,name,enrollmentNo){
        const btn=document.getElementById(`enroll-btn-${studentId}`);
        if(btn){btn.disabled=true;btn.textContent='...';}
        fetch(`${URL_STU_BASE}/${studentId}`,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'}})
        .then(r=>r.json()).then(data=>{
            if(data.message.includes('already')){showToast('Already enrolled.','yellow');return;}
            showToast(`${name} enrolled!`,'green');updateEnrolledCount(data.enrolled_count);
            document.getElementById('no-enrolled-msg')?.remove();
            document.getElementById('enrolled-students-list').insertAdjacentHTML('beforeend',`
                <div id="enrolled-row-${studentId}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50">
                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0">${name.charAt(0).toUpperCase()}</div>
                    <div class="flex-1 min-w-0"><p class="text-sm font-medium text-gray-800">${escapeHtml(name)}</p><p class="text-xs text-gray-500">${escapeHtml(enrollmentNo)}</p></div>
                    <button onclick="unenrollStudent(${studentId})" class="shrink-0 text-red-400 hover:text-red-600 text-xs px-2 py-1 rounded transition">Remove</button>
                </div>`);
            document.getElementById(`available-row-${studentId}`)?.remove();
        }).catch(()=>{showToast('Error.','red');if(btn){btn.disabled=false;btn.textContent='+ Enroll';}});
    }

    function unenrollStudent(studentId){
        if(!confirm('Remove this student?'))return;
        fetch(`${URL_STU_BASE}/${studentId}`,{method:'DELETE',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'}})
        .then(r=>r.json()).then(data=>{showToast('Student removed.','red');updateEnrolledCount(data.enrolled_count);document.getElementById(`enrolled-row-${studentId}`)?.remove();checkEmptyEnrolled();})
        .catch(()=>showToast('Error.','red'));
    }

    function enrollAll(){
        if(!confirm('Enroll ALL students?'))return;
        fetch(URL_ENROLL_ALL,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json'}})
        .then(r=>r.json()).then(data=>{showToast(data.message,'green');updateEnrolledCount(data.enrolled_count);location.reload();})
        .catch(()=>showToast('Error.','red'));
    }

    function filterAvailableStudents(query){
        const q=query.toLowerCase();
        document.querySelectorAll('.available-student-row').forEach(row=>{row.style.display=(row.dataset.name?.includes(q)||row.dataset.enrollment?.includes(q))?'':'none';});
    }

    function reloadAssignedPanel(){
        fetch(window.location.href).then(r=>r.text()).then(html=>{
            const doc=new DOMParser().parseFromString(html,'text/html');
            const nl=doc.getElementById('assigned-questions-list');
            const el=document.getElementById('assigned-questions-list');
            if(nl&&el){el.innerHTML=nl.innerHTML;}
        });
    }

    function updateStatCount(total){document.getElementById('stat-total-questions').textContent=total;document.getElementById('assigned-count').textContent=total;}
    function updateEnrolledCount(count){const el=document.getElementById('enrolled-count');if(el)el.textContent=count;const st=document.getElementById('stat-enrolled-students');if(st)st.textContent=count;}
    function checkEmptyAssigned(){const l=document.getElementById('assigned-questions-list');if(!l.querySelector('[id^="assigned-row-"]'))l.innerHTML=`<div id="no-questions-msg" style="grid-column:1/-1;" class="py-12 text-center text-gray-400"><p class="text-4xl mb-2">📋</p><p>No questions assigned.</p></div>`;}
    function checkEmptyEnrolled(){const l=document.getElementById('enrolled-students-list');if(!l.querySelector('[id^="enrolled-row-"]'))l.innerHTML=`<div id="no-enrolled-msg" class="px-6 py-12 text-center text-gray-400"><p class="text-4xl mb-2">&#128100;</p><p>No students enrolled yet.</p></div>`;}

    function showToast(msg,color='green'){
        const c={green:'bg-green-600',red:'bg-red-600',yellow:'bg-yellow-500'};
        const t=document.createElement('div');
        t.className=`fixed top-5 right-5 z-50 text-white text-sm font-medium px-5 py-3 rounded-lg shadow-lg ${c[color]??'bg-gray-800'}`;
        t.textContent=msg;document.body.appendChild(t);setTimeout(()=>t.remove(),3000);
    }

    function escapeHtml(text){const d=document.createElement('div');d.appendChild(document.createTextNode(text??''));return d.innerHTML;}
</script>
@endpush
@endsection