@extends('layouts.app')

@section('title', 'Professional Driving Courses - Pace Driving School')

@section('content')
@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp

<style>
    /* Add these styles to your existing CSS */
    .pricing-container {
        margin: 20px 0;
        padding: 15px;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .original-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: #ef4444;
        text-decoration: line-through;
        margin-bottom: 5px;
    }

    .discounted-price {
        font-size: 2.5rem;
        font-weight: 900;
        color: #1e40af;
        line-height: 1;
        margin-bottom: 5px;
    }

    .discount-badge {
        display: inline-block;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 800;
        margin-bottom: 10px;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .price-period {
        font-size: 0.9rem;
        color: #64748b;
        font-weight: 500;
    }

    .savings-text {
        font-size: 0.85rem;
        color: #059669;
        font-weight: 600;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .countdown-timer {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border: 1px solid #f59e0b;
        border-radius: 8px;
        padding: 10px;
        margin-top: 15px;
        text-align: center;
    }

    .countdown-text {
        font-size: 0.8rem;
        color: #92400e;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .countdown-numbers {
        font-size: 1.2rem;
        font-weight: 800;
        color: #dc2626;
        letter-spacing: 1px;
    }

    .offer-tag {
        position: absolute;
        top: 15px;
        right: 15px;
        background: linear-gradient(45deg, #ef4444, #f87171);
        color: white;
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 0.75rem;
        font-weight: 800;
        z-index: 5;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        animation: shake 3s infinite;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-2px); }
        20%, 40%, 60%, 80% { transform: translateX(2px); }
    }

    .pricing-comparison {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 10px;
    }
    /* Professional Courses Styles */
    .description-bullets li {
        transition: all 0.3s ease;
    }

    .description-bullets li:hover {
        transform: translateX(5px);
        color: #374151;
    }
    .courses-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        position: relative;
        overflow: hidden;
    }

    .course-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid #f1f5f9;
        position: relative;
        height: 100%;
        display: none; /* Hidden by default */
    }

    .course-card.show {
        display: block;
        animation: fadeInUp 0.6s ease forwards;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .course-card:hover {
        transform: translateY(-12px) scale(1.02);
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
    }

    .course-card.popular::before {
        content: '⭐ MOST POPULAR';
        position: absolute;
        top: 20px;
        right: -35px;
        background: linear-gradient(45deg, #f59e0b, #fbbf24);
        color: white;
        padding: 10px 45px;
        font-size: 0.7rem;
        font-weight: 800;
        transform: rotate(45deg);
        z-index: 10;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
        letter-spacing: 1px;
    }

    .course-image {
        height: 220px;
        background: linear-gradient(135deg, #3b82f6, #1e40af);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .course-image::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 60%;
        background: linear-gradient(transparent, rgba(0,0,0,0.1));
    }

    .course-icon {
        font-size: 4rem;
        color: rgba(255,255,255,0.9);
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
    }

    .course-badge {
        position: absolute;
        top: 20px;
        left: 20px;
        background: rgba(255, 255, 255, 0.95);
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 0.8rem;
        font-weight: 700;
        color: #1e40af;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
    }

    .course-content {
        padding: 30px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .course-price {
        font-size: 2.5rem;
        font-weight: 900;
        color: #1e40af;
        line-height: 1;
        margin-bottom: 10px;
    }

    .course-price-period {
        font-size: 0.9rem;
        color: #64748b;
        font-weight: 500;
    }

    .course-duration {
        display: inline-flex;
        align-items: center;
        background: linear-gradient(135deg, #f8fafc, #e2e8f0);
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 0.9rem;
        color: #475569;
        margin-bottom: 15px;
        border: 1px solid #e2e8f0;
    }

    .feature-list {
        list-style: none;
        padding: 0;
        margin: 0 0 25px 0;
        flex: 1;
    }

    .feature-list li {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
        font-size: 0.9rem;
        color: #475569;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .feature-list li:last-child {
        border-bottom: none;
    }

    .feature-list li::before {
        content: '✓';
        color: #10b981;
        font-weight: bold;
        margin-right: 12px;
        background: #f0fdf4;
        padding: 4px;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
    }

    .enroll-btn {
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
        color: #1e40af;
        padding: 15px 30px;
        border-radius: 15px;
        font-weight: 800;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
        border: none;
        cursor: pointer;
        width: 100%;
    }

    .enroll-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(245, 158, 11, 0.4);
        color: #1e40af;
    }

    .info-btn {
        background: #f8fafc;
        color: #64748b;
        padding: 12px;
        border-radius: 12px;
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .info-btn:hover {
        background: #e2e8f0;
        transform: translateY(-1px);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 25px;
        margin: 50px 0;
    }

    .stat-item {
        text-align: center;
        padding: 30px 20px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        border: 1px solid #f1f5f9;
        transition: transform 0.3s ease;
    }

    .stat-item:hover {
        transform: translateY(-5px);
    }

    .stat-number {
        font-size: 3rem;
        font-weight: 900;
        color: #1e40af;
        line-height: 1;
        margin-bottom: 8px;
    }

    .floating-shape {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.03);
        animation: float 20s infinite linear;
    }

    @keyframes float {
        0% { transform: translate(0, 0) rotate(0deg); }
        25% { transform: translate(15px, 20px) rotate(90deg); }
        50% { transform: translate(0, 40px) rotate(180deg); }
        75% { transform: translate(-15px, 20px) rotate(270deg); }
        100% { transform: translate(0, 0) rotate(360deg); }
    }

    .shape-1 { width: 100px; height: 100px; top: 10%; left: 5%; animation-delay: 0s; }
    .shape-2 { width: 150px; height: 150px; top: 60%; left: 80%; animation-delay: -5s; }
    .shape-3 { width: 80px; height: 80px; top: 80%; left: 10%; animation-delay: -10s; }

    .category-tag {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        margin-right: 8px;
        display: inline-block;
        margin-bottom: 10px;
    }

    .international-badge {
        background: linear-gradient(45deg, #f59e0b, #fbbf24);
        color: white;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        margin-left: 8px;
    }

    .certification-badge {
        display: inline-flex;
        align-items: center;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        margin-left: 8px;
    }

    .course-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .level-indicator {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .level-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #d1d5db;
    }

    .level-dot.active {
        background: #10b981;
    }

    .course-description {
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 20px;
        flex: 1;
    }

    .progress-bar {
        background: #e2e8f0;
        border-radius: 10px;
        height: 6px;
        overflow: hidden;
        margin: 15px 0;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(135deg, #3b82f6, #1e40af);
        border-radius: 10px;
        transition: width 0.3s ease;
    }

    .filter-section {
        background: white;
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        margin-bottom: 40px;
        border: 1px solid #f1f5f9;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        align-items: end;
    }

    .filter-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #374151;
    }

    .filter-select {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        background: white;
        color: #374151;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .filter-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .instruction-box {
        background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
        border: 2px solid #bae6fd;
        border-radius: 16px;
        padding: 25px;
        text-align: center;
        margin: 30px 0;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
    }

    .instruction-box h3 {
        color: #0369a1;
        font-size: 1.5rem;
        margin-bottom: 15px;
    }

    .instruction-box p {
        color: #0c4a6e;
        font-size: 1.1rem;
        margin-bottom: 10px;
    }

    .instruction-icon {
        font-size: 4rem;
        color: #3b82f6;
        margin-bottom: 20px;
        filter: drop-shadow(0 4px 6px rgba(59, 130, 246, 0.2));
    }

    .no-courses-message {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        display: none;
    }

    .no-courses-message.show {
        display: block;
    }

    .no-courses-icon {
        font-size: 5rem;
        color: #e2e8f0;
        margin-bottom: 20px;
    }
</style>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800">
    <div class="max-w-4xl mx-auto text-center text-white px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl md:text-5xl font-black mb-8">Ready to Start Your Driving Journey?</h2>
        <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
            <a href="{{ route('book-lessons') }}"
               class="enroll-btn bg-white text-blue-600 hover:bg-gray-50 text-lg px-12 py-4">
                <i class="fas fa-calendar-check mr-3"></i>
                Book Your Course Now
            </a>
            <a href="tel:+254728725559"
               class="border-2 border-white text-white hover:bg-white hover:text-blue-600 font-bold py-4 px-8 rounded-2xl transition-all duration-300 text-lg">
                <i class="fas fa-phone mr-3"></i>
                Call for Consultation
            </a>
        </div>
        <p class="text-yellow-500 mt-8 text-lg">
             <strong>Quick Enrollment:</strong> +254 728 725 559
        </p>
    </div>
</section>

<!-- Course Filter Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="filter-section">
            <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">Find Courses by Branch</h2>
            <p class="text-blue-600 text-center mb-8">Select a branch to view available courses at that location</p>

            <div class="filter-grid">
                <!-- BRANCH FILTER - Most Important -->
                <div class="filter-group">
                    <label for="branch" class="block text-sm font-medium text-gray-700 mb-2">Select Branch *</label>
                    <select id="branch" class="filter-select" required>
                        <option value="">-- Choose a Branch --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="courseType" class="block text-sm font-medium text-gray-700 mb-2">Course Category</label>
                    <select id="courseType" class="filter-select" disabled>
                        <option value="all">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">Duration</label>
                    <select id="duration" class="filter-select" disabled>
                        <option value="all">Any Duration</option>
                        <option value="short">1-2 Weeks</option>
                        <option value="medium">3-4 Weeks</option>
                        <option value="long">1+ Month</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="skillLevel" class="block text-sm font-medium text-gray-700 mb-2">Skill Level</label>
                    <select id="skillLevel" class="filter-select" disabled>
                        <option value="all">All Levels</option>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>
            </div>


        </div>

        <!-- No Courses Message (Initially Hidden) -->
        <div id="noCoursesMessage" class="no-courses-message">
            <div class="no-courses-icon">
                <i class="fas fa-search"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-700 mb-4">No Courses Found</h3>
            <p class="text-gray-600 mb-6">No courses are available for the selected branch and filters.</p>
            <button id="resetFiltersBtn" class="enroll-btn bg-blue-600 text-white hover:bg-blue-700 max-w-xs mx-auto">
                <i class="fas fa-redo mr-2"></i> Reset Filters
            </button>
        </div>

        <!-- Course Grid (Initially Hidden) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($courses as $course)
            <div class="course-card {{ $loop->index == 1 ? 'popular' : '' }}"
                data-category="{{ $course->category_id ?? 'all' }}"
                data-duration="medium"
                data-branch="{{ $course->branch_id ?? 'all' }}"
                data-price="{{ $course->total_amount <= 20000 ? 'budget' : ($course->total_amount <= 40000 ? 'standard' : 'premium') }}"
                data-level="beginner">

                <div class="course-image">
                    @if($course->image && file_exists(storage_path('app/public/' . $course->image)))
                        <!-- Display actual course image from database -->
                        <img src="{{ asset('storage/' . $course->image) }}"
                            alt="{{ $course->course }}"
                            class="w-full h-full object-cover"
                            onerror="this.onerror=null; this.style.display='none';
                                    this.parentElement.innerHTML = getFallbackIcon('{{ strtolower($course->course) }}');">
                        <div class="course-badge">
                            {{ $course->training_duration }}
                        </div>
                    @else
                        <!-- Fall back to icon if no image exists -->
                        <div class="course-icon">
                            @if(Str::contains(strtolower($course->course), 'motorcycle'))
                                <i class="fas fa-motorcycle"></i>
                            @elseif(Str::contains(strtolower($course->course), 'automatic'))
                                <i class="fas fa-car-side"></i>
                            @elseif(Str::contains(strtolower($course->course), 'manual'))
                                <i class="fas fa-cog"></i>
                            @elseif(Str::contains(strtolower($course->course), 'professional'))
                                <i class="fas fa-truck"></i>
                            @else
                                <i class="fas fa-car"></i>
                            @endif
                        </div>
                        <div class="course-badge">
                            {{ $course->training_duration }}
                        </div>
                    @endif
                </div>

                <div class="course-content">
                    <h2 class="text-2xl font-black text-blue-900 mb-3 leading-tight"> Branch: {{ $course->branch->name ?? ' '}}</h2>
                    <h3 class="text-2xl font-black text-gray-900 mb-3 leading-tight">{{ $course->course }}</h3>

                    <div class="course-description">
                        @php
                            $description = $course->description ?:
                                'Comprehensive driving program designed to build confidence and skills for safe road navigation. ' .
                                'Professional instruction from certified trainers. ' .
                                'Flexible scheduling options available. ' .
                                'Modern training vehicles with dual controls. ' .
                                'Theoretical and practical sessions included.';

                            $sentences = array_filter(array_map('trim',
                                preg_split('/[.!?]+/', $description, -1, PREG_SPLIT_NO_EMPTY)
                            ));
                        @endphp

                        <ul class="space-y-2">
                            @foreach($sentences as $sentence)
                                @if(!empty($sentence))
                                    <li class="flex items-start text-gray-600">
                                        <svg class="w-4 h-4 text-blue-500 mr-2 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-sm">{{ $sentence }}.</span>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>

                    <div class="course-duration">
                        <i class="fas fa-clock mr-2"></i>
                        {{ $course->training_duration }} Days Training
                    </div>
                    <!-- Progress/Sessions Info -->
                    <div class="mb-4">
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span>Practical Sessions: {{ $course->practical_sessions }}</span>
                            <span>Theory Classes: {{ $course->theoretical_sessions }}</span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ (($course->practical_sessions + $course->theoretical_sessions) / 30) * 100 }}%"></div>
                        </div>
                    </div>

                    @if($course->category && Str::contains(strtolower($course->category->name), 'computer'))
                        <!-- Simple Pricing Display - ONLY FOR COMPUTER COURSES -->
                        <div class="mt-6 mb-4 p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl">
                            @php
                                $discounted = $course->total_amount;
                                $markupPercent = $discounted <= 30000 ? 10 : 20;
                                $rawOriginal = $discounted * (1 + ($markupPercent / 100));
                                $original = ceil($rawOriginal / 100) * 100; // round UP to next 100
                            @endphp

                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <div class="text-sm text-gray-500">Original Price</div>
                                    <div class="text-xl font-bold text-red-500 line-through">
                                        Ksh {{ number_format($original) }}
                                    </div>
                                </div>
                                <div class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                                    +{{ $markupPercent }}%
                                </div>
                            </div>

                            <div class="mb-2">
                                <div class="text-sm text-gray-500">Discounted Price</div>
                                <div class="text-xl font-black text-blue-600">
                                    Ksh {{ number_format($discounted) }}
                                </div>
                            </div>
                        </div>
                    @endif


                    <div class="flex space-x-3 mt-auto">
                       <!-- Change this line in the course card -->
                        <a href="{{ route('book-lessons') }}?course={{ $course->id }}&branch={{ $course->branch_id }}"
                        class="enroll-btn">
                            <i class="fas fa-calendar-check mr-2"></i>
                            Enroll Now
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-16 bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-white text-center mb-12">Our Course Statistics</h2>
        <div class="stats-grid">
            <div class="stat-item bg-white/10 backdrop-blur-sm">
                <div class="stat-number text-white">{{ $stats['total_courses'] }}+</div>
                <p class="text-blue-600">Professional Courses</p>
            </div>
            <div class="stat-item bg-white/10 backdrop-blur-sm">
                <div class="stat-number text-white">{{ $stats['total_branches'] }}+</div>
                <p class="text-blue-600">Training Branches</p>
            </div>

            <div class="stat-item bg-white/10 backdrop-blur-sm">
                <div class="stat-number text-white">95%</div>
                <p class="text-blue-600">Success Rate</p>
            </div>
        </div>
    </div>
</section>

<script>
    // Countdown Timer Functionality
function updateCountdownTimers() {
    const countdownElements = document.querySelectorAll('.countdown-numbers');

    countdownElements.forEach(element => {
        const endTime = new Date(element.getAttribute('data-countdown')).getTime();
        const now = new Date().getTime();
        const distance = endTime - now;

        if (distance < 0) {
            element.textContent = "OFFER EXPIRED";
            element.style.color = "#ef4444";
            return;
        }

        // Calculate days, hours, minutes, seconds
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Format as DD : HH : MM : SS
        element.textContent =
            `${days.toString().padStart(2, '0')} : ${hours.toString().padStart(2, '0')} : ${minutes.toString().padStart(2, '0')} : ${seconds.toString().padStart(2, '0')}`;
    });
}

// Initialize countdown timers
document.addEventListener('DOMContentLoaded', function() {
    // Update countdown every second
    setInterval(updateCountdownTimers, 1000);
    updateCountdownTimers();

    // Also add hover effect to pricing containers
    document.querySelectorAll('.pricing-container').forEach(container => {
        container.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.02)';
            this.style.transition = 'transform 0.3s ease';
        });

        container.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    const branchSelect = document.getElementById('branch');
    const courseTypeSelect = document.getElementById('courseType');
    const durationSelect = document.getElementById('duration');
    const skillLevelSelect = document.getElementById('skillLevel');
    const courseCards = document.querySelectorAll('.course-card');
    const noCoursesMessage = document.getElementById('noCoursesMessage');
    const resetFiltersBtn = document.getElementById('resetFiltersBtn');
    const additionalFilters = [courseTypeSelect, durationSelect, skillLevelSelect];

    // Initially hide all course cards
    courseCards.forEach(card => {
        card.style.display = 'none';
    });

    // Enable/Disable additional filters based on branch selection
    function toggleAdditionalFilters() {
        const isBranchSelected = branchSelect.value !== '';

        additionalFilters.forEach(filter => {
            filter.disabled = !isBranchSelected;

            // Visual feedback for disabled state
            if (isBranchSelected) {
                filter.classList.remove('opacity-50', 'cursor-not-allowed');
                filter.style.opacity = '1';
                filter.style.cursor = 'pointer';
            } else {
                filter.classList.add('opacity-50', 'cursor-not-allowed');
                filter.style.opacity = '0.5';
                filter.style.cursor = 'not-allowed';
            }
        });
    }

    // Filter courses based on selections
    function filterCourses() {
        const branchValue = branchSelect.value;
        const typeValue = courseTypeSelect.value;
        const durationValue = durationSelect.value;
        const levelValue = skillLevelSelect.value;

        let visibleCards = 0;

        // If no branch is selected, hide all cards
        if (!branchValue) {
            courseCards.forEach(card => {
                card.classList.remove('show');
                card.style.display = 'none';
            });
            noCoursesMessage.classList.remove('show');
            return;
        }

        // Show/hide cards based on filters
        courseCards.forEach(card => {
            const cardBranch = card.getAttribute('data-branch');
            // In your filterCourses() function, update this part:
            const cardType = card.getAttribute('data-category');
            const cardDuration = card.getAttribute('data-duration');
            const cardLevel = card.getAttribute('data-level');

            // Check if card matches all filters
            const matchesBranch = cardBranch === branchValue;
            const matchesType = typeValue === 'all' || cardType === typeValue;
            const matchesDuration = durationValue === 'all' || cardDuration === durationValue;
            const matchesLevel = levelValue === 'all' || cardLevel === levelValue;

            if (matchesBranch && matchesType && matchesDuration && matchesLevel) {
                card.classList.add('show');
                card.style.display = 'block';
                visibleCards++;
            } else {
                card.classList.remove('show');
                card.style.display = 'none';
            }
        });

        // Show no courses message if no cards are visible
        if (visibleCards === 0) {
            noCoursesMessage.classList.add('show');
        } else {
            noCoursesMessage.classList.remove('show');
        }
    }

    // Reset all filters
    function resetFilters() {
        branchSelect.value = '';
        courseTypeSelect.value = 'all';
        durationSelect.value = 'all';
        skillLevelSelect.value = 'all';

        toggleAdditionalFilters();
        filterCourses();

        // Focus on branch select
        branchSelect.focus();
    }

    // Initialize
    toggleAdditionalFilters();
    filterCourses();

    // Event Listeners
    branchSelect.addEventListener('change', function() {
        toggleAdditionalFilters();
        filterCourses();

        // Reset other filters when branch changes
        if (this.value) {
            courseTypeSelect.value = 'all';
            durationSelect.value = 'all';
            skillLevelSelect.value = 'all';
        }
    });

    additionalFilters.forEach(filter => {
        filter.addEventListener('change', filterCourses);
    });

    resetFiltersBtn.addEventListener('click', resetFilters);

    // Animation on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && entry.target.classList.contains('show')) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0) scale(1)';
            }
        });
    }, observerOptions);

    // Observe course cards for animation
    courseCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px) scale(0.9)';
        card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });

    // Add hover effects
    courseCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            if (this.classList.contains('show')) {
                this.style.zIndex = '10';
            }
        });

        card.addEventListener('mouseleave', function() {
            this.style.zIndex = '1';
        });
    });
});

function showCourseDetails(courseId) {
    // You can implement a modal or redirect to course details page
    alert('Course details for ID: ' + courseId + '\n\nThis would show detailed course information, syllabus, requirements, etc.');
    // Alternatively: window.location.href = '/courses/' + courseId;
}
</script>
@endsection
