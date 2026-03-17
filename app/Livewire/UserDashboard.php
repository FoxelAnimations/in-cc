<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserDashboard extends Component
{
    public function render()
    {
        $collectedBeacons = Auth::user()
            ->collectedBeacons()
            ->where('is_collectible', true)
            ->orderByPivot('collected_at', 'desc')
            ->get();

        return view('livewire.user-dashboard', [
            'collectedBeacons' => $collectedBeacons,
        ])->layoutData(['bgClass' => 'bg-black']);
    }
}
