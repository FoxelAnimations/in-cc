<div wire:poll.3s="sendHeartbeat" class="flex flex-col h-full bg-zinc-900/50">
    {{-- Header: viewer count --}}
    <div class="px-3 py-2 bg-zinc-800/80 border-b border-zinc-700/50 flex items-center justify-between shrink-0">
        <span class="text-xs uppercase tracking-wider font-semibold text-zinc-300">Live Chat</span>
        <span class="flex items-center gap-1.5 text-zinc-400 text-xs">
            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
            <span>{{ $this->viewerCount }} {{ $this->viewerCount === 1 ? 'kijker' : 'kijkers' }}</span>
        </span>
    </div>

    {{-- Messages area --}}
    <div class="flex-1 overflow-y-auto px-3 py-2 space-y-1 min-h-0"
         x-data="{ autoScroll: true }"
         x-ref="chatScroll"
         @scroll="autoScroll = ($el.scrollTop + $el.clientHeight >= $el.scrollHeight - 30)"
         x-effect="if(autoScroll) $nextTick(() => { if($refs.chatScroll) $refs.chatScroll.scrollTop = $refs.chatScroll.scrollHeight })">

        @foreach ($this->messages as $msg)
            <div class="text-sm leading-relaxed group flex items-start gap-1" wire:key="msg-{{ $msg->id }}">
                <div class="flex-1 min-w-0">
                    <span class="font-semibold text-accent text-xs">{{ $msg->user->name }}</span>
                    <span class="text-white/80">{{ $msg->body }}</span>
                </div>
                @if (auth()->user()?->is_admin)
                    <button wire:click="deleteMessage({{ $msg->id }})"
                        class="opacity-0 group-hover:opacity-100 text-zinc-500 hover:text-red-400 transition shrink-0 mt-0.5"
                        title="Verwijder">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                @endif
            </div>
        @endforeach

        @if ($this->messages->isEmpty())
            <p class="text-zinc-600 text-xs text-center py-8">Nog geen berichten.</p>
        @endif
    </div>

    {{-- Chat disabled --}}
    <div class="px-3 py-3 border-t border-zinc-700/50 text-center shrink-0">
        <p class="text-zinc-500 text-xs">Chat is momenteel uitgeschakeld.</p>
    </div>
</div>
