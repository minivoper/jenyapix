<?php

namespace App\Cms\Types;

use Eshlink\Cms\Schema\Fields\Boolean;
use Eshlink\Cms\Schema\Fields\Image;
use Eshlink\Cms\Schema\Fields\Repeater;
use Eshlink\Cms\Schema\Fields\Text;
use Eshlink\Cms\Schema\Fields\Url;
use Eshlink\Cms\Schema\Schema;

/**
 * The Instagram strip on the home page, which used to be a `File::json()` read
 * of `content/instagram.json` on every request.
 *
 * It is a curated content type now: the caption/alt text is a field Jenya can
 * fix, and the strip can be reordered or trimmed without editing a JSON file.
 * The field keys match exactly what `home.blade.php` already reads off each
 * post — `file`, `href`, `alt`, `video` — so `Portfolio::instagram()` hands the
 * published payload straight to the view with no reshaping and the page renders
 * byte-for-byte as before.
 *
 * The defaults below are the original harvest, so the page is unchanged before
 * anyone signs in; `portfolio:import` re-imports the same file if the strip is
 * ever re-harvested.
 */
class InstagramFeedType extends PageSingleton
{
    public function key(): string
    {
        return 'instagram_feed';
    }

    public function label(): string
    {
        return 'Instagram';
    }

    public function blurb(): ?string
    {
        return 'The strip of posts on your home page.';
    }

    public function schema(): Schema
    {
        return Schema::make([
            Text::make('handle')->required()->max(60)
                ->help('The @name shown over the strip. No @ — it is added for you.'),
            Url::make('url')->required()->max(255)
                ->help('Where the strip heading and its link point.'),
            Repeater::make('posts')->max(60)->of(Schema::make([
                Image::make('file')->storesPath()->required()->max(255)
                    ->withLabel('The picture')
                    ->help('Choose one from your photos.'),
                Url::make('href')->required()->max(255)
                    ->help('The Instagram post or reel this tile links out to.'),
                Text::make('alt')->required()->max(255)
                    ->help('What the picture shows, for anyone who cannot see it.'),
                Boolean::make('video')
                    ->help('On for a reel, so the play badge is drawn over the tile.'),
            ])),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function defaults(): array
    {
        return [
            'handle' => 'jenyapix',
            'url' => 'https://www.instagram.com/jenyapix/',
            'posts' => [
                [
                    'file' => 'images/instagram/DTkz12QjTx5.jpg',
                    'href' => 'https://www.instagram.com/p/DTkz12QjTx5/',
                    'alt' => 'Shot a brief set with ASAP Rocky at #dontbedumb album listening party 🔥 Exclusive never seen before photos 📸',
                    'video' => false,
                ],
                [
                    'file' => 'images/instagram/DQ9eZH9jbh4.jpg',
                    'href' => 'https://www.instagram.com/p/DQ9eZH9jbh4/',
                    'alt' => 'What’s all the fuss about? The jeans are really great 😆',
                    'video' => false,
                ],
                [
                    'file' => 'images/instagram/DQ4jpAOEdnC.jpg',
                    'href' => 'https://www.instagram.com/p/DQ4jpAOEdnC/',
                    'alt' => 'Come create with me, it’s fun 😁 On set this September with @mirabiliaecollection & @emeraldmonde 📸',
                    'video' => false,
                ],
                [
                    'file' => 'images/instagram/DcWQb8skToR.jpg',
                    'href' => 'https://www.instagram.com/p/DcWQb8skToR/',
                    'alt' => 'There is nothing like a good dinner party 🍝',
                    'video' => false,
                ],
                [
                    'file' => 'images/instagram/DcRyMhWnIQp.jpg',
                    'href' => 'https://www.instagram.com/p/DcRyMhWnIQp/',
                    'alt' => 'It’s Ocean’s 11 but on the other side 🔐',
                    'video' => false,
                ],
                [
                    'file' => 'images/instagram/DcPTXBMEc4w.jpg',
                    'href' => 'https://www.instagram.com/p/DcPTXBMEc4w/',
                    'alt' => 'Poolside at The William Vale ☀️🌴',
                    'video' => false,
                ],
                [
                    'file' => 'images/instagram/Db_rTcLnOTQ.jpg',
                    'href' => 'https://www.instagram.com/p/Db_rTcLnOTQ/',
                    'alt' => 'Not much to say, #shotoniphone',
                    'video' => false,
                ],
                [
                    'file' => 'images/instagram/Db4nSLztPGR.jpg',
                    'href' => 'https://www.instagram.com/reel/Db4nSLztPGR/',
                    'alt' => 'Apparently you can 👀 #shotoniphone',
                    'video' => true,
                ],
                [
                    'file' => 'images/instagram/DRsgEYLEYE3.jpg',
                    'href' => 'https://www.instagram.com/p/DRsgEYLEYE3/',
                    'alt' => 'Burna Boy “I Told Them” Album Listening Party',
                    'video' => false,
                ],
                [
                    'file' => 'images/instagram/DRXvLE_ETIJ.jpg',
                    'href' => 'https://www.instagram.com/p/DRXvLE_ETIJ/',
                    'alt' => '@jidsv “The Forever Story” Album Listening Party 2022 💿',
                    'video' => false,
                ],
                [
                    'file' => 'images/instagram/DRHwElfje7I.jpg',
                    'href' => 'https://www.instagram.com/p/DRHwElfje7I/',
                    'alt' => 'Teyana Taylor Circa 2023, digging into my archives ❗️',
                    'video' => false,
                ],
                [
                    'file' => 'images/instagram/DBPeuGauFIa.jpg',
                    'href' => 'https://www.instagram.com/p/DBPeuGauFIa/',
                    'alt' => 'it’s kinda cold outside…',
                    'video' => false,
                ],
            ],
        ];
    }

    /**
     * The strip has no page of its own: it is drawn on the home page, which is
     * where a preview of it has to be drawn too.
     *
     * @param  array<string, mixed>  $entry
     */
    public function publicPath(array $entry): ?string
    {
        return '/';
    }
}
