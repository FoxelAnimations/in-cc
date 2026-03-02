<div class="min-h-screen -mt-16 pt-16 text-white"
     x-data="cameraFeed()"
     x-init="init()"
     :style="{ backgroundColor: skyColor, transition: 'background-color 60s linear' }">
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold uppercase text-center tracking-wider mb-10">
            Camera's
        </h1>

        @if ($cameras->isEmpty())
            <p class="text-center text-zinc-600 text-lg">{{ __("Geen camera's beschikbaar.") }}</p>
        @else
            <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($cameras as $camera)
                    <div class="border border-zinc-800 bg-zinc-900/50 rounded-sm overflow-hidden cursor-pointer backdrop-blur-sm"
                        data-camera-id="{{ $camera->id }}"
                        @click="openPopup({{ $camera->id }}, '{{ $camera->name }}')">

                        {{-- Camera Header --}}
                        <div class="flex items-center justify-between px-3 py-2 bg-zinc-800/80">
                            <span class="text-[11px] uppercase tracking-wider font-semibold text-zinc-300 truncate">
                                {{ $camera->name }}
                            </span>
                            <span class="flex items-center gap-1.5 shrink-0"
                                :class="getCameraStatus({{ $camera->id }}) === 'online' ? 'text-green-400' : 'text-red-400'">
                                <span class="w-2 h-2 rounded-full"
                                    :class="getCameraStatus({{ $camera->id }}) === 'online' ? 'bg-green-400 animate-pulse' : 'bg-red-500'"></span>
                                <span class="text-[10px] uppercase tracking-wider font-bold"
                                    x-text="getCameraStatus({{ $camera->id }}) === 'online' ? 'LIVE' : 'OFFLINE'"></span>
                            </span>
                        </div>

                        {{-- Video Area --}}
                        <div class="aspect-video relative"
                            :style="{ backgroundColor: skyColor, transition: 'background-color 60s linear' }">
                            <video
                                x-ref="cam{{ $camera->id }}"
                                autoplay muted loop playsinline
                                class="w-full h-full object-cover relative z-[1]"
                                x-show="getCameraStatus({{ $camera->id }}) === 'online' && getCameraVideoUrl({{ $camera->id }})"
                                style="display: none;"
                            ></video>

                            {{-- Color overlay --}}
                            <div class="absolute inset-0 z-[2] pointer-events-none"
                                :style="{ backgroundColor: overlayColor, transition: 'background-color 60s linear' }"></div>

                            {{-- Offline overlay --}}
                            <div x-show="getCameraStatus({{ $camera->id }}) !== 'online' || !getCameraVideoUrl({{ $camera->id }})"
                                class="w-full h-full flex flex-col items-center justify-center bg-zinc-900/80 absolute inset-0 z-[3]">
                                <svg class="w-10 h-10 text-zinc-700 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    <line x1="3" y1="21" x2="21" y2="3" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                                <p class="text-zinc-500 text-xs uppercase tracking-wider font-semibold">Camera offline</p>
                                <p class="text-zinc-600 text-[10px] uppercase tracking-wider">Geen signaal</p>
                            </div>

                            {{-- Scanline overlay --}}
                            <div class="absolute inset-0 pointer-events-none opacity-[0.07] z-[4]"
                                style="background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255,255,255,0.05) 2px, rgba(255,255,255,0.05) 4px);"></div>

                            {{-- Timestamp overlay --}}
                            <div class="absolute bottom-1.5 right-2 text-[9px] font-mono text-white/40 tracking-wider z-[5]"
                                x-text="getCurrentTimestamp()"></div>

                            {{-- REC indicator --}}
                            <div class="absolute top-2 left-2 flex items-center gap-1 z-[5]"
                                x-show="getCameraStatus({{ $camera->id }}) === 'online'">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                <span class="text-[9px] font-bold text-red-500/80 tracking-wider">REC</span>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Popup Modal --}}
                <template x-teleport="body">
                    <div x-show="popup.open" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4"
                        @keydown.escape.window="closePopup()" style="display: none;">
                        <div class="absolute inset-0" @click="closePopup()"></div>

                        <div class="relative w-full max-w-4xl" @click.stop>
                            {{-- Header --}}
                            <div class="flex items-center justify-between bg-zinc-900 border border-zinc-800 border-b-0 px-4 py-3 rounded-t-sm">
                                <div class="flex items-center gap-3">
                                    <span class="text-sm uppercase tracking-wider font-semibold text-white" x-text="popup.name"></span>
                                    <span class="flex items-center gap-1.5"
                                        :class="popup.id && getCameraStatus(popup.id) === 'online' ? 'text-green-400' : 'text-red-400'">
                                        <span class="w-2 h-2 rounded-full"
                                            :class="popup.id && getCameraStatus(popup.id) === 'online' ? 'bg-green-400 animate-pulse' : 'bg-red-500'"></span>
                                        <span class="text-[10px] uppercase tracking-wider font-bold"
                                            x-text="popup.id && getCameraStatus(popup.id) === 'online' ? 'LIVE' : 'OFFLINE'"></span>
                                    </span>
                                </div>
                                <div class="flex items-center gap-3">
                                    {{-- Audio toggle --}}
                                    <button @click="toggleMute()" class="text-zinc-400 hover:text-white transition" title="Audio aan/uit">
                                        {{-- Unmuted icon --}}
                                        <svg x-show="!popup.muted" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M17.95 6.05a8 8 0 010 11.9M11 5L6 9H2v6h4l5 4V5z"/>
                                        </svg>
                                        {{-- Muted icon --}}
                                        <svg x-show="popup.muted" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/>
                                        </svg>
                                    </button>
                                    {{-- Close --}}
                                    <button @click="closePopup()" class="text-zinc-400 hover:text-white transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Video --}}
                            <div class="aspect-video relative border border-zinc-800 border-t-0 rounded-b-sm overflow-hidden"
                                :style="{ backgroundColor: skyColor, transition: 'background-color 60s linear' }">
                                <video
                                    x-ref="popupVideo"
                                    autoplay loop playsinline
                                    :muted="popup.muted"
                                    class="w-full h-full object-contain relative z-[1]"
                                    x-show="popup.id && getCameraStatus(popup.id) === 'online' && getCameraVideoUrl(popup.id)"
                                    style="display: none;"
                                ></video>

                                {{-- Color overlay --}}
                                <div class="absolute inset-0 z-[2] pointer-events-none"
                                    :style="{ backgroundColor: overlayColor, transition: 'background-color 60s linear' }"></div>

                                {{-- Offline --}}
                                <div x-show="!popup.id || getCameraStatus(popup.id) !== 'online' || !getCameraVideoUrl(popup.id)"
                                    class="w-full h-full flex flex-col items-center justify-center bg-zinc-900/80 absolute inset-0 z-[3]">
                                    <svg class="w-16 h-16 text-zinc-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        <line x1="3" y1="21" x2="21" y2="3" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                    <p class="text-zinc-500 text-sm uppercase tracking-wider font-semibold">Camera offline</p>
                                    <p class="text-zinc-600 text-xs uppercase tracking-wider">Geen signaal</p>
                                </div>

                                {{-- Scanline overlay --}}
                                <div class="absolute inset-0 pointer-events-none opacity-[0.05] z-[4]"
                                    style="background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255,255,255,0.05) 2px, rgba(255,255,255,0.05) 4px);"></div>

                                {{-- Timestamp --}}
                                <div class="absolute bottom-2 right-3 text-[10px] font-mono text-white/40 tracking-wider z-[5]"
                                    x-text="getCurrentTimestamp()"></div>

                                {{-- REC --}}
                                <div class="absolute top-3 left-3 flex items-center gap-1.5 z-[5]"
                                    x-show="popup.id && getCameraStatus(popup.id) === 'online'">
                                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                                    <span class="text-[10px] font-bold text-red-500/80 tracking-wider">REC</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        @endif
    </section>
</div>

@script
<script>
Alpine.data('cameraFeed', () => ({
    cameras: {},
    timers: {},
    clockTimer: null,
    skyTimer: null,
    timestampText: '',
    popup: { open: false, id: null, name: '', muted: true },

    // Sky color system
    skyColor: '#0B1026',
    overlayColor: 'rgba(0,0,0,0)',
    slotKeyframes: [],

    async init() {
        this.updateTimestamp();
        this.clockTimer = setInterval(() => this.updateTimestamp(), 1000);
        await this.loadSchedule();
        this.skyTimer = setInterval(() => this.updateSkyColor(), 60000);
    },

    async loadSchedule() {
        try {
            const res = await fetch('/api/cameras/schedule');
            if (!res.ok) throw new Error('API returned ' + res.status);
            const data = await res.json();

            // Process slot data for sky colors
            if (data.slots) {
                this.slotKeyframes = this.buildKeyframes(data.slots);
                this.updateSkyColor();
            }

            data.cameras.forEach(cam => {
                const prev = this.cameras[cam.id];
                const videoChanged = !prev || prev.video_url !== cam.video_url;

                this.cameras[cam.id] = { ...cam };

                // Update grid video element if source changed
                if (videoChanged && cam.status === 'online' && cam.video_url) {
                    this.$nextTick(() => {
                        const videoEl = this.$el.querySelector('[data-camera-id="' + cam.id + '"] video');
                        if (videoEl) {
                            videoEl.src = cam.video_url;
                            videoEl.load();
                            videoEl.play().catch(() => {});
                        }

                        // Also update popup if it's showing this camera
                        if (this.popup.open && this.popup.id === cam.id) {
                            this.loadPopupVideo();
                        }
                    });
                }

                // Schedule next check
                if (this.timers[cam.id]) clearTimeout(this.timers[cam.id]);
                const checkIn = Math.max(10, cam.next_check_seconds) * 1000;
                this.timers[cam.id] = setTimeout(() => this.loadSchedule(), checkIn);
            });
        } catch (e) {
            setTimeout(() => this.loadSchedule(), 30000);
        }
    },

    /**
     * Build sorted keyframe array from slot data.
     * Each keyframe sits at the midpoint of its slot.
     */
    buildKeyframes(slots) {
        const keyframes = [];
        for (const [key, slot] of Object.entries(slots)) {
            const startMin = this.timeToMin(slot.start);
            const endMin = slot.end === '24:00' ? 1440 : this.timeToMin(slot.end);
            const midpoint = (startMin + endMin) / 2;
            keyframes.push({
                minutes: midpoint,
                bgColor: slot.bg_color || '#000000',
                overlayColor: slot.overlay_color || '#00000000',
            });
        }
        keyframes.sort((a, b) => a.minutes - b.minutes);
        return keyframes;
    },

    timeToMin(t) {
        const [h, m] = t.split(':').map(Number);
        return h * 60 + m;
    },

    /**
     * Calculate current interpolated sky + overlay color based on Brussels time.
     */
    updateSkyColor() {
        if (this.slotKeyframes.length === 0) return;

        const now = new Date();
        const brusselsStr = now.toLocaleString('en-GB', {
            timeZone: 'Europe/Brussels',
            hour: '2-digit', minute: '2-digit', hour12: false,
        });
        const [h, m] = brusselsStr.split(':').map(Number);
        const currentMin = h * 60 + m;

        const kf = this.slotKeyframes;
        const totalDay = 1440;

        let prevKf, nextKf, t;
        let nextIdx = kf.findIndex(k => k.minutes > currentMin);

        if (nextIdx === -1) {
            // After all keyframes -> between last and first(+1440)
            prevKf = kf[kf.length - 1];
            nextKf = kf[0];
            const gap = (nextKf.minutes + totalDay) - prevKf.minutes;
            const elapsed = currentMin - prevKf.minutes;
            t = gap > 0 ? elapsed / gap : 0;
        } else if (nextIdx === 0) {
            // Before all keyframes -> between last(-1440) and first
            prevKf = kf[kf.length - 1];
            nextKf = kf[0];
            const gap = (nextKf.minutes + totalDay) - prevKf.minutes;
            const elapsed = (currentMin + totalDay) - prevKf.minutes;
            t = gap > 0 ? elapsed / gap : 0;
        } else {
            prevKf = kf[nextIdx - 1];
            nextKf = kf[nextIdx];
            const gap = nextKf.minutes - prevKf.minutes;
            const elapsed = currentMin - prevKf.minutes;
            t = gap > 0 ? elapsed / gap : 0;
        }

        t = Math.max(0, Math.min(1, t));

        this.skyColor = this.lerpHex(prevKf.bgColor, nextKf.bgColor, t);
        this.overlayColor = this.lerpHexAlpha(prevKf.overlayColor, nextKf.overlayColor, t);
    },

    /**
     * Linearly interpolate between two 6-char hex colors (#RRGGBB).
     */
    lerpHex(c1, c2, t) {
        const r1 = parseInt(c1.slice(1, 3), 16), g1 = parseInt(c1.slice(3, 5), 16), b1 = parseInt(c1.slice(5, 7), 16);
        const r2 = parseInt(c2.slice(1, 3), 16), g2 = parseInt(c2.slice(3, 5), 16), b2 = parseInt(c2.slice(5, 7), 16);
        const r = Math.round(r1 + (r2 - r1) * t);
        const g = Math.round(g1 + (g2 - g1) * t);
        const b = Math.round(b1 + (b2 - b1) * t);
        return '#' + [r, g, b].map(v => v.toString(16).padStart(2, '0')).join('');
    },

    /**
     * Linearly interpolate between two hex colors with alpha (#RRGGBBAA).
     * Returns rgba() string for CSS.
     */
    lerpHexAlpha(c1, c2, t) {
        const r1 = parseInt(c1.slice(1, 3), 16), g1 = parseInt(c1.slice(3, 5), 16), b1 = parseInt(c1.slice(5, 7), 16);
        const a1 = c1.length > 7 ? parseInt(c1.slice(7, 9), 16) / 255 : 1;
        const r2 = parseInt(c2.slice(1, 3), 16), g2 = parseInt(c2.slice(3, 5), 16), b2 = parseInt(c2.slice(5, 7), 16);
        const a2 = c2.length > 7 ? parseInt(c2.slice(7, 9), 16) / 255 : 1;
        const r = Math.round(r1 + (r2 - r1) * t);
        const g = Math.round(g1 + (g2 - g1) * t);
        const b = Math.round(b1 + (b2 - b1) * t);
        const a = (a1 + (a2 - a1) * t).toFixed(3);
        return `rgba(${r}, ${g}, ${b}, ${a})`;
    },

    getCameraStatus(id) {
        return this.cameras[id]?.status ?? 'loading';
    },

    getCameraVideoUrl(id) {
        return this.cameras[id]?.video_url ?? '';
    },

    openPopup(id, name) {
        this.popup.open = true;
        this.popup.id = id;
        this.popup.name = name;
        this.popup.muted = true;

        this.$nextTick(() => this.loadPopupVideo());
    },

    closePopup() {
        const videoEl = this.$refs.popupVideo;
        if (videoEl) {
            videoEl.pause();
            videoEl.removeAttribute('src');
            videoEl.load();
        }
        this.popup.open = false;
        this.popup.id = null;
    },

    loadPopupVideo() {
        const url = this.getCameraVideoUrl(this.popup.id);
        const videoEl = this.$refs.popupVideo;
        if (videoEl && url) {
            videoEl.src = url;
            videoEl.muted = this.popup.muted;
            videoEl.load();
            videoEl.play().catch(() => {});
        }
    },

    toggleMute() {
        this.popup.muted = !this.popup.muted;
        const videoEl = this.$refs.popupVideo;
        if (videoEl) {
            videoEl.muted = this.popup.muted;
        }
    },

    updateTimestamp() {
        const now = new Date();
        this.timestampText = now.toLocaleString('nl-BE', {
            timeZone: 'Europe/Brussels',
            day: '2-digit', month: '2-digit', year: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit',
        });
    },

    getCurrentTimestamp() {
        return this.timestampText;
    },

    destroy() {
        Object.values(this.timers).forEach(t => clearTimeout(t));
        if (this.clockTimer) clearInterval(this.clockTimer);
        if (this.skyTimer) clearInterval(this.skyTimer);
    },
}));
</script>
@endscript
