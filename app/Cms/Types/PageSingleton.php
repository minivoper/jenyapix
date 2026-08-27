<?php

namespace App\Cms\Types;

use Eshlink\Cms\Contracts\ContentSource;
use Eshlink\Cms\Contracts\PubliclyRoutable;
use Eshlink\Cms\Sources\EntrySource;
use Eshlink\Cms\Support\SiteMap;

/**
 * Base for a page-shaped singleton: exactly one entry, no address of its own,
 * drawn at a route that belongs to `routes/web.php`. The Instagram strip is the
 * one such type this site has today.
 *
 * Backed by `EntrySource` rather than `ModelSource` because there is no model
 * to wrap — the content used to be a flat JSON file read off disk, not a row in
 * a table.
 */
abstract class PageSingleton extends BaseType implements PubliclyRoutable
{
    public function isSingleton(): bool
    {
        return true;
    }

    public function source(): ContentSource
    {
        return new EntrySource;
    }

    /**
     * Each subclass IS one page of the site, so the group is settled here.
     * Declared explicitly all the same: `SiteMap` would infer it from
     * `isSingleton()`, but a page that stopped being a singleton would then
     * quietly change groups, and that is not what "this is a page" means.
     */
    public function group(): ?string
    {
        return SiteMap::GROUP_PAGE;
    }

    /**
     * The address the page's words are drawn at — which is what gives it a live
     * preview pane in the editor. The entry is ignored: a singleton has no slug,
     * and the route it renders at is a literal in `routes/web.php`.
     *
     * @param  array<string, mixed>  $entry
     */
    abstract public function publicPath(array $entry): ?string;
}
