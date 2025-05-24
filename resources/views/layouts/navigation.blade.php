<nav x-data="{ open: false }" class="bg-gray-800 border-b border-gray-700">
    @php
        $navCartItems = session('cart.items', []);
        $navCartItemCount = 0;
        if (is_array($navCartItems)) {
            foreach ($navCartItems as $item) {
                $navCartItemCount += $item['quantity'] ?? 0;
            }
        }
    @endphp
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-green-500">EYS</a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')" class="text-gray-300 hover:text-white">
                        Anasayfa
                    </x-nav-link>
                    {{-- <x-nav-link :href="route('events.index')" :active="request()->routeIs('events.*')">
                        Etkinlikler
                    </x-nav-link> --}}
                    {{-- <x-nav-link :href="route('announcements.index')" :active="request()->routeIs('announcements.*')" class="text-gray-300 hover:text-white">
                        Duyurular
                    </x-nav-link> --}}
                    <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')" class="text-gray-300 hover:text-white">
                        Profilim
                    </x-nav-link>
                    <x-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')" class="text-gray-300 hover:text-white">
                        Sepetim <span id="cart-item-count-desktop" class="ml-1 inline-block px-2 py-0.5 text-xs font-semibold leading-none text-red-600 bg-red-100 rounded-full">{{ $navCartItemCount }}</span>
                    </x-nav-link>
                    @if(auth()->check() && auth()->user()->isAdmin())
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')" class="text-gray-300 hover:text-white">
                            Yönetici Paneli
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-300 hover:text-white focus:outline-none transition duration-150 ease-in-out">
                            <div>{{ Auth::user()?->name ?? 'Misafir' }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="text-gray-700 hover:text-gray-900">
                            {{ __('Profilim') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();" class="text-gray-700 hover:text-gray-900">
                                {{ __('Çıkış Yap') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @else
                    <div class="space-x-4">
                        <a href="{{ route('login') }}" class="text-sm text-gray-300 hover:text-white">Giriş Yap</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-sm text-gray-300 hover:text-white">Kayıt Ol</a>
                        @endif
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:bg-gray-700 focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')" class="text-gray-300 hover:text-white">
                Anasayfa
            </x-responsive-nav-link>
            {{-- <x-responsive-nav-link :href="route('events.index')" :active="request()->routeIs('events.*')">
                Etkinlikler
            </x-responsive-nav-link> --}}
            {{-- <x-responsive-nav-link :href="route('announcements.index')" :active="request()->routeIs('announcements.*')" class="text-gray-300 hover:text-white">
                Duyurular
            </x-responsive-nav-link> --}}
            <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')" class="text-gray-300 hover:text-white">
                Profilim
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')" class="text-gray-300 hover:text-white">
                Sepetim <span id="cart-item-count-mobile" class="ml-1 inline-block px-2 py-0.5 text-xs font-semibold leading-none text-red-600 bg-red-100 rounded-full">{{ $navCartItemCount }}</span>
            </x-responsive-nav-link>
            @if(auth()->check() && auth()->user()->isAdmin())
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')" class="text-gray-300 hover:text-white">
                    Yönetici Paneli
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-700">
            @auth
            <div class="px-4">
                <div class="font-medium text-base text-gray-300">{{ Auth::user()?->name ?? 'Misafir' }}</div>
                <div class="font-medium text-sm text-gray-400">{{ Auth::user()?->email ?? '' }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" class="text-gray-300 hover:text-white">
                    {{ __('Profilim') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();" class="text-gray-300 hover:text-white">
                        {{ __('Çıkış Yap') }}
                    </x-responsive-nav-link>
                </form>
            </div>
            @else
                <div class="px-4 space-y-1">
                    <x-responsive-nav-link :href="route('login')" class="text-gray-300 hover:text-white">
                        {{ __('Giriş Yap') }}
                    </x-responsive-nav-link>
                    @if (Route::has('register'))
                        <x-responsive-nav-link :href="route('register')" class="text-gray-300 hover:text-white">
                            {{ __('Kayıt Ol') }}
                        </x-responsive-nav-link>
                    @endif
                </div>
            @endauth
        </div>
    </div>
</nav>
