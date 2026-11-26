{{-- Android WebView Platform-Specific Creation Form Fields --}}

<!-- APP ID (Package Name) -->
<div>
    <label for="app-id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
        APP ID
        <span class="text-red-500">*</span>
    </label>
    <input type="text"
           id="app-id"
           wire:model="app_id"
           placeholder="com.example.myapp"
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

<!-- WebView JavaScript Toggle -->
<div class="flex items-start">
    <div class="flex items-center h-5">
        <input id="enable-javascript"
               type="checkbox"
               wire:model="enable_javascript"
               checked
               class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-600 dark:border-gray-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800">
    </div>
    <label for="enable-javascript" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
        Enable JavaScript in WebView
    </label>
</div>
<small class="text-gray-500 dark:text-gray-400 text-xs -mt-2 block">
    Required for most modern websites (recommended)
</small>
