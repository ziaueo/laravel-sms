@extends('layouts.app')

@section('title', 'Sekolah Terhapus')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb">
      <i class="ti ti-home" style="font-size:11px;"></i> Beranda
      <span>/ <a href="{{ route('master.schools.index') }}" style="color:var(--color-primary);">Master Data Sekolah</a> / Sampah</span>
    </div>
    <div class="page-title">Sekolah Terhapus</div>
    <div class="page-subtitle">Data sekolah yang sudah dihapus, masih bisa dipulihkan</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('master.schools.index') }}" class="btn btn-outline">
      <i class="ti ti-arrow-left"></i> Kembali
    </a>
  </div>
</div>

<div class="school-grid">
  @forelse($schools as $school)
    <div class="school-card">
      <div class="school-card-top">
        <div class="school-card-logo">
          @if($school->logo)
            <img src="{{ asset($school->logo) }}" alt="{{ $school->name }}">
          @else
            <i class="ti ti-building-school"></i>
          @endif
        </div>
        <span class="badge badge-red">Terhapus</span>
      </div>

      <div class="school-card-name">{{ $school->name }}</div>
      <div class="school-card-type">{{ $school->schoolType->name }}</div>

      <div class="school-card-stats">
        <div class="school-stat-item">
          <i class="ti ti-clock"></i> {{ format_datetime($school->deleted_at) }}
        </div>
      </div>

      <div class="school-card-actions">
        <form method="POST" action="{{ route('master.schools.restore', $school->id) }}" style="flex:1;">
          @csrf @method('PATCH')
          <button type="submit" class="btn btn-sm btn-outline" style="width:100%;justify-content:center;">
            <i class="ti ti-restore" style="font-size:13px;"></i> Pulihkan
          </button>
        </form>

        <button class="btn btn-sm btn-danger" style="flex:1;justify-content:center;"
                onclick="openForceDeleteModal({{ $school->id }}, '{{ $school->name }}')">
          <i class="ti ti-trash-x" style="font-size:13px;"></i> Hapus Permanen
        </button>
      </div>
    </div>
  @empty
    <div style="grid-column:1/-1;text-align:center;padding:50px;color:var(--color-text-secondary);">
      <i class="ti ti-trash-off" style="font-size:32px;display:block;margin-bottom:10px;"></i>
      Tidak ada sekolah yang terhapus
    </div>
  @endforelse
</div>

<x-pagination :paginator="$schools" />

{{-- MODAL HAPUS PERMANEN --}}
<div class="modal-backdrop" id="modalForceDelete">
  <div class="modal-box" style="max-width:400px;">
    <div class="modal-header">
      <div class="modal-title" style="color:var(--color-danger);"><i class="ti ti-alert-triangle"></i> Hapus Permanen</div>
      <button type="button" class="modal-close" onclick="closeModal('modalForceDelete')"><i class="ti ti-x"></i></button>
    </div>
    <div class="modal-body">
      <p style="font-size:12.5px;color:var(--color-text-secondary);">
        Hapus permanen <strong id="forceDeleteSchoolName"></strong>? Logo dan profil sekolah juga akan ikut terhapus.
        Tindakan ini <strong>tidak bisa dibatalkan</strong>.
      </p>
    </div>
    <form method="POST" id="formForceDelete">
      @csrf @method('DELETE')
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalForceDelete')">Batal</button>
        <button type="submit" class="btn btn-danger"><i class="ti ti-trash-x"></i> Ya, Hapus Permanen</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
function openModal(id) { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }

function openForceDeleteModal(schoolId, schoolName) {
  document.getElementById('forceDeleteSchoolName').textContent = schoolName;
  document.getElementById('formForceDelete').action = `/master-data/schools/${schoolId}/force-delete`;
  openModal('modalForceDelete');
}

document.querySelectorAll('.modal-backdrop').forEach(modal => {
  modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.classList.remove('show');
  });
});
</script>
@endpush
