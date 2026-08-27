<?php

declare(strict_types=1);

use App\Cms\Types\GalleryType;
use App\Cms\Types\InstagramFeedType;

/*
|--------------------------------------------------------------------------
| eshlink-cms — jenyapix
|--------------------------------------------------------------------------
|
| This file is a SNAPSHOT. The package fills in every key it leaves out
| (CmsServiceProvider::mergeCmsConfig merges the package defaults underneath
| this file, key by key, all the way down), so it only says the things
| jenyapix means differently from the package. Everything about MCP, media,
| auth, roles, rate limits, the availability block and the rest is the
| package's own answer on purpose — see docs/installing-into-a-site.md.
|
*/

return [

    'site' => env('CMS_SITE_SLUG', 'jenyapix'),

    'site_name' => env('CMS_SITE_NAME', env('APP_NAME', 'jenyapix')),

    'default_locale' => env('CMS_DEFAULT_LOCALE', 'en'),

    /*
    | Hosts. With `admin_domain` unset, the admin/mcp/service route files do not
    | register at all, so a not-yet-onboarded deploy exposes nothing but the
    | public site and its SEO surface.
    */

    'admin_domain' => env('CMS_ADMIN_DOMAIN'),

    'admin_prefix' => env('CMS_ADMIN_PREFIX', ''),

    'preview_domain' => env('CMS_PREVIEW_DOMAIN'),

    'production_domain' => env('CMS_PRODUCTION_DOMAIN', 'jenyapix.com'),

    /*
    | Explicit host => kind map. The apex and its www alias are production; a
    | developer machine and the test suite render the production site, so they
    | resolve as production too — otherwise they would fall to `default_mode`
    | and suppress their own canonical tags, which is exactly what makes "does
    | this page still render byte for byte" impossible to answer locally.
    */

    'site_domains' => [
        'jenyapix.com' => 'production',
        'www.jenyapix.com' => 'production',
        'localhost' => 'production',
        '127.0.0.1' => 'production',
        'jenyapix.eshlink.com' => 'preview',
    ],

    'default_mode' => env('CMS_DEFAULT_MODE', 'preview'),

    /*
    | The content this site declares. A portfolio gallery collection and the
    | Instagram strip singleton — the two things that used to be flat JSON on
    | disk. Their read path is App\Support\Portfolio, unchanged in shape.
    */

    'types' => [
        GalleryType::class,
        InstagramFeedType::class,
    ],

    /*
    | SEO / GEO surface. jenyapix had no robots.txt, sitemap or llms.txt before;
    | the package's host-mode generators stand them up from published content on
    | the production host and 404 everywhere else. The only site-specific facts
    | are that galleries live under /work (not /gallery) and that a gallery's
    | one-line summary is its `blurb`.
    */

    'seo' => [
        'url_prefixes' => [
            'gallery' => '/work',
        ],
        'summary_fields' => ['blurb', 'excerpt', 'summary', 'description'],
    ],

    /*
    | Credited on /humans.txt.
    */

    'humans' => [
        'photographer' => 'Jenya Mironov',
        'developer' => 'Evgeny Mironov',
    ],

];
