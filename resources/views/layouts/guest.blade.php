<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Login' }} - {{ \App\Models\SystemSetting::get('site_name', config('app.name')) }}</title>
    @if($siteDescription = \App\Models\SystemSetting::get('site_description'))
        <meta name="description" content="{{ $siteDescription }}">
    @endif
    @if($faviconPath = \App\Models\SystemSetting::get('favicon_path'))
        <link rel="icon" href="{{ Storage::url($faviconPath) }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif

    <!-- Dark Mode Script (must be before body renders) -->
    <script>
        // Expose admin default theme globally for use by app.js
        window.defaultTheme = '{{ \App\Models\SystemSetting::get("default_theme", "system") }}';

        (function() {
            const defaultTheme = window.defaultTheme;
            const savedTheme = localStorage.getItem('color-theme');

            let isDark = false;
            if (savedTheme) {
                // User has explicit preference
                isDark = savedTheme === 'dark';
            } else if (defaultTheme === 'system') {
                // Follow system preference
                isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            } else {
                // Use admin default
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

    <!-- reCAPTCHA v3 Script -->
    @php
        $recaptchaEnabled = \App\Services\RecaptchaService::isEnabled();
        $recaptchaSiteKey = \App\Services\RecaptchaService::getSiteKey();
    @endphp
    @if($recaptchaEnabled && $recaptchaSiteKey)
        <script src="https://www.google.com/recaptcha/api.js?render={{ $recaptchaSiteKey }}"></script>
    @endif
</head>
<body>
    <!-- Preloader -->
    <x-preloader />

    <section class="bg-gray-50 dark:bg-gray-900">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
            <a href="{{ route('login') }}" wire:navigate class="flex items-center mb-6">
                @php
                    $logoPath = \App\Models\SystemSetting::get('logo_path');
                    $logoDarkPath = \App\Models\SystemSetting::get('logo_dark_path');
                    $siteName = \App\Models\SystemSetting::get('site_name', config('app.name'));
                    $siteTagline = \App\Models\SystemSetting::get('site_tagline', 'Build native apps');
                @endphp
                @if($logoPath && $logoDarkPath)
                    {{-- Both logos available - show based on theme --}}
                    <img src="{{ Storage::url($logoPath) }}" alt="{{ $siteName }}" class="h-14 w-auto dark:hidden">
                    <img src="{{ Storage::url($logoDarkPath) }}" alt="{{ $siteName }}" class="h-14 w-auto hidden dark:block">
                @elseif($logoPath)
                    {{-- Only light logo --}}
                    <img src="{{ Storage::url($logoPath) }}" alt="{{ $siteName }}" class="h-14 w-auto">
                @elseif($logoDarkPath)
                    {{-- Only dark logo --}}
                    <img src="{{ Storage::url($logoDarkPath) }}" alt="{{ $siteName }}" class="h-14 w-auto">
                @else
                    <div class="relative">
                        <div class="w-11 h-11 bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20">
                            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.537 12.625a4.421 4.421 0 0 0 2.684 4.047 10.96 10.96 0 0 1-1.384 2.845c-.834 1.218-1.7 2.432-3.062 2.457-1.34.025-1.77-.794-3.3-.794-1.531 0-2.01.769-3.275.82-1.316.049-2.317-1.318-3.158-2.532-1.72-2.484-3.032-7.017-1.27-10.077A4.9 4.9 0 0 1 8.91 6.884c1.292-.025 2.51.869 3.3.869.789 0 2.27-1.075 3.828-.917a4.67 4.67 0 0 1 3.66 1.984 4.524 4.524 0 0 0-2.16 3.805m-2.52-7.432A4.4 4.4 0 0 0 16.06 2a4.482 4.482 0 0 0-2.945 1.516 4.185 4.185 0 0 0-1.061 3.093 3.708 3.708 0 0 0 2.967-1.416Z"/>
                            </svg>
                        </div>
                        <div class="absolute -top-0.5 -right-0.5 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-gray-900"></div>
                    </div>
                    <div class="ml-3">
                        <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 dark:from-blue-400 dark:to-blue-600 bg-clip-text text-transparent">{{ $siteName }}</span>
                        <p class="text-[10px] font-medium text-gray-500 dark:text-gray-400 mt-0.5">{{ $siteTagline }}</p>
                    </div>
                @endif
            </a>
            <div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 {{ request()->routeIs('register') ? 'sm:max-w-2xl' : 'sm:max-w-md' }} xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </section>

    {{-- Cookie Consent Banner --}}
    <livewire:cookie-consent-banner />
</body>
</html>
