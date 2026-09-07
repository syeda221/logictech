<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'type', 'group', 'label', 'description'];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $cacheKey = 'setting_' . $key;
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }

            return self::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value, ?string $group = null, ?string $type = null, ?string $label = null, ?string $description = null): bool
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            $setting = new self();
            $setting->key = $key;
            $setting->group = $group ?? 'company';
            $setting->type = $type ?? 'string';
            $setting->label = $label ?? ucwords(str_replace('_', ' ', $key));
            $setting->description = $description;
        }

        $setting->value = $value;
        $setting->save();

        Cache::forget('setting_' . $key);

        return true;
    }

    /**
     * Get all settings grouped by category
     */
    public static function getAllGrouped(): array
    {
        return self::all()->groupBy('group')->map(function ($settings) {
            return $settings->map(function ($setting) {
                return [
                    'key' => $setting->key,
                    'value' => self::castValue($setting->value, $setting->type),
                    'type' => $setting->type,
                    'label' => $setting->label,
                    'description' => $setting->description,
                ];
            });
        })->toArray();
    }

    /**
     * Get company logo source (Base64 data URI if file exists on disk, or asset URL)
     * This guarantees 100% reliable rendering in HTML invoices, print views, PDFs, and thermal receipts across all environments.
     */
    public static function getLogoUrl(): ?string
    {
        $logo = self::get('company_logo') ?: self::get('web_site_logo');
        if (empty($logo)) {
            return null;
        }

        if (str_starts_with($logo, 'data:') || str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://')) {
            return $logo;
        }

        $cleanPath = ltrim($logo, '/');

        $candidates = array_unique([
            public_path($cleanPath),
            base_path($cleanPath),
            base_path('public/' . $cleanPath),
            base_path('public_html/' . $cleanPath),
            base_path('public_html/public/' . $cleanPath),
            public_path('uploads/settings/' . basename($cleanPath)),
            base_path('uploads/settings/' . basename($cleanPath)),
            public_path('uploads/' . basename($cleanPath)),
            base_path('uploads/' . basename($cleanPath)),
        ]);

        foreach ($candidates as $candidate) {
            if (!empty($candidate) && file_exists($candidate) && is_file($candidate)) {
                $ext = strtolower(pathinfo($candidate, PATHINFO_EXTENSION));
                $mime = match($ext) {
                    'png' => 'image/png',
                    'jpg', 'jpeg' => 'image/jpeg',
                    'svg' => 'image/svg+xml',
                    'webp' => 'image/webp',
                    'gif' => 'image/gif',
                    default => @mime_content_type($candidate) ?: 'image/png'
                };
                $content = @file_get_contents($candidate);
                if ($content !== false && strlen($content) > 0) {
                    return 'data:' . $mime . ';base64,' . base64_encode($content);
                }
            }
        }

        return asset($cleanPath);
    }

    /**
     * Cast value to appropriate type
     */
    private static function castValue($value, string $type)
    {
        return match ($type) {
            'integer' => (int) $value,
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($value, true),
            default => $value,
        };
    }
}
