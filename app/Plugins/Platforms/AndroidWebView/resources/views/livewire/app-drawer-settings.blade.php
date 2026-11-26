<form wire:submit.prevent="save">

    <!-- Drawer Appearance Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Drawer Appearance</h3>
                <p class="settings-card-description">Customize the background and visual style of your navigation drawer</p>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Drawer Mode -->
            <div>
                <label for="drawer_mode" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Drawer Mode
                </label>
                <select wire:model.live="drawer_mode" id="drawer_mode"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="default">Default (Theme Color)</option>
                    <option value="color">Custom Color</option>
                    <option value="image">Background Image</option>
                </select>
                @error('drawer_mode')
                    <small class="helper-text text-red-600 dark:text-red-400">{{ $message }}</small>
                @else
                    <small class="helper-text">Choose how the drawer background should appear</small>
                @enderror
            </div>

            <!-- Drawer Background Color (shown when mode is 'color') -->
            @if($drawer_mode === 'color')
                <div>
                    <label class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">
                        Background Color
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="color" wire:model.live="drawer_background_color"
                               class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600">
                        <input type="text" wire:model.live="drawer_background_color"
                               placeholder="#3b82f6"
                               class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    </div>
                    @error('drawer_background_color')
                        <small class="helper-text text-red-600 dark:text-red-400">{{ $message }}</small>
                    @else
                        <small class="helper-text">Custom background color for the drawer</small>
                    @enderror
                </div>
            @endif

            <!-- Drawer Background Image (shown when mode is 'image') -->
            @if($drawer_mode === 'image')
                <div wire:key="drawer-background-{{ $drawer_background_image ?? 'none' }}">
                    <x-file-upload
                        wire-model="drawer_background_image_file"
                        accept="image/png"
                        label="Background Image"
                        help-text="PNG only (MAX. 2MB). Recommended: 1080x1920px"
                        :current-file="$drawer_background_image"
                        :max-size="2"
                        preview-type="image"
                    />
                    @if($drawer_background_image)
                        <button
                            type="button"
                            wire:click="$dispatch('delete-drawer-background')"
                            class="mt-2 rounded-lg bg-red-700 px-3 py-2 text-xs font-medium text-white hover:bg-red-800 focus:outline-hidden focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800"
                        >
                            Delete Background Image
                        </button>
                    @endif
                </div>
            @endif

        </div>
    </div>

    <!-- Drawer Branding Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Drawer Branding</h3>
                <p class="settings-card-description">Add logo, title, and customize text appearance in the drawer header</p>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Show Logo Toggle -->
            <div>
                <x-toggle
                    wire-model="drawer_logo_enabled"
                    :live="true"
                    label="Show Logo in Drawer"
                />
                <small class="helper-text">Display a logo in the drawer header</small>
            </div>

            @if($drawer_logo_enabled)
                <!-- Drawer Logo (Light Mode) -->
                <div wire:key="drawer-logo-light-{{ $drawer_logo_light ?? 'none' }}">
                    <x-file-upload
                        wire-model="drawer_logo_light_file"
                        accept="image/png"
                        label="Drawer Logo (Light Mode)"
                        help-text="PNG with transparency required (MAX. 2MB) - Shown when app is in light theme"
                        :current-file="$drawer_logo_light"
                        :max-size="2"
                        preview-type="image"
                    />
                    @if($drawer_logo_light)
                        <button
                            type="button"
                            wire:click="$dispatch('delete-drawer-logo-light')"
                            class="mt-2 rounded-lg bg-red-700 px-3 py-2 text-xs font-medium text-white hover:bg-red-800 focus:outline-hidden focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800"
                        >
                            Delete Light Logo
                        </button>
                    @endif
                </div>

                <!-- Drawer Logo (Dark Mode) -->
                <div wire:key="drawer-logo-dark-{{ $drawer_logo_dark ?? 'none' }}">
                    <x-file-upload
                        wire-model="drawer_logo_dark_file"
                        accept="image/png"
                        label="Drawer Logo (Dark Mode)"
                        help-text="PNG with transparency required (MAX. 2MB) - Shown when app is in dark theme"
                        :current-file="$drawer_logo_dark"
                        :max-size="2"
                        preview-type="image"
                    />
                    @if($drawer_logo_dark)
                        <button
                            type="button"
                            wire:click="$dispatch('delete-drawer-logo-dark')"
                            class="mt-2 rounded-lg bg-red-700 px-3 py-2 text-xs font-medium text-white hover:bg-red-800 focus:outline-hidden focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800"
                        >
                            Delete Dark Logo
                        </button>
                    @endif
                </div>

                <!-- Logo Size Slider -->
                <div>
                    <label for="drawer_logo_size" class="block mb-2.5 text-sm font-medium text-gray-900 dark:text-white">
                        Logo Size: <span class="text-blue-600 dark:text-blue-400 font-semibold">{{ $drawer_logo_size }}dp</span>
                    </label>
                    <div class="relative mb-6">
                        <input
                            wire:model.live="drawer_logo_size"
                            type="range"
                            id="drawer_logo_size"
                            min="40"
                            max="200"
                            value="{{ $drawer_logo_size }}"
                            class="w-full h-2 bg-gray-200 rounded-full appearance-none cursor-pointer dark:bg-gray-700 [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:bg-blue-600 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:cursor-pointer [&::-moz-range-thumb]:w-4 [&::-moz-range-thumb]:h-4 [&::-moz-range-thumb]:bg-blue-600 [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:border-0 [&::-moz-range-thumb]:cursor-pointer dark:[&::-webkit-slider-thumb]:bg-blue-500 dark:[&::-moz-range-thumb]:bg-blue-500"
                        >
                        <span class="text-sm text-gray-500 dark:text-gray-400 absolute start-0 -bottom-6">Small (40dp)</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 absolute start-1/2 -translate-x-1/2 rtl:translate-x-1/2 -bottom-6">Medium (120dp)</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 absolute end-0 -bottom-6">Large (200dp)</span>
                    </div>
                    @error('drawer_logo_size')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @else
                        <small class="helper-text">Adjust logo size for optimal appearance</small>
                    @enderror
                </div>
            @endif

            <!-- Drawer Title -->
            <div>
                <label for="drawer_title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Drawer Title
                </label>
                <input type="text" id="drawer_title" wire:model.live="drawer_title"
                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white @error('drawer_title') border-red-500 @enderror"
                       placeholder="My App">
                @error('drawer_title')
                    <small class="helper-text text-red-600 dark:text-red-400">{{ $message }}</small>
                @else
                    <small class="helper-text">Title displayed in drawer header</small>
                @enderror
            </div>

            <!-- Drawer Subtitle -->
            <div>
                <label for="drawer_subtitle" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Drawer Subtitle
                </label>
                <input type="text" id="drawer_subtitle" wire:model.live="drawer_subtitle"
                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white @error('drawer_subtitle') border-red-500 @enderror"
                       placeholder="Welcome to my app">
                @error('drawer_subtitle')
                    <small class="helper-text text-red-600 dark:text-red-400">{{ $message }}</small>
                @else
                    <small class="helper-text">Subtitle or tagline shown below the title</small>
                @enderror
            </div>

            <!-- Text Theme -->
            <div>
                <label for="drawer_text_theme" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Text Theme
                </label>
                <select wire:model.live="drawer_text_theme" id="drawer_text_theme"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="light">Light (White text)</option>
                    <option value="dark">Dark (Black text)</option>
                </select>
                @error('drawer_text_theme')
                    <small class="helper-text text-red-600 dark:text-red-400">{{ $message }}</small>
                @else
                    <small class="helper-text">Text color for drawer header and menu items</small>
                @enderror
            </div>
        </div>
    </div>

    <!-- Drawer Item Colors Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Drawer Item Colors</h3>
                <p class="settings-card-description">Customize icon and text colors for drawer menu items</p>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Drawer Icon Colors -->
            <div>
                <label class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">
                    Drawer Item Icon Colors
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-xs font-medium text-gray-700 dark:text-gray-300">Light Mode</label>
                        <div x-data="{ color: @entangle('drawer_icon_color_light').live }" class="flex items-center gap-3">
                            <input type="color" x-model="color"
                                   class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600 cursor-pointer">
                            <input type="text" x-model="color"
                                   placeholder="#6B7280"
                                   class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-medium text-gray-700 dark:text-gray-300">Dark Mode</label>
                        <div x-data="{ color: @entangle('drawer_icon_color_dark').live }" class="flex items-center gap-3">
                            <input type="color" x-model="color"
                                   class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600 cursor-pointer">
                            <input type="text" x-model="color"
                                   placeholder="#9CA3AF"
                                   class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        </div>
                    </div>
                </div>
                <small class="helper-text">Color of inactive drawer item icons and text in light and dark mode</small>
            </div>

            <!-- Active Drawer Item Colors -->
            <div>
                <label class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">
                    Active Item Colors
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-xs font-medium text-gray-700 dark:text-gray-300">Light Mode</label>
                        <div x-data="{ color: @entangle('drawer_active_color_light').live }" class="flex items-center gap-3">
                            <input type="color" x-model="color"
                                   class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600 cursor-pointer">
                            <input type="text" x-model="color"
                                   placeholder="#3B82F6"
                                   class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-xs font-medium text-gray-700 dark:text-gray-300">Dark Mode</label>
                        <div x-data="{ color: @entangle('drawer_active_color_dark').live }" class="flex items-center gap-3">
                            <input type="color" x-model="color"
                                   class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600 cursor-pointer">
                            <input type="text" x-model="color"
                                   placeholder="#3B82F6"
                                   class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                        </div>
                    </div>
                </div>
                <small class="helper-text">Color of the selected/active drawer item in light and dark mode</small>
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
