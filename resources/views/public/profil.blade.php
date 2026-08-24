@extends('layouts.public')

@section('title', 'Profil')

@section('content')

<section class="phead">
  <div class="pcontainer">
    <h1>Profil {{ $school->name }}</h1>
    <p>{{ $school->profile->tagline ?? 'Mengenal lebih dekat sekolah kami.' }}</p>
  </div>
</section>

<section class="psec">
  <div class="pcontainer">
    <div class="pgrid pgrid-2" style="align-items:start;">
      <div>
        <div class="psec-head">
          <span class="psec-eyebrow">Tentang</span>
          <div class="psec-title">Tentang Sekolah</div>
        </div>
        <p style="color:var(--color-text-secondary);white-space:pre-line;">
          {{ $school->profile->description ?? 'Belum ada deskripsi.' }}
        </p>

        @if($school->profile?->history)
          <div class="psec-head" style="margin-top:34px;">
            <div class="psec-title" style="font-size:21px;">Sejarah</div>
          </div>
          <p style="color:var(--color-text-secondary);white-space:pre-line;">{{ $school->profile->history }}</p>
        @endif
      </div>

      <div style="display:flex;flex-direction:column;gap:20px;">
        <div class="pvm-card">
          <div class="pvm-icon"><i class="ti ti-eye"></i></div>
          <h3>Visi</h3>
          <p>{{ $school->profile->vision ?? 'Belum dicantumkan.' }}</p>
        </div>

        <div class="pvm-card">
          <div class="pvm-icon"><i class="ti ti-target-arrow"></i></div>
          <h3>Misi</h3>
          @php
            $misi = $school->profile?->mission
              ? array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $school->profile->mission))))
              : [];
          @endphp
          @if(count($misi) > 1)
            <ul class="pvm-list">
              @foreach($misi as $m)<li><span>{{ $m }}</span></li>@endforeach
            </ul>
          @else
            <p>{{ $school->profile->mission ?? 'Belum dicantumkan.' }}</p>
          @endif
        </div>
      </div>
    </div>

    <div class="pgrid pgrid-3" style="margin-top:38px;">
      <div class="pinfo">
        <div class="pinfo-icon"><i class="ti ti-building"></i></div>
        <h3>Jenjang</h3>
        <p>{{ $school->schoolType->name ?? '-' }}</p>
      </div>
      <div class="pinfo">
        <div class="pinfo-icon"><i class="ti ti-award"></i></div>
        <h3>Akreditasi</h3>
        <p>{{ $school->accreditation ?? '-' }}</p>
      </div>
      <div class="pinfo">
        <div class="pinfo-icon"><i class="ti ti-calendar"></i></div>
        <h3>Berdiri</h3>
        <p>{{ $school->profile->founded_year ?? '-' }}</p>
      </div>
    </div>
  </div>
</section>

@endsection
