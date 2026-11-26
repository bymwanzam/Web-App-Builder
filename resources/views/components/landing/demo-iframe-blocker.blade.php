@if(config('app.demo'))
<div
    x-data="{
        show: false,
        demoUrl: 'https://appy.titansystems.ph',
        init() {
            try {
                const isInIframe = window.self !== window.top;
                const referrer = document.referrer.toLowerCase();
                const isCodeCanyon = referrer.includes('codecanyon.net') || referrer.includes('preview.codecanyon.net');
                this.show = isInIframe && isCodeCanyon;
            } catch (e) {
                // Cross-origin iframe - show modal as fallback
                this.show = true;
            }
        }
    }"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-[9999] bg-gray-900/95 backdrop-blur-sm flex items-center justify-center p-4"
>
    <div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 text-center">
        <!-- Icon -->
        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
        </div>

        <!-- Heading -->
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
            Open Demo in New Window
        </h2>

        <!-- Description -->
        <p class="text-gray-600 dark:text-gray-400 mb-8">
            For the best experience with all features working properly, please open the demo in a new browser window.
        </p>

        <!-- CTA Button -->
        <button
            @click="window.open(demoUrl, '_blank')"
            type="button"
            class="w-full inline-flex items-center justify-center px-6 py-4 text-base font-semibold text-white rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/30 transition-all duration-200"
        >
            Open Demo
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
        </button>
    </div>
</div>
@endif
