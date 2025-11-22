@extends('layouts.app')

@section('content')

<!-- Hero Section -->
<section class="relative flex items-center justify-center text-center text-white overflow-hidden min-h-[70vh] pt-24">

    <!-- Background Image -->
    <img src="https://tse2.mm.bing.net/th/id/OIP.jTNh35fD02SKGmY58rCNGwHaE8?rs=1&pid=ImgDetMain"
         alt="Sewerage background"
         class="absolute inset-0 w-full h-full object-cover opacity-80 z-0">

    <!-- Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-sky-900/70 to-sky-600/60 z-0"></div>

    <!-- Hero Text -->
    <div class="relative z-10 px-6">
        <h1 class="text-4xl md:text-5xl font-bold mb-3 drop-shadow-lg">
            Sewerage Services Connection
        </h1>
        <p class="text-lg text-sky-100 max-w-2xl mx-auto drop-shadow-md">
            Safe, clean and reliable wastewater disposal for your home, business, or institution.
        </p>
    </div>
</section>

<!-- Content Section -->
<section class="relative bg-gradient-to-b from-sky-50 to-white py-20">

    <!-- Light Swirl Background -->
    <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/wavecut.png')] pointer-events-none"></div>

    <div class="container mx-auto px-6 relative z-10">

        <!-- Info Cards -->
        <div class="grid md:grid-cols-2 gap-10 mb-12">

            <!-- Domestic Connection -->
            <div class="bg-white rounded-2xl shadow-lg p-8 border-t-4 border-sky-600 hover:shadow-2xl transition transform hover:-translate-y-1">
                <h2 class="text-2xl font-semibold text-sky-700 mb-6">Domestic (Individual) Connection Requirements</h2>
                <ul class="space-y-3 text-gray-700">
                    <li class="flex items-start"><i class="fas fa-id-card text-sky-600 mt-1 mr-3"></i> Copy of National ID</li>
                    <li class="flex items-start"><i class="fas fa-file-alt text-sky-600 mt-1 mr-3"></i> Copy of KRA PIN Certificate</li>
                    <li class="flex items-start"><i class="fas fa-file-signature text-sky-600 mt-1 mr-3"></i> Proof of property ownership (Title Deed or landlord introduction letter)</li>
                    <li class="flex items-start"><i class="fas fa-clipboard-check text-sky-600 mt-1 mr-3"></i> Duly filled Sewer Connection Application Form</li>
                </ul>
            </div>

            <!-- Company Connection -->
            <div class="bg-white rounded-2xl shadow-lg p-8 border-t-4 border-blue-600 hover:shadow-2xl transition transform hover:-translate-y-1">
                <h2 class="text-2xl font-semibold text-blue-700 mb-6">Companies, Businesses & Institutions</h2>
                <ul class="space-y-3 text-gray-700">
                    <li class="flex items-start"><i class="fas fa-building text-blue-600 mt-1 mr-3"></i> Certificate of Incorporation</li>
                    <li class="flex items-start"><i class="fas fa-file-alt text-blue-600 mt-1 mr-3"></i> KRA PIN Certificate</li>
                    <li class="flex items-start"><i class="fas fa-drafting-compass text-blue-600 mt-1 mr-3"></i> Approved architectural & drainage plans</li>
                    <li class="flex items-start"><i class="fas fa-file-contract text-blue-600 mt-1 mr-3"></i> Title Deed or lease agreement</li>
                    <li class="flex items-start"><i class="fas fa-id-badge text-blue-600 mt-1 mr-3"></i> CR12 for companies</li>
                </ul>
            </div>

        </div>

        <!-- Estates & Apartments -->
        <div class="bg-white rounded-2xl shadow-lg p-8 border-t-4 border-indigo-600 max-w-3xl mx-auto hover:shadow-2xl transition transform hover:-translate-y-1 mb-12">
            <h2 class="text-2xl font-semibold text-indigo-700 mb-6">Estates, Apartments & Multi-Unit Buildings</h2>
            <ul class="space-y-3 text-gray-700">
                <li class="flex items-start"><i class="fas fa-home text-indigo-600 mt-1 mr-3"></i> Sewer reticulation layout and manhole distribution plan</li>
                <li class="flex items-start"><i class="fas fa-layer-group text-indigo-600 mt-1 mr-3"></i> Number of units and expected discharge load</li>
                <li class="flex items-start"><i class="fas fa-drafting-compass text-indigo-600 mt-1 mr-3"></i> Approved engineering drawings</li>
            </ul>
        </div>

        <!-- Change of Tenancy -->
        <div class="bg-white rounded-2xl shadow-lg p-8 border-t-4 border-teal-600 max-w-3xl mx-auto hover:shadow-2xl transition transform hover:-translate-y-1">
            <h2 class="text-2xl font-semibold text-teal-700 mb-6">Change of Tenancy – Sewer Account Transfer</h2>
            <ul class="space-y-3 text-gray-700">
                <li class="flex items-start"><i class="fas fa-id-card text-teal-600 mt-1 mr-3"></i> National ID</li>
                <li class="flex items-start"><i class="fas fa-file-alt text-teal-600 mt-1 mr-3"></i> KRA PIN Certificate</li>
                <li class="flex items-start"><i class="fas fa-user-check text-teal-600 mt-1 mr-3"></i> Introduction letter from landlord/agent</li>
                <li class="flex items-start"><i class="fas fa-clipboard-check text-teal-600 mt-1 mr-3"></i> Filled tenancy transfer form</li>
            </ul>
        </div>

        <!-- Action Buttons -->
        <div class="relative z-10 flex flex-col md:flex-row justify-center top-10 items-center mt-16 gap-4">
            <a href="{{ route('water-connection.apply') }}"
            class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-sky-700 to-blue-600 text-white rounded-full hover:opacity-90 transition text-lg font-semibold shadow-lg">
                <i class="fas fa-globe mr-2"></i> Apply Sewer Connection Online
            </a>
            <a href="#"
            class="inline-flex items-center justify-center px-8 py-4 bg-white text-sky-700 border border-sky-600 rounded-full hover:bg-sky-50 transition text-lg font-semibold shadow">
                <i class="fas fa-download mr-2"></i> Download Application Form
            </a>
        </div>

    </div>
</section>

@endsection
