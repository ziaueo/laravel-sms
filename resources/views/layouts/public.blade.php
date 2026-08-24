<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', $school->name ?? 'Sekolah') — {{ $school->name ?? config('app.name') }}</title>
  @if($school->logo)<link rel="icon" href="{{ asset($school->logo) }}">@endif

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.44.0/dist/tabler-icons.min.css">
  @vite(['resources/css/public.css', 'resources/js/public.js'])

  @stack('styles')
</head>
<body>

@php
  $menu = [
    ['route' => 'public.home',   'match' => 'public.home',    'label' => 'Beranda'],
    ['route' => 'public.profil', 'match' => 'public.profil',  'label' => 'Profil'],
    ['route' => 'public.guru',   'match' => 'public.guru',    'label' => 'Guru & Staf'],
    ['route' => 'public.berita', 'match' => 'public.berita*', 'label' => 'Berita'],
    ['route' => 'public.galeri', 'match' => 'public.galeri*', 'label' => 'Galeri'],
    ['route' => 'public.kontak', 'match' => 'public.kontak',  'label' => 'Kontak'],
  ];
@endphp

<nav class="pnav">
  <div class="pcontainer pnav-in">
    <a href="{{ route('public.home', $school->slug) }}" class="pnav-brand">
      @if($school->logo)
        <img src="{{ asset($school->logo) }}" alt="Logo {{ $school->name }}">
      @endif
      <span class="pnav-brand-text">
        <span class="pnav-brand-name">{{ $school->name }}</span>
        @if($school->schoolType)
          <span class="pnav-brand-sub">{{ $school->schoolType->name }}</span>
        @endif
      </span>
    </a>

    <div class="pnav-links">
      @foreach($menu as $item)
        <a href="{{ route($item['route'], $school->slug) }}"
           class="{{ request()->routeIs($item['match']) ? 'active' : '' }}">{{ $item['label'] }}</a>
      @endforeach
      <a href="{{ route('public.ppdb', $school->slug) }}" class="pbtn pbtn-sm">
        <i class="ti ti-user-plus"></i> PPDB
      </a>
    </div>

    <button type="button" class="pnav-toggle" data-nav-open aria-label="Buka menu">
      <i class="ti ti-menu-2"></i>
    </button>
  </div>
</nav>

{{-- Panel menu mobile. Sebelumnya di bawah 900px tidak ada navigasi sama sekali. --}}
<div class="pnav-panel" id="navPanel">
  <div class="pnav-panel-inner">
    <div class="pnav-panel-head">
      <span class="pnav-panel-title">Menu</span>
      <button type="button" class="pnav-panel-close" data-nav-close aria-label="Tutup menu">
        <i class="ti ti-x"></i>
      </button>
    </div>

    @foreach($menu as $item)
      <a href="{{ route($item['route'], $school->slug) }}"
         class="{{ request()->routeIs($item['match']) ? 'active' : '' }}">{{ $item['label'] }}</a>
    @endforeach

    <a href="{{ route('public.ppdb', $school->slug) }}" class="pbtn">
      <i class="ti ti-user-plus"></i> Pendaftaran PPDB
    </a>
  </div>
</div>

@yield('content')

<footer class="pfoot">
  <div class="pcontainer">
    <div class="pfoot-grid">
      <div>
        <div class="pfoot-brand">
          @if($school->logo)<img src="{{ asset($school->logo) }}" alt="">@endif
          <span class="pfoot-brand-name">{{ $school->name }}</span>
        </div>
        <p>{{ $school->profile->tagline ?? 'Mencerdaskan generasi bangsa.' }}</p>

        @php $p = $school->profile; @endphp
        @if($p?->facebook_url || $p?->instagram_url || $p?->youtube_url)
          <div class="pfoot-social">
            @if($p->facebook_url)
              <a href="{{ $p->facebook_url }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="ti ti-brand-facebook"></i></a>
            @endif
            @if($p->instagram_url)
              <a href="{{ $p->instagram_url }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="ti ti-brand-instagram"></i></a>
            @endif
            @if($p->youtube_url)
              <a href="{{ $p->youtube_url }}" target="_blank" rel="noopener" aria-label="YouTube"><i class="ti ti-brand-youtube"></i></a>
            @endif
          </div>
        @endif
      </div>

      <div>
        <h4>Tautan</h4>
        <div class="pfoot-links">
          <a href="{{ route('public.profil', $school->slug) }}">Profil</a>
          <a href="{{ route('public.guru', $school->slug) }}">Guru &amp; Staf</a>
          <a href="{{ route('public.berita', $school->slug) }}">Berita</a>
          <a href="{{ route('public.galeri', $school->slug) }}">Galeri</a>
          <a href="{{ route('public.ppdb', $school->slug) }}">PPDB Online</a>
          <a href="{{ route('auth.login') }}">Login Sistem</a>
        </div>
      </div>

      <div>
        <h4>Kontak</h4>
        <div class="pfoot-contact">
          @if($school->address)
            <div><i class="ti ti-map-pin"></i> <span>{{ $school->address }}</span></div>
          @endif
          @if($school->phone)
            <div><i class="ti ti-phone"></i> <span>{{ $school->phone }}</span></div>
          @endif
          @if($school->email)
            <div><i class="ti ti-mail"></i> <span>{{ $school->email }}</span></div>
          @endif
        </div>
      </div>
    </div>

    <div class="pfoot-bottom">© {{ date('Y') }} {{ $school->name }}. Seluruh hak cipta dilindungi.</div>
  </div>
</footer>

@include('components.lightbox')

@stack('scripts')
</body>
</html>
