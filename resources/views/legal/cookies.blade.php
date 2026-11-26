<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $siteName = \App\Models\SystemSetting::get('site_name', config('app.name'));
        $faviconPath = \App\Models\SystemSetting::get('favicon_path');
    @endphp

    <title>Cookie Policy - {{ $siteName }}</title>
    <meta name="description" content="Cookie Policy for {{ $siteName }}">

    @if($faviconPath)
        <link rel="icon" href="{{ Storage::url($faviconPath) }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif

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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-white dark:bg-gray-900">
    <x-preloader />
    <x-landing.header />

    <main class="pt-24 pb-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Page Header -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-2xl mb-6">
                    <svg class="w-8 h-8 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Cookie Policy</h1>
                <p class="text-lg text-gray-600 dark:text-gray-400">Last updated: {{ now()->format('F j, Y') }}</p>
            </div>

            <!-- Content Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-8 sm:p-12">
                    <p class="text-lg text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
                        This Cookie Policy explains how {{ config('app.name') }} uses cookies and similar tracking technologies when you visit our website or use our services.
                    </p>

                    <!-- What Are Cookies -->
                    <section class="mb-10">
                        <h2 class="flex items-center text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            <span class="flex items-center justify-center w-8 h-8 bg-amber-100 dark:bg-amber-900/30 rounded-lg mr-3">
                                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span>
                            What Are Cookies
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            Cookies are small text files that are stored on your device when you visit a website. They are widely used to make websites work more efficiently, provide a better user experience, and give website owners information about how their site is being used.
                        </p>
                    </section>

                    <!-- Types of Cookies -->
                    <section class="mb-10">
                        <h2 class="flex items-center text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            <span class="flex items-center justify-center w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg mr-3">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </span>
                            Types of Cookies We Use
                        </h2>

                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mt-6 mb-3">Essential Cookies</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">These cookies are necessary for the website to function properly:</p>
                        <ul class="space-y-3 text-gray-600 dark:text-gray-300 mb-6">
                            <li class="flex items-start"><svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>User authentication and session management</li>
                            <li class="flex items-start"><svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Security features (CSRF protection)</li>
                            <li class="flex items-start"><svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Remembering your preferences</li>
                        </ul>

                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mt-6 mb-3">Analytics Cookies</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">These cookies help us understand how visitors interact with our website:</p>
                        <ul class="space-y-3 text-gray-600 dark:text-gray-300 mb-6">
                            <li class="flex items-start"><svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Page views and navigation patterns</li>
                            <li class="flex items-start"><svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Time spent on pages</li>
                            <li class="flex items-start"><svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Error tracking and performance monitoring</li>
                        </ul>

                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mt-6 mb-3">Marketing Cookies</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">These cookies are used to deliver relevant advertisements:</p>
                        <ul class="space-y-3 text-gray-600 dark:text-gray-300">
                            <li class="flex items-start"><svg class="w-5 h-5 text-purple-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Tracking across websites for advertising purposes</li>
                            <li class="flex items-start"><svg class="w-5 h-5 text-purple-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Measuring advertising campaign effectiveness</li>
                        </ul>
                    </section>

                    <!-- Third-Party Cookies -->
                    <section class="mb-10">
                        <h2 class="flex items-center text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            <span class="flex items-center justify-center w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg mr-3">
                                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </span>
                            Third-Party Cookies
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">We may use third-party services that set their own cookies, including:</p>
                        <ul class="space-y-3 text-gray-600 dark:text-gray-300">
                            <li class="flex items-start"><svg class="w-5 h-5 text-purple-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Google reCAPTCHA (security verification)</li>
                            <li class="flex items-start"><svg class="w-5 h-5 text-purple-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Payment processors (Stripe, PayPal, etc.)</li>
                            <li class="flex items-start"><svg class="w-5 h-5 text-purple-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Analytics services</li>
                        </ul>
                    </section>

                    <!-- Cookie List -->
                    <section class="mb-10">
                        <h2 class="flex items-center text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            <span class="flex items-center justify-center w-8 h-8 bg-teal-100 dark:bg-teal-900/30 rounded-lg mr-3">
                                <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                            </span>
                            Cookie List
                        </h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Purpose</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Duration</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100">{{ strtolower(str_replace(' ', '_', config('app.name'))) }}_session</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">Session management</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">Session</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100">XSRF-TOKEN</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">CSRF protection</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">Session</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100">cookie_consent</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">Cookie preferences</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">1 year</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- Managing Cookies -->
                    <section class="mb-10">
                        <h2 class="flex items-center text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            <span class="flex items-center justify-center w-8 h-8 bg-orange-100 dark:bg-orange-900/30 rounded-lg mr-3">
                                <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </span>
                            Managing Cookies
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">You can control cookies through:</p>
                        <ul class="space-y-3 text-gray-600 dark:text-gray-300">
                            <li class="flex items-start"><svg class="w-5 h-5 text-orange-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg><strong>Cookie Consent Banner:</strong> Use our cookie preferences tool to manage non-essential cookies</li>
                            <li class="flex items-start"><svg class="w-5 h-5 text-orange-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg><strong>Browser Settings:</strong> Most browsers allow you to refuse or delete cookies</li>
                        </ul>
                        <p class="text-gray-600 dark:text-gray-300 mt-4">
                            Please note that disabling essential cookies may prevent you from using certain features of our service.
                        </p>
                    </section>

                    <!-- Contact Us -->
                    <section class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-xl p-6 border border-amber-100 dark:border-amber-800">
                        <h2 class="flex items-center text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            <span class="flex items-center justify-center w-8 h-8 bg-amber-100 dark:bg-amber-900/30 rounded-lg mr-3">
                                <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            Contact Us
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">If you have questions about our use of cookies, please contact us at:</p>
                        <a href="mailto:{{ \App\Models\SystemSetting::get('contact_email', 'privacy@' . parse_url(config('app.url'), PHP_URL_HOST)) }}" class="inline-flex items-center text-amber-600 dark:text-amber-400 hover:underline font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            {{ \App\Models\SystemSetting::get('contact_email', 'privacy@' . parse_url(config('app.url'), PHP_URL_HOST)) }}
                        </a>
                    </section>
                </div>
            </div>

        </div>
    </main>

    <x-landing.footer />
    <livewire:cookie-consent-banner />
    @livewireScripts
</body>
</html>
