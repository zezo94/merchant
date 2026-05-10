@if ($paginator->hasPages())
    <nav class="d-flex flex-column gap-2 mt-3" role="navigation" aria-label="Pagination Navigation">
        <div class="d-flex justify-content-center flex-wrap gap-2 merchants-mobile-pagination">
            @if ($paginator->onFirstPage())
                <span class="btn btn-sm btn-outline-secondary disabled">السابق</span>
            @else
                <a class="btn btn-sm btn-outline-secondary" href="{{ $paginator->previousPageUrl() }}" rel="prev">السابق</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="btn btn-sm btn-light disabled">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="btn btn-sm btn-primary active" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="btn btn-sm btn-outline-primary" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a class="btn btn-sm btn-outline-secondary" href="{{ $paginator->nextPageUrl() }}" rel="next">التالي</a>
            @else
                <span class="btn btn-sm btn-outline-secondary disabled">التالي</span>
            @endif
        </div>

        <form method="GET" action="{{ url()->current() }}" class="merchants-page-jump-form">
            @foreach(request()->except('page') as $key => $value)
                @if(is_array($value))
                    @foreach($value as $item)
                        <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach

            <div class="merchants-page-jump-box">
                <label for="jump_page" class="form-label mb-0">اذهب إلى صفحة</label>
                <input
                    id="jump_page"
                    type="number"
                    name="page"
                    min="1"
                    max="{{ $paginator->lastPage() }}"
                    value="{{ $paginator->currentPage() }}"
                    class="form-control form-control-sm merchants-page-jump-input"
                >
                <button type="submit" class="btn btn-sm btn-primary">انتقال</button>
            </div>
        </form>

        <div class="text-center text-muted small">
            صفحة {{ $paginator->currentPage() }} من {{ $paginator->lastPage() }} — إجمالي {{ $paginator->total() }} سجل
        </div>
    </nav>
@endif
