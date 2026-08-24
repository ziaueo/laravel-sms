@extends('layouts.public')

@section('title', $album->title)

@section('content')

<section class="phead">
  <div class="pcontainer">
    <h1>{{ $album->title }}</h1>
    @if($album->description)
      <p>{{ $album->description }}</p>
    @endif
  </div>
</section>

<section class="psec">
  <div class="pcontainer">
    <div class="psec-head">
      <div class="psec-sub" style="margin:0;">{{ $album->items->count() }} foto</div>
    </div>

    @if($album->items->count())
      <div class="pgrid pgrid-4">
        @foreach($album->items as $item)
          @if($item->file_path)
            <img src="{{ asset($item->file_path) }}" alt="{{ $item->caption }}"
                 style="width:100%;height:200px;object-fit:cover;border-radius:var(--radius-lg);box-shadow:var(--shadow-card);"
                 data-lightbox="galeri-{{ hid($album) }}">
          @endif
        @endforeach
      </div>
    @else
      <div class="pempty">Album ini belum berisi foto.</div>
    @endif
  </div>
</section>

@endsection
