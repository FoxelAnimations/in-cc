<?php

namespace App\Livewire;

use App\Models\Collab;
use Livewire\Component;

class CollabIndex extends Component
{
    public function render()
    {
        return view('livewire.collabs', [
            'collabs' => Collab::published()->with(['episode', 'character'])->get(),
        ])->layoutData(['bgClass' => 'bg-black']);
    }
}
