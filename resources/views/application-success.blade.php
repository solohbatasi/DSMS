@extends('layouts.app')

@section('title', 'Application Submitted - Pace Driving School')

@section('content')
<style>
    .success-hero {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .success-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        padding: 40px;
        text-align: center;
    }
</style>

<!-- Success Hero -->
<section class="success-hero py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <div class="w-24 h-24 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-check text-4xl"></i>
        </div>
        <h1 class="text-4xl md:text-5xl font-black mb-4">Application Submitted!</h1>
        <p class="text-xl text-green-100">Your driving lesson application has been received successfully.</p>
    </div>
</section>

<!-- Success Details -->
<section class="py-16 bg-gray-50">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="success-card">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-file-alt text-green-600 text-2xl"></i>
            </div>

            <h2 class="text-2xl font-bold text-gray-900 mb-4">Application Details</h2>

            <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-semibold">Application Number:</span>
                        <div class="text-blue-600 font-mono font-bold">{{ $application->application_number }}</div>
                    </div>
                    <div>
                        <span class="font-semibold">Course:</span>
                        <div>{{ $application->course->course }}</div>
                    </div>
                    <div>
                        <span class="font-semibold">Applicant:</span>
                        <div>{{ $application->full_name }}</div>
                    </div>
                    <div>
                        <span class="font-semibold">Preferred Start:</span>
                        <div>{{ $application->preferred_start_date->format('M d, Y') }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <h3 class="font-semibold text-blue-900 mb-3">What Happens Next?</h3>
                <ul class="text-blue-800 text-sm space-y-2 text-left">
                    <li class="flex items-start">
                        <i class="fas fa-phone-alt text-blue-600 mt-1 mr-3"></i>
                        <span>Our team will contact you within 24 hours to confirm your application</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-calendar-check text-blue-600 mt-1 mr-3"></i>
                        <span>We'll schedule your orientation and first lesson</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-file-invoice-dollar text-blue-600 mt-1 mr-3"></i>
                        <span>Payment options and scheduling will be discussed</span>
                    </li>
                </ul>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('pages.courses') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition duration-300">
                    Browse More Courses
                </a>
                <a href="{{ url('/') }}" class="border border-gray-300 text-gray-700 hover:bg-gray-50 px-6 py-3 rounded-lg font-semibold transition duration-300">
                    Back to Home
                </a>
            </div>

            <p class="text-gray-600 mt-6 text-sm">
                Need immediate assistance? Call us at
                <a href="tel:+254728725559" class="text-blue-600 font-semibold">+254 728 725 559</a>
            </p>
        </div>
    </div>
</section>
@endsection
