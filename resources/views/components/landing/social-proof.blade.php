@php
    // Get real statistics from database
    $totalUsers = \App\Models\User::count();
    $totalApps = \App\Models\App::count();
    $totalBuilds = \App\Models\AppBuild::where('status', 'completed')->count();

    // Minimum thresholds for display
    $userThreshold = 100;
    $appThreshold = 500;
    $buildThreshold = 1000;

    // Format numbers with thresholds - show minimum if below threshold
    $usersDisplay = $totalUsers >= $userThreshold ? number_format($totalUsers) : $userThreshold . '+';
    $appsDisplay = $totalApps >= $appThreshold ? number_format($totalApps) : $appThreshold . '+';
    $buildsDisplay = $totalBuilds >= $buildThreshold ? number_format($totalBuilds) : number_format($buildThreshold) . '+';
@endphp

<section class="bg-white dark:bg-gray-900">
    <div class="max-w-screen-xl px-4 py-8 mx-auto text-center lg:py-16 lg:px-6">
        <dl class="grid max-w-screen-md gap-8 mx-auto text-gray-900 sm:grid-cols-3 dark:text-white">
            <div class="flex flex-col items-center justify-center">
                <dt class="mb-2 text-3xl md:text-4xl font-extrabold">{{ $usersDisplay }}</dt>
                <dd class="font-light text-gray-500 dark:text-gray-400">Happy developers</dd>
            </div>
            <div class="flex flex-col items-center justify-center">
                <dt class="mb-2 text-3xl md:text-4xl font-extrabold">{{ $appsDisplay }}</dt>
                <dd class="font-light text-gray-500 dark:text-gray-400">Apps created</dd>
            </div>
            <div class="flex flex-col items-center justify-center">
                <dt class="mb-2 text-3xl md:text-4xl font-extrabold">{{ $buildsDisplay }}</dt>
                <dd class="font-light text-gray-500 dark:text-gray-400">Builds generated</dd>
            </div>
        </dl>
    </div>
</section>
