<?php

namespace App\Livewire;

use App\Models\Collab;
use Livewire\Component;

class CollabShow extends Component
{
    public Collab $collab;

    public function mount(string $slug): void
    {
        $this->collab = Collab::where('slug', $slug)
            ->where('is_visible', true)
            ->with(['episode', 'character.job', 'character.socialLinks'])
            ->firstOrFail();
    }

    public function render()
    {
        return view('livewire.collab-show')
            ->layoutData(['bgClass' => 'bg-black']);
    }
}
