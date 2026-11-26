@extends('install.layout', ['title' => 'Requirements Check', 'currentStep' => 1])

@section('header')
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">System Requirements</h2>
    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
        Checking if your server meets the minimum requirements to run Appy.
    </p>
@endsection

@section('content')
    <div class="space-y-4">
        <!-- Requirements Table -->
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Requirement
                        </th>
                        <th scope="col" class="px-6 py-3 text-right">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dependencies as $name => $status)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $name }}
                            </th>
                            <td class="px-6 py-4 text-right">
                                @if ($status)
                                    <div class="inline-flex items-center text-green-600 dark:text-green-500">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="inline-flex items-center text-red-600 dark:text-red-500">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
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
            $passedCount = count(array_filter($dependencies, fn($status) => $status === true));
            $totalCount = count($dependencies);
            $allPassed = $passedCount === $totalCount;
        @endphp

        <div class="flex items-center justify-between p-4 rounded-lg {{ $allPassed ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
            <div class="flex items-center">
                @if ($allPassed)
                    <svg class="w-5 h-5 text-green-600 dark:text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium text-green-800 dark:text-green-400">
                        All requirements met!
                    </span>
                @else
                    <svg class="w-5 h-5 text-red-600 dark:text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-medium text-red-800 dark:text-red-400">
                        {{ $totalCount - $passedCount }} requirement(s) not met
                    </span>
                @endif
            </div>
            <span class="text-sm font-medium {{ $allPassed ? 'text-green-800 dark:text-green-400' : 'text-red-800 dark:text-red-400' }}">
                {{ $passedCount }}/{{ $totalCount }} passed
            </span>
        </div>
    </div>
@endsection

@section('footer')
    <div class="flex items-center justify-between">
        <a href="{{ route('install') }}" class="px-5 py-2.5 text-sm font-medium text-gray-900 focus:outline-hidden bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
            <svg class="w-5 h-5 mr-2 -ml-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>

        @if ($allPassed)
            <a href="{{ route('install.permissions') }}" class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-hidden focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
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
