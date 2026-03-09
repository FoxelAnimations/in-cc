<div class="bg-black min-h-screen -mt-16 pt-16 text-white">

    {{-- Page Header --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold uppercase text-center tracking-wider mb-6">
            Blog
        </h1>
    </section>

    {{-- Content Blocks (homepage-style blocks for blog page) --}}
    @if ($contentBlocks->isNotEmpty())
        <section class="pb-12 md:pb-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @foreach ($contentBlocks as $block)
                    <div class="@if(!$loop->first) mt-12 md:mt-16 @endif">
                        @if ($block->hasMedia())
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-stretch">
                                <div class="{{ $loop->index % 2 !== 0 ? 'md:order-2' : '' }} flex">
                                    <div class="bg-zinc-900 border border-zinc-800 rounded-sm overflow-hidden w-full">
                                        @if ($block->media_type === 'image' && $block->image_path)
                                            <img src="{{ Storage::url($block->image_path) }}" alt="{{ $block->title }}" class="w-full h-full object-cover aspect-[4/3]">
                                        @elseif ($block->media_type === 'video' && $block->video_path)
                                            <div x-data="{ muted: true }" class="relative cursor-pointer aspect-[4/3]" @click="muted = !muted; $refs.vid{{ $block->id }}.muted = muted">
                                                <video x-ref="vid{{ $block->id }}" autoplay muted loop playsinline class="w-full h-full object-cover">
                                                    <source src="{{ Storage::url($block->video_path) }}" type="video/mp4">
                                                </video>
                                                <div class="absolute bottom-3 right-3 bg-black/60 rounded-full p-2 transition-opacity hover:bg-black/80">
                                                    <template x-if="muted">
                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/></svg>
                                                    </template>
                                                    <template x-if="!muted">
                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072M18.364 5.636a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                                                    </template>
                                                </div>
                                            </div>
                                        @elseif ($block->media_type === 'youtube' && $block->youtube_embed_url)
                                            <div class="aspect-[4/3]">
                                                <iframe src="{{ $block->youtube_embed_url }}" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="{{ $loop->index % 2 !== 0 ? 'md:order-1' : '' }} flex flex-col justify-center">
                                    @if ($block->pre_title)
                                        <p class="text-sm tracking-[0.3em] uppercase text-zinc-400 mb-4">{{ $block->pre_title }}</p>
                                    @endif
                                    @if ($block->title)
                                        <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold uppercase leading-none mb-4">{{ $block->title }}</h2>
                                    @endif
                                    @if ($block->text)
                                        <div class="prose prose-invert prose-lg prose-zinc font-description max-w-none break-words content-block-text">{!! $block->text !!}</div>
                                    @endif
                                    @if ($block->hasButton())
                                        <div class="mt-6">
                                            <a href="{{ $block->button_url }}"
                                               @if($block->button_new_tab) target="_blank" rel="noopener" @endif
                                               class="inline-flex items-center bg-accent text-black px-6 py-3 text-lg font-bold uppercase tracking-wider transition hover:brightness-90">
                                                {{ $block->button_label }}
                                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="text-center max-w-3xl mx-auto">
                                @if ($block->pre_title)
                                    <p class="text-sm tracking-[0.3em] uppercase text-zinc-400 mb-4">{{ $block->pre_title }}</p>
                                @endif
                                @if ($block->title)
                                    <h2 class="text-3xl md:text-4xl lg:text-5xl font-bold uppercase leading-none mb-4">{{ $block->title }}</h2>
                                @endif
                                @if ($block->text)
                                    <div class="prose prose-invert prose-lg prose-zinc font-description max-w-none break-words content-block-text">{!! $block->text !!}</div>
                                @endif
                                @if ($block->hasButton())
                                    <div class="mt-6">
                                        <a href="{{ $block->button_url }}"
                                           @if($block->button_new_tab) target="_blank" rel="noopener" @endif
                                           class="inline-flex items-center bg-accent text-black px-6 py-3 text-lg font-bold uppercase tracking-wider transition hover:brightness-90">
                                            {{ $block->button_label }}
                                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @unless($loop->last)
                            <div class="mt-12 md:mt-16 flex justify-center">
                                <div class="w-24 h-1 rounded-full bg-accent"></div>
                            </div>
                        @endunless
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Blog Posts Grid --}}
    @if ($posts->isNotEmpty())
        @if ($contentBlocks->isNotEmpty())
            <div class="flex justify-center pb-12">
                <div class="w-24 h-1 rounded-full bg-accent"></div>
            </div>
        @endif

        <section class="pb-12 md:pb-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($posts as $post)
                        <a href="{{ route('blog.show', $post->slug) }}"
                           class="group bg-zinc-900 border border-zinc-800 rounded-sm overflow-hidden transition hover:border-zinc-700">
                            {{-- Featured image --}}
                            <div class="aspect-[16/10] overflow-hidden">
                                @if ($post->featured_image)
                                    <img src="{{ Storage::url($post->featured_image) }}"
                                         alt="{{ $post->title }}"
                                         class="w-full h-full object-cover transition group-hover:scale-105 duration-500">
                                @else
                                    <div class="w-full h-full bg-zinc-800 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            {{-- Text --}}
                            <div class="p-5">
                                <h3 class="text-xl font-bold uppercase tracking-wider mb-2 group-hover:text-accent transition">
                                    {{ $post->title }}
                                </h3>
                                @if ($post->excerpt)
                                    <p class="text-zinc-400 text-sm leading-relaxed font-description line-clamp-3">
                                        {{ $post->excerpt }}
                                    </p>
                                @endif
                                <span class="inline-flex items-center mt-4 text-accent text-sm font-bold uppercase tracking-wider">
                                    {{ __('Lees meer') }}
                                    <svg class="w-4 h-4 ml-1 transition group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @elseif ($contentBlocks->isEmpty())
        <section class="py-12 md:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <p class="text-zinc-500 text-lg text-center">{{ __('Binnenkort beschikbaar.') }}</p>
            </div>
        </section>
    @endif
</div>
