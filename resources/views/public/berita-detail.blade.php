@extends('layouts.public')

@section('title', $post->title)

@section('content')

<section class="psec">
  <div class="pcontainer">
    <div class="particle">
      <a href="{{ route('public.berita', $school->slug) }}" class="pcard-more" style="margin:0 0 16px;">
        <i class="ti ti-arrow-left"></i> Kembali ke Berita
      </a>

      <h1 style="font-size:clamp(24px,3.4vw,34px);font-weight:800;line-height:1.25;letter-spacing:-.3px;">
        {{ $post->title }}
      </h1>

      <div class="pcard-meta" style="margin:12px 0 22px;">
        <i class="ti ti-calendar"></i> {{ format_date($post->published_at ?? $post->created_at) }}
        @if($post->category) <span>·</span> {{ $post->category->name }} @endif
        @if($post->createdBy) <span>·</span> oleh {{ $post->createdBy->name }} @endif
      </div>

      @if($post->thumbnail)
        <img src="{{ asset($post->thumbnail) }}" alt="{{ $post->title }}"
             style="width:100%;border-radius:var(--radius-xl);box-shadow:var(--shadow-md);margin-bottom:26px;"
             data-lightbox="berita">
      @endif

      <div>{!! nl2br(e($post->content)) !!}</div>
    </div>
  </div>
</section>

@if($related->count())
  <section class="psec psec-alt">
    <div class="pcontainer">
      <div class="psec-head">
        <span class="psec-eyebrow">Lainnya</span>
        <div class="psec-title">Berita Lainnya</div>
      </div>

      <div class="pgrid pgrid-3">
        @foreach($related as $r)
          <a href="{{ route('public.berita.detail', [$school->slug, $r->slug]) }}" class="pcard">
            <div class="pcard-media">
              @if($r->thumbnail)
                <img src="{{ asset($r->thumbnail) }}" alt="{{ $r->title }}">
              @else
                <div class="pcard-media-blank"><i class="ti ti-news"></i></div>
              @endif
            </div>
            <div class="pcard-body">
              <div class="pcard-meta">
                <i class="ti ti-calendar"></i> {{ format_date($r->published_at ?? $r->created_at) }}
              </div>
              <div class="pcard-title" style="font-size:var(--font-size-md);">{{ $r->title }}</div>
            </div>
          </a>
        @endforeach
      </div>
    </div>
  </section>
@endif

@endsection
