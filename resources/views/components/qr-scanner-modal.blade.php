@props(['redirectUrl' => '/beacon', 'readerId' => 'qr-reader'])

<div x-data="{
    show: false,
    scanning: false,
    error: null,
    result: null,
    scanner: null,

    async openScanner() {
        this.show = true;
        this.error = null;
        this.result = null;
        await this.$nextTick();
        // Lazy-load html5-qrcode on first use
        if (typeof Html5Qrcode === 'undefined') {
            await new Promise((resolve, reject) => {
                const s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js';
                s.onload = resolve;
                s.onerror = () => { this.error = '{{ __('Scanner kon niet geladen worden.') }}'; reject(); };
                document.head.appendChild(s);
            });
        }
        setTimeout(() => this.startCamera(), 150);
    },

    async closeScanner() {
        await this.stopCamera();
        this.show = false;
    },

    async startCamera() {
        try {
            this.scanner = new Html5Qrcode('{{ $readerId }}');
            await this.scanner.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                (decodedText) => this.onScan(decodedText),
                () => {}
            );
            this.scanning = true;
        } catch (err) {
            this.error = '{{ __('Camera niet beschikbaar. Geef toegang tot je camera.') }}';
            this.scanning = false;
        }
    },

    async stopCamera() {
        if (this.scanner) {
            try {
                if (this.scanning) await this.scanner.stop();
                this.scanner.clear();
            } catch (e) {}
        }
        this.scanner = null;
        this.scanning = false;
    },

    onScan(text) {
        this.stopCamera();
        let guid = null;
        try {
            const url = new URL(text);
            const match = url.pathname.match(/\/beacon\/([A-Za-z0-9]+)/);
            if (match) guid = match[1];
        } catch {
            if (/^[A-Za-z0-9]{10}$/.test(text)) guid = text;
        }
        if (guid) {
            this.result = '{{ __('Beacon gevonden! Even geduld…') }}';
            window.location.href = '{{ url($redirectUrl) }}/' + encodeURIComponent(guid);
        } else {
            this.error = '{{ __('Geen geldige beacon QR-code.') }}';
            setTimeout(() => { this.error = null; this.startCamera(); }, 1500);
        }
    }
}" @open-scanner.window="openScanner()" @keydown.escape.window="if (show) closeScanner()"
   x-show="show" x-cloak
   class="fixed inset-0 z-[55] flex items-center justify-center bg-black/80 backdrop-blur-sm">

    <div class="bg-zinc-900 border border-zinc-700 rounded-lg w-full max-w-md mx-4 overflow-hidden" @click.outside="closeScanner()">
        <div class="flex items-center justify-between px-5 py-4 border-b border-zinc-800">
            <h3 class="text-white text-sm font-semibold tracking-widest uppercase">{{ __('Scan Beacon') }}</h3>
            <button @click="closeScanner()" class="text-zinc-400 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-5">
            <div id="{{ $readerId }}" class="w-full rounded overflow-hidden bg-black"></div>

            <p x-show="error" x-text="error" x-cloak class="mt-3 text-red-400 text-sm text-center"></p>
            <p x-show="result" x-text="result" x-cloak class="mt-3 text-accent text-sm text-center font-semibold"></p>
            <p x-show="scanning && !error && !result" x-cloak class="mt-3 text-zinc-400 text-sm text-center">
                {{ __('Richt je camera op een beacon QR-code') }}
            </p>
        </div>
    </div>
</div>
