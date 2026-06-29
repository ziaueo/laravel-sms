@extends('layouts.public')

@section('title', 'Kontak')

@section('content')

<section class="hero" style="padding:50px 0;">
  <div class="container"><h1 style="font-size:30px;">Hubungi Kami</h1></div>
</section>

<section class="sec">
  <div class="container">
    <div class="grid grid-2" style="align-items:start;">
      <div>
        <div class="vmcard" style="margin-bottom:14px;"><div class="ic"><i class="ti ti-map-pin"></i></div><h3>Alamat</h3><p style="color:var(--muted);">{{ $school->address ?? '-' }}</p></div>
        <div class="vmcard" style="margin-bottom:14px;"><div class="ic"><i class="ti ti-phone"></i></div><h3>Telepon</h3><p style="color:var(--muted);">{{ $school->phone ?? '-' }}</p></div>
        <div class="vmcard"><div class="ic"><i class="ti ti-mail"></i></div><h3>Email</h3><p style="color:var(--muted);">{{ $school->email ?? '-' }}</p></div>
        @if($school->profile && ($school->profile->facebook_url || $school->profile->instagram_url || $school->profile->youtube_url))
          <div style="margin-top:16px;display:flex;gap:10px;">
            @if($school->profile->facebook_url)<a href="{{ $school->profile->facebook_url }}" class="pbtn pbtn-ghost"><i class="ti ti-brand-facebook"></i></a>@endif
            @if($school->profile->instagram_url)<a href="{{ $school->profile->instagram_url }}" class="pbtn pbtn-ghost"><i class="ti ti-brand-instagram"></i></a>@endif
            @if($school->profile->youtube_url)<a href="{{ $school->profile->youtube_url }}" class="pbtn pbtn-ghost"><i class="ti ti-brand-youtube"></i></a>@endif
          </div>
        @endif
      </div>
      <div>
        @if($school->profile?->maps_embed)
          <div style="border-radius:14px;overflow:hidden;border:1px solid var(--line);">{!! $school->profile->maps_embed !!}</div>
        @else
          <div class="vmcard" style="text-align:center;color:var(--muted);"><i class="ti ti-map-2" style="font-size:40px;"></i><p style="margin-top:10px;">Peta lokasi belum tersedia.</p></div>
        @endif
      </div>
    </div>
  </div>
</section>

@endsection
