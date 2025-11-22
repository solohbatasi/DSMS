@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="min-h-screen bg-gray-900">
    <!-- Header Section -->
    <div class="bg-black shadow-sm border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-3xl font-bold leading-tight text-white">
                        Create New Event
                    </h1>
                    <p class="mt-2 text-lg text-gray-400">
                        Organize and host amazing events for your audience
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="{{ route('events.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-300 bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Events
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" id="eventForm">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Main Event Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information Card -->
                    <div class="bg-gray-800 rounded-lg shadow-sm border border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Basic Information</h3>

                        <div class="space-y-4">
                            <!-- Event Title -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-200 mb-2">
                                    Event Title <span class="text-red-400">*</span>
                                </label>
                                <input type="text" id="title" name="title" value="{{ old('title') }}"
                                    class="w-full px-4 py-3 bg-gray-900 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                    placeholder="Enter your event title" required maxlength="191">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-200 mb-2">
                                    Description
                                </label>
                                <textarea id="description" name="description" rows="5"
                                        class="w-full px-4 py-3 bg-gray-900 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                        placeholder="Describe your event in detail">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-200 mb-2">
                                    Category <span class="text-red-400">*</span>
                                </label>
                                <div class="relative">
                                    <select id="category_id" name="category_id"
                                            class="w-full px-4 py-3 pr-12 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200 appearance-none" required>
                                        <option value="" class="text-gray-500">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }} class="text-white">
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <!-- Add Category Button -->
                                    <button type="button"
                                            onclick="openCategoryModal()"
                                            class="absolute right-2 top-1/2 transform -translate-y-1/2 p-1.5 text-purple-400 hover:text-purple-300 hover:bg-purple-900/30 rounded-lg transition-all duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                </div>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Date & Time Card -->
                    <div class="bg-gray-800 rounded-lg shadow-sm border border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Date & Time</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-200 mb-2">
                                    Start Date & Time <span class="text-red-400">*</span>
                                </label>
                                <input type="datetime-local" id="start_date" name="start_date" value="{{ old('start_date') }}"
                                    class="w-full px-4 py-3 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200" required>
                                @error('start_date')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-200 mb-2">
                                    End Date & Time <span class="text-red-400">*</span>
                                </label>
                                <input type="datetime-local" id="end_date" name="end_date" value="{{ old('end_date') }}"
                                    class="w-full px-4 py-3 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200" required>
                                @error('end_date')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Location & Pricing Card -->
                    <div class="bg-gray-800 rounded-lg shadow-sm border border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Location & Pricing</h3>

                        <div class="space-y-4">
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-200 mb-2">
                                    Location <span class="text-red-400">*</span>
                                </label>
                                <input type="text" id="location" name="location" value="{{ old('location') }}"
                                    class="w-full px-4 py-3 bg-gray-900 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                    placeholder="Event venue or address" required maxlength="191">
                                @error('location')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="ticket_price" class="block text-sm font-medium text-gray-200 mb-2">
                                        Base Ticket Price (KES)
                                    </label>
                                    <input type="number" id="ticket_price" name="ticket_price" value="{{ old('ticket_price', 0) }}"
                                        class="w-full px-4 py-3 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                        step="0.01" min="0" placeholder="0.00">
                                    @error('ticket_price')
                                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="capacity" class="block text-sm font-medium text-gray-200 mb-2">
                                        Capacity
                                    </label>
                                    <input type="number" id="capacity" name="capacity" value="{{ old('capacity') }}"
                                        class="w-full px-4 py-3 bg-gray-900 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                        placeholder="Maximum number of attendees" min="1">
                                    @error('capacity')
                                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Banner Image Card -->
                    <div class="bg-gray-800 rounded-lg shadow-sm border border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Event Image</h3>

                        <div>
                            <label for="banner_image" class="block text-sm font-medium text-gray-200 mb-2">
                                Banner Image <span class="text-red-400">*</span>
                            </label>
                            <input type="file" id="banner_image" name="banner_image"
                                class="w-full px-4 py-3 bg-gray-900 border border-gray-600 rounded-lg text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                accept="image/*" required>
                            <p class="mt-2 text-sm text-gray-400">Recommended size: 1200x400 pixels. Max file size: 2MB</p>
                            @error('banner_image')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Right Column - Sidebar -->
                <div class="space-y-6">
                    <!-- Ticket Types Card -->
                    <div class="bg-gray-800 rounded-lg shadow-sm border border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Ticket Types</h3>

                        <div id="ticket-types" class="space-y-4">
                            <div class="ticket-type p-4 bg-gray-900 rounded-lg border border-gray-600 relative">
                                <!-- Remove Button -->
                                <button type="button" class="absolute top-3 right-3 text-gray-400 hover:text-red-400 remove-ticket transition-colors duration-200 p-1 rounded-full hover:bg-red-900/20">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>

                                <label class="block text-sm font-medium text-gray-200 mb-2">Ticket Name <span class="text-red-400">*</span></label>
                                <input type="text" name="tickets[0][name]" required
                                    class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                    placeholder="e.g., General Admission">
                                <label class="block text-sm font-medium text-gray-200 mt-3 mb-2">Price (KES) <span class="text-red-400">*</span></label>
                                <input type="number" name="tickets[0][price]" required
                                    class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded text-white focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                    step="0.01" min="0" value="0">
                                <label class="block text-sm font-medium text-gray-200 mt-3 mb-2">Quantity Available <span class="text-red-400">*</span></label>
                                <input type="number" name="tickets[0][quantity_available]" required
                                    class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded text-white focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                    min="1" value="100">
                                <label class="block text-sm font-medium text-gray-200 mt-3 mb-2">Max Per Order</label>
                                <input type="number" name="tickets[0][max_per_order]"
                                    class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded text-white focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                    min="1" value="10">
                                <label class="block text-sm font-medium text-gray-200 mt-3 mb-2">Sale Start Date</label>
                                <input type="datetime-local" name="tickets[0][sale_start_date]"
                                    class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded text-white focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-transparent transition-colors duration-200">
                                <label class="block text-sm font-medium text-gray-200 mt-3 mb-2">Sale End Date</label>
                                <input type="datetime-local" name="tickets[0][sale_end_date]"
                                    class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded text-white focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-transparent transition-colors duration-200">
                            </div>
                        </div>

                        <button type="button" id="add-ticket-type"
                                class="mt-4 w-full inline-flex justify-center items-center px-4 py-2 border border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Another Ticket Type
                        </button>
                    </div>

                    <!-- Voting Contest Card -->
                    <div class="bg-gray-800 rounded-lg shadow-sm border border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Voting Contest</h3>

                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="enable_voting" name="enable_voting" value="1"
                                    class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-600 rounded bg-gray-700">
                                <label for="enable_voting" class="ml-2 block text-sm text-gray-200">
                                    Enable voting contest
                                </label>
                            </div>

                            <div id="voting-section" class="space-y-4 hidden">
                                <div>
                                    <label for="voting_title" class="block text-sm font-medium text-gray-200 mb-2">Contest Title <span class="text-red-400">*</span></label>
                                    <input type="text" id="voting_title" name="voting_title"
                                        class="w-full px-3 py-2 bg-gray-900 border border-gray-600 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                        placeholder="Voting contest title">
                                </div>

                                <div>
                                    <label for="voting_description" class="block text-sm font-medium text-gray-200 mb-2">Description</label>
                                    <textarea id="voting_description" name="voting_description" rows="3"
                                            class="w-full px-3 py-2 bg-gray-900 border border-gray-600 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                            placeholder="Describe the voting contest"></textarea>
                                </div>

                                <div>
                                    <label for="nominee_category_id" class="block text-sm font-medium text-gray-200 mb-2">Nominee Category</label>
                                    <div class="relative">
                                        <select id="nominee_category_id" name="nominee_category_id"
                                                class="w-full px-3 py-2 bg-gray-900 border border-gray-600 rounded text-white focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-transparent transition-colors duration-200 appearance-none pr-12">
                                            <option value="" class="text-gray-500">Select Category</option>
                                            @foreach($nomineeCategories as $category)
                                                <option value="{{ $category->id }}" class="text-white">{{ $category->name }}</option>
                                            @endforeach
                                        </select>

                                        <!-- Add Category Button -->
                                        <button type="button"
                                                onclick="openNomineeCategoryModal()"
                                                class="absolute right-2 top-1/2 transform -translate-y-1/2 p-1.5 text-purple-400 hover:text-purple-300 hover:bg-purple-900/30 rounded-lg transition-all duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Nominees Section -->
                                <div class="border border-gray-600 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-md font-semibold text-white">Nominees</h4>
                                        <button type="button" onclick="openNomineeModal()"
                                                class="inline-flex items-center px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Add Nominee
                                        </button>
                                    </div>

                                    <div id="nominees-container" class="space-y-3">
                                        <!-- Nominees will be added here dynamically -->
                                    </div>

                                    <div id="no-nominees-message" class="text-center py-4 text-gray-400 text-sm">
                                        No nominees added yet. Click "Add Nominee" to get started.
                                    </div>
                                </div>

                                <div>
                                    <label for="price_per_vote" class="block text-sm font-medium text-gray-200 mb-2">Price Per Vote (KES) <span class="text-red-400">*</span></label>
                                    <input type="number" id="price_per_vote" name="price_per_vote" value="10.00"
                                        class="w-full px-3 py-2 bg-gray-900 border border-gray-600 rounded text-white focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                        step="0.01" min="0" required>
                                </div>

                                <div>
                                    <label for="max_votes_per_user" class="block text-sm font-medium text-gray-200 mb-2">Max Votes Per User</label>
                                    <input type="number" id="max_votes_per_user" name="max_votes_per_user" value="1"
                                        class="w-full px-3 py-2 bg-gray-900 border border-gray-600 rounded text-white focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                                        min="1">
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" id="requires_approval" name="requires_approval" value="1"
                                        class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-600 rounded bg-gray-700">
                                    <label for="requires_approval" class="ml-2 block text-sm text-gray-200">
                                        Requires approval for nominees
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Event Settings Card -->
                    <div class="bg-gray-800 rounded-lg shadow-sm border border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Event Settings</h3>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label for="is_featured" class="text-sm font-medium text-gray-200">Feature this event</label>
                                <input type="checkbox" id="is_featured" name="is_featured" value="1"
                                    class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-600 rounded bg-gray-700">
                            </div>

                            <div class="flex items-center justify-between">
                                <label for="is_active" class="text-sm font-medium text-gray-200">Activate event</label>
                                <input type="checkbox" id="is_active" name="is_active" value="1" checked
                                    class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-600 rounded bg-gray-700">
                            </div>
                        </div>
                    </div>

                    <!-- Actions Card -->
                    <div class="bg-gray-800 rounded-lg shadow-sm border border-gray-700 p-6">
                        <div class="space-y-3">
                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Create Event
                            </button>

                            <a href="{{ route('events.index') }}"
                            class="w-full inline-flex justify-center items-center px-4 py-3 border border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Add Nominee Category Modal -->
<div id="nomineeCategoryModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 hidden animate-fade-in">
    <div class="bg-gray-900 rounded-xl shadow-2xl w-full max-w-md mx-auto transform transition-all duration-300 ease-out animate-scale-in border border-gray-700/50">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-700/50 bg-gradient-to-r from-gray-800 to-gray-900 rounded-t-xl">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-purple-600/20 rounded-lg">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white">Create Category</h3>
            </div>
            <button type="button" onclick="closeNomineeCategoryModal()"
                    class="p-2 hover:bg-gray-700/50 rounded-lg transition-all duration-200 group">
                <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Form -->
        <form id="nomineeCategoryForm" method="POST" action="{{ route('nominee-categories.store') }}" class="p-6 space-y-5">
            @csrf

            <!-- Category Name -->
            <div class="space-y-2">
                <label for="nominee_category_name" class="flex items-center text-sm font-medium text-gray-200">
                    Category Name <span class="text-red-400 ml-1">*</span>
                </label>
                <input type="text" id="nominee_category_name" name="name" required
                       class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400
                              focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500
                              transition-all duration-200"
                       placeholder="Enter category name">
                <div id="nominee_category_name_error" class="text-sm text-red-400 hidden"></div>
            </div>

            <!-- Color & Icon Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Color -->
                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-200">Color</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" id="nominee_category_color" name="color" value="#8B5CF6"
                               class="w-10 h-10 bg-gray-800 border border-gray-600 rounded-lg cursor-pointer hover:scale-105 transition-transform">
                        <input type="text" id="nominee_category_color_text" value="#8B5CF6"
                               class="flex-1 px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm
                                      focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>

                <!-- Icon -->
                <div class="space-y-2">
                    <label for="nominee_category_icon" class="text-sm font-medium text-gray-200">Icon</label>
                    <select id="nominee_category_icon" name="icon"
                            class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm
                                   focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Select Icon</option>
                        <option value="trophy">🏆 Trophy</option>
                        <option value="star">⭐ Star</option>
                        <option value="crown">👑 Crown</option>
                        <option value="award">🎖️ Award</option>
                        <option value="medal">🥇 Medal</option>
                    </select>
                </div>
            </div>

            <!-- Sort Order & Active Status Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Sort Order -->
                <div class="space-y-2">
                    <label for="nominee_category_sort_order" class="text-sm font-medium text-gray-200">Sort Order</label>
                    <input type="number" id="nominee_category_sort_order" name="sort_order" value="0" min="0"
                           class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm
                                  focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>

                <!-- Active Status -->
                <div class="flex items-center space-x-3 pt-6">
                    <div class="relative inline-flex items-center">
                        <input type="checkbox" id="nominee_category_active" name="is_active" value="1" checked
                               class="w-4 h-4 text-purple-600 bg-gray-800 border-gray-600 rounded focus:ring-purple-500 focus:ring-2">
                    </div>
                    <label for="nominee_category_active" class="text-sm text-gray-200">Active Category</label>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-700/50">
                <button type="button" onclick="closeNomineeCategoryModal()"
                        class="px-5 py-2.5 text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors duration-200">
                    Cancel
                </button>
                <button type="submit" id="saveNomineeCategoryBtn"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                    <span>Save Category</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Nominee Modal -->
<div id="nomineeModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 hidden animate-fade-in">
    <div class="bg-gray-900 rounded-xl shadow-2xl w-full max-w-md mx-auto transform transition-all duration-300 ease-out animate-scale-in border border-gray-700/50">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-700/50 bg-gradient-to-r from-gray-800 to-gray-900 rounded-t-xl">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-green-600/20 rounded-lg">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white">Add Nominee</h3>
            </div>
            <button type="button" onclick="closeNomineeModal()"
                    class="p-2 hover:bg-gray-700/50 rounded-lg transition-all duration-200 group">
                <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Form -->
        <form id="nomineeForm" class="p-6 space-y-5">
            <!-- Name & Code Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label for="nominee_name" class="flex items-center text-sm font-medium text-gray-200">
                        Name <span class="text-red-400 ml-1">*</span>
                    </label>
                    <input type="text" id="nominee_name" name="name" required
                           class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>

                <div class="space-y-2">
                    <label for="nominee_code" class="text-sm font-medium text-gray-200">Code</label>
                    <input type="text" id="nominee_code" name="code"
                           class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                           placeholder="Unique code">
                </div>
            </div>

            <!-- Bio -->
            <div class="space-y-2">
                <label for="nominee_bio" class="text-sm font-medium text-gray-200">Bio</label>
                <textarea id="nominee_bio" name="bio" rows="2"
                          class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 text-sm resize-none
                                 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                          placeholder="Brief description about the nominee"></textarea>
            </div>

            <!-- Affiliation & Position Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label for="nominee_affiliation" class="text-sm font-medium text-gray-200">Affiliation</label>
                    <input type="text" id="nominee_affiliation" name="affiliation"
                           class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                           placeholder="Company, organization">
                </div>

                <div class="space-y-2">
                    <label for="nominee_position" class="text-sm font-medium text-gray-200">Display Order</label>
                    <input type="number" id="nominee_position" name="position" value="0" min="0"
                           class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm
                                  focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
            </div>

            <!-- Photo Upload -->
            <div class="space-y-2">
                <label for="nominee_photo" class="text-sm font-medium text-gray-200">Photo</label>
                <div class="relative">
                    <input type="file" id="nominee_photo" name="photo" accept="image/*"
                           class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-gray-300 text-sm
                                  file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-medium
                                  file:bg-green-600 file:text-white hover:file:bg-green-700 transition-colors duration-200
                                  focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-700/50">
                <button type="button" onclick="closeNomineeModal()"
                        class="px-5 py-2.5 text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors duration-200">
                    Cancel
                </button>
                <button type="button" onclick="addNominee()"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                    <span>Add Nominee</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Category Modal -->
<div id="categoryModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50 hidden animate-fade-in">
    <div class="bg-gray-900 rounded-xl shadow-2xl w-full max-w-md mx-auto transform transition-all duration-300 ease-out animate-scale-in border border-gray-700/50">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-700/50 bg-gradient-to-r from-gray-800 to-gray-900 rounded-t-xl">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-600/20 rounded-lg">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white">Create Category</h3>
            </div>
            <button type="button" onclick="closeCategoryModal()"
                    class="p-2 hover:bg-gray-700/50 rounded-lg transition-all duration-200 group">
                <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Form -->
        <form id="categoryForm" method="POST" action="{{ route('event-categories.store') }}" class="p-6 space-y-5">
            @csrf

            <!-- Category Name -->
            <div class="space-y-2">
                <label for="category_name" class="flex items-center text-sm font-medium text-gray-200">
                    Category Name <span class="text-red-400 ml-1">*</span>
                </label>
                <input type="text" id="category_name" name="name" required
                       class="w-full px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400
                              focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                              transition-all duration-200"
                       placeholder="Enter category name">
                <div id="category_name_error" class="text-sm text-red-400 hidden"></div>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <label for="category_description" class="text-sm font-medium text-gray-200">Description</label>
                <textarea id="category_description" name="description" rows="2"
                          class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 text-sm resize-none
                                 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Brief description of the category"></textarea>
            </div>

            <!-- Icon & Color Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label for="category_icon" class="text-sm font-medium text-gray-200">Icon</label>
                    <select id="category_icon" name="icon"
                            class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm
                                   focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Icon</option>
                        <option value="music">🎵 Music</option>
                        <option value="trophy">🏆 Sports</option>
                        <option value="briefcase">💼 Business</option>
                        <option value="palette">🎨 Art</option>
                        <option value="utensils">🍴 Food</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-gray-200">Color</label>
                    <div class="flex items-center space-x-3">
                        <input type="color" id="category_color" name="color" value="#3B82F6"
                               class="w-10 h-10 bg-gray-800 border border-gray-600 rounded-lg cursor-pointer hover:scale-105 transition-transform">
                        <input type="text" id="category_color_text" value="#3B82F6"
                               class="flex-1 px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Active Status -->
            <div class="flex items-center space-x-3 pt-2">
                <input type="checkbox" id="category_active" name="is_active" value="1" checked
                       class="w-4 h-4 text-blue-600 bg-gray-800 border-gray-600 rounded focus:ring-blue-500 focus:ring-2">
                <label for="category_active" class="text-sm text-gray-200">Active Category</label>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-700/50">
                <button type="button" onclick="closeCategoryModal()"
                        class="px-5 py-2.5 text-sm font-medium text-gray-300 bg-gray-700 hover:bg-gray-600 rounded-lg transition-colors duration-200">
                    Cancel
                </button>
                <button type="submit" id="saveCategoryBtn"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                    <span>Save Category</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes scale-in {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(-10px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .animate-fade-in {
        animation: fade-in 0.2s ease-out;
    }

    .animate-scale-in {
        animation: scale-in 0.3s ease-out;
    }

    /* Responsive modal sizing */
    @media (min-width: 640px) {
        #nomineeCategoryModal > div,
        #nomineeModal > div,
        #categoryModal > div {
            width: auto;
            min-width: 400px;
            max-width: min(90vw, 500px);
        }
    }

    @media (min-width: 1024px) {
        #nomineeCategoryModal > div,
        #nomineeModal > div,
        #categoryModal > div {
            max-width: min(80vw, 480px);
        }
    }

    @media (min-width: 1280px) {
        #nomineeCategoryModal > div,
        #nomineeModal > div,
        #categoryModal > div {
            max-width: min(70vw, 460px);
        }
    }

    /* Custom scrollbar for modals */
    .modal-scroll {
        scrollbar-width: thin;
        scrollbar-color: #4B5563 #1F2937;
    }

    .modal-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .modal-scroll::-webkit-scrollbar-track {
        background: #1F2937;
        border-radius: 3px;
    }

    .modal-scroll::-webkit-scrollbar-thumb {
        background: #4B5563;
        border-radius: 3px;
    }

    .modal-scroll::-webkit-scrollbar-thumb:hover {
        background: #6B7280;
    }
</style>

<!-- Success Toast -->
<div id="successToast" class="fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 z-50 hidden">
    <div class="flex items-center space-x-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span id="toastMessage">Category added successfully!</span>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle voting section
        const enableVoting = document.getElementById('enable_voting');
        const votingSection = document.getElementById('voting-section');

        enableVoting.addEventListener('change', function() {
            votingSection.classList.toggle('hidden', !this.checked);

            // Make voting fields required when enabled
            const votingFields = votingSection.querySelectorAll('input[required], select[required]');
            votingFields.forEach(field => {
                field.required = this.checked;
            });
        });

        // Add ticket type
        let ticketCount = 1;
        document.getElementById('add-ticket-type').addEventListener('click', function() {
            const ticketTypes = document.getElementById('ticket-types');
            const newTicket = document.createElement('div');
            newTicket.className = 'ticket-type p-4 bg-gray-900 rounded-lg border border-gray-600 relative';
            newTicket.innerHTML = `
                <button type="button" class="absolute top-3 right-3 text-gray-400 hover:text-red-400 remove-ticket transition-colors duration-200 p-1 rounded-full hover:bg-red-900/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <label class="block text-sm font-medium text-gray-200 mb-2">Ticket Name <span class="text-red-400">*</span></label>
                <input type="text" name="tickets[${ticketCount}][name]" required
                    class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                    placeholder="e.g., VIP Access">
                <label class="block text-sm font-medium text-gray-200 mt-3 mb-2">Price (KES) <span class="text-red-400">*</span></label>
                <input type="number" name="tickets[${ticketCount}][price]" required
                    class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded text-white focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                    step="0.01" min="0" value="0">
                <label class="block text-sm font-medium text-gray-200 mt-3 mb-2">Quantity Available <span class="text-red-400">*</span></label>
                <input type="number" name="tickets[${ticketCount}][quantity_available]" required
                    class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded text-white focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                    min="1" value="100">
                <label class="block text-sm font-medium text-gray-200 mt-3 mb-2">Max Per Order</label>
                <input type="number" name="tickets[${ticketCount}][max_per_order]"
                    class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded text-white focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-transparent transition-colors duration-200"
                    min="1" value="10">
                <label class="block text-sm font-medium text-gray-200 mt-3 mb-2">Sale Start Date</label>
                <input type="datetime-local" name="tickets[${ticketCount}][sale_start_date]"
                    class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded text-white focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-transparent transition-colors duration-200">
                <label class="block text-sm font-medium text-gray-200 mt-3 mb-2">Sale End Date</label>
                <input type="datetime-local" name="tickets[${ticketCount}][sale_end_date]"
                    class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded text-white focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-transparent transition-colors duration-200">
            `;
            ticketTypes.appendChild(newTicket);
            ticketCount++;
        });

        // Remove ticket type
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-ticket') || e.target.closest('.remove-ticket')) {
                const removeBtn = e.target.classList.contains('remove-ticket') ? e.target : e.target.closest('.remove-ticket');
                const ticketTypes = document.getElementById('ticket-types');
                const ticketTypeElements = ticketTypes.querySelectorAll('.ticket-type');

                // Only remove if there's more than one ticket type
                if (ticketTypeElements.length > 1) {
                    removeBtn.closest('.ticket-type').remove();
                } else {
                    // Optionally show a message or prevent removal of the last ticket
                    alert('You must have at least one ticket type.');
                }
            }
        });

        // Form validation
        document.getElementById('eventForm').addEventListener('submit', function(e) {
            const startDate = new Date(document.getElementById('start_date').value);
            const endDate = new Date(document.getElementById('end_date').value);

            if (endDate <= startDate) {
                e.preventDefault();
                alert('End date must be after start date.');
                return false;
            }

            // Validate that at least one ticket type is provided
            const ticketTypes = document.querySelectorAll('.ticket-type');
            if (ticketTypes.length === 0) {
                e.preventDefault();
                alert('Please add at least one ticket type.');
                return false;
            }

            // Validate voting contest fields if enabled
            if (document.getElementById('enable_voting').checked) {
                const votingTitle = document.getElementById('voting_title').value;
                const pricePerVote = document.getElementById('price_per_vote').value;

                if (!votingTitle.trim()) {
                    e.preventDefault();
                    alert('Please enter a voting contest title.');
                    return false;
                }

                if (!pricePerVote || parseFloat(pricePerVote) < 0) {
                    e.preventDefault();
                    alert('Please enter a valid price per vote.');
                    return false;
                }
            }
        });
    });

    // Modal Functions
    function openCategoryModal() {
        document.getElementById('categoryModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('category_name').focus();
    }

    function closeCategoryModal() {
        document.getElementById('categoryModal').classList.add('hidden');
        document.body.style.overflow = '';
        document.getElementById('categoryForm').reset();
        document.getElementById('category_color').value = '#3B82F6';
        document.getElementById('category_color_text').value = '#3B82F6';
        hideError('category_name_error');
    }

    // Color Picker Sync
    document.getElementById('category_color').addEventListener('input', function(e) {
        document.getElementById('category_color_text').value = e.target.value;
    });

    document.getElementById('category_color_text').addEventListener('input', function(e) {
        const color = e.target.value;
        if (/^#[0-9A-F]{6}$/i.test(color)) {
            document.getElementById('category_color').value = color;
        }
    });

    // Error Handling
    function showError(fieldId, message) {
        const errorElement = document.getElementById(fieldId);
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
    }

    function hideError(fieldId) {
        const errorElement = document.getElementById(fieldId);
        errorElement.classList.add('hidden');
    }

    // Toast Notification
    function showToast(message) {
        const toast = document.getElementById('successToast');
        const toastMessage = document.getElementById('toastMessage');

        toastMessage.textContent = message;
        toast.classList.remove('hidden');
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);

        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.classList.add('hidden'), 300);
        }, 3000);
    }

    // Category Form Submission
    document.getElementById('categoryForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const saveBtn = document.getElementById('saveCategoryBtn');
        const originalText = saveBtn.textContent;

        // Show loading state
        saveBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Saving...
        `;
        saveBtn.disabled = true;

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok) {
                // Success
                closeCategoryModal();

                // Add new category to select
                const categorySelect = document.getElementById('category_id');
                const newOption = new Option(data.category.name, data.category.id, false, true);
                categorySelect.add(newOption);

                showToast('Category added successfully!');
            } else {
                // Show validation errors
                if (data.errors) {
                    if (data.errors.name) {
                        showError('category_name_error', data.errors.name[0]);
                    }
                    if (data.errors.slug) {
                        showError('category_name_error', data.errors.slug[0]);
                    }
                } else if (data.message) {
                    showError('category_name_error', data.message);
                }
            }
        } catch (error) {
            console.error('Error:', error);
            showError('category_name_error', 'An error occurred while saving the category.');
        } finally {
            // Reset button state
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        }
    });

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('categoryModal').classList.contains('hidden')) {
            closeCategoryModal();
        }
    });

    // Close modal when clicking outside
    document.getElementById('categoryModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCategoryModal();
        }
    });

        // Modal Functions for Nominee Category
    function openNomineeCategoryModal() {
        document.getElementById('nomineeCategoryModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('nominee_category_name').focus();
    }

    function closeNomineeCategoryModal() {
        document.getElementById('nomineeCategoryModal').classList.add('hidden');
        document.body.style.overflow = '';
        document.getElementById('nomineeCategoryForm').reset();
        document.getElementById('nominee_category_color').value = '#8B5CF6';
        document.getElementById('nominee_category_color_text').value = '#8B5CF6';
        hideNomineeCategoryError('nominee_category_name_error');
    }

    // Color Picker Sync
    document.addEventListener('DOMContentLoaded', function() {
        const colorPicker = document.getElementById('nominee_category_color');
        const colorText = document.getElementById('nominee_category_color_text');

        if (colorPicker && colorText) {
            colorPicker.addEventListener('input', function(e) {
                colorText.value = e.target.value;
            });

            colorText.addEventListener('input', function(e) {
                const color = e.target.value;
                if (/^#[0-9A-F]{6}$/i.test(color)) {
                    colorPicker.value = color;
                }
            });
        }
    });

    // Error Handling
    function showNomineeCategoryError(fieldId, message) {
        const errorElement = document.getElementById(fieldId);
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
    }

    function hideNomineeCategoryError(fieldId) {
        const errorElement = document.getElementById(fieldId);
        errorElement.classList.add('hidden');
    }

    // Form Submission
    document.getElementById('nomineeCategoryForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const saveBtn = document.getElementById('saveNomineeCategoryBtn');
        const originalText = saveBtn.textContent;

        // Show loading state
        saveBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Saving...
        `;
        saveBtn.disabled = true;

        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok) {
                // Success
                closeNomineeCategoryModal();

                // Add new category to select
                const categorySelect = document.getElementById('nominee_category_id');
                const newOption = new Option(data.category.name, data.category.id, false, true);
                categorySelect.add(newOption);

                showToast('Nominee category added successfully!');
            } else {
                // Show validation errors
                if (data.errors) {
                    if (data.errors.name) {
                        showNomineeCategoryError('nominee_category_name_error', data.errors.name[0]);
                    }
                    if (data.errors.slug) {
                        showNomineeCategoryError('nominee_category_name_error', data.errors.slug[0]);
                    }
                } else if (data.message) {
                    showNomineeCategoryError('nominee_category_name_error', data.message);
                }
            }
        } catch (error) {
            console.error('Error:', error);
            showNomineeCategoryError('nominee_category_name_error', 'An error occurred while saving the category.');
        } finally {
            // Reset button state
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        }
    });

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('nomineeCategoryModal').classList.contains('hidden')) {
            closeNomineeCategoryModal();
        }
    });

    // Close modal when clicking outside
    document.getElementById('nomineeCategoryModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeNomineeCategoryModal();
        }
    });

    let nomineeCount = 0;

    // Modal Functions for Nominees
    function openNomineeModal() {
        document.getElementById('nomineeModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('nominee_name').focus();
    }

    function closeNomineeModal() {
        document.getElementById('nomineeModal').classList.add('hidden');
        document.body.style.overflow = '';
        document.getElementById('nomineeForm').reset();
    }

    function addNominee() {
        const form = document.getElementById('nomineeForm');
        const formData = new FormData(form);

        const name = formData.get('name');
        const code = formData.get('code');
        const bio = formData.get('bio');
        const affiliation = formData.get('affiliation');
        const position = formData.get('position');
        const photoFile = document.getElementById('nominee_photo').files[0];

        if (!name) {
            alert('Please enter nominee name');
            return;
        }

        // Hide no nominees message
        document.getElementById('no-nominees-message').classList.add('hidden');

        // Create nominee element
        const nomineeElement = document.createElement('div');
        nomineeElement.className = 'bg-gray-900 border border-gray-600 rounded-lg p-4 relative';
        nomineeElement.innerHTML = `
            <button type="button" onclick="this.parentElement.remove(); checkNomineesEmpty();"
                    class="absolute top-3 right-3 text-gray-400 hover:text-red-400 transition-colors duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <input type="hidden" name="nominees[${nomineeCount}][name]" value="${name}">
            <input type="hidden" name="nominees[${nomineeCount}][code]" value="${code}">
            <input type="hidden" name="nominees[${nomineeCount}][bio]" value="${bio}">
            <input type="hidden" name="nominees[${nomineeCount}][affiliation]" value="${affiliation}">
            <input type="hidden" name="nominees[${nomineeCount}][position]" value="${position}">
            ${photoFile ? `<input type="hidden" name="nominees[${nomineeCount}][photo]" value="${photoFile.name}">` : ''}

            <div class="flex items-start space-x-3">
                ${photoFile ? `
                    <div class="w-12 h-12 bg-gray-700 rounded-lg overflow-hidden flex-shrink-0">
                        <img src="${URL.createObjectURL(photoFile)}" alt="${name}" class="w-full h-full object-cover">
                    </div>
                ` : `
                    <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-semibold text-sm">${name.charAt(0)}</span>
                    </div>
                `}

                <div class="flex-1 min-w-0">
                    <h5 class="text-white font-semibold text-sm mb-1">${name}</h5>
                    ${code ? `<p class="text-gray-400 text-xs mb-1">Code: ${code}</p>` : ''}
                    ${affiliation ? `<p class="text-gray-400 text-xs mb-1">${affiliation}</p>` : ''}
                    ${bio ? `<p class="text-gray-400 text-xs line-clamp-2">${bio}</p>` : ''}
                </div>
            </div>
        `;

        document.getElementById('nominees-container').appendChild(nomineeElement);
        nomineeCount++;
        closeNomineeModal();
    }

    function checkNomineesEmpty() {
        const nomineesContainer = document.getElementById('nominees-container');
        if (nomineesContainer.children.length === 0) {
            document.getElementById('no-nominees-message').classList.remove('hidden');
        }
    }

    // Handle file uploads when form is submitted
    document.getElementById('eventForm')?.addEventListener('submit', function(e) {
        const nomineeForms = document.querySelectorAll('input[name^="nominees"][name$="[photo]"]');
        nomineeForms.forEach((input, index) => {
            if (input.value) {
                // Replace the hidden input with actual file input
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.name = `nominees[${index}][photo]`;
                fileInput.style.display = 'none';
                document.body.appendChild(fileInput);
                input.remove();
            }
        });
    });
</script>
@endpush

<style>
    .line-clamp-1 {
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 1;
    }

    .line-clamp-2 {
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }

    /* Custom file input styling */
    input[type="file"]::-webkit-file-upload-button {
        color: white;
        background: #7c3aed;
        border: 0;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    input[type="file"]::-webkit-file-upload-button:hover {
        background: #6d28d9;
    }

    /* Ensure datetime inputs have proper contrast */
    input[type="datetime-local"] {
        color-scheme: dark;
    }

    /* Improve select dropdown contrast */
    select option {
        background: #1f2937;
        color: white;
    }
</style>
@endsection
