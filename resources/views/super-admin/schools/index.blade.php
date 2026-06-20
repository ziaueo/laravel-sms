@extends('layouts.app')

@section('title', 'Master Data Sekolah')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb">
      <i class="ti ti-home" style="font-size:11px;"></i> Beranda <span>/ Master Data / Sekolah</span>
    </div>
    <div class="page-title">Master Data Sekolah</div>
    <div class="page-subtitle">Kelola semua sekolah dalam sistem</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('master.schools.trash') }}" class="btn btn-outline">
      <i class="ti ti-trash"></i> Sampah
    </a>
    <button class="btn btn-primary" onclick="openModal('modalAddSchool')">
      <i class="ti ti-plus"></i> Tambah Sekolah
    </button>
  </div>
</div>

{{-- FILTER --}}
<div class="card">
  <div class="card-body" style="padding:14px 16px;">
    <form method="GET" action="{{ route('master.schools.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;">
      <div style="flex:1;min-width:200px;">
        <input type="text" name="search" value="{{ request('search') }}"
               class="form-control" placeholder="Cari nama sekolah...">
      </div>
      <div style="min-width:180px;">
        <select name="school_type_id" class="form-control" onchange="this.form.submit()">
          <option value="">Semua Tipe</option>
          @foreach($schoolTypes as $type)
            <option value="{{ $type->id }}" {{ request('school_type_id') == $type->id ? 'selected' : '' }}>
              {{ $type->name }}
            </option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-outline btn-sm"><i class="ti ti-search"></i> Cari</button>
    </form>
  </div>
</div>

{{-- CARD GRID --}}
<div class="school-grid">
  @forelse($schools as $school)
    <div class="school-card">
      <div class="school-card-top">
        <div class="school-card-logo">
          @if($school->logo)
            <img src="{{ asset($school->logo) }}" alt="{{ $school->name }}">
          @else
            <i class="ti ti-building-school"></i>
          @endif
        </div>
        <div>
          @if($school->is_active)
            <span class="badge badge-green">Aktif</span>
          @else
            <span class="badge badge-red">Nonaktif</span>
          @endif
        </div>
      </div>

      <div class="school-card-name">{{ $school->name }}</div>
      <div class="school-card-type">{{ $school->schoolType->name }}</div>

      @if($school->address)
        <div class="school-card-address">
          <i class="ti ti-map-pin" style="font-size:12px;"></i> {{ Str::limit($school->address, 50) }}
        </div>
      @endif

      <div class="school-card-stats">
        <div class="school-stat-item">
          <i class="ti ti-users"></i> {{ $school->users_count }} User
        </div>
        @if($school->npsn)
          <div class="school-stat-item">
            <i class="ti ti-id"></i> NPSN {{ $school->npsn }}
          </div>
        @endif
      </div>

      <div class="school-card-actions">
        <button class="btn btn-sm btn-outline" style="flex:1;justify-content:center;"
                onclick='openEditModal(@json($school->load("profile")))'>
          <i class="ti ti-edit" style="font-size:13px;"></i> Edit
        </button>

        <form method="POST" action="{{ route('master.schools.toggle-active', $school->id) }}" style="display:inline;">
          @csrf @method('PATCH')
          <button type="submit" class="btn btn-sm btn-outline btn-icon" title="{{ $school->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
            <i class="ti {{ $school->is_active ? 'ti-toggle-right' : 'ti-toggle-left' }}" style="font-size:14px;"></i>
          </button>
        </form>

        <button class="btn btn-sm btn-danger btn-icon" title="Hapus"
                onclick="openDeleteModal({{ $school->id }}, '{{ $school->name }}')">
          <i class="ti ti-trash" style="font-size:13px;"></i>
        </button>
      </div>
    </div>
  @empty
    <div style="grid-column:1/-1;text-align:center;padding:50px;color:var(--color-text-secondary);">
      <i class="ti ti-building-skyscraper" style="font-size:32px;display:block;margin-bottom:10px;"></i>
      Belum ada data sekolah
    </div>
  @endforelse
</div>

<x-pagination :paginator="$schools" />

{{-- ═══════════════════════════════════════════════ --}}
{{-- MODAL TAMBAH SEKOLAH --}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="modalAddSchool">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-title"><i class="ti ti-building-school"></i> Tambah Sekolah Baru</div>
      <button type="button" class="modal-close" onclick="closeModal('modalAddSchool')"><i class="ti ti-x"></i></button>
    </div>

    <form method="POST" action="{{ route('master.schools.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="modal-body">

        <div class="form-group">
          <label class="form-label required">Tipe Sekolah</label>
          <select name="school_type_id" class="form-control" required>
            <option value="">Pilih tipe sekolah</option>
            @foreach($schoolTypes as $type)
              <option value="{{ $type->id }}">{{ $type->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label class="form-label required">Nama Sekolah</label>
          <input type="text" name="name" class="form-control" placeholder="Contoh: SD Harapan Bangsa" required>
        </div>

        <div class="form-group">
          <label class="form-label">NPSN</label>
          <input type="text" name="npsn" class="form-control" placeholder="Nomor Pokok Sekolah Nasional">
        </div>

        <div class="form-group">
          <label class="form-label">Alamat</label>
          <textarea name="address" class="form-control" rows="2" placeholder="Alamat lengkap sekolah"></textarea>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          <div class="form-group">
            <label class="form-label">No. Telepon</label>
            <input type="text" name="phone" class="form-control" placeholder="021xxxxxxx">
          </div>
          <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="sekolah@email.com">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Akreditasi</label>
          <input type="text" name="accreditation" class="form-control" placeholder="A / B / C">
        </div>

        <div class="form-group">
          <label class="form-label">Logo Sekolah</label>
          <input type="file" name="logo" class="form-control" accept="image/*">
          <div class="form-hint">Format JPG/PNG/WebP, maksimal 2MB</div>
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalAddSchool')">Batal</button>
        <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{-- MODAL EDIT SEKOLAH (DENGAN TAB) --}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="modalEditSchool">
  <div class="modal-box" style="max-width:560px;">
    <div class="modal-header">
      <div class="modal-title"><i class="ti ti-edit"></i> Edit Sekolah</div>
      <button type="button" class="modal-close" onclick="closeModal('modalEditSchool')"><i class="ti ti-x"></i></button>
    </div>

    {{-- Tab Switcher --}}
    <div class="modal-tabs">
      <button type="button" class="modal-tab-btn active" data-tab="dataDasar" onclick="switchModalTab('dataDasar')">
        <i class="ti ti-info-circle"></i> Data Dasar
      </button>
      <button type="button" class="modal-tab-btn" data-tab="profil" onclick="switchModalTab('profil')">
        <i class="ti ti-file-text"></i> Profil & Visi-Misi
      </button>
    </div>

    <form method="POST" id="formEditSchool" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="modal-body">

        {{-- TAB: DATA DASAR --}}
        <div class="modal-tab-content active" id="tab-dataDasar">

          <div class="form-group">
            <label class="form-label required">Tipe Sekolah</label>
            <select name="school_type_id" id="editSchoolTypeId" class="form-control" required>
              @foreach($schoolTypes as $type)
                <option value="{{ $type->id }}">{{ $type->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="form-group">
            <label class="form-label required">Nama Sekolah</label>
            <input type="text" name="name" id="editName" class="form-control" required>
          </div>

          <div class="form-group">
            <label class="form-label">NPSN</label>
            <input type="text" name="npsn" id="editNpsn" class="form-control">
          </div>

          <div class="form-group">
            <label class="form-label">Alamat</label>
            <textarea name="address" id="editAddress" class="form-control" rows="2"></textarea>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div class="form-group">
              <label class="form-label">No. Telepon</label>
              <input type="text" name="phone" id="editPhone" class="form-control">
            </div>
            <div class="form-group">
              <label class="form-label">Email</label>
              <input type="email" name="email" id="editEmail" class="form-control">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Akreditasi</label>
            <input type="text" name="accreditation" id="editAccreditation" class="form-control">
          </div>

          <div class="form-group">
            <label class="form-label">Logo Sekolah</label>
            <input type="file" name="logo" class="form-control" accept="image/*">
            <div class="form-hint">Kosongkan jika tidak ingin mengubah logo</div>
          </div>

          <div class="form-group">
            <label class="checkbox-item">
              <input type="hidden" name="is_active" value="0">
              <input type="checkbox" name="is_active" id="editIsActive" value="1">
              Sekolah aktif
            </label>
          </div>

        </div>

        {{-- TAB: PROFIL & VISI-MISI --}}
        <div class="modal-tab-content" id="tab-profil">

          <div class="form-group">
            <label class="form-label">Tagline</label>
            <input type="text" name="tagline" id="editTagline" class="form-control" placeholder="Contoh: Unggul dalam Prestasi">
          </div>

          <div class="form-group">
            <label class="form-label">Deskripsi Singkat</label>
            <textarea name="description" id="editDescription" class="form-control" rows="2"></textarea>
          </div>

          <div class="form-group">
            <label class="form-label">Visi</label>
            <textarea name="vision" id="editVision" class="form-control" rows="2"></textarea>
          </div>

          <div class="form-group">
            <label class="form-label">Misi</label>
            <textarea name="mission" id="editMission" class="form-control" rows="3"></textarea>
          </div>

          <div class="form-group">
            <label class="form-label">Sejarah Sekolah</label>
            <textarea name="history" id="editHistory" class="form-control" rows="3"></textarea>
          </div>

          <div style="display:grid;grid-template-columns:2fr 1fr;gap:10px;">
            <div class="form-group">
              <label class="form-label">Nama Kepala Sekolah</label>
              <input type="text" name="principal_name" id="editPrincipalName" class="form-control">
            </div>
            <div class="form-group">
              <label class="form-label">Tahun Berdiri</label>
              <input type="number" name="founded_year" id="editFoundedYear" class="form-control" min="1900" max="{{ date('Y') }}">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Facebook URL</label>
            <input type="text" name="facebook_url" id="editFacebook" class="form-control" placeholder="https://facebook.com/...">
          </div>

          <div class="form-group">
            <label class="form-label">Instagram URL</label>
            <input type="text" name="instagram_url" id="editInstagram" class="form-control" placeholder="https://instagram.com/...">
          </div>

          <div class="form-group">
            <label class="form-label">YouTube URL</label>
            <input type="text" name="youtube_url" id="editYoutube" class="form-control" placeholder="https://youtube.com/...">
          </div>

        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalEditSchool')">Batal</button>
        <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL HAPUS --}}
<div class="modal-backdrop" id="modalDeleteSchool">
  <div class="modal-box" style="max-width:380px;">
    <div class="modal-header">
      <div class="modal-title" style="color:var(--color-danger);"><i class="ti ti-alert-triangle"></i> Hapus Sekolah</div>
      <button type="button" class="modal-close" onclick="closeModal('modalDeleteSchool')"><i class="ti ti-x"></i></button>
    </div>
    <div class="modal-body">
      <p style="font-size:12.5px;color:var(--color-text-secondary);">
        Apakah kamu yakin ingin menghapus <strong id="deleteSchoolName"></strong>?
        Data akan dipindahkan ke Sampah.
      </p>
    </div>
    <form method="POST" id="formDeleteSchool">
      @csrf @method('DELETE')
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalDeleteSchool')">Batal</button>
        <button type="submit" class="btn btn-danger"><i class="ti ti-trash"></i> Ya, Hapus</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
function openModal(id) { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }

function switchModalTab(tab) {
  document.querySelectorAll('.modal-tab-btn').forEach(btn => btn.classList.remove('active'));
  document.querySelectorAll('.modal-tab-content').forEach(content => content.classList.remove('active'));
  document.querySelector(`.modal-tab-btn[data-tab="${tab}"]`).classList.add('active');
  document.getElementById(`tab-${tab}`).classList.add('active');
}

function openEditModal(school) {
  // Reset ke tab pertama
  switchModalTab('dataDasar');

  // Data Dasar
  document.getElementById('editSchoolTypeId').value = school.school_type_id;
  document.getElementById('editName').value = school.name;
  document.getElementById('editNpsn').value = school.npsn || '';
  document.getElementById('editAddress').value = school.address || '';
  document.getElementById('editPhone').value = school.phone || '';
  document.getElementById('editEmail').value = school.email || '';
  document.getElementById('editAccreditation').value = school.accreditation || '';
  document.getElementById('editIsActive').checked = school.is_active;

  // Profil
  const profile = school.profile || {};
  document.getElementById('editTagline').value = profile.tagline || '';
  document.getElementById('editDescription').value = profile.description || '';
  document.getElementById('editVision').value = profile.vision || '';
  document.getElementById('editMission').value = profile.mission || '';
  document.getElementById('editHistory').value = profile.history || '';
  document.getElementById('editPrincipalName').value = profile.principal_name || '';
  document.getElementById('editFoundedYear').value = profile.founded_year || '';
  document.getElementById('editFacebook').value = profile.facebook_url || '';
  document.getElementById('editInstagram').value = profile.instagram_url || '';
  document.getElementById('editYoutube').value = profile.youtube_url || '';

  document.getElementById('formEditSchool').action = `/master-data/schools/${school.id}`;
  openModal('modalEditSchool');
}

function openDeleteModal(schoolId, schoolName) {
  document.getElementById('deleteSchoolName').textContent = schoolName;
  document.getElementById('formDeleteSchool').action = `/master-data/schools/${schoolId}`;
  openModal('modalDeleteSchool');
}

document.querySelectorAll('.modal-backdrop').forEach(modal => {
  modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.classList.remove('show');
  });
});
</script>
@endpush
