{{-- resources/views/student/exams/attempt.blade.php --}}
@extends('layouts.student')

@section('content')
<div class="min-h-screen bg-gray-100" x-data="examEngine()" x-init="init()">
    <!-- Sticky Timer Bar -->
    <div class="sticky top-0 z-50 bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-bold">{{ $attempt->exam->title }}</h2>
                <p class="text-sm">{{ $attempt->exam->exam_code }}</p>
            </div>
            
            <div class="flex items-center space-x-6">
                <div class="text-center">
                    <div class="text-2xl font-mono font-bold" x-text="formatTime(remainingSeconds)"></div>
                    <div class="text-xs">Time Remaining</div>
                </div>
                
                <button @click="submitExam()" class="bg-red-500 hover:bg-red-600 px-6 py-2 rounded-lg font-bold">
                    Submit Exam
                </button>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6">
        <div class="grid grid-cols-12 gap-6">
            <!-- Question Area -->
            <div class="col-span-9">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <template x-for="(question, index) in questions" :key="question.id">
                        <div x-show="currentQuestionIndex === index" class="question-container">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-semibold">
                                    Question <span x-text="index + 1"></span> of <span x-text="questions.length"></span>
                                </h3>
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                    <span x-text="question.marks"></span> Marks
                                </span>
                            </div>

                            <!-- Question Text -->
                            <div class="mb-6 text-gray-800 leading-relaxed" x-html="question.question_text"></div>

                            <!-- Question Image -->
                            <template x-if="question.question_image">
                                <img :src="'/storage/' + question.question_image" 
                                     class="max-w-md mb-6 rounded-lg shadow">
                            </template>

                            <!-- Options -->
                            <div class="space-y-3">
                                <template x-for="option in question.options" :key="option.id">
                                    <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition"
                                           :class="answers[question.id]?.selected_option_id === option.id 
                                                  ? 'border-blue-500 bg-blue-50' 
                                                  : 'border-gray-200 hover:border-blue-300'"
                                           @click="selectOption(question.id, option.id)">
                                        <input type="radio" 
                                               :name="'question_' + question.id"
                                               :value="option.id"
                                               :checked="answers[question.id]?.selected_option_id === option.id"
                                               class="mt-1 mr-3">
                                        
                                        <div class="flex-1">
                                            <span class="font-semibold mr-2" x-text="option.option_key + '.'"></span>
                                            <span x-html="option.option_text"></span>
                                            
                                            <template x-if="option.option_image">
                                                <img :src="'/storage/' + option.option_image" 
                                                     class="mt-2 max-w-xs rounded">
                                            </template>
                                        </div>
                                    </label>
                                </template>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex justify-between mt-8">
                                <div class="space-x-3">
                                    <button @click="markForReview(question.id)" 
                                            class="px-4 py-2 border-2 border-yellow-500 text-yellow-600 rounded-lg hover:bg-yellow-50">
                                        <span x-text="answers[question.id]?.is_marked_for_review ? 'Unmark Review' : 'Mark for Review'"></span>
                                    </button>
                                    
                                    <button @click="clearResponse(question.id)" 
                                            class="px-4 py-2 border-2 border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50">
                                        Clear Response
                                    </button>
                                </div>

                                <div class="space-x-3">
                                    <button @click="previousQuestion()" 
                                            x-show="currentQuestionIndex > 0"
                                            class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                                        Previous
                                    </button>
                                    
                                    <button @click="nextQuestion()" 
                                            class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                        <span x-text="currentQuestionIndex < questions.length - 1 ? 'Save & Next' : 'Save'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Question Palette -->
            <div class="col-span-3">
                <div class="bg-white rounded-lg shadow-md p-4 sticky top-24">
                    <h4 class="font-bold text-lg mb-4">Question Palette</h4>
                    
                    <!-- Legend -->
                    <div class="grid grid-cols-2 gap-2 mb-4 text-xs">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                            <span>Attempted</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                            <span>Not Attempted</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-yellow-500 rounded mr-2"></div>
                            <span>Review</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-blue-500 rounded mr-2"></div>
                            <span>Current</span>
                        </div>
                    </div>

                    <!-- Question Numbers -->
                    <div class="grid grid-cols-5 gap-2">
                        <template x-for="(question, index) in questions" :key="question.id">
                            <button @click="goToQuestion(index)"
                                    class="aspect-square rounded-lg font-semibold text-white"
                                    :class="{
                                        'bg-blue-500': currentQuestionIndex === index,
                                        'bg-green-500': currentQuestionIndex !== index && answers[question.id]?.selected_option_id && !answers[question.id]?.is_marked_for_review,
                                        'bg-yellow-500': answers[question.id]?.is_marked_for_review,
                                        'bg-red-500': !answers[question.id]?.selected_option_id && !answers[question.id]?.is_marked_for_review && currentQuestionIndex !== index
                                    }"
                                    x-text="index + 1">
                            </button>
                        </template>
                    </div>

                    <!-- Statistics -->
                    <div class="mt-6 pt-4 border-t space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Attempted:</span>
                            <span class="font-bold text-green-600" x-text="getAttemptedCount()"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Not Attempted:</span>
                            <span class="font-bold text-red-600" x-text="getNotAttemptedCount()"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Marked for Review:</span>
                            <span class="font-bold text-yellow-600" x-text="getMarkedCount()"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function examEngine() {
    return {
        attemptToken: '{{ $attempt->attempt_token }}',
        questions: @json($questions->values()),
        answers: {},
        currentQuestionIndex: 0,
        remainingSeconds: {{ $attempt->getRemainingTimeSeconds() }},
        questionStartTime: null,
        statusPollInterval: null,
        autoSaveInterval: null,
        
        init() {
            preventBack();
            this.loadExistingAnswers();
            this.startStatusPolling();
            this.startAutoSave();
            this.startQuestionTimer();
            
            // Warn before leaving
            window.addEventListener('beforeunload', (e) => {
                if (!this.isSubmitted) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        },
        
        loadExistingAnswers() {
            @foreach($attempt->answers as $answer)
                this.answers[{{ $answer->question_id }}] = {
                    selected_option_id: {{ $answer->selected_option_id ?? 'null' }},
                    is_marked_for_review: {{ $answer->is_marked_for_review ? 'true' : 'false' }}
                };
            @endforeach
        },
        
        startStatusPolling() {
            this.statusPollInterval = setInterval(() => {
                this.checkExamStatus();
            }, 5000); // Poll every 5 seconds
        },
        
        async checkExamStatus() {
            try {
                const response = await fetch(`/student/exams/${this.attemptToken}/status`);
                const data = await response.json();
                
                if (data.time_expired) {
                    alert('Time is up! Your exam has been auto-submitted.');
                    window.location.href = data.redirect_url;
                } else {
                    this.remainingSeconds = data.remaining_seconds;
                }
            } catch (error) {
                console.error('Status poll failed:', error);
            }
        },
        
        startAutoSave() {
            this.autoSaveInterval = setInterval(() => {
                this.saveCurrentAnswer();
            }, 10000); // Auto-save every 10 seconds
        },
        
        startQuestionTimer() {
            this.questionStartTime = Date.now();
        },
        
        async trackQuestionTime() {
            if (!this.questionStartTime) return;
            
            const timeSpent = Math.floor((Date.now() - this.questionStartTime) / 1000);
            const currentQuestion = this.questions[this.currentQuestionIndex];
            
            if (timeSpent > 0) {
                await fetch(`/student/exams/${this.attemptToken}/track-time`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        question_id: currentQuestion.id,
                        time_spent: timeSpent
                    })
                });
            }
        },
        
        async selectOption(questionId, optionId) {
            if (!this.answers[questionId]) {
                this.answers[questionId] = {};
            }
            this.answers[questionId].selected_option_id = optionId;
            await this.saveAnswer(questionId);
        },
        
        async markForReview(questionId) {
            if (!this.answers[questionId]) {
                this.answers[questionId] = {};
            }
            this.answers[questionId].is_marked_for_review = !this.answers[questionId].is_marked_for_review;
            await this.saveAnswer(questionId);
        },
        
        async clearResponse(questionId) {
            if (this.answers[questionId]) {
                this.answers[questionId].selected_option_id = null;
                await this.saveAnswer(questionId);
            }
        },
        
        async saveAnswer(questionId) {
            try {
                const response = await fetch(`/student/exams/${this.attemptToken}/save-answer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        question_id: questionId,
                        option_id: this.answers[questionId]?.selected_option_id,
                        is_marked_for_review: this.answers[questionId]?.is_marked_for_review || false
                    })
                });
                
                const data = await response.json();
                return data.success;
            } catch (error) {
                console.error('Save failed:', error);
                return false;
            }
        },
        
        async saveCurrentAnswer() {
            const currentQuestion = this.questions[this.currentQuestionIndex];
            await this.saveAnswer(currentQuestion.id);
        },
        
        async nextQuestion() {
            await this.trackQuestionTime();
            await this.saveCurrentAnswer();
            
            if (this.currentQuestionIndex < this.questions.length - 1) {
                this.currentQuestionIndex++;
                this.startQuestionTimer();
            }
        },
        
        async previousQuestion() {
            await this.trackQuestionTime();
            await this.saveCurrentAnswer();
            
            if (this.currentQuestionIndex > 0) {
                this.currentQuestionIndex--;
                this.startQuestionTimer();
            }
        },
        
        async goToQuestion(index) {
            await this.trackQuestionTime();
            await this.saveCurrentAnswer();
            
            this.currentQuestionIndex = index;
            this.startQuestionTimer();
        },
        
        async submitExam() {
            if (!confirm('Are you sure you want to submit the exam? You cannot change answers after submission.')) {
                return;
            }
            
            await this.trackQuestionTime();
            await this.saveCurrentAnswer();
            
            clearInterval(this.statusPollInterval);
            clearInterval(this.autoSaveInterval);
            
            try {
                const response = await fetch(`/student/exams/${this.attemptToken}/submit`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.isSubmitted = true;
                    alert(data.message);
                    window.location.href = data.redirect_url;
                }
            } catch (error) {
                alert('Failed to submit exam. Please try again.');
            }
        },
        
        formatTime(seconds) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            
            return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        },
        
        getAttemptedCount() {
            return Object.values(this.answers).filter(a => a.selected_option_id && !a.is_marked_for_review).length;
        },
        
        getNotAttemptedCount() {
            return this.questions.length - Object.keys(this.answers).filter(qId => this.answers[qId].selected_option_id).length;
        },
        
        getMarkedCount() {
            return Object.values(this.answers).filter(a => a.is_marked_for_review).length;
        }
    }
}
</script>
@endsection
