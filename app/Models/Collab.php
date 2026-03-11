<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Collab extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_published' => 'boolean',
        'is_visible' => 'boolean',
        'show_on_homepage' => 'boolean',
        'link1_new_tab' => 'boolean',
        'link2_new_tab' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }

    public function character()
    {
        return $this->belongsTo(Character::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                     ->where('is_visible', true)
                     ->orderBy('sort_order');
    }

    public function hasLink1(): bool
    {
        return !empty($this->link1_label) && !empty($this->link1_url);
    }

    public function hasLink2(): bool
    {
        return !empty($this->link2_label) && !empty($this->link2_url);
    }

    public static function generateSlug(string $title, ?int $excludeId = null): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))->exists()) {
            $slug = $original . '-' . $count++;
        }

        return $slug;
    }
}
