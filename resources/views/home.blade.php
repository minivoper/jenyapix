@extends('layouts.app')

@section('title', 'jenyapix — Jenya Mironov, NYC photographer')

@section('content')
    @php
        $heroStill = $galleries[3]['cover'] ?? $galleries[0]['cover'];
        $openerShots = collect([
            $galleries[0]['cover'] ?? null,
            $galleries[2]['cover'] ?? null,
            $galleries[1]['cover'] ?? null,
            $galleries[4]['cover'] ?? null,
            $galleries[0]['images'][0]['file'] ?? null,
        ])->filter()->unique()->reject(fn ($src) => $src === $heroStill)->take(5)->push($heroStill)->values();
    @endphp

    <div id="opener" class="opener opener--off" role="dialog" aria-label="Work title sequence" aria-hidden="true">
        <div class="opener__shots">
            @foreach ($openerShots as $i => $shot)
                <img src="{{ asset($shot) }}" alt="" class="opener__shot" decoding="async">
            @endforeach
        </div>
        <p class="opener__meta opener__meta--tl">jenyapix · nyc</p>
        <p class="opener__meta opener__meta--tr"><span data-opener-count>01 / {{ str_pad($openerShots->count(), 2, '0', STR_PAD_LEFT) }}</span></p>
        <div class="opener__mark"><span>jenyapix</span></div>
        <button type="button" class="opener__meta opener__meta--br" data-opener-skip>Skip</button>
        <div class="opener__bar" aria-hidden="true"></div>
    </div>

    {{-- Full-viewport brand hero --}}
    <section class="relative isolate min-h-svh overflow-hidden">
        <img
            src="{{ asset($galleries[3]['cover'] ?? $galleries[0]['cover']) }}"
            alt="New York street photograph by Jenya Mironov"
            class="hero-still absolute inset-0 h-full w-full object-cover object-center"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-bg via-bg/25 to-bg/20"></div>

        <p class="absolute left-[4vw] top-1/2 hidden origin-left -translate-y-1/2 font-mono text-[0.65rem] uppercase tracking-[0.28em] text-fg/70 lg:block" style="writing-mode: vertical-rl;">
            New York City · 40.7128° N
        </p>

        <div class="relative flex min-h-svh flex-col justify-end px-[4vw] pb-10 pt-28">
            <p class="font-mono text-[0.7rem] uppercase tracking-[0.24em] text-fg/80">Photographer &amp; filmmaker</p>
            <h1 class="wordmark mt-2 text-[clamp(5.5rem,22vw,20rem)] text-fg">jenyapix</h1>
            <div class="mt-2 flex flex-wrap items-end justify-between gap-6">
                <p class="max-w-md text-sm leading-relaxed text-fg/80 sm:text-base">The personal mark of Jenya Mironov. Fashion, portraits, events, street, video. Brand work as JPX Studios.</p>
                <div class="flex flex-wrap items-center gap-x-8 gap-y-3">
                    <a href="#work" data-work-run class="inline-flex min-h-12 items-center bg-fg px-6 font-medium text-bg">Enter</a>
                    <a href="https://www.instagram.com/jenyapix" target="_blank" rel="noreferrer" class="font-mono text-[0.65rem] uppercase tracking-[0.18em] underline decoration-fg/40 underline-offset-4">@jenyapix</a>
                </div>
            </div>
            <p class="mt-8 flex items-center gap-3 font-mono text-[0.62rem] uppercase tracking-[0.24em] text-fg/50">
                <span>Scroll to explore</span>
                <span class="scroll-cue" aria-hidden="true">&darr;</span>
            </p>
        </div>
    </section>

    <div class="marquee" aria-hidden="true">
        <div class="marquee__track py-3">
            @foreach (range(1, 2) as $loopPass)
                @foreach (['Photography', 'Videography', 'Events', 'Fashion', 'Portraits', 'Street', 'BTS', 'JPX Studios', 'jenyapix', 'New York'] as $item)
                    <span>{{ $item }}</span>
                @endforeach
            @endforeach
        </div>
    </div>

    {{-- Selected work: 3+2, every tile the same aspect --}}
    <section id="work" class="px-[4vw] py-20 lg:py-28">
        <div class="flex items-end justify-between gap-6">
            <h2 class="text-3xl font-medium tracking-tight lg:text-5xl">Work</h2>
            <p class="font-mono text-[0.65rem] uppercase tracking-[0.18em] text-fg/45">Five rooms · one ratio</p>
        </div>
        <div class="mt-12 grid grid-cols-1 gap-4 sm:grid-cols-6">
            @foreach ($galleries as $i => $gallery)
                <a href="{{ route('work.show', $gallery['slug']) }}"
                    class="group block sm:col-span-2">
                    <div class="relative aspect-[4/5] overflow-hidden bg-fg/5">
                        <img
                            src="{{ asset($gallery['cover']) }}"
                            alt="{{ $gallery['title'] }} — Jenya Mironov"
                            class="h-full w-full object-cover transition-transform duration-[800ms] ease-[cubic-bezier(0.16,1,0.3,1)] group-hover:scale-[1.04]"
                        >
                        <span class="pointer-events-none absolute left-4 top-4 font-mono text-[0.62rem] tracking-[0.16em] text-fg/70">({{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }})</span>
                        <div class="pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-bg/80 to-transparent p-5 pt-16">
                            <div class="flex items-baseline justify-between gap-3">
                                <h3 class="text-xl tracking-tight">{{ $gallery['title'] }}</h3>
                                <span class="font-mono text-[0.65rem] uppercase tracking-[0.16em] text-fg/70">{{ $gallery['count'] }}</span>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Instagram --}}
    <section id="instagram" class="border-t border-fg/15 px-[4vw] py-20 lg:py-28">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="font-mono text-[0.65rem] uppercase tracking-[0.18em] text-fg/45">Live</p>
                <h2 class="mt-3 text-3xl font-medium tracking-tight lg:text-5xl">{{ '@'.$instagram['handle'] }}</h2>
            </div>
            <a href="{{ $instagram['url'] }}" target="_blank" rel="noreferrer" class="font-mono text-xs uppercase tracking-[0.18em] underline decoration-fg/30 underline-offset-4 hover:decoration-fg">Open Instagram</a>
        </div>
        <div class="mt-12 grid grid-cols-3 gap-[2px]">
            @foreach ($instagram['posts'] as $post)
                <a href="{{ $post['href'] }}" target="_blank" rel="noreferrer" class="group relative block overflow-hidden bg-fg/5">
                    <img src="{{ asset($post['file']) }}" alt="{{ $post['alt'] }}" class="aspect-square h-full w-full object-cover">
                    @if (!empty($post['video']))
                        <span class="ig-play" aria-hidden="true"></span>
                    @endif
                </a>
            @endforeach
        </div>
    </section>

    {{-- Brand kit --}}
    <section id="mark" class="border-t border-fg/15 px-[4vw] py-20 lg:py-28">
        <p class="font-mono text-[0.65rem] uppercase tracking-[0.18em] text-fg/45">Brand kit</p>
        <h2 class="mt-4 text-3xl font-medium tracking-tight lg:text-5xl">The mark.</h2>
        <p class="mt-6 max-w-xl text-fg/70">jenyapix is the personal name on the work. Lowercase, tight tracking, always set in Inter Tight. Never stacked on a logo lockup. Never outlined.</p>

        <div class="mt-14 overflow-hidden border border-fg/15 bg-bg px-[4vw] py-16">
            <p class="font-mono text-[0.65rem] uppercase tracking-[0.2em] text-fg/40">Wordmark · primary</p>
            <p class="wordmark mt-6 text-[clamp(3rem,14vw,11rem)]">jenyapix</p>
        </div>

        <div class="mt-4 grid gap-4 lg:grid-cols-2">
            <div class="border border-fg/15 bg-fg px-[4vw] py-16 text-bg">
                <p class="font-mono text-[0.65rem] uppercase tracking-[0.2em] text-bg/45">Wordmark · invert</p>
                <p class="wordmark mt-6 text-[clamp(2.4rem,8vw,6rem)]">jenyapix</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="border border-fg/15 p-6">
                    <div class="swatch bg-bg ring-1 ring-fg/20"></div>
                    <p class="mt-4 font-mono text-[0.65rem] uppercase tracking-[0.16em]">Ink</p>
                    <p class="mt-1 font-mono text-sm text-fg/55">#0F0F0F</p>
                </div>
                <div class="border border-fg/15 p-6">
                    <div class="swatch bg-fg"></div>
                    <p class="mt-4 font-mono text-[0.65rem] uppercase tracking-[0.16em]">Bone</p>
                    <p class="mt-1 font-mono text-sm text-fg/55">#FDFDFD</p>
                </div>
                <div class="border border-fg/15 p-6">
                    <p class="text-4xl tracking-tight">Aa</p>
                    <p class="mt-4 font-mono text-[0.65rem] uppercase tracking-[0.16em]">Display</p>
                    <p class="mt-1 text-sm text-fg/55">Inter Tight</p>
                </div>
                <div class="border border-fg/15 p-6">
                    <p class="font-mono text-2xl uppercase tracking-[0.14em]">Aa</p>
                    <p class="mt-4 font-mono text-[0.65rem] uppercase tracking-[0.16em]">Label</p>
                    <p class="mt-1 font-mono text-sm text-fg/55">PT Mono</p>
                </div>
            </div>
        </div>
        <p class="mt-8 max-w-2xl font-mono text-[0.7rem] uppercase leading-relaxed tracking-[0.12em] text-fg/45">
            Ratio 4:5 for stills. Square for social. Type never sits in the middle of a frame except this hero lockup. Captions live under or on a gradient edge.
        </p>
    </section>

    {{-- JPX Studios --}}
    <section id="studio" class="border-t border-fg/15 px-[4vw] py-20 lg:py-28">
        <p class="font-mono text-[0.65rem] uppercase tracking-[0.18em] text-fg/45">Studio</p>
        <div class="mt-6 grid gap-10 lg:grid-cols-2 lg:items-stretch">
            <div class="flex flex-col justify-between">
                <div>
                    <h2 class="text-3xl font-medium tracking-tight lg:text-5xl">JPX Studios</h2>
                    <p class="mt-8 max-w-xl text-lg leading-relaxed text-fg/75">
                        The commercial arm. Brand activations, corporate events, fashion week, portraits, product, and video — same team, same night.
                    </p>
                    <p class="mt-5 max-w-xl leading-relaxed text-fg/70">
                        Don Julio, Vogue Eyewear, Amika, Lenovo, Amazon, Red Bull, Hoka, AND1. Quoted per project. Selects in 48 hours.
                    </p>
                </div>
                <div class="mt-10 flex flex-wrap items-center gap-x-8 gap-y-3">
                    <a href="mailto:hello@jpxstudios.com" class="inline-flex min-h-12 items-center bg-fg px-6 font-medium text-bg">Book the studio</a>
                    <a href="mailto:hello@jpxstudios.com" class="font-mono text-xs uppercase tracking-[0.16em] text-fg/70 hover:text-fg">hello@jpxstudios.com</a>
                </div>
            </div>
            <figure class="relative aspect-[4/5] overflow-hidden bg-fg/5 lg:aspect-auto lg:min-h-full">
                <img src="{{ asset($galleries[2]['cover']) }}" alt="Event photography by JPX Studios" class="absolute inset-0 h-full w-full object-cover">
            </figure>
        </div>
    </section>

    {{-- About --}}
    <section id="about" class="border-t border-fg/15 px-[4vw] py-20 lg:py-28">
        <p class="font-mono text-[0.65rem] uppercase tracking-[0.18em] text-fg/45">About</p>
        <h2 class="mt-6 max-w-4xl text-3xl font-medium tracking-tight lg:text-5xl">Fourth generation behind the lens.</h2>
        <p class="mt-8 max-w-2xl text-lg leading-relaxed text-fg/75">
            Born in Russia, raised in Israel, based in New York City. The camera has been in the family since his great-great-grandfather exhibited in Ukraine. jenyapix is the name on the stills. JPX Studios is the name on the invoice.
        </p>
        <dl class="mt-14 grid gap-px bg-fg/15 sm:grid-cols-3">
            <div class="bg-bg p-6">
                <dt class="font-mono text-[0.65rem] uppercase tracking-[0.18em] text-fg/45">Based</dt>
                <dd class="mt-3 text-2xl tracking-tight">New York City</dd>
            </div>
            <div class="bg-bg p-6">
                <dt class="font-mono text-[0.65rem] uppercase tracking-[0.18em] text-fg/45">Generation</dt>
                <dd class="mt-3 text-2xl tracking-tight">Fourth</dd>
            </div>
            <div class="bg-bg p-6">
                <dt class="font-mono text-[0.65rem] uppercase tracking-[0.18em] text-fg/45">Shoots</dt>
                <dd class="mt-3 text-2xl tracking-tight">Photo and video</dd>
            </div>
        </dl>
    </section>

    {{-- Contact --}}
    <section id="contact" class="border-t border-fg/15 px-[4vw] py-20 lg:py-28">
        <p class="font-mono text-[0.65rem] uppercase tracking-[0.18em] text-fg/45">Contact</p>
        <h2 class="mt-6 text-3xl font-medium tracking-tight lg:text-5xl">Tell me what you’re planning.</h2>
        <p class="mt-6 max-w-xl text-fg/70">Date, neighborhood or venue, stills or video. Reply within one business day.</p>
        <div class="max-w-3xl">
            <livewire:contact-form />
        </div>
    </section>
@endsection
