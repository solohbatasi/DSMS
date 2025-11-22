@extends('layouts.app')

@section('title', 'Support - NYAWASCO')

@section('content')
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <!-- Page Header -->
    <section class="text-center mb-16">
        <h1 class="text-4xl md:text-5xl font-bold text-blue-700 mb-4">Customer Support</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            We’re here to assist you. Choose an area of support or contact us directly.
        </p>
    </section>

    <!-- Support Options -->
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-20">

        <!-- Water Supply Support -->
        <a href="#"
           class="block bg-white shadow-md hover:shadow-xl rounded-xl p-8 border border-gray-100 transition">
            <div class="flex items-center mb-4">
                <div class="w-14 h-14 flex items-center justify-center bg-blue-100 text-blue-600 rounded-full">
                    <i class="fas fa-tint text-2xl"></i>
                </div>
                <h3 class="ml-4 text-xl font-semibold text-gray-900">Water Supply Support</h3>
            </div>
            <p class="text-gray-600 leading-relaxed">
                Get help with water billing, water outages, meter issues and service interruptions.
            </p>
        </a>

        <!-- Sewerage Support -->
        <a href="#"
           class="block bg-white shadow-md hover:shadow-xl rounded-xl p-8 border border-gray-100 transition">
            <div class="flex items-center mb-4">
                <div class="w-14 h-14 flex items-center justify-center bg-green-100 text-green-600 rounded-full">
                    <i class="fas fa-water text-2xl"></i>
                </div>
                <h3 class="ml-4 text-xl font-semibold text-gray-900">Sewerage Support</h3>
            </div>
            <p class="text-gray-600 leading-relaxed">
                Report sewer blockages, leaks, overflows, or request sewer line maintenance.
            </p>
        </a>

        <!-- New Water Connection Support -->
        <a href="#"
           class="block bg-white shadow-md hover:shadow-xl rounded-xl p-8 border border-gray-100 transition">
            <div class="flex items-center mb-4">
                <div class="w-14 h-14 flex items-center justify-center bg-purple-100 text-purple-600 rounded-full">
                    <i class="fas fa-file-signature text-2xl"></i>
                </div>
                <h3 class="ml-4 text-xl font-semibold text-gray-900">Water Connection Support</h3>
            </div>
            <p class="text-gray-600 leading-relaxed">
                Need a new water connection? Track your application or request technical assistance.
            </p>
        </a>

        <!-- Billing Support -->
        <a href="#"
           class="block bg-white shadow-md hover:shadow-xl rounded-xl p-8 border border-gray-100 transition">
            <div class="flex items-center mb-4">
                <div class="w-14 h-14 flex items-center justify-center bg-yellow-100 text-yellow-500 rounded-full">
                    <i class="fas fa-money-bill-wave text-2xl"></i>
                </div>
                <h3 class="ml-4 text-xl font-semibold text-gray-900">Billing Support</h3>
            </div>
            <p class="text-gray-600 leading-relaxed">
                Get help with bill clarification, disputes, overcharges, and payment verification.
            </p>
        </a>

        <!-- Meter Support -->
        <a href="#"
           class="block bg-white shadow-md hover:shadow-xl rounded-xl p-8 border border-gray-100 transition">
            <div class="flex items-center mb-4">
                <div class="w-14 h-14 flex items-center justify-center bg-red-100 text-red-600 rounded-full">
                    <i class="fas fa-tachometer-alt text-2xl"></i>
                </div>
                <h3 class="ml-4 text-xl font-semibold text-gray-900">Meter Support</h3>
            </div>
            <p class="text-gray-600 leading-relaxed">
                Report faulty meters, request meter testing, or inquire about meter installation.
            </p>
        </a>

        <!-- Customer Accounts Support -->
        <a href="#"
           class="block bg-white shadow-md hover:shadow-xl rounded-xl p-8 border border-gray-100 transition">
            <div class="flex items-center mb-4">
                <div class="w-14 h-14 flex items-center justify-center bg-orange-100 text-orange-600 rounded-full">
                    <i class="fas fa-user-cog text-2xl"></i>
                </div>
                <h3 class="ml-4 text-xl font-semibold text-gray-900">Account Issues</h3>
            </div>
            <p class="text-gray-600 leading-relaxed">
                Recover your account, update customer details, or request account activation.
            </p>
        </a>
    </section>

    <!-- Full Contact + Support Form (The content you shared) -->
    <section id="contact" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-blue-700 mb-4">Still Need Help?</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Contact our support team for urgent issues, compliments, or complaints.
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
                                <h4 class="font-semibold text-gray-900 mb-1">Address</h4>
                                <p class="text-gray-600">NYAWASCO Headquarters<br>Nyamira, Kenya</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-phone-alt text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Phone Numbers</h4>
                                <p class="text-gray-600">
                                    +254 728 725 559<br>
                                    +254 714 725 559
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-envelope text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Email</h4>
                                <p class="text-gray-600">info@nyawasco.co.ke</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-clock text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Working Hours</h4>
                                <p class="text-gray-600">
                                    Mon - Fri: 8:00 AM - 5:00 PM<br>
                                    Sat: 8:00 AM - 1:00 PM
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div>
                    <h3 class="text-2xl font-semibold text-blue-700 mb-6">Send Us a Message</h3>
                    <form action="#" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                <input type="text" id="name" name="name" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
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
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-lg font-semibold transition">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

</main>
@endsection
