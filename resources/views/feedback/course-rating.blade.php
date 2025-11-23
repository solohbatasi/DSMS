<!-- resources/views/feedback/course-rating.blade.php -->
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Course & Instructor Feedback</h1>
            <p class="text-lg text-gray-600">Help us improve our teaching quality and course content</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white">Rate Your Learning Experience</h2>
            </div>

            <form id="courseRatingForm" action="{{ route('feedback.course-rating.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- Course Selection -->
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <label class="block text-sm font-semibold text-gray-900 mb-3">Course Information</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Select Course *</label>
                            <select name="course_id" id="course_id" required class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Choose a course...</option>
                                @foreach($enrolled_courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }} - {{ $course->code }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="instructor_id" class="block text-sm font-medium text-gray-700 mb-2">Instructor *</label>
                            <select name="instructor_id" id="instructor_id" required class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select instructor...</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Overall Ratings -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-4">Overall Ratings (1-5 Stars)</label>

                        <!-- Instructor Effectiveness -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Instructor's Teaching Effectiveness</span>
                                <span id="instructor_effectiveness_rating_text" class="text-sm text-gray-500">0/5</span>
                            </div>
                            <div class="flex space-x-1" id="instructor_effectiveness_rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" class="rating-star text-2xl" data-rating="{{ $i }}" data-target="instructor_effectiveness">
                                        <span class="text-gray-300 hover:text-yellow-400">★</span>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="instructor_effectiveness" id="instructor_effectiveness" value="0">
                        </div>

                        <!-- Course Content -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Course Content Quality</span>
                                <span id="content_quality_rating_text" class="text-sm text-gray-500">0/5</span>
                            </div>
                            <div class="flex space-x-1" id="content_quality_rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" class="rating-star text-2xl" data-rating="{{ $i }}" data-target="content_quality">
                                        <span class="text-gray-300 hover:text-yellow-400">★</span>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="content_quality" id="content_quality" value="0">
                        </div>

                        <!-- Learning Resources -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Learning Resources & Materials</span>
                                <span id="resources_rating_text" class="text-sm text-gray-500">0/5</span>
                            </div>
                            <div class="flex space-x-1" id="resources_rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" class="rating-star text-2xl" data-rating="{{ $i }}" data-target="resources">
                                        <span class="text-gray-300 hover:text-yellow-400">★</span>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="resources_quality" id="resources_quality" value="0">
                        </div>
                    </div>
                </div>

                <!-- Detailed Feedback -->
                <div class="space-y-4">
                    <div>
                        <label for="what_you_liked" class="block text-sm font-medium text-gray-700 mb-2">What did you like most about this course? *</label>
                        <textarea name="what_you_liked" id="what_you_liked" rows="3" required class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Share what worked well for you..."></textarea>
                    </div>

                    <div>
                        <label for="improvement_suggestions" class="block text-sm font-medium text-gray-700 mb-2">Suggestions for Improvement</label>
                        <textarea name="improvement_suggestions" id="improvement_suggestions" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="What could be improved?"></textarea>
                    </div>

                    <div>
                        <label for="instructor_feedback" class="block text-sm font-medium text-gray-700 mb-2">Specific Feedback for Instructor</label>
                        <textarea name="instructor_feedback" id="instructor_feedback" rows="3" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Any specific feedback for the instructor..."></textarea>
                    </div>
                </div>

                <!-- Recommendation -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Would you recommend this course to other students?</label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="would_recommend" value="1" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Yes, definitely</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="would_recommend" value="0" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Probably not</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="would_recommend" value="2" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Not sure</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end pt-6 border-t border-gray-200">
                    <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Submit Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Star Rating System
    document.querySelectorAll('.rating-star').forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            const target = this.getAttribute('data-target');
            const container = this.parentElement;
            const hiddenInput = document.getElementById(target);
            const ratingText = document.getElementById(target + '_rating_text');

            // Update stars
            container.querySelectorAll('.rating-star').forEach((s, index) => {
                const starSpan = s.querySelector('span');
                if (index < rating) {
                    starSpan.classList.remove('text-gray-300', 'hover:text-yellow-400');
                    starSpan.classList.add('text-yellow-400');
                } else {
                    starSpan.classList.remove('text-yellow-400');
                    starSpan.classList.add('text-gray-300', 'hover:text-yellow-400');
                }
            });

            // Update hidden input and text
            hiddenInput.value = rating;
            ratingText.textContent = rating + '/5';
        });
    });

    // Dynamic instructor loading based on course selection
    const courseSelect = document.getElementById('course_id');
    const instructorSelect = document.getElementById('instructor_id');

    courseSelect.addEventListener('change', function() {
        const courseId = this.value;
        instructorSelect.innerHTML = '<option value="">Select instructor...</option>';

        if (courseId) {
            fetch(`/api/courses/${courseId}/instructors`)
                .then(response => response.json())
                .then(data => {
                    data.instructors.forEach(instructor => {
                        const option = document.createElement('option');
                        option.value = instructor.id;
                        option.textContent = instructor.name;
                        instructorSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error loading instructors:', error));
        }
    });
});
</script>
@endsection
