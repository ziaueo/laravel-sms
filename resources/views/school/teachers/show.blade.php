@extends('layouts.app')

@section('title', 'Detail Guru')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb">
      <i class="ti ti-home" style="font-size:11px;"></i> Beranda
      <span>/ <a href="{{ route('teachers.index') }}" style="color:var(--color-primary);">Guru & Staff</a>
      / {{ $teacher->full_name }}</span>
    </div>
    <div class="page-title">{{ $teacher->full_name }}</div>
    <div class="page-subtitle">{{ $teacher->position?->name ?? 'Belum ada jabatan' }}</div>
  </div>
  <div class="page-actions">
    <form method="POST" action="{{ route('teachers.toggle-active', $teacher->id) }}" style="display:inline;">
      @csrf @method('PATCH')
      <button type="submit" class="btn btn-outline">
        <i class="ti {{ $teacher->is_active ? 'ti-toggle-right' : 'ti-toggle-left' }}"></i>
        {{ $teacher->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
      </button>
    </form>
    <a href="{{ route('teachers.edit', $teacher->id) }}" class="btn btn-outline">
      <i class="ti ti-edit"></i> Edit
    </a>
  </div>
</div>

<div class="form-page-grid">

  {{-- KOLOM KIRI --}}
  <div style="display:flex;flex-direction:column;gap:14px;">

    {{-- Profil Card --}}
    <div class="card">
      <div class="card-body" style="text-align:center;padding:24px 16px;">
        <div class="teacher-avatar-lg">
          @if($teacher->photo)
            <img src="{{ asset($teacher->photo) }}" alt="{{ $teacher->full_name }}">
          @else
            <span>{{ $teacher->initials }}</span>
          @endif
        </div>
        <div style="font-size:16px;font-weight:700;color:var(--color-text-primary);margin:12px 0 4px;">
          {{ $teacher->full_name }}
        </div>
        <div style="font-size:12px;color:var(--color-text-secondary);">
          {{ $teacher->position?->name ?? '-' }}
        </div>
        <div style="display:flex;justify-content:center;gap:6px;margin-top:10px;">
          @if($teacher->is_active)
            <span class="badge badge-green">Aktif</span>
          @else
            <span class="badge badge-red">Nonaktif</span>
          @endif
          @if($teacher->user_id)
            <span class="badge badge-blue">Punya Akun</span>
          @else
            <span class="badge badge-amber">Belum Ada Akun</span>
          @endif
        </div>
      </div>
    </div>

    {{-- Akun Login --}}
    <div class="card">
      <div class="card-header">
        <div class="card-title"><i class="ti ti-login"></i> Akun Login</div>
      </div>
      <div class="card-body">
        @if($teacher->user_id)
          <div class="info-row">
            <div class="info-label">Email Login</div>
            <div class="info-value">{{ $teacher->user->email }}</div>
          </div>
          <div class="info-row">
            <div class="info-label">Status Akun</div>
            <div class="info-value">
              @if($teacher->user->is_active)
                <span class="badge badge-green">Aktif</span>
              @else
                <span class="badge badge-red">Nonaktif</span>
              @endif
            </div>
          </div>
          @if($teacher->user->must_change_password)
            <div class="info-box" style="margin-top:10px;">
              <i class="ti ti-info-circle"></i>
              Guru belum mengganti password default.
            </div>
          @endif
        @else
          <div style="text-align:center;padding:10px 0;">
            <div style="font-size:12px;color:var(--color-text-secondary);margin-bottom:12px;">
              Guru ini belum memiliki akun login.
              @if(!$teacher->email)
                <br><span style="color:var(--color-warning);">Isi email guru terlebih dahulu.</span>
              @endif
            </div>
            @if($teacher->email)
              <form method="POST" action="{{ route('teachers.create-account', $teacher->id) }}">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">
                  <i class="ti ti-user-plus"></i> Buat Akun Login
                </button>
              </form>
            @endif
          </div>
        @endif
      </div>
    </div>

  </div>

  {{-- KOLOM KANAN --}}
  <div style="display:flex;flex-direction:column;gap:14px;">

    {{-- Data Pribadi --}}
    <div class="card">
      <div class="card-header">
        <div class="card-title"><i class="ti ti-user"></i> Data Pribadi</div>
      </div>
      <div class="card-body">
        <div class="info-grid">
          <div class="info-row">
            <div class="info-label">NIP</div>
            <div class="info-value">{{ $teacher->nip ?? '-' }}</div>
          </div>
          <div class="info-row">
            <div class="info-label">Jenis Kelamin</div>
            <div class="info-value">{{ gender_label($teacher->gender) }}</div>
          </div>
          <div class="info-row">
            <div class="info-label">Tempat, Tgl Lahir</div>
            <div class="info-value">
              {{ $teacher->birth_place ?? '-' }}
              @if($teacher->birth_date), {{ format_date($teacher->birth_date) }}@endif
            </div>
          </div>
          <div class="info-row">
            <div class="info-label">Agama</div>
            <div class="info-value">{{ $teacher->religion ?? '-' }}</div>
          </div>
          <div class="info-row">
            <div class="info-label">Alamat</div>
            <div class="info-value">{{ $teacher->address ?? '-' }}</div>
          </div>
          <div class="info-row">
            <div class="info-label">No. Telepon</div>
            <div class="info-value">{{ $teacher->phone ?? '-' }}</div>
          </div>
          <div class="info-row">
            <div class="info-label">Email</div>
            <div class="info-value">{{ $teacher->email ?? '-' }}</div>
          </div>
          <div class="info-row">
            <div class="info-label">Status Kepegawaian</div>
            <div class="info-value">
              {{ $teacher->employment_status ? employment_label($teacher->employment_status) : '-' }}
            </div>
          </div>
          <div class="info-row">
            <div class="info-label">Tanggal Bergabung</div>
            <div class="info-value">{{ $teacher->join_date ? format_date($teacher->join_date) : '-' }}</div>
          </div>
          <div class="info-row">
            <div class="info-label">Pendidikan Terakhir</div>
            <div class="info-value">{{ $teacher->last_education ?? '-' }}</div>
          </div>
          <div class="info-row">
            <div class="info-label">Jurusan</div>
            <div class="info-value">{{ $teacher->major ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>

    {{-- Penugasan Mengajar --}}
    <div class="card">
      <div class="card-header">
        <div class="card-title"><i class="ti ti-book"></i> Penugasan Mengajar</div>
        <button class="btn btn-sm btn-primary" onclick="openModal('modalAddAssignment')">
          <i class="ti ti-plus"></i> Tambah
        </button>
      </div>

      @forelse($teachingAssignments as $yearName => $assignments)
        <div style="padding:10px 16px;background:var(--color-bg-soft);border-bottom:0.5px solid var(--color-border);">
          <span style="font-size:11px;font-weight:700;color:var(--color-text-secondary);">
            {{ $yearName }}
          </span>
        </div>
        @foreach($assignments as $assign)
          <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-bottom:0.5px solid var(--color-border);">
            <div>
              <div style="font-size:12.5px;font-weight:600;color:var(--color-text-primary);">
                {{ $assign->subject->name }}
              </div>
              <div style="font-size:11px;color:var(--color-text-secondary);">
                {{ $assign->classroom->gradeLevel->name }} — {{ $assign->classroom->name }}
              </div>
            </div>
            <form method="POST" action="{{ route('teachers.assignments.destroy', $assign->id) }}">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Hapus">
                <i class="ti ti-trash" style="font-size:12px;"></i>
              </button>
            </form>
          </div>
        @endforeach
      @empty
        <div style="text-align:center;padding:24px;color:var(--color-text-secondary);font-size:12px;">
          Belum ada penugasan mengajar
        </div>
      @endforelse
    </div>

  </div>
</div>

{{-- MODAL TAMBAH PENUGASAN --}}
<div class="modal-backdrop" id="modalAddAssignment">
  <div class="modal-box" style="max-width:420px;">
    <div class="modal-header">
      <div class="modal-title"><i class="ti ti-book"></i> Tambah Penugasan Mengajar</div>
      <button type="button" class="modal-close" onclick="closeModal('modalAddAssignment')">
        <i class="ti ti-x"></i>
      </button>
    </div>
    <form method="POST" action="{{ route('teachers.assignments.store', $teacher->id) }}">
      @csrf
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label required">Tahun Ajaran</label>
          <select name="school_year_id" class="form-control" required>
            <option value="">Pilih tahun ajaran</option>
            @if($activeSchoolYear)
              <option value="{{ $activeSchoolYear->id }}" selected>
                {{ $activeSchoolYear->name }} (Aktif)
              </option>
            @endif
          </select>
        </div>
        <div class="form-group">
          <label class="form-label required">Kelas</label>
          <select name="classroom_id" class="form-control" required>
            <option value="">Pilih kelas</option>
            @foreach($classrooms as $classroom)
              <option value="{{ $classroom->id }}">
                {{ $classroom->gradeLevel->name }} — {{ $classroom->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label required">Mata Pelajaran</label>
          <select name="subject_id" class="form-control" required>
            <option value="">Pilih mata pelajaran</option>
            @foreach($subjects as $subject)
              <option value="{{ $subject->id }}">
                {{ $subject->name }} ({{ $subject->gradeLevel->name }})
              </option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalAddAssignment')">Batal</button>
        <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
function openModal(id) { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }

document.querySelectorAll('.modal-backdrop').forEach(modal => {
  modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.classList.remove('show');
  });
});
</script>
@endpush
