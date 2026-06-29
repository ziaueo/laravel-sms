@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="auth-wrapper">
  <div class="auth-card">
    <div class="auth-logo">
      <div class="auth-logo-box">
        <i class="ti ti-school" style="font-size:24px;color:#fff;"></i>
      </div>
      <div class="auth-title">{{ config('app.name') }}</div>
      <div class="auth-subtitle">Masuk ke akun kamu untuk melanjutkan</div>
    </div>

    @if(session('success'))
      <div class="alert alert-success" style="margin-bottom:16px;">
        <i class="ti ti-circle-check" style="font-size:18px;"></i>
        <span>{{ session('success') }}</span>
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-error" style="margin-bottom:16px;">
        <i class="ti ti-alert-circle" style="font-size:18px;"></i>
        <span>{{ session('error') }}</span>
      </div>
    @endif
    @if($errors->any())
      <div class="alert alert-error" style="margin-bottom:16px;">
        <i class="ti ti-alert-circle" style="font-size:18px;"></i>
        <span>{{ $errors->first() }}</span>
      </div>
    @endif

    <form method="POST" action="{{ route('auth.login') }}">
      @csrf

      <div class="form-group">
        <label class="form-label required">Email</label>
        <input type="email" name="email" value="{{ old('email') }}"
               class="form-control" placeholder="nama@email.com" autofocus required>
      </div>

      <div class="form-group">
        <label class="form-label required">Password</label>
        <input type="password" name="password"
               class="form-control" placeholder="••••••••" required>
      </div>

      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
        <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:#6c8f7a;cursor:pointer;">
          <input type="checkbox" name="remember" style="accent-color:#2d6a4f;">
          Ingat saya
        </label>
        <a href="{{ route('password.request') }}" style="font-size:12px;color:#2d6a4f;font-weight:600;">Lupa password?</a>
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;">
        <i class="ti ti-login"></i> Masuk
      </button>
    </form>

    <div class="auth-footer">
      Belum punya akun?
      <a href="{{ route('auth.register') }}">Daftar sebagai Siswa/Orang Tua</a>
    </div>
  </div>
</div>
@endsection
