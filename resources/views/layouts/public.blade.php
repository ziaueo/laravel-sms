<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', $school->name ?? 'Sekolah') — {{ $school->name ?? config('app.name') }}</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.44.0/dist/tabler-icons.min.css">
  <style>
    :root{ --pri:#1a7a3c; --pri-d:#0f5c2b; --ink:#1f2937; --muted:#6b7280; --bg:#f8faf9; --line:#e5ece8; }
    *{ box-sizing:border-box; margin:0; padding:0; }
    body{ font-family:'Segoe UI',system-ui,sans-serif; color:var(--ink); background:var(--bg); line-height:1.6; }
    a{ color:inherit; text-decoration:none; }
    img{ max-width:100%; display:block; }
    .container{ max-width:1100px; margin:0 auto; padding:0 18px; }
    .pnav{ background:#fff; border-bottom:1px solid var(--line); position:sticky; top:0; z-index:50; }
    .pnav-in{ display:flex; align-items:center; justify-content:space-between; height:64px; }
    .pnav-brand{ display:flex; align-items:center; gap:10px; font-weight:800; font-size:16px; color:var(--pri-d); }
    .pnav-brand img{ height:38px; width:38px; object-fit:contain; border-radius:8px; }
    .pnav-links{ display:flex; gap:4px; align-items:center; }
    .pnav-links a{ padding:8px 13px; border-radius:8px; font-size:14px; font-weight:500; color:var(--ink); }
    .pnav-links a:hover,.pnav-links a.active{ background:#e8f5ec; color:var(--pri-d); }
    .pbtn{ display:inline-flex; align-items:center; gap:6px; padding:9px 16px; border-radius:9px; font-weight:600; font-size:14px; background:var(--pri); color:#fff!important; border:none; cursor:pointer; }
    .pbtn:hover{ background:var(--pri-d); }
    .pbtn-ghost{ background:#fff; color:var(--pri-d)!important; border:1px solid var(--pri); }
    .hero{ background:linear-gradient(135deg,var(--pri-d),var(--pri)); color:#fff; padding:70px 0; }
    .hero h1{ font-size:38px; line-height:1.2; margin-bottom:14px; }
    .hero p{ font-size:17px; opacity:.92; max-width:620px; }
    .hero .acts{ margin-top:24px; display:flex; gap:12px; flex-wrap:wrap; }
    .sec{ padding:54px 0; }
    .sec-title{ font-size:26px; font-weight:800; margin-bottom:6px; }
    .sec-sub{ color:var(--muted); margin-bottom:30px; }
    .grid{ display:grid; gap:20px; }
    .grid-3{ grid-template-columns:repeat(3,1fr); }
    .grid-2{ grid-template-columns:repeat(2,1fr); }
    .pcard{ background:#fff; border:1px solid var(--line); border-radius:14px; overflow:hidden; }
    .pcard .body{ padding:16px; }
    .pcard img.thumb{ width:100%; height:180px; object-fit:cover; }
    .pcard h3{ font-size:16px; margin-bottom:6px; }
    .pcard .meta{ font-size:12px; color:var(--muted); margin-bottom:8px; }
    .pcard .ex{ font-size:13.5px; color:var(--muted); }
    .vmcard{ background:#fff; border:1px solid var(--line); border-radius:14px; padding:24px; }
    .vmcard .ic{ width:46px; height:46px; border-radius:11px; background:#e8f5ec; color:var(--pri-d); display:flex; align-items:center; justify-content:center; font-size:22px; margin-bottom:12px; }
    .form-row{ margin-bottom:14px; }
    .form-row label{ display:block; font-size:13px; font-weight:600; margin-bottom:5px; }
    .form-row input,.form-row select,.form-row textarea{ width:100%; padding:10px 12px; border:1px solid var(--line); border-radius:9px; font-size:14px; font-family:inherit; }
    .galimg{ width:100%; height:160px; object-fit:cover; border-radius:12px; }
    .pfoot{ background:#0f1c14; color:#cbd5cf; padding:40px 0 24px; margin-top:30px; }
    .pfoot a{ color:#cbd5cf; }
    .pfoot h4{ color:#fff; margin-bottom:12px; font-size:15px; }
    .pfoot-grid{ display:grid; grid-template-columns:2fr 1fr 1fr; gap:30px; }
    .pfoot-bottom{ border-top:1px solid #1e3326; margin-top:28px; padding-top:16px; font-size:12.5px; text-align:center; opacity:.8; }
    .palert{ background:#e8f5ec; border:1px solid #b7e4c7; color:#0f5c2b; padding:12px 14px; border-radius:10px; margin-bottom:18px; font-size:14px; }
    .palert-err{ background:#fdecec; border-color:#f5c2c2; color:#a12626; }
    @media(max-width:820px){ .grid-3{grid-template-columns:1fr 1fr;} .pfoot-grid{grid-template-columns:1fr;} .pnav-links{display:none;} .hero h1{font-size:28px;} }
    @media(max-width:560px){ .grid-3,.grid-2{grid-template-columns:1fr;} }
  </style>
  @stack('styles')
</head>
<body>

  <nav class="pnav">
    <div class="container pnav-in">
      <a href="{{ route('public.home', $school->slug) }}" class="pnav-brand">
        @if($school->logo)<img src="{{ asset($school->logo) }}" alt="">@endif
        {{ $school->name }}
      </a>
      <div class="pnav-links">
        <a href="{{ route('public.home', $school->slug) }}" class="{{ request()->routeIs('public.home') ? 'active' : '' }}">Beranda</a>
        <a href="{{ route('public.profil', $school->slug) }}" class="{{ request()->routeIs('public.profil') ? 'active' : '' }}">Profil</a>
        <a href="{{ route('public.berita', $school->slug) }}" class="{{ request()->routeIs('public.berita*') ? 'active' : '' }}">Berita</a>
        <a href="{{ route('public.galeri', $school->slug) }}" class="{{ request()->routeIs('public.galeri') ? 'active' : '' }}">Galeri</a>
        <a href="{{ route('public.kontak', $school->slug) }}" class="{{ request()->routeIs('public.kontak') ? 'active' : '' }}">Kontak</a>
        <a href="{{ route('public.ppdb', $school->slug) }}" class="pbtn"><i class="ti ti-user-plus"></i> PPDB</a>
      </div>
    </div>
  </nav>

  @yield('content')

  <footer class="pfoot">
    <div class="container">
      <div class="pfoot-grid">
        <div>
          <h4>{{ $school->name }}</h4>
          <p style="font-size:13.5px;">{{ $school->profile->tagline ?? 'Mencerdaskan generasi bangsa.' }}</p>
          <p style="font-size:13px;margin-top:10px;"><i class="ti ti-map-pin"></i> {{ $school->address ?? '-' }}</p>
        </div>
        <div>
          <h4>Tautan</h4>
          <p style="font-size:13.5px;line-height:2;">
            <a href="{{ route('public.profil', $school->slug) }}">Profil</a><br>
            <a href="{{ route('public.berita', $school->slug) }}">Berita</a><br>
            <a href="{{ route('public.ppdb', $school->slug) }}">PPDB Online</a><br>
            <a href="{{ route('auth.login') }}">Login Sistem</a>
          </p>
        </div>
        <div>
          <h4>Kontak</h4>
          <p style="font-size:13.5px;line-height:2;">
            @if($school->phone)<i class="ti ti-phone"></i> {{ $school->phone }}<br>@endif
            @if($school->email)<i class="ti ti-mail"></i> {{ $school->email }}@endif
          </p>
        </div>
      </div>
      <div class="pfoot-bottom">© {{ date('Y') }} {{ $school->name }}. All rights reserved.</div>
    </div>
  </footer>

</body>
</html>
