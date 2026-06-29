@extends('layouts.app')

@section('title', 'Absensi Siswa')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb">
      <i class="ti ti-home" style="font-size:11px;"></i> Beranda <span>/ Absensi</span>
    </div>
    <div class="page-title">Absensi Siswa</div>
    <div class="page-subtitle">{{ $school->name }} @if($activeYear) — T.A. {{ $activeYear->year }} {{ $activeYear->semester_label }} @endif</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('attendances.recap', ['classroom_id' => $classroom?->id]) }}" class="btn btn-outline"><i class="ti ti-report"></i> Rekap Bulanan</a>
  </div>
</div>

{{-- FILTER --}}
<div class="card">
  <div class="card-body" style="padding:14px 16px;">
    <form method="GET" action="{{ route('attendances.index') }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
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
        <input type="date" name="date" value="{{ $date }}" class="form-control" onchange="this.form.submit()" max="{{ now()->toDateString() }}">
      </div>
    </form>
  </div>
</div>

@if(!$activeYear)
  <div class="alert alert-warning"><i class="ti ti-alert-triangle"></i> Belum ada tahun ajaran aktif.</div>
@elseif(!$classroom)
  <div class="card"><div class="card-body" style="text-align:center;padding:40px;color:var(--color-text-secondary);">Belum ada kelas.</div></div>
@else

<form method="POST" action="{{ route('attendances.store') }}">
  @csrf
  <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">
  <input type="hidden" name="date" value="{{ $date }}">

  <div class="card">
    <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
      <div class="card-title"><i class="ti ti-clipboard-check"></i> {{ $classroom->name }} — {{ format_date($date) }}</div>
      <div style="display:flex;gap:6px;">
        <button type="button" class="btn btn-sm btn-outline" onclick="setAll(1)">Semua Hadir</button>
      </div>
    </div>
    <div class="card-body" style="padding:0;">
      <div class="table-wrapper">
        <table>
          <thead>
            <tr><th style="width:42px;">#</th><th>Siswa</th><th style="width:340px;">Status</th><th>Keterangan</th></tr>
          </thead>
          <tbody>
            @forelse($students as $i => $student)
              @php $att = $existing->get($student->id); $cur = $att->status ?? 1; @endphp
              <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                  <div style="font-weight:600;">{{ $student->full_name }}</div>
                  <div style="font-size:11px;color:var(--color-text-secondary);">{{ $student->nisn ?? $student->nis ?? '-' }}</div>
                </td>
                <td>
                  <div class="att-options">
                    @foreach(\App\Constants\AttendanceConstant::getAllStatus() as $val => $label)
                      <label class="att-opt">
                        <input type="radio" name="status[{{ $student->id }}]" value="{{ $val }}" {{ $cur == $val ? 'checked' : '' }}>
                        <span>{{ $label }}</span>
                      </label>
                    @endforeach
                  </div>
                </td>
                <td>
                  <input type="text" name="notes[{{ $student->id }}]" class="form-control" style="min-width:160px;"
                         value="{{ $att->notes ?? '' }}" placeholder="opsional">
                </td>
              </tr>
            @empty
              <tr><td colspan="4" style="text-align:center;padding:40px;color:var(--color-text-secondary);">Belum ada siswa di kelas ini.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  @if($students->count())
  <div style="display:flex;justify-content:flex-end;margin-top:14px;">
    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy"></i> Simpan Absensi</button>
  </div>
  @endif
</form>

@endif

@endsection

@push('styles')
<style>
.att-options { display:flex; gap:4px; flex-wrap:wrap; }
.att-opt { cursor:pointer; }
.att-opt input { display:none; }
.att-opt span { display:inline-block; padding:4px 9px; border-radius:var(--radius-pill,99px); font-size:11.5px; border:0.5px solid rgba(0,0,0,0.12); color:var(--color-text-secondary); }
.att-opt input:checked + span { background:var(--grad-primary-dark); color:#fff; border-color:transparent; }
</style>
@endpush

@push('scripts')
<script>
function setAll(val){
  document.querySelectorAll(`input[type=radio][value="${val}"]`).forEach(r => r.checked = true);
}
</script>
@endpush
