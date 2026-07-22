@section('title', 'Videos | Bonik Point')
@section('meta_description', 'Watch Bonik Point related YouTube videos directly from Daily Vlogs by Nayeem, Nayeem Rahman Vlogs, and Budget Koto.')
@section('canonical', route('videos.index'))

<x-app-layout>
    <section class="bg-[#f3f5f4] py-5 md:py-10">
        <div class="container">
            <div class="mb-5 flex flex-col justify-between gap-4 md:mb-8 md:flex-row md:items-end">
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-primary">Bonik Point Media</p>
                    <h1 class="mt-1 text-3xl font-black uppercase text-ink md:text-4xl">Videos</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-600 md:text-base">Watch videos from your selected YouTube channels without leaving Bonik Point.</p>
                </div>

                <a href="{{ route('shop.index') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-md border border-[#d7e1df] bg-white px-4 text-xs font-black text-ink shadow-sm hover:border-primary hover:text-primary md:h-11 md:px-5 md:text-sm">
                    <i class="fa-solid fa-arrow-left"></i>
                    Back to Shop
                </a>
            </div>

            @if($featuredVideo)
                <div class="mx-auto grid max-w-5xl gap-4 rounded-lg border border-[#dfe7e5] bg-white p-3 shadow-[0_18px_45px_rgba(18,59,62,0.10)] lg:grid-cols-[minmax(0,0.95fr)_300px] lg:p-4">
                    <div class="overflow-hidden rounded-md bg-black">
                        <iframe
                            src="{{ $featuredVideo['embed_url'] }}"
                            title="{{ $featuredVideo['title'] }}"
                            loading="lazy"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen
                            class="aspect-video w-full"
                        ></iframe>
                    </div>

                    <div class="flex flex-col justify-center p-1 lg:p-2">
                        <p class="text-xs font-black uppercase tracking-wide text-primary">Featured Video</p>
                        <h2 class="mt-2 text-xl font-black leading-tight text-ink lg:text-2xl">{{ $featuredVideo['title'] }}</h2>
                        <p class="mt-3 text-sm font-bold text-gray-500">{{ $featuredVideo['channel'] }} {{ $featuredVideo['published_at'] ? '- '.$featuredVideo['published_at'] : '' }}</p>
                        <a href="{{ $featuredVideo['watch_url'] }}" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex h-10 w-fit items-center gap-2 rounded-md bg-ink px-4 text-xs font-black text-white hover:bg-primary">
                            <i class="fa-brands fa-youtube"></i>
                            Open on YouTube
                        </a>
                    </div>
                </div>
            @endif

            <div class="mt-8 space-y-8">
                @foreach($channels as $channel)
                    <section>
                        <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                            <div>
                                <h2 class="text-xl font-black text-ink">{{ $channel['name'] }}</h2>
                                <p class="text-xs font-bold text-gray-500">{{ $channel['handle'] }}</p>
                            </div>

                            <a href="{{ $channel['url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-xs font-black text-primary hover:text-ink">
                                Visit Channel
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                        </div>

                        @if(count($channel['videos']))
                            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                                @foreach($channel['videos'] as $video)
                                    <article class="overflow-hidden rounded-lg border border-[#dfe7e5] bg-white shadow-[0_10px_28px_rgba(18,59,62,0.08)]">
                                        <div class="bg-black">
                                            <iframe
                                                src="{{ $video['embed_url'] }}"
                                                title="{{ $video['title'] }}"
                                                loading="lazy"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                allowfullscreen
                                                class="aspect-video w-full"
                                            ></iframe>
                                        </div>
                                        <div class="p-3">
                                            <h3 class="line-clamp-2 min-h-10 text-sm font-black leading-5 text-ink">{{ $video['title'] }}</h3>
                                            <p class="mt-2 text-xs font-semibold text-gray-500">{{ $video['published_at'] ?: $channel['name'] }}</p>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <div class="rounded-lg border border-dashed border-[#d7e1df] bg-white p-6 text-center text-sm text-gray-500">
                                Videos could not be loaded for this channel right now.
                            </div>
                        @endif
                    </section>
                @endforeach
            </div>
        </div>
    </section>
</x-app-layout>
