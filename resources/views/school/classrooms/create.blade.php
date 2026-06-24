@extends('layouts.app')

@section('title', 'Tambah Kelas')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb">
      <i class="ti ti-home" style="font-size:11px;"></i> Beranda
      <span>/ <a href="{{ route('master.index') }}" style="color:var(--color-primary);">Master Data</a>
      / <a href="{{ route('master.classrooms.index') }}" style="color:var(--color-primary);">Kelas</a>
      / Tambah</span>
    </div>
    <div class="page-title">Tambah Kelas Baru</div>
    <div class="page-subtitle">Buat rombongan belajar baru untuk tahun ajaran ini</div>
  </div>
</div>

<div class="card" style="max-width:600px;">
  <div class="card-body">
    <form method="POST" action="{{ route('master.classrooms.store') }}">
      @csrf

      <div class="form-group">
        <label class="form-label required">Tahun Ajaran</label>
        <select name="school_year_id" class="form-control" required>
          <option value="">Pilih tahun ajaran</option>
          @foreach($schoolYears as $sy)
            <option value="{{ $sy->id }}" {{ $activeSchoolYear?->id == $sy->id ? 'selected' : '' }}>
              {{ $sy->name }} {{ $sy->is_active ? '(Aktif)' : '' }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label class="form-label required">Tingkat Kelas</label>
        <select name="grade_level_id" class="form-control" required>
          <option value="">Pilih tingkat kelas</option>
          @foreach($gradeLevels as $gl)
            <option value="{{ $gl->id }}">{{ $gl->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label class="form-label required">Nama Kelas</label>
        <input type="text" name="name" class="form-control"
               placeholder="Contoh: 1A / 1B / Kelompok A-1" value="{{ old('name') }}" required>
        <div class="form-hint">Nama singkat kelas yang akan tampil di jadwal & absensi</div>
      </div>

      <div class="form-group">
        <label class="form-label required">Kapasitas Siswa</label>
        <input type="number" name="capacity" class="form-control"
               value="{{ old('capacity', 30) }}" min="1" max="100" required>
      </div>

      <div class="form-group">
        <label class="form-label">Wali Kelas</label>
        <select name="homeroom_teacher_id" class="form-control">
          <option value="">-- Belum ditentukan --</option>
          @foreach($teachers as $teacher)
            <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
          @endforeach
        </select>
      </div>

      @if($majors->count() > 0)
      <div class="form-group">
        <label class="form-label">Jurusan / Peminatan</label>
        <select name="major_id" class="form-control">
          <option value="">-- Tidak ada jurusan --</option>
          @foreach($majors as $major)
            <option value="{{ $major->id }}">{{ $major->name }}</option>
          @endforeach
        </select>
        <div class="form-hint">Kosongkan untuk kelas umum (SD/PAUD/SMP/Kelas 10 SMA)</div>
      </div>
      @endif

      <div style="display:flex;gap:10px;margin-top:20px;">
        <a href="{{ route('master.classrooms.index') }}" class="btn btn-outline">
          <i class="ti ti-arrow-left"></i> Batal
        </a>
        <button type="submit" class="btn btn-primary">
          <i class="ti ti-check"></i> Simpan Kelas
        </button>
      </div>

    </form>
  </div>
</div>

@endsection
