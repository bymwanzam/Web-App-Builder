<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CronLog extends Model
{
    protected $fillable = [
        'job_name',
        'job_class',
        'status',
        'started_at',
        'completed_at',
        'duration',
        'triggered_by',
        'servers_triggered',
        'builds_processed',
        'message',
        'exception',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'servers_triggered' => 'array',
        'builds_processed' => 'integer',
        'duration' => 'integer',
    ];

    /**
     * Scope to get recent logs.
     */
    public function scopeRecent($query, $limit = 50)
    {
        return $query->orderBy('started_at', 'desc')->limit($limit);
    }

    /**
     * Scope to get failed logs.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to get successful logs.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope to filter by job name.
     */
    public function scopeByJob($query, $jobName)
    {
        return $query->where('job_name', $jobName);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('started_at', [$startDate, $endDate]);
    }

    /**
     * Scope to exclude pruning jobs.
     */
    public function scopeExcludePruning($query)
    {
        return $query->where('job_name', '!=', 'PruneCronLogs');
    }

    /**
     * Get the status badge color.
     */
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'success' => 'green',
            'failed' => 'red',
            'running' => 'yellow',
            default => 'gray',
        };
    }

    /**
     * Get human-readable duration.
     */
    public function getHumanDurationAttribute()
    {
        if (! $this->duration) {
            return 'N/A';
        }

        if ($this->duration < 60) {
            return $this->duration.'s';
        }

        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;

        return "{$minutes}m {$seconds}s";
    }

    /**
     * Prune logs older than specified days.
     */
    public static function pruneOld($days = 30)
    {
        return static::where('created_at', '<', now()->subDays($days))->delete();
    }

    /**
     * Get the latest successful run for a specific job.
     */
    public static function latestSuccessfulRun($jobName)
    {
        return static::where('job_name', $jobName)
            ->where('status', 'success')
            ->orderBy('completed_at', 'desc')
            ->first();
    }

    /**
     * Get statistics for a specific job.
     */
    public static function getJobStats($jobName, $days = 7)
    {
        $logs = static::where('job_name', $jobName)
            ->where('created_at', '>=', now()->subDays($days))
            ->get();

        return [
            'total' => $logs->count(),
            'successful' => $logs->where('status', 'success')->count(),
            'failed' => $logs->where('status', 'failed')->count(),
            'success_rate' => $logs->count() > 0
                ? round(($logs->where('status', 'success')->count() / $logs->count()) * 100, 2)
                : 0,
            'avg_duration' => $logs->where('duration', '>', 0)->avg('duration'),
        ];
    }
}
