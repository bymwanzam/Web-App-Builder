<!-- Android WordPress App Card -->
<div class="relative flex flex-col p-6 bg-white border border-gray-200 rounded-xl shadow-[0_1px_3px_0_rgb(0_0_0_/_0.1)] dark:bg-gray-800 dark:border-gray-700">
    <div class="flex flex-col flex-1">
        <div class="flex items-start justify-between mb-4">
            @if($app->androidWordPressConfig?->app_icon)
                <img src="{{ Storage::url($app->androidWordPressConfig->app_icon) }}" alt="{{ $app->name }}" class="w-14 h-14 rounded-xl object-cover">
            @else
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18.5A2.493 2.493 0 0 1 7.51 20H7.5a2.468 2.468 0 0 1-2.4-3.154 2.98 2.98 0 0 1-.85-5.274 2.468 2.468 0 0 1 .92-3.182 2.477 2.477 0 0 1 1.876-3.344 2.5 2.5 0 0 1 3.41-1.856A2.5 2.5 0 0 1 12 5.5m0 13v-13m0 13a2.493 2.493 0 0 0 4.49 1.5h.01a2.468 2.468 0 0 0 2.403-3.154 2.98 2.98 0 0 0 .847-5.274 2.468 2.468 0 0 0-.921-3.182 2.477 2.477 0 0 0-1.875-3.344A2.5 2.5 0 0 0 14.5 3 2.5 2.5 0 0 0 12 5.5m-8 5a2.5 2.5 0 0 1 3.48-2.3m-.28 8.551a3 3 0 0 1-2.953-5.185M20 10.5a2.5 2.5 0 0 0-3.481-2.3m.28 8.551a3 3 0 0 0 2.954-5.185"/>
                    </svg>
                </div>
            @endif
            <div class="flex flex-col items-end gap-1.5">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-300">
                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25Zm.75-12h9v9h-9v-9Z" />
                    </svg>
                    {{ $app->getPlatformInstance()->getName() }}
                </span>
                @php
                    $layoutLabels = [
                        'list' => ['label' => 'List', 'color' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'],
                        'app_bar' => ['label' => 'App Bar', 'color' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'],
                        'app_bar_tabs' => ['label' => 'App Bar + Tabs', 'color' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300'],
                        'app_bar_drawer' => ['label' => 'App Bar + Drawer', 'color' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300'],
                    ];
                    $layoutTemplate = $app->androidWordPressConfig->layout_template ?? 'app_bar_tabs';
                    $layout = $layoutLabels[$layoutTemplate] ?? ['label' => ucfirst($layoutTemplate), 'color' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'];
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium {{ $layout['color'] }}">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
                    </svg>
                    {{ $layout['label'] }}
                </span>
            </div>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $app->name }}</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 truncate mb-1">{{ $app->androidWordPressConfig->site_url ?? 'No Site URL' }}</p>

        @if($app->builds->count() > 0)
            @php
                $latestBuild = $app->builds->first();
            @endphp
            <div class="flex items-center gap-2 text-xs mb-3">
                <span class="text-gray-500 dark:text-gray-400">Latest build:</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded {{ $latestBuild->status_color }}">
                    {{ $latestBuild->status_text }}
                </span>
            </div>
        @else
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">No builds yet</p>
        @endif
    </div>

    <div class="flex gap-2 mt-auto pt-3 border-t border-gray-100 dark:border-gray-700">
        <a href="{{ route('user.app', $app) }}" wire:navigate class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 text-center">
            Edit
        </a>
        <button type="button" wire:click="$dispatch('delete-app', { id: {{ $app->id }} })" class="px-4 py-2 text-sm font-medium text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50 dark:bg-gray-700 dark:text-red-400 dark:border-red-600 dark:hover:bg-red-900/20">
            Delete
        </button>
    </div>
</div>
