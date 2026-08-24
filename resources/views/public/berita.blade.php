@extends('layouts.public')

@section('title', 'Berita')

@section('content')

<section class="phead">
  <div class="pcontainer">
    <h1>Berita &amp; Kegiatan</h1>
    <p>Kabar dan dokumentasi kegiatan {{ $school->name }}.</p>
  </div>
</section>

<section class="psec">
  <div class="pcontainer">
    @if($posts->count())
      <div class="pgrid pgrid-3">
        @foreach($posts as $post)
          <a href="{{ route('public.berita.detail', [$school->slug, $post->slug]) }}" class="pcard">
            <div class="pcard-media">
              @if($post->thumbnail)
                <img src="{{ asset($post->thumbnail) }}" alt="{{ $post->title }}">
              @else
                <div class="pcard-media-blank"><i class="ti ti-news"></i></div>
              @endif
              @if($post->category)
                <span class="pcard-badge">{{ $post->category->name }}</span>
              @endif
            </div>
            <div class="pcard-body">
              <div class="pcard-meta">
                <i class="ti ti-calendar"></i> {{ format_date($post->published_at ?? $post->created_at) }}
              </div>
              <div class="pcard-title">{{ $post->title }}</div>
              <div class="pcard-text">{{ \Illuminate\Support\Str::limit($post->excerpt ?? strip_tags($post->content), 95) }}</div>
              <span class="pcard-more">Baca selengkapnya <i class="ti ti-arrow-right"></i></span>
            </div>
          </a>
        @endforeach
      </div>

      <div class="ppagination">{{ $posts->links() }}</div>
    @else
      <div class="pempty">Belum ada berita.</div>
    @endif
  </div>
</section>

@endsection
