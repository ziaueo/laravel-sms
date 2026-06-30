@extends('layouts.app')

@section('title', 'Penilaian')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb">
      <i class="ti ti-home" style="font-size:11px;"></i> Beranda <span>/ Penilaian</span>
    </div>
    <div class="page-title">Penilaian</div>
    <div class="page-subtitle">{{ $school->name }} @if($activeYear) — T.A. {{ $activeYear->year }} {{ $activeYear->semester_label }} @endif</div>
  </div>
  <div class="page-actions">
    <button class="btn btn-outline" onclick="openModal('modalAssessmentTypes')"><i class="ti ti-settings"></i> Jenis Penilaian</button>
  </div>
</div>

{{-- FILTER --}}
<div class="card">
  <div class="card-body" style="padding:14px 16px;">
    <form method="GET" action="{{ route('scores.index') }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
      <div style="min-width:190px;">
        <select name="classroom_id" class="form-control" onchange="this.form.submit()">
          @forelse($classrooms as $c)
            <option value="{{ $c->id }}" {{ $classroom && $classroom->id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
          @empty
            <option value="">Belum ada kelas</option>
          @endforelse
        </select>
      </div>
      <div style="min-width:190px;">
        <select name="subject_id" class="form-control" onchange="this.form.submit()">
          @forelse($subjects as $s)
            <option value="{{ $s->id }}" {{ $subject && $subject->id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
          @empty
            <option value="">Belum ada mapel</option>
          @endforelse
        </select>
      </div>
    </form>
  </div>
</div>

@if(!$activeYear)
  <div class="alert alert-warning"><i class="ti ti-alert-triangle"></i> Belum ada tahun ajaran aktif.</div>
@elseif(!$classroom || !$subject)
  <div class="card"><div class="card-body" style="text-align:center;padding:40px;color:var(--color-text-secondary);">Pilih kelas & mata pelajaran.</div></div>
@elseif($assessmentTypes->isEmpty())
  <div class="card"><div class="card-body" style="text-align:center;padding:40px;color:var(--color-text-secondary);">
    <i class="ti ti-clipboard-x" style="font-size:32px;display:block;margin-bottom:10px;"></i>
    Belum ada jenis penilaian. Tambahkan dulu (mis. Tugas, UH, UTS, UAS).
    <br><button class="btn btn-primary" style="margin-top:12px;" onclick="openModal('modalAssessmentTypes')"><i class="ti ti-plus"></i> Tambah Jenis Penilaian</button>
  </div></div>
@else

<form method="POST" action="{{ route('scores.store') }}">
  @csrf
  <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">
  <input type="hidden" name="subject_id" value="{{ $subject->id }}">

  <div class="card">
    <div class="card-header"><div class="card-title"><i class="ti ti-chart-bar"></i> {{ $classroom->name }} — {{ $subject->name }}</div></div>
    <div class="card-body" style="padding:0;">
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th style="width:42px;">#</th><th>Siswa</th>
              @foreach($assessmentTypes as $type)
                <th style="text-align:center;">{{ $type->name }}@if($type->weight > 0)<br><span style="font-weight:400;font-size:10px;">({{ rtrim(rtrim($type->weight,'0'),'.') }}%)</span>@endif</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @forelse($students as $i => $student)
              <tr>
                <td>{{ $i + 1 }}</td>
                <td><div style="font-weight:600;">{{ $student->full_name }}</div></td>
                @foreach($assessmentTypes as $type)
                  @php $val = $scores->get($student->id)?->get($type->id)?->score; @endphp
                  <td style="text-align:center;">
                    <input type="number" step="0.01" min="0" max="100"
                           name="scores[{{ $student->id }}][{{ $type->id }}]"
                           value="{{ $val !== null ? rtrim(rtrim($val,'0'),'.') : '' }}"
                           class="form-control" style="width:72px;text-align:center;margin:0 auto;padding:6px;">
                  </td>
                @endforeach
              </tr>
            @empty
              <tr><td colspan="{{ 2 + $assessmentTypes->count() }}" style="text-align:center;padding:40px;color:var(--color-text-secondary);">Belum ada siswa.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  @if($students->count())
  <div style="display:flex;justify-content:flex-end;margin-top:14px;">
    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy"></i> Simpan Nilai</button>
  </div>
  @endif
</form>
@endif

{{-- MODAL JENIS PENILAIAN --}}
<div class="modal-backdrop" id="modalAssessmentTypes">
  <div class="modal-box" style="max-width:460px;">
    <div class="modal-header">
      <div class="modal-title"><i class="ti ti-settings"></i> Jenis Penilaian</div>
      <button type="button" class="modal-close" onclick="closeModal('modalAssessmentTypes')"><i class="ti ti-x"></i></button>
    </div>
    <div class="modal-body">
      @if($assessmentTypes->count())
        <div class="table-wrapper" style="margin-bottom:14px;">
          <table>
            <thead><tr><th>Nama</th><th style="text-align:center;">Bobot</th><th></th></tr></thead>
            <tbody>
              @foreach($assessmentTypes as $type)
                <tr>
                  <td>{{ $type->name }}</td>
                  <td style="text-align:center;">{{ rtrim(rtrim($type->weight,'0'),'.') }}%</td>
                  <td style="text-align:right;">
                    <form method="POST" action="{{ route('scores.assessment-types.destroy', hid($type)) }}" onsubmit="return confirm('Hapus jenis penilaian ini? Nilai terkait ikut terhapus.')">
                      @csrf @method('DELETE')
                      <button class="btn btn-sm btn-danger btn-icon"><i class="ti ti-trash" style="font-size:12px;"></i></button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
      <form method="POST" action="{{ route('scores.assessment-types.store') }}">
        @csrf
        <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:8px;align-items:end;">
          <div class="form-group" style="margin:0;">
            <label class="form-label required">Nama</label>
            <input type="text" name="name" class="form-control" placeholder="Tugas / UH / UTS / UAS" required>
          </div>
          <div class="form-group" style="margin:0;">
            <label class="form-label">Bobot %</label>
            <input type="number" step="0.01" name="weight" class="form-control" placeholder="0">
          </div>
          <button type="submit" class="btn btn-primary"><i class="ti ti-plus"></i></button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
function openModal(id){ document.getElementById(id).classList.add('show'); }
function closeModal(id){ document.getElementById(id).classList.remove('show'); }
document.querySelectorAll('.modal-backdrop').forEach(m => m.addEventListener('click', e => { if(e.target===m) m.classList.remove('show'); }));
</script>
@endpush
