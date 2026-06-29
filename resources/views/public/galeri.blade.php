@extends('layouts.public')

@section('title', 'Galeri')

@section('content')

<section class="hero" style="padding:50px 0;">
  <div class="container"><h1 style="font-size:30px;">Galeri</h1></div>
</section>

<section class="sec">
  <div class="container">
    @forelse($galleries as $g)
      <div style="margin-bottom:34px;">
        <div class="sec-title" style="font-size:20px;">{{ $g->title }}</div>
        @if($g->description)<div class="sec-sub">{{ $g->description }}</div>@endif
        <div class="grid grid-3">
          @forelse($g->items as $item)
            @if($item->file_path)
              <img class="galimg" src="{{ asset($item->file_path) }}" alt="{{ $item->caption }}">
            @endif
          @empty
            @if($g->thumbnail)<img class="galimg" src="{{ asset($g->thumbnail) }}">@endif
          @endforelse
        </div>
      </div>
    @empty
      <p style="color:var(--muted);">Belum ada galeri.</p>
    @endforelse
    <div>{{ $galleries->links() }}</div>
  </div>
</section>

@endsection
