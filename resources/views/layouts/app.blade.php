<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'jenyapix — Jenya Mironov')</title>
    <meta name="description" content="@yield('description', 'jenyapix is the personal brand of Jenya Mironov, New York City photographer and filmmaker. Archive, Instagram, and JPX Studios.')">
    @if (\Eshlink\Cms\Support\HostMode::isProduction(request()))
    <link rel="canonical" href="{{ url()->current() }}">
    @else
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet, noimageindex">
    @endif
    @fonts
    @stack('head')
    <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/gsap.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/ScrollTrigger.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg font-sans text-fg antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:bg-fg focus:px-4 focus:py-3 focus:text-bg">Skip to content</a>

    <div id="wipe" class="wipe wipe--off" aria-hidden="true"></div>

    <header class="fixed inset-x-0 top-0 z-40">
        <div class="flex items-center justify-between px-[4vw] py-5">
            <a href="{{ route('home') }}" class="text-[0.95rem] tracking-[-0.04em]">jenyapix</a>
            <nav class="hidden items-center gap-7 text-[0.72rem] tracking-[0.14em] uppercase sm:flex" aria-label="Primary">
                @foreach ([
                    ['Work', route('home').'#work', true],
                    ['Mark', route('home').'#mark', false],
                    ['Instagram', 'https://www.instagram.com/jenyapix', false],
                    ['Contact', route('home').'#contact', false],
                ] as [$label, $href, $workRun])
                    <a href="{{ $href }}" @if(!str_starts_with($href, url('/'))) target="_blank" rel="noreferrer" @endif
                        @if($workRun) data-work-run @endif
                        class="group relative inline-block h-[1.15em] overflow-hidden">
                        <span class="block transition-transform duration-300 ease-out group-hover:-translate-y-full">{{ $label }}</span>
                        <span class="absolute inset-0 translate-y-full transition-transform duration-300 ease-out group-hover:translate-y-0" aria-hidden="true">{{ $label }}</span>
                    </a>
                @endforeach
            </nav>
            <a href="{{ route('home') }}#work" data-work-run class="font-mono text-[0.65rem] uppercase tracking-[0.18em] sm:hidden">Work</a>
        </div>
    </header>

    <main id="main">
        @yield('content')
    </main>

    <footer class="overflow-hidden border-t border-fg/15 px-[4vw] pt-16 pb-10">
        <div class="grid gap-10 md:grid-cols-3">
            <div>
                <p class="font-mono text-[0.65rem] uppercase tracking-[0.18em] text-fg/45">More</p>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a class="hover:opacity-70" href="https://www.instagram.com/jenyapix" target="_blank" rel="noreferrer">Instagram</a></li>
                    <li><a class="hover:opacity-70" href="https://www.pinterest.com/jenyapix/" target="_blank" rel="noreferrer">Pinterest</a></li>
                    <li><a class="hover:opacity-70" href="mailto:hello@jpxstudios.com">hello@jpxstudios.com</a></li>
                </ul>
            </div>
            <div>
                <p class="font-mono text-[0.65rem] uppercase tracking-[0.18em] text-fg/45">Index</p>
                <ul class="mt-4 space-y-2 text-sm">
                    <li><a class="hover:opacity-70" href="{{ route('home') }}">Home</a></li>
                    <li><a class="hover:opacity-70" href="{{ route('home') }}#mark">Mark</a></li>
                    @foreach ($galleries ?? [] as $g)
                        <li><a class="hover:opacity-70" href="{{ route('work.show', $g['slug']) }}">{{ $g['title'] }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="md:text-right">
                <p class="font-mono text-[0.65rem] uppercase tracking-[0.18em] text-fg/45">Jenya Mironov</p>
                <p class="mt-4 max-w-sm text-sm text-fg/70 md:ml-auto">Personal brand and archive. Commissioned work through JPX Studios. New York City.</p>
            </div>
        </div>
        <p class="wordmark mt-16 select-none text-[clamp(3.5rem,18vw,14rem)] text-fg/12">jenyapix</p>
        <p class="mt-4 font-mono text-[0.65rem] uppercase tracking-[0.14em] text-fg/40">© {{ date('Y') }} jenyapix. All rights reserved.</p>
    </footer>
</body>
</html>
