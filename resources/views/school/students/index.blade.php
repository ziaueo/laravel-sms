@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb">
      <i class="ti ti-home" style="font-size:11px;"></i> Beranda <span>/ Data Siswa</span>
    </div>
    <div class="page-title">Data Siswa</div>
    <div class="page-subtitle">
      Kelola data kesiswaan {{ $school->name }}
      @if($activeYear) — T.A. {{ $activeYear->year }} {{ $activeYear->semester_label }} @endif
    </div>
  </div>
  <div class="page-actions">
    <a href="{{ route('students.create') }}" class="btn btn-primary">
      <i class="ti ti-plus"></i> Tambah Siswa
    </a>
  </div>
</div>

{{-- FILTER --}}
<div class="card">
  <div class="card-body" style="padding:14px 16px;">
    <form method="GET" action="{{ route('students.index') }}"
          style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
      <div style="flex:1;min-width:200px;">
        <input type="text" name="search" value="{{ request('search') }}"
               class="form-control" placeholder="Cari nama / NISN / NIS...">
      </div>
      <div style="min-width:160px;">
        <select name="classroom_id" class="form-control" onchange="this.form.submit()">
          <option value="">Semua Kelas</option>
          @foreach($classrooms as $c)
            <option value="{{ $c->id }}" {{ request('classroom_id') == $c->id ? 'selected' : '' }}>
              {{ $c->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div style="min-width:140px;">
        <select name="status" class="form-control" onchange="this.form.submit()">
          @foreach(\App\Constants\StudentStatusConstant::getAll() as $val => $label)
            <option value="{{ $val }}" {{ (string)request('status', 1) === (string)$val ? 'selected' : '' }}>
              {{ $label }}
            </option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-outline btn-sm">
        <i class="ti ti-search"></i> Cari
      </button>
      @if(request()->hasAny(['search','classroom_id']))
        <a href="{{ route('students.index') }}" class="btn btn-outline btn-sm">
          <i class="ti ti-x"></i> Reset
        </a>
      @endif
    </form>
  </div>
</div>

{{-- TABEL SISWA --}}
<div class="card">
  <div class="card-body" style="padding:0;">
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th style="width:42px;">#</th>
            <th>Siswa</th>
            <th>NISN / NIS</th>
            <th>Kelas</th>
            <th>JK</th>
            <th>Status</th>
            <th>Akun</th>
            <th style="text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($students as $i => $student)
            <tr>
              <td>{{ $students->firstItem() + $i }}</td>
              <td>
                <div class="td-user">
                  <div class="td-avatar">
                    @if($student->photo)
                      <img src="{{ asset($student->photo) }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:var(--radius-full);">
                    @else
                      <span>{{ $student->initials }}</span>
                    @endif
                  </div>
                  <div>
                    <div style="font-weight:600;">{{ $student->full_name }}</div>
                    <div style="font-size:11px;color:var(--color-text-secondary);">
                      {{ $student->birth_place ? $student->birth_place . ', ' : '' }}{{ format_date($student->birth_date) }}
                    </div>
                  </div>
                </div>
              </td>
              <td>
                <div style="font-size:12px;">{{ $student->nisn ?? '-' }}</div>
                <div style="font-size:11px;color:var(--color-text-secondary);">{{ $student->nis ?? '-' }}</div>
              </td>
              <td>
                {{ $student->activeClassroom?->classroom?->name ?? '—' }}
              </td>
              <td>{{ gender_label($student->gender) }}</td>
              <td>{!! $student->status_badge !!}</td>
              <td>
                @if($student->user_id)
                  <span class="badge badge-blue">Ada</span>
                @else
                  <span class="badge" style="background:rgba(0,0,0,0.05);color:var(--color-text-secondary);">Belum</span>
                @endif
              </td>
              <td style="text-align:right;">
                <div style="display:inline-flex;gap:5px;">
                  <a href="{{ route('students.show', hid($student)) }}" class="btn btn-sm btn-outline btn-icon" title="Detail">
                    <i class="ti ti-eye" style="font-size:13px;"></i>
                  </a>
                  <a href="{{ route('students.edit', hid($student)) }}" class="btn btn-sm btn-outline btn-icon" title="Edit">
                    <i class="ti ti-edit" style="font-size:13px;"></i>
                  </a>
                  <button class="btn btn-sm btn-danger btn-icon" title="Hapus"
                          onclick="openDeleteModal('{{ hid($student) }}', '{{ $student->full_name }}')">
                    <i class="ti ti-trash" style="font-size:13px;"></i>
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" style="text-align:center;padding:50px;color:var(--color-text-secondary);">
                <i class="ti ti-users-off" style="font-size:32px;display:block;margin-bottom:10px;"></i>
                Belum ada data siswa
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<x-pagination :paginator="$students" />

{{-- MODAL HAPUS --}}
<div class="modal-backdrop" id="modalDeleteStudent">
  <div class="modal-box" style="max-width:380px;">
    <div class="modal-header">
      <div class="modal-title" style="color:var(--color-danger);">
        <i class="ti ti-alert-triangle"></i> Hapus Siswa
      </div>
      <button type="button" class="modal-close" onclick="closeModal('modalDeleteStudent')">
        <i class="ti ti-x"></i>
      </button>
    </div>
    <div class="modal-body">
      <p style="font-size:12.5px;color:var(--color-text-secondary);">
        Hapus data siswa <strong id="deleteStudentName"></strong>?
        Data orang tua & riwayat kelas juga akan ikut terhapus.
      </p>
    </div>
    <form method="POST" id="formDeleteStudent">
      @csrf @method('DELETE')
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalDeleteStudent')">Batal</button>
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
  document.getElementById('deleteStudentName').textContent = name;
  document.getElementById('formDeleteStudent').action = `/students/${id}`;
  openModal('modalDeleteStudent');
}

document.querySelectorAll('.modal-backdrop').forEach(modal => {
  modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.classList.remove('show');
  });
});
</script>
@endpush
