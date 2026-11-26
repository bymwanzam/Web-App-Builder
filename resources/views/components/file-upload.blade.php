@props([
    'wireModel' => '',
    'accept' => '*',
    'label' => 'Upload File',
    'helpText' => 'Select a file to upload',
    'currentFile' => null,
    'maxSize' => 2,
    'previewType' => 'image', // 'image' or 'file'
])

@php
$cacheBuster = $currentFile ? (@filemtime(storage_path('app/public/' . $currentFile)) ?: time()) : null;
@endphp

<div x-data="{
    fileName: '',
    fileSize: '',
    previewUrl: @js($currentFile ? \Storage::url($currentFile) . '?v=' . $cacheBuster : null),
    isDragging: false,
    isUploading: false,
    uploadProgress: 0,

    init() {
        // Watch for external resets of the wire:model
        this.$watch('$wire.{{ $wireModel }}', value => {
            if (value === null || value === undefined) {
                this.fileName = '';
                this.fileSize = '';
                this.previewUrl = @js($currentFile ? \Storage::url($currentFile) . '?v=' . $cacheBuster : null);
            }
        });
    },

    handleFiles(files) {
        if (files.length === 0) return;

        const file = files[0];
        this.fileName = file.name;
        this.fileSize = this.formatFileSize(file.size);

        // Show preview for images
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.previewUrl = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        // Upload to Livewire
        this.isUploading = true;
        this.uploadProgress = 0;

        this.$wire.upload('{{ $wireModel }}', file,
            (uploadedFilename) => {
                // Success callback
                this.isUploading = false;
                this.uploadProgress = 100;
            },
            (error) => {
                // Error callback
                this.isUploading = false;
                console.error('Upload error:', error);
            },
            (event) => {
                // Progress callback
                this.uploadProgress = event.detail.progress;
            }
        );
    },

    removeFile() {
        this.fileName = '';
        this.fileSize = '';
        this.previewUrl = null;
        this.$wire.set('{{ $wireModel }}', null);
        this.$refs.fileInput.value = '';
    },

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }
}" class="file-upload-container">

    <!-- Label -->
    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
        {{ $label }}
    </label>

    <!-- Current File Preview (if exists and is image) -->
    @if($currentFile && $previewType === 'image')
        <div class="mb-3" x-show="previewUrl">
            <div class="relative inline-block">
                <img x-bind:src="previewUrl" alt="Preview" class="h-32 rounded-lg border-2 border-gray-300 dark:border-gray-600 object-cover">
            </div>
        </div>
    @endif

    <!-- Hidden File Input -->
    <input
        type="file"
        x-ref="fileInput"
        accept="{{ $accept }}"
        @change="handleFiles($event.target.files)"
        class="hidden"
    >

    <!-- Drag & Drop Zone -->
    <div
        @click="$refs.fileInput.click()"
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="isDragging = false; handleFiles($event.dataTransfer.files)"
        x-bind:class="{ 'file-upload-zone-drag': isDragging }"
        class="file-upload-zone"
    >
        <!-- Upload Icon -->
        <svg class="w-10 h-10 mb-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
        </svg>

        <!-- Upload Text -->
        <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">
            <span class="font-semibold">Click to upload</span> or drag and drop
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $helpText }}</p>

        <!-- Upload Progress -->
        <div x-show="isUploading" class="mt-4 w-full">
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-medium text-blue-700 dark:text-blue-500">Uploading...</span>
                <span class="text-xs font-medium text-blue-700 dark:text-blue-500" x-text="uploadProgress + '%'"></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" x-bind:style="'width: ' + uploadProgress + '%'"></div>
            </div>
        </div>

        <!-- Selected File Info -->
        <div x-show="fileName && !isUploading" class="mt-4 flex items-center justify-between w-full px-4 py-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
            <div class="flex items-center gap-2 min-w-0 flex-1">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-blue-900 dark:text-blue-300 truncate" x-text="fileName"></p>
                    <p class="text-xs text-blue-700 dark:text-blue-400" x-text="fileSize"></p>
                </div>
            </div>
            <button
                type="button"
                @click.stop="removeFile()"
                class="ml-2 flex-shrink-0 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Error Messages (Livewire validation) -->
    @error($wireModel)
        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
</div>
