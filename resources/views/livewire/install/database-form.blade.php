<form wire:submit.prevent="save" class="space-y-4">
    <!-- Database Type Selection -->
    <div>
        <label for="dbType" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Choose Database Type
        </label>
        <div class="grid gap-4 md:grid-cols-2">
            <!-- SQLite Option -->
            <label for="sqlite" class="relative flex flex-col p-4 border rounded-lg cursor-pointer transition-all"
                   :class="$wire.dbType === 'sqlite' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-500' : 'border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500'">
                <div class="flex items-start">
                    <input type="radio" wire:model.live="dbType" value="sqlite" id="sqlite" class="mt-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <div class="ml-3 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">SQLite</span>
                            <span class="px-2 py-0.5 text-xs font-medium text-blue-800 bg-blue-100 rounded dark:bg-blue-900 dark:text-blue-300">Personal</span>
                        </div>
                        <ul class="mt-2 space-y-1 text-xs text-gray-600 dark:text-gray-400">
                            <li class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                No setup required
                            </li>
                            <li class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Perfect for small sites
                            </li>
                            <li class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Single file database
                            </li>
                        </ul>
                    </div>
                </div>
            </label>

            <!-- MySQL Option -->
            <label for="mysql" class="relative flex flex-col p-4 border rounded-lg cursor-pointer transition-all"
                   :class="$wire.dbType === 'mysql' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-500' : 'border-gray-300 bg-white dark:bg-gray-700 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500'">
                <div class="flex items-start">
                    <input type="radio" wire:model.live="dbType" value="mysql" id="mysql" class="mt-1 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                    <div class="ml-3 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">MySQL</span>
                            <span class="px-2 py-0.5 text-xs font-medium text-purple-800 bg-purple-100 rounded dark:bg-purple-900 dark:text-purple-300">Business</span>
                        </div>
                        <ul class="mt-2 space-y-1 text-xs text-gray-600 dark:text-gray-400">
                            <li class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                High performance
                            </li>
                            <li class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Handles high traffic
                            </li>
                            <li class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Industry standard
                            </li>
                        </ul>
                    </div>
                </div>
            </label>
        </div>
        @error('dbType')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- MySQL Configuration (Conditional) -->
    <div x-show="$wire.dbType === 'mysql'" x-cloak x-transition class="space-y-4">
        <div class="grid gap-4 md:grid-cols-2">
        <!-- Host -->
        <div>
            <label for="host" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                Database Host
            </label>
            <input type="text" wire:model.live="host" id="host" :required="$wire.dbType === 'mysql'"
                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="localhost">
            @error('host')
                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Port -->
        <div>
            <label for="port" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                Database Port
            </label>
            <input type="number" wire:model.live="port" id="port" min="1" max="65535" :required="$wire.dbType === 'mysql'"
                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="3306">
            @error('port')
                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Database Name -->
    <div>
        <label for="database" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Database Name
        </label>
        <input type="text" wire:model.live="database" id="database" :required="$wire.dbType === 'mysql'"
            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="appy">
        @error('database')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Username -->
    <div>
        <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Database Username
        </label>
        <input type="text" wire:model.live="username" id="username" :required="$wire.dbType === 'mysql'"
            class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
            placeholder="root">
        @error('username')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Password -->
    <div>
        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            Database Password
        </label>
        <div x-data="{ show: false }" class="relative">
            <input type="password" x-bind:type="show ? 'text' : 'password'" wire:model="password" id="password"
                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                placeholder="••••••••">
            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 dark:text-gray-400">
                <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
            </button>
        </div>
        @error('password')
            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
        @enderror
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Leave blank if your database has no password</p>
    </div>

        <!-- MySQL Info Box -->
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg dark:bg-blue-900/20 dark:border-blue-800">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        Make sure the database exists and the user has full privileges. The installer will create all required tables automatically.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- SQLite Info Box (Conditional) -->
    <div x-show="$wire.dbType === 'sqlite'" x-cloak x-transition class="p-4 bg-green-50 border border-green-200 rounded-lg dark:bg-green-900/20 dark:border-green-800">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-green-600 dark:text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h4 class="text-sm font-medium text-green-800 dark:text-green-300">SQLite is ready to use!</h4>
                <p class="mt-1 text-sm text-green-700 dark:text-green-400">
                    No additional configuration needed. A database file will be created automatically at <code class="px-1 py-0.5 bg-green-100 dark:bg-green-900/50 rounded text-xs">database/database.sqlite</code>
                </p>
                <p class="mt-2 text-xs text-green-600 dark:text-green-500">
                    <strong>Note:</strong> SQLite is perfect for small to medium sites (up to ~1000 users). For larger deployments, consider using MySQL.
                </p>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="flex items-center justify-between pt-4">
        <a href="{{ route('install.permissions') }}" class="px-5 py-2.5 text-sm font-medium text-gray-900 focus:outline-hidden bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
            <svg class="w-5 h-5 mr-2 -ml-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>

        <button type="submit" wire:loading.attr="disabled" wire:target="save" class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-hidden focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 disabled:opacity-50 disabled:cursor-not-allowed">
            <span wire:loading.remove wire:target="save">
                Continue
                <svg class="w-5 h-5 ml-2 -mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </span>
            <span wire:loading wire:target="save">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Please wait...
            </span>
        </button>
    </div>
</form>
