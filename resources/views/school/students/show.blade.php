@extends('layouts.app')

@section('title', $student->full_name)

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb">
      <i class="ti ti-home" style="font-size:11px;"></i> Beranda
      <span>/ <a href="{{ route('students.index') }}" style="color:var(--color-primary);">Data Siswa</a> / Detail</span>
    </div>
    <div class="page-title">{{ $student->full_name }}</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-outline"><i class="ti ti-edit"></i> Edit</a>
  </div>
</div>

<div class="form-page-grid">

  {{-- KIRI: profil + akun --}}
  <div style="display:flex;flex-direction:column;gap:14px;">

    <div class="card">
      <div class="card-body" style="text-align:center;">
        <div class="photo-preview" style="margin:0 auto;">
          @if($student->photo)
            <img src="{{ asset($student->photo) }}" style="width:100%;height:100%;object-fit:cover;border-radius:var(--radius-full);">
          @else
            <span style="font-size:28px;font-weight:700;color:var(--color-primary-dark);">{{ $student->initials }}</span>
          @endif
        </div>
        <div style="font-weight:700;font-size:15px;margin-top:12px;">{{ $student->full_name }}</div>
        <div style="font-size:12px;color:var(--color-text-secondary);">{{ $student->nisn ?? 'NISN -' }}</div>
        <div style="margin-top:8px;">{!! $student->status_badge !!}</div>
        <div style="margin-top:6px;font-size:12px;color:var(--color-text-secondary);">
          Kelas: <strong>{{ $student->activeClassroom?->classroom?->name ?? 'Belum ditempatkan' }}</strong>
        </div>
      </div>
    </div>

    {{-- Akun login --}}
    <div class="card">
      <div class="card-header"><div class="card-title"><i class="ti ti-key"></i> Akun Login</div></div>
      <div class="card-body">
        @if($student->user_id)
          <div style="font-size:12.5px;"><i class="ti ti-mail" style="color:var(--color-primary);"></i> {{ $student->user->email }}</div>
          <div style="margin-top:6px;"><span class="badge badge-green">Akun Aktif</span></div>
        @else
          <p style="font-size:12px;color:var(--color-text-secondary);margin-bottom:10px;">
            Siswa belum punya akun login. Akun dibuat dari NIS/NISN.
          </p>
          <form method="POST" action="{{ route('students.create-account', $student->id) }}">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm" style="width:100%;justify-content:center;">
              <i class="ti ti-user-plus"></i> Buat Akun Login
            </button>
          </form>
        @endif
      </div>
    </div>

  </div>

  {{-- KANAN: detail, kelas, ortu --}}
  <div style="display:flex;flex-direction:column;gap:14px;">

    {{-- Data pribadi --}}
    <div class="card">
      <div class="card-header"><div class="card-title"><i class="ti ti-user"></i> Data Pribadi</div></div>
      <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px 20px;font-size:12.5px;">
          <div><span style="color:var(--color-text-secondary);">NIS</span><br><strong>{{ $student->nis ?? '-' }}</strong></div>
          <div><span style="color:var(--color-text-secondary);">NISN</span><br><strong>{{ $student->nisn ?? '-' }}</strong></div>
          <div><span style="color:var(--color-text-secondary);">Jenis Kelamin</span><br><strong>{{ gender_label($student->gender) }}</strong></div>
          <div><span style="color:var(--color-text-secondary);">Agama</span><br><strong>{{ $student->religion ?? '-' }}</strong></div>
          <div><span style="color:var(--color-text-secondary);">Tempat, Tgl Lahir</span><br><strong>{{ $student->birth_place ?? '-' }}, {{ format_date($student->birth_date) }}</strong></div>
          <div><span style="color:var(--color-text-secondary);">Gol. Darah</span><br><strong>{{ $student->blood_type ?? '-' }}</strong></div>
          <div><span style="color:var(--color-text-secondary);">Telepon</span><br><strong>{{ $student->phone ?? '-' }}</strong></div>
          <div><span style="color:var(--color-text-secondary);">Tahun Masuk</span><br><strong>{{ $student->entry_year ?? '-' }}</strong></div>
          <div style="grid-column:1/-1;"><span style="color:var(--color-text-secondary);">Alamat</span><br><strong>{{ $student->address ?? '-' }}</strong></div>
        </div>
      </div>
    </div>

    {{-- Penempatan kelas --}}
    <div class="card">
      <div class="card-header"><div class="card-title"><i class="ti ti-door"></i> Penempatan Kelas</div></div>
      <div class="card-body">
        @if($activeYear)
          <form method="POST" action="{{ route('students.assign-classroom', $student->id) }}"
                style="display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;">
            @csrf
            <input type="hidden" name="school_year_id" value="{{ $activeYear->id }}">
            <div class="form-group" style="flex:1;min-width:180px;margin:0;">
              <label class="form-label">Kelas (T.A. {{ $activeYear->year }} {{ $activeYear->semester_label }})</label>
              <select name="classroom_id" class="form-control" required>
                <option value="">-- Pilih kelas --</option>
                @foreach($classrooms as $c)
                  <option value="{{ $c->id }}" {{ $student->activeClassroom?->classroom_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Tempatkan</button>
          </form>
        @else
          <p style="font-size:12px;color:var(--color-warning);">Belum ada tahun ajaran aktif. Atur dulu di Master Data.</p>
        @endif

        @if($classroomHistory->count())
          <div style="margin-top:14px;">
            <div style="font-size:11px;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Riwayat Kelas</div>
            <div class="table-wrapper">
              <table>
                <thead><tr><th>Tahun Ajaran</th><th>Kelas</th><th>Status</th></tr></thead>
                <tbody>
                  @foreach($classroomHistory as $h)
                    <tr>
                      <td>{{ $h->schoolYear->name ?? '-' }}</td>
                      <td>{{ $h->classroom->name ?? '-' }}</td>
                      <td>@if($h->is_active)<span class="badge badge-green">Aktif</span>@else<span class="badge badge-blue">Lampau</span>@endif</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        @endif
      </div>
    </div>

    {{-- Orang Tua / Wali --}}
    <div class="card">
      <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
        <div class="card-title"><i class="ti ti-users"></i> Orang Tua / Wali</div>
        <button class="btn btn-sm btn-outline" onclick="openModal('modalAddParent')"><i class="ti ti-plus"></i> Tambah</button>
      </div>
      <div class="card-body">
        @forelse($student->parents as $parent)
          <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:0.5px solid rgba(200,221,212,0.4);">
            <div>
              <div style="font-weight:600;font-size:13px;">
                {{ $parent->full_name }}
                <span class="badge badge-blue" style="margin-left:4px;">{{ parent_relation_label($parent->relation) }}</span>
                @if($parent->is_primary)<span class="badge badge-green">Utama</span>@endif
              </div>
              <div style="font-size:11.5px;color:var(--color-text-secondary);">
                {{ $parent->job ?? '-' }} @if($parent->phone) · <i class="ti ti-phone"></i> {{ $parent->phone }}@endif
              </div>
            </div>
            <form method="POST" action="{{ route('students.parents.destroy', $parent->id) }}"
                  onsubmit="return confirm('Hapus data orang tua/wali ini?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger btn-icon"><i class="ti ti-trash" style="font-size:13px;"></i></button>
            </form>
          </div>
        @empty
          <p style="font-size:12px;color:var(--color-text-secondary);">Belum ada data orang tua/wali.</p>
        @endforelse
      </div>
    </div>

  </div>
</div>

{{-- MODAL TAMBAH ORANG TUA --}}
<div class="modal-backdrop" id="modalAddParent">
  <div class="modal-box" style="max-width:480px;">
    <form method="POST" action="{{ route('students.parents.store', $student->id) }}">
      @csrf
      <div class="modal-header">
        <div class="modal-title"><i class="ti ti-user-plus"></i> Tambah Orang Tua / Wali</div>
        <button type="button" class="modal-close" onclick="closeModal('modalAddParent')"><i class="ti ti-x"></i></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label required">Nama Lengkap</label>
          <input type="text" name="full_name" class="form-control" required>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          <div class="form-group">
            <label class="form-label required">Hubungan</label>
            <select name="relation" class="form-control" required>
              @foreach(\App\Constants\ParentRelationConstant::getAll() as $val => $label)
                <option value="{{ $val }}">{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">No. Telepon</label>
            <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx">
          </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          <div class="form-group">
            <label class="form-label">Pekerjaan</label>
            <input type="text" name="job" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Pendidikan</label>
            <input type="text" name="education" class="form-control">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" placeholder="Untuk akun orang tua">
        </div>
        <div class="form-group">
          <label class="form-label">Alamat</label>
          <textarea name="address" class="form-control" rows="2"></textarea>
        </div>
        <label style="display:flex;align-items:center;gap:6px;font-size:12.5px;cursor:pointer;">
          <input type="checkbox" name="is_primary" value="1"> Jadikan kontak utama
        </label>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalAddParent')">Batal</button>
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
  modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.remove('show'); });
});
</script>
@endpush
