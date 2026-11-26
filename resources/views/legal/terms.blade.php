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

    <title>Terms of Service - {{ $siteName }}</title>
    <meta name="description" content="Terms of Service for {{ $siteName }}">

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
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 dark:bg-indigo-900/30 rounded-2xl mb-6">
                    <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Terms of Service</h1>
                <p class="text-lg text-gray-600 dark:text-gray-400">Last updated: {{ now()->format('F j, Y') }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-8 sm:p-12">
                    <p class="text-lg text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
                        Please read these Terms of Service carefully before using {{ config('app.name') }}. By accessing or using our service, you agree to be bound by these terms.
                    </p>

                    <section class="mb-10">
                        <h2 class="flex items-center text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            <span class="flex items-center justify-center w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg mr-3">
                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span>
                            Acceptance of Terms
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            By creating an account or using our services, you acknowledge that you have read, understood, and agree to be bound by these Terms of Service and our Privacy Policy. If you do not agree to these terms, please do not use our services.
                        </p>
                    </section>

                    <section class="mb-10">
                        <h2 class="flex items-center text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            <span class="flex items-center justify-center w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg mr-3">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </span>
                            Description of Service
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">{{ config('app.name') }} provides a platform for building native mobile applications. Our services include:</p>
                        <ul class="space-y-3 text-gray-600 dark:text-gray-300">
                            <li class="flex items-start"><svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>App configuration and customization tools</li>
                            <li class="flex items-start"><svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Automated build and compilation services</li>
                            <li class="flex items-start"><svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Push notification management</li>
                            <li class="flex items-start"><svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>App signing and distribution preparation</li>
                        </ul>
                    </section>

                    <section class="mb-10">
                        <h2 class="flex items-center text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            <span class="flex items-center justify-center w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg mr-3">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </span>
                            User Accounts
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">When creating an account, you agree to:</p>
                        <ul class="space-y-3 text-gray-600 dark:text-gray-300">
                            <li class="flex items-start"><svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Provide accurate and complete information</li>
                            <li class="flex items-start"><svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Maintain the security of your account credentials</li>
                            <li class="flex items-start"><svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Accept responsibility for all activities under your account</li>
                            <li class="flex items-start"><svg class="w-5 h-5 text-green-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Notify us immediately of any unauthorized access</li>
                        </ul>
                    </section>

                    <section class="mb-10">
                        <h2 class="flex items-center text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            <span class="flex items-center justify-center w-8 h-8 bg-orange-100 dark:bg-orange-900/30 rounded-lg mr-3">
                                <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </span>
                            Acceptable Use
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">You agree not to use our services to:</p>
                        <ul class="space-y-3 text-gray-600 dark:text-gray-300">
                            <li class="flex items-start"><svg class="w-5 h-5 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>Create apps that contain illegal or harmful content</li>
                            <li class="flex items-start"><svg class="w-5 h-5 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>Infringe on intellectual property rights</li>
                            <li class="flex items-start"><svg class="w-5 h-5 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>Distribute malware or malicious code</li>
                            <li class="flex items-start"><svg class="w-5 h-5 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>Attempt to gain unauthorized access to our systems</li>
                        </ul>
                    </section>

                    <section class="mb-10">
                        <h2 class="flex items-center text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            <span class="flex items-center justify-center w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg mr-3">
                                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            </span>
                            Subscription and Payments
                        </h2>
                        <div class="space-y-4 text-gray-600 dark:text-gray-300">
                            <p>Paid subscriptions are billed in advance on a recurring basis. You authorize us to charge your payment method for all fees incurred.</p>
                            <p>Subscription fees are non-refundable except as required by law. You may cancel your subscription at any time, and it will remain active until the end of the current billing period.</p>
                            <p>We reserve the right to modify pricing with 30 days notice to existing subscribers.</p>
                        </div>
                    </section>

                    <section class="mb-10">
                        <h2 class="flex items-center text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            <span class="flex items-center justify-center w-8 h-8 bg-teal-100 dark:bg-teal-900/30 rounded-lg mr-3">
                                <svg class="w-4 h-4 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                </svg>
                            </span>
                            Intellectual Property
                        </h2>
                        <div class="space-y-4 text-gray-600 dark:text-gray-300">
                            <p>You retain all rights to your content. By using our service, you grant us a license to use this content solely to provide our services.</p>
                            <p>The {{ config('app.name') }} platform, including its design, features, and technology, remains our exclusive property.</p>
                        </div>
                    </section>

                    <section class="mb-10">
                        <h2 class="flex items-center text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            <span class="flex items-center justify-center w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-lg mr-3">
                                <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.618 5.984A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016zM12 9v2m0 4h.01"/>
                                </svg>
                            </span>
                            Limitation of Liability
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            To the maximum extent permitted by law, {{ config('app.name') }} shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising from your use of our services.
                        </p>
                    </section>

                    <section class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl p-6 border border-indigo-100 dark:border-indigo-800">
                        <h2 class="flex items-center text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            <span class="flex items-center justify-center w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg mr-3">
                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            Contact Us
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">If you have any questions about these Terms of Service, please contact us at:</p>
                        <a href="mailto:{{ \App\Models\SystemSetting::get('contact_email', 'legal@' . parse_url(config('app.url'), PHP_URL_HOST)) }}" class="inline-flex items-center text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            {{ \App\Models\SystemSetting::get('contact_email', 'legal@' . parse_url(config('app.url'), PHP_URL_HOST)) }}
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
