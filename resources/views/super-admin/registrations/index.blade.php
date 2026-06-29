@extends('layouts.app')

@section('title', 'Verifikasi Pendaftaran')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb"><i class="ti ti-home" style="font-size:11px;"></i> Beranda <span>/ Verifikasi Pendaftaran</span></div>
    <div class="page-title">Verifikasi Pendaftaran</div>
    <div class="page-subtitle">Persetujuan akun siswa & orang tua</div>
  </div>
</div>

<div class="card">
  <div class="card-body" style="padding:14px 16px;">
    <form method="GET" style="display:flex;gap:10px;align-items:center;">
      <label class="form-label" style="margin:0;">Status:</label>
      <select name="status" class="form-control" style="max-width:200px;" onchange="this.form.submit()">
        @foreach(\App\Constants\RegistrationConstant::getAll() as $val => $label)
          <option value="{{ $val }}" {{ (string)request('status', 1) === (string)$val ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
      </select>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body" style="padding:0;">
    <div class="table-wrapper">
      <table>
        <thead><tr><th>Nama</th><th>Email</th><th>Peran</th><th>Sekolah</th><th>NIS/NISN</th><th style="text-align:center;">Status</th><th style="text-align:right;">Aksi</th></tr></thead>
        <tbody>
          @forelse($registrations as $r)
            <tr>
              <td><div style="font-weight:600;">{{ $r->name }}</div></td>
              <td style="font-size:12px;">{{ $r->email }}</td>
              <td>{{ role_label($r->role) }}</td>
              <td style="font-size:12px;">{{ $r->school->name ?? '-' }}</td>
              <td style="font-size:12px;">{{ $r->data['identifier'] ?? '-' }}</td>
              <td style="text-align:center;">{!! registration_status_badge($r->status) !!}</td>
              <td style="text-align:right;">
                @if($r->status == \App\Constants\RegistrationConstant::PENDING)
                  <div style="display:inline-flex;gap:5px;">
                    <form method="POST" action="{{ route('registrations.approve', $r->id) }}" onsubmit="return confirm('Setujui & buat akun?')">@csrf
                      <button class="btn btn-sm btn-primary"><i class="ti ti-check" style="font-size:13px;"></i> Setujui</button>
                    </form>
                    <form method="POST" action="{{ route('registrations.reject', $r->id) }}" onsubmit="return confirm('Tolak pendaftaran ini?')">@csrf
                      <button class="btn btn-sm btn-danger btn-icon"><i class="ti ti-x" style="font-size:13px;"></i></button>
                    </form>
                  </div>
                @else
                  <span style="font-size:11px;color:var(--color-text-muted);">{{ $r->reviewed_at ? time_ago($r->reviewed_at) : '' }}</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--color-text-secondary);">Tidak ada pendaftaran.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
<x-pagination :paginator="$registrations" />

@endsection
