<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
//Route::get('/{any}', function () {
//    return view('app');
//})->where('any', '^(?!firebase-messaging-sw\.js$).*');

