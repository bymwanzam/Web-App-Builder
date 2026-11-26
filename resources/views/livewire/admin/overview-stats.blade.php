<div>
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v15a1 1 0 0 0 1 1h15M8 16l2.5-5.5 3 3L17.273 7 20 9.667"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Admin Overview</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 hidden sm:block">
                    System-wide statistics and administration metrics
                </p>
            </div>
        </div>
    </div>

    {{-- Date Range Filter & Actions --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="flex flex-wrap gap-2">
            <button wire:click="setDateRange('today')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $dateRange === 'today' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                Today
            </button>
            <button wire:click="setDateRange('week')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $dateRange === 'week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                Week
            </button>
            <button wire:click="setDateRange('month')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $dateRange === 'month' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                Month
            </button>
            <button wire:click="setDateRange('year')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $dateRange === 'year' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                Year
            </button>
            <button wire:click="setDateRange('all')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $dateRange === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                All Time
            </button>
        </div>

        <div class="flex flex-wrap gap-2">
            <button wire:click="toggleAutoRefresh" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $autoRefresh ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                <svg class="w-4 h-4 inline-block mr-1 {{ $autoRefresh ? 'animate-spin' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Auto-Refresh
            </button>
            <button wire:click="refreshStats" class="px-4 py-2 text-sm font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh
            </button>
        </div>
    </div>

    {{-- Main Metrics Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Card 1: Total Users --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-xs p-6 dark:bg-gray-800 dark:border-gray-700 hover:shadow-md transition-all hover:-translate-y-1">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h5 class="text-4xl font-bold leading-none text-gray-900 dark:text-white mb-2">{{ number_format($stats['users_count']) }}</h5>
                    <p class="text-sm font-normal text-gray-500 dark:text-gray-400">Total System Users</p>
                </div>
                <div class="flex-shrink-0 w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 18">
                        <path d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z"/>
                    </svg>
                </div>
            </div>
            <div class="flex items-center text-sm">
                <span class="flex items-center {{ $stats['users_growth'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stats['users_growth'] >= 0 ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3' }}"/>
                    </svg>
                    {{ $stats['users_growth'] >= 0 ? '+' : '' }}{{ $stats['users_growth'] }}%
                </span>
                <span class="text-gray-500 dark:text-gray-400 ml-2">vs last month</span>
            </div>
        </div>

        {{-- Card 2: Active Subscriptions --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-xs p-6 dark:bg-gray-800 dark:border-gray-700 hover:shadow-md transition-all hover:-translate-y-1">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h5 class="text-4xl font-bold leading-none text-gray-900 dark:text-white mb-2">{{ number_format($stats['active_subscriptions']) }}</h5>
                    <p class="text-sm font-normal text-gray-500 dark:text-gray-400">Active Subscriptions</p>
                </div>
                <div class="flex-shrink-0 w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 0 0-2 2v1h16V6a2 2 0 0 0-2-2H4Z"/>
                        <path d="M18 9H2v5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9ZM4 13a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-1Zm5-1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-1a1 1 0 0 0-1-1H9Z"/>
                    </svg>
                </div>
            </div>
            <div class="flex items-center text-sm">
                <span class="flex items-center {{ $stats['subscriptions_growth'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stats['subscriptions_growth'] >= 0 ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3' }}"/>
                    </svg>
                    {{ $stats['subscriptions_growth'] >= 0 ? '+' : '' }}{{ $stats['subscriptions_growth'] }}%
                </span>
                <span class="text-gray-500 dark:text-gray-400 ml-2">vs last month</span>
            </div>
        </div>

        {{-- Card 3: Revenue --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-xs p-6 dark:bg-gray-800 dark:border-gray-700 hover:shadow-md transition-all hover:-translate-y-1">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h5 class="text-4xl font-bold leading-none text-gray-900 dark:text-white mb-2">${{ number_format($stats['monthly_revenue'], 2) }}</h5>
                    <p class="text-sm font-normal text-gray-500 dark:text-gray-400">{{ ucfirst($dateRange) }} Revenue</p>
                </div>
                <div class="flex-shrink-0 w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            <div class="flex items-center text-sm">
                <span class="flex items-center {{ $stats['revenue_growth'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stats['revenue_growth'] >= 0 ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3' }}"/>
                    </svg>
                    {{ $stats['revenue_growth'] >= 0 ? '+' : '' }}{{ $stats['revenue_growth'] }}%
                </span>
                <span class="text-gray-500 dark:text-gray-400 ml-2">vs last month</span>
            </div>
        </div>

        {{-- Card 4: Total Apps --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-xs p-6 dark:bg-gray-800 dark:border-gray-700 hover:shadow-md transition-all hover:-translate-y-1">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h5 class="text-4xl font-bold leading-none text-gray-900 dark:text-white mb-2">{{ number_format($stats['total_apps']) }}</h5>
                    <p class="text-sm font-normal text-gray-500 dark:text-gray-400">Total Apps</p>
                </div>
                <div class="flex-shrink-0 w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="currentColor" viewBox="0 0 18 18">
                        <path d="M6.143 0H1.857A1.857 1.857 0 0 0 0 1.857v4.286C0 7.169.831 8 1.857 8h4.286A1.857 1.857 0 0 0 8 6.143V1.857A1.857 1.857 0 0 0 6.143 0Zm10 0h-4.286A1.857 1.857 0 0 0 10 1.857v4.286C10 7.169 10.831 8 11.857 8h4.286A1.857 1.857 0 0 0 18 6.143V1.857A1.857 1.857 0 0 0 16.143 0Zm-10 10H1.857A1.857 1.857 0 0 0 0 11.857v4.286C0 17.169.831 18 1.857 18h4.286A1.857 1.857 0 0 0 8 16.143v-4.286A1.857 1.857 0 0 0 6.143 10Zm10 0h-4.286A1.857 1.857 0 0 0 10 11.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 18 16.143v-4.286A1.857 1.857 0 0 0 16.143 10Z"/>
                    </svg>
                </div>
            </div>
            <div class="flex items-center text-sm">
                <span class="flex items-center {{ $stats['apps_growth'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stats['apps_growth'] >= 0 ? 'M5 10l7-7m0 0l7 7m-7-7v18' : 'M19 14l-7 7m0 0l-7-7m7 7V3' }}"/>
                    </svg>
                    {{ $stats['apps_growth'] >= 0 ? '+' : '' }}{{ $stats['apps_growth'] }}%
                </span>
                <span class="text-gray-500 dark:text-gray-400 ml-2">vs last month</span>
            </div>
        </div>
    </div>

    {{-- Build Metrics Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total Builds --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-xs p-6 dark:bg-gray-800 dark:border-gray-700 hover:shadow-md transition-all hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-1">Total Builds</p>
                    <h5 class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_builds']) }}</h5>
                </div>
                <div class="flex-shrink-0 w-10 h-10 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Completed Today --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-xs p-6 dark:bg-gray-800 dark:border-gray-700 hover:shadow-md transition-all hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-1">Completed Today</p>
                    <h5 class="text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['completed_builds_today']) }}</h5>
                </div>
                <div class="flex-shrink-0 w-10 h-10 bg-green-50 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Build Success Rate --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-xs p-6 dark:bg-gray-800 dark:border-gray-700 hover:shadow-md transition-all hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-1">Build Success Rate</p>
                    <h5 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['build_success_rate'] }}%</h5>
                </div>
                <div class="flex-shrink-0 w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Pending Queue --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-xs p-6 dark:bg-gray-800 dark:border-gray-700 hover:shadow-md transition-all hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-1">Pending Queue</p>
                    <h5 class="text-3xl font-bold {{ $stats['pending_queue'] > 10 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">{{ number_format($stats['pending_queue']) }}</h5>
                </div>
                <div class="flex-shrink-0 w-10 h-10 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Secondary Metrics Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        {{-- Total Transactions --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-xs p-6 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-1">Total Transactions</p>
                    <h5 class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_transactions']) }}</h5>
                </div>
                <div class="flex-shrink-0 w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 5V.13a2.96 2.96 0 0 0-1.293.749L.879 3.707A2.96 2.96 0 0 0 .13 5H5Z"/>
                        <path d="M6.737 11.061a2.961 2.961 0 0 1 .81-1.515l6.117-6.116A4.839 4.839 0 0 1 16 2.141V2a1.97 1.97 0 0 0-1.933-2H7v5a2 2 0 0 1-2 2H0v11a1.969 1.969 0 0 0 1.933 2h12.134A1.97 1.97 0 0 0 16 18v-3.093l-1.546 1.546c-.413.413-.94.695-1.513.81l-3.4.679a2.947 2.947 0 0 1-1.85-.227 2.96 2.96 0 0 1-1.635-3.257l.681-3.397Z"/>
                        <path d="M8.961 16a.93.93 0 0 0 .189-.019l3.4-.679a.961.961 0 0 0 .49-.263l6.118-6.117a2.884 2.884 0 0 0-4.079-4.078l-6.117 6.117a.96.96 0 0 0-.263.491l-.679 3.4A.961.961 0 0 0 8.961 16Zm7.477-9.8a.958.958 0 0 1 .68-.281.961.961 0 0 1 .682 1.644l-.315.315-1.36-1.36.313-.318Zm-5.911 5.911 4.236-4.236 1.359 1.359-4.236 4.237-1.7.339.341-1.699Z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Active Plans --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-xs p-6 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-1">Active Plans</p>
                    <h5 class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['active_plans']) }}</h5>
                </div>
                <div class="flex-shrink-0 w-10 h-10 bg-green-50 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 11.424V1a1 1 0 1 0-2 0v10.424a3.228 3.228 0 0 0 0 6.152V19a1 1 0 1 0 2 0v-1.424a3.228 3.228 0 0 0 0-6.152ZM19.25 14.5A3.243 3.243 0 0 0 17 11.424V1a1 1 0 0 0-2 0v10.424a3.227 3.227 0 0 0 0 6.152V19a1 1 0 1 0 2 0v-1.424a3.243 3.243 0 0 0 2.25-3.076Zm-6-9A3.243 3.243 0 0 0 11 2.424V1a1 1 0 0 0-2 0v1.424a3.228 3.228 0 0 0 0 6.152V19a1 1 0 1 0 2 0V8.576A3.243 3.243 0 0 0 13.25 5.5Z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Active Builders --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-xs p-6 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-1">Active Builders</p>
                    <h5 class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['active_builders']) }}</h5>
                </div>
                <div class="flex-shrink-0 w-10 h-10 bg-purple-50 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Continue in next part due to length... --}}
    @include('livewire.admin.partials.overview-charts')
    @include('livewire.admin.partials.overview-analytics')
    @include('livewire.admin.partials.overview-recent-sections')

    {{-- Auto-refresh script --}}
    <div wire:poll.{{ $autoRefresh ? '30s' : '999999s' }}></div>
</div>
