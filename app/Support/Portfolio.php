<?php

namespace App\Support;

class Portfolio
{
    public static function galleries(): array
    {
        $path = base_path('content/galleries.json');

        return json_decode(file_get_contents($path), true) ?? [];
    }

    public static function gallery(string $slug): ?array
    {
        foreach (self::galleries() as $gallery) {
            if ($gallery['slug'] === $slug) {
                return $gallery;
            }
        }

        return null;
    }

    public static function instagram(): array
    {
        $path = base_path('content/instagram.json');

        return json_decode(file_get_contents($path), true) ?? [
            'handle' => 'jenyapix',
            'url' => 'https://www.instagram.com/jenyapix/',
            'posts' => [],
        ];
    }
}
