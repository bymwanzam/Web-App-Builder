<div>
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-cyan-100 dark:bg-cyan-900 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.99999 10.8571 12 13.1428m-4.00001-2.2857L4 13.1428m3.99999-2.2857.00004-4.57139M12 13.1428v4.5715m0-4.5715-4.00001 2.2857M12 13.1428l4-2.2857m-4 2.2857V8.57143m0 4.57137 4 2.2858m-4 2.2857L7.99999 20M12 17.7143 16 20m-8.00001 0L4 17.7143v-4.5715M7.99999 20v-4.5715M4 13.1428l3.99999 2.2857M16 6.28571 12 4 8.00003 6.28571m7.99997 0v4.57139m0-4.57139-4 2.28572m4 2.28567 4 2.2858M8.00003 6.28571 12 8.57143m8 4.57147v4.5714L16 20m4-6.8571-4 2.2857M16 20v-4.5714"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Plugins</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 hidden sm:block">
                    Extend functionality with custom plugins
                </p>
            </div>
        </div>
        @if(config('app.demo'))
            <button type="button" disabled title="Disabled in demo mode" class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-blue-700 rounded-lg dark:bg-blue-600 cursor-not-allowed opacity-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <span class="hidden sm:inline">Upload Plugin</span>
            </button>
        @else
            <button wire:click="$dispatch('upload-plugin')" type="button" class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-hidden focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <span class="hidden sm:inline">Upload Plugin</span>
            </button>
        @endif
    </div>

    <!-- Search Bar -->
    <div class="relative mb-6">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <input type="text" wire:model.live="search" placeholder="Search plugins by name..." class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
    </div>

    @if(count($plugins) === 0)
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                @if($search)
                    No plugins found
                @else
                    No plugins installed
                @endif
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                @if($search)
                    Try a different search term
                @else
                    Click <strong>Upload Plugin</strong> button to install your first plugin
                @endif
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($plugins as $plugin)
                @php
                    $instance = $plugin->getInstance();
                @endphp
                <div class="bg-white border border-gray-200 rounded-lg shadow-xs p-6 dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-lg flex items-center justify-center p-1.5">
                            @php
                                $iconPath = $instance->getIcon();
                            @endphp
                            @if($iconPath)
                                <img src="{{ asset($iconPath) }}" alt="{{ $plugin->name }}" class="w-full h-full object-contain" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                </svg>
                            @endif
                        </div>
                        @if($plugin->status === 'active')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Active</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Inactive</span>
                        @endif
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $plugin->name }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ $instance->getDescription() }}</p>

                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs text-gray-500 dark:text-gray-400">v{{ $plugin->version }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            by <a href="{{ $instance->getAuthorUrl() }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:underline">{{ $instance->getAuthor() }}</a>
                        </span>
                    </div>

                    <div class="flex flex-col gap-2">
                        <div class="flex gap-2">
                            <button wire:click="configure({{ $plugin->id }})" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 dark:bg-blue-600 dark:hover:bg-blue-700">
                                Configure
                            </button>
                            @if($plugin->status === 'active')
                                @if(config('app.demo'))
                                    <button disabled title="Disabled in demo mode" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 cursor-not-allowed opacity-50">
                                        Deactivate
                                    </button>
                                @else
                                    <button wire:click="deactivate({{ $plugin->id }})" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                                        Deactivate
                                    </button>
                                @endif
                            @elseif($instance->isConfigured())
                                @if(config('app.demo'))
                                    <button disabled title="Disabled in demo mode" class="px-4 py-2 text-sm font-medium text-white bg-green-700 rounded-lg dark:bg-green-600 cursor-not-allowed opacity-50">
                                        Activate
                                    </button>
                                @else
                                    <button wire:click="activate({{ $plugin->id }})" class="px-4 py-2 text-sm font-medium text-white bg-green-700 rounded-lg hover:bg-green-800 dark:bg-green-600 dark:hover:bg-green-700">
                                        Activate
                                    </button>
                                @endif
                            @endif
                        </div>
                        @if(config('app.demo'))
                            <button disabled title="Disabled in demo mode" class="w-full px-4 py-2 text-sm font-medium text-red-700 bg-white border border-red-300 rounded-lg dark:bg-gray-700 dark:text-red-400 dark:border-red-600 cursor-not-allowed opacity-50">
                                Uninstall
                            </button>
                        @else
                            <button wire:click="uninstall({{ $plugin->id }})" class="w-full px-4 py-2 text-sm font-medium text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50 dark:bg-gray-700 dark:text-red-400 dark:border-red-600 dark:hover:bg-red-900/20">
                                Uninstall
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
