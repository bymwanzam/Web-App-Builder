<div class="bg-white border border-gray-200 rounded-lg shadow-xs dark:bg-gray-800 dark:border-gray-700">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">General Settings</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update your site information and branding</p>
    </div>
    <form wire:submit="save" class="p-6 space-y-4">
        <!-- Site Name -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Site Name</label>
            <input type="text" wire:model="site_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            @error('site_name') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
        </div>

        <!-- Site Description -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Site Description</label>
            <textarea wire:model="site_description" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Used for meta description and SEO"></textarea>
            @error('site_description') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
        </div>

        <!-- Site Tagline -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Site Tagline</label>
            <input type="text" wire:model="site_tagline" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Short tagline shown below logo">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Displayed below the logo in sidebar and login pages</p>
            @error('site_tagline') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
        </div>

        <!-- Purchase Code -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Purchase Code</label>
            <input type="text" wire:model="purchase_code" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="XXXX-XXXX-XXXX-XXXX">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Your license or purchase code for this installation (optional)</p>
            @error('purchase_code') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
        </div>

        <!-- Logo Upload (Light Mode) -->
        <div>
            <x-file-upload
                wire-model="logo"
                accept="image/*"
                label="Logo (Light Mode)"
                help-text="Recommended: 200x50px (horizontal) or 50x50px (square). PNG, JPG, SVG with transparent background. Max 2MB."
                :current-file="$current_logo"
                :max-size="2"
                preview-type="image"
            />

            @if($current_logo)
                @if(config('app.demo'))
                    <button type="button" disabled title="Disabled in demo mode" class="mt-2 rounded-lg bg-red-700 px-3 py-2 text-xs font-medium text-white dark:bg-red-600 cursor-not-allowed opacity-50">
                        Delete Logo
                    </button>
                @else
                    <button
                        type="button"
                        wire:click="removeLogo"
                        class="mt-2 rounded-lg bg-red-700 px-3 py-2 text-xs font-medium text-white hover:bg-red-800 focus:outline-hidden focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                        Delete Logo
                    </button>
                @endif
            @endif
        </div>

        <!-- Logo Upload (Dark Mode) -->
        <div>
            <x-file-upload
                wire-model="logo_dark"
                accept="image/*"
                label="Logo (Dark Mode)"
                help-text="Recommended: 200x50px (horizontal) or 50x50px (square). PNG, JPG, SVG with transparent background. Max 2MB."
                :current-file="$current_logo_dark"
                :max-size="2"
                preview-type="image"
            />

            @if($current_logo_dark)
                @if(config('app.demo'))
                    <button type="button" disabled title="Disabled in demo mode" class="mt-2 rounded-lg bg-red-700 px-3 py-2 text-xs font-medium text-white dark:bg-red-600 cursor-not-allowed opacity-50">
                        Delete Dark Logo
                    </button>
                @else
                    <button
                        type="button"
                        wire:click="removeDarkLogo"
                        class="mt-2 rounded-lg bg-red-700 px-3 py-2 text-xs font-medium text-white hover:bg-red-800 focus:outline-hidden focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                        Delete Dark Logo
                    </button>
                @endif
            @endif
        </div>

        <!-- Favicon Upload -->
        <div>
            <x-file-upload
                wire-model="favicon"
                accept="image/*,.ico"
                label="Favicon"
                help-text="Recommended: 32x32px or 16x16px. PNG, ICO, or SVG. Max 1MB."
                :current-file="$current_favicon"
                :max-size="1"
                preview-type="image"
            />

            @if($current_favicon)
                @if(config('app.demo'))
                    <button type="button" disabled title="Disabled in demo mode" class="mt-2 rounded-lg bg-red-700 px-3 py-2 text-xs font-medium text-white dark:bg-red-600 cursor-not-allowed opacity-50">
                        Delete Favicon
                    </button>
                @else
                    <button
                        type="button"
                        wire:click="removeFavicon"
                        class="mt-2 rounded-lg bg-red-700 px-3 py-2 text-xs font-medium text-white hover:bg-red-800 focus:outline-hidden focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                        Delete Favicon
                    </button>
                @endif
            @endif
        </div>

        <!-- Default Color Theme -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Default Color Theme</label>
            <select wire:model="default_theme" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="system">System (Follow user's device preference)</option>
                <option value="light">Light</option>
                <option value="dark">Dark</option>
            </select>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Default theme for new visitors. Users can still toggle theme manually.</p>
            @error('default_theme') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
        </div>

        <!-- Landing Page Toggle -->
        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">Enable Public Landing Page</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">When disabled, visitors accessing the root URL (/) will be redirected to the login page</p>
            </div>
            <x-toggle wire-model="landing_page_enabled" />
        </div>

        @if(config('app.demo'))
            <button type="button" disabled title="Disabled in demo mode" class="text-white bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 cursor-not-allowed opacity-50">
                {{ __('Save') }}
            </button>
        @else
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-hidden dark:focus:ring-blue-800">
                {{ __('Save') }}
            </button>
        @endif
    </form>
</div>
