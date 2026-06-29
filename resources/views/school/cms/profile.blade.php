@extends('layouts.app')

@section('title', 'Profil Sekolah')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb"><i class="ti ti-home" style="font-size:11px;"></i> Beranda
      <span>/ <a href="{{ route('cms.index') }}" style="color:var(--color-primary);">Website</a> / Profil</span></div>
    <div class="page-title">Profil Sekolah</div>
  </div>
</div>

<form method="POST" action="{{ route('cms.profile.update') }}">
  @csrf @method('PUT')
  <div class="form-page-grid">
    <div style="display:flex;flex-direction:column;gap:14px;">
      <div class="card">
        <div class="card-header"><div class="card-title"><i class="ti ti-info-circle"></i> Umum</div></div>
        <div class="card-body">
          <div class="form-group"><label class="form-label">Tagline</label><input type="text" name="tagline" class="form-control" value="{{ old('tagline', $profile->tagline) }}"></div>
          <div class="form-group"><label class="form-label">Deskripsi Singkat</label><textarea name="description" class="form-control" rows="4">{{ old('description', $profile->description) }}</textarea></div>
          <div class="form-group"><label class="form-label">Tahun Berdiri</label><input type="number" name="founded_year" class="form-control" value="{{ old('founded_year', $profile->founded_year) }}"></div>
        </div>
      </div>
      <div class="card">
        <div class="card-header"><div class="card-title"><i class="ti ti-share"></i> Sosial Media & Peta</div></div>
        <div class="card-body">
          <div class="form-group"><label class="form-label">Facebook URL</label><input type="text" name="facebook_url" class="form-control" value="{{ old('facebook_url', $profile->facebook_url) }}"></div>
          <div class="form-group"><label class="form-label">Instagram URL</label><input type="text" name="instagram_url" class="form-control" value="{{ old('instagram_url', $profile->instagram_url) }}"></div>
          <div class="form-group"><label class="form-label">YouTube URL</label><input type="text" name="youtube_url" class="form-control" value="{{ old('youtube_url', $profile->youtube_url) }}"></div>
          <div class="form-group"><label class="form-label">Embed Google Maps (iframe)</label><textarea name="maps_embed" class="form-control" rows="3">{{ old('maps_embed', $profile->maps_embed) }}</textarea></div>
        </div>
      </div>
    </div>
    <div style="display:flex;flex-direction:column;gap:14px;">
      <div class="card">
        <div class="card-header"><div class="card-title"><i class="ti ti-eye"></i> Visi, Misi & Sejarah</div></div>
        <div class="card-body">
          <div class="form-group"><label class="form-label">Visi</label><textarea name="vision" class="form-control" rows="3">{{ old('vision', $profile->vision) }}</textarea></div>
          <div class="form-group"><label class="form-label">Misi</label><textarea name="mission" class="form-control" rows="5">{{ old('mission', $profile->mission) }}</textarea></div>
          <div class="form-group"><label class="form-label">Sejarah</label><textarea name="history" class="form-control" rows="5">{{ old('history', $profile->history) }}</textarea></div>
        </div>
      </div>
      <div><button class="btn btn-primary"><i class="ti ti-device-floppy"></i> Simpan Profil</button></div>
    </div>
  </div>
</form>

@endsection
