<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $siteName = \App\Models\SystemSetting::get('site_name', config('app.name'));
        $siteDescription = \App\Models\SystemSetting::get('site_description', 'Build native mobile apps from your website in minutes');
        $faviconPath = \App\Models\SystemSetting::get('favicon_path');
    @endphp

    <title>{{ $siteName }} - Build Native Mobile Apps in Minutes</title>
    <meta name="description" content="{{ $siteDescription }}">

    @if($faviconPath)
        <link rel="icon" href="{{ Storage::url($faviconPath) }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif

    <!-- Dark Mode Script (must be before body renders) -->
    <script>
        window.defaultTheme = '{{ \App\Models\SystemSetting::get("default_theme", "system") }}';

        (function() {
            const defaultTheme = window.defaultTheme;
            const savedTheme = localStorage.getItem('color-theme');

            let isDark = false;
            if (savedTheme) {
                isDark = savedTheme === 'dark';
            } else if (defaultTheme === 'system') {
                isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            } else {
                isDark = defaultTheme === 'dark';
            }

            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-white dark:bg-gray-900">
    <!-- Demo Iframe Blocker (CodeCanyon Preview) -->
    <x-landing.demo-iframe-blocker />

    <!-- Preloader -->
    <x-preloader />

    <!-- Header Navigation -->
    <x-landing.header />

    <!-- Hero Section -->
    <x-landing.hero />

    <!-- How It Works Section -->
    <x-landing.how-it-works />

    <!-- Features Section -->
    <x-landing.features />

    <!-- Pricing Section -->
    <x-landing.pricing />

    <!-- Testimonials Section -->
    <x-landing.testimonials />

    <!-- Social Proof Section -->
    <x-landing.social-proof />

    <!-- Company Logos Section -->
    <x-landing.logos />

    <!-- CTA Section -->
    <x-landing.cta />

    <!-- Footer -->
    <x-landing.footer />

    <!-- Scroll to Top Button -->
    <button
        x-data="{ show: false }"
        x-init="window.addEventListener('scroll', () => { show = window.scrollY > 500 })"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        x-cloak
        type="button"
        class="fixed bottom-6 right-6 z-50 p-3 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 transition-colors"
        aria-label="Scroll to top"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
        </svg>
    </button>

    <!-- Cookie Consent Banner -->
    <livewire:cookie-consent-banner />

    @livewireScripts

    <!-- Handle hash scrolling after Livewire navigation -->
    <script>
        document.addEventListener('livewire:navigated', () => {
            const hash = window.location.hash;
            if (hash) {
                const element = document.querySelector(hash);
                if (element) {
                    setTimeout(() => {
                        element.scrollIntoView({ behavior: 'smooth' });
                    }, 100);
                }
            }
        });
    </script>
</body>
</html>
