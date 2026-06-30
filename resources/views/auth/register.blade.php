@extends('layouts.auth')

@section('title', 'Daftar')

@section('content')
<div class="auth-wrapper">
  <div class="auth-card" style="max-width:440px;">
    <div class="auth-logo">
      <img src="{{ asset('images/logo_sms.png') }}" alt="{{ config('app.name') }}"
           style="height:64px;width:auto;max-width:160px;object-fit:contain;margin:0 auto 12px;display:block;">
      <div class="auth-title">Daftar Akun</div>
      <div class="auth-subtitle">Untuk Siswa & Orang Tua</div>
    </div>

    @if(session('error'))
      <div class="alert alert-error" style="margin-bottom:16px;"><i class="ti ti-alert-circle"></i><span>{{ session('error') }}</span></div>
    @endif
    @if($errors->any())
      <div class="alert alert-error" style="margin-bottom:16px;"><i class="ti ti-alert-circle"></i><div>@foreach($errors->all() as $e)<div style="font-size:12px;">{{ $e }}</div>@endforeach</div></div>
    @endif

    <form method="POST" action="{{ route('auth.register') }}">
      @csrf
      <div class="form-group">
        <label class="form-label required">Daftar Sebagai</label>
        <select name="role" class="form-control" required>
          <option value="{{ \App\Constants\RoleConstant::SISWA }}" {{ old('role')==\App\Constants\RoleConstant::SISWA?'selected':'' }}>Siswa</option>
          <option value="{{ \App\Constants\RoleConstant::ORANG_TUA }}" {{ old('role')==\App\Constants\RoleConstant::ORANG_TUA?'selected':'' }}>Orang Tua</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label required">Sekolah</label>
        <select name="school_id" class="form-control" required>
          <option value="">-- Pilih sekolah --</option>
          @foreach($schools as $s)<option value="{{ $s->id }}" {{ old('school_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>@endforeach
        </select>
      </div>
      <div class="form-group">
        <label class="form-label required">NIS / NISN Siswa</label>
        <input type="text" name="identifier" value="{{ old('identifier') }}" class="form-control" placeholder="NIS atau NISN" required>
        <div class="form-hint">Orang tua: isi NIS/NISN anak.</div>
      </div>
      <div class="form-group">
        <label class="form-label required">Nama Lengkap</label>
        <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
      </div>
      <div class="form-group">
        <label class="form-label required">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
      </div>
      <div class="form-group">
        <label class="form-label required">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="form-group">
        <label class="form-label required">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;">
        <i class="ti ti-user-plus"></i> Daftar
      </button>
    </form>

    <div class="auth-footer">Sudah punya akun? <a href="{{ route('auth.login') }}">Masuk</a></div>
  </div>
</div>
@endsection
