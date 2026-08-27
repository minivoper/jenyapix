<?php

use Eshlink\Cms\CmsServiceProvider;
use Eshlink\Cms\Http\Middleware\BlockCrawlers;
use Eshlink\Cms\Http\Middleware\PreviewGate;
use Eshlink\Cms\Http\Middleware\PreviewToLiveRedirect;
use Eshlink\Cms\Support\AdminErrorPages;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        /*
         * Every one of these is host-aware through Eshlink\Cms\Support\HostMode
         * and does nothing at all on a production host, so the public site at
         * jenyapix.com answers exactly as it did before this closure had
         * anything in it — same bytes, same headers. Livewire on the contact
         * form is untouched: none of this runs on the `livewire/*` update
         * endpoint on the production host.
         */

        /*
         * Global rather than grouped, on purpose: global middleware wrap routing
         * itself, so a 404 for a path that matched no route — and a rendered 500
         * — carry the no-index and admin security headers too. Group middleware
         * would miss both, and a 404 without X-Robots-Tag is exactly the
         * response a crawler keeps.
         */
        CmsServiceProvider::forceNoIndexGlobally($middleware);
        CmsServiceProvider::secureAdminGlobally($middleware);

        /*
         * The whole-site availability switch, on the public `web` group. Without
         * this line the "Take the site offline" dashboard control and the
         * set_site_mode MCP tool still record the mode, but nothing enforces it.
         * It is host-aware — admin and preview hosts pass through in every mode —
         * and reads the mode with a fail-open to `live`, so a database blip
         * never takes a live site dark. jenyapix ships `live`.
         */
        CmsServiceProvider::guardSiteAvailability($middleware);

        /*
         * On the `web` group because the preview gate reads and writes a session
         * and the crawler denylist only has an opinion about pages a person
         * could be shown. Named directly rather than through their `cms.*`
         * aliases: this closure runs before the package's provider registers
         * them with the router.
         */
        $middleware->appendToGroup('web', [
            PreviewToLiveRedirect::class,
            BlockCrawlers::class,
            PreviewGate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        /*
         * The admin's own refusal, expiry and not-found screens, on the admin
         * host only. AdminErrorPages asks HostMode which host answered and
         * returns null for every other host, so a dead link on jenyapix.com is
         * still answered by Laravel's own page, byte for byte.
         */
        AdminErrorPages::register($exceptions);
    })->create();
