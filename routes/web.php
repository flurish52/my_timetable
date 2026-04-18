<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');



Route::post('/store-token', function () {
    $file = storage_path('app/fcm_tokens.json');

    $tokens = file_exists($file)
        ? json_decode(file_get_contents($file), true)
        : [];

    $newToken = request('token');

    if (!in_array($newToken, $tokens)) {
        $tokens[] = $newToken;
    }

    file_put_contents($file, json_encode($tokens));

    return response()->json(['ok' => true]);
});
