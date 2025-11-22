@extends('layouts.app')

@section('content')

<!-- Hero Section -->
<section class="relative flex items-center justify-center text-center text-white overflow-hidden min-h-[70vh] pt-24">
    <!-- Background Image: Natural river water -->
    <img src="https://tse2.mm.bing.net/th/id/OIP.AeIKKhbEb1OdsgULPfYI0gHaFj?cb=ucfimgc2&rs=1&pid=ImgDetMain&o=7&rm=3"
         alt="Natural river water background"
         class="absolute inset-0 w-full h-full object-cover opacity-80 z-0">
    
    <!-- Blue Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-sky-900/70 to-sky-600/60 z-0"></div>

    <!-- Hero Text -->
    <div class="relative z-10 px-6">
        <h1 class="text-4xl md:text-5xl font-bold mb-3 drop-shadow-lg">
            New Water Connection
        </h1>
        <p class="text-lg text-sky-100 max-w-2xl mx-auto drop-shadow-md">
            Seamlessly connect to clean, safe, and reliable water supply today.
        </p>
    </div>
</section>


<!-- Page Content -->
<section class="relative bg-gradient-to-b from-sky-50 to-white py-20">
    <!-- Light Water Texture -->
    <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/wavecut.png')] pointer-events-none"></div>

    <div class="container mx-auto px-6 relative z-10">
        <!-- Info Cards -->
        <div class="grid md:grid-cols-2 gap-10 mb-12">
            
            <!-- Individual Requirements -->
            <div class="bg-white rounded-2xl shadow-lg p-8 border-t-4 border-sky-600 hover:shadow-2xl transition transform hover:-translate-y-1">
                <h2 class="text-2xl font-semibold text-sky-700 mb-6">Requirements for Individuals</h2>
                <ul class="space-y-3 text-gray-700">
                    <li class="flex items-start"><i class="fas fa-id-card text-sky-600 mt-1 mr-3"></i> Copy of National Identification Card (ID)</li>
                    <li class="flex items-start"><i class="fas fa-file-alt text-sky-600 mt-1 mr-3"></i> Copy of KRA PIN Certificate</li>
                    <li class="flex items-start"><i class="fas fa-file-signature text-sky-600 mt-1 mr-3"></i> Copy of Title Deed or introduction letter from agent/landlord</li>
                    <li class="flex items-start"><i class="fas fa-clipboard-check text-sky-600 mt-1 mr-3"></i> Duly filled application form</li>
                </ul>
            </div>

            <!-- Company Requirements -->
            <div class="bg-white rounded-2xl shadow-lg p-8 border-t-4 border-blue-600 hover:shadow-2xl transition transform hover:-translate-y-1">
                <h2 class="text-2xl font-semibold text-blue-700 mb-6">Requirements for Companies & Institutions</h2>
                <ul class="space-y-3 text-gray-700">
                    <li class="flex items-start"><i class="fas fa-building text-blue-600 mt-1 mr-3"></i> Copy of Certificate of Incorporation</li>
                    <li class="flex items-start"><i class="fas fa-file-alt text-blue-600 mt-1 mr-3"></i> Copy of KRA PIN Certificate</li>
                    <li class="flex items-start"><i class="fas fa-file-contract text-blue-600 mt-1 mr-3"></i> Copy of Title Deed</li>
                    <li class="flex items-start"><i class="fas fa-clipboard-list text-blue-600 mt-1 mr-3"></i> Duly filled application form</li>
                    <li class="flex items-start"><i class="fas fa-id-badge text-blue-600 mt-1 mr-3"></i> Copy of CR12</li>
                </ul>
            </div>
        </div>

        <!-- Change of Tenancy -->
        <div class="bg-white rounded-2xl shadow-lg p-8 border-t-4 border-teal-600 max-w-3xl mx-auto hover:shadow-2xl transition transform hover:-translate-y-1">
            <h2 class="text-2xl font-semibold text-teal-700 mb-6">Change of Tenancy Requirements</h2>
            <ul class="space-y-3 text-gray-700">
                <li class="flex items-start"><i class="fas fa-id-card text-teal-600 mt-1 mr-3"></i> Copy of National Identification Card (ID)</li>
                <li class="flex items-start"><i class="fas fa-file-alt text-teal-600 mt-1 mr-3"></i> Copy of KRA PIN Certificate</li>
                <li class="flex items-start"><i class="fas fa-file-signature text-teal-600 mt-1 mr-3"></i> Introduction letter from agent/landlord</li>
                <li class="flex items-start"><i class="fas fa-clipboard-check text-teal-600 mt-1 mr-3"></i> Duly filled application form</li>
            </ul>
        </div>

       <!-- Action Buttons -->
        <div class="relative z-10 flex flex-col md:flex-row justify-center top-10 items-center mt-16 gap-4">
            <a href="{{ route('water-connection.apply') }}"
            class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-sky-700 to-blue-600 text-blue rounded-full hover:opacity-90 transition text-lg font-semibold shadow-lg">
                <i class="fas fa-globe mr-2 text-black"></i> Apply Water Connection Online
            </a>
            <a href="#"
            class="inline-flex items-center justify-center px-8 py-4 bg-white text-sky-700 border border-sky-600 rounded-full hover:bg-sky-50 transition text-lg font-semibold shadow">
                <i class="fas fa-download mr-2"></i> Download Application Form
            </a>
        </div>

    </div>
</section>

@endsection
