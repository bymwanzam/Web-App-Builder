<div class="bg-white border border-gray-200 rounded-lg shadow-xs dark:bg-gray-800 dark:border-gray-700">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Authentication Settings</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure registration and social login options</p>
    </div>
    <form wire:submit="save" class="p-6 space-y-6">
        <!-- Enable Registration Toggle -->
        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">Enable Registration</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Allow new users to register accounts</p>
            </div>
            <x-toggle wire-model="enable_registration" />
        </div>

        <!-- Email Verification Toggle -->
        <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
            <div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">Require Email Verification</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">When disabled, new registrations are automatically verified</p>
            </div>
            <x-toggle wire-model="require_email_verification" />
        </div>

        <!-- reCAPTCHA v3 Settings -->
        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 flex items-center justify-center bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">reCAPTCHA v3</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Protect authentication forms from bots</p>
                </div>
                <x-toggle wire-model="recaptcha_enabled" />
            </div>
            <div class="space-y-3">
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">Site Key</label>
                    <input type="text" wire:model="recaptcha_site_key" placeholder="Your reCAPTCHA v3 Site Key" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    @error('recaptcha_site_key') <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">
                        Secret Key
                        @if($recaptchaHasSecret)
                            <span class="text-green-600 dark:text-green-400">(configured)</span>
                        @endif
                    </label>
                    <input type="password" wire:model="recaptcha_secret_key" placeholder="{{ $recaptchaHasSecret ? 'Leave blank to keep current' : 'Your reCAPTCHA v3 Secret Key' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    @error('recaptcha_secret_key') <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Get your keys from the <a href="https://www.google.com/recaptcha/admin" target="_blank" class="text-blue-600 hover:underline dark:text-blue-400">Google reCAPTCHA Admin Console</a>. Make sure to select reCAPTCHA v3.
                </p>
            </div>
        </div>

        <!-- Google Settings -->
        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 flex items-center justify-center">
                    <svg class="w-6 h-6" viewBox="0 0 24 24">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">Google</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Configure Google OAuth credentials</p>
                </div>
                <x-toggle wire-model="google_login_enabled" />
            </div>
            <div class="space-y-3">
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">Client ID</label>
                    <input type="text" wire:model="google_client_id" placeholder="Your Google Client ID" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    @error('google_client_id') <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">
                        Client Secret
                        @if($googleHasSecret)
                            <span class="text-green-600 dark:text-green-400">(configured)</span>
                        @endif
                    </label>
                    <input type="password" wire:model="google_client_secret" placeholder="{{ $googleHasSecret ? 'Leave blank to keep current' : 'Your Google Client Secret' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    @error('google_client_secret') <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>
                <div x-data="{
                    copied: false,
                    copyToClipboard() {
                        const input = this.$refs.googleCallback;
                        input.select();
                        input.setSelectionRange(0, 99999);
                        document.execCommand('copy');
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    }
                }">
                    <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">Callback URL</label>
                    <div class="flex">
                        <input type="text" x-ref="googleCallback" value="{{ $googleCallbackUrl }}" readonly class="bg-gray-100 border border-gray-300 text-gray-500 text-sm rounded-l-lg block w-full p-2 dark:bg-gray-600 dark:border-gray-600 dark:text-gray-400">
                        <button type="button" @click="copyToClipboard()" class="px-3 bg-gray-200 border border-l-0 border-gray-300 rounded-r-lg hover:bg-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600">
                            <svg x-show="!copied" class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <svg x-show="copied" x-cloak class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Add this URL to your Google Cloud Console</p>
                </div>
            </div>
        </div>

        <!-- Facebook Settings -->
        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 flex items-center justify-center bg-[#1877F2] rounded-lg">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">Facebook</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Configure Facebook OAuth credentials</p>
                </div>
                <x-toggle wire-model="facebook_login_enabled" />
            </div>
            <div class="space-y-3">
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">App ID</label>
                    <input type="text" wire:model="facebook_client_id" placeholder="Your Facebook App ID" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    @error('facebook_client_id') <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">
                        App Secret
                        @if($facebookHasSecret)
                            <span class="text-green-600 dark:text-green-400">(configured)</span>
                        @endif
                    </label>
                    <input type="password" wire:model="facebook_client_secret" placeholder="{{ $facebookHasSecret ? 'Leave blank to keep current' : 'Your Facebook App Secret' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    @error('facebook_client_secret') <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>
                <div x-data="{
                    copied: false,
                    copyToClipboard() {
                        const input = this.$refs.facebookCallback;
                        input.select();
                        input.setSelectionRange(0, 99999);
                        document.execCommand('copy');
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    }
                }">
                    <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">Callback URL</label>
                    <div class="flex">
                        <input type="text" x-ref="facebookCallback" value="{{ $facebookCallbackUrl }}" readonly class="bg-gray-100 border border-gray-300 text-gray-500 text-sm rounded-l-lg block w-full p-2 dark:bg-gray-600 dark:border-gray-600 dark:text-gray-400">
                        <button type="button" @click="copyToClipboard()" class="px-3 bg-gray-200 border border-l-0 border-gray-300 rounded-r-lg hover:bg-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600">
                            <svg x-show="!copied" class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <svg x-show="copied" x-cloak class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Add this URL to your Facebook App settings</p>
                </div>
            </div>
        </div>

        <!-- GitHub Settings -->
        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 flex items-center justify-center bg-gray-900 dark:bg-gray-700 rounded-lg">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 0C4.477 0 0 4.484 0 10.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0110 4.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.942.359.31.678.921.678 1.856 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0020 10.017C20 4.484 15.522 0 10 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">GitHub</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Configure GitHub OAuth credentials</p>
                </div>
                <x-toggle wire-model="github_login_enabled" />
            </div>
            <div class="space-y-3">
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">Client ID</label>
                    <input type="text" wire:model="github_client_id" placeholder="Your GitHub Client ID" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    @error('github_client_id') <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">
                        Client Secret
                        @if($githubHasSecret)
                            <span class="text-green-600 dark:text-green-400">(configured)</span>
                        @endif
                    </label>
                    <input type="password" wire:model="github_client_secret" placeholder="{{ $githubHasSecret ? 'Leave blank to keep current' : 'Your GitHub Client Secret' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    @error('github_client_secret') <span class="text-xs text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>
                <div x-data="{
                    copied: false,
                    copyToClipboard() {
                        const input = this.$refs.githubCallback;
                        input.select();
                        input.setSelectionRange(0, 99999);
                        document.execCommand('copy');
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    }
                }">
                    <label class="block mb-1 text-xs font-medium text-gray-700 dark:text-gray-300">Callback URL</label>
                    <div class="flex">
                        <input type="text" x-ref="githubCallback" value="{{ $githubCallbackUrl }}" readonly class="bg-gray-100 border border-gray-300 text-gray-500 text-sm rounded-l-lg block w-full p-2 dark:bg-gray-600 dark:border-gray-600 dark:text-gray-400">
                        <button type="button" @click="copyToClipboard()" class="px-3 bg-gray-200 border border-l-0 border-gray-300 rounded-r-lg hover:bg-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600">
                            <svg x-show="!copied" class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <svg x-show="copied" x-cloak class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Add this URL to your GitHub OAuth App settings</p>
                </div>
            </div>
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
