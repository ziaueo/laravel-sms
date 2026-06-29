@extends('layouts.auth')

@section('title', 'Ganti Password')

@section('content')
<div class="auth-wrapper">
  <div class="auth-card">
    <div class="auth-logo">
      <div class="auth-logo-box"><i class="ti ti-key" style="font-size:24px;color:#fff;"></i></div>
      <div class="auth-title">Ganti Password</div>
      <div class="auth-subtitle">Demi keamanan, ganti password default Anda</div>
    </div>

    @if($errors->any())
      <div class="alert alert-error" style="margin-bottom:16px;"><i class="ti ti-alert-circle"></i><span>{{ $errors->first() }}</span></div>
    @endif

    <form method="POST" action="{{ route('auth.change-password.update') }}">
      @csrf @method('PUT')
      <div class="form-group">
        <label class="form-label required">Password Baru</label>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required autofocus>
      </div>
      <div class="form-group">
        <label class="form-label required">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;">
        <i class="ti ti-check"></i> Simpan Password
      </button>
    </form>

    <div class="auth-footer">
      <form method="POST" action="{{ route('auth.logout') }}">@csrf
        <button type="submit" style="background:none;border:none;color:#2d6a4f;font-weight:600;cursor:pointer;">Keluar</button>
      </form>
    </div>
  </div>
</div>
@endsection
