@extends('layouts.app')

@section('title', 'Rapot — ' . $student->full_name)

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb">
      <i class="ti ti-home" style="font-size:11px;"></i> Beranda
      <span>/ <a href="{{ route('report-cards.index', ['classroom_id' => $reportCard->classroom_id]) }}" style="color:var(--color-primary);">Rapot</a> / {{ $student->full_name }}</span>
    </div>
    <div class="page-title">Rapot {{ $student->full_name }}</div>
  </div>
  <div class="page-actions" style="display:flex;gap:8px;">
    <a href="{{ route('report-cards.pdf', hid($reportCard)) }}" class="btn btn-outline"><i class="ti ti-file-type-pdf"></i> Unduh PDF</a>
    <form method="POST" action="{{ route('report-cards.toggle-publish', hid($reportCard)) }}">
      @csrf
      <button class="btn {{ $reportCard->is_published ? 'btn-outline' : 'btn-primary' }}">
        <i class="ti {{ $reportCard->is_published ? 'ti-eye-off' : 'ti-send' }}"></i>
        {{ $reportCard->is_published ? 'Tarik Publikasi' : 'Terbitkan' }}
      </button>
    </form>
  </div>
</div>

<div class="form-page-grid">
  <div style="display:flex;flex-direction:column;gap:14px;">
    <div class="card">
      <div class="card-header"><div class="card-title"><i class="ti ti-user"></i> Identitas</div></div>
      <div class="card-body" style="font-size:12.5px;line-height:1.9;">
        <div><span style="color:var(--color-text-secondary);">Nama</span><br><strong>{{ $student->full_name }}</strong></div>
        <div><span style="color:var(--color-text-secondary);">NISN / NIS</span><br><strong>{{ $student->nisn ?? '-' }} / {{ $student->nis ?? '-' }}</strong></div>
        <div><span style="color:var(--color-text-secondary);">Kelas</span><br><strong>{{ $reportCard->classroom->name }}</strong></div>
        <div><span style="color:var(--color-text-secondary);">Tahun Ajaran</span><br><strong>{{ $year->name }}</strong></div>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><div class="card-title"><i class="ti ti-chart-pie"></i> Ringkasan</div></div>
      <div class="card-body" style="font-size:12.5px;line-height:1.9;">
        <div><span style="color:var(--color-text-secondary);">Rata-rata</span> <strong style="float:right;">{{ $reportCard->gpa ?? '-' }}</strong></div>
        <div><span style="color:var(--color-text-secondary);">Peringkat</span> <strong style="float:right;">{{ $reportCard->rank_in_class ?? '-' }}</strong></div>
        <hr style="border:none;border-top:0.5px solid rgba(0,0,0,0.08);margin:8px 0;">
        <div>Hadir <strong style="float:right;">{{ $reportCard->total_hadir }}</strong></div>
        <div>Sakit <strong style="float:right;">{{ $reportCard->total_sakit }}</strong></div>
        <div>Izin <strong style="float:right;">{{ $reportCard->total_izin }}</strong></div>
        <div>Alpa <strong style="float:right;">{{ $reportCard->total_alpa }}</strong></div>
      </div>
    </div>
  </div>

  <div style="display:flex;flex-direction:column;gap:14px;">
    <div class="card">
      <div class="card-header"><div class="card-title"><i class="ti ti-list-numbers"></i> Nilai Mata Pelajaran</div></div>
      <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
          <table>
            <thead><tr><th style="width:42px;">#</th><th>Mata Pelajaran</th><th style="text-align:center;">Nilai</th><th style="text-align:center;">Grade</th><th>Predikat</th></tr></thead>
            <tbody>
              @forelse($finalScores as $i => $fs)
                <tr>
                  <td>{{ $i + 1 }}</td>
                  <td>{{ $fs->subject->name ?? '-' }}</td>
                  <td style="text-align:center;"><strong>{{ $fs->final_score }}</strong></td>
                  <td style="text-align:center;">{{ $fs->grade }}</td>
                  <td>{{ $fs->predicate }}</td>
                </tr>
              @empty
                <tr><td colspan="5" style="text-align:center;padding:30px;color:var(--color-text-secondary);">Belum ada nilai. Pastikan penilaian sudah diinput lalu generate ulang.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><div class="card-title"><i class="ti ti-message"></i> Catatan</div></div>
      <div class="card-body">
        <form method="POST" action="{{ route('report-cards.update-notes', hid($reportCard)) }}">
          @csrf
          <div class="form-group">
            <label class="form-label">Catatan Wali Kelas</label>
            <textarea name="homeroom_notes" class="form-control" rows="3">{{ $reportCard->homeroom_notes }}</textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Catatan Kepala Sekolah</label>
            <textarea name="principal_notes" class="form-control" rows="2">{{ $reportCard->principal_notes }}</textarea>
          </div>
          <button class="btn btn-primary"><i class="ti ti-device-floppy"></i> Simpan Catatan</button>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection
