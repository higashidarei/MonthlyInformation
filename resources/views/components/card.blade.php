{{-- @props([
    'items' => [],
    'variant' => 'default',
]) --}}

{{-- <ul class="card-grid card-grid--{{ $variant }}">
  @forelse($items as $item)
    <li class="card">
      <a href="{{ $item['url'] ?? '#' }}" class="card__link">
        @if (!empty($item['image']))
          <img src="{{ $item['image'] }}" alt="" class="card__thumb">
        @else
          <img src="{{ $item['image'] }}" alt="" class="card__thumb">
        @endif
        <h3 class="card__title">{{ $item['title'] ?? 'No title' }}</h3>
        @isset($item['meta'])
          <p class="card__meta">{{ $item['meta'] }}</p>
        @endisset
      </a>
    </li>
  @empty
    <li class="card card--empty">データがありません</li>
  @endforelse
</ul> --}}

<ul class="card-grid">
  <li class="card">
    <a href="">
      <div class="card__thumb">
        <img src="{{ asset('images/no-image.jpg') }}" alt="">
      </div>
      <h3 class="card__title">作品タイトル</h3>
      <ul class="card__meta">
        <li>2025.09.09</li>
        <li>日本</li>
        <li>スリラー/ホラー</li>
      </ul>
    </a>
  </li>
  <li class="card">
    <a href="">
      <div class="card__thumb">
        <img src="{{ asset('images/no-image.jpg') }}" alt="">
      </div>
      <h3 class="card__title">作品タイトル</h3>
      <ul class="card__meta">
        <li>公開日</li>
        <li>アメリカ</li>
        <li>カテゴリー</li>
      </ul>
    </a>
  </li>
  <li class="card">
    <a href="">
      <div class="card__thumb">
        <img src="{{ asset('images/no-image.jpg') }}" alt="">
      </div>
      <h3 class="card__title">作品タイトル</h3>
      <ul class="card__meta">
        <li>公開日</li>
        <li>国</li>
        <li>カテゴリー</li>
      </ul>
    </a>
  </li>
  <li class="card">
    <a href="">
      <div class="card__thumb">
        <img src="{{ asset('images/no-image.jpg') }}" alt="">
      </div>
      <h3 class="card__title">作品タイトル</h3>
      <ul class="card__meta">
        <li>公開日</li>
        <li>国</li>
        <li>カテゴリー</li>
      </ul>
    </a>
  </li>
</ul>
