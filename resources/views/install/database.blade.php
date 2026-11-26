@extends('install.layout', ['title' => 'Database Configuration', 'currentStep' => 3])

@section('header')
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Database Configuration</h2>
    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
        Enter your MySQL database credentials to connect your application.
    </p>
@endsection

@section('content')
    <livewire:install.database-form />
@endsection
