@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb">
      <i class="ti ti-home" style="font-size:11px;"></i> Beranda <span>/ Dashboard</span>
    </div>
    <div class="page-title">Selamat datang, {{ explode(' ', auth()->user()->name)[0] }} 👋</div>
    <div class="page-subtitle">{{ now()->translatedFormat('l, d F Y') }}@if(active_school()) — {{ active_school()->name }}@endif</div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <p>Dashboard akan diisi dengan statistik dan ringkasan data setelah modul-modul lain selesai dibuat.</p>
  </div>
</div>

@endsection
