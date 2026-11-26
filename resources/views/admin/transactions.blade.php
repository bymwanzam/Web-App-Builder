<x-app-layout>
    <x-slot name="title">Transactions</x-slot>

    <!-- Page Header -->
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 18h14M5 18v3h14v-3M5 18l1-9h12l1 9M16 6v3m-4-3v3m-2-6h8v3h-8V3Zm-1 9h.01v.01H9V12Zm3 0h.01v.01H12V12Zm3 0h.01v.01H15V12Zm-6 3h.01v.01H9V15Zm3 0h.01v.01H12V15Zm3 0h.01v.01H15V15Z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Transactions</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 hidden sm:block">
                View and manage payment transactions
            </p>
        </div>
    </div>

    <!-- Transactions Table Component -->
    <livewire:admin.transactions-table />
</x-app-layout>
