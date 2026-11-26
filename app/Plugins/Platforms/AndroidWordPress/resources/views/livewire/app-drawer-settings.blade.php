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
                <p class="settings-card-description">Customize the navigation drawer background</p>
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
                <small class="helper-text">Choose how the drawer background should appear</small>
            </div>

            @if($drawer_mode === 'color')
                <div x-data="{ color: @entangle('drawer_background_color').live }">
                    <label class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">
                        Background Color
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="color" x-model="color"
                               class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600">
                        <input type="text" x-model="color" placeholder="#21759B"
                               class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>
            @endif

            @if($drawer_mode === 'image')
                <div>
                    <x-file-upload
                        wire-model="drawer_background_image_file"
                        accept="image/png"
                        label="Background Image"
                        help-text="PNG only (MAX. 2MB)"
                        :current-file="$drawer_background_image"
                        :max-size="2"
                        preview-type="image"
                    />
                    @if($drawer_background_image)
                        <button type="button" wire:click="$dispatch('delete-drawer-background')"
                                class="mt-2 rounded-lg bg-red-700 px-3 py-2 text-xs font-medium text-white hover:bg-red-800">
                            Delete Background
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
                <p class="settings-card-description">Logo, title, and text styling</p>
            </div>
        </div>

        <div class="space-y-6">
            <div>
                <x-toggle wire-model="drawer_logo_enabled" :live="true" label="Show Logo in Drawer" />
            </div>

            @if($drawer_logo_enabled)
                <div>
                    <x-file-upload
                        wire-model="drawer_logo_light_file"
                        accept="image/png"
                        label="Logo (Light Mode)"
                        help-text="PNG with transparency (MAX. 2MB)"
                        :current-file="$drawer_logo_light"
                        :max-size="2"
                        preview-type="image"
                    />
                    @if($drawer_logo_light)
                        <button type="button" wire:click="$dispatch('delete-drawer-logo-light')"
                                class="mt-2 rounded-lg bg-red-700 px-3 py-2 text-xs font-medium text-white hover:bg-red-800">
                            Delete Light Logo
                        </button>
                    @endif
                </div>

                <div>
                    <x-file-upload
                        wire-model="drawer_logo_dark_file"
                        accept="image/png"
                        label="Logo (Dark Mode)"
                        help-text="PNG with transparency (MAX. 2MB)"
                        :current-file="$drawer_logo_dark"
                        :max-size="2"
                        preview-type="image"
                    />
                    @if($drawer_logo_dark)
                        <button type="button" wire:click="$dispatch('delete-drawer-logo-dark')"
                                class="mt-2 rounded-lg bg-red-700 px-3 py-2 text-xs font-medium text-white hover:bg-red-800">
                            Delete Dark Logo
                        </button>
                    @endif
                </div>

                <div>
                    <label class="block mb-2.5 text-sm font-medium text-gray-900 dark:text-white">
                        Logo Size: <span class="text-blue-600 dark:text-blue-400 font-semibold">{{ $drawer_logo_size }}dp</span>
                    </label>
                    <input type="range" wire:model.live="drawer_logo_size" min="40" max="200"
                           class="w-full h-2 bg-gray-200 rounded-full appearance-none cursor-pointer dark:bg-gray-700">
                </div>
            @endif

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Drawer Title</label>
                <input type="text" wire:model="drawer_title" placeholder="My WordPress App"
                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Drawer Subtitle</label>
                <input type="text" wire:model="drawer_subtitle" placeholder="Your tagline here"
                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Text Theme</label>
                <select wire:model="drawer_text_theme"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="light">Light (White text)</option>
                    <option value="dark">Dark (Black text)</option>
                </select>
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
                <p class="settings-card-description">Icon and text colors for menu items</p>
            </div>
        </div>

        <div class="space-y-6">
            <div>
                <label class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">Inactive Item Colors</label>
                <div class="grid grid-cols-2 gap-4">
                    <div x-data="{ color: @entangle('drawer_icon_color_light').live }">
                        <label class="block mb-2 text-xs font-medium text-gray-700 dark:text-gray-300">Light Mode</label>
                        <div class="flex items-center gap-2">
                            <input type="color" x-model="color" class="h-10 w-16 rounded border border-gray-300">
                            <input type="text" x-model="color" placeholder="#6B7280"
                                   class="flex-1 bg-gray-50 border border-gray-300 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                    <div x-data="{ color: @entangle('drawer_icon_color_dark').live }">
                        <label class="block mb-2 text-xs font-medium text-gray-700 dark:text-gray-300">Dark Mode</label>
                        <div class="flex items-center gap-2">
                            <input type="color" x-model="color" class="h-10 w-16 rounded border border-gray-300">
                            <input type="text" x-model="color" placeholder="#9CA3AF"
                                   class="flex-1 bg-gray-50 border border-gray-300 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">Active Item Colors</label>
                <div class="grid grid-cols-2 gap-4">
                    <div x-data="{ color: @entangle('drawer_active_color_light').live }">
                        <label class="block mb-2 text-xs font-medium text-gray-700 dark:text-gray-300">Light Mode</label>
                        <div class="flex items-center gap-2">
                            <input type="color" x-model="color" class="h-10 w-16 rounded border border-gray-300">
                            <input type="text" x-model="color" placeholder="#21759B"
                                   class="flex-1 bg-gray-50 border border-gray-300 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                    <div x-data="{ color: @entangle('drawer_active_color_dark').live }">
                        <label class="block mb-2 text-xs font-medium text-gray-700 dark:text-gray-300">Dark Mode</label>
                        <div class="flex items-center gap-2">
                            <input type="color" x-model="color" class="h-10 w-16 rounded border border-gray-300">
                            <input type="text" x-model="color" placeholder="#21759B"
                                   class="flex-1 bg-gray-50 border border-gray-300 text-sm rounded-lg p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                </div>
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
