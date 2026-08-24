@extends('layouts.public')

@section('title', 'Kontak')

@section('content')

<section class="phead">
  <div class="pcontainer">
    <h1>Hubungi Kami</h1>
    <p>Silakan hubungi {{ $school->name }} melalui saluran berikut.</p>
  </div>
</section>

<section class="psec">
  <div class="pcontainer">
    <div class="pgrid pgrid-2" style="align-items:start;">
      <div style="display:flex;flex-direction:column;gap:16px;">
        <div class="pinfo">
          <div class="pinfo-icon"><i class="ti ti-map-pin"></i></div>
          <h3>Alamat</h3>
          <p>{{ $school->address ?? 'Belum dicantumkan.' }}</p>
        </div>

        <div class="pinfo">
          <div class="pinfo-icon"><i class="ti ti-phone"></i></div>
          <h3>Telepon</h3>
          <p>{{ $school->phone ?? 'Belum dicantumkan.' }}</p>
        </div>

        <div class="pinfo">
          <div class="pinfo-icon"><i class="ti ti-mail"></i></div>
          <h3>Email</h3>
          <p>{{ $school->email ?? 'Belum dicantumkan.' }}</p>
        </div>

        @php $p = $school->profile; @endphp
        @if($p?->facebook_url || $p?->instagram_url || $p?->youtube_url)
          <div class="pinfo">
            <div class="pinfo-icon"><i class="ti ti-share"></i></div>
            <h3>Media Sosial</h3>
            <div style="display:flex;gap:10px;margin-top:12px;">
              @if($p->facebook_url)
                <a href="{{ $p->facebook_url }}" target="_blank" rel="noopener" class="pbtn pbtn-outline pbtn-sm"><i class="ti ti-brand-facebook"></i> Facebook</a>
              @endif
              @if($p->instagram_url)
                <a href="{{ $p->instagram_url }}" target="_blank" rel="noopener" class="pbtn pbtn-outline pbtn-sm"><i class="ti ti-brand-instagram"></i> Instagram</a>
              @endif
              @if($p->youtube_url)
                <a href="{{ $p->youtube_url }}" target="_blank" rel="noopener" class="pbtn pbtn-outline pbtn-sm"><i class="ti ti-brand-youtube"></i> YouTube</a>
              @endif
            </div>
          </div>
        @endif
      </div>

      <div>
        @if($school->profile?->maps_embed)
          <div style="border-radius:var(--radius-xl);overflow:hidden;border:1px solid var(--color-border-solid);box-shadow:var(--shadow-card);">
            {!! $school->profile->maps_embed !!}
          </div>
        @else
          <div class="pempty" style="padding:56px 20px;">
            <i class="ti ti-map-2" style="font-size:42px;display:block;margin-bottom:12px;"></i>
            Peta lokasi belum tersedia.
          </div>
        @endif
      </div>
    </div>
  </div>
</section>

@endsection
