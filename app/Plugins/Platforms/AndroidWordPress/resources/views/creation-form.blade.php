{{-- Android WordPress Platform-Specific Creation Form Fields --}}

<!-- APP ID (Package Name) -->
<div>
    <label for="app-id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
        APP ID
        <span class="text-red-500">*</span>
    </label>
    <input type="text"
           id="app-id"
           wire:model="app_id"
           placeholder="com.example.wordpress"
           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white @error('app_id') border-red-500 @enderror">
    @error('app_id')
        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
    @else
        <small class="text-gray-500 dark:text-gray-400 text-xs mt-1 block">
            Unique identifier in reverse domain format (e.g., com.example.app). Cannot be changed later.
        </small>
    @enderror
</div>

<!-- Screen Orientation -->
<div>
    <label for="screen-orientation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
        Screen Orientation
    </label>
    <select id="screen-orientation"
            wire:model="screen_orientation"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
        <option value="system">System Default</option>
        <option value="portrait">Portrait</option>
        <option value="landscape">Landscape</option>
    </select>
    <small class="text-gray-500 dark:text-gray-400 text-xs mt-1 block">
        Default screen orientation for your app
    </small>
</div>

<!-- Layout Style -->
<div>
    <label for="layout-style" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
        Post List Style
    </label>
    <select id="layout-style"
            wire:model="layout_style"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
        <option value="list">List View</option>
        <option value="grid">Grid View</option>
        <option value="magazine">Magazine Style</option>
    </select>
    <small class="text-gray-500 dark:text-gray-400 text-xs mt-1 block">
        How posts are displayed on the home screen
    </small>
</div>
