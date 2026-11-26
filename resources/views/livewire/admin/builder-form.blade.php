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
                 @click="$wire.closeModal()"
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
                        {{ $isEdit ? 'Edit' : 'Add' }} Builder
                    </h3>
                    <button type="button"
                            wire:click="closeModal"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal body -->
                <form wire:submit="save">
                    <!-- Section 1: Server Connection -->
                    <div class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Server Name -->
                            <div class="md:col-span-3">
                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Builder Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       wire:model="name"
                                       id="name"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                       placeholder="Production Builder">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Server URL -->
                            <div class="md:col-span-2">
                                <label for="url" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Server URL <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       wire:model="url"
                                       id="url"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                       placeholder="http://192.168.2.29">
                                @error('url')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Server Port -->
                            <div>
                                <label for="port" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Port <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       wire:model="port"
                                       id="port"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                       placeholder="8899">
                                @error('port')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Authentication -->
                    <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <label for="server_key" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Server Key <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2" x-data="{ copied: false }">
                                <input type="text"
                                       wire:model="server_key"
                                       id="server_key"
                                       {{ $isEdit ? 'readonly' : '' }}
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 {{ $isEdit ? 'cursor-default' : '' }}"
                                       placeholder="Generated server key">

                                <!-- Copy Button (only in edit mode) -->
                                @if($isEdit)
                                    <button type="button"
                                            @click="navigator.clipboard.writeText($wire.server_key); copied = true; setTimeout(() => copied = false, 2000)"
                                            class="px-4 py-2.5 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-hidden focus:ring-gray-200 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                                        <svg x-show="!copied" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        <svg x-show="copied" x-cloak class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                @else
                                    <!-- Generate Button (only in create mode) -->
                                    @if(config('app.demo'))
                                        <button type="button" disabled title="Disabled in demo mode"
                                                class="px-4 py-2.5 text-sm font-medium text-white bg-gray-700 rounded-lg dark:bg-gray-600 cursor-not-allowed opacity-50">
                                            Generate
                                        </button>
                                    @else
                                        <button type="button"
                                                wire:click="generateServerKey"
                                                class="px-4 py-2.5 text-sm font-medium text-white bg-gray-700 rounded-lg hover:bg-gray-800 focus:ring-4 focus:outline-hidden focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                                            Generate
                                        </button>
                                    @endif
                                @endif
                            </div>
                            @error('server_key')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @else
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Used to authenticate API requests to this builder</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Section 3: Build Configuration -->
                    <div class="mb-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Max Queue -->
                            <div>
                                <label for="max_queue" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Max Queue <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       wire:model="max_queue"
                                       id="max_queue"
                                       min="1"
                                       max="100"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                       placeholder="5">
                                @error('max_queue')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @else
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maximum concurrent builds</p>
                                @enderror
                            </div>

                            <!-- Build Credit Cost -->
                            <div>
                                <label for="credit_cost" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Build Credit Cost <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                       wire:model="credit_cost"
                                       id="credit_cost"
                                       min="1"
                                       max="100"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                       placeholder="1">
                                @error('credit_cost')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @else
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Credits deducted per build</p>
                                @enderror
                            </div>

                            <!-- Platforms -->
                            <div x-data="{
                                    open: false,
                                    get selectedCount() {
                                        return @this.platforms.length;
                                    },
                                    get buttonText() {
                                        const count = @this.platforms.length;
                                        if (count === 0) return 'Select platforms';
                                        return `${count} platform${count > 1 ? 's' : ''} selected`;
                                    },
                                    togglePlatform(platformId) {
                                        const current = @this.platforms;
                                        const index = current.indexOf(platformId);

                                        if (index > -1) {
                                            current.splice(index, 1);
                                        } else {
                                            current.push(platformId);
                                        }

                                        @this.platforms = current;
                                    },
                                    isSelected(platformId) {
                                        return @this.platforms.includes(platformId);
                                    }
                                 }"
                                 @click.away="open = false">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Supported Platforms <span class="text-red-500">*</span>
                                </label>

                                @if(count($this->availablePlatforms) === 0)
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No platforms available</p>
                                @else
                                    <div class="relative">
                                        <button type="button"
                                                @click="open = !open"
                                                class="w-full flex items-center justify-between px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-hidden focus:ring-blue-300 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 dark:focus:ring-blue-800">
                                            <span x-text="buttonText"></span>
                                            <svg class="w-4 h-4 ml-2 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>

                                        <div x-show="open"
                                             x-cloak
                                             x-transition
                                             class="absolute z-10 w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-lg dark:bg-gray-700 dark:border-gray-600 max-h-64 overflow-y-auto">

                                            @foreach($this->availablePlatforms as $platformOption)
                                                <label class="flex items-center px-3 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                                                       :class="{ 'bg-blue-50 dark:bg-blue-900/10': isSelected('{{ $platformOption['id'] }}') }">
                                                    <input type="checkbox"
                                                           :checked="isSelected('{{ $platformOption['id'] }}')"
                                                           @change="togglePlatform('{{ $platformOption['id'] }}')"
                                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-600 dark:border-gray-500">
                                                    <span class="ml-3 text-sm text-gray-900 dark:text-white">{{ $platformOption['name'] }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @error('platforms')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @else
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Select one or more platforms this builder supports</p>
                                @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="status"
                                        id="status"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button"
                                wire:click="closeModal"
                                class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:outline-hidden focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                            Cancel
                        </button>
                        @if(config('app.demo'))
                            <button type="button" disabled title="Disabled in demo mode"
                                    class="text-white bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 cursor-not-allowed opacity-50">
                                Submit
                            </button>
                        @else
                            <button type="submit"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-hidden focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                Submit
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
