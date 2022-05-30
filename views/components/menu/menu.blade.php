@props(['items'])

<nav x-data="{ open: false }">
    <!-- Primary Navigation Menu -->
    <div class="container">
        <div class="flex h-16 justify-between">
            <div class="flex">
                <!-- Logo -->
                <div class="flex shrink-0 items-center">
                    <a href="{{ route('dyndash.home') }}">
                        @if (Config('dyndash.application-mark'))
                            <img src="{{ asset(Config('dyndash.application-mark')) }}" class="block h-10 w-auto"
                                alt="App logo">
                        @else
                            <x-dyndash::menu.application-mark class="block h-10 w-auto" />
                        @endif

                    </a>
                </div>

                <!-- Main Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    
                    @foreach ($items as $key => $item)
                        <x-dyndash::menu.nav-link :active="$item['active']" href="{{ route($item['route'], [$item['parameter'] ?? null]) }}">
                            {{ $item['name'] }}
                        </x-dyndash::menu.nav-link>
                    @endforeach
                </div>
            </div>

            <div class="hidden sm:ml-6 sm:flex sm:items-center">

                <!-- Settings Dropdown -->
                <div class="relative ml-3">
                    <x-dyndash::menu.dropdown>
                        <x-slot name="trigger">

                            <span class="inline-flex rounded-md">
                                <button type="button"
                                    class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition hover:text-gray-700 focus:outline-none">
                                    {{ Auth::user()->name }}

                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </span>

                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            <x-dyndash::menu.dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Profile') }}
                            </x-dyndash::menu.dropdown-link>

                            <div class="border-t border-gray-100"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dyndash::menu.dropdown-link href="{{ route('logout') }}" onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dyndash::menu.dropdown-link>
                            </form>
                        </x-slot>
                    </x-dyndash::menu.dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">

        {{-- Main Links --}}
        <div class="space-y-1 pt-2 pb-3">
            
            @foreach ($items as $key => $item)
                <x-dyndash::menu.responsive-nav-link :active="request()->routeIs('profile.show')"
                    href="{{ route($item['route'], [$item['parameter'] ?? null]) }}">
                    {{ $item['name'] }}
                </x-dyndash::menu.responsive-nav-link>
            @endforeach
        </div>

        <!-- Responsive Settings Options -->
        <div class="border-t border-gray-200 pt-4 pb-1">
            <div class="flex items-center px-4">

                <div>
                    <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="text-sm font-medium text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Account Management -->
                <x-dyndash::menu.responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ __('Profile') }}
                </x-dyndash::menu.responsive-nav-link>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-dyndash::menu.responsive-nav-link href="{{ route('logout') }}" onclick="event.preventDefault();
                                    this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-dyndash::menu.responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
