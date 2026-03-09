<div class="bg-black min-h-screen -mt-16 pt-16 text-white">
    <article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">

        {{-- Back link --}}
        <a href="{{ route('blog') }}" class="inline-flex items-center text-zinc-400 hover:text-accent text-sm font-bold uppercase tracking-wider transition mb-8">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/></svg>
            {{ __('Terug naar blog') }}
        </a>

        {{-- Title --}}
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold uppercase tracking-wider leading-none mb-6">
            {{ $post->title }}
        </h1>

        {{-- Featured image --}}
        @if ($post->featured_image)
            <div class="mb-8 rounded-sm overflow-hidden border border-zinc-800">
                <img src="{{ Storage::url($post->featured_image) }}"
                     alt="{{ $post->title }}"
                     class="w-full object-cover max-h-[500px]">
            </div>
        @endif

        {{-- Content --}}
        @if ($post->content)
            <div class="prose prose-invert prose-lg prose-zinc font-description max-w-none content-block-text">
                {!! $post->content !!}
            </div>
        @endif

        {{-- Button --}}
        @if ($post->hasButton())
            <div class="mt-10">
                <a href="{{ $post->button_url }}"
                   @if($post->button_new_tab) target="_blank" rel="noopener" @endif
                   class="inline-flex items-center bg-accent text-black px-6 py-3 text-lg font-bold uppercase tracking-wider transition hover:brightness-90">
                    {{ $post->button_label }}
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        @endif

        {{-- Separator --}}
        <div class="mt-12 md:mt-16 flex justify-center">
            <div class="w-24 h-1 rounded-full bg-accent"></div>
        </div>

        {{-- Back to blog --}}
        <div class="mt-8 text-center">
            <a href="{{ route('blog') }}" class="inline-flex items-center text-zinc-400 hover:text-accent text-sm font-bold uppercase tracking-wider transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/></svg>
                {{ __('Alle artikelen') }}
            </a>
        </div>
    </article>
</div>
