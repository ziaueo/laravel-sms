{{-- Isi daftar modal ganti sekolah. Dikembalikan mentah oleh SchoolSwitchController::list(). --}}
@php $activeSchoolId = (int) session('active_school_id'); @endphp

@forelse($schools as $school)
  @php $isActive = (int) $school->id === $activeSchoolId; @endphp

  <button type="button"
          class="ss-item{{ $isActive ? ' is-active' : '' }}"
          @if($isActive) disabled @else data-school-id="{{ $school->id }}" @endif>
    <span class="ss-item-icon">
      @if($school->logo)
        <img src="{{ asset($school->logo) }}" alt="">
      @else
        <i class="ti ti-building-school"></i>
      @endif
    </span>

    <span class="ss-item-text">
      <span class="ss-item-name">{{ $school->name }}</span>
      <span class="ss-item-type">{{ $school->schoolType?->name ?? '-' }}</span>
    </span>

    @if($isActive)
      <span class="ss-item-badge">AKTIF</span>
    @else
      <i class="ti ti-chevron-right ss-item-arrow"></i>
    @endif
  </button>
@empty
  <div class="ss-empty">
    <i class="ti ti-building-school"></i>
    <div class="ss-empty-title">
      {{ request('q') ? 'Sekolah tidak ditemukan' : 'Tidak ada sekolah yang bisa diakses' }}
    </div>
    @if(request('q'))
      <div class="ss-empty-hint">Coba kata kunci lain.</div>
    @endif
  </div>
@endforelse

@if($isLimited ?? false)
  <div class="ss-hint">Ketik nama sekolah untuk mempersempit.</div>
@endif
