@php
    $siteName = \App\Models\SystemSetting::get('site_name', config('app.name'));
    $siteTagline = \App\Models\SystemSetting::get('site_tagline', 'Build native apps');
    $currentYear = date('Y');
    $logoPath = \App\Models\SystemSetting::get('logo_path');
    $logoDarkPath = \App\Models\SystemSetting::get('logo_dark_path');
@endphp

<footer class="p-4 bg-white md:p-8 lg:p-10 dark:bg-gray-800">
    <div class="mx-auto max-w-screen-xl text-center">
        <a href="/" wire:navigate class="inline-flex justify-center items-center mb-4">
            @if($logoPath && $logoDarkPath)
                {{-- Both logos available - show based on theme --}}
                <img src="{{ Storage::url($logoPath) }}" alt="{{ $siteName }}" class="h-10 w-auto dark:hidden">
                <img src="{{ Storage::url($logoDarkPath) }}" alt="{{ $siteName }}" class="h-10 w-auto hidden dark:block">
            @elseif($logoPath)
                {{-- Only light logo --}}
                <img src="{{ Storage::url($logoPath) }}" alt="{{ $siteName }}" class="h-10 w-auto">
            @elseif($logoDarkPath)
                {{-- Only dark logo --}}
                <img src="{{ Storage::url($logoDarkPath) }}" alt="{{ $siteName }}" class="h-10 w-auto">
            @else
                {{-- No logo uploaded - show default icon, site name, and tagline --}}
                <div class="relative">
                    <div class="w-11 h-11 bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.537 12.625a4.421 4.421 0 0 0 2.684 4.047 10.96 10.96 0 0 1-1.384 2.845c-.834 1.218-1.7 2.432-3.062 2.457-1.34.025-1.77-.794-3.3-.794-1.531 0-2.01.769-3.275.82-1.316.049-2.317-1.318-3.158-2.532-1.72-2.484-3.032-7.017-1.27-10.077A4.9 4.9 0 0 1 8.91 6.884c1.292-.025 2.51.869 3.3.869.789 0 2.27-1.075 3.828-.917a4.67 4.67 0 0 1 3.66 1.984 4.524 4.524 0 0 0-2.16 3.805m-2.52-7.432A4.4 4.4 0 0 0 16.06 2a4.482 4.482 0 0 0-2.945 1.516 4.185 4.185 0 0 0-1.061 3.093 3.708 3.708 0 0 0 2.967-1.416Z"/>
                        </svg>
                    </div>
                    <div class="absolute -top-0.5 -right-0.5 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-gray-800"></div>
                </div>
                <div class="ml-3 text-left">
                    <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 dark:from-blue-400 dark:to-blue-600 bg-clip-text text-transparent">{{ $siteName }}</span>
                    <p class="text-[10px] font-medium text-gray-500 dark:text-gray-400 mt-0.5">{{ $siteTagline }}</p>
                </div>
            @endif
        </a>

        <p class="my-6 text-gray-500 dark:text-gray-400">
            Build native apps from your website in minutes. Professional app builder with no coding required.
        </p>

        <ul class="flex flex-wrap justify-center items-center mb-6 text-sm text-gray-500 dark:text-gray-400">
            <li>
                <a href="{{ route('legal.privacy') }}" wire:navigate class="mr-4 hover:underline md:mr-6">Privacy Policy</a>
            </li>
            <li>
                <a href="{{ route('legal.terms') }}" wire:navigate class="mr-4 hover:underline md:mr-6">Terms of Service</a>
            </li>
            <li>
                <a href="{{ route('legal.cookies') }}" wire:navigate class="hover:underline">Cookie Policy</a>
            </li>
        </ul>

        <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">
            © {{ $currentYear }}. All Rights Reserved.
        </span>
    </div>
</footer>
