@extends('layouts.public')

@section('title', 'Guru & Staf')

@section('content')

<section class="phead">
  <div class="pcontainer">
    <h1>Guru &amp; Staf</h1>
    <p>Tenaga pendidik dan kependidikan {{ $school->name }}.</p>
  </div>
</section>

<section class="psec">
  <div class="pcontainer">

    @if($teachers->isEmpty())

      <div class="pempty" style="padding:56px 20px;">
        <i class="ti ti-users" style="font-size:42px;display:block;margin-bottom:12px;"></i>
        <strong style="color:var(--color-text-primary);">Data belum tersedia</strong>
        <div style="margin-top:4px;">Daftar guru dan staf sekolah ini belum dipublikasikan.</div>
      </div>

    @else

      @if($headmaster)
        <div class="psec-head psec-head-center">
          <span class="psec-eyebrow">Pimpinan</span>
          <div class="psec-title">{{ $headmaster->position?->name ?? 'Pimpinan Sekolah' }}</div>
        </div>

        <div class="plead" style="margin-bottom:56px;">
          <div class="pperson-photo">
            @if($headmaster->photo)
              <img src="{{ asset($headmaster->photo) }}" alt="{{ $headmaster->full_name }}"
                   data-lightbox="staf"
                   data-caption="{{ $headmaster->full_name }} — {{ $headmaster->position?->name ?? 'Pimpinan Sekolah' }}">
            @else
              {{ $headmaster->initials }}
            @endif
          </div>
          <div>
            <div class="plead-name">{{ $headmaster->full_name }}</div>
            <div class="plead-role">{{ $headmaster->position?->name ?? 'Pimpinan Sekolah' }}</div>
            @if($headmaster->nip)
              <div class="plead-nip">NIP {{ $headmaster->nip }}</div>
            @endif
          </div>
        </div>
      @endif

      @foreach($sections as $section)
        @continue($section['people']->isEmpty())

        <div style="margin-bottom:48px;">
          <div class="psec-head">
            <div class="psec-title" style="font-size:21px;">{{ $section['title'] }}</div>
            <div class="psec-sub">{{ $section['people']->count() }} orang</div>
          </div>

          <div class="pgrid pgrid-4">
            @foreach($section['people'] as $person)
              <div class="pperson">
                <div class="pperson-photo">
                  @if($person->photo)
                    <img src="{{ asset($person->photo) }}" alt="{{ $person->full_name }}"
                         data-lightbox="staf"
                         data-caption="{{ $person->full_name }} — {{ $person->position?->name ?? 'Belum ditentukan' }}">
                  @else
                    {{ $person->initials }}
                  @endif
                </div>
                <div class="pperson-name">{{ $person->full_name }}</div>
                <div class="pperson-role">{{ $person->position?->name ?? 'Belum ditentukan' }}</div>
                @if($person->nip)
                  <div class="pperson-nip">NIP {{ $person->nip }}</div>
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
