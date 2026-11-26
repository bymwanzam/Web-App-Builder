<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} &middot; {{ \App\Models\SystemSetting::get('site_name', config('app.name')) }}</title>
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

    <!-- ApexCharts (for dashboard charts) -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.min.js"></script>
    @stack('scripts-head')

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-gray-900 flex flex-col min-h-screen">
    <!-- Preloader -->
    <x-preloader />

    <!-- Sidebar (persisted across SPA navigation) -->
    @persist('sidebar')
        <x-sidebar />
    @endpersist

    <!-- Main Content -->
    <div class="p-4 sm:ml-64 flex-1">
        <div class="max-w-7xl mx-auto flex flex-col min-h-full">
            <div class="flex-1 page-ready" id="main-content">
                {{ $slot }}
            </div>
        </div>
    </div>

    <!-- Footer -->
    <x-footer />

    <!-- Modals (rendered outside main content to cover entire page) -->
    @stack('modals')

    <!-- Toast Notification (rendered at body level for proper fixed positioning) -->
    <livewire:toast-notification />

    <!-- Cookie Consent Banner -->
    <livewire:cookie-consent-banner />

    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>
