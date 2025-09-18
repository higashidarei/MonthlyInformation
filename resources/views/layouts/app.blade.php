<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  {{-- 共通の meta タグ --}}
  @include('parts.meta')
  @stack('head')

  {{-- 共通の CSS/JS --}}
  @vite(['resources/scss/style.scss','resources/js/app.js'])
</head>
<body class="@yield('body_class')">
   @include('layouts.header', ['current' => $current ?? null])

  <main>
    @yield('content')
  </main>

  @include('layouts.footer', ['current' => $current ?? null])

  @stack('scripts') {{-- ページごとの追加JS --}}
</body>
</html>
