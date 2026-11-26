<footer class="py-4 px-6 sm:ml-64 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            All Rights Reserved &copy; {{ date('Y') }}
        </p>
        @if(auth()->check() && auth()->user()->isAdmin())
        <div class="flex items-center gap-1.5">
            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9a3 3 0 0 1 3-3m-2 15h4m0-3c0-4.1 4-4.9 4-9A6 6 0 1 0 6 9c0 4 4 5 4 9h4Z"/>
            </svg>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ \App\Models\SystemSetting::get('site_name', config('app.name')) }} v1.0
            </p>
        </div>
        @endif
    </div>
</footer>
