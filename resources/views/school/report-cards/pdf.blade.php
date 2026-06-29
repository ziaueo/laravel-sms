<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
  * { font-family: DejaVu Sans, sans-serif; }
  body { font-size: 11px; color: #222; margin: 0; }
  .header { text-align: center; border-bottom: 2px solid #1a7a3c; padding-bottom: 8px; margin-bottom: 14px; }
  .header h1 { font-size: 15px; margin: 0; text-transform: uppercase; }
  .header p { margin: 2px 0; font-size: 10px; }
  .ident { width: 100%; margin-bottom: 12px; }
  .ident td { padding: 2px 4px; font-size: 11px; vertical-align: top; }
  table.grades { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
  table.grades th, table.grades td { border: 0.5px solid #888; padding: 5px 6px; }
  table.grades th { background: #e8f5ec; text-align: left; font-size: 10.5px; }
  table.grades td.c { text-align: center; }
  .summary { width: 100%; margin-bottom: 14px; }
  .summary td { padding: 3px 6px; font-size: 11px; }
  .notes { border: 0.5px solid #888; padding: 8px; margin-bottom: 10px; min-height: 36px; }
  .notes-label { font-weight: bold; font-size: 10px; color: #555; }
  .sign { width: 100%; margin-top: 24px; }
  .sign td { text-align: center; font-size: 11px; vertical-align: top; width: 33%; }
  .sign .space { height: 50px; }
</style>
</head>
<body>

  <div class="header">
    <h1>Laporan Hasil Belajar Siswa</h1>
    <p><strong>{{ $school->name }}</strong></p>
    <p>{{ $school->address ?? '' }}</p>
  </div>

  <table class="ident">
    <tr>
      <td style="width:15%;">Nama</td><td style="width:35%;">: <strong>{{ $student->full_name }}</strong></td>
      <td style="width:15%;">Kelas</td><td style="width:35%;">: {{ $reportCard->classroom->name }}</td>
    </tr>
    <tr>
      <td>NISN / NIS</td><td>: {{ $student->nisn ?? '-' }} / {{ $student->nis ?? '-' }}</td>
      <td>Tahun Ajaran</td><td>: {{ $year->name }}</td>
    </tr>
  </table>

  <table class="grades">
    <thead>
      <tr><th style="width:6%;" class="c">No</th><th>Mata Pelajaran</th><th style="width:14%;" class="c">Nilai</th><th style="width:12%;" class="c">Grade</th><th style="width:24%;">Predikat</th></tr>
    </thead>
    <tbody>
      @forelse($finalScores as $i => $fs)
        <tr>
          <td class="c">{{ $i + 1 }}</td>
          <td>{{ $fs->subject->name ?? '-' }}</td>
          <td class="c">{{ $fs->final_score }}</td>
          <td class="c">{{ $fs->grade }}</td>
          <td>{{ $fs->predicate }}</td>
        </tr>
      @empty
        <tr><td colspan="5" class="c">Belum ada nilai.</td></tr>
      @endforelse
    </tbody>
  </table>

  <table class="summary">
    <tr>
      <td style="width:50%;">
        <strong>Rata-rata:</strong> {{ $reportCard->gpa ?? '-' }} &nbsp;&nbsp;
        <strong>Peringkat:</strong> {{ $reportCard->rank_in_class ?? '-' }}
      </td>
      <td style="width:50%;">
        <strong>Kehadiran:</strong>
        Hadir {{ $reportCard->total_hadir }},
        Sakit {{ $reportCard->total_sakit }},
        Izin {{ $reportCard->total_izin }},
        Alpa {{ $reportCard->total_alpa }}
      </td>
    </tr>
  </table>

  <div class="notes">
    <span class="notes-label">Catatan Wali Kelas:</span><br>
    {{ $reportCard->homeroom_notes ?? '-' }}
  </div>
  <div class="notes">
    <span class="notes-label">Catatan Kepala Sekolah:</span><br>
    {{ $reportCard->principal_notes ?? '-' }}
  </div>

  <table class="sign">
    <tr>
      <td>Orang Tua/Wali<div class="space"></div>(................................)</td>
      <td>Wali Kelas<div class="space"></div>({{ $reportCard->classroom->homeroomTeacher->full_name ?? '................................' }})</td>
      <td>Kepala Sekolah<div class="space"></div>(................................)</td>
    </tr>
  </table>

</body>
</html>
