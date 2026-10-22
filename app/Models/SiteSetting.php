<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'label', 'group', 'type', 'value'];

    /** @var array<string, string|null> */
    private static array $memory = [];

    private static bool $hydrated = false;

    /** @return array<string, string|null> */
    public static function allKeyed(): array
    {
        if (! self::$hydrated) {
            try {
                self::$memory = Cache::remember('site_settings.keyed', 3600, fn (): array => self::query()->pluck('value', 'key')->all());
            } catch (\Throwable) {
                self::$memory = [];
            }
            self::$hydrated = true;
        }

        return self::$memory;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $all = self::allKeyed();

        return array_key_exists($key, $all) && $all[$key] !== null && $all[$key] !== '' ? $all[$key] : $default;
    }

    public static function forgetCache(): void
    {
        self::$memory = [];
        self::$hydrated = false;
        Cache::forget('site_settings.keyed');
    }

    protected static function booted(): void
    {
        static::saved(function (): void {
            self::forgetCache();
        });
        static::deleted(function (): void {
            self::forgetCache();
        });
    }

    /**
     * Normalize a phone/WhatsApp number to digits suitable for a wa.me link.
     */
    public static function whatsappLink(?string $raw): ?string
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }
        $digits = preg_replace('/\D+/', '', $raw);
        if (! $digits) {
            return null;
        }
        // Convert local Zimbabwe leading 0 to 263.
        if (str_starts_with($digits, '0') && strlen($digits) >= 9) {
            $digits = '263'.substr($digits, 1);
        }

        return 'https://wa.me/'.$digits;
    }
}
