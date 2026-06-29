@extends('layouts.app')

@section('title', 'Buat Pengumuman')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb"><i class="ti ti-home" style="font-size:11px;"></i> Beranda
      <span>/ <a href="{{ route('announcements.index') }}" style="color:var(--color-primary);">Pengumuman</a> / Buat</span></div>
    <div class="page-title">Buat Pengumuman</div>
  </div>
</div>

@include('school.announcements._form', ['action' => route('announcements.store'), 'announcement' => null, 'method' => 'POST'])

@endsection
