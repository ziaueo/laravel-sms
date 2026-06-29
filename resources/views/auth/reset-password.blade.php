@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="auth-wrapper">
  <div class="auth-card">
    <div class="auth-logo">
      <div class="auth-logo-box"><i class="ti ti-key" style="font-size:24px;color:#fff;"></i></div>
      <div class="auth-title">Reset Password</div>
      <div class="auth-subtitle">Buat password baru Anda</div>
    </div>

    @if($errors->any())
      <div class="alert alert-error" style="margin-bottom:16px;"><i class="ti ti-alert-circle"></i><div>@foreach($errors->all() as $e)<div style="font-size:12px;">{{ $e }}</div>@endforeach</div></div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      <div class="form-group">
        <label class="form-label required">Email</label>
        <input type="email" name="email" value="{{ old('email', $email) }}" class="form-control" required>
      </div>
      <div class="form-group">
        <label class="form-label required">Password Baru</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="form-group">
        <label class="form-label required">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;">
        <i class="ti ti-check"></i> Reset Password
      </button>
    </form>

    <div class="auth-footer"><a href="{{ route('auth.login') }}">Kembali ke Login</a></div>
  </div>
</div>
@endsection
