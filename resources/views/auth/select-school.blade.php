@extends('layouts.auth')

@section('title', 'Pilih Sekolah')

@section('content')
<div class="auth-wrapper">
  <div class="auth-card" style="max-width:480px;">
    <div class="auth-logo">
      <img src="{{ asset('images/logo_sms.png') }}" alt="{{ config('app.name') }}"
           style="height:64px;width:auto;max-width:160px;object-fit:contain;margin:0 auto 12px;display:block;">
      <div class="auth-title">Pilih Sekolah</div>
      <div class="auth-subtitle">Kamu memiliki akses ke lebih dari satu sekolah</div>
    </div>

    <form method="POST" action="{{ route('select.school') }}">
      @csrf
      <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:16px;">
        @foreach($schools as $school)
          <label class="school-option">
            <input type="radio" name="school_id" value="{{ $school->id }}" required>
            <div class="school-option-icon">
              <i class="ti ti-building-school"></i>
            </div>
            <div>
              <div class="school-option-name">{{ $school->name }}</div>
              <div class="school-option-type">{{ $school->schoolType->name }}</div>
            </div>
          </label>
        @endforeach
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;">
        <i class="ti ti-check"></i> Lanjutkan
      </button>
    </form>
  </div>
</div>
@endsection
