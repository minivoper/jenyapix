# jenyapix — what it is and how it was built

Personal brand site for **Jenya Mironov** (`jenyapix`): photographer and filmmaker in New York. Commissioned / commercial work is billed as **JPX Studios**. This repo is **not** the JPX Studios marketing site (Wil’s Laravel app on staging). This is the jenyapix.com archive, Instagram, brand kit, and inquiry form.

Live: https://jenyapix-production-3lbdbh.laravel.cloud  
GitHub: https://github.com/minivoper/jenyapix

---

## Stack

Same Laravel stack as the other recent sites (marina.newyorkcity, etc.). Not Next.js.

| Layer | Choice |
|---|---|
| App | Laravel 13, PHP 8.3+ |
| UI | Blade + Livewire 4 + Alpine (via Livewire) |
| CSS | Tailwind 4 |
| Bundler | Vite 8 |
| Type | Inter Tight (display), PT Mono (labels) via Bunny fonts |
| Motion | GSAP 3 + ScrollTrigger from jsDelivr; custom opener / wipe in `resources/js/app.js` |

Key paths:

- `routes/web.php` — two routes
- `app/Support/Portfolio.php` — JSON loaders
- `app/Livewire/ContactForm.php` — inquiry form
- `resources/views/layouts/app.blade.php` — shell, nav, wipe overlay
- `resources/views/home.blade.php` — homepage + opener markup
- `resources/views/work/show.blade.php` — gallery
- `resources/js/app.js` — opener, work-run trigger, ink wipe, light GSAP
- `resources/css/app.css` — tokens, opener, wipe, wordmark
- `content/galleries.json`, `content/instagram.json`

---

## Content model

No CMS and no work tables. Galleries and Instagram are JSON on disk.

**Work rooms** (`content/galleries.json`): fashion, portraits, events, street, bts. Each has slug, title, year, blurb, cover path, count, and an `images[]` of `{id, file, alt, width}`.

**Instagram** (`content/instagram.json`): handle `jenyapix`, profile URL, and 12 posts in the same order as the public grid (three pinned first, then recents). Each post: `file`, `href` (`/p/` or `/reel/`), `alt` (first line of caption), `video`.

**Files:** `public/images/work/{slug}/` and `public/images/instagram/{shortcode}.jpg`.

There is no scraper in this repo. Stills were ingested once:

1. Work frames from Adobe Portfolio (`cdn.myportfolio.com`) into `public/images/work/`.
2. Instagram covers from the public profile API (`/api/v1/users/web_profile_info/?username=jenyapix`), downloaded with `https://www.instagram.com/p/{shortcode}/media/?size=l`.

To refresh Instagram: re-pull the profile JSON, replace the 12 files, rewrite `content/instagram.json`. Do not pad the grid with work-room photos.

---

## Pages

**`/` (home)**

1. Full-viewport hero (street cover + `jenyapix` wordmark)
2. Selected work — five 4:5 tiles
3. `@jenyapix` — 3-column square grid, 2px gaps, play mark on reels
4. Brand kit — wordmark primary/invert, ink `#0F0F0F`, bone `#FDFDFD`, type specimens
5. JPX Studios blurb + `hello@jpxstudios.com`
6. About
7. Contact (Livewire)

**`/work/{slug}`** — one room, 4:5 grid, prev/next rooms at the bottom.

Contact fields: name, email, project (`message`). Honeypot `website`. Mails `hello@jpxstudios.com` with reply-to the sender. No “how did you find us” field on this site (that was for the JPX Studios brief).

---

## Motion

This was rewritten until the opener stopped fighting other animation.

**Homepage load does not play the opener.** The overlay starts as `.opener--off`. Ken Burns (`.hero-still.is-ken`) starts on `jpx:ready`.

**The image run plays only when the user presses Work:**

- Nav **Work**
- Mobile **Work**
- Hero **Enter**

Those links have `data-work-run` and `href` `#work`. Click plays 6 stills (crossfade ~780ms, last frame = hero still), then fades out and scrolls to `#work`. Skip after 1.6s: Skip, Escape, click, wheel, or touch.

From a gallery page, Work is a different path, so JS sets `sessionStorage jpx:play-opener=1` and goes to `/#work`. Home sees the flag, plays the run, then scrolls to Work.

What was removed because it jumped: mix-blend-difference on the wordmark, white flash on cuts, alternating zooms, GSAP clip-path on the hero/h1, Ken Burns under the opener, GSAP hiding every brand-kit label, opener `transitionend` on child nodes.

**Ink wipe** (`#wipe`) only covers real same-origin path changes (e.g. `/` → `/work/fashion`). Hash links including `#work` do not wipe.

**`target="_blank"` bug:** `route('home')` is an `http://…` URL, so a naive `str_starts_with($href, 'http')` opened Work/Mark/Contact in a new tab. Only hosts that are not `url('/')` get `target="_blank"` (Instagram).

`prefers-reduced-motion: reduce` hides opener and wipe and kills Ken Burns / marquee / scroll cue.

---

## Design language

Taken from the Webflow JPX template tokens, not from dummy JPX copy.

- Ink `#0F0F0F`, bone `#FDFDFD`
- Wordmark: lowercase, Inter Tight, tracking about `-0.07em`, `h1.wordmark` has extra padding so j/y/p/x descenders do not sit on the line below
- Stills 4:5, social square
- Split-hover nav (two stacked labels)

---

## Run locally

```bash
cd GITHUB/jenyapix
cp .env.example .env
php artisan key:generate
composer install
npm install
php artisan serve --host=127.0.0.1 --port=8000
npm run dev   # Vite on :5173
```

Open http://127.0.0.1:8000/. To replay the opener: click Work (or `sessionStorage.removeItem('jpx:play-opener')` is only the cross-page flag; load does not autoplay).

---

## Deploy

| | |
|---|---|
| Git | `minivoper/jenyapix`, public, branch `main` |
| Host | Laravel Cloud, org **Evgeny Mir**, region `us-east-2`, app `jenyapix` |
| URL | https://jenyapix-production-3lbdbh.laravel.cloud |
| PHP / Node | 8.5 / 24 |
| Build | `composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader` then `npm ci --audit false` then `npm run build` |
| Deploy command | `php artisan migrate --force` (sessions/cache/queue tables; work content is still JSON) |
| Push-to-deploy | on |

`cloud ship` on CLI **v0.5.2** created the app and postgres cluster, then crashed because it called the clusters API with `include=schemas` (API now wants `include=databases`). Workaround: read the cluster via the API, `cloud environment:update --database-id=…`, set deploy command, `cloud deploy jenyapix production`.

No custom domain (jenyapix.com) is attached yet.

CLI: `/Users/jenyapix/.composer/vendor/bin/cloud`  
GitHub CLI is logged in as **minivoper**.

---

## Out of scope (same calendar day)

A separate handoff for **Wil’s JPX Studios** staging site (`jpxstudios.madebywil.co`) lived as notes + PDF, not this repo: `/Users/jenyapix/Claude/JPX-Studios-Design-QA.pdf` and `/Users/jenyapix/Claude/docs/superpowers/plans/2026-08-26-jpx-studios-layout-and-contact.md`.
