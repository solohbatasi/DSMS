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
</style>

    <!-- Hero Section with Slideshow -->
    <section class="relative">
        <div class="swiper hero-swiper">
            <div class="swiper-wrapper">
                <!-- Slide 1 -->
                <div class="swiper-slide relative">
                    <div class="absolute inset-0 bg-blue-900 bg-opacity-50 z-10"></div>
                    <img src="/img/driving-hero-1.jpeg" alt="Pace Driving School" class="w-full h-full object-cover">
                    <div class="absolute inset-0 z-20 flex items-center justify-center text-center px-4">
                        <div class="text-white max-w-4xl">
                            <h2 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4">Welcome to Pace Driving School</h2>
                            <p class="text-lg md:text-xl lg:text-2xl mb-6 md:mb-8 opacity-90">
                                Your journey to safe and confident driving starts here
                            </p>
                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <a href="#courses"
                                   class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 md:px-8 md:py-4 rounded-lg font-semibold text-base md:text-lg transition duration-300 transform hover:scale-105 inline-block text-center">
                                    View Courses
                                </a>
                                <a href="#services"
                                   class="border-2 border-white text-white hover:bg-white hover:text-blue-700 px-6 py-3 md:px-8 md:py-4 rounded-lg font-semibold text-base md:text-lg transition duration-300 transform hover:scale-105 inline-block text-center">
                                    Our Services
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="swiper-slide relative">
                    <div class="absolute inset-0 bg-black bg-opacity-50 z-10"></div>
                    <img src="/img/driving-hero-2.jpeg" alt="Professional Driving Lessons" class="w-full h-full object-cover">
                    <div class="absolute inset-0 z-20 flex items-center justify-center text-center px-4">
                        <div class="text-white max-w-4xl">
                            <h2 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4">Expert Driving Instructors</h2>
                            <p class="text-lg md:text-xl lg:text-2xl mb-6 md:mb-8 opacity-90">
                                Learn from certified professionals with years of experience
                            </p>
                            <a href="#instructors"
                               class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 md:px-8 md:py-4 rounded-lg font-semibold text-base md:text-lg transition duration-300 transform hover:scale-105 inline-block text-center">
                                Meet Our Team
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="swiper-slide relative">
                    <div class="absolute inset-0 bg-black bg-opacity-50 z-10"></div>
                    <img src="/img/driving-hero-3.jpeg" alt="Driving Test Preparation" class="w-full h-full object-cover">
                    <div class="absolute inset-0 z-20 flex items-center justify-center text-center px-4">
                        <div class="text-white max-w-4xl">
                            <h2 class="text-3xl md:text-5xl lg:text-6xl font-bold mb-4">High Success Rate</h2>
                            <p class="text-lg md:text-xl lg:text-2xl mb-6 md:mb-8 opacity-90">
                                95% first-time pass rate on driving tests
                            </p>
                            <a href="#contact"
                               class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 md:px-8 md:py-4 rounded-lg font-semibold text-base md:text-lg transition duration-300 transform hover:scale-105 inline-block text-center">
                                Book Lessons
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="swiper-pagination !bottom-4"></div>
            <div class="swiper-button-prev !w-10 !h-10 md:!w-12 md:!h-12 !left-2 bg-white bg-opacity-20 rounded-full shadow-lg"></div>
            <div class="swiper-button-next !w-10 !h-10 md:!w-12 md:!h-12 !right-2 bg-white bg-opacity-20 rounded-full shadow-lg"></div>
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

    <!-- Quick Stats Section -->
    <section class="py-8 bg-blue-50 fade-in-section">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8">
                <div class="text-center">
                    <div class="text-2xl md:text-3xl font-bold text-blue-700 mb-1">5,000+</div>
                    <div class="text-sm md:text-base text-gray-600">Students Trained</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl md:text-3xl font-bold text-blue-700 mb-1">95%</div>
                    <div class="text-sm md:text-base text-gray-600">First-Time Pass Rate</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl md:text-3xl font-bold text-blue-700 mb-1">15+</div>
                    <div class="text-sm md:text-base text-gray-600">Certified Instructors</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl md:text-3xl font-bold text-blue-700 mb-1">10+</div>
                    <div class="text-sm md:text-base text-gray-600">Years Experience</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Courses Section -->
    <section id="courses" class="py-16 bg-white fade-in-section">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-blue-700 mb-4">Our Driving Courses</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Comprehensive driving programs tailored to your needs and experience level
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
                <!-- Course 1 -->
                <div class="service-card group">
                    <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-yellow-100 transition duration-300">
                        <i class="fas fa-user text-blue-600 text-2xl group-hover:text-yellow-600 transition duration-300"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-blue-700 mb-3">Beginner Course</h3>
                    <p class="text-gray-600 text-sm">
                        Perfect for first-time drivers with no previous experience
                    </p>
                    <a href="#" class="inline-block mt-4 text-blue-600 hover:text-yellow-600 font-medium text-sm transition duration-300">
                        Learn More →
                    </a>
                </div>

                <!-- Course 2 -->
                <div class="service-card group">
                    <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-yellow-100 transition duration-300">
                        <i class="fas fa-sync-alt text-blue-600 text-2xl group-hover:text-yellow-600 transition duration-300"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-blue-700 mb-3">Refresher Course</h3>
                    <p class="text-gray-600 text-sm">
                        Regain confidence and update your driving skills
                    </p>
                    <a href="#" class="inline-block mt-4 text-blue-600 hover:text-yellow-600 font-medium text-sm transition duration-300">
                        Learn More →
                    </a>
                </div>

                <!-- Course 3 -->
                <div class="service-card group">
                    <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-yellow-100 transition duration-300">
                        <i class="fas fa-truck text-blue-600 text-2xl group-hover:text-yellow-600 transition duration-300"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-blue-700 mb-3">Commercial License</h3>
                    <p class="text-gray-600 text-sm">
                        Training for commercial vehicle licenses and endorsements
                    </p>
                    <a href="#" class="inline-block mt-4 text-blue-600 hover:text-yellow-600 font-medium text-sm transition duration-300">
                        Learn More →
                    </a>
                </div>

                <!-- Course 4 -->
                <div class="service-card group">
                    <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center group-hover:bg-yellow-100 transition duration-300">
                        <i class="fas fa-motorcycle text-blue-600 text-2xl group-hover:text-yellow-600 transition duration-300"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-blue-700 mb-3">Motorcycle Training</h3>
                    <p class="text-gray-600 text-sm">
                        Specialized training for motorcycle license and safety
                    </p>
                    <a href="#" class="inline-block mt-4 text-blue-600 hover:text-yellow-600 font-medium text-sm transition duration-300">
                        Learn More →
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Online Services Section -->
    <section class="py-16 bg-blue-600 text-white fade-in-section">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Online Services</h2>
                <p class="text-lg opacity-90 max-w-3xl mx-auto">
                    Manage your driving education conveniently from anywhere
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <div class="text-center p-6">
                    <div class="w-20 h-20 mx-auto mb-4 bg-white bg-opacity-20 rounded-full flex items-center justify-center hover:bg-yellow-500 hover:bg-opacity-30 transition duration-300">
                        <i class="fas fa-calendar-plus text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Book Lessons</h3>
                    <p class="opacity-90 text-sm mb-4">
                        Schedule your driving lessons online
                    </p>
                    <a href="{{ url('book-lessons') }}" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg font-semibold transition duration-300 transform hover:scale-105">
                        Book Now
                    </a>
                </div>

                <div class="text-center p-6">
                    <div class="w-20 h-20 mx-auto mb-4 bg-white bg-opacity-20 rounded-full flex items-center justify-center hover:bg-yellow-500 hover:bg-opacity-30 transition duration-300">
                        <i class="fas fa-file-invoice-dollar text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Make Payment</h3>
                    <p class="opacity-90 text-sm mb-4">
                        Pay for courses and lessons securely online
                    </p>
                    <a href="#" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg font-semibold transition duration-300 transform hover:scale-105">
                        Pay Now
                    </a>
                </div>

                <div class="text-center p-6">
                    <div class="w-20 h-20 mx-auto mb-4 bg-white bg-opacity-20 rounded-full flex items-center justify-center hover:bg-yellow-500 hover:bg-opacity-30 transition duration-300">
                        <i class="fas fa-headset text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Student Support</h3>
                    <p class="opacity-90 text-sm mb-4">
                        Get help with your driving education journey
                    </p>
                    <a href="{{url('support')}}" class="inline-block bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg font-semibold transition duration-300 transform hover:scale-105">
                        Get Help
                    </a>
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
        </div>
    </section>

    <!-- Instructors Section -->
    <section id="instructors" class="py-16 bg-white fade-in-section">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-blue-700 mb-4">Meet Our Instructors</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Learn from our team of certified, experienced driving instructors
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Instructor 1 -->
                <div class="news-card group">
                    <div class="overflow-hidden">
                        <img src="/img/instructor-1.jpeg" alt="John Smith" class="w-full h-48 object-cover group-hover:scale-105 transition duration-500">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-blue-700 mb-2">John Smith</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Senior Instructor with 8 years experience specializing in beginner drivers
                        </p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span>Certified: 2015</span>
                            <span>Students: 450+</span>
                        </div>
                    </div>
                </div>

                <!-- Instructor 2 -->
                <div class="news-card group">
                    <div class="overflow-hidden">
                        <img src="/img/instructor-2.jpeg" alt="Sarah Johnson" class="w-full h-48 object-cover group-hover:scale-105 transition duration-500">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-blue-700 mb-2">Sarah Johnson</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Expert in defensive driving and commercial license training
                        </p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span>Certified: 2017</span>
                            <span>Students: 380+</span>
                        </div>
                    </div>
                </div>

                <!-- Instructor 3 -->
                <div class="news-card group">
                    <div class="overflow-hidden">
                        <img src="/img/instructor-3.jpeg" alt="Mike Davis" class="w-full h-48 object-cover group-hover:scale-105 transition duration-500">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-blue-700 mb-2">Mike Davis</h3>
                        <p class="text-gray-600 text-sm mb-4">
                            Motorcycle specialist and advanced driving techniques instructor
                        </p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span>Certified: 2016</span>
                            <span>Students: 320+</span>
                        </div>
                    </div>
                </div>
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
@endsection
