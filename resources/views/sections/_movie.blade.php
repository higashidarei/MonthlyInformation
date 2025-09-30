
@extends('layouts.app')

@section('content')
<section id="movie" class="movie">
  <div class="l-inner">
    <h2 class="heading-A">Movie</h2>

    <ul class="card-grid">
      @forelse ($movies as $m)
        @php
          $img = $m->image_url ?: asset('images/no-image.jpg');
          $date = $m->start_date ? \Carbon\Carbon::parse($m->start_date)->format('Y.m.d') : '公開日未定';
          $country = '日本';
          $category = 'カテゴリ未設定';
          $title = $m->title ?? '（無題）';
          $link = $m->detail_url ?: 'https://www.themoviedb.org/movie/' . $m->source_id;
        @endphp

        <li class="card">
          <a href="{{ $link }}" target="_blank" rel="noopener noreferrer">
            <div class="card__thumb">
              <img src="{{ $img }}" alt="{{ e($title) }}">
            </div>
            <h3 class="card__title">{{ $title }}</h3>
            <ul class="card__meta">
              <li>{{ $date }}</li>
              <li>{{ $country }}</li>
              <li>{{ $category }}</li>
            </ul>
          </a>
        </li>
      @empty
        <li>該当月の映画がありません。</li>
      @endforelse
    </ul>

    {{-- ページネーション --}}
{{ $movies->onEachSide(1)->withQueryString()->links('components.pagination') }}
  </div>
</section>
@endsection
