<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Installation' }} - {{ config('app.name', 'Appy') }}</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Dark mode script - prevent flash -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col bg-gray-50 dark:bg-gray-900">
        <!-- Dark Mode Toggle - Top Right -->
        <div class="absolute top-4 right-4 z-10">
            <button id="theme-toggle" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-hidden focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                </svg>
                <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
            <div class="w-full max-w-2xl">
                <!-- Logo - Centered at Top -->
                <div class="flex items-center justify-center mb-8">
                    @php
                        try {
                            $logoPath = \App\Models\SystemSetting::get('logo_path');
                            $logoDarkPath = \App\Models\SystemSetting::get('logo_dark_path');
                            $siteName = \App\Models\SystemSetting::get('site_name', 'Appy');
                            $siteTagline = \App\Models\SystemSetting::get('site_tagline', 'Build native apps');
                        } catch (\Exception $e) {
                            // Database not set up yet, use defaults
                            $logoPath = null;
                            $logoDarkPath = null;
                            $siteName = 'Appy';
                            $siteTagline = 'Build native apps';
                        }
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
                        {{-- Default logo - same as guest layout --}}
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
                </div>
                <!-- Progress Steps -->
                @if (isset($currentStep))
                <div class="mb-8">
                    <div class="flex items-center justify-center">
                        @php
                            $steps = [
                                ['name' => 'Requirements', 'route' => 'install.requirements'],
                                ['name' => 'Permissions', 'route' => 'install.permissions'],
                                ['name' => 'Database', 'route' => 'install.database'],
                                ['name' => 'Admin', 'route' => 'install.admin'],
                                ['name' => 'Complete', 'route' => 'install.completed'],
                            ];
                        @endphp

                        @foreach ($steps as $index => $step)
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $currentStep >= $index + 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                                    {{ $index + 1 }}
                                </div>
                                @if ($index < count($steps) - 1)
                                    <div class="w-12 h-1 mx-2 {{ $currentStep > $index + 1 ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-700' }}"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="flex items-center justify-center mt-3">
                        @foreach ($steps as $index => $step)
                            <div class="flex items-center">
                                <div class="text-xs text-center {{ $currentStep >= $index + 1 ? 'text-blue-600 dark:text-blue-500 font-medium' : 'text-gray-500 dark:text-gray-400' }}" style="width: 80px;">
                                    {{ $step['name'] }}
                                </div>
                                @if ($index < count($steps) - 1)
                                    <div class="w-12"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Card -->
                <div class="bg-white rounded-lg shadow dark:border dark:bg-gray-800 dark:border-gray-700">
                    <!-- Card Header -->
                    <div class="p-6 sm:p-8 border-b border-gray-200 dark:border-gray-700">
                        @yield('header')
                    </div>

                    <!-- Card Body -->
                    <div class="p-6 sm:p-8">
                        <!-- Error Messages -->
                        @if ($errors->any())
                            <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400" role="alert">
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Success Messages -->
                        @if (session('success'))
                            <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- Info Messages -->
                        @if (session('error'))
                            <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        @yield('content')
                    </div>

                    <!-- Card Footer -->
                    @hasSection('footer')
                    <div class="p-6 sm:p-8 border-t border-gray-200 dark:border-gray-700">
                        @yield('footer')
                    </div>
                    @endif
                </div>

                <!-- Copyright -->
                <div class="mt-8 text-center text-sm text-gray-500 dark:text-gray-400">
                    <p>&copy; {{ date('Y') }}. All Rights Reserved.</p>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts

    <!-- Dark mode toggle script -->
    <script>
        var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Change the icons inside the button based on previous settings
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        var themeToggleBtn = document.getElementById('theme-toggle');

        themeToggleBtn.addEventListener('click', function() {
            // toggle icons inside button
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            // if set via local storage previously
            if (localStorage.getItem('color-theme')) {
                if (localStorage.getItem('color-theme') === 'light') {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                }
            } else {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('color-theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('color-theme', 'dark');
                }
            }
        });
    </script>
</body>
</html>
