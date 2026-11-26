@extends('install.layout', ['title' => 'Permissions Check', 'currentStep' => 2])

@section('header')
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">File Permissions</h2>
    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
        Verifying that the required files and directories are writable.
    </p>
@endsection

@section('content')
    <div class="space-y-4">
        <!-- Permissions Table -->
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            File/Directory
                        </th>
                        <th scope="col" class="px-6 py-3 text-right">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($permissions as $path => $status)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-mono text-xs font-medium text-gray-900 dark:text-white">
                                {{ $path }}
                            </th>
                            <td class="px-6 py-4 text-right">
                                @if ($status)
                                    <div class="inline-flex items-center text-green-600 dark:text-green-500">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="ml-2">Writable</span>
                                    </div>
                                @else
                                    <div class="inline-flex items-center text-red-600 dark:text-red-500">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="ml-2">Not writable</span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        @php
            $passedCount = count(array_filter($permissions, fn($status) => $status === true));
            $totalCount = count($permissions);
            $allPassed = $passedCount === $totalCount;
        @endphp

        <div class="flex items-center justify-between p-4 rounded-lg {{ $allPassed ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
            <div class="flex items-center">
                @if ($allPassed)
                    <svg class="w-5 h-5 text-green-600 dark:text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium text-green-800 dark:text-green-400">
                        All permissions correct!
                    </span>
                @else
                    <svg class="w-5 h-5 text-red-600 dark:text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium text-red-800 dark:text-red-400">
                        {{ $totalCount - $passedCount }} permission issue(s)
                    </span>
                @endif
            </div>
            <span class="text-sm font-medium {{ $allPassed ? 'text-green-800 dark:text-green-400' : 'text-red-800 dark:text-red-400' }}">
                {{ $passedCount }}/{{ $totalCount }} passed
            </span>
        </div>

        <!-- Help Text -->
        @if (!$allPassed)
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg dark:bg-yellow-900/20 dark:border-yellow-800">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-400">
                            How to fix permission issues
                        </h3>
                        <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                            <p>Run these commands on your server:</p>
                            <pre class="mt-2 p-2 bg-yellow-100 dark:bg-yellow-900/40 rounded text-xs overflow-x-auto">chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache</pre>
                            <p class="mt-2 text-xs">Then refresh this page to check again.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('footer')
    <div class="flex items-center justify-between">
        <a href="{{ route('install.requirements') }}" class="px-5 py-2.5 text-sm font-medium text-gray-900 focus:outline-hidden bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
            <svg class="w-5 h-5 mr-2 -ml-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>

        @if ($allPassed)
            <a href="{{ route('install.database') }}" class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-hidden focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Next
                <svg class="w-5 h-5 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        @else
            <button type="button" disabled class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-gray-400 rounded-lg cursor-not-allowed dark:bg-gray-600">
                Next
                <svg class="w-5 h-5 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </button>
        @endif
    </div>
@endsection
