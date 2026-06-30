@extends('layouts.app')

@section('title', 'Data Guru & Staff')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb">
      <i class="ti ti-home" style="font-size:11px;"></i> Beranda <span>/ Guru & Staff</span>
    </div>
    <div class="page-title">Data Guru & Staff</div>
    <div class="page-subtitle">Kelola data kepegawaian sekolah</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('teachers.create') }}" class="btn btn-primary">
      <i class="ti ti-plus"></i> Tambah Guru/Staff
    </a>
  </div>
</div>

{{-- FILTER --}}
<div class="card">
  <div class="card-body" style="padding:14px 16px;">
    <form method="GET" action="{{ route('teachers.index') }}"
          style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
      <div style="flex:1;min-width:200px;">
        <input type="text" name="search" value="{{ request('search') }}"
               class="form-control" placeholder="Cari nama atau NIP...">
      </div>
      <div style="min-width:160px;">
        <select name="position_id" class="form-control" onchange="this.form.submit()">
          <option value="">Semua Jabatan</option>
          @foreach($positions as $pos)
            <option value="{{ $pos->id }}" {{ request('position_id') == $pos->id ? 'selected' : '' }}>
              {{ $pos->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div style="min-width:130px;">
        <select name="status" class="form-control" onchange="this.form.submit()">
          <option value="">Semua Status</option>
          <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
          <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>
      </div>
      <button type="submit" class="btn btn-outline btn-sm">
        <i class="ti ti-search"></i> Cari
      </button>
      @if(request()->hasAny(['search','position_id','status']))
        <a href="{{ route('teachers.index') }}" class="btn btn-outline btn-sm">
          <i class="ti ti-x"></i> Reset
        </a>
      @endif
    </form>
  </div>
</div>

{{-- TEACHER GRID --}}
<div class="teacher-grid">
  @forelse($teachers as $teacher)
    <div class="teacher-card">
      <div class="teacher-card-header">
        <div class="teacher-avatar">
          @if($teacher->photo)
            <img src="{{ asset($teacher->photo) }}" alt="{{ $teacher->full_name }}">
          @else
            <span>{{ $teacher->initials }}</span>
          @endif
        </div>
        <div class="teacher-status-badges">
          @if($teacher->is_active)
            <span class="badge badge-green">Aktif</span>
          @else
            <span class="badge badge-red">Nonaktif</span>
          @endif
          @if($teacher->user_id)
            <span class="badge badge-blue">Punya Akun</span>
          @endif
        </div>
      </div>

      <div class="teacher-name">{{ $teacher->full_name }}</div>
      <div class="teacher-position">
        {{ $teacher->position?->name ?? 'Belum ada jabatan' }}
      </div>

      <div class="teacher-info-row">
        @if($teacher->nip)
          <div class="teacher-info-item">
            <i class="ti ti-id"></i> {{ $teacher->nip }}
          </div>
        @endif
        @if($teacher->phone)
          <div class="teacher-info-item">
            <i class="ti ti-phone"></i> {{ $teacher->phone }}
          </div>
        @endif
        <div class="teacher-info-item">
          <i class="ti ti-book"></i> {{ $teacher->teaching_assignments_count }} Penugasan
        </div>
        <div class="teacher-info-item">
          <i class="ti ti-gender-{{ $teacher->gender == 1 ? 'male' : 'female' }}"></i>
          {{ gender_label($teacher->gender) }}
        </div>
      </div>

      <div class="teacher-card-actions">
        <a href="{{ route('teachers.show', hid($teacher)) }}"
           class="btn btn-sm btn-outline" style="flex:1;justify-content:center;">
          <i class="ti ti-eye" style="font-size:13px;"></i> Detail
        </a>
        <a href="{{ route('teachers.edit', hid($teacher)) }}"
           class="btn btn-sm btn-outline btn-icon" title="Edit">
          <i class="ti ti-edit" style="font-size:13px;"></i>
        </a>
        <form method="POST" action="{{ route('teachers.toggle-active', hid($teacher)) }}" style="display:inline;">
          @csrf @method('PATCH')
          <button type="submit" class="btn btn-sm btn-outline btn-icon"
                  title="{{ $teacher->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
            <i class="ti {{ $teacher->is_active ? 'ti-toggle-right' : 'ti-toggle-left' }}"
               style="font-size:14px;"></i>
          </button>
        </form>
        <button class="btn btn-sm btn-danger btn-icon" title="Hapus"
                onclick="openDeleteModal('{{ hid($teacher) }}', '{{ $teacher->full_name }}')">
          <i class="ti ti-trash" style="font-size:13px;"></i>
        </button>
      </div>
    </div>
  @empty
    <div style="grid-column:1/-1;text-align:center;padding:50px;color:var(--color-text-secondary);">
      <i class="ti ti-users-off" style="font-size:32px;display:block;margin-bottom:10px;"></i>
      Belum ada data guru & staff
      <br>
      <a href="{{ route('teachers.create') }}" class="btn btn-primary" style="margin-top:14px;">
        <i class="ti ti-plus"></i> Tambah Sekarang
      </a>
    </div>
  @endforelse
</div>

<x-pagination :paginator="$teachers" />

{{-- MODAL HAPUS --}}
<div class="modal-backdrop" id="modalDeleteTeacher">
  <div class="modal-box" style="max-width:380px;">
    <div class="modal-header">
      <div class="modal-title" style="color:var(--color-danger);">
        <i class="ti ti-alert-triangle"></i> Hapus Guru
      </div>
      <button type="button" class="modal-close" onclick="closeModal('modalDeleteTeacher')">
        <i class="ti ti-x"></i>
      </button>
    </div>
    <div class="modal-body">
      <p style="font-size:12.5px;color:var(--color-text-secondary);">
        Hapus data guru <strong id="deleteTeacherName"></strong>?
        Data penugasan mengajar juga akan ikut terhapus.
      </p>
    </div>
    <form method="POST" id="formDeleteTeacher">
      @csrf @method('DELETE')
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalDeleteTeacher')">Batal</button>
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
  document.getElementById('deleteTeacherName').textContent = name;
  document.getElementById('formDeleteTeacher').action = `/teachers/${id}`;
  openModal('modalDeleteTeacher');
}

document.querySelectorAll('.modal-backdrop').forEach(modal => {
  modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.classList.remove('show');
  });
});
</script>
@endpush
