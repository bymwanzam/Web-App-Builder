<form wire:submit="save">

    <!-- Splashscreen Appearance Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Splashscreen Appearance</h3>
                <p class="settings-card-description">Customize background, display duration, and animation effects</p>
            </div>
        </div>

        <div class="space-y-6">

            <!-- Background Type -->
            <div>
                <label for="splashscreen_background_type" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    Background Type
                </label>
                <select
                    wire:model.live="splashscreen_background_type"
                    id="splashscreen_background_type"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-blue-500 dark:focus:ring-blue-500"
                >
                    <option value="color">Solid Color</option>
                    <option value="image">Background Image</option>
                </select>
                @error('splashscreen_background_type')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Background Color (shown when type is 'color') -->
            @if($splashscreen_background_type === 'color')
                <div>
                    <label for="splashscreen_background" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Background Color
                    </label>
                    <input
                        wire:model="splashscreen_background"
                        type="color"
                        id="splashscreen_background"
                        class="h-10 w-full rounded-lg border border-gray-300 bg-gray-50 p-1 dark:border-gray-600 dark:bg-gray-700"
                    >
                    @error('splashscreen_background')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <!-- Background Image (shown when type is 'image') -->
            @if($splashscreen_background_type === 'image')
                <div>
                    <x-file-upload
                        wire-model="splashscreen_image_file"
                        accept="image/png"
                        label="Background Image"
                        help-text="PNG only (MAX. 2MB)"
                        :current-file="$splashscreen_image"
                        :max-size="2"
                        preview-type="image"
                    />
                    @if($splashscreen_image)
                        <button
                            type="button"
                            wire:click="$dispatch('delete-splashscreen-image')"
                            class="mt-2 rounded-lg bg-red-700 px-3 py-2 text-xs font-medium text-white hover:bg-red-800 focus:outline-hidden focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800"
                        >
                            Delete Background Image
                        </button>
                    @endif
                </div>
            @endif

            <!-- Duration -->
            <div>
                <label for="splashscreen_duration" class="block mb-2.5 text-sm font-medium text-gray-900 dark:text-white">
                    Display Duration: <span class="text-blue-600 dark:text-blue-400 font-semibold">{{ $splashscreen_duration }} seconds</span>
                </label>
                <div class="relative mb-6">
                    <input
                        wire:model.live="splashscreen_duration"
                        type="range"
                        id="splashscreen_duration"
                        min="1"
                        max="10"
                        value="{{ $splashscreen_duration }}"
                        class="w-full h-2 bg-gray-200 rounded-full appearance-none cursor-pointer dark:bg-gray-700 [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:bg-blue-600 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:cursor-pointer [&::-moz-range-thumb]:w-4 [&::-moz-range-thumb]:h-4 [&::-moz-range-thumb]:bg-blue-600 [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:border-0 [&::-moz-range-thumb]:cursor-pointer dark:[&::-webkit-slider-thumb]:bg-blue-500 dark:[&::-moz-range-thumb]:bg-blue-500"
                    >
                    <span class="text-sm text-gray-500 dark:text-gray-400 absolute start-0 -bottom-6">1s</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400 absolute start-1/2 -translate-x-1/2 rtl:translate-x-1/2 -bottom-6">5s</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400 absolute end-0 -bottom-6">10s</span>
                </div>
                @error('splashscreen_duration')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Animation -->
            <div>
                <label for="splashscreen_animation" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    Animation Effect
                </label>
                <select
                    wire:model="splashscreen_animation"
                    id="splashscreen_animation"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-blue-500 dark:focus:ring-blue-500"
                >
                    <option value="fade">Fade</option>
                    <option value="slide">Slide</option>
                    <option value="zoom">Zoom</option>
                    <option value="none">None</option>
                </select>
                @error('splashscreen_animation')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Splashscreen Branding Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Splashscreen Branding</h3>
                <p class="settings-card-description">Add logo, title, subtitle, and text styling to your splashscreen</p>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Logo Mode -->
            <div>
                <div class="mb-4">
                    <x-toggle
                        wire-model="splashscreen_logo_enabled"
                        :live="true"
                        id="splashscreen_logo_enabled"
                        label="Show Logo on Splashscreen"
                    />
                </div>

                @if($splashscreen_logo_enabled)
                    <div>
                        <x-file-upload
                            wire-model="splashscreen_logo_file"
                            accept="image/png"
                            label="Logo Image"
                            help-text="PNG with transparency recommended (MAX. 2MB)"
                            :current-file="$splashscreen_logo"
                            :max-size="2"
                            preview-type="image"
                        />
                        @if($splashscreen_logo)
                            <button
                                type="button"
                                wire:click="$dispatch('delete-splashscreen-logo')"
                                class="mt-2 rounded-lg bg-red-700 px-3 py-2 text-xs font-medium text-white hover:bg-red-800 focus:outline-hidden focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800"
                            >
                                Delete Logo
                            </button>
                        @endif
                    </div>

                    <!-- Logo Size Slider -->
                    <div class="mt-6">
                        <label for="splashscreen_logo_size" class="block mb-2.5 text-sm font-medium text-gray-900 dark:text-white">
                            Logo Size: <span class="text-blue-600 dark:text-blue-400 font-semibold">{{ $splashscreen_logo_size }}dp</span>
                        </label>
                        <div class="relative mb-6">
                            <input
                                wire:model.live="splashscreen_logo_size"
                                type="range"
                                id="splashscreen_logo_size"
                                min="60"
                                max="240"
                                value="{{ $splashscreen_logo_size }}"
                                class="w-full h-2 bg-gray-200 rounded-full appearance-none cursor-pointer dark:bg-gray-700 [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-4 [&::-webkit-slider-thumb]:h-4 [&::-webkit-slider-thumb]:bg-blue-600 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:cursor-pointer [&::-moz-range-thumb]:w-4 [&::-moz-range-thumb]:h-4 [&::-moz-range-thumb]:bg-blue-600 [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:border-0 [&::-moz-range-thumb]:cursor-pointer dark:[&::-webkit-slider-thumb]:bg-blue-500 dark:[&::-moz-range-thumb]:bg-blue-500"
                            >
                            <span class="text-sm text-gray-500 dark:text-gray-400 absolute start-0 -bottom-6">Small (60dp)</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 absolute start-1/2 -translate-x-1/2 rtl:translate-x-1/2 -bottom-6">Medium (150dp)</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 absolute end-0 -bottom-6">Large (240dp)</span>
                        </div>
                        @error('splashscreen_logo_size')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @else
                            <small class="helper-text">Adjust logo size for optimal appearance</small>
                        @enderror
                    </div>
                @endif
            </div>

            <!-- Title and Subtitle -->
            <div>
                <label for="splashscreen_title" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    Title
                </label>
                <input
                    wire:model="splashscreen_title"
                    type="text"
                    id="splashscreen_title"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                    placeholder="My WordPress App"
                >
                @error('splashscreen_title')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="splashscreen_subtitle" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    Subtitle
                </label>
                <input
                    wire:model="splashscreen_subtitle"
                    type="text"
                    id="splashscreen_subtitle"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                    placeholder="Welcome to the best blog app ever"
                >
                @error('splashscreen_subtitle')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Text Theme -->
            <div>
                <label for="splashscreen_text_theme" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    Text Theme
                </label>
                <select
                    wire:model="splashscreen_text_theme"
                    id="splashscreen_text_theme"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-blue-500 dark:focus:ring-blue-500"
                >
                    <option value="light">Light (White text)</option>
                    <option value="dark">Dark (Black text)</option>
                </select>
                @error('splashscreen_text_theme')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                @enderror
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
