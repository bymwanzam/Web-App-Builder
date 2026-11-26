<x-app-layout>
    <x-slot name="title">Audit Logs</x-slot>

    <div class="p-4">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Audit Logs</h1>
            <p class="text-gray-500 dark:text-gray-400">Track user actions and system events for GDPR compliance</p>
        </div>

        <livewire:admin.audit-logs-table />
    </div>
</x-app-layout>
