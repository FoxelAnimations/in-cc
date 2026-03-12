<?php

namespace App\Livewire\Admin;

use App\Models\ChatConversation;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class ChatNotifier extends Component
{
    public int $lastKnownUnread = -1;

    public function pollUnread(): void
    {
        $totalUnread = (int) ChatConversation::where('status', 'open')
            ->sum('unread_count');

        if ($totalUnread > $this->lastKnownUnread && $this->lastKnownUnread >= 0) {
            $this->dispatch('chat-ping');
        }

        $this->lastKnownUnread = $totalUnread;
    }

    public function render()
    {
        return <<<'HTML'
        <div wire:poll.5s="pollUnread" class="hidden"></div>
        HTML;
    }
}
