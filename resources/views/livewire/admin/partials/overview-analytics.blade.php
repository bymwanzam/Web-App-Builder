{{-- Analytics Section --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Revenue by Payment Gateway --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-xs p-6 dark:bg-gray-800 dark:border-gray-700">
        <h5 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Revenue by Payment Gateway</h5>
        @if(count($stats['revenue_by_gateway']) > 0)
            <div class="space-y-3">
                @foreach($stats['revenue_by_gateway'] as $gateway)
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">{{ $gateway['gateway'] }}</span>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">${{ number_format($gateway['amount'], 2) }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">({{ $gateway['percentage'] }}%)</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="h-2.5 rounded-full"
                                 style="width: {{ $gateway['percentage'] }}%; background: linear-gradient(90deg, #8B5CF6 0%, #6366F1 100%);">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Revenue</span>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">
                        ${{ number_format($stats['total_revenue_all_time'], 2) }}
                    </span>
                </div>
            </div>
        @else
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm">No revenue data available yet</p>
            </div>
        @endif
    </div>

    {{-- Platform Distribution --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-xs p-6 dark:bg-gray-800 dark:border-gray-700">
        <h5 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Platform Distribution</h5>
        @if(count($stats['platform_distribution']) > 0)
            <div class="space-y-4">
                @php
                    $colors = [
                        'bg-green-600',
                        'bg-blue-600',
                        'bg-purple-600',
                        'bg-orange-600',
                        'bg-pink-600'
                    ];
                @endphp
                @foreach($stats['platform_distribution'] as $index => $platform)
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $platform['platform'] }}</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $platform['count'] }} ({{ $platform['percentage'] }}%)
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="{{ $colors[$index % count($colors)] }} h-2.5 rounded-full" style="width: {{ $platform['percentage'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Apps</span>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ number_format(array_sum(array_column($stats['platform_distribution'], 'count'))) }}
                    </span>
                </div>
            </div>
        @else
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-3" fill="currentColor" viewBox="0 0 18 18">
                    <path d="M6.143 0H1.857A1.857 1.857 0 0 0 0 1.857v4.286C0 7.169.831 8 1.857 8h4.286A1.857 1.857 0 0 0 8 6.143V1.857A1.857 1.857 0 0 0 6.143 0Zm10 0h-4.286A1.857 1.857 0 0 0 10 1.857v4.286C10 7.169 10.831 8 11.857 8h4.286A1.857 1.857 0 0 0 18 6.143V1.857A1.857 1.857 0 0 0 16.143 0Zm-10 10H1.857A1.857 1.857 0 0 0 0 11.857v4.286C0 17.169.831 18 1.857 18h4.286A1.857 1.857 0 0 0 8 16.143v-4.286A1.857 1.857 0 0 0 6.143 10Zm10 0h-4.286A1.857 1.857 0 0 0 10 11.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 18 16.143v-4.286A1.857 1.857 0 0 0 16.143 10Z"/>
                </svg>
                <p class="text-sm">No apps created yet</p>
            </div>
        @endif
    </div>
</div>

{{-- User Engagement & Additional Metrics --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Total Revenue All Time --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-xs p-5 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Total Revenue</p>
                <h5 class="text-xl font-bold text-gray-900 dark:text-white">${{ number_format($stats['total_revenue_all_time'], 2) }}</h5>
            </div>
            <div class="flex-shrink-0 w-9 h-9 bg-green-50 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Pending Payments --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-xs p-5 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Pending Payments</p>
                <h5 class="text-xl font-bold {{ $stats['pending_payments'] > 0 ? 'text-yellow-600 dark:text-yellow-400' : 'text-gray-900 dark:text-white' }}">
                    ${{ number_format($stats['pending_payments'], 2) }}
                </h5>
            </div>
            <div class="flex-shrink-0 w-9 h-9 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Active Users (Last Week) --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-xs p-5 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Active Users (7d)</p>
                <h5 class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['active_users_week']) }}</h5>
            </div>
            <div class="flex-shrink-0 w-9 h-9 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Push Notifications Sent Today --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-xs p-5 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Notifications (Today)</p>
                <h5 class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['push_notifications_sent_today']) }}</h5>
            </div>
            <div class="flex-shrink-0 w-9 h-9 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

{{-- Build Performance by Platform --}}
@if(count($stats['build_performance_by_platform']) > 0)
    <div class="bg-white border border-gray-200 rounded-lg shadow-xs p-6 dark:bg-gray-800 dark:border-gray-700 mb-6">
        <h5 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Build Performance by Platform</h5>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Platform</th>
                        <th scope="col" class="px-6 py-3 text-center">Total Builds</th>
                        <th scope="col" class="px-6 py-3 text-center">Completed</th>
                        <th scope="col" class="px-6 py-3 text-center">Failed</th>
                        <th scope="col" class="px-6 py-3 text-center">Success Rate</th>
                        <th scope="col" class="px-6 py-3 text-center">Avg Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['build_performance_by_platform'] as $platform)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white capitalize">
                                {{ $platform['platform'] }}
                            </th>
                            <td class="px-6 py-4 text-center">{{ number_format($platform['total']) }}</td>
                            <td class="px-6 py-4 text-center text-green-600 dark:text-green-400">{{ number_format($platform['completed']) }}</td>
                            <td class="px-6 py-4 text-center text-red-600 dark:text-red-400">{{ number_format($platform['failed']) }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $platform['success_rate'] >= 90 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : ($platform['success_rate'] >= 70 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300') }}">
                                    {{ $platform['success_rate'] }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($platform['avg_duration'])
                                    @php
                                        $minutes = floor($platform['avg_duration'] / 60);
                                        $seconds = $platform['avg_duration'] % 60;
                                    @endphp
                                    {{ $minutes > 0 ? "{$minutes}m " : '' }}{{ $seconds }}s
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
