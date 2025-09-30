<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;

class ItemController extends Controller
{
 public function index(\Illuminate\Http\Request $req)
{

    $month = $req->query('month');               // 例: 2025-09
    $type  = $req->query('type');                // movie|book|exhibition
    $per   = min((int)$req->query('per', 30), 100);

    $q = \App\Models\Item::query();

    if ($month) $q->where('month_tag', $month);
    if ($type)  $q->where('type', $type);

    $q->orderBy('start_date')->orderBy('id');    // start_dateがnullでも安定

    $paginated = $q->paginate($per);

    // Unicodeをエスケープせずに返す（日本語が \uXXXX にならない）
    return response()->json($paginated, 200, [], JSON_UNESCAPED_UNICODE);
}

}
