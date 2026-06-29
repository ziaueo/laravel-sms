@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb"><i class="ti ti-home" style="font-size:11px;"></i> Beranda <span>/ Pengaturan</span></div>
    <div class="page-title">Pengaturan Akun</div>
  </div>
</div>

<div class="form-page-grid">
  <div class="card">
    <div class="card-header"><div class="card-title"><i class="ti ti-user"></i> Profil</div></div>
    <div class="card-body">
      <form method="POST" action="{{ route('settings.profile') }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="form-group"><label class="form-label required">Nama</label><input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required></div>
        <div class="form-group"><label class="form-label required">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required></div>
        <div class="form-group"><label class="form-label">No. Telepon</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}"></div>
        <div class="form-group"><label class="form-label">Avatar</label><input type="file" name="avatar" class="form-control"></div>
        <button class="btn btn-primary"><i class="ti ti-device-floppy"></i> Simpan Profil</button>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><div class="card-title"><i class="ti ti-lock"></i> Ubah Password</div></div>
    <div class="card-body">
      <form method="POST" action="{{ route('settings.password') }}">
        @csrf @method('PUT')
        <div class="form-group"><label class="form-label required">Password Saat Ini</label><input type="password" name="current_password" class="form-control" required></div>
        <div class="form-group"><label class="form-label required">Password Baru</label><input type="password" name="password" class="form-control" required><div class="form-hint">Minimal 8 karakter</div></div>
        <div class="form-group"><label class="form-label required">Konfirmasi Password Baru</label><input type="password" name="password_confirmation" class="form-control" required></div>
        <button class="btn btn-primary"><i class="ti ti-key"></i> Ubah Password</button>
      </form>
    </div>
  </div>
</div>

@endsection
