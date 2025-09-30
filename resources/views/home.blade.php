@extends('layouts.app')

@php
  $meta = [
      'title' => 'ホーム | ' . config('app.name'),
      'description' => 'ホームページの説明文。検索結果で魅力的に見せたい要約文を入れます。',
      'canonical' => route('home'),
      'keywords' => 'ホーム,キーワード2,キーワード3',
  ];
  // $current = 'movie'; // これを渡すことでHomeにis-activeが付く
@endphp

@push('head')
  {{-- このページだけの追加CSSやmetaがあれば --}}
@endpush

@section('body_class', 'page-home')

@section('content')
 @include('sections._movie', ['movies' => $movies, 'month' => $month])
 @include('sections._book')
 @include('sections._exhibition')
@endsection


@push('scripts')
  <script>
    console.log('home only');
  </script>
@endpush
