<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class MoviePageController extends Controller
{
  public function index(Request $request)
  {
    $month = $request->query('month', now()->format('Y-m'));

    $movies = Item::where('type', 'movie')
      ->where('month_tag', $month)
      ->orderBy('start_date')
      ->orderBy('id')
      ->paginate(24);

    return view('movies.index', compact('movies', 'month'));
  }
}
