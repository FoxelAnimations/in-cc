<?php

namespace App\Livewire\Episodes;

use App\Models\AgeGate;
use App\Models\Episode;
use Livewire\Component;

class Show extends Component
{
    public function render()
    {
        $allEpisodes = Episode::with(['characters.job', 'characters.socialLinks'])
            ->withAvg('ratings', 'rating')
            ->where('visible', true)
            ->whereIn('category', ['episode', 'short', 'mini'])
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        return view('livewire.episodes.show', [
            'episodes' => $allEpisodes->get('episode', collect()),
            'shorts' => $allEpisodes->get('short', collect()),
            'minis' => $allEpisodes->get('mini', collect()),
            'ageGate' => AgeGate::first(),
        ])->layoutData(['bgClass' => 'bg-black']);
    }
}
