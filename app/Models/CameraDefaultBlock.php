<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CameraDefaultBlock extends Model
{
    protected $fillable = [
        'camera_id',
        'camera_video_id',
        'day_of_week',
        'time_slot',
    ];

    public const DAY_LABELS = ['Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za', 'Zo'];

    /**
     * Get slot definitions from database settings (cached).
     */
    public static function slots(): array
    {
        return CameraSlotSetting::getSlots();
    }

    public static function slotForTime(string $time): string
    {
        foreach (static::slots() as $slot => $bounds) {
            $start = $bounds['start'];
            $end = $bounds['end'] === '24:00' ? '24:00' : $bounds['end'];

            if ($start <= $end) {
                // Normal slot: e.g. 06:00 - 18:00
                if ($time >= $start && $time < $end) {
                    return $slot;
                }
            } else {
                // Wrapping slot (crosses midnight): e.g. 22:00 - 06:00
                if ($time >= $start || $time < $end) {
                    return $slot;
                }
            }
        }

        return array_key_first(static::slots()) ?? 'nacht';
    }

    public function camera(): BelongsTo
    {
        return $this->belongsTo(Camera::class);
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(CameraVideo::class, 'camera_video_id');
    }
}
