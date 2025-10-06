<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Item;
use Carbon\Carbon;

class IngestMonthly extends Command
{
    protected $signature = 'ingest:monthly {--month=} {--months-back=2}';
    protected $description = 'Fetch and upsert JP now_playing items within a month window';

    protected function http()
    {
        return \Illuminate\Support\Facades\Http::withOptions([
            'verify'  => base_path('storage/certs/cacert.pem'),
            'timeout' => 15,
        ]);
    }

    public function handle()
    {
        // 基準月（YYYY-MM）と遡り月数
        $baseMonth   = $this->option('month') ?? now()->format('Y-m');
        $monthsBack  = max(0, min((int)$this->option('months-back'), 6)); // 上限はお好みで

        if (!preg_match('/^\d{4}-\d{2}$/', $baseMonth)) {
            $this->error('Invalid --month format. Use YYYY-MM');
            return Command::FAILURE;
        }

        [$y, $m] = explode('-', $baseMonth);
        // 期間: (基準月の月初 - monthsBackヶ月) 〜 基準月の月末
        $end   = Carbon::createFromDate((int)$y, (int)$m, 1)->endOfMonth()->endOfDay();
        $start = (clone $end)->startOfMonth()->subMonthsNoOverflow($monthsBack)->startOfMonth();

        // 1) ジャンル辞書
        $genreMap = [];
        $g = $this->http()->get('https://api.themoviedb.org/3/genre/movie/list', [
            'api_key'  => env('TMDB_API_KEY'),
            'language' => 'ja-JP',
        ]);
        if ($g->successful()) {
            foreach ($g->json('genres', []) as $row) {
                $genreMap[$row['id']] = $row['name'];
            }
        } else {
            $this->warn('ジャンル辞書の取得に失敗: '.$g->status().'（処理続行）');
        }

        // 2) 日本の上映中をページングで取得 → 期間内のみ保存
        $this->info(sprintf(
            'Fetching now_playing (JP) for window %s ~ %s ...',
            $start->toDateString(), $end->toDateString()
        ));

        $page = 1;
        $totalPages = 1;

        do {
            $np = $this->http()->get('https://api.themoviedb.org/3/movie/now_playing', [
                'api_key'  => env('TMDB_API_KEY'),
                'language' => 'ja-JP',
                'region'   => 'JP',
                'page'     => $page,
            ]);

            if (!$np->successful()) {
                $this->error('now_playing取得失敗: '.$np->status());
                break;
            }

            $payload    = $np->json();
            $totalPages = max(1, (int)($payload['total_pages'] ?? 1));

            foreach (($payload['results'] ?? []) as $r) {
                $release = $r['release_date'] ?? null;
                if (!$release || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $release)) {
                    continue;
                }

                $rd = Carbon::parse($release);
                // 期間外はスキップ（＝10月基準で8〜10月等）
                if (!$rd->betweenIncluded($start, $end)) {
                    continue;
                }

                // 保存する month_tag は「実際の公開月」
                $tag = $rd->format('Y-m');

                $countryCode = (!empty($r['origin_country']) && is_array($r['origin_country']))
                    ? ($r['origin_country'][0] ?? null)
                    : null;

                $countryLabel = match ($countryCode) {
                    'JP' => '日本',
                    'US' => 'アメリカ',
                    'GB' => 'イギリス',
                    'FR' => 'フランス',
                    'KR' => '韓国',
                    'CN' => '中国',
                    default => $countryCode,
                };

                $genreNames = collect($r['genre_ids'] ?? [])
                    ->map(fn($id) => $genreMap[$id] ?? null)
                    ->filter()
                    ->implode(', ');

                Item::updateOrCreate(
                    [
                        'source'    => 'tmdb',
                        'source_id' => (string)$r['id'],
                        'month_tag' => $tag, // ← 実公開月でタグ
                    ],
                    [
                        'type'        => 'movie',
                        'title'       => $r['title'] ?? $r['original_title'] ?? '（無題）',
                        'description' => $r['overview'] ?? null,
                        'image_url'   => !empty($r['poster_path'])
                            ? ('https://image.tmdb.org/t/p/w500' . $r['poster_path'])
                            : null,
                        'detail_url'  => !empty($r['id'])
                            ? ('https://www.themoviedb.org/movie/' . $r['id'])
                            : null,
                        'start_date'  => $release,
                        'country'     => $countryLabel,
                        'genre_names' => $genreNames,
                    ]
                );
            }

            $this->info("now_playing page {$page}/{$totalPages} 取り込み完了");
            $page++;
        } while ($page <= $totalPages);

        $this->info("取り込み完了: window {$start->toDateString()} ~ {$end->toDateString()}");
        return Command::SUCCESS;
    }
}
