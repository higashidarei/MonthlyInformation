<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Item;

Route::get('/', function (\Illuminate\Http\Request $request) {
    $month = $request->query('month', now()->format('Y-m'));

    $movies = \App\Models\Item::where('type','movie')
        ->where('month_tag', $month)
        ->orderBy('start_date')->orderBy('id')
        ->paginate(6);

    return view('home', compact('movies','month'));
})->name('home');