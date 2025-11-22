@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <nav class="flex mb-4" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-4">
                            <li>
                                <a href="{{ route('organizers.index') }}" class="text-gray-400 hover:text-gray-500">
                                    <svg class="flex-shrink-0 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    <span class="sr-only">Back to Organizers</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('organizers.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Organizers</a>
                            </li>
                            <li>
                                <span class="text-gray-300">/</span>
                            </li>
                            <li>
                                <span class="text-sm font-medium text-gray-900">{{ $organizer->name }}</span>
                            </li>
                        </ol>
                    </nav>
                    <div class="flex items-center space-x-6">
                        <!-- Organizer Avatar -->
                        <div class="flex-shrink-0">
                            @if($organizer->avatar)
                                <img src="{{ Storage::url($organizer->avatar) }}"
                                     alt="{{ $organizer->name }}"
                                     class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-lg">
                            @else
                                <div class="w-20 h-20 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white text-2xl font-bold border-4 border-white shadow-lg">
                                    {{ strtoupper(substr($organizer->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <!-- Organizer Info -->
                        <div class="flex-1 min-w-0">
                            <h1 class="text-3xl font-bold text-gray-900">{{ $organizer->name }}</h1>
                            @if($organizerProfile && $organizerProfile->company_name)
                                <p class="text-xl text-gray-600 mt-1">{{ $organizerProfile->company_name }}</p>
                            @endif

                            <!-- Badges -->
                            <div class="flex flex-wrap gap-2 mt-2">
                                @if($organizerProfile && $organizerProfile->is_verified)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Verified Organizer
                                    </span>
                                @endif
                                @if($organizerProfile && $organizerProfile->is_featured)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        Featured Organizer
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="hidden md:flex space-x-8 text-center">
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $organizer->organized_events_count }}</div>
                        <div class="text-sm text-gray-500">Events</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $organizer->organized_voting_contests_count }}</div>
                        <div class="text-sm text-gray-500">Voting Contests</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Organizer Details -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Organizer Details</h3>
                    <div class="space-y-3">
                        @if($organizerProfile && $organizerProfile->website)
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="flex-shrink-0 mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"></path>
                                </svg>
                                <a href="{{ $organizerProfile->website }}" target="_blank" class="text-blue-600 hover:text-blue-700 truncate">
                                    {{ $organizerProfile->website }}
                                </a>
                            </div>
                        @endif

                        @if($organizerProfile && ($organizerProfile->city || $organizerProfile->country))
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="flex-shrink-0 mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>{{ $organizerProfile->full_address }}</span>
                            </div>
                        @endif

                        @if($organizerProfile && $organizerProfile->phone)
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="flex-shrink-0 mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span>{{ $organizerProfile->phone }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Social Links -->
                @if($organizerProfile && $organizerProfile->social_links)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Follow</h3>
                        <div class="flex space-x-3">
                            @foreach($organizerProfile->social_links as $platform => $url)
                                <a href="{{ $url }}" target="_blank" class="text-gray-400 hover:text-gray-500">
                                    <span class="sr-only">{{ ucfirst($platform) }}</span>
                                    @if($platform === 'facebook')
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                    @elseif($platform === 'twitter')
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                                        </svg>
                                    @elseif($platform === 'instagram')
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path fill-rule="evenodd" d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987c6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.611-3.223-1.561c-.775-.95-1.172-2.238-1.061-3.537c.111-1.298.718-2.489 1.682-3.253c.964-.764 2.201-1.027 3.423-.741c1.222.286 2.257 1.109 2.822 2.255c.565 1.146.595 2.491.083 3.662c-.512 1.171-1.524 2.038-2.726 2.375v-.001zm7.718 1.151c-.557.395-1.208.637-1.899.703-.69.066-1.392-.046-2.032-.323a3.719 3.719 0 01-1.532-1.129 3.972 3.972 0 01-.782-1.796 4.106 4.106 0 01.121-1.863c.239-.619.637-1.162 1.148-1.565a3.69 3.69 0 011.654-.703c.59-.111 1.198-.064 1.767.137.569.201 1.071.57 1.446 1.065.375.495.607 1.095.67 1.724.063.629-.045 1.262-.312 1.826-.267.565-.682 1.038-1.201 1.369l-.002.001z" clip-rule="evenodd"/>
                                        </svg>
                                    @elseif($platform === 'linkedin')
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                                        </svg>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3 space-y-8">
                <!-- About Section -->
                @if($organizerProfile && $organizerProfile->about)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">About</h2>
                        <div class="prose prose-blue max-w-none">
                            <p class="text-gray-700 leading-relaxed">{{ $organizerProfile->about }}</p>
                        </div>
                    </div>
                @endif

                <!-- Upcoming Events -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Upcoming Events</h2>
                        <a href="{{ route('events.index', ['organizer' => $organizer->id]) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            View all events →
                        </a>
                    </div>

                    @if($events->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($events as $event)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors duration-200">
                                    <h3 class="font-semibold text-gray-900 mb-2">{{ $event->name }}</h3>
                                    <div class="flex items-center text-sm text-gray-500 mb-2">
                                        <svg class="flex-shrink-0 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        {{ $event->start_date->format('M j, Y') }}
                                    </div>
                                    <a href="{{ route('events.show', $event) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        View details →
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No upcoming events</h3>
                            <p class="mt-1 text-sm text-gray-500">This organizer doesn't have any upcoming events.</p>
                        </div>
                    @endif
                </div>

                <!-- Active Voting Contests -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Active Voting Contests</h2>
                        <a href="{{ route('voting.index', ['organizer' => $organizer->id]) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            View all contests →
                        </a>
                    </div>

                    @if($votingContests->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($votingContests as $contest)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-purple-300 transition-colors duration-200">
                                    <h3 class="font-semibold text-gray-900 mb-2">{{ $contest->title }}</h3>
                                    <div class="flex items-center text-sm text-gray-500 mb-2">
                                        <svg class="flex-shrink-0 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $contest->total_votes }} votes
                                    </div>
                                    <a href="{{ route('voting.show', $contest) }}" class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                                        {{ $contest->isOngoing() ? 'Vote now' : 'View results' }} →
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No active voting contests</h3>
                            <p class="mt-1 text-sm text-gray-500">This organizer doesn't have any active voting contests.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
