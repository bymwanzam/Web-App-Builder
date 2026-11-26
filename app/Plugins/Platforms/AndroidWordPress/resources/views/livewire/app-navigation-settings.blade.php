<form wire:submit.prevent="save">

    <!-- Bottom Navigation Bar - Only show when layout is app_bar_tabs -->
    @if($app->androidWordPressConfig->layout_template === 'app_bar_tabs')
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-card-icon">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="settings-card-title">Bottom Navigation Tabs</h3>
                    <p class="settings-card-description">Configure bottom navigation items (max 8 + home)</p>
                </div>
                <button type="button" wire:click="addNavigationItem"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    + Add Tab
                </button>
            </div>

            <div class="space-y-4">
                @foreach($navigation_items as $index => $item)
                    @php $isHome = isset($item['is_home']) && $item['is_home']; @endphp
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 border {{ $isHome ? 'border-blue-500' : 'border-gray-200 dark:border-gray-600' }} rounded-lg">
                        @if($isHome)
                            <div class="mb-3 inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-medium rounded-full">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                </svg>
                                <span>Home Tab - Shows All Posts</span>
                            </div>
                        @endif

                        <div class="flex items-start gap-3">
                            <div class="flex flex-col gap-1">
                                <button type="button" wire:click="moveUp({{ $index }})" @if($index === 0) disabled @endif
                                        class="p-1 text-gray-400 hover:text-gray-600 disabled:opacity-30">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    </svg>
                                </button>
                                <button type="button" wire:click="moveDown({{ $index }})" @if($index === count($navigation_items) - 1) disabled @endif
                                        class="p-1 text-gray-400 hover:text-gray-600 disabled:opacity-30">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="flex-1 space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">Label</label>
                                        <input type="text" wire:model="navigation_items.{{ $index }}.label" placeholder="Tab Name" maxlength="50"
                                               class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                    </div>
                                    <div x-data="iconSelector($wire, 'navigation_items', {{ $index }})">
                                        <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">Icon</label>
                                        <button type="button" @click="open = true"
                                                class="flex items-center gap-3 px-4 py-2.5 w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg hover:bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:hover:bg-gray-700">
                                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" x-html="getIconSvg(selectedIcon || '{{ $item['icon'] ?? 'home' }}')"></svg>
                                            <span class="flex-1 text-left" x-text="icons[selectedIcon || '{{ $item['icon'] ?? 'home' }}']"></span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>

                                        <!-- Icon Selector Modal -->
                                        <div x-show="open" x-cloak @click.away="open = false"
                                             class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                            <div class="flex items-center justify-center min-h-screen px-4">
                                                <div class="fixed inset-0 bg-gray-500/75 transition-opacity"></div>
                                                <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full p-6 shadow-xl">
                                                    <div class="flex items-center justify-between mb-4">
                                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Select Icon</h3>
                                                        <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                            </svg>
                                                        </button>
                                                    </div>

                                                    <!-- Search -->
                                                    <input type="text" x-model="searchTerm" placeholder="Search icons..."
                                                           class="w-full px-4 py-2 mb-4 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">

                                                    <!-- Icon Grid -->
                                                    <div class="grid grid-cols-5 gap-3 max-h-96 overflow-y-auto">
                                                        <template x-for="[key, name] in filteredIcons" :key="key">
                                                            <button type="button" @click="selectIcon(key)"
                                                                    class="flex flex-col items-center justify-center p-4 border-2 rounded-lg transition-all hover:bg-gray-50 dark:hover:bg-gray-700"
                                                                    :class="selectedIcon === key ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600'">
                                                                <svg class="w-8 h-8 mb-2 text-gray-700 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20" x-html="getIconSvg(key)"></svg>
                                                                <span class="text-xs text-gray-600 dark:text-gray-400 text-center" x-text="name"></span>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if(!$isHome)
                                    <div>
                                        <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">Action Type</label>
                                        <select wire:model.live="navigation_items.{{ $index }}.action_type"
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                            <option value="category">Category</option>
                                            <option value="tag">Tag</option>
                                            <option value="search">Search</option>
                                            <option value="bookmarks">Bookmarks</option>
                                            @if(auth()->user()->canUseHostedPages() && $this->getAvailableHostedPages()->count() > 0)
                                                <option value="hosted_page">Hosted Page</option>
                                            @endif
                                            <option value="internal">Internal URL</option>
                                            <option value="external">External URL</option>
                                        </select>
                                    </div>

                                    @php $actionType = $item['action_type'] ?? 'category'; @endphp
                                    @if(in_array($actionType, ['category', 'tag']))
                                        <div>
                                            <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">
                                                {{ $actionType === 'category' ? 'Category Slug' : 'Tag Slug' }}
                                            </label>
                                            <input type="text" wire:model="navigation_items.{{ $index }}.value"
                                                   placeholder="{{ $actionType === 'category' ? 'technology' : 'featured' }}"
                                                   class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                        </div>
                                    @elseif(in_array($actionType, ['internal', 'external']))
                                        <div>
                                            <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">URL</label>
                                            <input type="url" wire:model="navigation_items.{{ $index }}.value" placeholder="https://example.com"
                                                   class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                        </div>
                                    @elseif($actionType === 'hosted_page')
                                        <div>
                                            <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">Hosted Page</label>
                                            <select wire:model="navigation_items.{{ $index }}.value"
                                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                                <option value="">Select a page</option>
                                                @foreach($this->getAvailableHostedPages() as $page)
                                                    <option value="{{ $page->id }}">{{ $page->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <button type="button" wire:click="removeNavigationItem({{ $index }})"
                                    @if($isHome) disabled @endif
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg {{ $isHome ? 'opacity-30 cursor-not-allowed' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- App Bar Buttons - Show when layout has app bar -->
    @if(in_array($app->androidWordPressConfig->layout_template, ['app_bar', 'app_bar_tabs', 'app_bar_drawer']))
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-card-icon">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="settings-card-title">App Bar Buttons</h3>
                    <p class="settings-card-description">Action buttons in the top right (max 3)</p>
                </div>
                <button type="button" wire:click="addAppBarButton"
                        @if(count($app_bar_buttons) >= 3) disabled @endif
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:bg-gray-300 transition-colors">
                    + Add Button
                </button>
            </div>

            @if(count($app_bar_buttons) === 0)
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>No app bar buttons configured. Add search, share, or other quick actions.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($app_bar_buttons as $index => $item)
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg">
                            <div class="flex items-start gap-3">
                                <div class="flex-1 space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">Label</label>
                                            <input type="text" wire:model="app_bar_buttons.{{ $index }}.label" placeholder="Search" maxlength="50"
                                                   class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                        </div>
                                        <div x-data="iconSelector($wire, 'app_bar_buttons', {{ $index }})">
                                            <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">Icon</label>
                                            <button type="button" @click="open = true"
                                                    class="flex items-center gap-3 px-4 py-2.5 w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg hover:bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:hover:bg-gray-700">
                                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" x-html="getIconSvg(selectedIcon || '{{ $item['icon'] ?? 'magnifying-glass' }}')"></svg>
                                                <span class="flex-1 text-left" x-text="icons[selectedIcon || '{{ $item['icon'] ?? 'magnifying-glass' }}']"></span>
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>

                                            <!-- Icon Selector Modal -->
                                            <div x-show="open" x-cloak @click.away="open = false"
                                                 class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                                <div class="flex items-center justify-center min-h-screen px-4">
                                                    <div class="fixed inset-0 bg-gray-500/75 transition-opacity"></div>
                                                    <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full p-6 shadow-xl">
                                                        <div class="flex items-center justify-between mb-4">
                                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Select Icon</h3>
                                                            <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                </svg>
                                                            </button>
                                                        </div>

                                                        <!-- Search -->
                                                        <input type="text" x-model="searchTerm" placeholder="Search icons..."
                                                               class="w-full px-4 py-2 mb-4 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">

                                                        <!-- Icon Grid -->
                                                        <div class="grid grid-cols-5 gap-3 max-h-96 overflow-y-auto">
                                                            <template x-for="[key, name] in filteredIcons" :key="key">
                                                                <button type="button" @click="selectIcon(key)"
                                                                        class="flex flex-col items-center justify-center p-4 border-2 rounded-lg transition-all hover:bg-gray-50 dark:hover:bg-gray-700"
                                                                        :class="selectedIcon === key ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600'">
                                                                    <svg class="w-8 h-8 mb-2 text-gray-700 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20" x-html="getIconSvg(key)"></svg>
                                                                    <span class="text-xs text-gray-600 dark:text-gray-400 text-center" x-text="name"></span>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">Action Type</label>
                                        <select wire:model.live="app_bar_buttons.{{ $index }}.action_type"
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                            <option value="search">Search</option>
                                            <option value="bookmarks">Bookmarks</option>
                                            <option value="share">Share</option>
                                            @if(auth()->user()->canUseHostedPages() && $this->getAvailableHostedPages()->count() > 0)
                                                <option value="hosted_page">Hosted Page</option>
                                            @endif
                                            <option value="internal">Internal URL</option>
                                            <option value="external">External URL</option>
                                        </select>
                                    </div>

                                    @php $actionType = $item['action_type'] ?? 'search'; @endphp
                                    @if(in_array($actionType, ['internal', 'external']))
                                        <div>
                                            <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">URL</label>
                                            <input type="url" wire:model="app_bar_buttons.{{ $index }}.value" placeholder="https://example.com"
                                                   class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                        </div>
                                    @elseif($actionType === 'hosted_page')
                                        <div>
                                            <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">Hosted Page</label>
                                            <select wire:model="app_bar_buttons.{{ $index }}.value"
                                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                                <option value="">Select a page</option>
                                                @foreach($this->getAvailableHostedPages() as $page)
                                                    <option value="{{ $page->id }}">{{ $page->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                </div>

                                <button type="button" wire:click="removeAppBarButton({{ $index }})"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <!-- Drawer Menu Items - Only show when layout is app_bar_drawer -->
    @if($app->androidWordPressConfig->layout_template === 'app_bar_drawer')
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-card-icon">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="settings-card-title">Drawer Menu Items</h3>
                    <p class="settings-card-description">Navigation drawer menu items (1 home + up to 9 items)</p>
                </div>
                <button type="button" wire:click="addDrawerItem"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    + Add Item
                </button>
            </div>

            <div class="space-y-4">
                @foreach($drawer_items as $index => $item)
                    @php $isHome = isset($item['is_home']) && $item['is_home']; @endphp
                    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 border {{ $isHome ? 'border-blue-500' : 'border-gray-200 dark:border-gray-600' }} rounded-lg">
                        @if($isHome)
                            <div class="mb-3 inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-medium rounded-full">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                </svg>
                                <span>Home Item - Shows All Posts</span>
                            </div>
                        @endif

                        <div class="flex items-start gap-3">
                            <div class="flex flex-col gap-1">
                                <button type="button" wire:click="moveDrawerItemUp({{ $index }})" @if($index === 0) disabled @endif
                                        class="p-1 text-gray-400 hover:text-gray-600 disabled:opacity-30">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    </svg>
                                </button>
                                <button type="button" wire:click="moveDrawerItemDown({{ $index }})" @if($index === count($drawer_items) - 1) disabled @endif
                                        class="p-1 text-gray-400 hover:text-gray-600 disabled:opacity-30">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="flex-1 space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">Label</label>
                                        <input type="text" wire:model="drawer_items.{{ $index }}.label" placeholder="Menu Item" maxlength="50"
                                               class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                    </div>
                                    <div x-data="iconSelector($wire, 'drawer_items', {{ $index }})">
                                        <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">Icon</label>
                                        <button type="button" @click="open = true"
                                                class="flex items-center gap-3 px-4 py-2.5 w-full bg-white border border-gray-300 text-gray-900 text-sm rounded-lg hover:bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:hover:bg-gray-700">
                                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" x-html="getIconSvg(selectedIcon || '{{ $item['icon'] ?? 'home' }}')"></svg>
                                            <span class="flex-1 text-left" x-text="icons[selectedIcon || '{{ $item['icon'] ?? 'home' }}']"></span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>

                                        <!-- Icon Selector Modal -->
                                        <div x-show="open" x-cloak @click.away="open = false"
                                             class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                                            <div class="flex items-center justify-center min-h-screen px-4">
                                                <div class="fixed inset-0 bg-gray-500/75 transition-opacity"></div>
                                                <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full p-6 shadow-xl">
                                                    <div class="flex items-center justify-between mb-4">
                                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Select Icon</h3>
                                                        <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                            </svg>
                                                        </button>
                                                    </div>

                                                    <!-- Search -->
                                                    <input type="text" x-model="searchTerm" placeholder="Search icons..."
                                                           class="w-full px-4 py-2 mb-4 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">

                                                    <!-- Icon Grid -->
                                                    <div class="grid grid-cols-5 gap-3 max-h-96 overflow-y-auto">
                                                        <template x-for="[key, name] in filteredIcons" :key="key">
                                                            <button type="button" @click="selectIcon(key)"
                                                                    class="flex flex-col items-center justify-center p-4 border-2 rounded-lg transition-all hover:bg-gray-50 dark:hover:bg-gray-700"
                                                                    :class="selectedIcon === key ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600'">
                                                                <svg class="w-8 h-8 mb-2 text-gray-700 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20" x-html="getIconSvg(key)"></svg>
                                                                <span class="text-xs text-gray-600 dark:text-gray-400 text-center" x-text="name"></span>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if(!$isHome)
                                    <div>
                                        <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">Action Type</label>
                                        <select wire:model.live="drawer_items.{{ $index }}.action_type"
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                            <option value="category">Category</option>
                                            <option value="tag">Tag</option>
                                            <option value="search">Search</option>
                                            <option value="bookmarks">Bookmarks</option>
                                            @if(auth()->user()->canUseHostedPages() && $this->getAvailableHostedPages()->count() > 0)
                                                <option value="hosted_page">Hosted Page</option>
                                            @endif
                                            <option value="internal">Internal URL</option>
                                            <option value="external">External URL</option>
                                        </select>
                                    </div>

                                    @php $actionType = $item['action_type'] ?? 'category'; @endphp
                                    @if(in_array($actionType, ['category', 'tag']))
                                        <div>
                                            <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">
                                                {{ $actionType === 'category' ? 'Category Slug' : 'Tag Slug' }}
                                            </label>
                                            <input type="text" wire:model="drawer_items.{{ $index }}.value"
                                                   placeholder="{{ $actionType === 'category' ? 'technology' : 'featured' }}"
                                                   class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                        </div>
                                    @elseif(in_array($actionType, ['internal', 'external']))
                                        <div>
                                            <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">URL</label>
                                            <input type="url" wire:model="drawer_items.{{ $index }}.value" placeholder="https://example.com"
                                                   class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                        </div>
                                    @elseif($actionType === 'hosted_page')
                                        <div>
                                            <label class="block mb-2 text-xs font-medium text-gray-900 dark:text-white">Hosted Page</label>
                                            <select wire:model="drawer_items.{{ $index }}.value"
                                                    class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                                <option value="">Select a page</option>
                                                @foreach($this->getAvailableHostedPages() as $page)
                                                    <option value="{{ $page->id }}">{{ $page->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <button type="button" wire:click="removeDrawerItem({{ $index }})"
                                    @if($isHome) disabled @endif
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg {{ $isHome ? 'opacity-30 cursor-not-allowed' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Behavior Settings Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Behavior Settings</h3>
                <p class="settings-card-description">Configure user interactions</p>
            </div>
        </div>

        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Pull to Refresh</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Allow users to refresh content by pulling down</p>
                </div>
                <x-toggle wire-model="pull_to_refresh" :live="true" />
            </div>

            @if($app->androidWordPressConfig->layout_template !== 'app_bar_drawer')
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Swipe Navigation</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Navigate back by swiping from edge</p>
                    </div>
                    <x-toggle wire-model="swipe_navigation" :live="true" />
                </div>
            @endif

            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Preserve Tab State</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Keep scroll position when switching tabs</p>
                </div>
                <x-toggle wire-model="preserve_tab_state" :live="true" />
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="flex items-center justify-between pt-4">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-hidden focus:ring-blue-300 font-medium rounded-lg text-sm px-6 py-3 text-center dark:bg-blue-600 dark:hover:bg-blue-700 transition-all">
            {{ __('Save') }}
        </button>
    </div>

</form>

@push('scripts')
<script>
// Track if we've already registered to prevent duplicate registrations
window.wpIconSelectorRegistered = window.wpIconSelectorRegistered || false;

// Register Alpine.js component - works on both initial load and Livewire navigate
function registerWpIconSelector() {
    if (typeof Alpine === 'undefined') {
        // Alpine not loaded yet, wait for it
        setTimeout(registerWpIconSelector, 50);
        return;
    }

    // Prevent duplicate registration
    if (window.wpIconSelectorRegistered) {
        return;
    }
    window.wpIconSelectorRegistered = true;

    // Register the component
    Alpine.data('iconSelector', (wire, field, index) => ({
        open: false,
        searchTerm: '',
        selectedIcon: '',
        icons: @json($availableIcons),

        init() {
            // Get current icon value
            this.selectedIcon = this.getCurrentIcon();
        },

        getCurrentIcon() {
            const parts = field.split('.');
            let value = this.$wire.get(parts[0]);
            if (value && value[index]) {
                return value[index].icon || 'home';
            }
            return 'home';
        },

        get filteredIcons() {
            if (!this.searchTerm) {
                return Object.entries(this.icons);
            }
            return Object.entries(this.icons).filter(([key, name]) =>
                name.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                key.toLowerCase().includes(this.searchTerm.toLowerCase())
            );
        },

        selectIcon(iconKey) {
            this.selectedIcon = iconKey;
            // Update nested property (navigation_items.0.icon or app_bar_buttons.0.icon)
            this.$wire.set(`${field}.${index}.icon`, iconKey);
            this.open = false;
        },

        getIconSvg(iconKey) {
            const iconSvgs = {
                'home': '<path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>',
                'newspaper': '<path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"/><path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z"/>',
                'tag': '<path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>',
                'folder': '<path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>',
                'magnifying-glass': '<path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>',
                'bookmark': '<path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/>',
                'heart': '<path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>',
                'user': '<path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>',
                'calendar': '<path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>',
                'fire': '<path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/>',
                'globe': '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM4.332 8.027a6.012 6.012 0 011.912-2.706C6.512 5.73 6.974 6 7.5 6A1.5 1.5 0 019 7.5V8a2 2 0 004 0 2 2 0 011.523-1.943A5.977 5.977 0 0116 10c0 .34-.028.675-.083 1H15a2 2 0 00-2 2v.878A2.999 2.999 0 0110 15a3 3 0 01-2.236-1H8a2 2 0 00-2-2v-1c0-.537.213-1.053.59-1.404a2 2 0 002.805-2.805A2.989 2.989 0 0110 7V5.5A5.972 5.972 0 004.332 8.027z" clip-rule="evenodd"/>',
                'info': '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>',
                'settings': '<path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>',
                'share': '<path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z"/>'
            };
            return iconSvgs[iconKey] || iconSvgs['home'];
        }
    }));
}

// Register on both initial load and Livewire navigation
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', registerWpIconSelector);
} else {
    registerWpIconSelector();
}

document.addEventListener('livewire:navigated', registerWpIconSelector);
</script>
@endpush
