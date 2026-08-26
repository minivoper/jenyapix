<?php

use App\Support\Portfolio;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', [
        'galleries' => Portfolio::galleries(),
        'instagram' => Portfolio::instagram(),
    ]);
})->name('home');

Route::get('/work/{slug}', function (string $slug) {
    $gallery = Portfolio::gallery($slug);
    abort_unless($gallery, 404);

    return view('work.show', [
        'gallery' => $gallery,
        'galleries' => Portfolio::galleries(),
    ]);
})->name('work.show');
