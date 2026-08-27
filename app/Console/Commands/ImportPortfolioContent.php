<?php

namespace App\Console\Commands;

use Eshlink\Cms\Services\EntryService;
use Eshlink\Cms\Support\TypeRegistry;
use Illuminate\Console\Command;
use Throwable;

/**
 * One-time migration of the site's flat JSON into the CMS.
 *
 * `content/galleries.json` becomes published `gallery` entries and
 * `content/instagram.json` becomes the published `instagram_feed` singleton.
 * The command is idempotent by slug: a rerun updates the same entries and
 * republishes them rather than making a second copy, so it is safe to run
 * during development as often as needed.
 *
 * Two shape translations happen here and nowhere else:
 *
 *  - a gallery photograph is stored on disk as `{id, file, alt, width}` and the
 *    `gallery` schema names the picture field `image`, so `file` is mapped to
 *    `image` on the way in (and back to `file` by `Portfolio` on the way out);
 *  - galleries are written in file order and then reordered, because a fresh
 *    collection would otherwise sort newest-published-first and reverse the row
 *    order the home page indexes into.
 *
 * The generic `cms:import-json` cannot do the `file` → `image` remap on nested
 * repeater items, which is the whole reason this small site-specific command
 * exists rather than a call to the package importer.
 */
class ImportPortfolioContent extends Command
{
    protected $signature = 'portfolio:import
        {--dry-run : Report what would change without writing.}';

    protected $description = 'Import content/galleries.json and content/instagram.json into the CMS (idempotent by slug).';

    public function handle(TypeRegistry $types, EntryService $entries): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $meta = ['actor_name' => 'portfolio:import', 'actor_role' => 'superadmin'];

        $failed = 0;
        $failed += $this->importGalleries($types, $entries, $meta, $dryRun);
        $failed += $this->importInstagram($types, $entries, $meta, $dryRun);

        if ($dryRun) {
            $this->components->info('Dry run: nothing written.');

            return self::SUCCESS;
        }

        if ($failed > 0) {
            $this->components->error(sprintf('%d item(s) refused. See above.', $failed));

            return self::FAILURE;
        }

        $this->components->info('Portfolio imported.');

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function importGalleries(TypeRegistry $types, EntryService $entries, array $meta, bool $dryRun): int
    {
        $type = $types->get('gallery');
        $rows = $this->readJson(base_path('content/galleries.json'));

        if (! is_array($rows)) {
            $this->components->error('content/galleries.json is missing or not valid JSON.');

            return 1;
        }

        $this->components->info(sprintf('Importing %d galleries.', count($rows)));

        $orderedIds = [];
        $failed = 0;

        foreach ($rows as $gallery) {
            $slug = (string) ($gallery['slug'] ?? '');
            $data = [
                'title' => $gallery['title'] ?? '',
                'slug' => $slug,
                'year' => $gallery['year'] ?? null,
                'blurb' => $gallery['blurb'] ?? '',
                'cover' => $gallery['cover'] ?? '',
                'images' => array_map(static fn (array $image): array => [
                    'image' => $image['file'] ?? '',
                    'alt' => $image['alt'] ?? '',
                    'width' => $image['width'] ?? null,
                ], array_values($gallery['images'] ?? [])),
            ];

            if ($dryRun) {
                $existing = $entries->locate($type, $slug);
                $this->components->twoColumnDetail($slug, $existing === null ? 'would create' : 'would update');

                continue;
            }

            try {
                $entry = $entries->importBySlug($type, $slug, $data, $meta);
                $entries->publish($type, $entry['id'], $meta);
                $orderedIds[] = $entry['id'];
                $this->components->twoColumnDetail($slug, '<fg=green>imported</>');
            } catch (Throwable $e) {
                $failed++;
                $this->components->twoColumnDetail($slug, '<fg=red>refused</>');
                $this->components->bulletList([$e->getMessage()]);
            }
        }

        // Pin the published order to the file order. Without this a fresh
        // collection sorts by published_at descending and the home page, which
        // reads galleries by index, would show them reversed.
        if (! $dryRun && $failed === 0 && $orderedIds !== []) {
            $entries->reorder($type, $orderedIds, $meta);
        }

        return $failed;
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function importInstagram(TypeRegistry $types, EntryService $entries, array $meta, bool $dryRun): int
    {
        $type = $types->get('instagram_feed');
        $feed = $this->readJson(base_path('content/instagram.json'));

        if (! is_array($feed)) {
            $this->components->error('content/instagram.json is missing or not valid JSON.');

            return 1;
        }

        $data = [
            'handle' => $feed['handle'] ?? 'jenyapix',
            'url' => $feed['url'] ?? 'https://www.instagram.com/jenyapix/',
            'posts' => array_map(static fn (array $post): array => [
                'file' => $post['file'] ?? '',
                'href' => $post['href'] ?? '',
                'alt' => $post['alt'] ?? '',
                'video' => (bool) ($post['video'] ?? false),
            ], array_values($feed['posts'] ?? [])),
        ];

        if ($dryRun) {
            $existing = $entries->locate($type, null);
            $this->components->twoColumnDetail('instagram_feed', $existing === null ? 'would create' : 'would update');

            return 0;
        }

        try {
            $entry = $entries->importBySlug($type, null, $data, $meta);
            $entries->publish($type, $entry['id'], $meta);
            $this->components->twoColumnDetail('instagram_feed', '<fg=green>imported</>');

            return 0;
        } catch (Throwable $e) {
            $this->components->twoColumnDetail('instagram_feed', '<fg=red>refused</>');
            $this->components->bulletList([$e->getMessage()]);

            return 1;
        }
    }

    private function readJson(string $path): mixed
    {
        if (! is_file($path)) {
            return null;
        }

        return json_decode((string) file_get_contents($path), true);
    }
}
