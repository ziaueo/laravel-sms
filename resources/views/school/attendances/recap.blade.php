@extends('layouts.app')

@section('title', 'Rekap Absensi')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb">
      <i class="ti ti-home" style="font-size:11px;"></i> Beranda
      <span>/ <a href="{{ route('attendances.index') }}" style="color:var(--color-primary);">Absensi</a> / Rekap</span>
    </div>
    <div class="page-title">Rekap Absensi Bulanan</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('attendances.index', ['classroom_id' => $classroom?->id]) }}" class="btn btn-outline"><i class="ti ti-arrow-left"></i> Input Absensi</a>
  </div>
</div>

<div class="card">
  <div class="card-body" style="padding:14px 16px;">
    <form method="GET" action="{{ route('attendances.recap') }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
      <div style="min-width:200px;">
        <select name="classroom_id" class="form-control" onchange="this.form.submit()">
          @forelse($classrooms as $c)
            <option value="{{ $c->id }}" {{ $classroom && $classroom->id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
          @empty
            <option value="">Belum ada kelas</option>
          @endforelse
        </select>
      </div>
      <div>
        <input type="month" name="month" value="{{ $month }}" class="form-control" onchange="this.form.submit()">
      </div>
    </form>
  </div>
</div>

@if($classroom)
<div class="card">
  <div class="card-header"><div class="card-title"><i class="ti ti-report"></i> {{ $classroom->name }} — {{ \Carbon\Carbon::parse($month.'-01')->translatedFormat('F Y') }}</div></div>
  <div class="card-body" style="padding:0;">
    <div class="table-wrapper">
      <table>
        <thead>
          <tr><th style="width:42px;">#</th><th>Siswa</th>
            <th style="text-align:center;">Hadir</th><th style="text-align:center;">Sakit</th>
            <th style="text-align:center;">Izin</th><th style="text-align:center;">Alpa</th></tr>
        </thead>
        <tbody>
          @forelse($students as $i => $student)
            @php $r = $recap->get($student->id, ['hadir'=>0,'sakit'=>0,'izin'=>0,'alpa'=>0]); @endphp
            <tr>
              <td>{{ $i + 1 }}</td>
              <td><div style="font-weight:600;">{{ $student->full_name }}</div></td>
              <td style="text-align:center;"><span class="badge badge-hadir">{{ $r['hadir'] }}</span></td>
              <td style="text-align:center;"><span class="badge badge-sakit">{{ $r['sakit'] }}</span></td>
              <td style="text-align:center;"><span class="badge badge-izin">{{ $r['izin'] }}</span></td>
              <td style="text-align:center;"><span class="badge badge-alpa">{{ $r['alpa'] }}</span></td>
            </tr>
          @empty
            <tr><td colspan="6" style="text-align:center;padding:40px;color:var(--color-text-secondary);">Belum ada siswa.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endif

@endsection
