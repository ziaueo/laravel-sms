@extends('layouts.app')

@section('title', 'Berita')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb"><i class="ti ti-home" style="font-size:11px;"></i> Beranda
      <span>/ <a href="{{ route('cms.index') }}" style="color:var(--color-primary);">Website</a> / Berita</span></div>
    <div class="page-title">Berita & Artikel</div>
  </div>
  <div class="page-actions" style="display:flex;gap:8px;">
    <button class="btn btn-outline" onclick="openModal('modalCat')"><i class="ti ti-tags"></i> Kategori</button>
    <a href="{{ route('cms.posts.create') }}" class="btn btn-primary"><i class="ti ti-plus"></i> Tulis Berita</a>
  </div>
</div>

<div class="card">
  <div class="card-body" style="padding:0;">
    <div class="table-wrapper">
      <table>
        <thead><tr><th>Judul</th><th>Kategori</th><th style="text-align:center;">Status</th><th>Tanggal</th><th style="text-align:right;">Aksi</th></tr></thead>
        <tbody>
          @forelse($posts as $post)
            <tr>
              <td><div style="font-weight:600;">{{ $post->title }}</div></td>
              <td>{{ $post->category->name ?? '-' }}</td>
              <td style="text-align:center;">@if($post->is_published)<span class="badge badge-green">Terbit</span>@else<span class="badge badge-amber">Draft</span>@endif</td>
              <td style="font-size:12px;">{{ format_date($post->published_at ?? $post->created_at) }}</td>
              <td style="text-align:right;">
                <div style="display:inline-flex;gap:5px;">
                  <a href="{{ route('cms.posts.edit', $post->id) }}" class="btn btn-sm btn-outline btn-icon"><i class="ti ti-edit" style="font-size:13px;"></i></a>
                  <form method="POST" action="{{ route('cms.posts.destroy', $post->id) }}" onsubmit="return confirm('Hapus berita ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger btn-icon"><i class="ti ti-trash" style="font-size:13px;"></i></button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--color-text-secondary);">Belum ada berita.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
<x-pagination :paginator="$posts" />

<div class="modal-backdrop" id="modalCat">
  <div class="modal-box" style="max-width:420px;">
    <div class="modal-header"><div class="modal-title"><i class="ti ti-tags"></i> Kategori Berita</div>
      <button type="button" class="modal-close" onclick="closeModal('modalCat')"><i class="ti ti-x"></i></button></div>
    <div class="modal-body">
      @if($categories->count())
        <div style="margin-bottom:12px;display:flex;gap:6px;flex-wrap:wrap;">
          @foreach($categories as $cat)<span class="badge badge-blue">{{ $cat->name }}</span>@endforeach
        </div>
      @endif
      <form method="POST" action="{{ route('cms.categories.store') }}" style="display:flex;gap:8px;">
        @csrf
        <input type="text" name="name" class="form-control" placeholder="Nama kategori" required>
        <button class="btn btn-primary"><i class="ti ti-plus"></i></button>
      </form>
    </div>
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
