@extends('layouts.app')

@section('title', $extracurricular->name)

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb"><i class="ti ti-home" style="font-size:11px;"></i> Beranda
      <span>/ <a href="{{ route('extracurriculars.index') }}" style="color:var(--color-primary);">Ekstrakurikuler</a> / {{ $extracurricular->name }}</span></div>
    <div class="page-title">{{ $extracurricular->name }}</div>
    <div class="page-subtitle">Anggota @if($activeYear) T.A. {{ $activeYear->year }} {{ $activeYear->semester_label }} @endif</div>
  </div>
</div>

<div class="form-page-grid">
  <div class="card">
    <div class="card-header"><div class="card-title"><i class="ti ti-user-plus"></i> Tambah Anggota</div></div>
    <div class="card-body">
      @if($activeYear)
        <form method="POST" action="{{ route('extracurriculars.members.add', hid($extracurricular)) }}" style="display:flex;gap:8px;">
          @csrf
          <select name="student_id" class="form-control" required>
            <option value="">-- Pilih siswa --</option>
            @foreach($students as $s)<option value="{{ $s->id }}">{{ $s->full_name }}</option>@endforeach
          </select>
          <button class="btn btn-primary"><i class="ti ti-plus"></i></button>
        </form>
      @else
        <p style="font-size:12px;color:var(--color-warning);">Belum ada tahun ajaran aktif.</p>
      @endif
    </div>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title"><i class="ti ti-users"></i> Daftar Anggota ({{ $members->count() }})</div></div>
    <div class="card-body" style="padding:0;">
      <div class="table-wrapper">
        <table>
          <thead><tr><th>#</th><th>Siswa</th><th style="text-align:right;">Aksi</th></tr></thead>
          <tbody>
            @forelse($members as $i => $m)
              <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $m->student->full_name ?? '-' }}</td>
                <td style="text-align:right;">
                  <form method="POST" action="{{ route('extracurriculars.members.remove', hid($m)) }}" onsubmit="return confirm('Hapus anggota?')">@csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger btn-icon"><i class="ti ti-trash" style="font-size:13px;"></i></button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="3" style="text-align:center;padding:30px;color:var(--color-text-secondary);">Belum ada anggota.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection
