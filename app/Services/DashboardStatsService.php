<?php

namespace App\Services;

use App\Models\App;
use App\Models\AppBuild;
use App\Models\AppBuilder;
use App\Models\AppNotification;
use App\Models\Plan;
use App\Models\Plugin;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardStatsService
{
    /**
     * Cache TTL in seconds (5 minutes)
     */
    private const CACHE_TTL = 300;

    /**
     * Get all dashboard statistics with caching
     */
    public function getStats(string $dateRange = 'all'): array
    {
        $cacheKey = "admin.overview.stats.{$dateRange}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($dateRange) {
            return [
                // Primary metrics
                'users_count' => $this->getTotalUsers(),
                'users_growth' => $this->getUsersGrowth(),
                'active_subscriptions' => $this->getActiveSubscriptions(),
                'subscriptions_growth' => $this->getSubscriptionsGrowth(),
                'monthly_revenue' => $this->getRevenue($dateRange),
                'revenue_growth' => $this->getRevenueGrowth(),
                'total_apps' => $this->getTotalApps(),
                'apps_growth' => $this->getAppsGrowth(),

                // Build metrics
                'total_builds' => $this->getTotalBuilds($dateRange),
                'completed_builds_today' => $this->getCompletedBuildsToday(),
                'failed_builds_today' => $this->getFailedBuildsToday(),
                'build_success_rate' => $this->getBuildSuccessRate(),
                'avg_build_duration' => $this->getAverageBuildDuration(),
                'pending_queue' => $this->getPendingQueue(),

                // Secondary metrics
                'total_transactions' => $this->getTotalTransactions($dateRange),
                'active_plans' => $this->getActivePlans(),
                'active_builders' => $this->getActiveBuilders(),

                // Additional metrics
                'total_revenue_all_time' => $this->getTotalRevenueAllTime(),
                'pending_payments' => $this->getPendingPayments(),
                'active_payment_gateways' => $this->getActivePaymentGateways(),
                'push_notifications_sent_today' => $this->getPushNotificationsSentToday(),
                'active_users_week' => $this->getActiveUsersLastWeek(),

                // Chart data
                'revenue_chart_data' => $this->getRevenueChartData(),
                'build_activity_chart_data' => $this->getBuildActivityChartData(),

                // Distribution & Analytics
                'platform_distribution' => $this->getPlatformDistribution(),
                'revenue_by_gateway' => $this->getRevenueByGateway(),
                'build_performance_by_platform' => $this->getBuildPerformanceByPlatform(),

                // Recent data
                'recent_builds' => $this->getRecentBuilds(),
                'recent_transactions' => $this->getRecentTransactions(),
            ];
        });
    }

    /**
     * Clear cache for dashboard stats
     */
    public function clearCache(): void
    {
        $ranges = ['all', 'today', 'week', 'month', 'year'];
        foreach ($ranges as $range) {
            Cache::forget("admin.overview.stats.{$range}");
        }
    }

    // ========================================
    // PRIMARY METRICS
    // ========================================

    private function getTotalUsers(): int
    {
        return User::count();
    }

    private function getUsersGrowth(): float
    {
        return $this->calculateGrowth(
            User::whereMonth('created_at', now()->month)->count(),
            User::whereMonth('created_at', now()->subMonth()->month)->count()
        );
    }

    private function getActiveSubscriptions(): int
    {
        return Subscription::where('status', 'active')->count();
    }

    private function getSubscriptionsGrowth(): float
    {
        return $this->calculateGrowth(
            Subscription::where('status', 'active')
                ->whereMonth('created_at', now()->month)
                ->count(),
            Subscription::where('status', 'active')
                ->whereMonth('created_at', now()->subMonth()->month)
                ->count()
        );
    }

    private function getRevenue(string $dateRange): float
    {
        $query = Transaction::where('status', 'completed');
        $query = $this->applyDateFilter($query, $dateRange);

        return round($query->sum('amount'), 2);
    }

    private function getRevenueGrowth(): float
    {
        return $this->calculateGrowth(
            Transaction::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            Transaction::where('status', 'completed')
                ->whereMonth('created_at', now()->subMonth()->month)
                ->sum('amount')
        );
    }

    private function getTotalApps(): int
    {
        return App::count();
    }

    private function getAppsGrowth(): float
    {
        return $this->calculateGrowth(
            App::whereMonth('created_at', now()->month)->count(),
            App::whereMonth('created_at', now()->subMonth()->month)->count()
        );
    }

    // ========================================
    // BUILD METRICS
    // ========================================

    private function getTotalBuilds(string $dateRange): int
    {
        $query = AppBuild::query();
        $query = $this->applyDateFilter($query, $dateRange);

        return $query->count();
    }

    private function getCompletedBuildsToday(): int
    {
        return AppBuild::whereDate('completed_at', today())
            ->where('status', 'completed')
            ->count();
    }

    private function getFailedBuildsToday(): int
    {
        return AppBuild::whereDate('created_at', today())
            ->where('status', 'failed')
            ->count();
    }

    private function getBuildSuccessRate(): float
    {
        $total = AppBuild::whereIn('status', ['completed', 'failed'])->count();

        if ($total === 0) {
            return 0;
        }

        $completed = AppBuild::where('status', 'completed')->count();

        return round(($completed / $total) * 100, 1);
    }

    private function getAverageBuildDuration(): ?int
    {
        return AppBuild::where('status', 'completed')
            ->whereNotNull('build_duration')
            ->avg('build_duration');
    }

    private function getPendingQueue(): int
    {
        return AppBuild::where('status', 'pending')->count();
    }

    // ========================================
    // SECONDARY METRICS
    // ========================================

    private function getTotalTransactions(string $dateRange): int
    {
        $query = Transaction::query();
        $query = $this->applyDateFilter($query, $dateRange);

        return $query->count();
    }

    private function getActivePlans(): int
    {
        return Plan::where('status', 'active')->count();
    }

    private function getActiveBuilders(): int
    {
        return AppBuilder::where('status', 'active')->count();
    }

    private function getTotalRevenueAllTime(): float
    {
        return round(Transaction::where('status', 'completed')->sum('amount'), 2);
    }

    private function getPendingPayments(): float
    {
        return round(Transaction::where('status', 'pending')->sum('amount'), 2);
    }

    private function getActivePaymentGateways(): int
    {
        return Plugin::where('type', 'payment_gateway')
            ->where('status', 'active')
            ->count();
    }

    private function getPushNotificationsSentToday(): int
    {
        return AppNotification::whereDate('sent_at', today())
            ->where('status', 'sent')
            ->count();
    }

    private function getActiveUsersLastWeek(): int
    {
        return User::where(function ($query) {
            $query->whereHas('apps', function ($q) {
                $q->where('created_at', '>=', now()->subWeek());
            })->orWhereHas('apps.builds', function ($q) {
                $q->where('created_at', '>=', now()->subWeek());
            });
        })->distinct()->count();
    }

    // ========================================
    // CHART DATA
    // ========================================

    private function getRevenueChartData(): array
    {
        $driver = DB::connection()->getDriverName();

        // Database-agnostic month extraction
        $monthExpression = $driver === 'sqlite'
            ? "CAST(strftime('%m', created_at) AS INTEGER)"
            : 'MONTH(created_at)';

        $data = Transaction::where('status', 'completed')
            ->where('created_at', '>=', now()->subYear())
            ->selectRaw("{$monthExpression} as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        // Fill in missing months with 0
        $months = [];
        $labels = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = $data[$i] ?? 0;
            $labels[] = now()->month($i)->format('M');
        }

        return [
            'labels' => $labels,
            'data' => $months,
        ];
    }

    private function getBuildActivityChartData(): array
    {
        $last30Days = now()->subDays(30);

        $driver = DB::connection()->getDriverName();

        // Database-agnostic date extraction
        $dateExpression = $driver === 'sqlite'
            ? 'date(created_at)'
            : 'DATE(created_at)';

        $data = AppBuild::where('created_at', '>=', $last30Days)
            ->selectRaw("{$dateExpression} as date, status, COUNT(*) as count")
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get();

        $dates = [];
        $completed = [];
        $failed = [];
        $pending = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates[] = now()->subDays($i)->format('M d');

            $dayData = $data->where('date', $date);
            $completed[] = $dayData->where('status', 'completed')->sum('count');
            $failed[] = $dayData->where('status', 'failed')->sum('count');
            $pending[] = $dayData->where('status', 'pending')->sum('count');
        }

        return [
            'labels' => $dates,
            'completed' => $completed,
            'failed' => $failed,
            'pending' => $pending,
        ];
    }

    // ========================================
    // DISTRIBUTION & ANALYTICS
    // ========================================

    private function getPlatformDistribution(): array
    {
        $data = App::join('plugins', 'apps.platform_plugin_id', '=', 'plugins.id')
            ->select('plugins.name as platform', DB::raw('COUNT(*) as count'))
            ->groupBy('plugins.name')
            ->get();

        $total = $data->sum('count');

        return $data->map(function ($item) use ($total) {
            return [
                'platform' => $item->platform,
                'count' => $item->count,
                'percentage' => $total > 0 ? round(($item->count / $total) * 100, 1) : 0,
            ];
        })->toArray();
    }

    private function getRevenueByGateway(): array
    {
        $data = Transaction::where('status', 'completed')
            ->selectRaw('payment_method, SUM(amount) as total')
            ->whereNotNull('payment_method')
            ->groupBy('payment_method')
            ->get();

        $total = $data->sum('total');

        return $data->map(function ($item) use ($total) {
            return [
                'gateway' => $item->payment_method ?? 'Unknown',
                'amount' => round($item->total, 2),
                'percentage' => $total > 0 ? round(($item->total / $total) * 100, 1) : 0,
            ];
        })->toArray();
    }

    private function getBuildPerformanceByPlatform(): array
    {
        return AppBuild::select('platform', DB::raw('
            COUNT(*) as total,
            SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed,
            AVG(CASE WHEN status = "completed" AND build_duration IS NOT NULL THEN build_duration END) as avg_duration
        '))
            ->groupBy('platform')
            ->get()
            ->map(function ($item) {
                $successRate = $item->total > 0 ? round(($item->completed / $item->total) * 100, 1) : 0;

                return [
                    'platform' => $item->platform,
                    'total' => $item->total,
                    'completed' => $item->completed,
                    'failed' => $item->failed,
                    'success_rate' => $successRate,
                    'avg_duration' => $item->avg_duration ? round($item->avg_duration) : null,
                ];
            })
            ->toArray();
    }

    // ========================================
    // RECENT DATA
    // ========================================

    private function getRecentBuilds(): array
    {
        return AppBuild::with(['app.user'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($build) {
                return [
                    'id' => $build->id,
                    'app_name' => $build->app->name ?? 'Unknown',
                    'user_name' => $build->app->user->name ?? 'Unknown',
                    'platform' => $build->platform,
                    'status' => $build->status,
                    'version' => $build->version_name,
                    'created_at' => $build->created_at,
                    'status_color' => $build->status_color,
                    'status_text' => $build->status_text,
                ];
            })
            ->toArray();
    }

    private function getRecentTransactions(): array
    {
        return Transaction::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'transaction_id' => $transaction->transaction_id,
                    'user_name' => $transaction->user->name ?? 'Unknown',
                    'amount' => $transaction->amount,
                    'currency' => $transaction->currency,
                    'status' => $transaction->status,
                    'gateway' => $transaction->payment_method ?? 'N/A',
                    'created_at' => $transaction->created_at,
                ];
            })
            ->toArray();
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Calculate growth percentage
     */
    private function calculateGrowth(float $current, float $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Apply date filter to query
     */
    private function applyDateFilter($query, string $dateRange)
    {
        return match ($dateRange) {
            'today' => $query->whereDate('created_at', today()),
            'week' => $query->where('created_at', '>=', now()->subWeek()),
            'month' => $query->where('created_at', '>=', now()->subMonth()),
            'year' => $query->where('created_at', '>=', now()->subYear()),
            default => $query,
        };
    }
}
