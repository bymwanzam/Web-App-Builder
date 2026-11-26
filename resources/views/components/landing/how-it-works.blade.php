@php
    $title = \App\Models\SystemSetting::get('how_it_works_title', 'How It Works');
    $subtitle = \App\Models\SystemSetting::get('how_it_works_subtitle', 'Build your mobile app in 4 simple steps');
@endphp

<section id="how-it-works" class="bg-gray-50 dark:bg-gray-800">
    <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
        <!-- Section Header -->
        <div class="max-w-screen-md mb-8 lg:mb-16 mx-auto text-center">
            <h2 class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white">
                {{ $title }}
            </h2>
            <p class="text-gray-500 sm:text-xl dark:text-gray-400">
                {{ $subtitle }}
            </p>
        </div>

        <!-- Steps Grid -->
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
            <!-- Step 1 -->
            <div class="relative text-center group">
                <!-- Step Number -->
                <div class="flex justify-center mb-4">
                    <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20 group-hover:scale-110 transition-transform duration-300">
                        <span class="text-2xl font-bold text-white">1</span>
                    </div>
                </div>
                <!-- Icon -->
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <!-- Content -->
                <h3 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Enter Your URL</h3>
                <p class="text-gray-500 dark:text-gray-400">
                    Paste your website URL and we'll automatically analyze it to get you started quickly.
                </p>
                <!-- Connector (hidden on mobile and last item) -->
                <div class="hidden lg:block absolute top-7 left-[60%] w-[80%] h-0.5 bg-gradient-to-r from-blue-600 to-blue-400"></div>
            </div>

            <!-- Step 2 -->
            <div class="relative text-center group">
                <div class="flex justify-center mb-4">
                    <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20 group-hover:scale-110 transition-transform duration-300">
                        <span class="text-2xl font-bold text-white">2</span>
                    </div>
                </div>
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900/50 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <h3 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Customize Design</h3>
                <p class="text-gray-500 dark:text-gray-400">
                    Choose colors, icons, splash screens, and navigation style. Use AI assistance for quick setup.
                </p>
                <div class="hidden lg:block absolute top-7 left-[60%] w-[80%] h-0.5 bg-gradient-to-r from-blue-500 to-blue-300"></div>
            </div>

            <!-- Step 3 -->
            <div class="relative text-center group">
                <div class="flex justify-center mb-4">
                    <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20 group-hover:scale-110 transition-transform duration-300">
                        <span class="text-2xl font-bold text-white">3</span>
                    </div>
                </div>
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900/50 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <h3 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Build Your App</h3>
                <p class="text-gray-500 dark:text-gray-400">
                    One-click build with our distributed system. Get native builds in minutes.
                </p>
                <div class="hidden lg:block absolute top-7 left-[60%] w-[80%] h-0.5 bg-gradient-to-r from-blue-400 to-blue-200"></div>
            </div>

            <!-- Step 4 -->
            <div class="relative text-center group">
                <div class="flex justify-center mb-4">
                    <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20 group-hover:scale-110 transition-transform duration-300">
                        <span class="text-2xl font-bold text-white">4</span>
                    </div>
                </div>
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900/50 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <h3 class="mb-2 text-xl font-bold text-gray-900 dark:text-white">Download & Publish</h3>
                <p class="text-gray-500 dark:text-gray-400">
                    Download your app or scan QR code to test. Ready for app store publishing.
                </p>
            </div>
        </div>
    </div>
</section>
