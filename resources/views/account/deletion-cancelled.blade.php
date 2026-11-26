<x-guest-layout>
    <x-slot name="title">Account Deletion</x-slot>

    <div class="text-center">
        @if($success)
            <div class="w-16 h-16 mx-auto mb-4 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                Deletion Cancelled
            </h1>
        @else
            <div class="w-16 h-16 mx-auto mb-4 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                Unable to Cancel
            </h1>
        @endif

        <p class="text-gray-600 dark:text-gray-400 mb-6">
            {{ $message }}
        </p>

        <a href="{{ route('login') }}" wire:navigate class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            @if($success)
                Go to Dashboard
            @else
                Go to Login
            @endif
        </a>
    </div>
</x-guest-layout>
