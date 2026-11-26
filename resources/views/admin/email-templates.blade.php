<x-app-layout>
    <x-slot name="title">Email Templates</x-slot>

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Email Templates</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 hidden sm:block">
                    Customize email notifications sent to users
                </p>
            </div>
        </div>
    </div>

    <!-- Email Templates Table Component -->
    <livewire:admin.email-templates-table />

    @push('modals')
        <livewire:admin.email-template-form />
        <livewire:admin.reset-email-template-modal />
    @endpush
</x-app-layout>
