<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class WebsiteSetting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'brand_name' => 'Global Value Thesis',
            'short_name' => 'GVT',
            'slogan' => 'Every investment needs a thesis.',
            'logo_path' => null,
            'favicon_path' => null,
            'contact_email' => 'research@gvt.test',
            'contact_phone' => '+20 100 000 0000',
            'contact_address' => 'Cairo, Egypt',
            'linkedin_url' => 'https://linkedin.com/company/global-value-thesis',
            'x_url' => null,
            'instagram_url' => null,
            'default_seo_title' => 'Global Value Thesis',
            'default_seo_description' => 'Institutional real estate investment advisory and developer certification.',
            'default_seo_keywords' => 'investment thesis, real estate advisory, developer certification, Egypt real estate',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function site(): array
    {
        return Cache::rememberForever('website_settings.site', function (): array {
            $value = static::query()->where('key', 'site')->first()?->value;

            return [...static::defaults(), ...(is_array($value) ? $value : [])];
        });
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    public static function saveSite(array $settings): void
    {
        static::query()->updateOrCreate(
            ['key' => 'site'],
            ['value' => [...static::site(), ...$settings]],
        );

        Cache::forget('website_settings.site');
    }
}
