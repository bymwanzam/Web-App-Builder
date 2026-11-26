<x-app-layout>
    <x-slot name="title">Languages</x-slot>

    <!-- Languages Grid Component -->
    <livewire:admin.languages-grid />

    @push('modals')
        <livewire:admin.language-form />
        <livewire:admin.delete-language-modal />
    @endpush
</x-app-layout>
