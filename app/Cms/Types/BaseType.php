<?php

namespace App\Cms\Types;

use Eshlink\Cms\Contracts\ContentRule;
use Eshlink\Cms\Contracts\ContentType;
use Eshlink\Cms\Contracts\Presentable;

/**
 * Shared answers for the parts of the {@see ContentType} contract that are the
 * same for almost every type this site declares.
 *
 * Only `key()`, `label()`, `schema()` and `source()` stay abstract, because
 * those are the four things that actually differ between a gallery and the
 * Instagram strip. Everything else has one obviously correct answer here and is
 * overridden where it is not.
 *
 * {@see Presentable} is implemented here rather than type by type so that every
 * type this site declares is guaranteed to answer the admin's three questions,
 * even if the answer is "nothing in particular" — no card whose blurb is a
 * stray `null` on one type and a sentence on its neighbour.
 */
abstract class BaseType implements ContentType, Presentable
{
    public function pluralLabel(): string
    {
        return $this->label();
    }

    public function isSingleton(): bool
    {
        return false;
    }

    /**
     * Nothing rather than something generic. A card that says "A content type"
     * under its name has spent a line of the screen saying what the reader
     * already knew; every type with something worth saying says it.
     */
    public function blurb(): ?string
    {
        return null;
    }

    /**
     * Null defers to `SiteMap`, which sorts a singleton into Pages and
     * everything else into Collections.
     */
    public function group(): ?string
    {
        return null;
    }

    /**
     * Always null on this site. `SiteMap` puts the first letter of the label on
     * the plate, and a letter beats a symbol somebody chose for you.
     */
    public function glyph(): ?string
    {
        return null;
    }

    public function hasSlug(): bool
    {
        return false;
    }

    public function isOrderable(): bool
    {
        return false;
    }

    /**
     * This is Jenya's own site and Jenya's own work. The approval gate exists
     * for davidkober's compliance lock, not here.
     */
    public function requiresApproval(): bool
    {
        return false;
    }

    /**
     * @return array<int, ContentRule>
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Empty by default. A collection type seeds its rows through the one-time
     * `portfolio:import` command rather than `cms:install --seed-defaults`,
     * because defaults describe a single entry and a gallery list is many. The
     * page singletons override this with the literals they used to read off
     * disk.
     *
     * @return array<string, mixed>
     */
    public function defaults(): array
    {
        return [];
    }

    /**
     * @return array{read: string, write: string, publish: string, delete: string}
     */
    public function abilities(): array
    {
        return [
            'read' => 'content.read',
            'write' => 'content.write',
            'publish' => 'content.publish',
            'delete' => 'content.delete',
        ];
    }
}
