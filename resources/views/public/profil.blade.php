@extends('layouts.public')

@section('title', 'Profil')

@section('content')

<section class="hero" style="padding:50px 0;">
  <div class="container"><h1 style="font-size:30px;">Profil {{ $school->name }}</h1>
    <p>{{ $school->profile->tagline ?? '' }}</p></div>
</section>

<section class="sec">
  <div class="container">
    <div class="grid grid-2" style="align-items:start;">
      <div>
        <div class="sec-title" style="font-size:22px;">Tentang Sekolah</div>
        <p style="color:var(--muted);white-space:pre-line;">{{ $school->profile->description ?? 'Belum ada deskripsi.' }}</p>
        @if($school->profile?->history)
          <div class="sec-title" style="font-size:20px;margin-top:24px;">Sejarah</div>
          <p style="color:var(--muted);white-space:pre-line;">{{ $school->profile->history }}</p>
        @endif
      </div>
      <div>
        <div class="vmcard" style="margin-bottom:18px;">
          <div class="ic"><i class="ti ti-eye"></i></div>
          <h3 style="margin-bottom:8px;">Visi</h3>
          <p style="color:var(--muted);white-space:pre-line;">{{ $school->profile->vision ?? '-' }}</p>
        </div>
        <div class="vmcard">
          <div class="ic"><i class="ti ti-target"></i></div>
          <h3 style="margin-bottom:8px;">Misi</h3>
          <p style="color:var(--muted);white-space:pre-line;">{{ $school->profile->mission ?? '-' }}</p>
        </div>
      </div>
    </div>

    <div class="grid grid-3" style="margin-top:30px;">
      <div class="vmcard"><div class="ic"><i class="ti ti-building"></i></div><h3>Jenjang</h3><p style="color:var(--muted);">{{ $school->schoolType->name ?? '-' }}</p></div>
      <div class="vmcard"><div class="ic"><i class="ti ti-award"></i></div><h3>Akreditasi</h3><p style="color:var(--muted);">{{ $school->accreditation ?? '-' }}</p></div>
      <div class="vmcard"><div class="ic"><i class="ti ti-calendar"></i></div><h3>Berdiri</h3><p style="color:var(--muted);">{{ $school->profile->founded_year ?? '-' }}</p></div>
    </div>
  </div>
</section>

@endsection
