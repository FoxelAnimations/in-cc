<div
    x-data="{ show: false }"
    x-cloak
    @show-user-profile.window="$wire.loadUser($event.detail.userId)"
    @user-profile-loaded.window="show = true"
    @keydown.escape.window="if (show) show = false"
>
    <div x-show="show" x-transition.opacity
         class="fixed inset-0 z-[60] flex items-center justify-center bg-black/85 p-4"
         @click.self="show = false">

        <div class="bg-zinc-900 border border-zinc-800 rounded-sm w-full max-w-sm overflow-hidden" @click.stop>
            <div class="p-6">

                {{-- User name --}}
                <h3 class="text-xl font-bold uppercase tracking-wider text-white mb-1">{{ $profileName }}</h3>

                {{-- Badge count --}}
                <p class="text-sm text-zinc-400 mb-4">
                    {{ $badgeCount }} {{ $badgeCount === 1 ? 'badge' : 'badges' }}
                </p>

                {{-- Badge grid --}}
                @if (count($badges) > 0)
                    <div class="grid grid-cols-4 gap-3 mb-4 max-h-48 overflow-y-auto">
                        @foreach ($badges as $badge)
                            <div class="text-center">
                                @if ($badge['image'])
                                    <img src="{{ $badge['image'] }}" alt="{{ $badge['title'] }}"
                                         class="w-14 h-14 mx-auto object-contain rounded-full border border-accent/30 bg-zinc-800 p-0.5">
                                @else
                                    <div class="w-14 h-14 mx-auto rounded-full border border-accent/30 bg-zinc-800 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-accent/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                    </div>
                                @endif
                                <p class="text-[10px] text-zinc-400 mt-1 leading-tight truncate">{{ $badge['title'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-zinc-600 mb-4">{{ __('Nog geen badges.') }}</p>
                @endif

                {{-- Admin blocking section --}}
                @if (auth()->user()?->is_admin && $userId && $userId !== auth()->id())
                    <div class="border-t border-zinc-800 pt-4 mt-2 space-y-3">
                        <p class="text-xs uppercase tracking-wider text-zinc-500 font-semibold">{{ __('Admin') }}</p>

                        {{-- Account block status --}}
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-zinc-400">{{ __('Account') }}</span>
                            @if ($isAccountBlocked)
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-red-400">{{ $accountBlockLabel }}</span>
                                    <button wire:click="unblockAccount"
                                        class="text-[10px] uppercase tracking-wider font-semibold text-green-400 hover:text-green-300 transition">
                                        {{ __('Deblokkeren') }}
                                    </button>
                                </div>
                            @else
                                <button wire:click="$set('showBlockForm', true); $set('blockType', 'account')"
                                    x-on:click="$wire.set('showBlockForm', true); $wire.set('blockType', 'account')"
                                    class="text-[10px] uppercase tracking-wider font-semibold text-red-400 hover:text-red-300 transition">
                                    {{ __('Blokkeren') }}
                                </button>
                            @endif
                        </div>

                        {{-- Comment block status --}}
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-zinc-400">{{ __('Reacties') }}</span>
                            @if ($isCommentBlocked)
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-orange-400">{{ $commentBlockLabel }}</span>
                                    <button wire:click="unblockComments"
                                        class="text-[10px] uppercase tracking-wider font-semibold text-green-400 hover:text-green-300 transition">
                                        {{ __('Deblokkeren') }}
                                    </button>
                                </div>
                            @else
                                <button
                                    x-on:click="$wire.set('showBlockForm', true); $wire.set('blockType', 'comment')"
                                    class="text-[10px] uppercase tracking-wider font-semibold text-orange-400 hover:text-orange-300 transition">
                                    {{ __('Blokkeren') }}
                                </button>
                            @endif
                        </div>

                        {{-- Block form --}}
                        @if ($showBlockForm)
                            <form wire:submit="blockUser" class="space-y-3 pt-2 border-t border-zinc-800">
                                <p class="text-xs text-white font-semibold">
                                    {{ $blockType === 'account' ? __('Account blokkeren') : __('Reacties blokkeren') }}
                                </p>

                                <textarea wire:model="blockReason" rows="2" maxlength="500"
                                    placeholder="{{ __('Reden...') }}"
                                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-sm p-2 text-xs placeholder-zinc-500 focus:border-accent focus:ring-1 focus:ring-accent focus:outline-none resize-none"></textarea>
                                @error('blockReason') <p class="text-red-400 text-[10px]">{{ $message }}</p> @enderror

                                <div class="flex gap-3">
                                    <label class="flex items-center gap-1.5 text-xs text-zinc-300 cursor-pointer">
                                        <input type="radio" wire:model="blockDuration" value="day" class="text-accent focus:ring-accent bg-zinc-800 border-zinc-600">
                                        {{ __('24 uur') }}
                                    </label>
                                    <label class="flex items-center gap-1.5 text-xs text-zinc-300 cursor-pointer">
                                        <input type="radio" wire:model="blockDuration" value="indefinite" class="text-accent focus:ring-accent bg-zinc-800 border-zinc-600">
                                        {{ __('Permanent') }}
                                    </label>
                                </div>

                                <div class="flex gap-2">
                                    <button type="submit"
                                        class="px-3 py-1.5 text-[10px] font-semibold uppercase tracking-wider bg-red-600 text-white transition hover:bg-red-500">
                                        {{ __('Blokkeren') }}
                                    </button>
                                    <button type="button" wire:click="$set('showBlockForm', false)"
                                        class="px-3 py-1.5 text-[10px] font-semibold uppercase tracking-wider border border-zinc-700 text-zinc-400 transition hover:text-white hover:border-zinc-500">
                                        {{ __('Annuleren') }}
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                @endif

                {{-- Close button --}}
                <button @click="show = false"
                    class="w-full mt-4 px-6 py-2 text-sm font-semibold bg-accent text-black uppercase tracking-wider transition hover:brightness-90">
                    {{ __('Sluiten') }}
                </button>
            </div>
        </div>
    </div>
</div>
