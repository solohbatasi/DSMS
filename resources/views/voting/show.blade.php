@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="min-h-screen modal-header">
    <!-- Header Section -->
<!-- Contest Header -->
<div
    class="relative bg-gray-900 text-white"
    @if($contest->featured_image)
        style="background-image: url('{{ \Illuminate\Support\Facades\Storage::url($contest->featured_image) }}'); background-size: cover; background-position: center;"
    @endif
>
    <div class="absolute inset-0 bg-yellow bg-opacity-60"></div> <!-- dark overlay -->

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-6 lg:space-y-0">
            <div class="flex-1">
                <!-- Breadcrumb -->
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-3 text-sm text-gray-300">
                        <li>
                            <a href="{{ route('voting.index') }}" class="hover:text-white flex items-center">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Back
                            </a>
                        </li>
                        <li>/</li>
                        <li>
                            <a href="{{ route('voting.index') }}" class="hover:text-white">Voting Contests</a>
                        </li>
                        <li>/</li>
                        <li class="text-gray-100 font-semibold">{{ $contest->title }}</li>
                    </ol>
                </nav>

                <!-- Contest Info -->
                <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">{{ $contest->title }}</h1>
                <p class="text-gray-200 text-lg leading-relaxed mb-4 max-w-2xl">
                    {{ $contest->description }}
                </p>

                <!-- Status Badges -->
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $contest->isOngoing() ? 'bg-green-600/80' :
                           ($contest->hasEnded() ? 'bg-gray-500/80' : 'bg-blue-600/80') }}">
                        {{ $contest->isOngoing() ? 'Live Voting' : ($contest->hasEnded() ? 'Voting Ended' : 'Upcoming') }}
                    </span>

                    @if($contest->is_featured)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-500/90 text-gray-900">
                            Featured
                        </span>
                    @endif
                    <br>
                    {{-- Total votes (visible only to organizer) --}}
                    @if($contest->isOrganizer())
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-600/80 text-white">
                            Total Votes: {{ $contest->total_votes }}
                        </span>
                    @endif
                </div>

                <!-- Date Info -->
                <div class="mt-6 flex flex-wrap items-center text-sm text-gray-300 gap-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Starts: {{ $contest->start_date->format('M j, Y g:i A') }}</span>
                    </div>
                    @if($contest->end_date)
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Ends: {{ $contest->end_date->format('M j, Y g:i A') }}</span>
                        </div>
                    @endif
                </div>

                <!-- Countdown -->
                <div class="mt-4">
                    <div id="countdown" class="text-lg font-semibold text-yellow-300 bg-black/40 inline-block px-4 py-2 rounded-md"></div>
                </div>
            </div>

            <!-- Rules Box -->
            @if($contest->rules)
                <div class="bg-white/10 border border-white/20 backdrop-blur-sm rounded-lg p-6 max-w-sm text-white">
                    <h3 class="text-lg font-semibold mb-3">Voting Rules</h3>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-400 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>{!! nl2br(e($contest->rules)) !!}</span>
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>

    <script>
        // Countdown Timer Logic
        document.addEventListener('DOMContentLoaded', () => {
            const countdownElement = document.getElementById('countdown');
            const startTime = new Date("{{ $contest->start_date }}").getTime();
            const endTime = new Date("{{ $contest->end_date }}").getTime();
            const now = new Date().getTime();

            let targetTime, label;
            if (now < startTime) {
                targetTime = startTime;
                label = "Starts in:";
            } else if (now >= startTime && now < endTime) {
                targetTime = endTime;
                label = "Ends in:";
            } else {
                countdownElement.textContent = "Voting has ended";
                return;
            }

            const updateCountdown = () => {
                const current = new Date().getTime();
                const distance = targetTime - current;

                if (distance <= 0) {
                    countdownElement.textContent = (label === "Starts in:") ? "Voting is now LIVE!" : "Voting has ended";
                    return;
                }

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                countdownElement.textContent = `${label} ${days}d ${hours}h ${minutes}m ${seconds}s`;
            };

            updateCountdown();
            setInterval(updateCountdown, 1000);
        });
    </script>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 modal-header">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-4">
                <div class="rounded-lg shadow-sm border-gray-100 p-6">
                <h2 class="text-2xl font-bold text-white mb-6">Contestants</h2>

                @if($groupedNominees->count() > 0)
                    @foreach($groupedNominees as $categoryName => $nominees)
                        <!-- Category Title -->
                        <h3 class="text-xl font-semibold text-purple-500 mb-4 border-b pb-1">
                            {{ $categoryName }}
                        </h3>

                        <!-- Nominees Grid -->
                        <div class="grid gap-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 mb-8">
                            @foreach($nominees as $nominee)
                                <div class="bg-black  border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200 p-5 flex flex-col justify-between">
                                    <div>
                                        <!-- Photo -->
                                        <div class="flex justify-center mb-4">
                                            @if($nominee->photo)
                                                <img
                                                    src="{{ $nominee->photo
                                                        ? \Illuminate\Support\Facades\Storage::url($nominee->photo)
                                                        : \Illuminate\Support\Facades\Storage::url('nominees/OIP.jpg') }}"
                                                    class="w-24 h-24 rounded-full object-cover ring-4 ring-purple-100"
                                                />
                                            @else
                                                <div class="w-24 h-24 rounded-full bg-purple-500 flex items-center justify-center text-white text-3xl font-bold">
                                                    {{ strtoupper(substr($nominee->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Info -->
                                        <div class="text-center">
                                            <h3 class="text-lg font-semibold text-white">{{ $nominee->name }}</h3>
                                            <p class="text-white text-center text-sm mt-1">Code: {{ $nominee->code ?? '' }}</p>
                                            @if($nominee->bio)
                                                <p class="text-white text-sm mt-1 line-clamp-2">{{ $nominee->bio }}</p>
                                            @endif

                                            @if($contest->isOrganizer())
                                                <div class="mt-3 flex justify-center space-x-4 text-sm text-gray-500">
                                                    <span>{{ $nominee->votes_count }} votes</span>
                                                    @if($contest->total_votes > 0)
                                                        <span>{{ number_format($nominee->vote_percentage, 1) }}%</span>
                                                    @endif
                                                </div>

                                                @if($contest->total_votes > 0)
                                                    <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                                                        <div class="bg-purple-600 h-2 rounded-full"
                                                            style="width: {{ $nominee->vote_percentage }}%"></div>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Buttons -->
                                    <div class="mt-5 text-center space-y-2">
                                        @if($contest->isOngoing())
                                            <button
                                                type="button"
                                                onclick="openVoteModal('{{ $nominee->id }}', '{{ $nominee->name }}', '{{ $nominee->code }}', '{{ $nominee->photo ? \Illuminate\Support\Facades\Storage::url($nominee->photo) : '' }}')"
                                                class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500" style="background-color: rgba(226, 0, 177, 1);">
                                                Vote
                                            </button>
                                        @endif

                                        <!-- Share Button -->
                                        <button
                                            type="button"
                                            onclick="copyShareLink('{{ $contest->id }}', '{{ $nominee->code }}', this)"
                                            class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-pink bg-purple-50 rounded-md hover:bg-purple-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                            Share Voting Link
                                        </button>

                                        <!-- Tooltip -->
                                        <span class="text-xs text-blue-500 hidden" id="copied-{{ $nominee->code }}">Link copied!</span>

                                        @if($userVote && $userVote->nominee_id == $nominee->id)
                                            <span class="inline-flex items-center justify-center w-full mt-2 px-3 py-1 rounded-md text-sm font-medium bg-green-100 text-green-800">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Your Vote
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-10">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No nominees yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Nominees will be added soon.</p>
                    </div>
                @endif

                </div>
            </div>


        </div>
    </div>
</div>
<!-- Vote Modal -->
<div id="voteModal" class="hidden fixed inset-0 z-50 overflow-y-auto transition-opacity duration-300">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="closeVoteModal()"></div>

        <div class="modal-header rounded-xl shadow-lg overflow-hidden w-full max-w-md z-50 transform transition-all scale-100">
            <div class="p-6">
                <div class="text-center">
                    <img id="modalNomineePhoto" src="" alt="" class="w-24 h-24 rounded-full mx-auto object-cover ring-4 ring-purple-100">
                    <h2 id="modalNomineeName" class="mt-4 text-2xl font-bold text-white-900"></h2>
                    <p id="modalNomineeCode" class="text-sm text-white-500"></p>
                </div>

                <form id="voteForm" method="POST" action="{{ route('pesapal.stkpush') }}" class="mt-6 space-y-4">
                     @csrf
                    <input type="hidden" name="nominee_id" id="modalNomineeId">
                    <input type="hidden" name="contest_id" value="{{ $contest->id }}">
                    <!-- Price Info -->
                    <div class="flex justify-between items-center">
                        <span class="text-white-700 font-medium">Price per Vote:</span>
                        <span class="text-white font-semibold">KES {{ number_format($contest->price_per_vote ?? 10, 2) }}</span>
                    </div>

                    <!-- Vote Quantity -->
                    <div>
                        <label for="voteCount" class="block text-sm font-medium text-white-700">Number of Votes</label>
                        <input type="number" name="votes" id="voteCount" min="1" value="1"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500" style="color: black;">
                    </div>

                    <!-- Total -->
                    <div class="flex justify-between items-center bg-purple-50 px-4 py-3 rounded-md">
                        <span class="text-gray-700 font-medium">Total:</span>
                        <span id="totalAmount" class="text-pink font-bold text-lg">KES {{ number_format($contest->price_per_vote ?? 10, 2) }}</span>
                    </div>

                    <!-- Phone Number -->
                    <!-- <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number (for STK Push)</label>
                        <input type="text" name="phone" id="modalPhone" placeholder="e.g. 07XXXXXXXX"
                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500" required>
                    </div> -->
                    <!-- Feedback / progress -->
                    <div id="voteFeedback" class="mt-4 hidden">
                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div id="voteProgress" class="bg-purple-600 h-2 w-0 transition-all"></div>
                        </div>
                        <p id="voteMessage" class="mt-2 text-center text-gray-700"></p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeVoteModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-5 py-2 text-sm font-medium text-white rounded-md hover:bg-purple-700 focus:ring-2 focus:ring-offset-2 focus:ring-purple-500" style="background-color: rgba(0, 0, 0, 1);">
                            Confirm & Pay
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function openVoteModal(id, name, code, photoUrl) {
    // Set nominee data
    document.getElementById('modalNomineeId').value = id;
    document.getElementById('modalNomineeName').textContent = name;
    document.getElementById('modalNomineeCode').textContent = 'Code: ' + code;
    document.getElementById('modalNomineePhoto').src = photoUrl || '/default-avatar.png';

    // Reset form inputs
    document.getElementById('voteCount').value = 1;
    document.getElementById('totalAmount').textContent = 'KES {{ number_format($contest->price_per_vote ?? 10, 2) }}';
    document.getElementById('phone').value = '';

    // Show modal
    document.getElementById('voteModal').classList.remove('hidden');
}

function closeVoteModal() {
    document.getElementById('voteModal').classList.add('hidden');
}

// Live total update when votes change
document.getElementById('voteCount').addEventListener('input', function() {
    const count = parseInt(this.value) || 1;
    const price = {{ $contest->price_per_vote ?? 10 }};
    const total = count * price;
    document.getElementById('totalAmount').textContent = 'KES ' + total.toLocaleString();
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($autoNominee)
        // Automatically open modal for this nominee
        openVoteModal(
            '{{ $autoNominee->id }}',
            '{{ addslashes($autoNominee->name) }}',
            '{{ $autoNominee->code }}',
            '{{ $autoNominee->photo ? \Illuminate\Support\Facades\Storage::url($autoNominee->photo) : \Illuminate\Support\Facades\Storage::url("nominees/OIP.jpg") }}'
        );

    @endif
});
function copyShareLink(contestId, code, btn) {
    const baseUrl = window.location.origin + '/voting/' + contestId + '?code=' + code;
    navigator.clipboard.writeText(baseUrl).then(() => {
        // Show tooltip
        const copiedMsg = document.getElementById('copied-' + code);
        if (copiedMsg) {
            copiedMsg.classList.remove('hidden');
            setTimeout(() => copiedMsg.classList.add('hidden'), 2000);
        }
    });
}

</script>

<!-- <script>
document.getElementById('voteForm').addEventListener('submit', function(e){

    e.preventDefault(); // prevent actual form submission
    function showVoteError(msg, progressEl, messageEl) {
        progressEl.style.width = '100%';
        messageEl.textContent = msg;
        messageEl.classList.remove('text-green-600');
        messageEl.classList.add('text-red-500');
    }

    const form = e.target;

    // Normalize phone
    let phone = document.getElementById('modalPhone').value.trim();
    if (phone.startsWith('07')) {
        phone = '254' + phone.substring(1);
    } else if (phone.startsWith('+254')) {
        phone = phone.substring(1);
    } else if (!phone.startsWith('254')) {
        showVoteError('Invalid phone number. Use 07XXXXXXXX format.');
        return;
    }
    phone = phone.replace(/[^0-9]/g, '');

    const data = {
        nominee_id: document.getElementById('modalNomineeId').value,
        contest_id: {{ $contest->id }},
        votes: document.getElementById('voteCount').value,
        phone: phone
    };

    // Feedback elements
    const feedback = document.getElementById('voteFeedback');
    const progress = document.getElementById('voteProgress');
    const message = document.getElementById('voteMessage');
    feedback.classList.remove('hidden');
    progress.style.width = '0%';
    message.textContent = 'Processing payment...';
    message.classList.remove('text-red-500','text-green-600');
    message.classList.add('text-gray-700');

    // Animate progress bar
    let width = 0;
    const interval = setInterval(() => {
        if(width >= 90) clearInterval(interval);
        width += 10;
        progress.style.width = width + '%';
    }, 300);

    fetch(form.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(res => {
        clearInterval(interval);
        progress.style.width = '100%';

        if(res.mpesa_response && res.mpesa_response.ResponseCode === "0") {
            message.textContent = 'STK Push sent! Check your phone to complete the payment.';
            message.classList.remove('text-red-500');
            message.classList.add('text-green-600');
        } else if(res.mpesa_response && res.mpesa_response.errorMessage) {
            showVoteError(res.mpesa_response.errorMessage);
        } else if(res.error) {
            showVoteError(res.error);
        } else {
            message.textContent = 'Payment request sent. Check your phone.';
            message.classList.remove('text-red-500');
            message.classList.add('text-gray-700');
        }
    })
    .catch(err => {
        console.error(err);
        showVoteError('Error initiating payment. Try again.', progress, message);
    });


});

</script> -->


@endsection
