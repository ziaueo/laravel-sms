@extends('layouts.app')

@section('title', 'Rapot')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb">
      <i class="ti ti-home" style="font-size:11px;"></i> Beranda <span>/ Rapot</span>
    </div>
    <div class="page-title">Rapot Siswa</div>
    <div class="page-subtitle">{{ $school->name }} @if($activeYear) — T.A. {{ $activeYear->year }} {{ $activeYear->semester_label }} @endif</div>
  </div>
  @if($classroom && $activeYear)
  <div class="page-actions">
    <form method="POST" action="{{ route('report-cards.generate') }}" onsubmit="return confirm('Generate/perbarui rapot semua siswa di kelas ini? Nilai akhir akan dihitung ulang dari data penilaian & absensi.')">
      @csrf
      <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">
      <button type="submit" class="btn btn-primary"><i class="ti ti-refresh"></i> Generate Rapot</button>
    </form>
  </div>
  @endif
</div>

<div class="card">
  <div class="card-body" style="padding:14px 16px;">
    <form method="GET" action="{{ route('report-cards.index') }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
      <div style="min-width:200px;">
        <select name="classroom_id" class="form-control" onchange="this.form.submit()">
          @forelse($classrooms as $c)
            <option value="{{ $c->id }}" {{ $classroom && $classroom->id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
          @empty
            <option value="">Belum ada kelas</option>
          @endforelse
        </select>
      </div>
    </form>
  </div>
</div>

@if(!$activeYear)
  <div class="alert alert-warning"><i class="ti ti-alert-triangle"></i> Belum ada tahun ajaran aktif.</div>
@elseif(!$classroom)
  <div class="card"><div class="card-body" style="text-align:center;padding:40px;color:var(--color-text-secondary);">Belum ada kelas.</div></div>
@else
<div class="card">
  <div class="card-header"><div class="card-title"><i class="ti ti-report"></i> {{ $classroom->name }}</div></div>
  <div class="card-body" style="padding:0;">
    <div class="table-wrapper">
      <table>
        <thead>
          <tr><th style="width:42px;">#</th><th>Siswa</th><th style="text-align:center;">Rata-rata</th><th style="text-align:center;">Peringkat</th><th style="text-align:center;">Status</th><th style="text-align:right;">Aksi</th></tr>
        </thead>
        <tbody>
          @forelse($students as $i => $student)
            @php $rc = $reportCards->get($student->id); @endphp
            <tr>
              <td>{{ $i + 1 }}</td>
              <td><div style="font-weight:600;">{{ $student->full_name }}</div></td>
              <td style="text-align:center;">{{ $rc?->gpa !== null ? $rc->gpa : '—' }}</td>
              <td style="text-align:center;">{{ $rc?->rank_in_class ?? '—' }}</td>
              <td style="text-align:center;">
                @if(!$rc)
                  <span class="badge" style="background:rgba(0,0,0,0.05);color:var(--color-text-secondary);">Belum dibuat</span>
                @elseif($rc->is_published)
                  <span class="badge badge-green">Terbit</span>
                @else
                  <span class="badge badge-amber">Draft</span>
                @endif
              </td>
              <td style="text-align:right;">
                @if($rc)
                <div style="display:inline-flex;gap:5px;">
                  <a href="{{ route('report-cards.show', hid($rc)) }}" class="btn btn-sm btn-outline btn-icon" title="Lihat"><i class="ti ti-eye" style="font-size:13px;"></i></a>
                  <a href="{{ route('report-cards.pdf', hid($rc)) }}" class="btn btn-sm btn-outline btn-icon" title="PDF"><i class="ti ti-file-type-pdf" style="font-size:13px;"></i></a>
                </div>
                @else
                  <span style="font-size:11px;color:var(--color-text-muted);">Klik Generate Rapot</span>
                @endif
              </td>
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
