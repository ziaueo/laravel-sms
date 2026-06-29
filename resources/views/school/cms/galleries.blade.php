@extends('layouts.app')

@section('title', 'Galeri')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb"><i class="ti ti-home" style="font-size:11px;"></i> Beranda
      <span>/ <a href="{{ route('cms.index') }}" style="color:var(--color-primary);">Website</a> / Galeri</span></div>
    <div class="page-title">Album Galeri</div>
  </div>
  <div class="page-actions"><button class="btn btn-primary" onclick="openModal('modalGallery')"><i class="ti ti-plus"></i> Album Baru</button></div>
</div>

<div class="form-page-grid" style="grid-template-columns:repeat(3,1fr);">
  @forelse($galleries as $g)
    <div class="card">
      <img src="{{ $g->thumbnail ? asset($g->thumbnail) : 'https://placehold.co/600x400/e8f5ec/1a7a3c?text=Album' }}" style="width:100%;height:150px;object-fit:cover;">
      <div class="card-body">
        <div style="font-weight:600;">{{ $g->title }}</div>
        <div style="font-size:12px;color:var(--color-text-secondary);margin-bottom:10px;">{{ $g->items_count }} foto</div>
        <div style="display:flex;gap:6px;">
          <a href="{{ route('cms.galleries.show', $g->id) }}" class="btn btn-sm btn-outline" style="flex:1;justify-content:center;"><i class="ti ti-photo"></i> Kelola</a>
          <form method="POST" action="{{ route('cms.galleries.destroy', $g->id) }}" onsubmit="return confirm('Hapus album ini beserta semua foto?')">
            @csrf @method('DELETE')<button class="btn btn-sm btn-danger btn-icon"><i class="ti ti-trash" style="font-size:13px;"></i></button>
          </form>
        </div>
      </div>
    </div>
  @empty
    <div class="card" style="grid-column:1/-1;"><div class="card-body" style="text-align:center;padding:40px;color:var(--color-text-secondary);">Belum ada album.</div></div>
  @endforelse
</div>

<div class="modal-backdrop" id="modalGallery">
  <div class="modal-box" style="max-width:420px;">
    <form method="POST" action="{{ route('cms.galleries.store') }}">
      @csrf
      <div class="modal-header"><div class="modal-title"><i class="ti ti-photo"></i> Album Baru</div>
        <button type="button" class="modal-close" onclick="closeModal('modalGallery')"><i class="ti ti-x"></i></button></div>
      <div class="modal-body">
        <div class="form-group"><label class="form-label required">Judul Album</label><input type="text" name="title" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="2"></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-outline" onclick="closeModal('modalGallery')">Batal</button><button class="btn btn-primary"><i class="ti ti-check"></i> Buat</button></div>
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
