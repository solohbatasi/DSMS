<div class="relative" x-data="{ open: false }">
    <button
        @click="open = !open"
        class="flex items-center text-sm font-medium text-white-500 hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
        <div>{{ $user->name }}</div>
        <div class="ml-1">
            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </div>
    </button>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-black ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
        style="display: none;"
        @click.away="open = false">
        <div class="py-1">
            <!-- Dashboard -->
            <a
                href="{{ route('dashboard') }}"
                class="block px-4 py-2 text-sm text-white-700 hover:bg-gray-100"
                @click="open = false">
                Dashboard
            </a>

            <!-- Profile -->
            <a
                href="{{ route('profile.edit') }}"
                class="block px-4 py-2 text-sm text-white-700 hover:bg-gray-100"
                @click="open = false">
                Profile
            </a>


            <a
                href="{{ route('dashboard') }}"
                class="block px-4 py-2 text-sm text-white-700 hover:bg-gray-100"
                @click="open = false">
                Dashboard
            </a>


            <!-- Divider -->
            <div class="border-t border-gray-100 my-1"></div>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    class="block w-full text-left px-4 py-2 text-sm text-white-700 hover:bg-gray-100"
                    @click="open = false">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</div>
