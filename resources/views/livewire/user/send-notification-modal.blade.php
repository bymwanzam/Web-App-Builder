<div>
    <!-- Modal backdrop -->
    <div x-data="{ show: @entangle('showModal') }"
         x-show="show"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true">

        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-gray-500/75 dark:bg-gray-900/75"
                 @click="show = false"
                 aria-hidden="true"></div>

            <!-- Center modal -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative z-10 inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">

                <!-- Modal header -->
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Send Push Notification
                    </h3>
                    <button type="button"
                            @click="show = false"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal body -->
                <form wire:submit="send">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- App Selection -->
                        <div class="col-span-2">
                            <label for="app_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Select App <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="app_id"
                                    id="app_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="">Choose an app</option>
                                @foreach($userApps as $app)
                                    <option value="{{ $app->id }}">{{ $app->name }}</option>
                                @endforeach
                            </select>
                            @error('app_id')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Title -->
                        <div class="col-span-2">
                            <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="title"
                                   type="text"
                                   id="title"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                   placeholder="Notification title">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Body -->
                        <div class="col-span-2">
                            <label for="body" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Message <span class="text-red-500">*</span>
                            </label>
                            <textarea wire:model="body"
                                      id="body"
                                      rows="3"
                                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                      placeholder="Notification message"></textarea>
                            @error('body')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notification Style -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Notification Style</label>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center">
                                    <input wire:model.live="notification_style"
                                           id="style-text"
                                           type="radio"
                                           value="text"
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="style-text" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Text Only</label>
                                </div>
                                <div class="flex items-center">
                                    <input wire:model.live="notification_style"
                                           id="style-image"
                                           type="radio"
                                           value="image"
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="style-image" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">With Image</label>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule -->
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">When to Send</label>
                            <div class="flex items-center gap-4">
                                <div class="flex items-center">
                                    <input wire:model.live="schedule_type"
                                           id="send-now"
                                           type="radio"
                                           value="now"
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="send-now" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Now</label>
                                </div>
                                <div class="flex items-center">
                                    <input wire:model.live="schedule_type"
                                           id="send-later"
                                           type="radio"
                                           value="later"
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="send-later" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Schedule</label>
                                </div>
                            </div>
                        </div>

                        <!-- Scheduled Time (conditional) -->
                        @if($schedule_type === 'later')
                            <div class="col-span-2">
                                <label for="scheduled_at" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Scheduled Time <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="scheduled_at"
                                       type="datetime-local"
                                       id="scheduled_at"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                @error('scheduled_at')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <!-- Image Upload (conditional) -->
                        @if($notification_style === 'image')
                            <div class="col-span-2">
                                <x-file-upload
                                    wire-model="image_file"
                                    accept=".jpg,.jpeg,.png"
                                    label="Notification Image"
                                    help-text="JPG or PNG only (MAX. 1MB)"
                                    :max-size="1"
                                    preview-type="image"
                                />
                            </div>
                        @endif
                    </div>

                    <!-- Modal footer -->
                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button"
                                @click="show = false"
                                class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-hidden focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                            Cancel
                        </button>
                        <button type="submit"
                                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-hidden focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            {{ $schedule_type === 'later' ? 'Schedule' : 'Send Now' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
