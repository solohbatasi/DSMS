<!-- resources/views/feedback/school-services.blade.php -->
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">School Services Feedback</h1>
            <p class="text-lg text-gray-600">Help us improve our facilities and services</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white">Rate Our Services & Facilities</h2>
            </div>

            <form id="servicesFeedbackForm" action="{{ route('feedback.services.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- Service Ratings -->
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Administrative Services -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Administrative Services</h3>

                            <div class="rating-item">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Registration Process</label>
                                @include('components.star-rating', ['name' => 'registration_rating'])
                            </div>

                            <div class="rating-item">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Fee Payment Process</label>
                                @include('components.star-rating', ['name' => 'payment_rating'])
                            </div>

                            <div class="rating-item">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Student Support Services</label>
                                @include('components.star-rating', ['name' => 'support_rating'])
                            </div>
                        </div>

                        <!-- Facilities -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Facilities</h3>

                            <div class="rating-item">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Classroom Facilities</label>
                                @include('components.star-rating', ['name' => 'classroom_rating'])
                            </div>

                            <div class="rating-item">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Library Resources</label>
                                @include('components.star-rating', ['name' => 'library_rating'])
                            </div>

                            <div class="rating-item">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Computer Labs</label>
                                @include('components.star-rating', ['name' => 'computer_lab_rating'])
                            </div>
                        </div>
                    </div>

                    <!-- Additional Services -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Learning Support</h3>

                            <div class="rating-item">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Career Counseling</label>
                                @include('components.star-rating', ['name' => 'counseling_rating'])
                            </div>

                            <div class="rating-item">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tutoring Services</label>
                                @include('components.star-rating', ['name' => 'tutoring_rating'])
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Overall Experience</h3>

                            <div class="rating-item">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Overall Satisfaction</label>
                                @include('components.star-rating', ['name' => 'overall_rating'])
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Feedback -->
                <div class="space-y-4">
                    <div>
                        <label for="positive_experiences" class="block text-sm font-medium text-gray-700 mb-2">What services or facilities impressed you most? *</label>
                        <textarea name="positive_experiences" id="positive_experiences" rows="3" required class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Share your positive experiences..."></textarea>
                    </div>

                    <div>
                        <label for="improvement_areas" class="block text-sm font-medium text-gray-700 mb-2">Which areas need improvement? *</label>
                        <textarea name="improvement_areas" id="improvement_areas" rows="3" required class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="What can we do better?"></textarea>
                    </div>

                    <div>
                        <label for="additional_suggestions" class="block text-sm font-medium text-gray-700 mb-2">Additional Suggestions</label>
                        <textarea name="additional_suggestions" id="additional_suggestions" rows="2" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Any other feedback or suggestions..."></textarea>
                    </div>
                </div>

                <!-- Contact Permission -->
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <label class="flex items-center space-x-3">
                        <input type="checkbox" name="contact_permission" value="1" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                        <span class="text-sm text-gray-700">I'm open to being contacted for follow-up regarding my feedback</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end pt-6 border-t border-gray-200">
                    <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                        Submit Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
