@extends('layouts.public')

@section('title', 'Beranda')

@section('content')

{{-- ══ HERO ══ --}}
@if($banners->count())
  <section class="phero {{ $banners->count() < 2 ? 'is-single' : '' }}" id="heroSlider">
    <div class="phero-slides">
      @foreach($banners as $i => $banner)
        <div class="phero-slide {{ $i === 0 ? 'is-active' : '' }}">
          <img src="{{ asset($banner->image) }}" alt="{{ $banner->title }}">
          <div class="pcontainer phero-body">
            <h1 class="phero-title">{{ $banner->title }}</h1>
            @if($banner->subtitle)
              <p class="phero-sub">{{ $banner->subtitle }}</p>
            @endif
            @if($banner->button_text && $banner->button_url)
              <div class="phero-acts">
                <a href="{{ $banner->button_url }}" class="pbtn">
                  {{ $banner->button_text }} <i class="ti ti-arrow-right"></i>
                </a>
              </div>
            @endif
          </div>
        </div>
      @endforeach
    </div>

    @if($banners->count() > 1)
      <div class="phero-dots" role="tablist" aria-label="Pilih banner">
        @foreach($banners as $i => $banner)
          <button type="button" class="phero-dot {{ $i === 0 ? 'is-active' : '' }}"
                  role="tab" aria-selected="{{ $i === 0 ? 'true' : 'false' }}"
                  aria-label="Banner {{ $i + 1 }}"></button>
        @endforeach
      </div>
    @endif
  </section>
@else
  {{-- Sekolah belum punya banner — jangan tampilkan kotak kosong. --}}
  <section class="phero-plain">
    <div class="pcontainer phero-body">
      <h1 class="phero-title">{{ $school->profile->tagline ?? 'Selamat Datang di ' . $school->name }}</h1>
      <p class="phero-sub">{{ $school->profile->description ?? 'Membentuk generasi cerdas, berkarakter, dan berakhlak mulia.' }}</p>
      <div class="phero-acts">
        <a href="{{ route('public.ppdb', $school->slug) }}" class="pbtn"><i class="ti ti-user-plus"></i> Daftar Sekarang</a>
        <a href="{{ route('public.profil', $school->slug) }}" class="pbtn pbtn-ghost">Tentang Kami</a>
      </div>
    </div>
  </section>
@endif

{{-- ══ PENGUMUMAN ══ --}}
@if($announcements->count())
  <section class="psec" style="padding-bottom:0;">
    <div class="pcontainer">
      @foreach($announcements as $a)
        <div class="palert">
          <i class="ti ti-speakerphone"></i>
          <div><strong>{{ $a->title }}</strong> — {{ \Illuminate\Support\Str::limit(strip_tags($a->content), 130) }}</div>
        </div>
      @endforeach
    </div>
  </section>
@endif

{{-- ══ STATISTIK ══ --}}
@if(array_sum($stats) > 0)
  <section class="psec">
    <div class="pcontainer">
      <div class="pstats">
        @foreach([
          ['siswa',  'ti-users',           'Siswa Aktif'],
          ['guru',   'ti-chalkboard',      'Guru & Staf'],
          ['kelas',  'ti-door',            'Rombongan Belajar'],
          ['ekskul', 'ti-ball-basketball', 'Ekstrakurikuler'],
        ] as [$key, $icon, $label])
          <div class="pstat">
            <div class="pstat-icon"><i class="ti {{ $icon }}"></i></div>
            <div class="pstat-num">{{ $stats[$key] }}</div>
            <div class="pstat-label">{{ $label }}</div>
          </div>
        @endforeach
      </div>
    </div>
  </section>
@endif

{{-- ══ VISI & MISI ══ --}}
@if($school->profile?->vision || $school->profile?->mission)
  <section class="psec psec-alt">
    <div class="pcontainer">
      <div class="psec-head psec-head-center">
        <span class="psec-eyebrow">Arah Kami</span>
        <div class="psec-title">Visi &amp; Misi</div>
      </div>

      <div class="pvm">
        @if($school->profile->vision)
          <div class="pvm-card">
            <div class="pvm-icon"><i class="ti ti-eye"></i></div>
            <h3>Visi</h3>
            <p>{{ $school->profile->vision }}</p>
          </div>
        @endif

        @if($school->profile->mission)
          <div class="pvm-card">
            <div class="pvm-icon"><i class="ti ti-target-arrow"></i></div>
            <h3>Misi</h3>
            @php
              // Misi disimpan sebagai teks bebas; tiap baris diperlakukan satu butir.
              $misi = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $school->profile->mission))));
            @endphp
            @if(count($misi) > 1)
              <ul class="pvm-list">
                @foreach($misi as $m)<li><span>{{ $m }}</span></li>@endforeach
              </ul>
            @else
              <p>{{ $school->profile->mission }}</p>
            @endif
          </div>
        @endif
      </div>
    </div>
  </section>
@endif

{{-- ══ BERITA ══ --}}
<section class="psec">
  <div class="pcontainer">
    <div class="psec-head">
      <span class="psec-eyebrow">Kabar Terbaru</span>
      <div class="psec-title">Berita &amp; Kegiatan</div>
      <div class="psec-sub">Apa yang sedang berlangsung di {{ $school->name }}</div>
    </div>

    @if($posts->count())
      <div class="pgrid pgrid-3">
        @foreach($posts as $post)
          <a href="{{ route('public.berita.detail', [$school->slug, $post->slug]) }}" class="pcard">
            <div class="pcard-media">
              @if($post->thumbnail)
                <img src="{{ asset($post->thumbnail) }}" alt="{{ $post->title }}">
              @else
                <div class="pcard-media-blank"><i class="ti ti-news"></i></div>
              @endif
            </div>
            <div class="pcard-body">
              <div class="pcard-meta">
                <i class="ti ti-calendar"></i> {{ format_date($post->published_at ?? $post->created_at) }}
              </div>
              <div class="pcard-title">{{ $post->title }}</div>
              <div class="pcard-text">{{ \Illuminate\Support\Str::limit($post->excerpt ?? strip_tags($post->content), 95) }}</div>
              <span class="pcard-more">Baca selengkapnya <i class="ti ti-arrow-right"></i></span>
            </div>
          </a>
        @endforeach
      </div>
      <div class="psec-foot">
        <a href="{{ route('public.berita', $school->slug) }}" class="pbtn pbtn-outline">Semua Berita</a>
      </div>
    @else
      <div class="pempty">Belum ada berita.</div>
    @endif
  </div>
</section>

{{-- ══ EKSTRAKURIKULER ══ --}}
@if($extracurriculars->count())
  <section class="psec psec-alt">
    <div class="pcontainer">
      <div class="psec-head">
        <span class="psec-eyebrow">Pengembangan Diri</span>
        <div class="psec-title">Ekstrakurikuler</div>
        <div class="psec-sub">Ruang bagi siswa menekuni minat dan bakatnya</div>
      </div>

      <div class="pgrid pgrid-3">
        @foreach($extracurriculars as $ekskul)
          <div class="pekskul">
            <div class="pekskul-icon"><i class="ti ti-ball-basketball"></i></div>
            <div>
              <div class="pekskul-name">{{ $ekskul->name }}</div>
              @if($ekskul->description)
                <div class="pekskul-desc">{{ \Illuminate\Support\Str::limit($ekskul->description, 90) }}</div>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>
@endif

{{-- ══ GALERI ══ --}}
@if($galleries->count())
  <section class="psec">
    <div class="pcontainer">
      <div class="psec-head">
        <span class="psec-eyebrow">Dokumentasi</span>
        <div class="psec-title">Galeri</div>
        <div class="psec-sub">Momen kegiatan sekolah</div>
      </div>

      <div class="pgrid pgrid-3">
        @foreach($galleries as $g)
          <a href="{{ route('public.galeri.detail', [$school->slug, hid($g)]) }}" class="pcard">
            <div class="pcard-media">
              @if($g->thumbnail)
                <img src="{{ asset($g->thumbnail) }}" alt="{{ $g->title }}">
              @else
                <div class="pcard-media-blank"><i class="ti ti-photo"></i></div>
              @endif
            </div>
            <div class="pcard-body">
              <div class="pcard-title" style="font-size:var(--font-size-md);">{{ $g->title }}</div>
            </div>
          </a>
        @endforeach
      </div>

      <div class="psec-foot">
        <a href="{{ route('public.galeri', $school->slug) }}" class="pbtn pbtn-outline">Semua Album</a>
      </div>
    </div>
  </section>
@endif

{{-- ══ AJAKAN PPDB ══ --}}
<section class="psec">
  <div class="pcontainer">
    <div class="pcta">
      <h2>Bergabung Bersama Kami</h2>
      <p>Pendaftaran siswa baru {{ $school->name }} telah dibuka.</p>
      <a href="{{ route('public.ppdb', $school->slug) }}" class="pbtn">
        <i class="ti ti-user-plus"></i> Daftar PPDB Online
      </a>
    </div>
  </div>
</section>

@endsection
