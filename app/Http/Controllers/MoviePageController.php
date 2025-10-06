<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MoviePageController extends Controller
{
  public function index(Request $request)
  {
    $month = $request->query('month', now()->format('Y-m'));
    $monthsBack = (int)$request->query('months_back', 2);
    $monthsBack = max(0, min($monthsBack, 6));
    $per = 24;

    if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
      abort(422, 'Invalid month format (YYYY-MM).');
    }

    // 期間（例：2025-10 を基準、過去2ヶ月 → 8/1〜10/31）
    [$y, $m] = explode('-', $month);
    $end   = Carbon::createFromDate((int)$y, (int)$m, 1)->endOfMonth()->endOfDay();
    $start = (clone $end)->startOfMonth()->subMonthsNoOverflow($monthsBack)->startOfMonth();

    // 保険用：月タグの配列（['2025-08','2025-09','2025-10'] のような配列）
    $monthTags = [];
    $cursor = (clone $start)->startOfMonth();
    while ($cursor->lte($end)) {
      $monthTags[] = $cursor->format('Y-m');
      $cursor->addMonthNoOverflow();
    }

    $movies = Item::query()
      ->where('type', 'movie')
      ->where('source', 'tmdb')
      ->where(function ($q) use ($start, $end, $monthTags) {
        $q->whereBetween('start_date', [$start, $end])
          ->orWhereIn('month_tag', $monthTags);
      })
      ->orderByRaw('start_date IS NULL, start_date ASC')
      ->orderBy('id')
      ->paginate($per);

    return view('movies.index', compact('movies', 'month'));
  }
}
