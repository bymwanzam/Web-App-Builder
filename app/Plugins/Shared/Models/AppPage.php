<?php

namespace App\Plugins\Shared\Models;

use App\Models\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AppPage extends Model
{
    protected $table = 'app_pages';

    protected $fillable = [
        'app_id',
        'title',
        'slug',
        'html_content',
        'css_content',
        'js_content',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }

    /**
     * Scope to get published pages only.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope to order pages by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Generate a unique slug for a page within an app.
     */
    public static function generateSlug(string $title, int $appId, ?int $excludeId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while (static::slugExists($slug, $appId, $excludeId)) {
            $slug = $baseSlug.'-'.$counter++;
        }

        return $slug;
    }

    /**
     * Check if a slug exists for the given app.
     */
    public static function slugExists(string $slug, int $appId, ?int $excludeId = null): bool
    {
        $query = static::where('app_id', $appId)->where('slug', $slug);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Get the URL for this hosted page.
     */
    public function getUrlAttribute(): string
    {
        return route('hosted-page.show', [
            'app' => $this->app_id,
            'slug' => $this->slug,
        ]);
    }
}
