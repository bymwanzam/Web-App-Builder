<section class="bg-white dark:bg-gray-900">
    <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
        <div class="mx-auto max-w-screen-sm text-center">
            <h2 class="mb-4 text-4xl tracking-tight font-extrabold leading-tight text-gray-900 dark:text-white">
                Start building your app today
            </h2>
            <p class="mb-6 font-light text-gray-500 dark:text-gray-400 md:text-lg">
                Join thousands of developers and businesses who trust {{ \App\Models\SystemSetting::get('site_name', config('app.name')) }} to build their apps. No credit card required.
            </p>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" wire:navigate class="text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20">
                    Get started free
                </a>
            @endif
        </div>
    </div>
</section>
