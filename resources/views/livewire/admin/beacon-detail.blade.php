<div class="py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-3">
                @if ($beacon->image_path)
                    <img src="{{ Storage::url($beacon->image_path) }}" alt="" class="w-10 h-10 object-cover rounded-sm border border-zinc-700">
                @else
                    <div class="w-10 h-10 rounded-sm border border-zinc-700 bg-zinc-800 flex items-center justify-center">
                        <svg class="w-4 h-4 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                @endif
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-zinc-500">CMS — Beacons</p>
                    <h1 class="text-4xl font-bold uppercase tracking-wider">{{ $beacon->title }}</h1>
                    <p class="mt-1 text-zinc-500 text-sm font-mono">GUID: {{ $beacon->guid }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" wire:click="save"
                    class="inline-flex items-center bg-accent text-black px-6 py-2 text-sm font-semibold tracking-wider uppercase transition hover:brightness-90"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">Save Changes</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
                <a href="{{ route('admin.beacons') }}" class="inline-flex items-center border border-zinc-700 text-zinc-400 px-4 py-2 text-sm font-semibold tracking-wider uppercase transition hover:text-white hover:border-zinc-500">
                    Back
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-sm bg-accent/10 border border-accent/30 px-4 py-3 text-sm text-accent">
                {{ session('status') }}
            </div>
        @endif

        {{-- Tabs --}}
        <div class="flex items-center gap-1 mb-6 border-b border-zinc-800">
            @foreach (['details' => 'Details', 'scans' => 'Scan History', 'qr' => 'QR Code'] as $key => $label)
                <button wire:click="setTab('{{ $key }}')"
                    class="px-4 py-3 text-sm font-semibold uppercase tracking-wider transition border-b-2 {{ $tab === $key ? 'text-accent border-accent' : 'text-zinc-500 border-transparent hover:text-zinc-300' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Details Tab --}}
        @if ($tab === 'details')
            <form wire:submit="save" class="space-y-6">
                {{-- Basic Info --}}
                <div class="rounded-sm bg-zinc-900 border border-zinc-800 p-5 space-y-4">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-400 mb-3">Basic Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-400 mb-1">Title *</label>
                            <input type="text" wire:model="title" class="w-full bg-zinc-800 border border-zinc-700 text-white px-3 py-2 text-sm focus:border-accent focus:ring-accent rounded-sm">
                            @error('title') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-400 mb-1">GUID</label>
                            <input type="text" value="{{ $beacon->guid }}" disabled class="w-full bg-zinc-800/50 border border-zinc-700 text-zinc-500 px-3 py-2 text-sm rounded-sm cursor-not-allowed font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-1">Description</label>
                        <textarea wire:model="description" rows="3" class="w-full bg-zinc-800 border border-zinc-700 text-white px-3 py-2 text-sm focus:border-accent focus:ring-accent rounded-sm"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-400 mb-1">Type</label>
                            <select wire:model="typeId" class="w-full bg-zinc-800 border border-zinc-700 text-white px-3 py-2 text-sm focus:border-accent focus:ring-accent rounded-sm">
                                <option value="">— None —</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-400 mb-1">Amount in circulation</label>
                            <input type="number" wire:model="amount" min="0" class="w-full bg-zinc-800 border border-zinc-700 text-white px-3 py-2 text-sm focus:border-accent focus:ring-accent rounded-sm">
                            @error('amount') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-400 mb-1">Redirect URL</label>
                            <input type="text" wire:model="redirectUrl" class="w-full bg-zinc-800 border border-zinc-700 text-white px-3 py-2 text-sm focus:border-accent focus:ring-accent rounded-sm" placeholder="/ (homepage)">
                        </div>
                    </div>
                </div>

                {{-- Main Image --}}
                <div id="image-section" class="rounded-sm bg-zinc-900 border border-zinc-800 p-5 space-y-4">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-400 mb-3">Main Image</h2>

                    @if ($beacon->image_path)
                        <div class="flex items-start gap-4">
                            <img src="{{ Storage::url($beacon->image_path) }}" alt="{{ $beacon->title }}" class="w-32 h-32 object-cover rounded-sm border border-zinc-700">
                            <button type="button" wire:click="removeImage" wire:confirm="Remove this image?"
                                class="inline-flex items-center px-2 py-1 text-xs font-semibold bg-red-900/30 text-red-400 border border-red-800 rounded-sm transition hover:bg-red-900/50 uppercase tracking-wider">
                                Remove
                            </button>
                        </div>
                    @endif

                    <div>
                        <input type="file" wire:model="image" accept="image/*" class="w-full text-sm text-zinc-400 file:mr-3 file:py-2 file:px-3 file:border-0 file:text-sm file:font-semibold file:bg-zinc-800 file:text-zinc-300 file:cursor-pointer hover:file:bg-zinc-700">
                        @error('image') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Additional Images --}}
                <div class="rounded-sm bg-zinc-900 border border-zinc-800 p-5 space-y-4"
                    x-data="{ editing: false, lightbox: { open: false, src: '', index: 0 }, images: {{ Js::from($beacon->images->map(fn($img) => ['id' => $img->id, 'url' => Storage::url($img->image_path)])) }} }">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-400">Additional Images</h2>
                        <button type="button" @click="editing = !editing"
                            class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-semibold rounded-sm transition uppercase tracking-wider"
                            :class="editing ? 'bg-accent text-black' : 'bg-zinc-800 text-zinc-400 border border-zinc-700 hover:text-white hover:border-zinc-500'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            <span x-text="editing ? 'Done' : 'Edit'"></span>
                        </button>
                    </div>

                    @if ($beacon->images->isNotEmpty())
                        <div class="grid grid-cols-4 sm:grid-cols-6 gap-3">
                            @foreach ($beacon->images as $img)
                                <div class="relative group">
                                    <img src="{{ Storage::url($img->image_path) }}" alt=""
                                        class="w-full aspect-square object-cover rounded-sm border border-zinc-700 cursor-pointer hover:border-zinc-500 transition"
                                        @click="if (!editing) { lightbox.src = '{{ Storage::url($img->image_path) }}'; lightbox.index = {{ $loop->index }}; lightbox.open = true; }">

                                    {{-- Remove button (visible in edit mode) --}}
                                    <button type="button" x-show="editing" x-cloak
                                        wire:click="removeBeaconImage({{ $img->id }})"
                                        wire:confirm="Remove this image?"
                                        class="absolute -top-1.5 -right-1.5 bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-[10px] hover:bg-red-500 z-10">
                                        &times;
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-zinc-600 text-sm">No additional images yet.</p>
                    @endif

                    {{-- Upload new images (visible in edit mode) --}}
                    <div x-show="editing" x-cloak>
                        <input type="file" wire:model="newImages" accept="image/*" multiple
                            class="w-full text-sm text-zinc-400 file:mr-3 file:py-2 file:px-3 file:border-0 file:text-sm file:font-semibold file:bg-zinc-800 file:text-zinc-300 file:cursor-pointer hover:file:bg-zinc-700">
                        <p class="mt-1 text-[10px] text-zinc-600">Select multiple images. Max 4MB each.</p>
                        @error('newImages.*') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Lightbox --}}
                    <template x-teleport="body">
                        <div x-show="lightbox.open" x-transition.opacity
                            class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4"
                            @click.self="lightbox.open = false"
                            @keydown.escape.window="lightbox.open = false"
                            style="display: none;">

                            {{-- Close --}}
                            <button @click="lightbox.open = false" class="absolute top-4 right-4 text-white hover:text-accent transition z-10">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>

                            {{-- Prev --}}
                            <button @click="lightbox.index = (lightbox.index - 1 + images.length) % images.length; lightbox.src = images[lightbox.index].url"
                                x-show="images.length > 1"
                                class="absolute left-4 text-white hover:text-accent transition z-10">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>

                            {{-- Image --}}
                            <img :src="lightbox.src" class="max-w-full max-h-[85vh] object-contain rounded-sm" @click.stop>

                            {{-- Next --}}
                            <button @click="lightbox.index = (lightbox.index + 1) % images.length; lightbox.src = images[lightbox.index].url"
                                x-show="images.length > 1"
                                class="absolute right-4 text-white hover:text-accent transition z-10">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>

                            {{-- Counter --}}
                            <div class="absolute bottom-4 text-zinc-400 text-sm font-mono" x-text="(lightbox.index + 1) + ' / ' + images.length"></div>
                        </div>
                    </template>
                </div>

                {{-- Coordinates --}}
                <div class="rounded-sm bg-zinc-900 border border-zinc-800 p-5 space-y-4">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-400 mb-3">Coordinates</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-400 mb-1">Latitude</label>
                            <input type="text" wire:model="latitude" class="w-full bg-zinc-800 border border-zinc-700 text-white px-3 py-2 text-sm focus:border-accent focus:ring-accent rounded-sm" placeholder="52.3676">
                            @error('latitude') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-400 mb-1">Longitude</label>
                            <input type="text" wire:model="longitude" class="w-full bg-zinc-800 border border-zinc-700 text-white px-3 py-2 text-sm focus:border-accent focus:ring-accent rounded-sm" placeholder="4.9041">
                            @error('longitude') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    @if ($beacon->latitude && $beacon->longitude)
                        {{-- Leaflet pin map --}}
                        <div class="w-full h-48 rounded-sm border border-zinc-700 bg-zinc-800 overflow-hidden"
                            wire:ignore
                            x-data="{
                                map: null,
                                init() {
                                    if (typeof L === 'undefined') return;
                                    this.waitForVisible();
                                },
                                waitForVisible() {
                                    const el = this.$el;
                                    const check = () => {
                                        if (el.offsetWidth > 0 && el.offsetHeight > 0) {
                                            this.initMap();
                                        } else {
                                            requestAnimationFrame(check);
                                        }
                                    };
                                    requestAnimationFrame(check);
                                },
                                initMap() {
                                    if (this.map) return;
                                    const lat = {{ $beacon->latitude }};
                                    const lng = {{ $beacon->longitude }};
                                    this.map = L.map(this.$el, {
                                        zoomControl: false,
                                        attributionControl: false
                                    }).setView([lat, lng], 15);

                                    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                                        subdomains: 'abcd',
                                        maxZoom: 19,
                                        className: 'beacon-map-tiles'
                                    }).addTo(this.map);

                                    L.marker([lat, lng]).addTo(this.map);
                                    this.$nextTick(() => this.map.invalidateSize());
                                }
                            }">
                        </div>
                        <style>.beacon-map-tiles { filter: grayscale(100%) brightness(0.85) contrast(1.1) !important; }</style>

                        {{-- Map links --}}
                        <div class="flex items-center gap-4">
                            <a href="{{ $beacon->google_maps_url }}" target="_blank" rel="noopener"
                                class="inline-flex items-center gap-2 text-sm text-accent hover:underline">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Open in Google Maps
                            </a>
                            <a href="https://www.google.com/maps?layer=c&cbll={{ $beacon->latitude }},{{ $beacon->longitude }}" target="_blank" rel="noopener"
                                class="inline-flex items-center gap-2 text-sm text-accent hover:underline">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Street View
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Status --}}
                <div class="rounded-sm bg-zinc-900 border border-zinc-800 p-5 space-y-4">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-400 mb-3">Status</h2>

                    <div class="flex items-center gap-6">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" wire:model="isOnline" class="rounded-sm border-zinc-700 bg-zinc-800 text-accent focus:ring-accent">
                            <span class="text-zinc-300">Online</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" wire:model="isOutOfAction" class="rounded-sm border-zinc-700 bg-zinc-800 text-accent focus:ring-accent">
                            <span class="text-zinc-300">Out of Action</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-1">Activation Date</label>
                        <div class="flex items-center gap-3">
                            <input type="date" wire:model="activationDate"
                                class="bg-zinc-800 border border-zinc-700 text-white px-3 py-2 text-sm focus:border-accent focus:ring-accent rounded-sm">
                            @if ($activationDate)
                                <button type="button" wire:click="$set('activationDate', null)" class="text-xs text-zinc-500 hover:text-zinc-300 transition">Clear</button>
                            @endif
                        </div>
                        @error('activationDate') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        <p class="mt-1 text-[10px] text-zinc-600">Beacon will not be scannable before this date. Leave empty for immediate activation.</p>
                        @if ($beacon->activation_date && $beacon->isBeforeActivation())
                            <p class="mt-1 text-xs text-yellow-400">Not yet active — activates {{ $beacon->activation_date->format('d M Y') }}.</p>
                        @endif
                    </div>
                </div>

                {{-- Collectible / Badge --}}
                <div class="rounded-sm bg-zinc-900 border border-zinc-800 p-5 space-y-4">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-400 mb-3">Collectible Reward</h2>

                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" wire:model.live="isCollectible" class="rounded-sm border-zinc-700 bg-zinc-800 text-accent focus:ring-accent">
                        <span class="text-zinc-300">Collectible</span>
                    </label>
                    <p class="text-xs text-zinc-600">When enabled, logged-in users who scan this beacon will collect it as a badge on their dashboard.</p>

                    @if ($isCollectible)
                        <div class="pt-2 space-y-3">
                            <label class="block text-sm font-medium text-zinc-400 mb-1">Badge Image</label>

                            @if ($beacon->badge_image_path)
                                <div class="flex items-start gap-4">
                                    <img src="{{ Storage::url($beacon->badge_image_path) }}" alt="Badge" class="w-24 h-24 object-contain rounded-sm border border-zinc-700 bg-zinc-800 p-1">
                                    <button type="button" wire:click="removeBadgeImage" wire:confirm="Remove badge image?"
                                        class="inline-flex items-center px-2 py-1 text-xs font-semibold bg-red-900/30 text-red-400 border border-red-800 rounded-sm transition hover:bg-red-900/50 uppercase tracking-wider">
                                        Remove
                                    </button>
                                </div>
                            @endif

                            <input type="file" wire:model="badgeImage" accept="image/*"
                                class="w-full text-sm text-zinc-400 file:mr-3 file:py-2 file:px-3 file:border-0 file:text-sm file:font-semibold file:bg-zinc-800 file:text-zinc-300 file:cursor-pointer hover:file:bg-zinc-700">
                            <p class="text-[10px] text-zinc-600">Square image recommended (e.g. 512x512). Max 4MB.</p>
                            @error('badgeImage') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
                        </div>
                    @endif
                </div>

                {{-- Linked Badges --}}
                <div class="rounded-sm bg-zinc-900 border border-zinc-800 p-5 space-y-4">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-400 mb-3">Linked Badges</h2>
                    <p class="text-xs text-zinc-600">Badges that users earn when scanning this beacon.</p>
                    <div class="max-h-48 overflow-y-auto bg-zinc-800 border border-zinc-700 rounded-sm p-2 space-y-1">
                        @forelse ($allBadges as $badge)
                            <label class="flex items-center gap-2 py-1 px-2 hover:bg-zinc-700/50 rounded-sm cursor-pointer">
                                <input type="checkbox" wire:model="selectedBadgeIds" value="{{ $badge->id }}"
                                    class="rounded-sm bg-zinc-700 border-zinc-600 text-accent focus:ring-accent">
                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                    @if ($badge->image_path)
                                        <img src="{{ Storage::url($badge->image_path) }}" class="w-6 h-6 rounded-full object-cover flex-shrink-0">
                                    @endif
                                    <span class="text-sm text-zinc-300 truncate">{{ $badge->title }}</span>
                                    @if (!$badge->is_active)
                                        <span class="text-xs text-zinc-500">(inactive)</span>
                                    @endif
                                </div>
                            </label>
                        @empty
                            <p class="text-xs text-zinc-600 p-2">No badges created yet.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Linked Locations --}}
                <div class="rounded-sm bg-zinc-900 border border-zinc-800 p-5 space-y-4">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-400 mb-3">Linked Locations</h2>
                    <p class="text-xs text-zinc-600">Locations revealed when a user scans this beacon.</p>
                    <div class="max-h-48 overflow-y-auto bg-zinc-800 border border-zinc-700 rounded-sm p-2 space-y-1">
                        @forelse ($allLocations as $loc)
                            <label class="flex items-center gap-2 py-1 px-2 hover:bg-zinc-700/50 rounded-sm cursor-pointer">
                                <input type="checkbox" wire:model="selectedLocationIds" value="{{ $loc->id }}"
                                    class="rounded-sm bg-zinc-700 border-zinc-600 text-accent focus:ring-accent">
                                <span class="text-sm text-zinc-300">{{ $loc->title }}</span>
                                @if (!$loc->is_visible)
                                    <span class="text-xs text-zinc-500">(hidden)</span>
                                @endif
                            </label>
                        @empty
                            <p class="text-xs text-zinc-600 p-2">No locations created yet.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Out of Action Configuration --}}
                <div class="rounded-sm bg-zinc-900 border border-zinc-800 p-5 space-y-4">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-400 mb-3">Out of Action Behavior</h2>

                    <div>
                        <label class="block text-sm font-medium text-zinc-400 mb-1">Mode</label>
                        <select wire:model.live="outOfActionMode" class="w-full bg-zinc-800 border border-zinc-700 text-white px-3 py-2 text-sm focus:border-accent focus:ring-accent rounded-sm">
                            @foreach ($outOfActionModes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if ($outOfActionMode === 'redirectCustom')
                        <div>
                            <label class="block text-sm font-medium text-zinc-400 mb-1">Custom Redirect URL</label>
                            <input type="text" wire:model="outOfActionRedirectUrl" class="w-full bg-zinc-800 border border-zinc-700 text-white px-3 py-2 text-sm focus:border-accent focus:ring-accent rounded-sm" placeholder="/maintenance">
                        </div>
                    @endif

                    @if (in_array($outOfActionMode, ['showPage', 'block']))
                        <div>
                            <label class="block text-sm font-medium text-zinc-400 mb-1">Custom Message</label>
                            <textarea wire:model="outOfActionMessage" rows="3" class="w-full bg-zinc-800 border border-zinc-700 text-white px-3 py-2 text-sm focus:border-accent focus:ring-accent rounded-sm" placeholder="This beacon is currently out of service."></textarea>
                        </div>
                    @endif
                </div>

            </form>
        @endif

        {{-- Scan History Tab --}}
        @if ($tab === 'scans')
            <div class="space-y-4">
                {{-- Beacon Image --}}
                <div class="flex justify-center">
                    @if ($beacon->image_path)
                        <img src="{{ Storage::url($beacon->image_path) }}" alt="{{ $beacon->title }}" class="w-14 h-14 object-cover rounded-sm border border-zinc-700">
                    @else
                        <div class="w-14 h-14 rounded-sm border border-zinc-700 bg-zinc-800 flex items-center justify-center">
                            <svg class="w-6 h-6 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between">
                    <p class="text-sm text-zinc-500">{{ $beacon->scans()->count() }} total scans</p>
                    <button wire:click="clearScans"
                        wire:confirm="Clear ALL scan logs for this beacon? This cannot be undone."
                        class="inline-flex items-center px-3 py-1.5 text-xs font-semibold bg-red-900/30 text-red-400 border border-red-800 rounded-sm transition hover:bg-red-900/50 uppercase tracking-wider">
                        Clear All Logs
                    </button>
                </div>

                {{-- Scans Table --}}
                <div class="rounded-sm bg-zinc-900 border border-zinc-800 overflow-hidden">
                    @if (isset($scans) && $scans->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-zinc-800">
                                <thead>
                                    <tr class="text-xs uppercase tracking-wider text-zinc-500">
                                        <th class="px-4 py-3 text-left w-6"></th>
                                        <th class="px-4 py-3 text-left">Timestamp</th>
                                        <th class="px-4 py-3 text-left">Source</th>
                                        <th class="px-4 py-3 text-left">Device</th>
                                        <th class="px-4 py-3 text-center">Type</th>
                                        <th class="px-4 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-800" x-data="{ expanded: null }">
                                    @foreach ($scans as $scan)
                                        <tr @click="expanded = expanded === {{ $scan->id }} ? null : {{ $scan->id }}"
                                            class="hover:bg-zinc-800/50 transition text-sm cursor-pointer"
                                            :class="expanded === {{ $scan->id }} && 'bg-zinc-800/50'">
                                            <td class="px-4 py-3 text-zinc-500">
                                                <svg class="w-4 h-4 transition-transform" :class="expanded === {{ $scan->id }} && 'rotate-90'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </td>
                                            <td class="px-4 py-3 text-zinc-300 whitespace-nowrap">{{ $scan->scanned_at->format('Y-m-d H:i:s') }}</td>
                                            <td class="px-4 py-3 text-zinc-400">
                                                @if ($scan->referrer && str_contains($scan->referrer, '/admin'))
                                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-sm bg-purple-900/30 text-purple-400 border border-purple-800">Admin Scan</span>
                                                @elseif ($scan->referrer)
                                                    <span class="max-w-[150px] truncate block" title="{{ $scan->referrer }}">{{ $scan->referrer }}</span>
                                                @else
                                                    <span class="text-zinc-600">Direct</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-zinc-400">
                                                @php $deviceType = $scan->device_type; @endphp
                                                <span class="inline-flex items-center gap-1 text-xs">
                                                    @if ($deviceType === 'mobile')
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                    @elseif ($deviceType === 'tablet')
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                    @else
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                    @endif
                                                    {{ ucfirst($deviceType) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-sm {{ $scan->is_known ? 'bg-green-900/30 text-green-400 border border-green-800' : 'bg-zinc-800 text-zinc-500 border border-zinc-700' }}">
                                                    {{ $scan->is_known ? 'Known' : 'Unknown' }}
                                                </span>
                                                @if ($scan->rate_limited)
                                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-sm bg-orange-900/30 text-orange-400 border border-orange-800 ml-1">
                                                        RL
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-right" @click.stop>
                                                <button wire:click="deleteScan({{ $scan->id }})"
                                                    wire:confirm="Delete this scan log entry?"
                                                    class="inline-flex items-center px-2 py-1 text-xs font-semibold bg-red-900/30 text-red-400 border border-red-800 rounded-sm transition hover:bg-red-900/50">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                        {{-- Expanded details row --}}
                                        <tr x-show="expanded === {{ $scan->id }}" x-collapse x-cloak>
                                            <td colspan="6" class="px-4 py-0">
                                                <div class="py-4 pl-10 grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3 text-sm border-l-2 border-accent/30 ml-1">
                                                    <div>
                                                        <span class="text-xs uppercase tracking-wider text-zinc-500 block mb-0.5">Hashed IP</span>
                                                        <code class="text-xs text-zinc-400 font-mono">{{ $scan->hashed_ip }}</code>
                                                    </div>
                                                    <div>
                                                        <span class="text-xs uppercase tracking-wider text-zinc-500 block mb-0.5">User Agent</span>
                                                        <span class="text-zinc-400 text-xs break-all">{{ $scan->user_agent ?: '—' }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-xs uppercase tracking-wider text-zinc-500 block mb-0.5">Source</span>
                                                        @if ($scan->referrer && str_contains($scan->referrer, '/admin'))
                                                            <span class="text-purple-400 text-xs">Admin Scan</span>
                                                            <span class="text-zinc-600 text-xs block">{{ $scan->referrer }}</span>
                                                        @else
                                                            <span class="text-zinc-400 text-xs break-all">{{ $scan->referrer ?: 'Direct (no referrer)' }}</span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <span class="text-xs uppercase tracking-wider text-zinc-500 block mb-0.5">Requested URL</span>
                                                        <span class="text-zinc-400 text-xs break-all">{{ $scan->requested_url ?: '—' }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-xs uppercase tracking-wider text-zinc-500 block mb-0.5">Redirect Used</span>
                                                        <span class="text-zinc-400 text-xs break-all">{{ $scan->redirect_url_used ?: '—' }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-xs uppercase tracking-wider text-zinc-500 block mb-0.5">Location</span>
                                                        @if ($scan->recorded_location)
                                                            @if ($scan->recorded_location_map_url)
                                                                <a href="{{ $scan->recorded_location_map_url }}" target="_blank" class="text-accent hover:underline text-xs">{{ $scan->recorded_location }}</a>
                                                            @else
                                                                <span class="text-zinc-400 text-xs">{{ $scan->recorded_location }}</span>
                                                            @endif
                                                        @else
                                                            <span class="text-zinc-600 text-xs">—</span>
                                                        @endif
                                                    </div>
                                                    @if ($scan->utm_source || $scan->utm_medium || $scan->utm_campaign)
                                                        <div class="md:col-span-2">
                                                            <span class="text-xs uppercase tracking-wider text-zinc-500 block mb-0.5">UTM Parameters</span>
                                                            <div class="flex flex-wrap gap-2">
                                                                @if ($scan->utm_source)
                                                                    <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-sm bg-zinc-800 text-zinc-400 border border-zinc-700">source: {{ $scan->utm_source }}</span>
                                                                @endif
                                                                @if ($scan->utm_medium)
                                                                    <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-sm bg-zinc-800 text-zinc-400 border border-zinc-700">medium: {{ $scan->utm_medium }}</span>
                                                                @endif
                                                                @if ($scan->utm_campaign)
                                                                    <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-sm bg-zinc-800 text-zinc-400 border border-zinc-700">campaign: {{ $scan->utm_campaign }}</span>
                                                                @endif
                                                                @if ($scan->utm_term)
                                                                    <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-sm bg-zinc-800 text-zinc-400 border border-zinc-700">term: {{ $scan->utm_term }}</span>
                                                                @endif
                                                                @if ($scan->utm_content)
                                                                    <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-sm bg-zinc-800 text-zinc-400 border border-zinc-700">content: {{ $scan->utm_content }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if ($scan->meta_json)
                                                        <div class="md:col-span-2">
                                                            <span class="text-xs uppercase tracking-wider text-zinc-500 block mb-0.5">Extra Query Params</span>
                                                            <code class="text-xs text-zinc-400 font-mono break-all">{{ json_encode($scan->meta_json) }}</code>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="px-4 py-3 border-t border-zinc-800">
                            {{ $scans->links() }}
                        </div>
                    @else
                        <div class="p-8 text-center text-zinc-600">
                            No scan logs yet.
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- QR Code Tab --}}
        @if ($tab === 'qr')
            <div class="space-y-6">
                <div class="rounded-sm bg-zinc-900 border border-zinc-800 p-6">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-zinc-400 mb-4">QR Code Preview</h2>
                    <p class="text-sm text-zinc-500 mb-4">Scan URL: <code class="font-mono text-accent">{{ $beacon->public_url }}</code></p>

                    <div class="flex flex-col items-center gap-4"
                        x-data="{
                            url: '{{ $beacon->public_url }}',
                            init() {
                                this.renderQR();
                            },
                            renderQR() {
                                const canvas = this.$refs.qrCanvas;
                                this.generateQR(canvas, this.url);
                            },
                            generateQR(canvas, text) {
                                // Simple QR code rendering using canvas
                                // Uses the qr-code-styling approach via a minimal encoder
                                const size = 300;
                                canvas.width = size;
                                canvas.height = size;
                                const ctx = canvas.getContext('2d');
                                ctx.fillStyle = '#ffffff';
                                ctx.fillRect(0, 0, size, size);

                                // Load QR via external library
                                const img = new Image();
                                img.crossOrigin = 'anonymous';
                                img.src = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' + encodeURIComponent(text) + '&format=svg';
                                img.onload = () => {
                                    ctx.drawImage(img, 0, 0, size, size);
                                };
                            },
                            downloadPNG() {
                                const canvas = this.$refs.qrCanvas;
                                const link = document.createElement('a');
                                link.download = 'beacon-qr-{{ $beacon->guid }}.png';
                                link.href = canvas.toDataURL('image/png');
                                link.click();
                            },
                            downloadSVG() {
                                const url = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' + encodeURIComponent(this.url) + '&format=svg';
                                const link = document.createElement('a');
                                link.download = 'beacon-qr-{{ $beacon->guid }}.svg';
                                link.href = url;
                                link.click();
                            }
                        }"
                    >
                        <div class="bg-white p-4 rounded-sm">
                            <canvas x-ref="qrCanvas" width="300" height="300"></canvas>
                        </div>

                        <div class="flex items-center gap-3">
                            <button @click="downloadPNG()" class="inline-flex items-center bg-accent text-black px-4 py-2 text-sm font-semibold tracking-wider uppercase transition hover:brightness-90">
                                Download PNG
                            </button>
                            <button @click="downloadSVG()" class="inline-flex items-center border border-zinc-700 text-zinc-400 px-4 py-2 text-sm font-semibold tracking-wider uppercase transition hover:text-white hover:border-zinc-500">
                                Download SVG
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
