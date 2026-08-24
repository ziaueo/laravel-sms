@extends('layouts.public')

@section('title', $album->title)

@section('content')

<section class="hero" style="padding:50px 0;">
  <div class="container">
    <h1 style="font-size:30px;">{{ $album->title }}</h1>
    @if($album->description)
      <p>{{ $album->description }}</p>
    @endif
  </div>
</section>

<section class="sec">
  <div class="container">
    <div class="sec-sub">{{ $album->items->count() }} foto</div>

    <div class="grid grid-3">
      @forelse($album->items as $item)
        @if($item->file_path)
          <img class="galimg" src="{{ asset($item->file_path) }}" alt="{{ $item->caption }}"
               data-lightbox="galeri-{{ hid($album) }}">
        @endif
      @empty
        <p style="color:var(--muted);">Album ini belum berisi foto.</p>
      @endforelse
    </div>
  </div>
</section>

@endsection
