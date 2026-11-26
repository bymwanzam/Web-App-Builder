<section class="bg-white dark:bg-gray-900 pt-24 relative overflow-hidden">
    <!-- Background Gradient Blob -->
    <div class="absolute top-20 right-0 w-[600px] h-[600px] bg-gradient-to-br from-blue-400/20 via-purple-400/20 to-pink-400/20 dark:from-blue-600/10 dark:via-purple-600/10 dark:to-pink-600/10 rounded-full blur-3xl -z-10"></div>
    <div class="absolute bottom-0 left-1/4 w-[400px] h-[400px] bg-gradient-to-tr from-cyan-400/10 to-blue-400/10 dark:from-cyan-600/5 dark:to-blue-600/5 rounded-full blur-3xl -z-10"></div>

    <div class="grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12">
        <div class="mr-auto place-self-center lg:col-span-7">
            <!-- Feature Pills -->
            <div class="flex flex-wrap gap-2 mb-4">
                <span class="inline-flex items-center px-3 py-1 text-xs font-medium text-blue-700 dark:text-blue-300 bg-blue-100 dark:bg-blue-900/50 rounded-full">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    No coding required
                </span>
                <span class="inline-flex items-center px-3 py-1 text-xs font-medium text-green-700 dark:text-green-300 bg-green-100 dark:bg-green-900/50 rounded-full">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                    5-minute setup
                </span>
                <span class="inline-flex items-center px-3 py-1 text-xs font-medium text-purple-700 dark:text-purple-300 bg-purple-100 dark:bg-purple-900/50 rounded-full">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    Free tier available
                </span>
            </div>

            <!-- Main Headline -->
            <h1 class="max-w-2xl mb-4 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl dark:text-white">
                Build Native Apps <span class="bg-gradient-to-r from-blue-600 to-blue-800 dark:from-blue-400 dark:to-blue-600 bg-clip-text text-transparent">in Minutes</span>
            </h1>

            <!-- Subheadline -->
            <p class="max-w-2xl mb-6 font-light text-gray-500 lg:mb-8 md:text-lg lg:text-xl dark:text-gray-400">
                Transform your website into native apps for any platform. <span class="text-blue-600 dark:text-blue-400 font-medium">AI-powered</span> configuration, custom branding, push notifications, and automated builds. No coding required.
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col space-y-4 sm:flex-row sm:space-y-0 sm:space-x-4">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" wire:navigate class="inline-flex items-center justify-center px-5 py-3 text-base font-medium text-center text-white rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20">
                        Get started free
                        <svg class="w-5 h-5 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                @endif
                <a href="#pricing" class="inline-flex items-center justify-center px-5 py-3 text-base font-medium text-center text-gray-900 border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:text-white dark:border-gray-700 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                    View pricing
                </a>
            </div>

            <!-- Trust Indicators & Stats -->
            <div class="mt-8 space-y-4">
                <!-- Stats Row -->
                <div class="flex flex-wrap items-center gap-6">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">500+</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Developers</p>
                        </div>
                    </div>
                    <div class="w-px h-10 bg-gray-200 dark:bg-gray-700 hidden sm:block"></div>
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 bg-green-100 dark:bg-green-900/50 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 00-1 1v1.323l-3.954 1.582a1 1 0 00-.646.934V14a1 1 0 001 1h9.2a1 1 0 001-1V6.839a1 1 0 00-.646-.934L11 4.323V3a1 1 0 00-1-1zM6.4 15a2.4 2.4 0 014.8 0H6.4zm2.4-1.2a1.2 1.2 0 100-2.4 1.2 1.2 0 000 2.4z" clip-rule="evenodd"/>
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16z" fill="none"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">1,200+</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Apps Built</p>
                        </div>
                    </div>
                    <div class="w-px h-10 bg-gray-200 dark:bg-gray-700 hidden sm:block"></div>
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 bg-purple-100 dark:bg-purple-900/50 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xl font-bold text-gray-900 dark:text-white">3,500+</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Builds Generated</p>
                        </div>
                    </div>
                </div>

                <!-- Rating & Trust -->
                <div class="flex items-center">
                    <div class="flex items-center">
                        @for($i = 0; $i < 5; $i++)
                        <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        @endfor
                    </div>
                    <span class="ml-2 text-sm font-medium text-gray-600 dark:text-gray-400">4.9/5 from 200+ reviews</span>
                </div>
            </div>
        </div>

        <!-- Hero Image/Mockup - Multi-Device Showcase -->
        <div class="hidden lg:mt-0 lg:col-span-5 lg:flex lg:items-center lg:justify-center">
            <div class="relative flex items-end gap-4">

                <!-- Phone Mockup (Scaled Down) -->
                <div class="relative flex-shrink-0 transform scale-[0.55] origin-bottom-right -mr-8 z-10">
                    <div class="relative border-gray-800 dark:border-gray-800 bg-gray-800 border-[14px] rounded-[2.5rem] h-[500px] w-[250px] shadow-2xl">
                        <div class="w-[120px] h-[15px] bg-gray-800 top-0 rounded-b-[1rem] left-1/2 -translate-x-1/2 absolute"></div>
                        <div class="h-[40px] w-[3px] bg-gray-800 absolute -start-[17px] top-[100px] rounded-s-lg"></div>
                        <div class="h-[40px] w-[3px] bg-gray-800 absolute -start-[17px] top-[148px] rounded-s-lg"></div>
                        <div class="h-[54px] w-[3px] bg-gray-800 absolute -end-[17px] top-[118px] rounded-e-lg"></div>
                        <div class="rounded-[2rem] overflow-hidden w-[222px] h-[472px] bg-white dark:bg-gray-900 flex flex-col">
                            <!-- Status Bar -->
                            <div class="h-6 bg-blue-600 flex items-center justify-between px-3 flex-shrink-0">
                                <span class="text-[9px] text-white/90 font-medium">9:41</span>
                                <div class="flex items-center space-x-1">
                                    <svg class="w-2.5 h-2.5 text-white/90" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M17.778 8.222c-4.296-4.296-11.26-4.296-15.556 0A1 1 0 01.808 6.808c5.076-5.077 13.308-5.077 18.384 0a1 1 0 01-1.414 1.414z" clip-rule="evenodd"/></svg>
                                    <svg class="w-3 h-2.5 text-white/90" fill="currentColor" viewBox="0 0 24 12"><rect x="0" y="0" width="20" height="12" rx="2" stroke="currentColor" stroke-width="1" fill="none"/><rect x="2" y="2" width="14" height="8" rx="1" fill="currentColor"/></svg>
                                </div>
                            </div>
                            <!-- App Header -->
                            <div class="bg-blue-600 px-3 pb-3 pt-1 flex-shrink-0">
                                <div class="flex items-center space-x-2">
                                    <div class="w-7 h-7 bg-white/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10.496 2.132a1 1 0 00-.992 0l-7 4A1 1 0 002 7v6a1 1 0 00.504.868l7 4a1 1 0 00.992 0l7-4A1 1 0 0018 13V7a1 1 0 00-.504-.868l-7-4z" clip-rule="evenodd"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-white font-semibold text-xs">My App</h4>
                                        <p class="text-white/70 text-[8px]">Mobile</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Content -->
                            <div class="flex-1 bg-gray-50 dark:bg-gray-800 p-2 space-y-2 overflow-hidden">
                                <div class="bg-white dark:bg-gray-700 rounded-lg p-2 shadow-sm">
                                    <div class="w-full h-12 bg-gradient-to-r from-blue-500 to-purple-500 rounded mb-1.5"></div>
                                    <div class="h-1.5 bg-gray-200 dark:bg-gray-600 rounded w-3/4"></div>
                                </div>
                                <div class="grid grid-cols-2 gap-1.5">
                                    <div class="bg-white dark:bg-gray-700 rounded p-1.5 shadow-sm">
                                        <div class="w-full h-10 bg-gray-100 dark:bg-gray-600 rounded mb-1"></div>
                                        <div class="h-1.5 bg-gray-200 dark:bg-gray-600 rounded"></div>
                                    </div>
                                    <div class="bg-white dark:bg-gray-700 rounded p-1.5 shadow-sm">
                                        <div class="w-full h-10 bg-gray-100 dark:bg-gray-600 rounded mb-1"></div>
                                        <div class="h-1.5 bg-gray-200 dark:bg-gray-600 rounded"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- Bottom Nav -->
                            <div class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 px-4 py-2 flex-shrink-0">
                                <div class="flex justify-between items-center">
                                    <div class="w-4 h-4 bg-blue-600 rounded"></div>
                                    <div class="w-4 h-4 bg-gray-300 dark:bg-gray-600 rounded"></div>
                                    <div class="w-4 h-4 bg-gray-300 dark:bg-gray-600 rounded"></div>
                                    <div class="w-4 h-4 bg-gray-300 dark:bg-gray-600 rounded"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Laptop Mockup (Primary) -->
                <div class="relative flex-shrink-0">
                    <!-- Laptop Screen -->
                    <div class="relative bg-gray-900 dark:bg-gray-950 rounded-t-xl p-2 shadow-2xl ring-1 ring-gray-700 dark:ring-gray-600 w-[380px]">
                        <!-- macOS Window Chrome -->
                        <div class="bg-gray-100 dark:bg-gray-900 rounded-lg overflow-hidden ring-1 ring-gray-200 dark:ring-gray-700">
                            <!-- Title Bar -->
                            <div class="bg-gray-200 dark:bg-gray-800 px-3 py-2 flex items-center border-b border-gray-300 dark:border-gray-700">
                                <div class="flex space-x-1.5">
                                    <div class="w-2.5 h-2.5 bg-red-500 rounded-full shadow-sm"></div>
                                    <div class="w-2.5 h-2.5 bg-yellow-500 rounded-full shadow-sm"></div>
                                    <div class="w-2.5 h-2.5 bg-green-500 rounded-full shadow-sm"></div>
                                </div>
                                <div class="flex-1 text-center">
                                    <span class="text-[10px] text-gray-600 dark:text-gray-300 font-medium">My App — Dashboard</span>
                                </div>
                            </div>
                            <!-- Window Content -->
                            <div class="bg-white dark:bg-gray-950 h-[220px] flex">
                                <!-- Sidebar -->
                                <div class="w-16 bg-gray-50 dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 p-2 space-y-2">
                                    <div class="w-full h-8 bg-blue-100 dark:bg-blue-900/70 rounded flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                                    </div>
                                    <div class="w-full h-8 bg-gray-100 dark:bg-gray-800 rounded"></div>
                                    <div class="w-full h-8 bg-gray-100 dark:bg-gray-800 rounded"></div>
                                    <div class="w-full h-8 bg-gray-100 dark:bg-gray-800 rounded"></div>
                                </div>
                                <!-- Main Content -->
                                <div class="flex-1 p-3 space-y-2">
                                    <!-- Header -->
                                    <div class="flex items-center justify-between">
                                        <div class="h-3 bg-gray-200 dark:bg-gray-800 rounded w-24"></div>
                                        <div class="h-6 w-6 bg-blue-100 dark:bg-blue-900/70 rounded-full"></div>
                                    </div>
                                    <!-- Stats Cards -->
                                    <div class="grid grid-cols-3 gap-2">
                                        <div class="bg-blue-50 dark:bg-blue-950/50 rounded p-2 ring-1 ring-blue-100 dark:ring-blue-900/50">
                                            <div class="h-2 bg-blue-200 dark:bg-blue-800 rounded w-1/2 mb-1"></div>
                                            <div class="h-4 bg-blue-400 dark:bg-blue-600 rounded w-3/4"></div>
                                        </div>
                                        <div class="bg-green-50 dark:bg-green-950/50 rounded p-2 ring-1 ring-green-100 dark:ring-green-900/50">
                                            <div class="h-2 bg-green-200 dark:bg-green-800 rounded w-1/2 mb-1"></div>
                                            <div class="h-4 bg-green-400 dark:bg-green-600 rounded w-3/4"></div>
                                        </div>
                                        <div class="bg-purple-50 dark:bg-purple-950/50 rounded p-2 ring-1 ring-purple-100 dark:ring-purple-900/50">
                                            <div class="h-2 bg-purple-200 dark:bg-purple-800 rounded w-1/2 mb-1"></div>
                                            <div class="h-4 bg-purple-400 dark:bg-purple-600 rounded w-3/4"></div>
                                        </div>
                                    </div>
                                    <!-- Content Area -->
                                    <div class="bg-gray-50 dark:bg-gray-900 rounded p-2 flex-1 ring-1 ring-gray-100 dark:ring-gray-800">
                                        <div class="space-y-1.5">
                                            <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
                                            <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded w-5/6"></div>
                                            <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded w-4/6"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Laptop Base -->
                    <div class="bg-gray-300 dark:bg-gray-800 h-3 rounded-b-lg mx-auto ring-1 ring-gray-400 dark:ring-gray-600 w-[420px]"></div>
                    <div class="bg-gray-400 dark:bg-gray-700 h-1 rounded-b mx-auto w-[180px]"></div>
                </div>

                <!-- Floating badges - Dynamic platforms -->
                @php
                    $activePlatforms = \App\Models\Plugin::where('type', 'platform')
                        ->where('status', 'active')
                        ->pluck('name')
                        ->toArray();

                    // Fallback if no platforms active
                    if (empty($activePlatforms)) {
                        $activePlatforms = ['Android', 'Windows', 'macOS'];
                    }

                    // Shuffle and take max 3 random platforms
                    shuffle($activePlatforms);
                    $activePlatforms = array_slice($activePlatforms, 0, 3);

                    // Badge colors cycle
                    $badgeColors = ['green', 'blue', 'purple', 'orange'];
                @endphp

                @foreach($activePlatforms as $index => $platform)
                    @php
                        $color = $badgeColors[$index % count($badgeColors)];
                        // Position badges dynamically (bottom area only, no top)
                        $positions = [
                            'absolute -left-4 bottom-24',
                            'absolute right-24 bottom-20',
                            'absolute left-20 bottom-40',
                        ];
                        $position = $positions[$index] ?? $positions[0];
                    @endphp
                    <div class="{{ $position }} z-20 bg-white dark:bg-gray-800 rounded-lg shadow-xl p-3">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-{{ $color }}-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $platform }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
