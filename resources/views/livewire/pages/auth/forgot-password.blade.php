<?php

use App\Models\SystemSetting;
use App\Services\RecaptchaService;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';
    public string $recaptcha_token = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // Check if email is configured
        if (! SystemSetting::isEmailConfigured()) {
            throw ValidationException::withMessages([
                'email' => 'Password reset is currently unavailable. Please contact support.',
            ]);
        }

        // Verify reCAPTCHA if enabled
        if (RecaptchaService::isEnabled()) {
            if (! RecaptchaService::verify($this->recaptcha_token, 'forgot_password')) {
                throw ValidationException::withMessages([
                    'email' => 'reCAPTCHA verification failed. Please try again.',
                ]);
            }
        }

        try {
            $status = Password::sendResetLink($this->only('email'));

            if ($status != Password::RESET_LINK_SENT) {
                $this->addError('email', __($status));

                return;
            }

            $this->reset('email');

            session()->flash('status', __($status));
        } catch (\Exception $e) {
            \Log::error('Password reset email failed', ['error' => $e->getMessage()]);
            throw ValidationException::withMessages([
                'email' => 'Unable to send reset email. Please try again later or contact support.',
            ]);
        }
    }
}; ?>

@php
    $emailConfigured = \App\Models\SystemSetting::isEmailConfigured();
@endphp

<div class="space-y-4 md:space-y-6">
    @if (! $emailConfigured)
        <!-- Email Not Configured State -->
        <div>
            <div class="flex items-center justify-center w-12 h-12 mb-4 mx-auto bg-yellow-100 dark:bg-yellow-900 rounded-full">
                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold leading-tight tracking-tight text-center text-gray-900 md:text-2xl dark:text-white mb-2">
                Password Reset Unavailable
            </h1>
            <p class="text-sm text-center text-gray-500 dark:text-gray-400 mb-6">
                Email service is not configured. Please contact the administrator for assistance.
            </p>
            <a href="{{ route('login') }}" wire:navigate class="block w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-hidden focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Back to login
            </a>
        </div>
    @elseif (session('status'))
        <!-- Success State: Email Sent -->
        <div>
            <div class="flex items-center justify-center w-12 h-12 mb-4 mx-auto bg-green-100 dark:bg-green-900 rounded-full">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold leading-tight tracking-tight text-center text-gray-900 md:text-2xl dark:text-white mb-2">
                Check your email
            </h1>
            <p class="text-sm text-center text-gray-500 dark:text-gray-400 mb-6">
                {{ session('status') }}
            </p>
            <a href="{{ route('login') }}" wire:navigate class="block w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-hidden focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Back to login
            </a>
        </div>
    @else
        <!-- Initial State: Request Reset -->
        <div>
            <div class="flex items-center justify-center w-12 h-12 mb-4 mx-auto bg-blue-100 dark:bg-blue-900 rounded-full">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold leading-tight tracking-tight text-center text-gray-900 md:text-2xl dark:text-white mb-2">
                Forgot your password?
            </h1>
            <p class="text-sm text-center text-gray-500 dark:text-gray-400 mb-6">
                No worries, we'll send you reset instructions.
            </p>
            <form wire:submit="sendPasswordResetLink" class="space-y-4 md:space-y-6">
                <div>
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                    <input wire:model="email" type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="name@company.com" required autofocus>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- reCAPTCHA -->
                @php
                    $recaptchaEnabled = \App\Services\RecaptchaService::isEnabled();
                    $recaptchaSiteKey = \App\Services\RecaptchaService::getSiteKey();
                @endphp
                @if($recaptchaEnabled && $recaptchaSiteKey)
                    <input type="hidden" wire:model="recaptcha_token">
                @endif

                @if($recaptchaEnabled && $recaptchaSiteKey)
                <div x-data="{
                    async submitForm() {
                        try {
                            const token = await grecaptcha.execute('{{ $recaptchaSiteKey }}', {action: 'forgot_password'});
                            $wire.set('recaptcha_token', token);
                            await $nextTick();
                            $wire.sendPasswordResetLink();
                        } catch (error) {
                            console.error('reCAPTCHA error:', error);
                            $wire.sendPasswordResetLink();
                        }
                    }
                }">
                    <button type="button" @click="submitForm()" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-hidden focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Reset password</button>
                </div>
                @else
                <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-hidden focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Reset password</button>
                @endif
                <div class="flex items-center justify-center">
                    <a href="{{ route('login') }}" wire:navigate class="flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to login
                    </a>
                </div>
            </form>
        </div>
    @endif
</div>
