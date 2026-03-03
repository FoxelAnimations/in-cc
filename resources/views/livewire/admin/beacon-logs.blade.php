<div class="py-10">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <p class="text-sm uppercase tracking-[0.3em] text-zinc-500">CMS — Beacons</p>
                <h1 class="text-4xl font-bold uppercase tracking-wider">Beacon Logs</h1>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.beacons') }}" class="inline-flex items-center border border-zinc-700 text-zinc-400 px-4 py-2 text-sm font-semibold tracking-wider uppercase transition hover:text-white hover:border-zinc-500">
                    Back
                </a>
                <button wire:click="exportCsv"
                    class="inline-flex items-center bg-accent text-black px-4 py-2 text-sm font-semibold tracking-wider uppercase transition hover:brightness-90">
                    Export CSV
                </button>
            </div>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-sm bg-accent/10 border border-accent/30 px-4 py-3 text-sm text-accent">
                {{ session('status') }}
            </div>
        @endif

        {{-- Filters --}}
        <div class="mb-6 flex flex-wrap items-center gap-3">
            <input type="text" wire:model.live.debounce.300ms="filterGuid" placeholder="GUID contains..."
                class="bg-zinc-800 border border-zinc-700 text-white px-3 py-2 text-sm focus:border-accent focus:ring-accent rounded-sm w-48">

            <select wire:model.live="filterKnown" class="bg-zinc-800 border border-zinc-700 text-white px-3 py-2 text-sm focus:border-accent focus:ring-accent rounded-sm">
                <option value="">All (known + unknown)</option>
                <option value="known">Known only</option>
                <option value="unknown">Unknown only</option>
            </select>

            <select wire:model.live="filterTypeId" class="bg-zinc-800 border border-zinc-700 text-white px-3 py-2 text-sm focus:border-accent focus:ring-accent rounded-sm">
                <option value="">All Types</option>
                @foreach ($types as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterStatus" class="bg-zinc-800 border border-zinc-700 text-white px-3 py-2 text-sm focus:border-accent focus:ring-accent rounded-sm">
                <option value="">All Statuses</option>
                <option value="online">Online</option>
                <option value="offline">Offline</option>
                <option value="out_of_action">Out of Action</option>
            </select>

            <input type="date" wire:model.live="filterDateFrom" class="bg-zinc-800 border border-zinc-700 text-white px-3 py-2 text-sm focus:border-accent focus:ring-accent rounded-sm">
            <span class="text-zinc-600">to</span>
            <input type="date" wire:model.live="filterDateTo" class="bg-zinc-800 border border-zinc-700 text-white px-3 py-2 text-sm focus:border-accent focus:ring-accent rounded-sm">
        </div>

        {{-- Table --}}
        <div class="rounded-sm bg-zinc-900 border border-zinc-800 overflow-hidden">
            @if ($scans->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-800">
                        <thead>
                            <tr class="text-xs uppercase tracking-wider text-zinc-500">
                                <th class="px-4 py-3 text-left w-8"></th>
                                <th class="px-4 py-3 text-left">Timestamp</th>
                                <th class="px-4 py-3 text-left">GUID</th>
                                <th class="px-4 py-3 text-left">Beacon</th>
                                <th class="px-4 py-3 text-center">Known</th>
                                <th class="px-4 py-3 text-left">User Agent</th>
                                <th class="px-4 py-3 text-left">Location</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800">
                            @foreach ($scans as $scan)
                                <tr x-data="{ open: false }" class="group">
                                    {{-- Summary row --}}
                                    <td colspan="8" class="p-0">
                                        <div class="flex items-center hover:bg-zinc-800/50 transition text-sm cursor-pointer" @click="open = !open">
                                            <div class="px-4 py-3 w-8 flex-shrink-0">
                                                <svg class="w-4 h-4 text-zinc-500 transition-transform duration-200" :class="open && 'rotate-90'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </div>
                                            <div class="px-4 py-3 text-zinc-300 whitespace-nowrap">{{ $scan->scanned_at?->format('Y-m-d H:i:s') }}</div>
                                            <div class="px-4 py-3"><code class="text-xs text-zinc-500 font-mono">{{ Str::limit($scan->guid, 12) }}</code></div>
                                            <div class="px-4 py-3 text-zinc-300">
                                                @if ($scan->beacon)
                                                    <a href="{{ route('admin.beacon-detail', $scan->beacon) }}" class="hover:text-accent transition" @click.stop>{{ $scan->beacon->title }}</a>
                                                @else
                                                    <span class="text-zinc-600">—</span>
                                                @endif
                                            </div>
                                            <div class="px-4 py-3 text-center">
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-sm {{ $scan->is_known ? 'bg-green-900/30 text-green-400 border border-green-800' : 'bg-zinc-800 text-zinc-500 border border-zinc-700' }}">
                                                    {{ $scan->is_known ? 'Known' : 'Unknown' }}
                                                </span>
                                                @if ($scan->rate_limited)
                                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-sm bg-orange-900/30 text-orange-400 border border-orange-800 ml-1">RL</span>
                                                @endif
                                            </div>
                                            <div class="px-4 py-3 text-zinc-400 max-w-[200px] truncate">{{ Str::limit($scan->user_agent, 40) ?: '—' }}</div>
                                            <div class="px-4 py-3 text-zinc-400 whitespace-nowrap">
                                                @if ($scan->recorded_location)
                                                    {{ $scan->recorded_location }}
                                                @else
                                                    —
                                                @endif
                                            </div>
                                            <div class="px-4 py-3 text-right ml-auto">
                                                <button wire:click="deleteScan({{ $scan->id }})"
                                                    wire:confirm="Delete this scan log entry?"
                                                    @click.stop
                                                    class="inline-flex items-center px-2 py-1 text-xs font-semibold bg-red-900/30 text-red-400 border border-red-800 rounded-sm transition hover:bg-red-900/50">
                                                    Delete
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Expanded detail panel --}}
                                        <div x-show="open" x-collapse x-cloak class="border-t border-zinc-800 bg-zinc-950/50 px-6 py-4">
                                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-3 text-sm">
                                                <div>
                                                    <span class="text-xs uppercase tracking-wider text-zinc-600">GUID</span>
                                                    <p class="text-zinc-300 font-mono text-xs mt-0.5 break-all">{{ $scan->guid }}</p>
                                                </div>
                                                <div>
                                                    <span class="text-xs uppercase tracking-wider text-zinc-600">Hashed IP</span>
                                                    <p class="text-zinc-300 font-mono text-xs mt-0.5 break-all">{{ $scan->hashed_ip }}</p>
                                                </div>
                                                <div>
                                                    <span class="text-xs uppercase tracking-wider text-zinc-600">Device Type</span>
                                                    <p class="text-zinc-300 mt-0.5 capitalize">{{ $scan->device_type }}</p>
                                                </div>
                                                <div class="md:col-span-2 lg:col-span-3">
                                                    <span class="text-xs uppercase tracking-wider text-zinc-600">User Agent</span>
                                                    <p class="text-zinc-300 text-xs mt-0.5 break-all">{{ $scan->user_agent ?: '—' }}</p>
                                                </div>
                                                <div>
                                                    <span class="text-xs uppercase tracking-wider text-zinc-600">Referrer</span>
                                                    <p class="text-zinc-300 text-xs mt-0.5 break-all">{{ $scan->referrer ?: '—' }}</p>
                                                </div>
                                                <div>
                                                    <span class="text-xs uppercase tracking-wider text-zinc-600">Requested URL</span>
                                                    <p class="text-zinc-300 text-xs mt-0.5 break-all">{{ $scan->requested_url ?: '—' }}</p>
                                                </div>
                                                <div>
                                                    <span class="text-xs uppercase tracking-wider text-zinc-600">Redirect URL</span>
                                                    <p class="text-zinc-300 text-xs mt-0.5 break-all">{{ $scan->redirect_url_used ?: '—' }}</p>
                                                </div>
                                                <div>
                                                    <span class="text-xs uppercase tracking-wider text-zinc-600">Location</span>
                                                    <p class="text-zinc-300 mt-0.5">
                                                        @if ($scan->recorded_location)
                                                            {{ $scan->recorded_location }}
                                                            @if ($scan->recorded_location_map_url)
                                                                <a href="{{ $scan->recorded_location_map_url }}" target="_blank" class="text-accent hover:underline ml-1" @click.stop>Open in Maps</a>
                                                            @endif
                                                        @else
                                                            —
                                                        @endif
                                                    </p>
                                                </div>
                                                <div>
                                                    <span class="text-xs uppercase tracking-wider text-zinc-600">UTM Source</span>
                                                    <p class="text-zinc-300 mt-0.5">{{ $scan->utm_source ?: '—' }}</p>
                                                </div>
                                                <div>
                                                    <span class="text-xs uppercase tracking-wider text-zinc-600">UTM Medium</span>
                                                    <p class="text-zinc-300 mt-0.5">{{ $scan->utm_medium ?: '—' }}</p>
                                                </div>
                                                <div>
                                                    <span class="text-xs uppercase tracking-wider text-zinc-600">UTM Campaign</span>
                                                    <p class="text-zinc-300 mt-0.5">{{ $scan->utm_campaign ?: '—' }}</p>
                                                </div>
                                                <div>
                                                    <span class="text-xs uppercase tracking-wider text-zinc-600">UTM Term</span>
                                                    <p class="text-zinc-300 mt-0.5">{{ $scan->utm_term ?: '—' }}</p>
                                                </div>
                                                <div>
                                                    <span class="text-xs uppercase tracking-wider text-zinc-600">UTM Content</span>
                                                    <p class="text-zinc-300 mt-0.5">{{ $scan->utm_content ?: '—' }}</p>
                                                </div>
                                            </div>
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
                    No scan logs found.
                </div>
            @endif
        </div>
    </div>
</div>
