@extends('layouts.admin')

@section('title', 'Edit Exam')
@section('page-title', 'Edit Exam')
@section('page-description', 'Update exam details and configuration')

@section('content')
<div class="max-w-7xl mx-auto">
    <form action="{{ route('admin.exams.update', $exam) }}" method="POST" id="editExamForm">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6 pb-4 border-b">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Edit Exam: {{ $exam->title }}</h2>
                    <p class="text-sm text-gray-500 mt-1">Exam Code: <span class="font-mono">{{ $exam->exam_code }}</span></p>
                </div>
                <a href="{{ route('admin.exams.show', $exam) }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    ← Back to Exam
                </a>
            </div>

            <!-- Basic Information -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">📋 Basic Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Exam Title -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Exam Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title', $exam->title) }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Exam Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Exam Category <span class="text-red-500">*</span>
                        </label>
                        <select name="exam_category_id" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                required>
                            <option value="">Select Category</option>
                            @foreach($examCategories as $category)
                                <option value="{{ $category->id }}" {{ old('exam_category_id', $exam->exam_category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('exam_category_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Class -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Class (Optional)
                        </label>
                        <select name="school_class_id" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('school_class_id', $exam->school_class_id) == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Assigned Teacher -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Assign Teacher (Optional)
                        </label>
                        <select name="assigned_teacher_id" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Teacher</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('assigned_teacher_id', $exam->assigned_teacher_id) == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->user->name ?? 'Unknown' }}
                                    @if($teacher->subjects->count() > 0)
                                        ({{ $teacher->subjects->pluck('name')->join(', ') }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Description (Optional)
                        </label>
                        <textarea name="description" rows="3" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('description', $exam->description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Exam Configuration -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">⚙️ Exam Configuration</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Duration -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Duration (minutes) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}" 
                               min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                        @error('duration_minutes')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Total Questions -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Total Questions <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="total_questions" value="{{ old('total_questions', $exam->total_questions) }}" 
                               min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-100" 
                               required readonly>
                        @error('total_questions')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Total Marks -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Total Marks <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="total_marks" value="{{ old('total_marks', $exam->total_marks) }}" 
                               min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                        @error('total_marks')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Passing Marks -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Passing Marks <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="passing_marks" value="{{ old('passing_marks', $exam->passing_marks) }}" 
                               min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                        @error('passing_marks')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Schedule -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">📅 Schedule</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Start Time -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Start Time <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="start_time" 
                               value="{{ old('start_time', \Carbon\Carbon::parse($exam->start_time)->format('Y-m-d\TH:i')) }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                        @error('start_time')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- End Time -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            End Time <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="end_time" 
                               value="{{ old('end_time', \Carbon\Carbon::parse($exam->end_time)->format('Y-m-d\TH:i')) }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               required>
                        @error('end_time')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Result Release Time -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Result Release Time (Optional)
                        </label>
                        <input type="datetime-local" name="result_release_time" 
                               value="{{ old('result_release_time', $exam->result_release_time ? \Carbon\Carbon::parse($exam->result_release_time)->format('Y-m-d\TH:i') : '') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Exam Settings -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">🔧 Exam Settings</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Randomize Questions -->
                    <div class="flex items-center">
                        <input type="checkbox" name="randomize_questions" value="1" 
                               {{ old('randomize_questions', $exam->randomize_questions) ? 'checked' : '' }}
                               id="randomize_questions" 
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="randomize_questions" class="ml-2 text-sm text-gray-700">
                            Randomize Questions Order
                        </label>
                    </div>

                    <!-- Randomize Options -->
                    <div class="flex items-center">
                        <input type="checkbox" name="randomize_options" value="1" 
                               {{ old('randomize_options', $exam->randomize_options) ? 'checked' : '' }}
                               id="randomize_options" 
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="randomize_options" class="ml-2 text-sm text-gray-700">
                            Randomize Answer Options
                        </label>
                    </div>

                    <!-- Show Results Immediately -->
                    <div class="flex items-center">
                        <input type="checkbox" name="show_results_immediately" value="1" 
                               {{ old('show_results_immediately', $exam->show_results_immediately) ? 'checked' : '' }}
                               id="show_results_immediately" 
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="show_results_immediately" class="ml-2 text-sm text-gray-700">
                            Show Results Immediately After Submission
                        </label>
                    </div>

                    <!-- Allow Resume -->
                    <div class="flex items-center">
                        <input type="checkbox" name="allow_resume" value="1" 
                               {{ old('allow_resume', $exam->allow_resume) ? 'checked' : '' }}
                               id="allow_resume" 
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="allow_resume" class="ml-2 text-sm text-gray-700">
                            Allow Students to Resume Exam
                        </label>
                    </div>
                </div>
            </div>

            <!-- Current Questions Info -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">❓ Current Questions ({{ $exam->questions->count() }})</h3>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-800 mb-2">
                        <strong>Note:</strong> This exam currently has <strong>{{ $exam->questions->count() }} questions</strong>. 
                    </p>
                    <p class="text-xs text-blue-700">
                        Questions cannot be modified after exam creation. To change questions, please create a new exam.
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('admin.exams.show', $exam) }}" 
                   class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    💾 Update Exam
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
