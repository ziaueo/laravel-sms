@if($paginator->hasPages())
<div class="pagination-wrap">
  <div class="pagination-info">
    Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}
    dari {{ $paginator->total() }} data
  </div>
  <div class="pagination-links">
    {{-- Previous --}}
    @if($paginator->onFirstPage())
      <span class="page-btn disabled"><i class="ti ti-chevron-left"></i></span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}" class="page-btn">
        <i class="ti ti-chevron-left"></i>
      </a>
    @endif

    {{-- Pages --}}
    @foreach($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
      @if($page == $paginator->currentPage())
        <span class="page-btn active">{{ $page }}</span>
      @else
        <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
      @endif
    @endforeach

    {{-- Next --}}
    @if($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}" class="page-btn">
        <i class="ti ti-chevron-right"></i>
      </a>
    @else
      <span class="page-btn disabled"><i class="ti ti-chevron-right"></i></span>
    @endif
  </div>
</div>
@endif
