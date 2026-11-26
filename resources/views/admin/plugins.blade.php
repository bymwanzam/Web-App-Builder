<x-app-layout>
    <x-slot name="title">Plugins</x-slot>

    <livewire:admin.plugins-grid />

    @push('modals')
        <livewire:admin.plugin-config-modal />
        <livewire:admin.delete-plugin-modal />
        <livewire:admin.upload-plugin-modal />
    @endpush
</x-app-layout>
