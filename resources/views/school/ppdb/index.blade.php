@extends('layouts.app')

@section('title', 'PPDB')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb"><i class="ti ti-home" style="font-size:11px;"></i> Beranda <span>/ PPDB</span></div>
    <div class="page-title">PPDB — Penerimaan Siswa Baru</div>
    <div class="page-subtitle">{{ $school->name }}</div>
  </div>
  <div class="page-actions">
    <button class="btn btn-primary" onclick="openModal('modalPeriod')"><i class="ti ti-plus"></i> Gelombang Baru</button>
  </div>
</div>

{{-- GELOMBANG --}}
<div class="card">
  <div class="card-body" style="padding:14px 16px;">
    <form method="GET" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
      <label class="form-label" style="margin:0;">Gelombang:</label>
      <div style="min-width:240px;">
        <select name="period_id" class="form-control" onchange="this.form.submit()">
          @forelse($periods as $p)
            <option value="{{ $p->id }}" {{ $period && $period->id == $p->id ? 'selected' : '' }}>
              {{ $p->name }} ({{ $p->registrations_count }} pendaftar)
            </option>
          @empty
            <option value="">Belum ada gelombang</option>
          @endforelse
        </select>
      </div>
      @if($period)
        <form method="POST" action="{{ route('ppdb.periods.toggle', $period->id) }}" style="display:inline;">
          @csrf
          <button class="btn btn-sm btn-outline">{{ $period->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
        </form>
        <span class="badge {{ $period->is_open ? 'badge-green' : 'badge-amber' }}">{{ $period->is_open ? 'Dibuka' : 'Ditutup' }}</span>
      @endif
    </form>
  </div>
</div>

@if(!$period)
  <div class="card"><div class="card-body" style="text-align:center;padding:40px;color:var(--color-text-secondary);">
    <i class="ti ti-user-plus" style="font-size:32px;display:block;margin-bottom:10px;"></i>
    Belum ada gelombang PPDB. Buat gelombang dulu.
  </div></div>
@else

{{-- STATISTIK --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:14px;">
  <div class="card"><div class="card-body" style="text-align:center;"><div style="font-size:22px;font-weight:700;">{{ $counts['total'] }}</div><div style="font-size:11.5px;color:var(--color-text-secondary);">Total Pendaftar</div></div></div>
  <div class="card"><div class="card-body" style="text-align:center;"><div style="font-size:22px;font-weight:700;color:var(--color-warning);">{{ $counts['pending'] }}</div><div style="font-size:11.5px;color:var(--color-text-secondary);">Menunggu</div></div></div>
  <div class="card"><div class="card-body" style="text-align:center;"><div style="font-size:22px;font-weight:700;color:var(--color-success);">{{ $counts['diterima'] }}</div><div style="font-size:11.5px;color:var(--color-text-secondary);">Diterima</div></div></div>
  <div class="card"><div class="card-body" style="text-align:center;"><div style="font-size:22px;font-weight:700;color:var(--color-danger);">{{ $counts['ditolak'] }}</div><div style="font-size:11.5px;color:var(--color-text-secondary);">Ditolak</div></div></div>
</div>

{{-- FILTER + TABEL --}}
<div class="card">
  <div class="card-body" style="padding:14px 16px;">
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;">
      <input type="hidden" name="period_id" value="{{ $period->id }}">
      <input type="text" name="search" value="{{ request('search') }}" class="form-control" style="flex:1;min-width:180px;" placeholder="Cari nama pendaftar...">
      <select name="status" class="form-control" style="min-width:150px;" onchange="this.form.submit()">
        <option value="">Semua Status</option>
        @foreach(\App\Constants\PpdbConstant::getAll() as $val => $label)
          <option value="{{ $val }}" {{ (string)request('status') === (string)$val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
      </select>
      <button class="btn btn-outline btn-sm"><i class="ti ti-search"></i> Cari</button>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body" style="padding:0;">
    <div class="table-wrapper">
      <table>
        <thead><tr><th>No. Daftar</th><th>Nama</th><th>JK</th><th>Asal Sekolah</th><th>Orang Tua</th><th style="text-align:center;">Status</th><th style="text-align:right;">Aksi</th></tr></thead>
        <tbody>
          @forelse($registrations as $reg)
            <tr>
              <td style="font-size:11.5px;">{{ $reg->registration_number }}</td>
              <td><div style="font-weight:600;">{{ $reg->full_name }}</div></td>
              <td>{{ gender_label($reg->gender) }}</td>
              <td style="font-size:12px;">{{ $reg->previous_school ?? '-' }}</td>
              <td style="font-size:12px;">{{ $reg->parent_name }}<br><span style="color:var(--color-text-secondary);">{{ $reg->parent_phone }}</span></td>
              <td style="text-align:center;">{!! $reg->status_badge !!}</td>
              <td style="text-align:right;">
                <a href="{{ route('ppdb.show', $reg->id) }}" class="btn btn-sm btn-outline"><i class="ti ti-eye" style="font-size:13px;"></i> Review</a>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--color-text-secondary);">Belum ada pendaftar.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
<x-pagination :paginator="$registrations" />
@endif

{{-- MODAL GELOMBANG --}}
<div class="modal-backdrop" id="modalPeriod">
  <div class="modal-box" style="max-width:460px;">
    <form method="POST" action="{{ route('ppdb.periods.store') }}">
      @csrf
      <div class="modal-header">
        <div class="modal-title"><i class="ti ti-calendar-plus"></i> Gelombang PPDB Baru</div>
        <button type="button" class="modal-close" onclick="closeModal('modalPeriod')"><i class="ti ti-x"></i></button>
      </div>
      <div class="modal-body">
        @if(!$activeYear)
          <div class="alert alert-warning" style="margin-bottom:12px;"><i class="ti ti-alert-triangle"></i> Belum ada tahun ajaran aktif.</div>
        @endif
        <div class="form-group">
          <label class="form-label required">Nama Gelombang</label>
          <input type="text" name="name" class="form-control" placeholder="Gelombang 1 TA 2025/2026" required>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          <div class="form-group"><label class="form-label required">Buka</label><input type="date" name="open_date" class="form-control" required></div>
          <div class="form-group"><label class="form-label required">Tutup</label><input type="date" name="close_date" class="form-control" required></div>
        </div>
        <div class="form-group"><label class="form-label">Kuota</label><input type="number" name="quota" class="form-control" min="1"></div>
        <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="2"></textarea></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalPeriod')">Batal</button>
        <button type="submit" class="btn btn-primary" {{ !$activeYear ? 'disabled' : '' }}><i class="ti ti-check"></i> Simpan</button>
      </div>
    </form>
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
