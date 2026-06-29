@extends('layouts.app')

@section('title', 'Jadwal Pelajaran')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb">
      <i class="ti ti-home" style="font-size:11px;"></i> Beranda <span>/ Jadwal Pelajaran</span>
    </div>
    <div class="page-title">Jadwal Pelajaran</div>
    <div class="page-subtitle">
      {{ $school->name }}
      @if($activeYear) — T.A. {{ $activeYear->year }} {{ $activeYear->semester_label }} @endif
    </div>
  </div>
  @if($classroom && $activeYear)
  <div class="page-actions">
    <button class="btn btn-primary" onclick="openModal('modalAddSchedule')"><i class="ti ti-plus"></i> Tambah Slot</button>
  </div>
  @endif
</div>

{{-- PILIH KELAS --}}
<div class="card">
  <div class="card-body" style="padding:14px 16px;">
    <form method="GET" action="{{ route('schedules.index') }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
      <label class="form-label" style="margin:0;">Kelas:</label>
      <div style="min-width:220px;">
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
  <div class="alert alert-warning"><i class="ti ti-alert-triangle"></i> Belum ada tahun ajaran aktif. Atur dulu di Master Data.</div>
@elseif(!$classroom)
  <div class="card"><div class="card-body" style="text-align:center;padding:40px;color:var(--color-text-secondary);">
    <i class="ti ti-calendar-off" style="font-size:32px;display:block;margin-bottom:10px;"></i>
    Belum ada kelas untuk tahun ajaran aktif.
  </div></div>
@else

{{-- GRID JADWAL --}}
<div class="schedule-grid">
  @foreach(\App\Constants\ScheduleConstant::getAllDay() as $dayVal => $dayLabel)
    <div class="schedule-col">
      <div class="schedule-col-head">{{ $dayLabel }}</div>
      <div class="schedule-col-body">
        @forelse($schedulesByDay->get($dayVal, collect()) as $s)
          <div class="schedule-item {{ $s->type != 1 ? 'schedule-item-alt' : '' }}">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:6px;">
              <div style="font-size:11px;font-weight:700;color:var(--color-primary-dark);">
                {{ \Illuminate\Support\Str::of($s->start_time)->substr(0,5) }}–{{ \Illuminate\Support\Str::of($s->end_time)->substr(0,5) }}
              </div>
              <form method="POST" action="{{ route('schedules.destroy', $s->id) }}" onsubmit="return confirm('Hapus slot ini?')">
                @csrf @method('DELETE')
                <button class="schedule-del" title="Hapus"><i class="ti ti-x"></i></button>
              </form>
            </div>
            @if($s->type == 1)
              <div style="font-weight:600;font-size:12.5px;margin-top:2px;">{{ $s->subject?->name ?? 'Mapel' }}</div>
              <div style="font-size:11px;color:var(--color-text-secondary);">
                <i class="ti ti-user"></i> {{ $s->teacher?->full_name ?? '-' }}
              </div>
            @else
              <div style="font-weight:600;font-size:12.5px;margin-top:2px;">{{ $s->type_label }}</div>
            @endif
          </div>
        @empty
          <div style="text-align:center;padding:14px 6px;color:var(--color-text-muted);font-size:11px;">Kosong</div>
        @endforelse
      </div>
    </div>
  @endforeach
</div>

{{-- MODAL TAMBAH --}}
<div class="modal-backdrop" id="modalAddSchedule">
  <div class="modal-box" style="max-width:440px;">
    <form method="POST" action="{{ route('schedules.store') }}">
      @csrf
      <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">
      <div class="modal-header">
        <div class="modal-title"><i class="ti ti-calendar-plus"></i> Tambah Slot Jadwal</div>
        <button type="button" class="modal-close" onclick="closeModal('modalAddSchedule')"><i class="ti ti-x"></i></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label required">Hari</label>
          <select name="day_of_week" class="form-control" required>
            @foreach(\App\Constants\ScheduleConstant::getAllDay() as $val => $label)
              <option value="{{ $val }}">{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          <div class="form-group">
            <label class="form-label required">Jam Mulai</label>
            <input type="time" name="start_time" class="form-control" required>
          </div>
          <div class="form-group">
            <label class="form-label required">Jam Selesai</label>
            <input type="time" name="end_time" class="form-control" required>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label required">Tipe</label>
          <select name="type" class="form-control" id="schedType" required onchange="toggleSubject()">
            @foreach(\App\Constants\ScheduleConstant::getAllType() as $val => $label)
              <option value="{{ $val }}">{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div id="subjectFields">
          <div class="form-group">
            <label class="form-label">Mata Pelajaran</label>
            <select name="subject_id" class="form-control">
              <option value="">-- Pilih mapel --</option>
              @foreach($subjects as $sub)
                <option value="{{ $sub->id }}">{{ $sub->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Guru Pengampu</label>
            <select name="teacher_id" class="form-control">
              <option value="">-- Pilih guru --</option>
              @foreach($teachers as $t)
                <option value="{{ $t->id }}">{{ $t->full_name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalAddSchedule')">Batal</button>
        <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>

@endif

@endsection

@push('styles')
<style>
.schedule-grid { display:grid; grid-template-columns:repeat(6,1fr); gap:10px; align-items:start; }
.schedule-col { background:var(--color-surface,#fff); border:0.5px solid rgba(200,221,212,0.5); border-radius:var(--radius-lg,12px); overflow:hidden; }
.schedule-col-head { background:var(--grad-primary-dark); color:#fff; font-weight:600; font-size:12.5px; text-align:center; padding:8px; }
.schedule-col-body { padding:8px; display:flex; flex-direction:column; gap:8px; min-height:80px; }
.schedule-item { background:linear-gradient(135deg,rgba(216,243,220,0.5),rgba(183,228,199,0.2)); border-radius:8px; padding:8px; }
.schedule-item-alt { background:rgba(0,0,0,0.04); }
.schedule-del { border:none; background:none; color:var(--color-danger); cursor:pointer; padding:0; font-size:13px; line-height:1; }
@media(max-width:900px){ .schedule-grid{ grid-template-columns:repeat(2,1fr); } }
</style>
@endpush

@push('scripts')
<script>
function openModal(id){ document.getElementById(id).classList.add('show'); }
function closeModal(id){ document.getElementById(id).classList.remove('show'); }
function toggleSubject(){
  const t = document.getElementById('schedType').value;
  document.getElementById('subjectFields').style.display = (t === '1') ? 'block' : 'none';
}
document.querySelectorAll('.modal-backdrop').forEach(m => m.addEventListener('click', e => { if(e.target===m) m.classList.remove('show'); }));
@if($classroom && $activeYear)
toggleSubject();
@endif
</script>
@endpush
