@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Carbon;
@endphp
@props([
    'title' => 'Page Title',
    'subtitle' => '',
    'actionButtons' => []
])

<!-- Alpine.js Menu System -->
<script src="//unpkg.com/alpinejs" defer></script>

<div x-data="dashboardMenu()" class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">

        <!-- Logo + Page Titles -->
        <div class="flex items-center space-x-4">
            <div class="h-10 w-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-lg">J</span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
                <p class="text-sm text-gray-500">{{ $subtitle }}</p>
            </div>
        </div>

        <!-- RIGHT SIDE SECTION -->
        <div class="flex items-center space-x-3">

            <!-- Desktop Navigation Buttons -->
            <div class="hidden md:flex items-center gap-2">
                <template x-for="button in buttons" :key="button.name">
                    <a :href="button.href"
                       class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium shadow-sm border"
                       :class="button.type === 'primary'
                            ? 'text-white bg-blue-600 hover:bg-blue-700 border-blue-700'
                            : 'text-gray-700 bg-white hover:bg-gray-100 border-gray-300'">
                        <i :class="button.icon" class="w-4 h-4 mr-2"></i>
                        <span x-text="button.name"></span>
                    </a>
                </template>


            </div>

            <!-- ACTION BUTTON (ALWAYS VISIBLE, ALL SCREEN SIZES) -->
            @foreach($actionButtons as $btn)

                {{-- Modal Button --}}
                @if(isset($btn['onclick']))
                    <button onclick="{{ $btn['onclick'] }}"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white
                        {{ $btn['color'] ?? 'bg-green-600 hover:bg-green-700' }} shadow">
                        @if(isset($btn['icon']))
                            <i class="{{ $btn['icon'] }} mr-2"></i>
                        @endif
                        {{ $btn['text'] }}
                    </button>

                {{-- Link Button --}}
                @elseif(isset($btn['href']))
                    <a href="{{ $btn['href'] }}"
                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white
                        {{ $btn['color'] ?? 'bg-blue-600 hover:bg-blue-700' }} shadow">
                        @if(isset($btn['icon']))
                            <i class="{{ $btn['icon'] }} mr-2"></i>
                        @endif
                        {{ $btn['text'] }}
                    </a>
                @endif

            @endforeach

            <!-- Mobile Menu Toggle (ONLY SHOW on small screens) -->
            <div class="mobile-toggle">
                <button @click="mobileMenuOpen = true" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <style>
            /* Hide toggle at ≥ 768px (md) */
            @media (min-width: 768px) {
                .mobile-toggle { display: none !important; }
            }
            </style>

        </div>
    </div>

    <!-- Mobile Sidebar Menu -->
    <div x-show="mobileMenuOpen" x-transition x-cloak class="fixed inset-0 z-50 flex">
        <div class="bg-black bg-opacity-50 w-full" @click="mobileMenuOpen = false"></div>

        <div class="bg-white w-64 p-4 flex flex-col space-y-3 overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-bold text-lg">Menu</h2>
                <button @click="mobileMenuOpen = false" class="text-gray-700 hover:text-black">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <template x-for="button in buttons" :key="button.name">
                <a :href="button.href"
                    @click="mobileMenuOpen = false"
                    class="flex items-center px-4 py-2 rounded-lg hover:bg-gray-100 text-gray-800">
                    <i :class="button.icon" class="w-4 h-4 mr-2"></i>
                    <span x-text="button.name"></span>
                </a>
            </template>



        </div>
    </div>
</div>

<script>
function dashboardMenu() {
    return {
        mobileMenuOpen: false,
        buttons: [
            @can('view students')
            { name: 'Learners', href: '{{ route("students.index") }}', type: 'primary', icon: 'fas fa-tachometer-alt' },
            @endcan
            @can('view results')
            { name: 'Courses', href: '{{ route("courses.index") }}', type: 'primary', icon: 'fas fa-file-invoice' },
            @endcan
            @can('view payments')
            { name: 'Payments', href: '{{ route("payments.index") }}', type: 'primary', icon: 'fas fa-credit-card' },
            @endcan
            @can('view expenses')
            { name: 'Expenditures', href: '{{ route("expenses.index") }}', type: 'primary', icon: 'fas fa-users' },
            @endcan
            @can('manage tickets')
            { name: 'Tickets', href: 'student.tickets', type: 'primary', icon: 'fas fa-ticket-alt' },
            @endcan
            @can('view reports')
            { name: 'Reports', href: '#', type: 'secondary', icon: 'fas fa-chart-line' },
            @endcan
        ]
    }
}
</script>
