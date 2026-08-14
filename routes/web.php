<?php

use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    $imageName = 'default.jpeg';
    $url = asset('images/products/' . $imageName);
    $appUrl = config('app.url');
    $basePath = public_path('images/products/' . $imageName);
    $fileExists = file_exists($basePath);

    return response()->json([
        'app_url' => $appUrl,
        'asset_url' => $url,
        'file_path' => $basePath,
        'file_exists' => $fileExists,
        'file_size' => $fileExists ? filesize($basePath) : null,
    ]);
});

Route::get('/{any?}', function () {
    return view('master');
})->where('any', '.*');
