@extends('layouts.public')

@section('title', 'Berita')

@section('content')

<section class="hero" style="padding:50px 0;">
  <div class="container"><h1 style="font-size:30px;">Berita & Kegiatan</h1></div>
</section>

<section class="sec">
  <div class="container">
    @if($posts->count())
      <div class="grid grid-3">
        @foreach($posts as $post)
          <a href="{{ route('public.berita.detail', [$school->slug, $post->slug]) }}" class="pcard">
            <img class="thumb" src="{{ $post->thumbnail ? asset($post->thumbnail) : 'https://placehold.co/600x400/e8f5ec/1a7a3c?text=Berita' }}" alt="">
            <div class="body">
              <div class="meta"><i class="ti ti-calendar"></i> {{ format_date($post->published_at ?? $post->created_at) }} @if($post->category) · {{ $post->category->name }} @endif</div>
              <h3>{{ $post->title }}</h3>
              <div class="ex">{{ \Illuminate\Support\Str::limit($post->excerpt ?? strip_tags($post->content), 90) }}</div>
            </div>
          </a>
        @endforeach
      </div>
      <div style="margin-top:30px;">{{ $posts->links() }}</div>
    @else
      <p style="color:var(--muted);">Belum ada berita.</p>
    @endif
  </div>
</section>

@endsection
