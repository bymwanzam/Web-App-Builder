<x-app-layout>
    <x-slot name="title">My Apps</x-slot>

    <livewire:apps-grid />

    @push('modals')
        <livewire:app-form />
        <livewire:delete-app-modal />
    @endpush
</x-app-layout>
