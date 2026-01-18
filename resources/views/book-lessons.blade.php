@extends('layouts.app')

@section('title', 'Book Driving Lessons - Pace Driving School')

@section('content')
<style>
    .booking-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
    }

    .booking-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .form-section {
        padding: 40px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #374151;
    }

    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 12px center;
        background-repeat: no-repeat;
        background-size: 16px;
        padding-right: 40px;
    }

    .schedule-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
        margin-top: 8px;
    }

    .schedule-option {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
    }

    .schedule-option:hover {
        border-color: #3b82f6;
        background: #f8fafc;
    }

    .schedule-option.selected {
        border-color: #3b82f6;
        background: #dbeafe;
        color: #1e40af;
    }

    .schedule-option input {
        display: none;
    }

    .course-summary {
        background: linear-gradient(135deg, #f8fafc, #e2e8f0);
        padding: 30px;
        border-radius: 16px;
        margin-bottom: 25px;
    }

    .submit-btn {
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
        color: #1e40af;
        padding: 16px 32px;
        border: none;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 700;
        width: 100%;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(245, 158, 11, 0.3);
    }

    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 40px;
        position: relative;
    }

    .step-indicator::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 2px;
        background: #e2e8f0;
        z-index: 1;
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 2;
    }

    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        margin-bottom: 8px;
        transition: all 0.3s ease;
    }

    .step.active .step-number {
        background: #3b82f6;
        color: white;
    }

    .step-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #64748b;
    }

    .step.active .step-label {
        color: #3b82f6;
    }
</style>

<!-- Hero Section -->
<section class="booking-hero py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-black mb-6">
            Book Your
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-yellow-300">
                Driving Lessons
            </span>
        </h1>
        <p class="text-xl md:text-2xl text-blue-100 max-w-3xl mx-auto">
            Start your journey to becoming a confident, safe driver. Fill out the form below and we'll contact you within 24 hours.
        </p>
    </div>
</section>

<!-- Booking Form -->
<section class="py-16 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="booking-card">
            <div class="form-section">
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step active">
                        <div class="step-number">1</div>
                        <div class="step-label">Personal Info</div>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <div class="step-label">Branch & Course</div>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <div class="step-label">Schedule</div>
                    </div>
                    <div class="step">
                        <div class="step-number">4</div>
                        <div class="step-label">Review & Submit</div>
                    </div>
                </div>

                <form action="{{ route('applications.store') }}" method="POST" id="bookingForm">
                    @csrf

                    <!-- Personal Information -->
                    <div class="form-section-step">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">Personal Information</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-group">
                                <label for="full_name" class="form-label">Full Name *</label>
                                <input type="text" id="full_name" name="full_name" required
                                       class="form-input" value="{{ old('full_name') }}"
                                       placeholder="Enter your full name">
                                @error('full_name')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" id="email" name="email" required
                                       class="form-input" value="{{ old('email') }}"
                                       placeholder="your.email@example.com">
                                @error('email')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="phone" class="form-label">Phone Number *</label>
                                <input type="tel" id="phone" name="phone" required
                                       class="form-input" value="{{ old('phone') }}"
                                       placeholder="+254 700 000 000">
                                @error('phone')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="id_number" class="form-label">ID Number</label>
                                <input type="text" id="id_number" name="id_number"
                                       class="form-input" value="{{ old('id_number') }}"
                                       placeholder="Optional - for existing students">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address" class="form-label">Address</label>
                            <textarea id="address" name="address" rows="3"
                                      class="form-input" placeholder="Your residential address">{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <!-- Branch and Course Selection -->
                    <div class="form-section-step mt-12">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">Branch & Course Selection</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Branch Selection -->
                            <div class="form-group">
                                <label for="branch_id" class="form-label">Select Branch *</label>
                                <select id="branch_id" name="branch_id" required class="form-input form-select">
                                    <option value="">Choose a branch...</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                                {{ (old('branch_id') == $branch->id || $selectedBranch == $branch->id) ? 'selected' : '' }}>
                                            {{ $branch->name }} - {{ $branch->location }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Course Selection -->
                            <div class="form-group">
                                <label for="course_id" class="form-label">Select Course *</label>
                                <select id="course_id" name="course_id" required class="form-input form-select">
                                    <option value="">Select branch first...</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}"
                                                {{ (old('course_id') == $course->id || $selectedCourse == $course->id) ? 'selected' : '' }}
                                                data-price="{{ $course->total_amount }}"
                                                data-duration="{{ $course->training_duration }}"
                                                data-sessions="{{ $course->practical_sessions + $course->theoretical_sessions }}"
                                                data-branch-id="{{ $course->branch_id }}"
                                                data-branch-name="{{ $course->branch ? $course->branch->name : 'N/A' }}">
                                            {{ $course->course }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Course Summary -->
                        <div id="courseSummary" class="course-summary" style="display: none;">
                            <h4 class="font-bold text-lg text-gray-900 mb-3">Course Summary</h4>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="font-semibold">Branch:</span>
                                    <span id="summaryBranch">-</span>
                                </div>
                                <div>
                                    <span class="font-semibold">Duration:</span>
                                    <span id="summaryDuration">-</span>
                                </div>
                                <div>
                                    <span class="font-semibold">Total Sessions:</span>
                                    <span id="summarySessions">-</span>
                                </div>
                                <div>
                                    <span class="font-semibold">Total Fee:</span>
                                    <span id="summaryFee" class="font-bold text-blue-600">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Preferences -->
                    <div class="form-section-step mt-12">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">Schedule Preferences</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-group">
                                <label for="preferred_start_date" class="form-label">Preferred Start Date *</label>
                                <input type="date" id="preferred_start_date" name="preferred_start_date" required
                                       class="form-input" value="{{ old('preferred_start_date') }}"
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                @error('preferred_start_date')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="form-label">Preferred Schedule *</label>
                                <div class="schedule-options">
                                    @php
                                        $schedules = [
                                            'weekday_morning' => 'Weekday Morning',
                                            'weekday_afternoon' => 'Weekday Afternoon',
                                            'weekday_evening' => 'Weekday Evening',
                                            'weekend_morning' => 'Weekend Morning',
                                            'weekend_afternoon' => 'Weekend Afternoon',
                                            'flexible' => 'Flexible'
                                        ];
                                    @endphp
                                    @foreach($schedules as $value => $label)
                                        <label class="schedule-option {{ old('preferred_schedule') == $value ? 'selected' : '' }}">
                                            <input type="radio" name="preferred_schedule" value="{{ $value }}"
                                                   {{ old('preferred_schedule') == $value ? 'checked' : '' }} required>
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                                @error('preferred_schedule')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="special_requirements" class="form-label">Special Requirements</label>
                            <textarea id="special_requirements" name="special_requirements" rows="3"
                                      class="form-input" placeholder="Any special requirements or notes...">{{ old('special_requirements') }}</textarea>
                        </div>
                    </div>

                    <!-- Terms and Submit -->
                    <div class="form-section-step mt-12">
                        <div class="form-group">
                            <label class="flex items-start">
                                <input type="checkbox" name="terms_accepted" required
                                       class="mt-1 mr-3" {{ old('terms_accepted') ? 'checked' : '' }}>
                                <span class="text-sm text-gray-600">
                                    I agree to the <a href="#" class="text-blue-600 hover:underline">Terms and Conditions</a>
                                    and <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>.
                                    I understand that submitting this form doesn't guarantee enrollment and that
                                    a representative will contact me to confirm details.
                                </span>
                            </label>
                            @error('terms_accepted')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="submit-btn">
                            <i class="fas fa-paper-plane mr-3"></i>
                            Submit Application
                        </button>

                        <p class="text-center text-gray-600 mt-4 text-sm">
                            We'll contact you within 24 hours to confirm your booking and discuss next steps.
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get branch and course selects
    const branchSelect = document.getElementById('branch_id');
    const courseSelect = document.getElementById('course_id');
    const courseSummary = document.getElementById('courseSummary');
    const summaryBranch = document.getElementById('summaryBranch');
    const summaryDuration = document.getElementById('summaryDuration');
    const summarySessions = document.getElementById('summarySessions');
    const summaryFee = document.getElementById('summaryFee');

    // Store all courses data from HTML data attributes
    function getAllCoursesData() {
        const courses = [];
        const courseOptions = document.querySelectorAll('#course_id option[value]');

        courseOptions.forEach(option => {
            if (option.value) {
                courses.push({
                    id: option.value,
                    name: option.textContent,
                    price: option.dataset.price || '0',
                    duration: option.dataset.duration || '0',
                    sessions: option.dataset.sessions || '0',
                    branch_id: option.dataset.branchId || '',
                    branch_name: option.dataset.branchName || 'N/A'
                });
            }
        });

        return courses;
    }

    // Function to populate courses based on selected branch
    function populateCourses(branchId) {
        const allCourses = getAllCoursesData();

        // Clear current options
        courseSelect.innerHTML = '<option value="">Select a course...</option>';

        // Filter courses by branch
        const filteredCourses = allCourses.filter(course => {
            return branchId === '' || course.branch_id == branchId;
        });

        // Add filtered courses
        filteredCourses.forEach(course => {
            const option = document.createElement('option');
            option.value = course.id;
            option.textContent = course.name;
            option.dataset.price = course.price;
            option.dataset.duration = course.duration;
            option.dataset.sessions = course.sessions;
            option.dataset.branchId = course.branch_id;
            option.dataset.branchName = course.branch_name;
            courseSelect.appendChild(option);
        });

        // If there's a pre-selected course from query parameter, select it
        const selectedCourseId = "{{ $selectedCourse }}";
        if (selectedCourseId && courseSelect.querySelector(`option[value="${selectedCourseId}"]`)) {
            courseSelect.value = selectedCourseId;
            setTimeout(() => {
                courseSelect.dispatchEvent(new Event('change'));
            }, 100);
        }
    }

    // Branch selection change handler
    branchSelect.addEventListener('change', function() {
        populateCourses(this.value);
        // Clear course summary when branch changes
        courseSummary.style.display = 'none';
        courseSelect.value = '';
    });

    // Course selection change handler
    courseSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];

        if (selectedOption && selectedOption.value) {
            const price = selectedOption.dataset.price || '0';
            const duration = selectedOption.dataset.duration || '0';
            const sessions = selectedOption.dataset.sessions || '0';
            const branchName = selectedOption.dataset.branchName || 'N/A';

            summaryBranch.textContent = branchName;
            summaryDuration.textContent = duration + ' days';
            summarySessions.textContent = sessions + ' sessions';
            summaryFee.textContent = 'KES ' + Number(price).toLocaleString();

            courseSummary.style.display = 'block';
        } else {
            courseSummary.style.display = 'none';
        }
    });

    // Initialize with selected branch if any
    const selectedBranch = "{{ $selectedBranch }}";
    if (selectedBranch) {
        branchSelect.value = selectedBranch;
        setTimeout(() => {
            branchSelect.dispatchEvent(new Event('change'));
        }, 100);
    }

    // Schedule option selection
    const scheduleOptions = document.querySelectorAll('.schedule-option');
    scheduleOptions.forEach(option => {
        option.addEventListener('click', function() {
            scheduleOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input').checked = true;
        });
    });

    // Set minimum date for start date (tomorrow)
    const startDateInput = document.getElementById('preferred_start_date');
    if (startDateInput) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        startDateInput.min = tomorrow.toISOString().split('T')[0];
    }

    // Form validation
    const form = document.getElementById('bookingForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let valid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.style.borderColor = '#ef4444';
                } else {
                    field.style.borderColor = '#e2e8f0';
                }
            });

            // Special validation for branch-course combination
            const branchId = branchSelect.value;
            const courseBranchId = courseSelect.options[courseSelect.selectedIndex]?.dataset.branchId;

            if (branchId && courseBranchId && branchId != courseBranchId) {
                alert('The selected course is not available at the selected branch. Please choose a different course or branch.');
                e.preventDefault();
                return;
            }

            if (!valid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    }

    // If course is pre-selected, update summary
    const selectedCourseId = "{{ $selectedCourse }}";
    if (selectedCourseId && courseSelect.value === selectedCourseId) {
        setTimeout(() => {
            courseSelect.dispatchEvent(new Event('change'));
        }, 200);
    }
});
</script>
@endsection
