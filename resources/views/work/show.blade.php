@extends('layouts.app')

@section('title', $gallery['title'].' — jenyapix')
@section('description', $gallery['blurb'])

@section('content')
    <article class="px-[4vw] pt-28 pb-20">
        <p class="font-mono text-[0.65rem] uppercase tracking-[0.18em] text-fg/45">
            <a href="{{ route('home') }}" class="hover:text-fg">jenyapix</a>
            <span class="mx-2">/</span>
            {{ $gallery['title'] }}
        </p>
        <div class="mt-6 flex flex-wrap items-end justify-between gap-6">
            <h1 class="text-[clamp(2.8rem,7vw,6rem)] font-medium leading-[0.9] tracking-[-0.04em]">{{ $gallery['title'] }}</h1>
            <p class="font-mono text-[0.7rem] uppercase tracking-[0.16em] text-fg/50">{{ $gallery['year'] }} · {{ $gallery['count'] }} frames · 4:5</p>
        </div>
        <p class="mt-6 max-w-xl text-fg/70">{{ $gallery['blurb'] }}</p>

        <div class="mt-14 grid grid-cols-2 gap-2 lg:grid-cols-3 lg:gap-3">
            @foreach ($gallery['images'] as $i => $image)
                <figure class="group relative aspect-[4/5] overflow-hidden bg-fg/5">
                    <img
                        src="{{ asset($image['file']) }}"
                        alt="{{ $image['alt'] }}"
                        class="h-full w-full object-cover transition-transform duration-[800ms] ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:scale-[1.03]"
                        loading="lazy"
                    >
                    <figcaption class="pointer-events-none absolute inset-x-0 bottom-0 flex items-end justify-between bg-gradient-to-t from-bg/85 to-transparent p-3 pt-12 text-[0.7rem] text-fg/90 opacity-0 transition-opacity duration-300 group-hover:opacity-100 lg:p-4">
                        <span class="max-w-[80%] truncate">
                            @if (!empty($image['alt']) && !str_contains($image['alt'], 'Jenya Mironov'))
                                {{ $image['alt'] }}
                            @endif
                        </span>
                        <span class="font-mono text-[0.6rem] tracking-[0.14em] text-fg/60">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    </figcaption>
                </figure>
            @endforeach
        </div>

        <nav class="mt-20 flex flex-wrap gap-x-8 gap-y-3 border-t border-fg/15 pt-8 font-mono text-xs uppercase tracking-[0.16em]" aria-label="Other work">
            @foreach ($galleries as $other)
                <a href="{{ route('work.show', $other['slug']) }}"
                    class="{{ $other['slug'] === $gallery['slug'] ? 'text-fg' : 'text-fg/45 hover:text-fg' }}">
                    {{ $other['title'] }}
                </a>
            @endforeach
        </nav>
    </article>
@endsection
