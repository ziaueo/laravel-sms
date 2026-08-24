@extends('layouts.public')

@section('title', 'Galeri')

@section('content')

<section class="phead">
  <div class="pcontainer">
    <h1>Galeri</h1>
    <p>Dokumentasi kegiatan {{ $school->name }}.</p>
  </div>
</section>

<section class="psec">
  <div class="pcontainer">
    @if($galleries->count())
      <div class="pgrid pgrid-3">
        @foreach($galleries as $g)
          @php $cover = $g->thumbnail ?: $g->items->first()?->file_path; @endphp

          <a href="{{ route('public.galeri.detail', [$school->slug, hid($g)]) }}" class="pcard">
            <div class="pcard-media">
              @if($cover)
                <img src="{{ asset($cover) }}" alt="{{ $g->title }}">
              @else
                <div class="pcard-media-blank"><i class="ti ti-photo"></i></div>
              @endif
              <span class="pcard-badge">{{ $g->items_count }} foto</span>
            </div>
            <div class="pcard-body">
              <div class="pcard-title">{{ $g->title }}</div>
              @if($g->description)
                <div class="pcard-text">{{ \Illuminate\Support\Str::limit($g->description, 80) }}</div>
              @endif
              <span class="pcard-more">Buka album <i class="ti ti-arrow-right"></i></span>
            </div>
          </a>
        @endforeach
      </div>

      <div class="ppagination">{{ $galleries->links() }}</div>
    @else
      <div class="pempty">Belum ada galeri.</div>
    @endif
  </div>
</section>

@endsection
