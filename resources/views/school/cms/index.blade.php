@extends('layouts.app')

@section('title', 'Website / CMS')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb"><i class="ti ti-home" style="font-size:11px;"></i> Beranda <span>/ Website</span></div>
    <div class="page-title">Manajemen Website</div>
    <div class="page-subtitle">{{ $school->name }}</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('public.home', $school->slug) }}" target="_blank" class="btn btn-outline"><i class="ti ti-external-link"></i> Lihat Website</a>
  </div>
</div>

<div class="form-page-grid" style="grid-template-columns:repeat(2,1fr);">
  <a href="{{ route('cms.profile') }}" class="card" style="text-decoration:none;color:inherit;">
    <div class="card-body" style="display:flex;align-items:center;gap:14px;">
      <div class="icon-green" style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px;"><i class="ti ti-building"></i></div>
      <div><div style="font-weight:700;">Profil Sekolah</div><div style="font-size:12px;color:var(--color-text-secondary);">Visi, misi, sejarah, sosial media</div></div>
    </div>
  </a>
  <a href="{{ route('cms.posts') }}" class="card" style="text-decoration:none;color:inherit;">
    <div class="card-body" style="display:flex;align-items:center;gap:14px;">
      <div class="icon-blue" style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px;"><i class="ti ti-news"></i></div>
      <div><div style="font-weight:700;">Berita ({{ $stats['posts'] }})</div><div style="font-size:12px;color:var(--color-text-secondary);">Artikel & kegiatan</div></div>
    </div>
  </a>
  <a href="{{ route('cms.banners') }}" class="card" style="text-decoration:none;color:inherit;">
    <div class="card-body" style="display:flex;align-items:center;gap:14px;">
      <div class="icon-amber" style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px;"><i class="ti ti-photo"></i></div>
      <div><div style="font-weight:700;">Banner ({{ $stats['banners'] }})</div><div style="font-size:12px;color:var(--color-text-secondary);">Slider halaman depan</div></div>
    </div>
  </a>
  <a href="{{ route('cms.galleries') }}" class="card" style="text-decoration:none;color:inherit;">
    <div class="card-body" style="display:flex;align-items:center;gap:14px;">
      <div class="icon-green" style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:24px;"><i class="ti ti-photo-square-rounded"></i></div>
      <div><div style="font-weight:700;">Galeri ({{ $stats['galleries'] }})</div><div style="font-size:12px;color:var(--color-text-secondary);">Album foto kegiatan</div></div>
    </div>
  </a>
</div>

@endsection
