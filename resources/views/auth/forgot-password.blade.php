@extends('layouts.auth')

@section('title', 'Lupa Password')

@section('content')
<div class="auth-wrapper">
  <div class="auth-card">
    <div class="auth-logo">
      <div class="auth-logo-box"><i class="ti ti-lock-question" style="font-size:24px;color:#fff;"></i></div>
      <div class="auth-title">Lupa Password</div>
      <div class="auth-subtitle">Masukkan email untuk menerima tautan reset</div>
    </div>

    @if(session('success'))
      <div class="alert alert-success" style="margin-bottom:16px;"><i class="ti ti-circle-check"></i><span>{{ session('success') }}</span></div>
    @endif
    @if(session('error'))
      <div class="alert alert-error" style="margin-bottom:16px;"><i class="ti ti-alert-circle"></i><span>{{ session('error') }}</span></div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <div class="form-group">
        <label class="form-label required">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="nama@email.com" required autofocus>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;">
        <i class="ti ti-send"></i> Kirim Tautan Reset
      </button>
    </form>

    <div class="auth-footer"><a href="{{ route('auth.login') }}">Kembali ke Login</a></div>
  </div>
</div>
@endsection
