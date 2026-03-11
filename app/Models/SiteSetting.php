<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $table = 'site_settings';

    protected $fillable = [
        'login_enabled',
        'register_enabled',
        'chat_enabled',
        'default_chat_character_id',
        'chat_blocked_sound',
        'chat_notification_sound',
        'weather_enabled',
        'rain_mode',
        'manual_rain_intensity',
        'manual_cloud_cover',
        'manual_wind_speed',
        'show_episodes',
        'show_shorts',
        'show_minis',
        'show_specials',
        'show_collabs',
        'show_quotes',
        'carousel_title',
    ];

    protected function casts(): array
    {
        return [
            'login_enabled' => 'boolean',
            'register_enabled' => 'boolean',
            'chat_enabled' => 'boolean',
            'weather_enabled' => 'boolean',
            'show_episodes' => 'boolean',
            'show_shorts' => 'boolean',
            'show_minis' => 'boolean',
            'show_specials' => 'boolean',
            'show_collabs' => 'boolean',
            'show_quotes' => 'boolean',
        ];
    }

    public function defaultChatCharacter()
    {
        return $this->belongsTo(Character::class, 'default_chat_character_id');
    }
}
