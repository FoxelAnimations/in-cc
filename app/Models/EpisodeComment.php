<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EpisodeComment extends Model
{
    protected $fillable = ['user_id', 'episode_id', 'body'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }
}
