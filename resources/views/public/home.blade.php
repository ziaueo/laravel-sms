@extends('layouts.public')

@section('title', 'Beranda')

@section('content')

{{-- HERO --}}
<section class="hero">
  <div class="container">
    <h1>{{ $school->profile->tagline ?? 'Selamat Datang di ' . $school->name }}</h1>
    <p>{{ $school->profile->description ?? 'Membentuk generasi cerdas, berkarakter, dan berakhlak mulia.' }}</p>
    <div class="acts">
      <a href="{{ route('public.ppdb', $school->slug) }}" class="pbtn"><i class="ti ti-user-plus"></i> Daftar Sekarang</a>
      <a href="{{ route('public.profil', $school->slug) }}" class="pbtn pbtn-ghost">Tentang Kami</a>
    </div>
  </div>
</section>

{{-- PENGUMUMAN PUBLIK --}}
@if($announcements->count())
<section class="sec" style="padding-bottom:0;">
  <div class="container">
    @foreach($announcements as $a)
      <div class="palert"><i class="ti ti-speakerphone"></i> <strong>{{ $a->title }}</strong> — {{ \Illuminate\Support\Str::limit(strip_tags($a->content), 120) }}</div>
    @endforeach
  </div>
</section>
@endif

{{-- BERITA TERBARU --}}
<section class="sec">
  <div class="container">
    <div class="sec-title">Berita & Kegiatan</div>
    <div class="sec-sub">Kabar terbaru dari {{ $school->name }}</div>
    @if($posts->count())
      <div class="grid grid-3">
        @foreach($posts as $post)
          <a href="{{ route('public.berita.detail', [$school->slug, $post->slug]) }}" class="pcard">
            <img class="thumb" src="{{ $post->thumbnail ? asset($post->thumbnail) : 'https://placehold.co/600x400/e8f5ec/1a7a3c?text=' . urlencode($school->name) }}" alt="">
            <div class="body">
              <div class="meta"><i class="ti ti-calendar"></i> {{ format_date($post->published_at ?? $post->created_at) }}</div>
              <h3>{{ $post->title }}</h3>
              <div class="ex">{{ \Illuminate\Support\Str::limit($post->excerpt ?? strip_tags($post->content), 90) }}</div>
            </div>
          </a>
        @endforeach
      </div>
    @else
      <p style="color:var(--muted);">Belum ada berita.</p>
    @endif
  </div>
</section>

{{-- GALERI --}}
@if($galleries->count())
<section class="sec" style="background:#fff;border-top:1px solid var(--line);border-bottom:1px solid var(--line);">
  <div class="container">
    <div class="sec-title">Galeri</div>
    <div class="sec-sub">Momen kegiatan sekolah</div>
    <div class="grid grid-3">
      @foreach($galleries as $g)
        <a href="{{ route('public.galeri', $school->slug) }}" class="pcard">
          <img class="thumb" src="{{ $g->thumbnail ? asset($g->thumbnail) : 'https://placehold.co/600x400/e8f5ec/1a7a3c?text=Galeri' }}" alt="">
          <div class="body"><h3 style="font-size:14px;">{{ $g->title }}</h3></div>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- CTA --}}
<section class="sec">
  <div class="container" style="text-align:center;">
    <div class="sec-title">Bergabung Bersama Kami</div>
    <div class="sec-sub">Pendaftaran siswa baru telah dibuka</div>
    <a href="{{ route('public.ppdb', $school->slug) }}" class="pbtn"><i class="ti ti-user-plus"></i> Daftar PPDB Online</a>
  </div>
</section>

@endsection
