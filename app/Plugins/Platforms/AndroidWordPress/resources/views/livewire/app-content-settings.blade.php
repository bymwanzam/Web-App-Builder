<form wire:submit.prevent="save">

    <!-- Post Display Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"/>
                    <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Post Display</h3>
                <p class="settings-card-description">Configure how posts are displayed in your app</p>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Posts Per Page -->
            <div>
                <label for="posts_per_page" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Posts Per Page
                </label>
                <input type="number" id="posts_per_page" wire:model="posts_per_page"
                       min="1" max="50"
                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white @error('posts_per_page') border-red-500 @enderror">
                @error('posts_per_page')
                    <small class="helper-text text-red-600 dark:text-red-400">{{ $message }}</small>
                @else
                    <small class="helper-text">Number of posts to load at a time (1-50)</small>
                @enderror
            </div>

            <!-- Excerpt Length -->
            <div>
                <label for="excerpt_length" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Excerpt Length (characters)
                </label>
                <input type="number" id="excerpt_length" wire:model="excerpt_length"
                       min="50" max="500"
                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white @error('excerpt_length') border-red-500 @enderror">
                @error('excerpt_length')
                    <small class="helper-text text-red-600 dark:text-red-400">{{ $message }}</small>
                @else
                    <small class="helper-text">Maximum characters shown in post excerpts (50-500)</small>
                @enderror
            </div>
        </div>
    </div>

    <!-- Post Metadata Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Post Metadata</h3>
                <p class="settings-card-description">Choose what information to display with each post</p>
            </div>
        </div>

        <div class="space-y-4">
            <!-- Show Featured Image -->
            <div>
                <x-toggle
                    wire-model="show_featured_image"
                    :live="true"
                    label="Show featured image"
                />
                <small class="helper-text">Display the post's featured/thumbnail image</small>
            </div>

            <!-- Show Excerpt -->
            <div>
                <x-toggle
                    wire-model="show_excerpt"
                    :live="true"
                    label="Show post excerpt"
                />
                <small class="helper-text">Display a preview of the post content</small>
            </div>

            <!-- Show Author -->
            <div>
                <x-toggle
                    wire-model="show_author"
                    :live="true"
                    label="Show author name"
                />
                <small class="helper-text">Display the author who wrote the post</small>
            </div>

            <!-- Show Date -->
            <div>
                <x-toggle
                    wire-model="show_date"
                    :live="true"
                    label="Show publish date"
                />
                <small class="helper-text">Display when the post was published</small>
            </div>

            <!-- Show Categories -->
            <div>
                <x-toggle
                    wire-model="show_categories"
                    :live="true"
                    label="Show categories"
                />
                <small class="helper-text">Display the categories assigned to the post</small>
            </div>

            <!-- Show Reading Time -->
            <div>
                <x-toggle
                    wire-model="show_reading_time"
                    :live="true"
                    label="Show estimated reading time"
                />
                <small class="helper-text">Display how long it takes to read the post</small>
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
