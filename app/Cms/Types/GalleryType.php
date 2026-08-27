<?php

namespace App\Cms\Types;

use Eshlink\Cms\Contracts\ContentSource;
use Eshlink\Cms\Contracts\PubliclyRoutable;
use Eshlink\Cms\Schema\Fields\Image;
use Eshlink\Cms\Schema\Fields\Number;
use Eshlink\Cms\Schema\Fields\Repeater;
use Eshlink\Cms\Schema\Fields\Slug;
use Eshlink\Cms\Schema\Fields\Text;
use Eshlink\Cms\Schema\Schema;
use Eshlink\Cms\Sources\EntrySource;
use Eshlink\Cms\Support\SiteMap;

/**
 * A portfolio gallery — Fashion, Portraits, Events, Street, BTS — each a titled
 * set of photographs rendered at `/work/{slug}` and previewed as a cover tile
 * on the home page.
 *
 * These used to live as objects in `content/galleries.json`, read on every
 * request by `App\Support\Portfolio`. They are entries now: an ordinary
 * `EntrySource` collection with a user-editable address and a `Repeater` of
 * photographs, imported once by `portfolio:import`. `Portfolio::galleries()`
 * became a read-through over the published entries, so `routes/web.php` and the
 * two Blade views that consume them did not have to change at all.
 *
 * The image path is stored, not a `cms_media` id: the pictures already sit
 * under `public/images/work/*` and the Blade renders them with `asset()`, so a
 * content-addressed path is the value the column has always held. `count` is
 * not a field — the number the page prints is exactly how many photographs the
 * gallery holds, so it is derived from the repeater rather than stored and left
 * free to drift.
 */
class GalleryType extends BaseType implements PubliclyRoutable
{
    /**
     * `/work/{slug}` — the route the gallery renders at. The posted slug wins
     * over the stored one, so renaming a gallery and looking at it shows it at
     * its new address. A gallery with no slug has no address yet, and null is
     * the honest answer.
     *
     * @param  array<string, mixed>  $entry
     */
    public function publicPath(array $entry): ?string
    {
        $slug = $entry['data']['slug'] ?? $entry['slug'] ?? null;

        return is_string($slug) && $slug !== '' ? '/work/'.$slug : null;
    }

    public function key(): string
    {
        return 'gallery';
    }

    public function label(): string
    {
        return 'Gallery';
    }

    public function pluralLabel(): string
    {
        return 'Galleries';
    }

    public function blurb(): ?string
    {
        return 'The bodies of work under Work, each with its own page of photographs.';
    }

    public function group(): ?string
    {
        return SiteMap::GROUP_COLLECTION;
    }

    public function hasSlug(): bool
    {
        return true;
    }

    public function isOrderable(): bool
    {
        return true;
    }

    public function schema(): Schema
    {
        return Schema::make([
            Text::make('title')->required()->max(120),
            Slug::make('slug')->from('title')->max(255)
                ->help('The address of the gallery, e.g. /work/fashion. Changing it breaks links people have already shared.'),
            Number::make('year')->integer()->min(1900)->max(2200)
                ->help('The year shown on the gallery page.'),
            Text::make('blurb')->required()->max(255)
                ->help('One line under the title on the gallery page and beside its cover on the home page.'),
            Image::make('cover')->storesPath()->required()->max(255)
                ->withLabel('Cover picture')
                ->help('The tile shown for this gallery on the home page.'),
            Repeater::make('images')->min(1)->max(400)->of(Schema::make([
                Image::make('image')->storesPath()->required()->max(255)
                    ->withLabel('Photograph')
                    ->help('Choose one from your photos.'),
                Text::make('alt')->required()->max(255)
                    ->help('What the photograph shows, for anyone who cannot see it.'),
                Number::make('width')->integer()->min(1)->max(20000)
                    ->help('The pixel width of the source file. Used to hint layout; leave it as imported.'),
            ])),
        ]);
    }

    public function source(): ContentSource
    {
        return new EntrySource;
    }
}
