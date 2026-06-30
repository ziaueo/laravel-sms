@extends('layouts.app')

@section('title', 'Ekstrakurikuler')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb"><i class="ti ti-home" style="font-size:11px;"></i> Beranda <span>/ Ekstrakurikuler</span></div>
    <div class="page-title">Ekstrakurikuler</div>
    <div class="page-subtitle">{{ $school->name }}</div>
  </div>
  <div class="page-actions"><button class="btn btn-primary" onclick="openModal('modalEkskul')"><i class="ti ti-plus"></i> Tambah</button></div>
</div>

<div class="form-page-grid" style="grid-template-columns:repeat(3,1fr);">
  @forelse($extracurriculars as $e)
    <div class="card">
      <div class="card-body">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;">
          <div style="font-weight:700;">{{ $e->name }}</div>
          @if($e->is_active)<span class="badge badge-green">Aktif</span>@else<span class="badge badge-red">Nonaktif</span>@endif
        </div>
        <div style="font-size:12.5px;color:var(--color-text-secondary);margin:8px 0;">{{ \Illuminate\Support\Str::limit($e->description, 80) ?: 'Tanpa deskripsi' }}</div>
        <div style="display:flex;gap:6px;">
          <a href="{{ route('extracurriculars.show', hid($e)) }}" class="btn btn-sm btn-outline" style="flex:1;justify-content:center;"><i class="ti ti-users"></i> Anggota</a>
          <form method="POST" action="{{ route('extracurriculars.destroy', hid($e)) }}" onsubmit="return confirm('Hapus ekskul ini?')">@csrf @method('DELETE')
            <button class="btn btn-sm btn-danger btn-icon"><i class="ti ti-trash" style="font-size:13px;"></i></button>
          </form>
        </div>
      </div>
    </div>
  @empty
    <div class="card" style="grid-column:1/-1;"><div class="card-body" style="text-align:center;padding:40px;color:var(--color-text-secondary);">Belum ada ekstrakurikuler.</div></div>
  @endforelse
</div>

<div class="modal-backdrop" id="modalEkskul">
  <div class="modal-box" style="max-width:420px;">
    <form method="POST" action="{{ route('extracurriculars.store') }}">@csrf
      <div class="modal-header"><div class="modal-title"><i class="ti ti-ball-football"></i> Tambah Ekstrakurikuler</div>
        <button type="button" class="modal-close" onclick="closeModal('modalEkskul')"><i class="ti ti-x"></i></button></div>
      <div class="modal-body">
        <div class="form-group"><label class="form-label required">Nama</label><input type="text" name="name" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control" rows="3"></textarea></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-outline" onclick="closeModal('modalEkskul')">Batal</button><button class="btn btn-primary"><i class="ti ti-check"></i> Simpan</button></div>
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
