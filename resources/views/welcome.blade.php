@extends('layouts.app')

@section('title', 'Pace Driving School - Professional Driving Lessons')

@section('content')
@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;
@endphp

    <style>
        /* Fade-in animation for sections */
        .fade-in-section {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .fade-in-section.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Custom colors for driving school */
        .quick-link-card {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 0.5rem;
            padding: 1.5rem 1rem;
            text-align: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .quick-link-card:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-5px);
            border-color: rgba(255, 255, 255, 0.4);
        }

        .quick-link-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            background-color: rgba(255, 255, 255, 0.2);
        }

        .service-card {
            background-color: white;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            border: 1px solid #f1f5f9;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .news-card {
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            background-color: white;
            border: 1px solid #f1f5f9;
        }

        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-card {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
        }

        /* Professional Section Styles */
        .professional-hero {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        }

        .floating-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.03);
            animation: float 20s infinite linear;
        }

        .shape-1 {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 5%;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 120px;
            height: 120px;
            top: 60%;
            left: 80%;
            animation-delay: -5s;
        }

        .shape-3 {
            width: 60px;
            height: 60px;
            top: 80%;
            left: 10%;
            animation-delay: -10s;
        }

        .shape-4 {
            width: 100px;
            height: 100px;
            top: 20%;
            left: 90%;
            animation-delay: -15s;
        }

        @keyframes float {
            0% {
                transform: translate(0, 0) rotate(0deg);
            }
            25% {
                transform: translate(10px, 15px) rotate(90deg);
            }
            50% {
                transform: translate(0, 30px) rotate(180deg);
            }
            75% {
                transform: translate(-10px, 15px) rotate(270deg);
            }
            100% {
                transform: translate(0, 0) rotate(360deg);
            }
        }

        .professional-title {
            position: relative;
            display: inline-block;
        }

        .professional-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, #f59e0b, #fbbf24);
            border-radius: 2px;
            transform: scaleX(0);
            transform-origin: left;
            animation: titleUnderline 2s ease-in-out 1s forwards;
        }

        @keyframes titleUnderline {
            to {
                transform: scaleX(1);
            }
        }

        .floating-card {
            position: absolute;
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            z-index: 5;
            animation: floatCard 15s infinite ease-in-out;
        }

        .card-1 {
            top: 10%;
            right: 10%;
            animation-delay: 0s;
        }

        .card-2 {
            top: 60%;
            right: -5%;
            animation-delay: -5s;
        }

        .card-3 {
            bottom: 10%;
            left: 5%;
            animation-delay: -10s;
        }

        @keyframes floatCard {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            25% {
                transform: translateY(-10px) rotate(2deg);
            }
            50% {
                transform: translateY(0) rotate(0deg);
            }
            75% {
                transform: translateY(10px) rotate(-2deg);
            }
        }

        .main-visual {
            position: relative;
            z-index: 2;
            border: 8px solid white;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        }

        .scroll-indicator {
            text-align: center;
            color: white;
            font-size: 0.875rem;
            opacity: 0.7;
            animation: fadeInOut 2s infinite;
        }

        @keyframes fadeInOut {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }

        .feature-card {
            text-align: center;
            padding: 2rem 1.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(to right, #3b82f6, #1e40af);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-icon {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-bg {
            position: absolute;
            width: 100%;
            height: 100%;
            background: #dbeafe;
            border-radius: 50%;
            transform: scale(1);
            transition: transform 0.3s ease;
        }

        .feature-card:hover .icon-bg {
            transform: scale(1.1);
        }

        .feature-icon i {
            font-size: 2rem;
            z-index: 2;
            position: relative;
        }
    </style>
    <!-- Professional Hero Section -->
    <section class="professional-hero relative overflow-hidden fade-in-section">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-900 to-blue-700 z-0"></div>

        <!-- Animated Background Elements -->
        <div class="absolute inset-0 z-1">
            <div class="floating-shape shape-1"></div>
            <div class="floating-shape shape-2"></div>
            <div class="floating-shape shape-3"></div>
            <div class="floating-shape shape-4"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="text-white">
                    <div class="inline-flex items-center px-4 py-2 rounded-full bg-white bg-opacity-10 backdrop-blur-sm border border-white border-opacity-20 mb-6">
                        <span class="w-2 h-2 bg-yellow-400 rounded-full mr-2 animate-pulse"></span>
                        <span class="text-sm font-medium">Trusted by 5,000+ Students</span>
                    </div>

                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                        Master The Road With
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-yellow-300 professional-title">
                            Confidence
                        </span>
                    </h1>

                    <p class="text-xl md:text-2xl text-blue-100 mb-8 leading-relaxed">
                        Professional driving education with a 95% first-time pass rate.
                        Your journey to safe, confident driving starts here.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ url('courses') }}"
                        class="bg-yellow-500 hover:bg-yellow-600 text-blue-900 font-bold py-4 px-8 rounded-lg transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex items-center justify-center">
                            <span>Start Your Journey</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                        <a href="#why-choose-us"
                        class="border-2 border-white border-opacity-30 hover:border-opacity-100 text-white font-bold py-4 px-8 rounded-lg transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                            <i class="fas fa-play-circle mr-2"></i>
                            <span>Watch Our Story</span>
                        </a>
                    </div>

                    <!-- Trust Indicators -->
                    <div class="flex flex-wrap gap-6 mt-12 pt-8 border-t border-white border-opacity-20">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-green-500 bg-opacity-20 flex items-center justify-center mr-3">
                                <i class="fas fa-check text-green-400"></i>
                            </div>
                            <div>
                                <div class="font-bold">95% Pass Rate</div>
                                <div class="text-sm text-blue-200">First Time</div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-blue-500 bg-opacity-20 flex items-center justify-center mr-3">
                                <i class="fas fa-award text-blue-300"></i>
                            </div>
                            <div>
                                <div class="font-bold">Certified</div>
                                <div class="text-sm text-blue-200">Instructors</div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-purple-500 bg-opacity-20 flex items-center justify-center mr-3">
                                <i class="fas fa-shield-alt text-purple-300"></i>
                            </div>
                            <div>
                                <div class="font-bold">Insured</div>
                                <div class="text-sm text-blue-200">Vehicles</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Content - Animated Visual -->
                <div class="relative">
                    <div class="relative z-10">
                        <div class="floating-card card-1">
                            <div class="card-content">
                                <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center mb-4">
                                    <i class="fas fa-car text-yellow-600 text-xl"></i>
                                </div>
                                <h3 class="font-bold text-gray-800">Beginner Friendly</h3>
                                <p class="text-sm text-gray-600 mt-2">No experience needed</p>
                            </div>
                        </div>

                        <div class="floating-card card-2">
                            <div class="card-content">
                                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mb-4">
                                    <i class="fas fa-calendar-check text-green-600 text-xl"></i>
                                </div>
                                <h3 class="font-bold text-gray-800">Flexible Scheduling</h3>
                                <p class="text-sm text-gray-600 mt-2">Book anytime</p>
                            </div>
                        </div>

                        <div class="floating-card card-3">
                            <div class="card-content">
                                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mb-4">
                                    <i class="fas fa-graduation-cap text-blue-600 text-xl"></i>
                                </div>
                                <h3 class="font-bold text-gray-800">Certified Instructors</h3>
                                <p class="text-sm text-gray-600 mt-2">Expert guidance</p>
                            </div>
                        </div>

                        <!-- Main Visual -->
                        <div class="main-visual rounded-2xl overflow-hidden shadow-2xl transform hover:scale-105 transition duration-700">
                            <img src="/img/water-hero-1.jpeg" alt="Professional Driving Instruction" class="w-full h-auto">
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-blue-900 to-transparent p-6">
                                <div class="text-white">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full border-2 border-white mr-3 overflow-hidden">
                                            <img src="/img/instructor-1.jpeg" alt="Instructor" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <div class="font-bold">John Smith</div>
                                            <div class="text-sm text-blue-200">Senior Instructor</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-10">
            <div class="scroll-indicator">
                <span>Scroll to explore</span>
                <div class="mt-2 w-6 h-10 border-2 border-white border-opacity-30 rounded-full flex justify-center">
                    <div class="w-1 h-3 bg-white rounded-full mt-2 animate-bounce"></div>
                </div>
            </div>
        </div>
    </section>

     <!-- About Section -->
    <section id="about" class="py-16 bg-gray-50 fade-in-section">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-blue-700 mb-6">About Pace Driving School</h2>
                    <p class="text-lg text-gray-700 mb-6 leading-relaxed">
                        Pace Driving School has been training confident and safe drivers for over a decade.
                        Our certified instructors are committed to providing the highest quality driving education.
                    </p>
                    <p class="text-lg text-gray-700 mb-8 leading-relaxed">
                        We pride ourselves on our personalized approach, ensuring each student receives
                        the attention and guidance they need to become skilled, responsible drivers.
                    </p>

                    <div class="grid grid-cols-2 gap-6 mb-8">
                        <div class="stat-card p-4">
                            <div class="text-2xl font-bold text-blue-700 mb-1">Mission</div>
                            <p class="text-sm text-gray-600">Create confident, safe drivers for life</p>
                        </div>
                        <div class="stat-card p-4">
                            <div class="text-2xl font-bold text-blue-700 mb-1">Approach</div>
                            <p class="text-sm text-gray-600">Patient, personalized instruction</p>
                        </div>
                    </div>

                    <a href="{{ url('about') }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-8 py-3 rounded-lg font-semibold transition duration-300 inline-block transform hover:scale-105">
                        Read More About Us
                    </a>
                </div>

                <div class="relative">
                    <img src="/img/about-driving.jpeg" alt="About Pace Driving School" class="rounded-lg shadow-lg w-full">
                    <div class="absolute -bottom-6 -left-6 bg-yellow-500 text-white p-6 rounded-lg shadow-lg">
                        <div class="text-3xl font-bold mb-2">10+ Years</div>
                        <div class="opacity-90">Of Excellence</div>
                    </div>
                </div>
            </div>
              <!-- CTA Section -->
        <div class="mt-16 bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl p-8 md:p-12 text-center text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white bg-opacity-5 rounded-full -translate-y-32 translate-x-32"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white bg-opacity-5 rounded-full translate-y-24 -translate-x-24"></div>

            <div class="relative z-10 max-w-3xl mx-auto">
                <h2 class="text-2xl md:text-3xl font-bold mb-4">Ready to Start Your Driving Journey?</h2>
                <p class="text-lg text-blue-100 mb-8">
                    Join thousands of successful drivers who started their journey with Pace Driving School
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ url('book-lessons') }}"
                       class="bg-yellow-500 hover:bg-yellow-600 text-blue-900 font-bold py-3 px-8 rounded-lg transition-all duration-300 transform hover:scale-105">
                        Book Your First Lesson
                    </a>
                    <a href="tel:+254700123456"
                       class="border-2 border-white border-opacity-30 hover:border-opacity-100 text-white font-bold py-3 px-8 rounded-lg transition-all duration-300 transform hover:scale-105 flex items-center justify-center">
                        <i class="fas fa-phone mr-2"></i>
                        Call Us Now
                    </a>
                </div>
            </div>
        </div>
        </div>
    </section>

    <!-- Quick Links Section -->
    <section class="quick-links-section py-12 bg-blue-800 fade-in-section">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-8">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Quick Access</h2>
                <p class="text-xl text-blue-100 max-w-2xl mx-auto">
                    Everything you need for your driving journey in one place
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 md:gap-6">
                <!-- Driving Courses -->
                <a href="{{ url('courses') }}" class="quick-link-card group">
                    <div class="quick-link-icon">
                        <i class="fas fa-car text-yellow-400"></i>
                    </div>
                    <div class="quick-link-text">Driving Courses</div>
                </a>

                <!-- Book Lessons -->
                <a href="{{ url('book-lessons') }}" class="quick-link-card group">
                    <div class="quick-link-icon">
                        <i class="fas fa-calendar-check text-yellow-400"></i>
                    </div>
                    <div class="quick-link-text">Book Lessons</div>
                </a>

                <!-- Theory Test -->
                <a href="#" class="quick-link-card group">
                    <div class="quick-link-icon">
                        <i class="fas fa-book text-yellow-400"></i>
                    </div>
                    <div class="quick-link-text">Theory Test</div>
                </a>

                <!-- Practical Test -->
                <a href="#" class="quick-link-card group">
                    <div class="quick-link-icon">
                        <i class="fas fa-road text-yellow-400"></i>
                    </div>
                    <div class="quick-link-text">Practical Test</div>
                </a>

                <!-- Online Payment -->
                <a href="#" class="quick-link-card group">
                    <div class="quick-link-icon">
                        <i class="fas fa-credit-card text-yellow-400"></i>
                    </div>
                    <div class="quick-link-text">Make Payment</div>
                </a>

                <!-- Download Resources -->
                <a href="#" class="quick-link-card group">
                    <div class="quick-link-icon">
                        <i class="fas fa-download text-yellow-400"></i>
                    </div>
                    <div class="quick-link-text">Resources</div>
                </a>

                <!-- Instructor Portal -->
                <a href="#" class="quick-link-card group">
                    <div class="quick-link-icon">
                        <i class="fas fa-chalkboard-teacher text-yellow-400"></i>
                    </div>
                    <div class="quick-link-text">Instructor Portal</div>
                </a>

                <!-- Student Portal -->
                <a href="#" class="quick-link-card group">
                    <div class="quick-link-icon">
                        <i class="fas fa-user-graduate text-yellow-400"></i>
                    </div>
                    <div class="quick-link-text">Student Portal</div>
                </a>

                <!-- FAQ -->
                <a href="#" class="quick-link-card group">
                    <div class="quick-link-icon">
                        <i class="fas fa-question-circle text-yellow-400"></i>
                    </div>
                    <div class="quick-link-text">FAQ</div>
                </a>

                <!-- Testimonials -->
                <a href="#" class="quick-link-card group">
                    <div class="quick-link-icon">
                        <i class="fas fa-star text-yellow-400"></i>
                    </div>
                    <div class="quick-link-text">Testimonials</div>
                </a>

                <!-- Contact -->
                <a href="#contact" class="quick-link-card group">
                    <div class="quick-link-icon">
                        <i class="fas fa-phone-alt text-yellow-400"></i>
                    </div>
                    <div class="quick-link-text">Contact Us</div>
                </a>

                <!-- Locations -->
                <a href="#" class="quick-link-card group">
                    <div class="quick-link-icon">
                        <i class="fas fa-map-marker-alt text-yellow-400"></i>
                    </div>
                    <div class="quick-link-text">Our Locations</div>
                </a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-16 bg-gray-50 fade-in-section">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-blue-700 mb-4">Student Testimonials</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Hear from our successful students about their experience with Pace Driving School
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="news-card">
                    <div class="p-6">
                        <div class="text-sm text-blue-600 font-semibold mb-2">November 15, 2024</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">"Confident Driver Now!"</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            "I was so nervous about learning to drive, but my instructor was incredibly patient and supportive. Passed my test on the first try!"
                        </p>
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                            <div>
                                <div class="font-semibold">Emily R.</div>
                                <div class="text-sm text-gray-500">Beginner Course Graduate</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="news-card">
                    <div class="p-6">
                        <div class="text-sm text-blue-600 font-semibold mb-2">October 28, 2024</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">"Excellent Refresher Course"</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            "After not driving for 10 years, I needed to regain my confidence. The refresher course was perfect - patient instruction and practical lessons."
                        </p>
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                            <div>
                                <div class="font-semibold">David M.</div>
                                <div class="text-sm text-gray-500">Refresher Course Student</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="news-card">
                    <div class="p-6">
                        <div class="text-sm text-blue-600 font-semibold mb-2">October 12, 2024</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">"Commercial License Success"</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            "The commercial license program prepared me thoroughly for my CDL test. The instructors were knowledgeable and professional."
                        </p>
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                            <div>
                                <div class="font-semibold">James K.</div>
                                <div class="text-sm text-gray-500">Commercial License Graduate</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-12">
                <a href="#" class="bg-yellow-500 hover:bg-yellow-600 text-white px-8 py-3 rounded-lg font-semibold transition duration-300 inline-block transform hover:scale-105">
                    View All Testimonials
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16 bg-white fade-in-section">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-blue-700 mb-4">Contact Us</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Get in touch to start your driving journey or ask any questions
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Contact Information -->
                <div>
                    <h3 class="text-2xl font-semibold text-blue-700 mb-6">Get In Touch</h3>

                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-map-marker-alt text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Our Address</h4>
                                <p class="text-gray-600">Pace Driving School Headquarters<br>Nairobi, Kenya</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-phone-alt text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Phone Numbers</h4>
                                <p class="text-gray-600">
                                    +254 700 123 456<br>
                                    +254 711 987 654
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-envelope text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Email Address</h4>
                                <p class="text-gray-600">info@pacedrivingschool.co.ke</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-clock text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Working Hours</h4>
                                <p class="text-gray-600">
                                    Monday - Friday: 7:00 AM - 7:00 PM<br>
                                    Saturday - Sunday: 8:00 AM - 5:00 PM
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div>
                    <h3 class="text-2xl font-semibold text-blue-700 mb-6">Send us a Message</h3>
                    <form action="#" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                <input type="text" id="name" name="name" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                                <input type="email" id="email" name="email" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                            </div>
                        </div>
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                            <input type="text" id="subject" name="subject" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                            <textarea id="message" name="message" rows="5" required
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"></textarea>
                        </div>
                        <button type="submit"
                                class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-3 px-6 rounded-lg font-semibold transition duration-300 transform hover:scale-105">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Emergency Banner -->
    <div class="emergency-banner mt-2 bg-yellow-500 text-white py-3 fade-in-section">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flex flex-col md:flex-row items-center justify-center space-y-2 md:space-y-0 md:space-x-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span class="font-semibold">Urgent Booking:</span>
                </div>
                <div class="flex items-center">
                    <i class="fas fa-phone-alt mr-2"></i>
                    <span>+254 700 123 456 (Last Minute Lessons Available)</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Swiper for hero section
            new Swiper('.hero-swiper', {
                loop: true,
                autoplay: {
                    delay: 6000,
                    disableOnInteraction: false,
                },
                speed: 1000,
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });

            // Fade-in animation for sections
            const fadeInSections = document.querySelectorAll('.fade-in-section');

            const fadeInObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            fadeInSections.forEach(section => {
                fadeInObserver.observe(section);
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize animations for the professional section
            const professionalTitle = document.querySelector('.professional-title');
            const floatingCards = document.querySelectorAll('.floating-card');
            const featureCards = document.querySelectorAll('.feature-card');

            // Add intersection observer for feature cards
            const featureObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });

            featureCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
                featureObserver.observe(card);
            });

            // Add scroll indicator behavior
            const scrollIndicator = document.querySelector('.scroll-indicator');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 100) {
                    scrollIndicator.style.opacity = '0';
                } else {
                    scrollIndicator.style.opacity = '0.7';
                }
            });
        });
    </script>
@endsection
