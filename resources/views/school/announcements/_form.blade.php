@php
  $roles = [
    'kepala_sekolah' => 'Kepala Sekolah',
    'guru'           => 'Guru',
    'staff'          => 'Staff',
    'siswa'          => 'Siswa',
    'orang_tua'      => 'Orang Tua',
  ];
  $selectedRoles = old('target_roles', $announcement->target_roles ?? []);
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data">
  @csrf
  @if($method === 'PUT') @method('PUT') @endif

  <div class="form-page-grid">
    <div style="display:flex;flex-direction:column;gap:14px;">
      <div class="card">
        <div class="card-header"><div class="card-title"><i class="ti ti-speakerphone"></i> Isi Pengumuman</div></div>
        <div class="card-body">
          @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:16px;">
              <i class="ti ti-alert-circle"></i>
              <div>@foreach($errors->all() as $e)<div style="font-size:12px;">{{ $e }}</div>@endforeach</div>
            </div>
          @endif
          <div class="form-group">
            <label class="form-label required">Judul</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $announcement->title ?? '') }}" required>
          </div>
          <div class="form-group">
            <label class="form-label required">Konten</label>
            <textarea name="content" class="form-control" rows="10" required>{{ old('content', $announcement->content ?? '') }}</textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Lampiran</label>
            <input type="file" name="attachment" class="form-control">
            @if(($announcement->attachment ?? null))
              <div class="form-hint"><a href="{{ asset($announcement->attachment) }}" target="_blank">Lihat lampiran saat ini</a></div>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:14px;">
      <div class="card">
        <div class="card-header"><div class="card-title"><i class="ti ti-target"></i> Sasaran & Publikasi</div></div>
        <div class="card-body">
          <label class="form-label">Target Role</label>
          <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:14px;">
            @foreach($roles as $key => $label)
              <label style="display:flex;align-items:center;gap:8px;font-size:12.5px;cursor:pointer;">
                <input type="checkbox" name="target_roles[]" value="{{ $key }}" {{ in_array($key, $selectedRoles) ? 'checked' : '' }}>
                {{ $label }}
              </label>
            @endforeach
          </div>
          <hr style="border:none;border-top:0.5px solid rgba(0,0,0,0.08);margin:6px 0 12px;">
          <label style="display:flex;align-items:center;gap:8px;font-size:12.5px;cursor:pointer;margin-bottom:8px;">
            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $announcement->is_published ?? false) ? 'checked' : '' }}> Terbitkan sekarang
          </label>
          <label style="display:flex;align-items:center;gap:8px;font-size:12.5px;cursor:pointer;margin-bottom:8px;">
            <input type="checkbox" name="is_public" value="1" {{ old('is_public', $announcement->is_public ?? false) ? 'checked' : '' }}> Tampilkan di website publik
          </label>
          <label style="display:flex;align-items:center;gap:8px;font-size:12.5px;cursor:pointer;">
            <input type="checkbox" name="show_in_feed" value="1" {{ old('show_in_feed', $announcement->show_in_feed ?? false) ? 'checked' : '' }}> Tampilkan di feed dashboard
          </label>
        </div>
      </div>
      <div style="display:flex;gap:10px;">
        <a href="{{ route('announcements.index') }}" class="btn btn-outline"><i class="ti ti-arrow-left"></i> Batal</a>
        <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Simpan</button>
      </div>
    </div>
  </div>
</form>
