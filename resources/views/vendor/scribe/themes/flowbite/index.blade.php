<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $metadata['title'] }}</title>
    @php
        $faviconPath = \App\Models\SystemSetting::get('favicon_path');
    @endphp
    @if($faviconPath)
        <link rel="icon" href="{{ Storage::url($faviconPath) }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif

    {{-- Dark Mode Script (before body renders to prevent flash) --}}
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
            }
        })();
    </script>

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Highlight.js for code syntax --}}
    <link rel="stylesheet" href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/github-dark.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    {{-- Scribe Try It Out --}}
    <script>
        var tryItOutBaseUrl = "{{ $baseUrl }}";
        var useCsrf = Boolean({{ $tryItOut['csrf_url'] ? 'true' : 'false' }});
        var csrfUrl = "{{ $tryItOut['csrf_url'] ?? '' }}";
    </script>
    <script src="{{ asset('/vendor/scribe/js/tryitout-5.6.0.js') }}"></script>

    <style>
        [x-cloak] { display: none !important; }

        /* Custom scrollbar for sidebar */
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 2px; }
        .dark .sidebar-scroll::-webkit-scrollbar-thumb { background: #4b5563; }

        /* Code block styling */
        pre code.hljs { background: transparent !important; padding: 0 !important; }
        .code-block { background: #1e293b; }

        /* Smooth transitions */
        .nav-item { transition: all 0.15s ease; }

        /* Active language tab */
        .lang-tab.active { background-color: rgb(37 99 235); color: white; }
        .dark .lang-tab.active { background-color: rgb(37 99 235); }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 antialiased" x-data="{
    sidebarOpen: false,
    activeSection: 'introduction',
    expandedGroups: { introduction: true, auth: true },
    activeLanguage: 'bash'
}">

    {{-- Mobile Sidebar Overlay --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden"
         @click="sidebarOpen = false"
         x-cloak></div>

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed top-0 left-0 z-50 w-72 h-screen transition-transform lg:translate-x-0">
        <div class="h-full flex flex-col bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700">

            {{-- Logo & Title --}}
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                @php
                    $logoPath = \App\Models\SystemSetting::get('logo_path');
                    $logoDarkPath = \App\Models\SystemSetting::get('logo_dark_path');
                    $siteName = \App\Models\SystemSetting::get('site_name', config('app.name'));
                @endphp
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    @if($logoPath || $logoDarkPath)
                        @if($logoPath)
                            <img src="{{ Storage::url($logoPath) }}" alt="{{ $siteName }}" class="h-8 w-auto dark:hidden">
                        @endif
                        @if($logoDarkPath)
                            <img src="{{ Storage::url($logoDarkPath) }}" alt="{{ $siteName }}" class="h-8 w-auto hidden dark:block">
                        @elseif($logoPath)
                            <img src="{{ Storage::url($logoPath) }}" alt="{{ $siteName }}" class="h-8 w-auto hidden dark:block">
                        @endif
                    @else
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                            </svg>
                        </div>
                    @endif
                    <div>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">API</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 block">Documentation</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Search --}}
            <div class="p-4">
                <div class="relative">
                    <input type="text"
                           id="search-input"
                           placeholder="Search endpoints..."
                           class="w-full pl-10 pr-4 py-2 text-sm bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:text-white dark:placeholder-gray-400">
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto sidebar-scroll px-3 pb-4">
                <ul class="space-y-1">
                    {{-- Introduction --}}
                    <li>
                        <a href="#introduction"
                           class="nav-item flex items-center p-2 rounded-lg text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700"
                           :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400': activeSection === 'introduction' }">
                            <svg class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-medium">Introduction</span>
                        </a>
                    </li>

                    {{-- Authentication --}}
                    <li>
                        <a href="#authenticating-requests"
                           class="nav-item flex items-center p-2 rounded-lg text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700"
                           :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400': activeSection === 'authenticating-requests' }">
                            <svg class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            <span class="text-sm font-medium">Authentication</span>
                        </a>
                    </li>

                    {{-- Endpoint Groups --}}
                    @foreach($groupedEndpoints as $group)
                        @php
                            $groupSlug = Str::slug($group['name']);
                        @endphp
                        <li>
                            <button @click="expandedGroups['{{ $groupSlug }}'] = !expandedGroups['{{ $groupSlug }}']"
                                    class="nav-item w-full flex items-center justify-between p-2 rounded-lg text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                    </svg>
                                    <span class="text-sm font-medium">{{ $group['name'] }}</span>
                                </span>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': expandedGroups['{{ $groupSlug }}'] }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <ul x-show="expandedGroups['{{ $groupSlug }}']" x-collapse class="pl-8 mt-1 space-y-1">
                                @foreach($group['endpoints'] as $endpoint)
                                    @php
                                        $endpointId = $group['name'] . '-' . implode('', $endpoint->httpMethods) . str_replace(['/', '{', '}'], ['-', '-', '-'], $endpoint->uri);
                                        $endpointId = Str::slug($endpointId);
                                    @endphp
                                    <li>
                                        <a href="#{{ $endpointId }}"
                                           class="nav-item flex items-center gap-2 p-2 text-sm rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white">
                                            @php $method = $endpoint->httpMethods[0]; @endphp
                                            <span class="px-1.5 py-0.5 text-[10px] font-bold rounded
                                                @if($method === 'GET') bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400
                                                @elseif($method === 'POST') bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400
                                                @elseif($method === 'PUT' || $method === 'PATCH') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-400
                                                @elseif($method === 'DELETE') bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400
                                                @endif">
                                                {{ $method }}
                                            </span>
                                            <span class="truncate">{{ $endpoint->metadata->title ?? $endpoint->uri }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>

                {{-- Footer Links --}}
                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('scribe.postman') }}" class="flex items-center p-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13.527.099C6.955-.744.942 3.9.099 10.473c-.843 6.572 3.8 12.584 10.373 13.428 6.573.843 12.587-3.801 13.428-10.374C24.744 6.955 20.101.943 13.527.099zm2.471 7.485a.855.855 0 0 0-.593.25l-4.453 4.453-.307-.307-.643-.643c4.389-4.376 5.18-4.418 5.996-3.753zm-4.863 4.861l4.44-4.44a.62.62 0 1 1 .847.903l-4.699 4.125-.588-.588zm.33.694l-1.1.238a.06.06 0 0 1-.067-.032.06.06 0 0 1 .01-.073l.645-.645.512.512zm-2.803-.459l1.172-1.172.879.878-1.979.426a.074.074 0 0 1-.085-.039.072.072 0 0 1 .013-.093zm-1.463 1.462l.291-.293.478.479-.776.168a.054.054 0 0 1-.063-.028.053.053 0 0 1 .009-.068l.061-.258zm-.29.727l1.463-.315.64.639-2.324.503a.106.106 0 0 1-.088-.026.104.104 0 0 1-.033-.082c.004-.04.019-.077.044-.107l.298-.612zm-2.234 3.46a.089.089 0 0 1 .073-.017l2.357-.51.641.64-3.125.676a.071.071 0 0 1-.073-.019.072.072 0 0 1-.012-.08l.139-.69zm-.552 1.655a.089.089 0 0 1-.005-.089.088.088 0 0 1 .076-.048h.001l3.606-.778.398.398a.57.57 0 0 1-.123.034l-3.953.483zm5.258-.384l-1.097-1.097 3.68-.795-.263.263a.56.56 0 0 1-.392.162h-.001a.56.56 0 0 1-.387-.152l-.232-.233-1.308 1.852z"/>
                        </svg>
                        Postman Collection
                    </a>
                    <a href="{{ route('scribe.openapi') }}" class="flex items-center p-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0C5.383 0 0 5.383 0 12s5.383 12 12 12c6.616 0 12-5.383 12-12S18.616 0 12 0zm0 1.144c5.995 0 10.856 4.86 10.856 10.856 0 5.995-4.86 10.856-10.856 10.856C6.004 22.856 1.144 17.996 1.144 12 1.144 6.004 6.004 1.144 12 1.144zM8.37 5.868a6.707 6.707 0 0 0-.423.005c-.983.056-1.573.517-1.735 1.472-.115.665-.096 1.348-.143 2.017-.013.35-.05.697-.115 1.038-.134.609-.397.887-1.006.997-.063.012-.12.032-.194.053v.868c.54.053.887.152 1.063.472.193.348.241.742.263 1.134.035.726.009 1.456.064 2.181.074.854.39 1.307 1.246 1.508.236.056.49.074.736.105v-.872c-.333-.053-.63-.102-.884-.221-.263-.125-.386-.373-.415-.651-.053-.488-.043-.983-.072-1.475a5.47 5.47 0 0 0-.158-1.104c-.134-.508-.39-.907-.871-1.104.473-.222.727-.611.859-1.104.094-.347.134-.71.16-1.068.028-.506.006-1.015.063-1.518.072-.609.35-.856.961-.915.18-.018.362-.018.545-.028v-.862c-.025-.001-.045-.003-.065-.003zm7.325.005v.862c.596.047.946.139 1.089.555.084.242.104.506.118.763.028.506-.007 1.015.049 1.518.036.288.076.58.147.859.128.508.382.906.86 1.106-.47.202-.723.596-.857 1.104-.094.347-.135.71-.158 1.068-.028.506-.006 1.015-.065 1.518-.074.609-.353.856-.963.915-.18.016-.362.016-.545.028v.869c.063.006.11.015.158.015.964-.009 1.628-.399 1.878-1.278.063-.221.098-.455.107-.685.028-.726.006-1.456.065-2.18.037-.46.123-.88.418-1.224.201-.234.477-.32.794-.379v-.869c-.616-.107-.878-.386-1.012-.996a5.559 5.559 0 0 1-.115-1.038c-.046-.67-.028-1.353-.143-2.017-.169-.984-.791-1.441-1.81-1.491a5.107 5.107 0 0 0-.015-.025z"/>
                        </svg>
                        OpenAPI Spec
                    </a>
                </div>
            </nav>
        </div>
    </aside>

    {{-- Main Content --}}
    <div class="lg:ml-72">
        {{-- Top Bar --}}
        <header class="sticky top-0 z-30 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between px-4 py-3">
                <div class="flex items-center gap-4">
                    {{-- Mobile menu button --}}
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div class="hidden sm:block">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Base URL:</span>
                        <code class="ml-2 px-2 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded">{{ $baseUrl }}</code>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    {{-- Language Selector --}}
                    <div class="hidden sm:flex items-center gap-1 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                        <button @click="activeLanguage = 'bash'"
                                :class="activeLanguage === 'bash' ? 'bg-white dark:bg-gray-600 shadow-xs' : ''"
                                class="lang-tab px-3 py-1 text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 transition-all">
                            cURL
                        </button>
                        <button @click="activeLanguage = 'javascript'"
                                :class="activeLanguage === 'javascript' ? 'bg-white dark:bg-gray-600 shadow-xs' : ''"
                                class="lang-tab px-3 py-1 text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 transition-all">
                            JavaScript
                        </button>
                        <button @click="activeLanguage = 'php'"
                                :class="activeLanguage === 'php' ? 'bg-white dark:bg-gray-600 shadow-xs' : ''"
                                class="lang-tab px-3 py-1 text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 transition-all">
                            PHP
                        </button>
                    </div>

                    {{-- Dark Mode Toggle --}}
                    <button id="theme-toggle-docs" type="button" class="p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                        <svg id="theme-toggle-dark-icon-docs" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                        </svg>
                        <svg id="theme-toggle-light-icon-docs" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/>
                        </svg>
                    </button>

                    {{-- Back to Dashboard --}}
                    <a href="{{ url('/user/apps') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Dashboard
                    </a>
                </div>
            </div>
        </header>

        {{-- Content --}}
        <main class="p-4 md:p-6 lg:p-8 max-w-5xl">
            {{-- Introduction --}}
            <section id="introduction" class="mb-12">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ $metadata['title'] }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mb-6">{{ $metadata['description'] ?? '' }}</p>

                @if($intro)
                    <div class="prose dark:prose-invert max-w-none">
                        {!! $intro !!}
                    </div>
                @endif
            </section>

            {{-- Authentication --}}
            <section id="authenticating-requests" class="mb-12">
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                        Authenticating Requests
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        To authenticate requests, include an <code class="px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-sm font-mono">Authorization</code> header with the value <code class="px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-sm font-mono">"Bearer {'{YOUR_API_TOKEN}'}"</code>.
                    </p>
                    <p class="text-gray-600 dark:text-gray-400">
                        All authenticated endpoints are marked with a <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-400 rounded">requires authentication</span> badge.
                    </p>
                    @if($auth['extra_info'] ?? false)
                        <p class="mt-4 text-gray-600 dark:text-gray-400">{!! $auth['extra_info'] !!}</p>
                    @endif
                </div>
            </section>

            {{-- Endpoint Groups --}}
            @foreach($groupedEndpoints as $group)
                <section id="{{ Str::slug($group['name']) }}" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $group['name'] }}</h2>
                    @if($group['description'] ?? false)
                        <p class="text-gray-600 dark:text-gray-400 mb-6">{{ $group['description'] }}</p>
                    @endif

                    {{-- Endpoints --}}
                    @foreach($group['endpoints'] as $endpoint)
                        @php
                            $endpointId = $group['name'] . '-' . implode('', $endpoint->httpMethods) . str_replace(['/', '{', '}'], ['-', '-', '-'], $endpoint->uri);
                            $endpointId = Str::slug($endpointId);
                            $method = $endpoint->httpMethods[0];
                            $formId = implode('', $endpoint->httpMethods) . str_replace(['/', '{', '}', '-'], '', $endpoint->uri);
                        @endphp

                        <div id="{{ $endpointId }}" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 mb-6 overflow-hidden">
                            {{-- Endpoint Header --}}
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-md
                                        @if($method === 'GET') bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400
                                        @elseif($method === 'POST') bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400
                                        @elseif($method === 'PUT' || $method === 'PATCH') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-400
                                        @elseif($method === 'DELETE') bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400
                                        @endif">
                                        {{ $method }}
                                    </span>
                                    <code class="text-sm font-mono text-gray-800 dark:text-gray-200">{{ $endpoint->uri }}</code>
                                    @if($endpoint->metadata->authenticated ?? false)
                                        <span class="px-2 py-0.5 text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-400 rounded">requires authentication</span>
                                    @endif
                                </div>
                                <h3 class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">{{ $endpoint->metadata->title ?? '' }}</h3>
                            </div>

                            <div class="grid lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-gray-200 dark:divide-gray-700">
                                {{-- Left: Description & Parameters --}}
                                <div class="p-6">
                                    @if($endpoint->metadata->description ?? false)
                                        <p class="text-gray-600 dark:text-gray-400 mb-6">{{ $endpoint->metadata->description }}</p>
                                    @endif

                                    {{-- URL Parameters --}}
                                    @if(count($endpoint->urlParameters) > 0)
                                        <div class="mb-6">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                                <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                                                URL Parameters
                                            </h4>
                                            <div class="space-y-3">
                                                @foreach($endpoint->urlParameters as $name => $param)
                                                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                                        <div class="flex items-center gap-2 mb-1">
                                                            <code class="text-sm font-mono text-purple-600 dark:text-purple-400">{{ $name }}</code>
                                                            <span class="text-xs text-gray-500">{{ $param['type'] ?? 'string' }}</span>
                                                            @if($param['required'] ?? false)
                                                                <span class="text-xs text-red-500">required</span>
                                                            @endif
                                                        </div>
                                                        @if($param['description'] ?? false)
                                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $param['description'] }}</p>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Query Parameters --}}
                                    @if(count($endpoint->queryParameters) > 0)
                                        <div class="mb-6">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                                Query Parameters
                                            </h4>
                                            <div class="space-y-3">
                                                @foreach($endpoint->queryParameters as $name => $param)
                                                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                                        <div class="flex items-center gap-2 mb-1">
                                                            <code class="text-sm font-mono text-blue-600 dark:text-blue-400">{{ $name }}</code>
                                                            <span class="text-xs text-gray-500">{{ $param['type'] ?? 'string' }}</span>
                                                            @if($param['required'] ?? false)
                                                                <span class="text-xs text-red-500">required</span>
                                                            @endif
                                                        </div>
                                                        @if($param['description'] ?? false)
                                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $param['description'] }}</p>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Body Parameters --}}
                                    @if(count($endpoint->bodyParameters) > 0)
                                        <div class="mb-6">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                                Body Parameters
                                            </h4>
                                            <div class="space-y-3">
                                                @foreach($endpoint->bodyParameters as $name => $param)
                                                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                                        <div class="flex items-center gap-2 mb-1">
                                                            <code class="text-sm font-mono text-green-600 dark:text-green-400">{{ $name }}</code>
                                                            <span class="text-xs text-gray-500">{{ $param['type'] ?? 'string' }}</span>
                                                            @if($param['required'] ?? false)
                                                                <span class="text-xs text-red-500">required</span>
                                                            @endif
                                                        </div>
                                                        @if($param['description'] ?? false)
                                                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $param['description'] }}</p>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Response Examples --}}
                                    @if(count($endpoint->responses) > 0)
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                                <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                                                Response Examples
                                            </h4>
                                            @foreach($endpoint->responses as $response)
                                                <div class="mb-3">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <span class="px-2 py-0.5 text-xs font-medium rounded
                                                            @if($response['status'] >= 200 && $response['status'] < 300) bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400
                                                            @elseif($response['status'] >= 400) bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400
                                                            @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400
                                                            @endif">
                                                            {{ $response['status'] }}
                                                        </span>
                                                        @if($response['description'] ?? false)
                                                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $response['description'] }}</span>
                                                        @endif
                                                    </div>
                                                    @if($response['content'] ?? false)
                                                        <pre class="p-4 bg-gray-900 rounded-lg overflow-x-auto"><code class="language-json text-sm">{{ is_string($response['content']) ? $response['content'] : json_encode(json_decode($response['content']), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                {{-- Right: Code Examples & Try It Out --}}
                                <div class="p-6 bg-gray-50 dark:bg-gray-900/50">
                                    {{-- Code Examples --}}
                                    <div class="mb-6">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Example Request</h4>

                                        {{-- Mobile Language Tabs --}}
                                        <div class="flex sm:hidden items-center gap-1 bg-gray-200 dark:bg-gray-700 rounded-lg p-1 mb-3">
                                            <button @click="activeLanguage = 'bash'"
                                                    :class="activeLanguage === 'bash' ? 'bg-white dark:bg-gray-600 shadow-xs' : ''"
                                                    class="flex-1 px-2 py-1 text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 transition-all">
                                                cURL
                                            </button>
                                            <button @click="activeLanguage = 'javascript'"
                                                    :class="activeLanguage === 'javascript' ? 'bg-white dark:bg-gray-600 shadow-xs' : ''"
                                                    class="flex-1 px-2 py-1 text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 transition-all">
                                                JS
                                            </button>
                                            <button @click="activeLanguage = 'php'"
                                                    :class="activeLanguage === 'php' ? 'bg-white dark:bg-gray-600 shadow-xs' : ''"
                                                    class="flex-1 px-2 py-1 text-xs font-medium rounded-md text-gray-700 dark:text-gray-300 transition-all">
                                                PHP
                                            </button>
                                        </div>

                                        {{-- Code Blocks --}}
                                        <div x-show="activeLanguage === 'bash'" class="code-block rounded-lg overflow-hidden">
                                            <pre class="p-4 overflow-x-auto"><code class="language-bash text-sm text-gray-100">curl --request {{ $method }} \
    --get "{{ $baseUrl }}/{{ $endpoint->uri }}" \
    --header "Authorization: Bearer {'{YOUR_API_TOKEN}'}" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre>
                                        </div>

                                        <div x-show="activeLanguage === 'javascript'" x-cloak class="code-block rounded-lg overflow-hidden">
                                            <pre class="p-4 overflow-x-auto"><code class="language-javascript text-sm text-gray-100">const url = new URL("{{ $baseUrl }}/{{ $endpoint->uri }}");

const headers = {
    "Authorization": "Bearer {YOUR_API_TOKEN}",
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "{{ $method }}",
    headers,
}).then(response => response.json());</code></pre>
                                        </div>

                                        <div x-show="activeLanguage === 'php'" x-cloak class="code-block rounded-lg overflow-hidden">
                                            <pre class="p-4 overflow-x-auto"><code class="language-php text-sm text-gray-100">$client = new \GuzzleHttp\Client();
$url = '{{ $baseUrl }}/{{ $endpoint->uri }}';
$response = $client->{{ strtolower($method) }}(
    $url,
    [
        'headers' => [
            'Authorization' => 'Bearer {YOUR_API_TOKEN}',
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
    ]
);
$body = $response->getBody();
print_r(json_decode((string) $body));</code></pre>
                                        </div>
                                    </div>

                                    {{-- Try It Out --}}
                                    @if($tryItOut['enabled'] ?? false)
                                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                </svg>
                                                Try It Out
                                            </h4>

                                            <form id="form-{{ $formId }}"
                                                  data-method="{{ $method }}"
                                                  data-path="{{ $endpoint->uri }}"
                                                  data-authed="{{ $endpoint->metadata->authenticated ?? false ? '1' : '0' }}"
                                                  data-hasfiles="0"
                                                  data-isarraybody="0"
                                                  autocomplete="off"
                                                  onsubmit="event.preventDefault(); executeTryOut('{{ $formId }}', this);">

                                                {{-- Auth Header --}}
                                                @if($endpoint->metadata->authenticated ?? false)
                                                    <div class="mb-4">
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Authorization</label>
                                                        <input type="text"
                                                               name="Authorization"
                                                               class="auth-value w-full px-3 py-2 text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:text-white"
                                                               data-endpoint="{{ $formId }}"
                                                               data-component="header"
                                                               value="Bearer {YOUR_API_TOKEN}"
                                                               placeholder="Bearer {YOUR_API_TOKEN}">
                                                    </div>
                                                @endif

                                                {{-- URL Parameters --}}
                                                @foreach($endpoint->urlParameters as $name => $param)
                                                    <div class="mb-4">
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                            {{ $name }}
                                                            @if($param['required'] ?? false)
                                                                <span class="text-red-500">*</span>
                                                            @endif
                                                        </label>
                                                        <input type="text"
                                                               name="{{ $name }}"
                                                               class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:text-white"
                                                               data-endpoint="{{ $formId }}"
                                                               data-component="url"
                                                               value="{{ $param['example'] ?? '' }}"
                                                               placeholder="{{ $param['description'] ?? '' }}">
                                                    </div>
                                                @endforeach

                                                {{-- Query Parameters --}}
                                                @foreach($endpoint->queryParameters as $name => $param)
                                                    <div class="mb-4">
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                            {{ $name }}
                                                            @if($param['required'] ?? false)
                                                                <span class="text-red-500">*</span>
                                                            @endif
                                                        </label>
                                                        <input type="text"
                                                               name="{{ $name }}"
                                                               class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:text-white"
                                                               data-endpoint="{{ $formId }}"
                                                               data-component="query"
                                                               value="{{ $param['example'] ?? '' }}"
                                                               placeholder="{{ $param['description'] ?? '' }}">
                                                    </div>
                                                @endforeach

                                                {{-- Body Parameters --}}
                                                @foreach($endpoint->bodyParameters as $name => $param)
                                                    <div class="mb-4">
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                            {{ $name }}
                                                            @if($param['required'] ?? false)
                                                                <span class="text-red-500">*</span>
                                                            @endif
                                                        </label>
                                                        @if(($param['type'] ?? '') === 'object' || str_contains($param['type'] ?? '', 'array'))
                                                            <textarea name="{{ $name }}"
                                                                      rows="3"
                                                                      class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:text-white font-mono"
                                                                      data-endpoint="{{ $formId }}"
                                                                      data-component="body"
                                                                      placeholder="{{ $param['description'] ?? '' }}">{{ $param['example'] ?? '' }}</textarea>
                                                        @else
                                                            <input type="text"
                                                                   name="{{ $name }}"
                                                                   class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:text-white"
                                                                   data-endpoint="{{ $formId }}"
                                                                   data-component="body"
                                                                   value="{{ $param['example'] ?? '' }}"
                                                                   placeholder="{{ $param['description'] ?? '' }}">
                                                        @endif
                                                    </div>
                                                @endforeach

                                                {{-- Hidden headers --}}
                                                <input type="hidden" name="Content-Type" data-endpoint="{{ $formId }}" data-component="header" value="application/json">
                                                <input type="hidden" name="Accept" data-endpoint="{{ $formId }}" data-component="header" value="application/json">

                                                {{-- Buttons --}}
                                                <div class="flex items-center gap-2">
                                                    <button type="button"
                                                            id="btn-tryout-{{ $formId }}"
                                                            onclick="tryItOut('{{ $formId }}');"
                                                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                                                        Try it out
                                                    </button>
                                                    <button type="button"
                                                            id="btn-canceltryout-{{ $formId }}"
                                                            onclick="cancelTryOut('{{ $formId }}');"
                                                            class="hidden px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg transition-colors">
                                                        Cancel
                                                    </button>
                                                    <button type="submit"
                                                            id="btn-executetryout-{{ $formId }}"
                                                            data-initial-text="Send Request"
                                                            data-loading-text="Sending..."
                                                            class="hidden px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                                                        Send Request
                                                    </button>
                                                </div>
                                            </form>

                                            {{-- Response Display --}}
                                            <div id="execution-results-{{ $formId }}" class="hidden mt-4">
                                                <h5 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Response</h5>
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="text-sm text-gray-600 dark:text-gray-400">Status:</span>
                                                    <span id="execution-response-status-{{ $formId }}" class="px-2 py-0.5 text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded"></span>
                                                </div>
                                                <pre class="p-4 bg-gray-900 rounded-lg overflow-x-auto"><code id="execution-response-content-{{ $formId }}" class="language-json text-sm text-gray-100" data-empty-response-text="<Empty response>"></code></pre>
                                            </div>

                                            <div id="execution-error-{{ $formId }}" class="hidden mt-4">
                                                <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                                    <h5 class="text-sm font-medium text-red-800 dark:text-red-400 mb-2">Request Failed</h5>
                                                    <pre id="execution-error-message-{{ $formId }}" class="text-sm text-red-700 dark:text-red-300 whitespace-pre-wrap"></pre>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </section>
            @endforeach

            {{-- Append Content --}}
            @if($append ?? false)
                <section class="prose dark:prose-invert max-w-none">
                    {!! $append !!}
                </section>
            @endif
        </main>
    </div>

    {{-- Initialize --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize highlight.js
            hljs.highlightAll();

            // Theme toggle
            const themeToggleBtn = document.getElementById('theme-toggle-docs');
            const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon-docs');
            const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon-docs');

            function updateThemeIcons() {
                if (document.documentElement.classList.contains('dark')) {
                    themeToggleLightIcon.classList.remove('hidden');
                    themeToggleDarkIcon.classList.add('hidden');
                } else {
                    themeToggleDarkIcon.classList.remove('hidden');
                    themeToggleLightIcon.classList.add('hidden');
                }
            }

            updateThemeIcons();

            themeToggleBtn.addEventListener('click', function() {
                document.documentElement.classList.toggle('dark');
                if (document.documentElement.classList.contains('dark')) {
                    localStorage.setItem('color-theme', 'dark');
                } else {
                    localStorage.setItem('color-theme', 'light');
                }
                updateThemeIcons();
            });

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });

            // Search functionality
            const searchInput = document.getElementById('search-input');
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const query = e.target.value.toLowerCase();
                    document.querySelectorAll('.nav-item').forEach(item => {
                        const text = item.textContent.toLowerCase();
                        const parent = item.closest('li');
                        if (parent) {
                            parent.style.display = text.includes(query) ? '' : 'none';
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>
