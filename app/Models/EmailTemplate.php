<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'subject',
        'body',
        'variables',
        'category',
        'is_active',
        'default_subject',
        'default_body',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Find a template by its slug.
     */
    public static function findBySlug(string $slug): ?self
    {
        return self::where('slug', $slug)->where('is_active', true)->first();
    }

    /**
     * Parse template placeholders with provided data.
     */
    public function parse(array $data): array
    {
        $subject = $this->subject;
        $body = $this->body;

        foreach ($data as $key => $value) {
            $placeholder = '{'.$key.'}';
            $subject = str_replace($placeholder, (string) $value, $subject);
            $body = str_replace($placeholder, (string) $value, $body);
        }

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }

    /**
     * Reset template to default values.
     */
    public function resetToDefault(): bool
    {
        if ($this->default_subject && $this->default_body) {
            $this->subject = $this->default_subject;
            $this->body = $this->default_body;

            return $this->save();
        }

        return false;
    }

    /**
     * Get templates by category.
     */
    public static function getByCategory(string $category): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('category', $category)->get();
    }

    /**
     * Scope for active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get available categories.
     */
    public static function getCategories(): array
    {
        return [
            'build' => 'Build Notifications',
            'payment' => 'Payment Notifications',
            'auth' => 'Authentication',
        ];
    }
}
