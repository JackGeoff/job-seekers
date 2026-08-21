<nav class="sticky top-0 z-50 border-b border-brand-100 bg-white/95 shadow-sm shadow-brand-900/5 backdrop-blur" x-data="{ mobileOpen: false, userMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center rounded-lg focus-visible:outline-brand-500">
                <img src="{{ asset('images/jobseekers-logo.png') }}" alt="Job Seekers" class="brand-logo">
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center gap-8">
                <a href="#" class="text-slate-600 hover:text-brand-600 text-sm font-medium transition-colors">Browse Jobs</a>
                <a href="#" class="text-slate-600 hover:text-brand-600 text-sm font-medium transition-colors">Companies</a>
                <a href="#" class="text-slate-600 hover:text-brand-600 text-sm font-medium transition-colors">Resources</a>
            </div>

            <!-- Right side actions -->
            <div class="flex items-center gap-4">
                @auth
                    <!-- Desktop User Menu -->
                    <div class="hidden md:flex items-center gap-4">
                        <div class="relative" @click.outside="userMenuOpen = false">
                            <button
                                @click="userMenuOpen = !userMenuOpen"
                                class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-neutral-100 transition-colors"
                            >
                                <span class="text-sm font-medium text-neutral-700">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-neutral-600" :class="{ 'rotate-180': userMenuOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                            </button>

                            <!-- User Dropdown Menu -->
                            <div
                                x-show="userMenuOpen"
                                x-transition
                                class="absolute right-0 mt-2 w-48 bg-white border border-neutral-200 rounded-lg shadow-lg"
                            >
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50 transition-colors">Dashboard</a>
                                <a href="#" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50 transition-colors">Profile Settings</a>
                                <a href="#" class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-50 transition-colors">Preferences</a>
                                <div class="border-t border-neutral-200"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-error-600 hover:bg-neutral-50 transition-colors">Sign out</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <button
                        @click="mobileOpen = !mobileOpen"
                        class="md:hidden p-2 rounded-lg hover:bg-neutral-100"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                @else
                    <a href="{{ route('login') }}" class="rounded-xl px-4 py-2 text-sm font-semibold text-brand-700 hover:bg-brand-50">Sign in</a>
                    <a href="{{ route('register') }}" class="brand-btn min-h-10 px-4 py-2 text-sm">Get Started</a>
                @endauth
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        @auth
        <div
            x-show="mobileOpen"
            x-transition
            class="md:hidden border-t border-neutral-200"
        >
            <div class="px-4 py-4 space-y-3">
                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-neutral-700 hover:bg-neutral-50 rounded-lg transition-colors">Dashboard</a>
                <a href="#" class="block px-4 py-2 text-neutral-700 hover:bg-neutral-50 rounded-lg transition-colors">Browse Jobs</a>
                <a href="#" class="block px-4 py-2 text-neutral-700 hover:bg-neutral-50 rounded-lg transition-colors">Companies</a>
                <a href="#" class="block px-4 py-2 text-neutral-700 hover:bg-neutral-50 rounded-lg transition-colors">Resources</a>
                <a href="#" class="block px-4 py-2 text-neutral-700 hover:bg-neutral-50 rounded-lg transition-colors">Profile Settings</a>
                <form method="POST" action="{{ route('logout') }}" class="pt-3 border-t border-neutral-200">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-error-600 hover:bg-neutral-50 rounded-lg transition-colors">Sign out</button>
                </form>
            </div>
        </div>
        @endauth
    </div>
</nav>
