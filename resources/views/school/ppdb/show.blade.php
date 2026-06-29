@extends('layouts.app')

@section('title', 'Review Pendaftar')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb"><i class="ti ti-home" style="font-size:11px;"></i> Beranda
      <span>/ <a href="{{ route('ppdb.index', ['period_id' => $registration->ppdb_period_id]) }}" style="color:var(--color-primary);">PPDB</a> / Review</span></div>
    <div class="page-title">{{ $registration->full_name }}</div>
    <div class="page-subtitle">No. Daftar: {{ $registration->registration_number }} · {{ $registration->ppdbPeriod->name ?? '' }}</div>
  </div>
</div>

<div class="form-page-grid">
  <div style="display:flex;flex-direction:column;gap:14px;">
    <div class="card">
      <div class="card-header"><div class="card-title"><i class="ti ti-user"></i> Data Calon Siswa</div></div>
      <div class="card-body" style="font-size:12.5px;line-height:1.9;">
        <div><span style="color:var(--color-text-secondary);">Nama</span><br><strong>{{ $registration->full_name }}</strong></div>
        <div><span style="color:var(--color-text-secondary);">Jenis Kelamin</span><br><strong>{{ gender_label($registration->gender) }}</strong></div>
        <div><span style="color:var(--color-text-secondary);">Tempat, Tgl Lahir</span><br><strong>{{ $registration->birth_place ?? '-' }}, {{ format_date($registration->birth_date) }}</strong></div>
        <div><span style="color:var(--color-text-secondary);">Agama</span><br><strong>{{ $registration->religion ?? '-' }}</strong></div>
        <div><span style="color:var(--color-text-secondary);">Asal Sekolah</span><br><strong>{{ $registration->previous_school ?? '-' }}</strong></div>
        <div><span style="color:var(--color-text-secondary);">Alamat</span><br><strong>{{ $registration->address ?? '-' }}</strong></div>
      </div>
    </div>
    <div class="card">
      <div class="card-header"><div class="card-title"><i class="ti ti-users"></i> Data Orang Tua/Wali</div></div>
      <div class="card-body" style="font-size:12.5px;line-height:1.9;">
        <div><span style="color:var(--color-text-secondary);">Nama</span><br><strong>{{ $registration->parent_name }}</strong> ({{ parent_relation_label($registration->parent_relation) }})</div>
        <div><span style="color:var(--color-text-secondary);">Telepon</span><br><strong>{{ $registration->parent_phone }}</strong></div>
        <div><span style="color:var(--color-text-secondary);">Email</span><br><strong>{{ $registration->parent_email ?? '-' }}</strong></div>
        <div><span style="color:var(--color-text-secondary);">Pekerjaan</span><br><strong>{{ $registration->parent_job ?? '-' }}</strong></div>
      </div>
    </div>
  </div>

  <div style="display:flex;flex-direction:column;gap:14px;">
    <div class="card">
      <div class="card-header"><div class="card-title"><i class="ti ti-checklist"></i> Status Pendaftaran</div></div>
      <div class="card-body">
        <div style="margin-bottom:12px;">Status saat ini: {!! $registration->status_badge !!}
          @if($registration->reviewed_at)<span style="font-size:11px;color:var(--color-text-muted);"> · direview {{ time_ago($registration->reviewed_at) }} oleh {{ $registration->reviewedBy->name ?? '-' }}</span>@endif
        </div>
        <form method="POST" action="{{ route('ppdb.update-status', $registration->id) }}">
          @csrf @method('PUT')
          <div class="form-group">
            <label class="form-label required">Ubah Status</label>
            <select name="status" class="form-control" required>
              @foreach(\App\Constants\PpdbConstant::getAll() as $val => $label)
                <option value="{{ $val }}" {{ $registration->status == $val ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Catatan</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Alasan / keterangan">{{ $registration->notes }}</textarea>
          </div>
          <div class="alert alert-warning" style="font-size:11.5px;margin-bottom:12px;">
            <i class="ti ti-info-circle"></i> Jika status <strong>Diterima</strong>, data siswa & orang tua akan otomatis dibuat di Kesiswaan.
          </div>
          <button class="btn btn-primary" style="width:100%;justify-content:center;"><i class="ti ti-check"></i> Simpan Status</button>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection
