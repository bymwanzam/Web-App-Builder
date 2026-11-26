@extends('install.layout', ['title' => 'Installation Complete', 'currentStep' => 5])

@section('header')
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Installation Complete!</h2>
    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
        Your Appy installation has been successfully completed.
    </p>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Success Icon -->
        <div class="flex justify-center">
            <div class="flex items-center justify-center w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full">
                <svg class="w-12 h-12 text-green-600 dark:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>

        <!-- Success Message -->
        <div class="text-center space-y-3">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                Welcome to Appy!
            </h3>
            <p class="text-gray-600 dark:text-gray-400">
                Your application has been set up successfully. You can now log in with your admin account and start building mobile apps.
            </p>
        </div>

        <!-- Resources -->
        <div class="grid gap-4 md:grid-cols-2">
            <a href="#" class="block p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <div>
                        <h5 class="font-medium text-gray-900 dark:text-white">Documentation</h5>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Read the full docs</p>
                    </div>
                </div>
            </a>

            <a href="https://support.titansystems.ph/" target="_blank" class="block p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <div>
                        <h5 class="font-medium text-gray-900 dark:text-white">Support</h5>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Get help & support</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
@endsection

@section('footer')
    <a href="{{ route('login') }}" class="w-full inline-flex justify-center items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-hidden focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
        Go to Login
        <svg class="w-5 h-5 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
        </svg>
    </a>
@endsection
