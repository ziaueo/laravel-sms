@extends('layouts.app')

@section('title', $gallery->title)

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb"><i class="ti ti-home" style="font-size:11px;"></i> Beranda
      <span>/ <a href="{{ route('cms.galleries') }}" style="color:var(--color-primary);">Galeri</a> / {{ $gallery->title }}</span></div>
    <div class="page-title">{{ $gallery->title }}</div>
  </div>
</div>

<div class="card">
  <div class="card-header"><div class="card-title"><i class="ti ti-upload"></i> Tambah Foto</div></div>
  <div class="card-body">
    <form method="POST" action="{{ route('cms.galleries.items.store', hid($gallery)) }}" enctype="multipart/form-data" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
      @csrf
      <input type="file" name="images[]" class="form-control" accept="image/*" multiple required style="flex:1;min-width:220px;">
      <button class="btn btn-primary"><i class="ti ti-upload"></i> Upload</button>
    </form>
    <div class="form-hint" style="margin-top:6px;">Bisa pilih beberapa foto sekaligus. JPG/PNG/WebP, maks 3MB/foto.</div>
  </div>
</div>

<div class="form-page-grid" style="grid-template-columns:repeat(4,1fr);margin-top:14px;">
  @forelse($gallery->items as $item)
    <div class="card">
      <img src="{{ asset($item->file_path) }}" style="width:100%;height:130px;object-fit:cover;">
      <div class="card-body" style="text-align:center;padding:8px;">
        <form method="POST" action="{{ route('cms.galleries.items.destroy', hid($item)) }}" onsubmit="return confirm('Hapus foto?')">
          @csrf @method('DELETE')<button class="btn btn-sm btn-danger" style="width:100%;justify-content:center;"><i class="ti ti-trash" style="font-size:13px;"></i> Hapus</button>
        </form>
      </div>
    </div>
  @empty
    <div class="card" style="grid-column:1/-1;"><div class="card-body" style="text-align:center;padding:40px;color:var(--color-text-secondary);">Belum ada foto.</div></div>
  @endforelse
</div>

@endsection
