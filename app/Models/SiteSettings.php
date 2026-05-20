<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSettings extends Model
{
    protected $fillable = [
        'site_name',
        'show_site_name',
        'logo',
        'favicon',
        'banner_background',
        'contact_email',
        'contact_phone',
        'contact_address',
        'whatsapp_number',
        'facebook_url',
        'instagram_url',
        'twitter_url',
        'footer_text',
    ];

    protected function casts(): array
    {
        return [
            'show_site_name' => 'boolean',
        ];
    }

    public static function getSettings()
    {
        return self::first() ?? self::create([
            'site_name' => 'Krafty Curvz',
            'show_site_name' => true,
        ]);
    }
}
