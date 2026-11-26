@php
    $logoPath = \App\Models\SystemSetting::get('logo_path');
    $logoDarkPath = \App\Models\SystemSetting::get('logo_dark_path');
    $siteName = \App\Models\SystemSetting::get('site_name', config('app.name'));
    $siteTagline = \App\Models\SystemSetting::get('site_tagline', 'Build native apps');
    $isLandingPage = request()->is('/') || request()->routeIs('home');
@endphp

<header class="fixed w-full z-50 top-0 start-0 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800">
    <nav class="max-w-screen-xl relative flex flex-wrap items-center justify-between mx-auto p-4">
        <!-- Logo -->
        <a href="{{ url('/') }}" @if($isLandingPage) @click.prevent="window.scrollTo({ top: 0, behavior: 'smooth' })" @else wire:navigate @endif class="flex items-center z-10">
            @if($logoPath && $logoDarkPath)
                {{-- Both logos available - show based on theme --}}
                <img src="{{ Storage::url($logoPath) }}" alt="{{ $siteName }}" class="h-10 w-auto dark:hidden">
                <img src="{{ Storage::url($logoDarkPath) }}" alt="{{ $siteName }}" class="h-10 w-auto hidden dark:block">
            @elseif($logoPath)
                {{-- Only light logo --}}
                <img src="{{ Storage::url($logoPath) }}" alt="{{ $siteName }}" class="h-10 w-auto">
            @elseif($logoDarkPath)
                {{-- Only dark logo --}}
                <img src="{{ Storage::url($logoDarkPath) }}" alt="{{ $siteName }}" class="h-10 w-auto">
            @else
                {{-- No logo uploaded - show default icon, site name, and tagline --}}
                <div class="relative">
                    <div class="w-11 h-11 bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.537 12.625a4.421 4.421 0 0 0 2.684 4.047 10.96 10.96 0 0 1-1.384 2.845c-.834 1.218-1.7 2.432-3.062 2.457-1.34.025-1.77-.794-3.3-.794-1.531 0-2.01.769-3.275.82-1.316.049-2.317-1.318-3.158-2.532-1.72-2.484-3.032-7.017-1.27-10.077A4.9 4.9 0 0 1 8.91 6.884c1.292-.025 2.51.869 3.3.869.789 0 2.27-1.075 3.828-.917a4.67 4.67 0 0 1 3.66 1.984 4.524 4.524 0 0 0-2.16 3.805m-2.52-7.432A4.4 4.4 0 0 0 16.06 2a4.482 4.482 0 0 0-2.945 1.516 4.185 4.185 0 0 0-1.061 3.093 3.708 3.708 0 0 0 2.967-1.416Z"/>
                        </svg>
                    </div>
                    <div class="absolute -top-0.5 -right-0.5 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-gray-800"></div>
                </div>
                <div class="ml-3">
                    <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 dark:from-blue-400 dark:to-blue-600 bg-clip-text text-transparent">{{ $siteName }}</span>
                    <p class="text-[10px] font-medium text-gray-500 dark:text-gray-400 mt-0.5">{{ $siteTagline }}</p>
                </div>
            @endif
        </a>

        <!-- Mobile Menu Button & Auth Buttons -->
        <div class="flex md:order-2 space-x-2 md:space-x-0 rtl:space-x-reverse items-center z-10">
            <!-- Theme Toggle Button -->
            <button id="theme-toggle-landing" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-hidden focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                <svg id="theme-toggle-dark-icon-landing" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                </svg>
                <svg id="theme-toggle-light-icon-landing" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/>
                </svg>
                <span class="sr-only">Toggle dark mode</span>
            </button>

            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" wire:navigate class="hidden sm:block text-gray-800 dark:text-white hover:bg-gray-100 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-2 dark:hover:bg-gray-700 focus:outline-hidden dark:focus:ring-gray-800">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" wire:navigate class="hidden sm:block text-gray-800 dark:text-white hover:bg-gray-100 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-2 dark:hover:bg-gray-700 focus:outline-hidden dark:focus:ring-gray-800">
                        Log in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" wire:navigate class="text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20">
                            Get started
                        </a>
                    @endif
                @endauth
            @endif

            <button data-collapse-toggle="navbar-sticky" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-hidden focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-sticky" aria-expanded="false">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
                </svg>
            </button>
        </div>

        <!-- Navigation Links -->
        <div class="items-center justify-between hidden w-full md:flex md:w-auto md:absolute md:left-1/2 md:-translate-x-1/2" id="navbar-sticky">
            <ul class="flex flex-col p-4 md:p-0 mt-4 font-medium border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-transparent dark:bg-gray-800 md:dark:bg-transparent dark:border-gray-700">
                <li>
                    <a href="{{ $isLandingPage ? '#how-it-works' : url('/#how-it-works') }}" @if(!$isLandingPage) wire:navigate @endif class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 md:dark:hover:text-blue-500 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">How It Works</a>
                </li>
                <li>
                    <a href="{{ $isLandingPage ? '#features' : url('/#features') }}" @if(!$isLandingPage) wire:navigate @endif class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 md:dark:hover:text-blue-500 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Features</a>
                </li>
                <li>
                    <a href="{{ $isLandingPage ? '#pricing' : url('/#pricing') }}" @if(!$isLandingPage) wire:navigate @endif class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 md:dark:hover:text-blue-500 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Pricing</a>
                </li>
                <li>
                    <a href="{{ $isLandingPage ? '#testimonials' : url('/#testimonials') }}" @if(!$isLandingPage) wire:navigate @endif class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 md:dark:hover:text-blue-500 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Testimonials</a>
                </li>
                @auth
                    <li class="md:hidden">
                        <a href="{{ url('/dashboard') }}" wire:navigate class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 dark:text-white dark:hover:bg-gray-700">Dashboard</a>
                    </li>
                @else
                    <li class="md:hidden">
                        <a href="{{ route('login') }}" wire:navigate class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0 dark:text-white dark:hover:bg-gray-700">Log in</a>
                    </li>
                @endauth
            </ul>
        </div>
    </nav>
</header>
