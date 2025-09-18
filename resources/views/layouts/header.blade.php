<header class="header">
  <div class="header__inner">
    <a href="{{ route('home') }}" class="header__logo">Site</a>
    <ul class="header__nav">
      {{-- <a href="{{ route('home') }}" class="{{ ($current==='home') ? 'is-active' : '' }}">Home</a> --}}
      <li><a href="{{ route('home') }}#movie" class="{{ $current === 'movie' ? 'is-active' : '' }}">Movie</a></li>
      <li><a href="{{ route('home') }}#book" class="{{ $current === 'book' ? 'is-active' : '' }}">Book</a></li>
      <li><a href="{{ route('home') }}#exhibition" class="{{ $current === 'exhibition' ? 'is-active' : '' }}">Exhibition</a></li>
    </ul>
  </div>
</header>
