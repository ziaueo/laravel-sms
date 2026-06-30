@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb"><i class="ti ti-home" style="font-size:11px;"></i> Beranda <span>/ Notifikasi</span></div>
    <div class="page-title">Notifikasi</div>
  </div>
  <div class="page-actions">
    <form method="POST" action="{{ route('notifications.read-all') }}">@csrf
      <button class="btn btn-outline"><i class="ti ti-checks"></i> Tandai Semua Dibaca</button>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body" style="padding:0;">
    @forelse($notifications as $n)
      <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;padding:14px 16px;border-bottom:0.5px solid rgba(200,221,212,0.4);{{ $n->is_read ? '' : 'background:rgba(216,243,220,0.25);' }}">
        <div style="display:flex;gap:12px;">
          <div class="icon-{{ $n->is_read ? 'blue' : 'green' }}" style="width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="ti ti-bell"></i>
          </div>
          <div>
            <div style="font-weight:600;font-size:13px;">{{ $n->title }}</div>
            <div style="font-size:12.5px;color:var(--color-text-secondary);">{{ $n->message }}</div>
            <div style="font-size:11px;color:var(--color-text-muted);margin-top:3px;">{{ time_ago($n->created_at) }}</div>
          </div>
        </div>
        <div style="display:flex;gap:5px;flex-shrink:0;">
          @if(!$n->is_read)
            <form method="POST" action="{{ route('notifications.read', hid($n)) }}">@csrf
              <button class="btn btn-sm btn-outline btn-icon" title="Tandai dibaca"><i class="ti ti-check" style="font-size:13px;"></i></button>
            </form>
          @endif
          <form method="POST" action="{{ route('notifications.destroy', hid($n)) }}">@csrf @method('DELETE')
            <button class="btn btn-sm btn-danger btn-icon"><i class="ti ti-trash" style="font-size:13px;"></i></button>
          </form>
        </div>
      </div>
    @empty
      <div style="text-align:center;padding:50px;color:var(--color-text-secondary);">
        <i class="ti ti-bell-off" style="font-size:32px;display:block;margin-bottom:10px;"></i>
        Tidak ada notifikasi.
      </div>
    @endforelse
  </div>
</div>
<x-pagination :paginator="$notifications" />

@endsection
