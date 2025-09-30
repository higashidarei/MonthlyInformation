<style>
  .pagenav {
    margin: 32px 0;
    text-align: center;
  }

  .pagenav__list {
    display: inline-flex;
    gap: 8px;
    padding: 0;
    margin: 0;
    list-style: none;
  }

  .pagenav__item {}

  .pagenav__link {
    display: inline-block;
    min-width: 40px;
    height: 40px;
    line-height: 40px;
    padding: 0 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
  }

  .pagenav__item a.pagenav__link:hover {
    background: #f3f4f6;
  }

  .pagenav__item.is-active .pagenav__link {
    background: #111827;
    color: #fff;
    border-color: #111827;
  }

  .pagenav__item.is-disabled .pagenav__link {
    color: #9ca3af;
    border-color: #e5e7eb;
    pointer-events: none;
  }

  .pagenav__item.is-ellipsis .pagenav__link {
    border-color: transparent;
  }
</style>
@if ($paginator->hasPages())
  <nav class="pagenav" role="navigation" aria-label="Pagination">
    <ul class="pagenav__list">
      {{-- Prev --}}
      @if ($paginator->onFirstPage())
        <li class="pagenav__item is-disabled" aria-disabled="true" aria-label="前のページ">
          <span class="pagenav__link">&laquo;</span>
        </li>
      @else
        <li class="pagenav__item">
          <a class="pagenav__link" href="{{ $paginator->previousPageUrl() }}" rel="prev"
            aria-label="前のページ">&laquo;</a>
        </li>
      @endif

      {{-- Numbers --}}
      @foreach ($elements as $element)
        @if (is_string($element))
          <li class="pagenav__item is-ellipsis" aria-disabled="true"><span
              class="pagenav__link">{{ $element }}</span></li>
        @endif

        @if (is_array($element))
          @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
              <li class="pagenav__item is-active" aria-current="page"><span
                  class="pagenav__link">{{ $page }}</span></li>
            @else
              <li class="pagenav__item"><a class="pagenav__link" href="{{ $url }}">{{ $page }}</a></li>
            @endif
          @endforeach
        @endif
      @endforeach

      {{-- Next --}}
      @if ($paginator->hasMorePages())
        <li class="pagenav__item">
          <a class="pagenav__link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="次のページ">&raquo;</a>
        </li>
      @else
        <li class="pagenav__item is-disabled" aria-disabled="true" aria-label="次のページ">
          <span class="pagenav__link">&raquo;</span>
        </li>
      @endif
    </ul>
  </nav>
@endif
