@php
  // サイト共通デフォルト
  $defaults = [
    'title'       => config('app.name', 'My Site'),
    'description' => 'サイトのデフォルト説明文をここに。約120字を目安に。',
    'keywords'    => 'keyword1, keyword2',
    'canonical'   => url()->current(),
    'robots'      => 'index,follow',
    'og' => [
      'type'      => 'website',
      'site_name' => config('app.name', 'My Site'),
      'image'     => asset('images/ogp.jpg'), // public/images/ogp.jpg 想定
    ],
  ];

  // ページ側で用意された $meta をマージ（なければ空配列）
  $meta = array_replace_recursive($defaults, $meta ?? []);

  // 最終タイトルを<title>にも使う
  $finalTitle = $meta['title'] ?? $defaults['title'];
@endphp

<title>{{ $finalTitle }}</title>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="description" content="{{ $meta['description'] }}">
@if(!empty($meta['keywords']))<meta name="keywords" content="{{ $meta['keywords'] }}">@endif
<link rel="canonical" href="{{ $meta['canonical'] }}">
<meta name="robots" content="{{ $meta['robots'] }}">

{{-- Open Graph --}}
<meta property="og:title" content="{{ $finalTitle }}">
<meta property="og:description" content="{{ $meta['description'] }}">
<meta property="og:type" content="{{ $meta['og']['type'] }}">
<meta property="og:site_name" content="{{ $meta['og']['site_name'] }}">
<meta property="og:url" content="{{ $meta['canonical'] }}">
<meta property="og:image" content="{{ $meta['og']['image'] }}">
