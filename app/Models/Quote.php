<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function character()
    {
        return $this->belongsTo(Character::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
