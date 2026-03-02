<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CameraScheduledVideo extends Model
{
    protected $fillable = [
        'camera_id',
        'camera_video_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    public function camera(): BelongsTo
    {
        return $this->belongsTo(Camera::class);
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(CameraVideo::class, 'camera_video_id');
    }
}
