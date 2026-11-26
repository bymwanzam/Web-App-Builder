@extends('install.layout', ['title' => 'Welcome'])

@section('header')
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome to Appy Installer</h2>
    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
        Let's get your app builder platform up and running in just a few steps.
    </p>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Description -->
        <div class="text-center space-y-3">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Build Native Mobile Apps Without Code
            </h3>
            <p class="text-gray-600 dark:text-gray-400">
                Appy helps you create native mobile applications for multiple platforms without writing a single line of code.
            </p>
        </div>

        <!-- Features List -->
        <div class="grid gap-4 md:grid-cols-2">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white">Multi-Platform Support</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Build apps for multiple platforms</p>
                </div>
            </div>

            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white">Plugin Architecture</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Extensible platform system</p>
                </div>
            </div>

            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white">Subscription Management</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Built-in plans and billing</p>
                </div>
            </div>

            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 dark:text-white">Push Notifications</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Engage users with Firebase/APNs</p>
                </div>
            </div>
        </div>

        <!-- Requirements Note -->
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg dark:bg-blue-900/20 dark:border-blue-800">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-400">
                        Before you begin
                    </h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <p>Make sure you have:</p>
                        <ul class="list-disc list-inside mt-1 space-y-1">
                            <li>PHP 8.2 or higher installed</li>
                            <li>SQLite or MySQL database ready</li>
                            <li>Writable file permissions on server</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <a href="{{ route('install.requirements') }}" class="w-full inline-flex justify-center items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-hidden focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
        Get Started
        <svg class="w-5 h-5 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
    </a>
@endsection
