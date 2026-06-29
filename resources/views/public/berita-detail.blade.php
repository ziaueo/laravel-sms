@extends('layouts.public')

@section('title', $post->title)

@section('content')

<section class="sec">
  <div class="container" style="max-width:780px;">
    <a href="{{ route('public.berita', $school->slug) }}" style="color:var(--pri-d);font-size:14px;"><i class="ti ti-arrow-left"></i> Kembali ke Berita</a>
    <h1 style="font-size:30px;margin:14px 0 8px;">{{ $post->title }}</h1>
    <div style="color:var(--muted);font-size:13px;margin-bottom:18px;">
      <i class="ti ti-calendar"></i> {{ format_date($post->published_at ?? $post->created_at) }}
      @if($post->category) · {{ $post->category->name }} @endif
      @if($post->createdBy) · oleh {{ $post->createdBy->name }} @endif
    </div>
    @if($post->thumbnail)
      <img src="{{ asset($post->thumbnail) }}" style="width:100%;border-radius:14px;margin-bottom:20px;">
    @endif
    <div style="font-size:15.5px;line-height:1.8;color:#374151;">{!! nl2br(e($post->content)) !!}</div>
  </div>
</section>

@if($related->count())
<section class="sec" style="background:#fff;border-top:1px solid var(--line);">
  <div class="container">
    <div class="sec-title" style="font-size:20px;">Berita Lainnya</div>
    <div class="grid grid-3" style="margin-top:18px;">
      @foreach($related as $r)
        <a href="{{ route('public.berita.detail', [$school->slug, $r->slug]) }}" class="pcard">
          <img class="thumb" src="{{ $r->thumbnail ? asset($r->thumbnail) : 'https://placehold.co/600x400/e8f5ec/1a7a3c?text=Berita' }}" alt="">
          <div class="body"><h3 style="font-size:14px;">{{ $r->title }}</h3></div>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

@endsection
