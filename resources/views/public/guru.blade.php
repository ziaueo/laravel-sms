@extends('layouts.public')

@section('title', 'Guru & Staf')

@push('styles')
<style>
  /* Kartu orang lebih sempit daripada kartu berita, jadi gridnya lebih rapat
     daripada .grid-3 bawaan layout. */
  .pgrid{ display:grid; grid-template-columns:repeat(4,1fr); gap:18px; }
  @media(max-width:900px){ .pgrid{ grid-template-columns:repeat(3,1fr); } }
  @media(max-width:640px){ .pgrid{ grid-template-columns:repeat(2,1fr); } }

  .person{ background:#fff; border:1px solid var(--line); border-radius:14px; padding:18px 14px; text-align:center; }
  .person-ph{ width:88px; height:88px; margin:0 auto 12px; border-radius:50%; overflow:hidden;
              background:#e8f5ec; color:var(--pri-d); display:flex; align-items:center;
              justify-content:center; font-size:28px; font-weight:800; letter-spacing:1px; }
  .person-ph img{ width:100%; height:100%; object-fit:cover; }
  .person-name{ font-size:14.5px; font-weight:700; line-height:1.35; }
  .person-pos{ font-size:13px; color:var(--pri-d); margin-top:3px; }
  .person-nip{ font-size:11.5px; color:var(--muted); margin-top:5px; }

  /* Kepala sekolah: kartu tunggal yang lebih besar dan mendatar */
  .lead{ background:#fff; border:1px solid var(--line); border-radius:16px; padding:26px;
         display:flex; align-items:center; gap:22px; max-width:520px; margin:0 auto; }
  .lead .person-ph{ width:112px; height:112px; margin:0; font-size:34px; flex-shrink:0; }
  .lead-name{ font-size:20px; font-weight:800; line-height:1.3; }
  .lead-pos{ display:inline-block; margin-top:7px; padding:4px 12px; border-radius:20px;
             background:#e8f5ec; color:var(--pri-d); font-size:13px; font-weight:600; }
  .lead-nip{ font-size:12.5px; color:var(--muted); margin-top:8px; }
  @media(max-width:560px){ .lead{ flex-direction:column; text-align:center; gap:16px; } }

  .grp-title{ font-size:19px; font-weight:800; margin-bottom:4px; }
  .grp-sub{ color:var(--muted); font-size:14px; margin-bottom:20px; }
  .grp + .grp{ margin-top:44px; }
</style>
@endpush

@section('content')

<section class="hero" style="padding:50px 0;">
  <div class="container">
    <h1 style="font-size:30px;">Guru &amp; Staf</h1>
    <p>Tenaga pendidik dan kependidikan {{ $school->name }}.</p>
  </div>
</section>

<section class="sec">
  <div class="container">

    @if($teachers->isEmpty())

      <div class="vmcard" style="text-align:center;">
        <div class="ic" style="margin:0 auto 12px;"><i class="ti ti-users"></i></div>
        <h3 style="margin-bottom:6px;">Data belum tersedia</h3>
        <p style="color:var(--muted);">Daftar guru dan staf sekolah ini belum dipublikasikan.</p>
      </div>

    @else

      @if($headmaster)
        <div style="text-align:center;margin-bottom:14px;">
          <div class="sec-title" style="font-size:22px;">{{ $headmaster->position?->name ?? 'Pimpinan Sekolah' }}</div>
        </div>

        <div class="lead" style="margin-bottom:48px;">
          <div class="person-ph">
            @if($headmaster->photo)
              <img src="{{ asset($headmaster->photo) }}" alt="{{ $headmaster->full_name }}"
                   data-lightbox="staf"
                   data-caption="{{ $headmaster->full_name }} — {{ $headmaster->position?->name ?? 'Pimpinan Sekolah' }}">
            @else
              {{ $headmaster->initials }}
            @endif
          </div>
          <div>
            <div class="lead-name">{{ $headmaster->full_name }}</div>
            <div class="lead-pos">{{ $headmaster->position?->name ?? 'Pimpinan Sekolah' }}</div>
            @if($headmaster->nip)
              <div class="lead-nip">NIP {{ $headmaster->nip }}</div>
            @endif
          </div>
        </div>
      @endif

      @foreach($sections as $section)
        @continue($section['people']->isEmpty())

        <div class="grp">
          <div class="grp-title">{{ $section['title'] }}</div>
          <div class="grp-sub">{{ $section['people']->count() }} orang</div>

          <div class="pgrid">
            @foreach($section['people'] as $person)
              <div class="person">
                <div class="person-ph">
                  @if($person->photo)
                    <img src="{{ asset($person->photo) }}" alt="{{ $person->full_name }}"
                         data-lightbox="staf"
                         data-caption="{{ $person->full_name }} — {{ $person->position?->name ?? 'Belum ditentukan' }}">
                  @else
                    {{ $person->initials }}
                  @endif
                </div>
                <div class="person-name">{{ $person->full_name }}</div>
                <div class="person-pos">{{ $person->position?->name ?? 'Belum ditentukan' }}</div>
                @if($person->nip)
                  <div class="person-nip">NIP {{ $person->nip }}</div>
                @endif
              </div>
            @endforeach
          </div>
        </div>
      @endforeach

    @endif

  </div>
</section>

@endsection
