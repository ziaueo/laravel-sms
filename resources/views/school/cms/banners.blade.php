@extends('layouts.app')

@section('title', 'Banner')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb"><i class="ti ti-home" style="font-size:11px;"></i> Beranda
      <span>/ <a href="{{ route('cms.index') }}" style="color:var(--color-primary);">Website</a> / Banner</span></div>
    <div class="page-title">Banner Slider</div>
  </div>
  <div class="page-actions"><button class="btn btn-primary" onclick="openModal('modalBanner')"><i class="ti ti-plus"></i> Tambah Banner</button></div>
</div>

<div class="form-page-grid" style="grid-template-columns:repeat(2,1fr);">
  @forelse($banners as $b)
    <div class="card">
      <img src="{{ asset($b->image) }}" style="width:100%;height:160px;object-fit:cover;">
      <div class="card-body" style="display:flex;justify-content:space-between;align-items:center;">
        <div><div style="font-weight:600;">{{ $b->title ?? 'Tanpa judul' }}</div><div style="font-size:12px;color:var(--color-text-secondary);">{{ $b->subtitle }}</div></div>
        <form method="POST" action="{{ route('cms.banners.destroy', hid($b)) }}" onsubmit="return confirm('Hapus banner?')">
          @csrf @method('DELETE')<button class="btn btn-sm btn-danger btn-icon"><i class="ti ti-trash" style="font-size:13px;"></i></button>
        </form>
      </div>
    </div>
  @empty
    <div class="card" style="grid-column:1/-1;"><div class="card-body" style="text-align:center;padding:40px;color:var(--color-text-secondary);">Belum ada banner.</div></div>
  @endforelse
</div>

<div class="modal-backdrop" id="modalBanner">
  <div class="modal-box" style="max-width:440px;">
    <form method="POST" action="{{ route('cms.banners.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="modal-header"><div class="modal-title"><i class="ti ti-photo"></i> Tambah Banner</div>
        <button type="button" class="modal-close" onclick="closeModal('modalBanner')"><i class="ti ti-x"></i></button></div>
      <div class="modal-body">
        <div class="form-group"><label class="form-label required">Gambar</label><input type="file" name="image" class="form-control" required><div class="form-hint">JPG/PNG/WebP, maks 3MB. Disarankan 1600×600.</div></div>
        <div class="form-group"><label class="form-label">Judul</label><input type="text" name="title" class="form-control"></div>
        <div class="form-group"><label class="form-label">Subjudul</label><input type="text" name="subtitle" class="form-control"></div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          <div class="form-group"><label class="form-label">Teks Tombol</label><input type="text" name="button_text" class="form-control"></div>
          <div class="form-group"><label class="form-label">URL Tombol</label><input type="text" name="button_url" class="form-control"></div>
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-outline" onclick="closeModal('modalBanner')">Batal</button><button class="btn btn-primary"><i class="ti ti-check"></i> Simpan</button></div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
function openModal(id){ document.getElementById(id).classList.add('show'); }
function closeModal(id){ document.getElementById(id).classList.remove('show'); }
document.querySelectorAll('.modal-backdrop').forEach(m => m.addEventListener('click', e => { if(e.target===m) m.classList.remove('show'); }));
</script>
@endpush
