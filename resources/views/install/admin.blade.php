@extends('install.layout', ['title' => 'Admin Account', 'currentStep' => 4])

@section('header')
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create Admin Account</h2>
    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
        Set up your administrator account and configure basic site settings.
    </p>
@endsection

@section('content')
    <livewire:install.admin-form />
@endsection
