# jenyapix

Personal portfolio for [Jenya Mironov](https://jenyapix.com/) — photographer and filmmaker in New York City. Laravel + Livewire + Tailwind, same stack as the studio sites. Design language (near-black, Inter Tight, PT Mono, split nav) is taken from the JPX Webflow template; the photos and credits are scraped from the live Adobe Portfolio.

## Run

```bash
cd /Users/jenyapix/Claude/GITHUB/jenyapix
php artisan serve
npm run dev
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000).

## What’s here

- `/` homepage — hero, selected work, Instagram, JPX Studios, about, contact form
- `/work/{fashion,portraits,events,street,bts}` — full galleries
- `content/galleries.json` — parsed Adobe metadata
- `content/instagram.json` — curated IG stills + profile
- `public/images/work/` — 345 downloaded frames from jenyapix.com

Contact submissions go to `hello@jpxstudios.com` (log mailer locally).
