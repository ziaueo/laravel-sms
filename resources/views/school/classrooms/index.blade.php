@extends('layouts.app')

@section('title', 'Manajemen Kelas')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb">
      <i class="ti ti-home" style="font-size:11px;"></i> Beranda
      <span>/ <a href="{{ route('master.index') }}" style="color:var(--color-primary);">Master Data</a> / Kelas</span>
    </div>
    <div class="page-title">Manajemen Kelas</div>
    <div class="page-subtitle">Kelola rombongan belajar per tahun ajaran</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('classrooms.create') }}" class="btn btn-primary">
      <i class="ti ti-plus"></i> Tambah Kelas
    </a>
  </div>
</div>

{{-- FILTER TAHUN AJARAN --}}
<div class="card">
  <div class="card-body" style="padding:14px 16px;">
    <form method="GET" action="{{ route('classrooms.index') }}"
          style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
      <div style="font-size:12px;font-weight:600;color:var(--color-text-secondary);">Tahun Ajaran:</div>
      @foreach($schoolYears as $sy)
        <a href="{{ route('classrooms.index', ['school_year_id' => $sy->id]) }}"
           class="tab-item {{ $activeYearId == $sy->id ? 'active' : '' }}"
           style="padding:5px 12px;">
          {{ $sy->year }} Sem {{ $sy->semester }}
          @if($sy->is_active) <span style="font-size:9px;">✓</span> @endif
        </a>
      @endforeach
    </form>
  </div>
</div>

{{-- CLASSROOM GRID --}}
<div class="school-grid">
  @forelse($classrooms as $classroom)
    <div class="school-card">
      <div class="school-card-top">
        <div class="school-card-logo" style="background:var(--grad-primary);color:#fff;font-size:16px;font-weight:800;">
          {{ $classroom->name }}
        </div>
        @if($classroom->is_active)
          <span class="badge badge-green">Aktif</span>
        @else
          <span class="badge badge-red">Nonaktif</span>
        @endif
      </div>

      <div class="school-card-name">{{ $classroom->gradeLevel->name }} — {{ $classroom->name }}</div>
      @if($classroom->major)
        <div class="school-card-type">{{ $classroom->major->name }}</div>
      @endif

      <div class="school-card-stats">
        <div class="school-stat-item">
          <i class="ti ti-users"></i> {{ $classroom->students_count }}/{{ $classroom->capacity }} Siswa
        </div>
        @if($classroom->homeroomTeacher)
          <div class="school-stat-item">
            <i class="ti ti-user"></i> {{ Str::limit($classroom->homeroomTeacher->full_name, 20) }}
          </div>
        @else
          <div class="school-stat-item" style="color:var(--color-warning);">
            <i class="ti ti-alert-circle"></i> Belum ada wali kelas
          </div>
        @endif
      </div>

      <div class="school-card-actions">
        <a href="{{ route('classrooms.edit', hid($classroom)) }}"
           class="btn btn-sm btn-outline" style="flex:1;justify-content:center;">
          <i class="ti ti-edit" style="font-size:13px;"></i> Edit
        </a>
        <button class="btn btn-sm btn-danger btn-icon"
                onclick="openDeleteModal('{{ hid($classroom) }}', '{{ $classroom->name }}')">
          <i class="ti ti-trash" style="font-size:13px;"></i>
        </button>
      </div>
    </div>
  @empty
    <div style="grid-column:1/-1;text-align:center;padding:50px;color:var(--color-text-secondary);">
      <i class="ti ti-door-off" style="font-size:32px;display:block;margin-bottom:10px;"></i>
      Belum ada kelas untuk tahun ajaran ini
      <br>
      <a href="{{ route('classrooms.create') }}" class="btn btn-primary" style="margin-top:14px;">
        <i class="ti ti-plus"></i> Tambah Kelas Sekarang
      </a>
    </div>
  @endforelse
</div>

<x-pagination :paginator="$classrooms" />

{{-- MODAL HAPUS --}}
<div class="modal-backdrop" id="modalDeleteClassroom">
  <div class="modal-box" style="max-width:380px;">
    <div class="modal-header">
      <div class="modal-title" style="color:var(--color-danger);">
        <i class="ti ti-alert-triangle"></i> Hapus Kelas
      </div>
      <button type="button" class="modal-close" onclick="closeModal('modalDeleteClassroom')"><i class="ti ti-x"></i></button>
    </div>
    <div class="modal-body">
      <p style="font-size:12.5px;color:var(--color-text-secondary);">
        Hapus kelas <strong id="deleteClassName"></strong>?
        Data siswa yang terdaftar di kelas ini tidak akan ikut terhapus.
      </p>
    </div>
    <form method="POST" id="formDeleteClassroom">
      @csrf @method('DELETE')
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalDeleteClassroom')">Batal</button>
        <button type="submit" class="btn btn-danger"><i class="ti ti-trash"></i> Ya, Hapus</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
function openModal(id) { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }

function openDeleteModal(id, name) {
  document.getElementById('deleteClassName').textContent = name;
  document.getElementById('formDeleteClassroom').action = `/classrooms/${id}`;
  openModal('modalDeleteClassroom');
}

document.querySelectorAll('.modal-backdrop').forEach(modal => {
  modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.classList.remove('show');
  });
});
</script>
@endpush
