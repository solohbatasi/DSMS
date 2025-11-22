@extends('layouts.app')

@section('content')

<!-- Hero Section -->
<section class="relative flex items-center justify-center text-center text-white overflow-hidden min-h-[70vh] pt-24">

    <img src="https://tse3.mm.bing.net/th/id/OIP.eWHTGgk2NO9qeK8W2O8fbgHaE8?rs=1&pid=ImgDetMain"
         class="absolute inset-0 w-full h-full object-cover opacity-80 z-0">

    <div class="absolute inset-0 bg-gradient-to-b from-sky-900/70 to-sky-600/60 z-0"></div>

    <div class="relative z-10 px-6">
        <h1 class="text-4xl md:text-5xl font-bold mb-3 drop-shadow-lg">
            Water Supply Services
        </h1>
        <p class="text-lg text-sky-100 max-w-2xl mx-auto drop-shadow-md">
            Reliable, clean and safe water for homes, businesses, and institutions.
        </p>
    </div>
</section>

<!-- Page Content -->
<section class="relative bg-gradient-to-b from-sky-50 to-white py-20">

    <!-- Background texture -->
    <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/wavecut.png')] pointer-events-none"></div>

    <div class="container mx-auto px-6 relative z-10">

        <!-- Overview Section -->
        <div class="max-w-4xl mx-auto text-center mb-16">
            <h2 class="text-3xl font-bold text-sky-700 mb-4">Our Water Supply Services</h2>
            <p class="text-gray-700 text-lg">
                We ensure delivery of high-quality, treated, and reliable water to all consumers.
                Below is an overview of our supply services and how you can get connected.
            </p>
        </div>

        <!-- Cards Grid -->
        <div class="grid md:grid-cols-3 gap-10 mb-16">

            <!-- Domestic Supply -->
            <div class="bg-white rounded-2xl shadow-lg p-8 border-t-4 border-sky-600 hover:shadow-2xl transition transform hover:-translate-y-1">
                <h3 class="text-2xl font-semibold text-sky-700 mb-4">Domestic Supply</h3>
                <p class="text-gray-700 mb-4">
                    Clean and safe treated water for individual households and residential units.
                </p>
                <a href="{{ route('water-connection') }}"
                   class="text-sky-700 font-semibold hover:underline">
                   Learn More →
                </a>
            </div>

            <!-- Commercial Supply -->
            <div class="bg-white rounded-2xl shadow-lg p-8 border-t-4 border-blue-600 hover:shadow-2xl transition transform hover:-translate-y-1">
                <h3 class="text-2xl font-semibold text-blue-700 mb-4">Commercial & Industrial Supply</h3>
                <p class="text-gray-700 mb-4">
                    Dedicated water supply for businesses, industries, hotels, and institutions.
                </p>
                <a href="{{ route('water-connection') }}"
                   class="text-blue-700 font-semibold hover:underline">
                   Learn More →
                </a>
            </div>

            <!-- Bulk & Tanker Water -->
            <div class="bg-white rounded-2xl shadow-lg p-8 border-t-4 border-teal-600 hover:shadow-2xl transition transform hover:-translate-y-1">
                <h3 class="text-2xl font-semibold text-teal-700 mb-4">Bulk & Tanker Water</h3>
                <p class="text-gray-700 mb-4">
                    Large-volume water supply for construction, events, and emergency purposes.
                </p>
                <a href="#"
                   class="text-teal-700 font-semibold hover:underline">
                   Request Service →
                </a>
            </div>

        </div>

        <!-- Links to Other Service Pages -->
        <div class="max-w-3xl mx-auto text-center mt-20">
            <h3 class="text-2xl font-bold text-sky-700 mb-6">Explore More Services</h3>

            <div class="flex flex-col md:flex-row justify-center gap-6">

                <a href="{{ route('water-connection') }}"
                   class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-sky-700 to-blue-600 text-white rounded-full hover:opacity-90 transition text-lg font-semibold shadow-lg">
                    <i class="fas fa-tint mr-2"></i> Water Connection
                </a>

                <a href="{{ route('sewer-connection') }}"
                   class="inline-flex items-center justify-center px-8 py-4 bg-white text-sky-700 border border-sky-600 rounded-full hover:bg-sky-50 transition text-lg font-semibold shadow">
                    <i class="fas fa-water mr-2"></i> Sewerage Services
                </a>

            </div>
        </div>

    </div>

</section>

@endsection
