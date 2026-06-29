@extends('layouts.app')

@section('title', $post ? 'Edit Berita' : 'Tulis Berita')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb"><i class="ti ti-home" style="font-size:11px;"></i> Beranda
      <span>/ <a href="{{ route('cms.posts') }}" style="color:var(--color-primary);">Berita</a> / {{ $post ? 'Edit' : 'Tulis' }}</span></div>
    <div class="page-title">{{ $post ? 'Edit Berita' : 'Tulis Berita Baru' }}</div>
  </div>
</div>

<form method="POST" action="{{ $post ? route('cms.posts.update', $post->id) : route('cms.posts.store') }}" enctype="multipart/form-data">
  @csrf
  @if($post) @method('PUT') @endif
  <div class="form-page-grid">
    <div style="display:flex;flex-direction:column;gap:14px;">
      <div class="card">
        <div class="card-body">
          @if($errors->any())<div class="alert alert-error" style="margin-bottom:16px;"><i class="ti ti-alert-circle"></i><div>@foreach($errors->all() as $e)<div style="font-size:12px;">{{ $e }}</div>@endforeach</div></div>@endif
          <div class="form-group"><label class="form-label required">Judul</label><input type="text" name="title" class="form-control" value="{{ old('title', $post->title ?? '') }}" required></div>
          <div class="form-group"><label class="form-label">Ringkasan</label><textarea name="excerpt" class="form-control" rows="2">{{ old('excerpt', $post->excerpt ?? '') }}</textarea></div>
          <div class="form-group"><label class="form-label">Konten</label><textarea name="content" class="form-control" rows="14">{{ old('content', $post->content ?? '') }}</textarea></div>
        </div>
      </div>
    </div>
    <div style="display:flex;flex-direction:column;gap:14px;">
      <div class="card">
        <div class="card-header"><div class="card-title"><i class="ti ti-settings"></i> Pengaturan</div></div>
        <div class="card-body">
          <div class="form-group"><label class="form-label">Kategori</label>
            <select name="category_id" class="form-control"><option value="">-- Tanpa kategori --</option>
              @foreach($categories as $cat)<option value="{{ $cat->id }}" {{ old('category_id', $post->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>@endforeach
            </select></div>
          <div class="form-group"><label class="form-label">Thumbnail</label><input type="file" name="thumbnail" class="form-control">
            @if($post && $post->thumbnail)<img src="{{ asset($post->thumbnail) }}" style="margin-top:8px;width:100%;border-radius:8px;">@endif</div>
          <label style="display:flex;align-items:center;gap:8px;font-size:12.5px;margin-bottom:8px;cursor:pointer;">
            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $post->is_published ?? false) ? 'checked' : '' }}> Terbitkan</label>
          <label style="display:flex;align-items:center;gap:8px;font-size:12.5px;cursor:pointer;">
            <input type="checkbox" name="show_in_feed" value="1" {{ old('show_in_feed', $post->show_in_feed ?? false) ? 'checked' : '' }}> Tampilkan di feed</label>
        </div>
      </div>
      <div style="display:flex;gap:10px;">
        <a href="{{ route('cms.posts') }}" class="btn btn-outline"><i class="ti ti-arrow-left"></i> Batal</a>
        <button class="btn btn-primary"><i class="ti ti-check"></i> Simpan</button>
      </div>
    </div>
  </div>
</form>

@endsection
