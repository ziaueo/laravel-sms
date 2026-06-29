@extends('layouts.app')

@section('title', 'Edit Siswa')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb">
      <i class="ti ti-home" style="font-size:11px;"></i> Beranda
      <span>/ <a href="{{ route('students.index') }}" style="color:var(--color-primary);">Data Siswa</a>
      / <a href="{{ route('students.show', $student->id) }}" style="color:var(--color-primary);">{{ $student->full_name }}</a> / Edit</span>
    </div>
    <div class="page-title">Edit Data Siswa</div>
  </div>
</div>

<form method="POST" action="{{ route('students.update', $student->id) }}" enctype="multipart/form-data">
  @csrf @method('PUT')

  <div class="form-page-grid">

    {{-- KOLOM KIRI --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

      <div class="card">
        <div class="card-header"><div class="card-title"><i class="ti ti-camera"></i> Foto</div></div>
        <div class="card-body" style="text-align:center;">
          <div class="photo-preview" id="photoPreview">
            @if($student->photo)
              <img src="{{ asset($student->photo) }}" style="width:100%;height:100%;object-fit:cover;border-radius:var(--radius-full);">
            @else
              <i class="ti ti-user" style="font-size:40px;color:var(--color-text-muted);"></i>
            @endif
          </div>
          <input type="file" name="photo" id="photoInput" accept="image/*" style="display:none;" onchange="previewPhoto(this)">
          <button type="button" class="btn btn-outline btn-sm" style="margin-top:12px;"
                  onclick="document.getElementById('photoInput').click()">
            <i class="ti ti-upload"></i> Ganti Foto
          </button>
        </div>
      </div>

      {{-- Akademik --}}
      <div class="card">
        <div class="card-header"><div class="card-title"><i class="ti ti-school"></i> Akademik</div></div>
        <div class="card-body">
          <div class="form-group">
            <label class="form-label">NISN</label>
            <input type="text" name="nisn" class="form-control" value="{{ old('nisn', $student->nisn) }}">
          </div>
          <div class="form-group">
            <label class="form-label">NIS</label>
            <input type="text" name="nis" class="form-control" value="{{ old('nis', $student->nis) }}">
          </div>
          <div class="form-group">
            <label class="form-label">Tahun Masuk</label>
            <input type="number" name="entry_year" class="form-control" value="{{ old('entry_year', $student->entry_year) }}" min="1990" max="2100">
          </div>
          <div class="form-group">
            <label class="form-label">Kelas Masuk Awal</label>
            <select name="entry_class_id" class="form-control">
              <option value="">-- Tidak diatur --</option>
              @foreach($classrooms as $c)
                <option value="{{ $c->id }}" {{ old('entry_class_id', $student->entry_class_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
              @endforeach
            </select>
            <div class="form-hint">Penempatan kelas berjalan diatur di halaman detail.</div>
          </div>
        </div>
      </div>

      {{-- Status --}}
      <div class="card">
        <div class="card-header"><div class="card-title"><i class="ti ti-flag"></i> Status</div></div>
        <div class="card-body">
          <div class="form-group">
            <label class="form-label required">Status Siswa</label>
            <select name="status" class="form-control" id="statusSelect" required onchange="toggleExit()">
              @foreach(\App\Constants\StudentStatusConstant::getAll() as $val => $label)
                <option value="{{ $val }}" {{ old('status', $student->status) == $val ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div id="exitFields" style="display:none;">
            <div class="form-group">
              <label class="form-label">Tanggal Keluar/Lulus</label>
              <input type="date" name="exit_date" class="form-control" value="{{ old('exit_date', optional($student->exit_date)->format('Y-m-d')) }}">
            </div>
            <div class="form-group">
              <label class="form-label">Keterangan</label>
              <textarea name="exit_reason" class="form-control" rows="2">{{ old('exit_reason', $student->exit_reason) }}</textarea>
            </div>
          </div>
        </div>
      </div>

    </div>

    {{-- KOLOM KANAN --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

      <div class="card">
        <div class="card-header"><div class="card-title"><i class="ti ti-user"></i> Data Pribadi</div></div>
        <div class="card-body">

          @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:16px;">
              <i class="ti ti-alert-circle" style="font-size:18px;flex-shrink:0;"></i>
              <div>@foreach($errors->all() as $error)<div style="font-size:12px;">{{ $error }}</div>@endforeach</div>
            </div>
          @endif

          <div class="form-group">
            <label class="form-label required">Nama Lengkap</label>
            <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $student->full_name) }}" required>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div class="form-group">
              <label class="form-label required">Jenis Kelamin</label>
              <select name="gender" class="form-control" required>
                @foreach(\App\Constants\GenderConstant::getAll() as $val => $label)
                  <option value="{{ $val }}" {{ old('gender', $student->gender) == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Agama</label>
              <select name="religion" class="form-control">
                <option value="">-- Pilih --</option>
                @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $r)
                  <option value="{{ $r }}" {{ old('religion', $student->religion) == $r ? 'selected' : '' }}>{{ $r }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div class="form-group">
              <label class="form-label">Tempat Lahir</label>
              <input type="text" name="birth_place" class="form-control" value="{{ old('birth_place', $student->birth_place) }}">
            </div>
            <div class="form-group">
              <label class="form-label">Tanggal Lahir</label>
              <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', optional($student->birth_date)->format('Y-m-d')) }}">
            </div>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div class="form-group">
              <label class="form-label">No. Telepon</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone', $student->phone) }}">
            </div>
            <div class="form-group">
              <label class="form-label">Golongan Darah</label>
              <select name="blood_type" class="form-control">
                <option value="">-- Pilih --</option>
                @foreach(['A','B','AB','O'] as $bt)
                  <option value="{{ $bt }}" {{ old('blood_type', $student->blood_type) == $bt ? 'selected' : '' }}>{{ $bt }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Alamat</label>
            <textarea name="address" class="form-control" rows="2">{{ old('address', $student->address) }}</textarea>
          </div>

        </div>
      </div>

      <div style="display:flex;gap:10px;">
        <a href="{{ route('students.show', $student->id) }}" class="btn btn-outline"><i class="ti ti-arrow-left"></i> Batal</a>
        <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Simpan Perubahan</button>
      </div>

    </div>
  </div>
</form>

@endsection

@push('scripts')
<script>
function previewPhoto(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = (e) => {
      document.getElementById('photoPreview').innerHTML =
        `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:var(--radius-full);">`;
    };
    reader.readAsDataURL(input.files[0]);
  }
}
function toggleExit() {
  const val = document.getElementById('statusSelect').value;
  document.getElementById('exitFields').style.display = (val === '3' || val === '4' || val === '2') ? 'block' : 'none';
}
toggleExit();
</script>
@endpush
