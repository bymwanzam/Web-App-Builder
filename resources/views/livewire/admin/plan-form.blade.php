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

            <!-- Modal panel - wider -->
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative z-10 inline-block w-full max-w-5xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg">

                <!-- Modal header -->
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        {{ $planId ? 'Edit Plan' : 'Create New Plan' }}
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
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Left Column -->
                        <div class="space-y-5">
                            <!-- Basic Info Section -->
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Basic Information
                                </h4>

                                <!-- Name -->
                                <div class="mb-4">
                                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Plan Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           wire:model="name"
                                           id="name"
                                           class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                           placeholder="Pro">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Price -->
                                    <div>
                                        <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                            Price (USD/mo) <span class="text-red-500">*</span>
                                        </label>
                                        <input type="number"
                                               wire:model="price"
                                               id="price"
                                               step="0.01"
                                               min="0"
                                               class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                               placeholder="29.00">
                                        @error('price')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Status -->
                                    <div>
                                        <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                            Status <span class="text-red-500">*</span>
                                        </label>
                                        <select wire:model="status"
                                                id="status"
                                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                        @error('status')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Limits Section -->
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    Usage Limits
                                </h4>

                                <!-- Build Credits -->
                                <div class="mb-4">
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Monthly Build Credits <span class="text-red-500">*</span>
                                    </label>
                                    <div class="flex items-center gap-3">
                                        <input type="number"
                                               wire:model="monthly_build_credits"
                                               :disabled="$wire.unlimited_builds"
                                               :min="$wire.unlimited_builds ? null : 1"
                                               class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed"
                                               placeholder="10">
                                        <label class="inline-flex items-center cursor-pointer whitespace-nowrap">
                                            <input type="checkbox" wire:model.live="unlimited_builds" class="sr-only peer">
                                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-hidden peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-blue-600"></div>
                                            <span class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Unlimited</span>
                                        </label>
                                    </div>
                                    @error('monthly_build_credits')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Appetize Minutes -->
                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="text-sm font-medium text-gray-900 dark:text-white">Appetize Preview</label>
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" wire:model.live="can_use_appetize" class="sr-only peer">
                                            <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-hidden peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-500 peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                    <div x-data x-show="$wire.can_use_appetize" x-cloak class="flex items-center gap-3">
                                        <input type="number"
                                               wire:model="monthly_appetize_minutes"
                                               :disabled="$wire.unlimited_appetize"
                                               min="1"
                                               class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed"
                                               placeholder="300">
                                        <label class="inline-flex items-center cursor-pointer whitespace-nowrap">
                                            <input type="checkbox" wire:model.live="unlimited_appetize" class="sr-only peer">
                                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-hidden peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-blue-600"></div>
                                            <span class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Unlimited</span>
                                        </label>
                                    </div>
                                    @error('monthly_appetize_minutes')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Hosted Pages -->
                                <div class="mb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="text-sm font-medium text-gray-900 dark:text-white">Hosted Pages</label>
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" wire:model.live="can_use_hosted_pages" class="sr-only peer">
                                            <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-hidden peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-500 peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                    <div x-data x-show="$wire.can_use_hosted_pages" x-cloak class="flex items-center gap-3">
                                        <input type="number"
                                               wire:model="max_hosted_pages_per_app"
                                               :disabled="$wire.unlimited_hosted_pages"
                                               min="1"
                                               class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed"
                                               placeholder="5">
                                        <label class="inline-flex items-center cursor-pointer whitespace-nowrap">
                                            <input type="checkbox" wire:model.live="unlimited_hosted_pages" class="sr-only peer">
                                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-hidden peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-blue-600"></div>
                                            <span class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Unlimited</span>
                                        </label>
                                    </div>
                                    @error('max_hosted_pages_per_app')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- AI Tokens (only show if AI is enabled globally) -->
                                @if($this->aiEnabled)
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="text-sm font-medium text-gray-900 dark:text-white">AI Assistant</label>
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" wire:model.live="can_use_ai_chat" class="sr-only peer">
                                            <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-hidden peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-500 peer-checked:bg-blue-600"></div>
                                        </label>
                                    </div>
                                    <div x-data x-show="$wire.can_use_ai_chat" x-cloak class="space-y-3">
                                        <div class="flex items-center gap-3">
                                            <input type="number"
                                                   wire:model="monthly_ai_tokens"
                                                   :disabled="$wire.unlimited_ai_tokens"
                                                   min="1000"
                                                   step="1000"
                                                   class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white disabled:opacity-50 disabled:cursor-not-allowed"
                                                   placeholder="500000">
                                            <label class="inline-flex items-center cursor-pointer whitespace-nowrap">
                                                <input type="checkbox" wire:model.live="unlimited_ai_tokens" class="sr-only peer">
                                                <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-hidden peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-blue-600"></div>
                                                <span class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Unlimited</span>
                                            </label>
                                        </div>
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" wire:model="allow_user_ai_api_key" class="sr-only peer">
                                            <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-hidden peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-500 peer-checked:bg-blue-600"></div>
                                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Allow own API keys</span>
                                        </label>
                                    </div>
                                    @error('monthly_ai_tokens')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                @endif
                            </div>

                            <!-- Permissions Section -->
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    Permissions
                                </h4>

                                <div class="space-y-3">
                                    <!-- Custom Code -->
                                    <label class="flex items-center justify-between cursor-pointer">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">Custom CSS/JS Injection</span>
                                        <input type="checkbox" wire:model="can_use_custom_code" class="sr-only peer">
                                        <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-hidden peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-500 peer-checked:bg-blue-600"></div>
                                    </label>

                                    <!-- API Access -->
                                    <label class="flex items-center justify-between cursor-pointer">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">API Access</span>
                                        <input type="checkbox" wire:model="can_use_api" class="sr-only peer">
                                        <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-hidden peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-500 peer-checked:bg-blue-600"></div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-5">
                            <!-- Builder Access Section -->
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg"
                                 x-data="{
                                    open: false,
                                    search: '',
                                    get filteredBuilders() {
                                        if (!this.search) return @js($this->availableBuilders->groupBy('platform'));

                                        const searchLower = this.search.toLowerCase();
                                        const filtered = @js($this->availableBuilders).filter(builder =>
                                            builder.name.toLowerCase().includes(searchLower)
                                        );

                                        return filtered.reduce((groups, builder) => {
                                            if (!groups[builder.platform]) groups[builder.platform] = [];
                                            groups[builder.platform].push(builder);
                                            return groups;
                                        }, {});
                                    },
                                    get selectedCount() {
                                        return @this.allow_all_builders ? 0 : @this.allowed_builders.length;
                                    },
                                    get buttonText() {
                                        if (@this.allow_all_builders) return 'All builders allowed';
                                        const count = @this.allowed_builders.length;
                                        if (count === 0) return 'Select builders';
                                        return `${count} builder${count > 1 ? 's' : ''} selected`;
                                    },
                                    toggleAllBuilders() {
                                        @this.allow_all_builders = true;
                                        @this.allowed_builders = [];
                                    },
                                    toggleBuilder(builderId) {
                                        if (@this.allow_all_builders) {
                                            @this.allow_all_builders = false;
                                        }

                                        const current = @this.allowed_builders;
                                        const index = current.indexOf(builderId);

                                        if (index > -1) {
                                            current.splice(index, 1);
                                        } else {
                                            current.push(builderId);
                                        }

                                        @this.allowed_builders = current;
                                    },
                                    isSelected(builderId) {
                                        return @this.allowed_builders.includes(builderId);
                                    },
                                    clearAll() {
                                        @this.allow_all_builders = false;
                                        @this.allowed_builders = [];
                                        this.search = '';
                                    }
                                }"
                                @click.away="open = false">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    Builder Access
                                </h4>

                                @if(count($this->availableBuilders) === 0)
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No builders configured yet.</p>
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
                                             class="absolute z-10 w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-lg dark:bg-gray-700 dark:border-gray-600 max-h-64 overflow-hidden flex flex-col">

                                            <div class="p-3 border-b border-gray-200 dark:border-gray-600">
                                                <input type="text"
                                                       x-model="search"
                                                       placeholder="Search builders..."
                                                       class="block w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white">
                                            </div>

                                            <div class="overflow-y-auto flex-1">
                                                <label class="flex items-center px-3 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer bg-blue-50 dark:bg-blue-900/20 border-b border-gray-200 dark:border-gray-600">
                                                    <input type="checkbox"
                                                           :checked="$wire.allow_all_builders"
                                                           @change="toggleAllBuilders()"
                                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-600 dark:border-gray-500">
                                                    <span class="ml-3 text-sm font-semibold text-blue-700 dark:text-blue-300">Allow All Builders</span>
                                                </label>

                                                <template x-for="(builders, platform) in filteredBuilders" :key="platform">
                                                    <div>
                                                        <div class="px-3 py-1.5 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400 bg-gray-100 dark:bg-gray-800" x-text="platform"></div>
                                                        <template x-for="builder in builders" :key="builder.id">
                                                            <label class="flex items-center px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer"
                                                                   :class="{ 'bg-blue-50 dark:bg-blue-900/10': isSelected(builder.id) }">
                                                                <input type="checkbox"
                                                                       :checked="isSelected(builder.id)"
                                                                       @change="toggleBuilder(builder.id)"
                                                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-600 dark:border-gray-500">
                                                                <span class="ml-3 text-sm text-gray-900 dark:text-white" x-text="builder.name"></span>
                                                            </label>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Features Section -->
                            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Features (Pricing Display) <span class="text-red-500">*</span>
                                </h4>

                                <!-- Add Feature Form -->
                                <div class="flex gap-2 mb-3">
                                    <input type="text"
                                           wire:model="featureName"
                                           placeholder="Feature name"
                                           class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                    <select wire:model="featureIncluded"
                                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white w-32">
                                        <option value="1">Included</option>
                                        <option value="0">Not Included</option>
                                    </select>
                                    <button type="button"
                                            wire:click="addFeature"
                                            class="px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 whitespace-nowrap">
                                        Add
                                    </button>
                                </div>
                                @error('featureName')
                                    <p class="mb-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror

                                <!-- Features List -->
                                <div class="max-h-48 overflow-y-auto">
                                    @if(count($features) > 0)
                                        <div class="space-y-2">
                                            @foreach($features as $index => $feature)
                                                <div class="flex items-center justify-between p-2 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600">
                                                    <div class="flex items-center gap-2">
                                                        @if($feature['included'])
                                                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                        @else
                                                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                            </svg>
                                                        @endif
                                                        <span class="text-sm text-gray-900 dark:text-white">{{ $feature['name'] }}</span>
                                                    </div>
                                                    <button type="button"
                                                            wire:click="removeFeature({{ $index }})"
                                                            class="text-red-600 hover:text-red-800 dark:text-red-400">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
                                            No features added yet. Add features to display on pricing page.
                                        </p>
                                    @endif
                                </div>
                                @error('features')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button"
                                wire:click="closeModal"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        @if(config('app.demo'))
                            <button type="button" disabled title="Disabled in demo mode"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg dark:bg-blue-600 cursor-not-allowed opacity-50">
                                {{ $planId ? 'Update Plan' : 'Create Plan' }}
                            </button>
                        @else
                            <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-hidden focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                {{ $planId ? 'Update Plan' : 'Create Plan' }}
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
