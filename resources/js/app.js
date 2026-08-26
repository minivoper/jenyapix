const REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

let motionStarted = false;
let pageReady = false;
let openerPlaying = false;

const markReady = () => {
    if (pageReady) return;
    pageReady = true;
    document.documentElement.classList.remove('opener-lock');
    document.querySelector('.hero-still')?.classList.add('is-ken');
    window.dispatchEvent(new Event('jpx:ready'));
};

const isModifiedClick = (e, a) => {
    if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return true;
    if (typeof e.button === 'number' && e.button !== 0) return true;
    const target = a.getAttribute('target');
    if (target && target !== '_self') return true;
    if (a.hasAttribute('download')) return true;
    return false;
};

const workUrl = (url) => {
    try {
        return url.origin === window.location.origin && url.hash === '#work';
    } catch (err) {
        return false;
    }
};

const goToWork = (url) => {
    const here = window.location.pathname === url.pathname;
    if (here) {
        document.getElementById('work')?.scrollIntoView({ behavior: REDUCED ? 'auto' : 'smooth', block: 'start' });
        if (window.location.hash !== '#work') {
            history.pushState(null, '', '#work');
        }
        return;
    }
    try { sessionStorage.setItem('jpx:play-opener', '1'); } catch (e) {}
    window.location.href = url.pathname + url.search + '#work';
};

const playOpener = (onDone) => {
    const opener = document.getElementById('opener');
    let settled = false;
    const finish = () => {
        if (settled) return;
        settled = true;
        openerPlaying = false;
        document.documentElement.classList.remove('opener-lock');
        if (opener) {
            opener.classList.remove('is-live', 'is-done', 'is-skippable');
            opener.classList.add('opener--off');
            opener.setAttribute('aria-hidden', 'true');
            opener.querySelectorAll('.opener__shot').forEach((shot) => shot.classList.remove('is-on'));
        }
        onDone?.();
    };

    if (!opener || REDUCED) {
        finish();
        return;
    }
    if (openerPlaying) return;

    const shots = [...opener.querySelectorAll('.opener__shot')];
    if (shots.length === 0) {
        finish();
        return;
    }

    openerPlaying = true;
    const counter = opener.querySelector('[data-opener-count]');
    const pad = (n) => String(n).padStart(2, '0');
    const total = shots.length;
    const beat = 780;
    const hold = 900;
    const fade = 800;

    document.documentElement.classList.add('opener-lock');
    opener.classList.remove('opener--off', 'is-done', 'is-live', 'is-skippable');
    opener.removeAttribute('aria-hidden');
    opener.style.setProperty('--opener-dur', `${(total - 1) * beat + hold}ms`);

    let closed = false;
    let skippable = false;
    const timers = [];
    const later = (fn, ms) => timers.push(window.setTimeout(fn, ms));

    const close = () => {
        if (closed) return;
        closed = true;
        timers.forEach(window.clearTimeout);
        opener.classList.add('is-done');
        const end = () => {
            if (!closed) return;
            finish();
        };
        opener.addEventListener('transitionend', (e) => {
            if (e.target === opener && e.propertyName === 'opacity') end();
        });
        later(end, fade + 200);
    };

    opener.querySelector('[data-opener-skip]')?.addEventListener('click', (e) => {
        e.stopPropagation();
        if (skippable) close();
    }, { once: true });
    const onKey = (e) => {
        if (e.key === 'Escape' && skippable) close();
    };
    window.addEventListener('keydown', onKey);
    later(() => {
        if (closed) return;
        skippable = true;
        opener.classList.add('is-skippable');
        opener.addEventListener('click', close, { once: true });
        window.addEventListener('wheel', close, { once: true, passive: true });
        window.addEventListener('touchstart', close, { once: true, passive: true });
    }, 1600);

    const show = (index) => {
        shots.forEach((shot, i) => shot.classList.toggle('is-on', i === index));
        if (counter) counter.textContent = `${pad(index + 1)} / ${pad(total)}`;
    };

    const start = () => {
        if (closed) return;
        opener.classList.add('is-live');
        show(0);
        for (let i = 1; i < total; i++) {
            later(() => {
                if (closed) return;
                show(i);
            }, i * beat);
        }
        later(close, (total - 1) * beat + hold);
    };

    Promise.race([
        shots[0].decode ? shots[0].decode().catch(() => {}) : Promise.resolve(),
        new Promise((r) => window.setTimeout(r, 500)),
    ]).then(start);

    later(() => {
        window.removeEventListener('keydown', onKey);
    }, (total - 1) * beat + hold + fade + 400);
};

const initOpener = () => {
    markReady();

    let queued = false;
    try { queued = sessionStorage.getItem('jpx:play-opener') === '1'; } catch (e) {}
    if (queued) {
        try { sessionStorage.removeItem('jpx:play-opener'); } catch (e) {}
        playOpener(() => {
            document.getElementById('work')?.scrollIntoView({ behavior: REDUCED ? 'auto' : 'smooth', block: 'start' });
        });
    }
};

const initMotion = () => {
    if (motionStarted || REDUCED) return;
    const gsap = window.gsap;
    const ScrollTrigger = window.ScrollTrigger;
    if (!gsap || !ScrollTrigger) return;
    motionStarted = true;
    gsap.registerPlugin(ScrollTrigger);

    const hero = document.querySelector('main > section');
    const nodes = [...document.querySelectorAll('main section h2')]
        .filter((el) => {
            if (hero && hero.contains(el)) return false;
            return el.getBoundingClientRect().top > window.innerHeight * 0.9;
        });

    nodes.forEach((el) => {
        gsap.from(el, {
            autoAlpha: 0,
            y: 18,
            duration: 0.65,
            ease: 'power2.out',
            immediateRender: true,
            scrollTrigger: { trigger: el, start: 'top 90%', once: true },
            onComplete: () => gsap.set(el, { clearProps: 'all' }),
        });
    });

    ScrollTrigger.refresh();
};

const initWipe = () => {
    const wipe = document.getElementById('wipe');
    if (!wipe || REDUCED) {
        wipe?.classList.add('wipe--off');
    } else {
        wipe.classList.add('wipe--off');
        window.addEventListener('pageshow', (e) => {
            if (e.persisted) wipe.className = 'wipe wipe--off';
        });
    }

    document.addEventListener('click', (e) => {
        const a = e.target.closest && e.target.closest('a[href]');
        if (!a || isModifiedClick(e, a)) return;

        let url;
        try { url = new URL(a.href, window.location.href); } catch (err) { return; }
        if (url.origin !== window.location.origin) return;

        if (a.hasAttribute('data-work-run') || workUrl(url)) {
            e.preventDefault();
            e.stopPropagation();
            if (REDUCED || !document.getElementById('opener')) {
                goToWork(url);
                return;
            }
            playOpener(() => goToWork(url));
            return;
        }

        if (!wipe || REDUCED) return;
        if (url.pathname === window.location.pathname && url.search === window.location.search) return;

        e.preventDefault();
        wipe.classList.remove('wipe--off', 'wipe--out');
        wipe.classList.add('wipe--below');
        requestAnimationFrame(() => requestAnimationFrame(() => {
            wipe.classList.remove('wipe--below');
            wipe.classList.add('wipe--covering');
        }));

        let navigated = false;
        const go = () => {
            if (navigated) return;
            navigated = true;
            window.location.href = url.href;
        };
        wipe.addEventListener('transitionend', (ev) => {
            if (ev.target === wipe) go();
        }, { once: true });
        window.setTimeout(go, 700);
    }, true);
};

const boot = () => {
    initOpener();
    initWipe();
    window.addEventListener('jpx:ready', initMotion, { once: true });
    window.setTimeout(initMotion, 4000);
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
