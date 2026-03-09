<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CameraDefaultSound extends Model
{
    protected $fillable = [
        'camera_id',
        'time_slot',
        'sound_path',
    ];

    public function camera(): BelongsTo
    {
        return $this->belongsTo(Camera::class);
    }

    public function soundUrl(): ?string
    {
        return $this->sound_path ? Storage::url($this->sound_path) : null;
    }
}
