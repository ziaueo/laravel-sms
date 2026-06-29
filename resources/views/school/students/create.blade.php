@extends('layouts.app')

@section('title', 'Tambah Siswa')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb">
      <i class="ti ti-home" style="font-size:11px;"></i> Beranda
      <span>/ <a href="{{ route('students.index') }}" style="color:var(--color-primary);">Data Siswa</a> / Tambah</span>
    </div>
    <div class="page-title">Tambah Siswa Baru</div>
  </div>
</div>

<form method="POST" action="{{ route('students.store') }}" enctype="multipart/form-data">
  @csrf

  <div class="form-page-grid">

    {{-- KOLOM KIRI --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

      {{-- Foto --}}
      <div class="card">
        <div class="card-header"><div class="card-title"><i class="ti ti-camera"></i> Foto</div></div>
        <div class="card-body" style="text-align:center;">
          <div class="photo-preview" id="photoPreview">
            <i class="ti ti-user" style="font-size:40px;color:var(--color-text-muted);"></i>
          </div>
          <input type="file" name="photo" id="photoInput" accept="image/*"
                 style="display:none;" onchange="previewPhoto(this)">
          <button type="button" class="btn btn-outline btn-sm" style="margin-top:12px;"
                  onclick="document.getElementById('photoInput').click()">
            <i class="ti ti-upload"></i> Upload Foto
          </button>
          <div class="form-hint" style="margin-top:6px;">JPG/PNG/WebP, maks 2MB</div>
        </div>
      </div>

      {{-- Akademik --}}
      <div class="card">
        <div class="card-header"><div class="card-title"><i class="ti ti-school"></i> Akademik</div></div>
        <div class="card-body">
          <div class="form-group">
            <label class="form-label">NISN</label>
            <input type="text" name="nisn" class="form-control" value="{{ old('nisn') }}" placeholder="Nomor Induk Siswa Nasional">
          </div>
          <div class="form-group">
            <label class="form-label">NIS</label>
            <input type="text" name="nis" class="form-control" value="{{ old('nis') }}" placeholder="Nomor Induk Siswa">
          </div>
          <div class="form-group">
            <label class="form-label">Tahun Masuk</label>
            <input type="number" name="entry_year" class="form-control" value="{{ old('entry_year', date('Y')) }}" min="1990" max="2100">
          </div>
          <div class="form-group">
            <label class="form-label">Kelas
              @if($activeYear)<span class="form-hint" style="display:inline;">(T.A. {{ $activeYear->year }})</span>@endif
            </label>
            <select name="entry_class_id" class="form-control">
              <option value="">-- Belum ditempatkan --</option>
              @foreach($classrooms as $c)
                <option value="{{ $c->id }}" {{ old('entry_class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
              @endforeach
            </select>
            @if(!$activeYear)
              <div class="form-hint" style="color:var(--color-warning);">Belum ada tahun ajaran aktif — atur dulu di Master Data.</div>
            @endif
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
            <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" placeholder="Nama lengkap sesuai akta" required>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div class="form-group">
              <label class="form-label required">Jenis Kelamin</label>
              <select name="gender" class="form-control" required>
                <option value="">-- Pilih --</option>
                @foreach(\App\Constants\GenderConstant::getAll() as $val => $label)
                  <option value="{{ $val }}" {{ old('gender') == $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Agama</label>
              <select name="religion" class="form-control">
                <option value="">-- Pilih --</option>
                @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $r)
                  <option value="{{ $r }}" {{ old('religion') == $r ? 'selected' : '' }}>{{ $r }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div class="form-group">
              <label class="form-label">Tempat Lahir</label>
              <input type="text" name="birth_place" class="form-control" value="{{ old('birth_place') }}" placeholder="Kota">
            </div>
            <div class="form-group">
              <label class="form-label">Tanggal Lahir</label>
              <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}">
            </div>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div class="form-group">
              <label class="form-label">No. Telepon</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
            </div>
            <div class="form-group">
              <label class="form-label">Golongan Darah</label>
              <select name="blood_type" class="form-control">
                <option value="">-- Pilih --</option>
                @foreach(['A','B','AB','O'] as $bt)
                  <option value="{{ $bt }}" {{ old('blood_type') == $bt ? 'selected' : '' }}>{{ $bt }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Alamat</label>
            <textarea name="address" class="form-control" rows="2" placeholder="Alamat lengkap">{{ old('address') }}</textarea>
          </div>

        </div>
      </div>

      <div style="display:flex;gap:10px;">
        <a href="{{ route('students.index') }}" class="btn btn-outline"><i class="ti ti-arrow-left"></i> Batal</a>
        <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Simpan Data Siswa</button>
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
</script>
@endpush
