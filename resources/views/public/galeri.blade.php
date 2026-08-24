@extends('layouts.public')

@section('title', 'Galeri')

@section('content')

<section class="hero" style="padding:50px 0;">
  <div class="container"><h1 style="font-size:30px;">Galeri</h1></div>
</section>

<section class="sec">
  <div class="container">
    <div class="grid grid-3">
      @forelse($galleries as $g)
        @php $cover = $g->thumbnail ?: $g->items->first()?->file_path; @endphp

        <a href="{{ route('public.galeri.detail', [$school->slug, hid($g)]) }}" class="pcard">
          @if($cover)
            <img class="thumb" src="{{ asset($cover) }}" alt="{{ $g->title }}">
          @else
            <div style="height:180px;display:flex;align-items:center;justify-content:center;
                        background:#e8f5ec;color:var(--pri-d);font-size:34px;">
              <i class="ti ti-photo"></i>
            </div>
          @endif

          <div class="body">
            <h3 style="font-size:15px;">{{ $g->title }}</h3>
            <div class="meta" style="margin-bottom:0;">{{ $g->items_count }} foto</div>
          </div>
        </a>
      @empty
        <p style="color:var(--muted);">Belum ada galeri.</p>
      @endforelse
    </div>

    <div style="margin-top:24px;">{{ $galleries->links() }}</div>
  </div>
</section>

@endsection
