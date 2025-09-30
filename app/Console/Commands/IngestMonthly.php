<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Item;

use Illuminate\Support\Str;

class IngestMonthly extends Command
{
    protected $signature = 'ingest:monthly {--month=}';
    protected $description = 'Fetch and upsert monthly items';
    protected function http()
    {
        return \Illuminate\Support\Facades\Http::withOptions([
            'verify'  => base_path('storage/certs/cacert.pem'),
            'timeout' => 15,
        ]);
    }


    public function handle()
    {
        $month = $this->option('month') ?? now()->format('Y-m');
        [$y, $m] = explode('-', $month);
        $gte = "{$y}-{$m}-01";
        $lte = date('Y-m-t', strtotime($gte));

        // 1) ジャンル辞書を取得
        $genreMap = [];
        $g = $this->http()->get('https://api.themoviedb.org/3/genre/movie/list', [
            'api_key'  => env('TMDB_API_KEY'),
            'language' => 'ja-JP',
        ]);
        if ($g->successful()) {
            foreach ($g->json('genres', []) as $row) {
                $genreMap[$row['id']] = $row['name']; // [28 => "アクション"] など
            }
        }

        // 2) 今月公開映画
        $res = $this->http()->get('https://api.themoviedb.org/3/discover/movie', [
            'api_key'   => env('TMDB_API_KEY'),
            'language'  => 'ja-JP',
            'region'    => 'JP',
            'primary_release_date.gte' => $gte,
            'primary_release_date.lte' => $lte,
            'sort_by' => 'primary_release_date.asc',
            'page' => 1, // 必要なら total_pages を見てループ
        ]);

        if ($res->successful()) {
            foreach ($res->json('results', []) as $r) {
                // 公開日
                $start = $r['primary_release_date'] ?? $r['release_date'] ?? null;

                // 国（production_countries は /movie/{id} で詳細を取らないと出ないことが多いので、origin_country で代替）
                $country = null;
                if (!empty($r['origin_country']) && is_array($r['origin_country'])) {
                    $country = $r['origin_country'][0] ?? null; // "JP" や "US"
                }
                // 2文字コード→表示名が欲しければ自前マップ（最低限"JP"なら"日本"など）
                $countryLabel = match ($country) {
                    'JP' => '日本',
                    'US' => 'アメリカ',
                    'GB' => 'イギリス',
                    'FR' => 'フランス',
                    'KR' => '韓国',
                    'CN' => '中国',
                    default => $country, // わからなければそのままコードを表示
                };

                // ジャンル名
                $genreNames = collect($r['genre_ids'] ?? [])
                    ->map(fn($id) => $genreMap[$id] ?? null)
                    ->filter()
                    ->implode(', '); // "スリラー, ホラー"

                \App\Models\Item::updateOrCreate(
                    ['source' => 'tmdb', 'source_id' => (string)$r['id'], 'month_tag' => $month],
                    [
                        'type'        => 'movie',
                        'title'       => $r['title'] ?? $r['original_title'] ?? '（無題）',
                        'description' => $r['overview'] ?? null,
                        'image_url'   => !empty($r['poster_path']) ? ('https://image.tmdb.org/t/p/w500' . $r['poster_path']) : null,
                        'detail_url'  => !empty($r['id']) ? ('https://www.themoviedb.org/movie/' . $r['id']) : null,
                        'start_date'  => $start,
                        'country'     => $countryLabel,     // ★ ここに保存
                        'genre_names' => $genreNames,       // ★ ここに保存
                    ]
                );
            }
            $this->info("TMDb取り込みOK（国/ジャンル付与）: {$month}");
        } else {
            $this->error('TMDb取得失敗: ' . $res->status());
        }
    }
}
