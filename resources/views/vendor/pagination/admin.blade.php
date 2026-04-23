@if ($paginator->total() > 0)
    <nav class="pagination-nav" aria-label="Pagination">
        <div class="pagination-nav__info">
            Showing
            <strong>{{ $paginator->firstItem() }}</strong>
            –
            <strong>{{ $paginator->lastItem() }}</strong>
            of
            <strong>{{ $paginator->total() }}</strong>
        </div>
        @if ($paginator->hasPages())
            <ul class="pagination-nav__list">
                @if ($paginator->onFirstPage())
                    <li><span class="pagination-nav__disabled" aria-disabled="true">Previous</span></li>
                @else
                    <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">Previous</a></li>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li><span class="pagination-nav__ellipsis">{{ $element }}</span></li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li><span class="pagination-nav__current" aria-current="page">{{ $page }}</span></li>
                            @else
                                <li><a href="{{ $url }}">{{ $page }}</a></li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a></li>
                @else
                    <li><span class="pagination-nav__disabled" aria-disabled="true">Next</span></li>
                @endif
            </ul>
        @endif
    </nav>
@endif
