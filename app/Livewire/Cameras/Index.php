<?php

namespace App\Livewire\Cameras;

use App\Models\Camera;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.cameras.index', [
            'cameras' => Camera::visible()->get(),
        ])->layoutData(['bgClass' => 'bg-black']);
    }
}
