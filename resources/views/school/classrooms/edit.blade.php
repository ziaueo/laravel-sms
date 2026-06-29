@extends('layouts.app')

@section('title', 'Edit Kelas')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb">
      <i class="ti ti-home" style="font-size:11px;"></i> Beranda
      <span>/ <a href="{{ route('master.index') }}" style="color:var(--color-primary);">Master Data</a>
      / <a href="{{ route('classrooms.index') }}" style="color:var(--color-primary);">Kelas</a>
      / Edit</span>
    </div>
    <div class="page-title">Edit Kelas — {{ $classroom->name }}</div>
    <div class="page-subtitle">{{ $classroom->gradeLevel->name }} — {{ $classroom->schoolYear->name }}</div>
  </div>
</div>

<div class="card" style="max-width:600px;">
  <div class="card-body">
    <form method="POST" action="{{ route('classrooms.update', $classroom->id) }}">
      @csrf @method('PUT')

      <div class="form-group">
        <label class="form-label required">Tahun Ajaran</label>
        <select name="school_year_id" class="form-control" required>
          @foreach($schoolYears as $sy)
            <option value="{{ $sy->id }}" {{ $classroom->school_year_id == $sy->id ? 'selected' : '' }}>
              {{ $sy->name }} {{ $sy->is_active ? '(Aktif)' : '' }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label class="form-label required">Tingkat Kelas</label>
        <select name="grade_level_id" class="form-control" required>
          @foreach($gradeLevels as $gl)
            <option value="{{ $gl->id }}" {{ $classroom->grade_level_id == $gl->id ? 'selected' : '' }}>
              {{ $gl->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label class="form-label required">Nama Kelas</label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name', $classroom->name) }}" required>
      </div>

      <div class="form-group">
        <label class="form-label required">Kapasitas Siswa</label>
        <input type="number" name="capacity" class="form-control"
               value="{{ old('capacity', $classroom->capacity) }}" min="1" max="100" required>
      </div>

      <div class="form-group">
        <label class="form-label">Wali Kelas</label>
        <select name="homeroom_teacher_id" class="form-control">
          <option value="">-- Belum ditentukan --</option>
          @foreach($teachers as $teacher)
            <option value="{{ $teacher->id }}"
              {{ $classroom->homeroom_teacher_id == $teacher->id ? 'selected' : '' }}>
              {{ $teacher->full_name }}
            </option>
          @endforeach
        </select>
      </div>

      @if($majors->count() > 0)
      <div class="form-group">
        <label class="form-label">Jurusan / Peminatan</label>
        <select name="major_id" class="form-control">
          <option value="">-- Tidak ada jurusan --</option>
          @foreach($majors as $major)
            <option value="{{ $major->id }}" {{ $classroom->major_id == $major->id ? 'selected' : '' }}>
              {{ $major->name }}
            </option>
          @endforeach
        </select>
      </div>
      @endif

      <div class="form-group">
        <label class="checkbox-item">
          <input type="hidden" name="is_active" value="0">
          <input type="checkbox" name="is_active" value="1"
                 {{ $classroom->is_active ? 'checked' : '' }}>
          Kelas aktif
        </label>
      </div>

      <div style="display:flex;gap:10px;margin-top:20px;">
        <a href="{{ route('classrooms.index') }}" class="btn btn-outline">
          <i class="ti ti-arrow-left"></i> Batal
        </a>
        <button type="submit" class="btn btn-primary">
          <i class="ti ti-check"></i> Simpan Perubahan
        </button>
      </div>

    </form>
  </div>
</div>

@endsection
