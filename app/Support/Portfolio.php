<?php

namespace App\Support;

use Eshlink\Cms\Facades\Cms;

/**
 * The read side of the portfolio, backed by the CMS.
 *
 * This class used to `json_decode` `content/galleries.json` and
 * `content/instagram.json` on every call. It now reads the published CMS
 * entries instead — but its three methods return the exact same array shapes
 * they always did, so `routes/web.php`, `home.blade.php` and `work/show.blade.php`
 * did not change and the site renders byte-for-byte as before.
 *
 * Reads are cached by the CMS itself: `Cms::published()` / `Cms::all()` cache
 * per content type behind a version token that every publish bumps, so a
 * published edit shows up at once and an unchanged page is not re-queried. On
 * top of that the shaped result is memoised for the life of the request,
 * because both Blade views ask for the gallery list more than once per page.
 *
 * Every read falls back to the shape the views expect even with an empty
 * database, so a CMS hiccup degrades to an empty strip or list, never a fatal
 * error on the public site.
 */
class Portfolio
{
    /** @var array<string, mixed>|null */
    private static ?array $galleries = null;

    /** @var array<string, mixed>|null */
    private static ?array $instagram = null;

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function galleries(): array
    {
        if (self::$galleries !== null) {
            return self::$galleries;
        }

        $entries = collect(Cms::published('gallery'))
            ->map(fn (array $entry): array => self::shapeGallery($entry))
            ->all();

        if ($entries !== []) {
            return self::$galleries = $entries;
        }

        // The CMS holds no published galleries yet — a fresh database, before
        // `portfolio:import` has run. Fall back to the file the content was
        // migrated from so the site renders exactly as it did pre-CMS rather
        // than as an empty page. This is the collection analogue of the way the
        // CMS falls back to a singleton's declared defaults, and it keeps the
        // home page (which indexes into the gallery list) from breaking on an
        // unseeded environment such as the test database.
        return self::$galleries = self::galleriesFromFile();
    }

    /**
     * The pre-CMS content, in the exact shape the views expect. Used only when
     * the CMS has nothing published.
     *
     * @return array<int, array<string, mixed>>
     */
    private static function galleriesFromFile(): array
    {
        $path = base_path('content/galleries.json');

        if (! is_file($path)) {
            return [];
        }

        $decoded = json_decode((string) file_get_contents($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function gallery(string $slug): ?array
    {
        foreach (self::galleries() as $gallery) {
            if (($gallery['slug'] ?? null) === $slug) {
                return $gallery;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    public static function instagram(): array
    {
        if (self::$instagram !== null) {
            return self::$instagram;
        }

        $data = Cms::all('instagram_feed', []);

        // As with galleries: if the CMS has nothing (a fresh or unmigrated
        // database), read the file the strip was migrated from so the page
        // renders as it did pre-CMS rather than with an empty strip.
        if (! is_array($data) || $data === []) {
            $path = base_path('content/instagram.json');
            $data = is_file($path)
                ? json_decode((string) file_get_contents($path), true)
                : null;
        }

        if (! is_array($data) || $data === []) {
            return self::$instagram = [
                'handle' => 'jenyapix',
                'url' => 'https://www.instagram.com/jenyapix/',
                'posts' => [],
            ];
        }

        return self::$instagram = [
            'handle' => $data['handle'] ?? 'jenyapix',
            'url' => $data['url'] ?? 'https://www.instagram.com/jenyapix/',
            'posts' => array_map(static fn (array $post): array => [
                'file' => $post['file'] ?? '',
                'href' => $post['href'] ?? '',
                'alt' => $post['alt'] ?? '',
                'video' => (bool) ($post['video'] ?? false),
            ], array_values($data['posts'] ?? [])),
        ];
    }

    /**
     * Map one published `gallery` entry back to the array the views read: the
     * repeater's `image` becomes each photo's `file`, and `count` is derived
     * from how many photographs the gallery actually holds.
     *
     * @param  array<string, mixed>  $entry
     * @return array<string, mixed>
     */
    private static function shapeGallery(array $entry): array
    {
        $data = $entry['data'] ?? [];

        $images = array_map(static fn (array $image): array => [
            'file' => $image['image'] ?? '',
            'alt' => $image['alt'] ?? '',
            'width' => $image['width'] ?? null,
        ], array_values($data['images'] ?? []));

        return [
            'slug' => $entry['slug'] ?? '',
            'title' => $data['title'] ?? '',
            'year' => $data['year'] ?? null,
            'blurb' => $data['blurb'] ?? '',
            'cover' => $data['cover'] ?? '',
            'count' => count($images),
            'images' => $images,
        ];
    }
}
