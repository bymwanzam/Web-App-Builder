<x-app-layout>
    <x-slot name="title">System Settings</x-slot>

    <div x-data="{
        search: '',
        get visibleCount() {
            if (!this.search) return 7;
            const keywords = {
                general: 'general site name logo favicon theme registration landing page tagline description branding',
                auth: 'authentication social login google facebook github oauth recaptcha verification email verify',
                email: 'email smtp php mail sendmail from address notifications mailer configuration',
                plan: 'plan default plan subscription configuration app',
                appetize: 'appetize preview emulator api token integration testing',
                ai: 'ai artificial intelligence chat assistant openai anthropic claude gpt tokens model',
                gdpr: 'gdpr privacy data protection cookie consent export deletion retention compliance policy terms'
            };
            let count = 0;
            const s = this.search.toLowerCase();
            Object.values(keywords).forEach(k => { if (k.includes(s)) count++; });
            return count;
        }
    }">
        <!-- Page Header -->
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13v-2a1 1 0 0 0-1-1h-.757l-.707-1.707.535-.536a1 1 0 0 0 0-1.414l-1.414-1.414a1 1 0 0 0-1.414 0l-.536.535L14 4.757V4a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v.757l-1.707.707-.536-.535a1 1 0 0 0-1.414 0L4.929 6.343a1 1 0 0 0 0 1.414l.536.536L4.757 10H4a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h.757l.707 1.707-.535.536a1 1 0 0 0 0 1.414l1.414 1.414a1 1 0 0 0 1.414 0l.536-.535 1.707.707V20a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-.757l1.707-.708.536.536a1 1 0 0 0 1.414 0l1.414-1.414a1 1 0 0 0 0-1.414l-.535-.536.707-1.707H20a1 1 0 0 0 1-1Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">System Settings</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 hidden sm:block">
                    Configure system-wide settings and preferences
                </p>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="relative mb-6">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" x-model="search" placeholder="Search settings..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
        </div>

        <!-- Settings Cards -->
        <div class="space-y-6">
            <!-- General Settings Card -->
            <div x-show="!search || 'general site name logo favicon theme registration landing page tagline description branding'.includes(search.toLowerCase())" x-transition>
                <livewire:admin.general-settings-form />
            </div>

            <!-- Authentication Settings Card -->
            <div x-show="!search || 'authentication social login google facebook github oauth recaptcha verification email verify'.includes(search.toLowerCase())" x-transition>
                <livewire:admin.social-login-settings-form />
            </div>

            <!-- Email Configuration Card -->
            <div x-show="!search || 'email smtp php mail sendmail from address notifications mailer configuration'.includes(search.toLowerCase())" x-transition>
                <livewire:admin.email-settings-form />
            </div>

            <!-- App Configuration Card -->
            <div x-show="!search || 'plan default plan subscription configuration app'.includes(search.toLowerCase())" x-transition>
                <livewire:admin.app-settings-form />
            </div>

            <!-- Appetize.io Integration Card -->
            <div x-show="!search || 'appetize preview emulator api token integration testing'.includes(search.toLowerCase())" x-transition>
                <livewire:admin.appetize-settings-form />
            </div>

            <!-- AI Chat Assistant Card -->
            <div x-show="!search || 'ai artificial intelligence chat assistant openai anthropic claude gpt tokens model'.includes(search.toLowerCase())" x-transition>
                <livewire:admin.ai-settings-form />
            </div>

            <!-- GDPR & Privacy Settings Card -->
            <div x-show="!search || 'gdpr privacy data protection cookie consent export deletion retention compliance policy terms'.includes(search.toLowerCase())" x-transition>
                <livewire:admin.gdpr-settings-form />
            </div>

            <!-- No Results Message -->
            <div x-show="search && visibleCount === 0" x-transition class="flex flex-col items-center justify-center py-12">
                <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <p class="text-gray-500 dark:text-gray-400 text-center">
                    No settings found matching "<span x-text="search"></span>"
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
