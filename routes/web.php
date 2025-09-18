<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/news', function () {
    return view('pages.news', ['current' => 'news']);
})->name('news.index');
