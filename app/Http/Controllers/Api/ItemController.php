<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;

class ItemController extends Controller
{
 public function index(\Illuminate\Http\Request $req)
{
    // 基準の月（YYYY-MM）。指定なければ今月
    $month = $req->query('month', now()->format('Y-m'));

    // 何ヶ月遡るか。デフォ2、最大6まで許容（必要なら調整）
    $monthsBack = (int)$req->query('months_back', 2);
    $monthsBack = max(0, min($monthsBack, 6));

    $type = $req->query('type');
    $per  = min((int)$req->query('per', 30), 100);

    if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
        return response()->json([
            'message' => 'Invalid month format. Use YYYY-MM.'
        ], 422, [], JSON_UNESCAPED_UNICODE);
    }

    [$y, $m] = explode('-', $month);

    $end  = \Carbon\Carbon::createFromDate((int)$y, (int)$m, 1)->endOfMonth()->endOfDay();
    $start = (clone $end)->startOfMonth()->subMonthsNoOverflow($monthsBack)->startOfMonth();

    $q = \App\Models\Item::query()
        ->where('source', 'tmdb')
        ->when($type, fn($q) => $q->where('type', $type))
        // month_tag ではなく start_date の範囲で直近数ヶ月を抽出
        ->whereBetween('start_date', [$start, $end])
        // null を最後に（保険）
        ->orderByRaw('start_date IS NULL, start_date ASC')
        ->orderBy('id');

    $paginated = $q->paginate($per);

    return response()->json($paginated, 200, [], JSON_UNESCAPED_UNICODE);
}



}
