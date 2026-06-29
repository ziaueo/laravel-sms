@extends('layouts.app')

@section('title', 'Pengumuman')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb"><i class="ti ti-home" style="font-size:11px;"></i> Beranda <span>/ Pengumuman</span></div>
    <div class="page-title">Pengumuman</div>
    <div class="page-subtitle">{{ $school->name }}</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('announcements.create') }}" class="btn btn-primary"><i class="ti ti-plus"></i> Buat Pengumuman</a>
  </div>
</div>

<div class="card">
  <div class="card-body" style="padding:14px 16px;">
    <form method="GET" style="display:flex;gap:10px;">
      <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari judul...">
      <button class="btn btn-outline btn-sm"><i class="ti ti-search"></i> Cari</button>
    </form>
  </div>
</div>

@forelse($announcements as $a)
  <div class="card" style="margin-bottom:12px;">
    <div class="card-body">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
        <div style="flex:1;">
          <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <span style="font-weight:700;font-size:14px;">{{ $a->title }}</span>
            @if($a->is_published)<span class="badge badge-green">Terbit</span>@else<span class="badge badge-amber">Draft</span>@endif
            @if($a->is_public)<span class="badge badge-blue">Publik</span>@endif
          </div>
          <div style="font-size:12.5px;color:var(--color-text-secondary);margin:6px 0;">
            {{ \Illuminate\Support\Str::limit(strip_tags($a->content), 160) }}
          </div>
          <div style="font-size:11px;color:var(--color-text-muted);">
            <i class="ti ti-user"></i> {{ $a->createdBy->name ?? '-' }} ·
            <i class="ti ti-clock"></i> {{ time_ago($a->created_at) }}
            @if($a->target_roles && count($a->target_roles)) · <i class="ti ti-users"></i> {{ implode(', ', $a->target_roles) }} @endif
            @if($a->attachment) · <i class="ti ti-paperclip"></i> Lampiran @endif
          </div>
        </div>
        <div style="display:flex;gap:5px;flex-shrink:0;">
          <form method="POST" action="{{ route('announcements.toggle-publish', $a->id) }}">
            @csrf
            <button class="btn btn-sm btn-outline btn-icon" title="{{ $a->is_published ? 'Jadikan draft' : 'Terbitkan' }}">
              <i class="ti {{ $a->is_published ? 'ti-eye-off' : 'ti-send' }}" style="font-size:13px;"></i>
            </button>
          </form>
          <a href="{{ route('announcements.edit', $a->id) }}" class="btn btn-sm btn-outline btn-icon" title="Edit"><i class="ti ti-edit" style="font-size:13px;"></i></a>
          <form method="POST" action="{{ route('announcements.destroy', $a->id) }}" onsubmit="return confirm('Hapus pengumuman ini?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-danger btn-icon" title="Hapus"><i class="ti ti-trash" style="font-size:13px;"></i></button>
          </form>
        </div>
      </div>
    </div>
  </div>
@empty
  <div class="card"><div class="card-body" style="text-align:center;padding:50px;color:var(--color-text-secondary);">
    <i class="ti ti-speakerphone" style="font-size:32px;display:block;margin-bottom:10px;"></i>
    Belum ada pengumuman.
  </div></div>
@endforelse

<x-pagination :paginator="$announcements" />

@endsection
