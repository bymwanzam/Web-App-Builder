<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    /**
     * Get a setting value by key with optional default
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        return self::castValue($setting->value, $setting->type);
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, mixed $value, string $type = 'string', ?string $group = null): self
    {
        // Convert value to storable format
        $storableValue = match ($type) {
            'boolean' => $value ? '1' : '0',
            'integer' => (string) $value,
            'json' => is_string($value) ? $value : json_encode($value),
            default => (string) $value,
        };

        return self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $storableValue,
                'type' => $type,
                'group' => $group,
            ]
        );
    }

    /**
     * Get all settings in a group
     */
    public static function getGroup(string $group): array
    {
        return self::where('group', $group)
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => self::castValue($setting->value, $setting->type)];
            })
            ->toArray();
    }

    /**
     * Cast value based on type
     */
    protected static function castValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'integer' => (int) $value,
            'boolean' => (bool) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Check if email/SMTP is properly configured.
     */
    public static function isEmailConfigured(): bool
    {
        $mailer = self::get('mail_mailer', 'sendmail');

        if ($mailer === 'smtp') {
            $host = self::get('mail_host');
            $username = self::get('mail_username');

            // SMTP requires host and username to be configured
            return ! empty($host) && ! empty($username);
        }

        // For sendmail (PHP mail), just need from address
        return ! empty(self::get('mail_from_address'));
    }
}
