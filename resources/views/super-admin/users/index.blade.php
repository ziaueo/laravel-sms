@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')

<div class="page-header">
    <div>
        <div class="page-breadcrumb">
            <i class="ti ti-home" style="font-size:11px;"></i> Beranda <span>/ Manajemen User</span>
        </div>
        <div class="page-title">Manajemen User</div>
        <div class="page-subtitle">Kelola akun Kepala Sekolah, Guru, Staff, Siswa, dan Orang Tua</div>
    </div>
    <div class="page-actions">
        <a href="{{ route('users.trash', ['tab' => $activeTab]) }}" class="btn btn-outline">
            <i class="ti ti-trash"></i> Sampah
        </a>
        <button class="btn btn-primary" onclick="openModal('modalAddUser')">
            <i class="ti ti-plus"></i> Tambah User
        </button>
        </div>
</div>

{{-- TABS --}}
<div class="tabs-wrap">
  @foreach(\App\Constants\RoleConstant::getAll() as $value => $label)
    @if($value !== \App\Constants\RoleConstant::SUPER_ADMIN)
      <a href="{{ route('users.index', ['tab' => $value]) }}"
         class="tab-item {{ $activeTab == $value ? 'active' : '' }}">
        {{ $label }}
        <span class="tab-count">{{ $roleCounts[$value] ?? 0 }}</span>
      </a>
    @endif
  @endforeach
</div>

{{-- FILTER & SEARCH --}}
<div class="card">
  <div class="card-body" style="padding:14px 16px;">
    <form method="GET" action="{{ route('users.index') }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
      <input type="hidden" name="tab" value="{{ $activeTab }}">

      <div style="flex:1;min-width:200px;">
        <input type="text" name="search" value="{{ request('search') }}"
               class="form-control" placeholder="Cari nama atau email...">
      </div>

      @if($schools->count() > 1)
      <div style="min-width:180px;">
        <select name="school_id" class="form-control" onchange="this.form.submit()">
          <option value="">Semua Sekolah</option>
          @foreach($schools as $school)
            <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
              {{ $school->name }}
            </option>
          @endforeach
        </select>
      </div>
      @endif

      <button type="submit" class="btn btn-outline btn-sm">
        <i class="ti ti-search"></i> Cari
      </button>
    </form>
  </div>

  {{-- TABLE --}}
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Nama</th>
          <th>Email</th>
          <th>Sekolah</th>
          <th>Status</th>
          <th style="text-align:right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
          @php
            $userPayload = [
              'name'         => $user->name,
              'email'        => $user->email,
              'phone'        => $user->phone,
              'is_active'    => $user->is_active,
              'user_schools' => $user->userSchools->map(fn($us) => ['school_id' => $us->school_id])->values(),
            ];
          @endphp
          <tr>
            <td>
              <div class="td-user">
                <div class="td-avatar">{{ $user->initials }}</div>
                {{ $user->name }}
              </div>
            </td>
            <td>{{ $user->email }}</td>
            <td>
              @foreach($user->userSchools as $us)
                <span class="badge badge-green" style="margin-right:4px;">{{ $us->school->name }}</span>
              @endforeach
            </td>
            <td>
              @if($user->is_active)
                <span class="badge badge-green">Aktif</span>
              @else
                <span class="badge badge-red">Nonaktif</span>
              @endif
            </td>
            <td>
              <div style="display:flex;gap:6px;justify-content:flex-end;">
                <button class="btn btn-icon btn-outline" title="Edit"
                        onclick='openEditModal(@json($userPayload), "{{ hid($user) }}")'>
                  <i class="ti ti-edit" style="font-size:14px;"></i>
                </button>

                <form method="POST" action="{{ route('users.toggle-active', hid($user)) }}" style="display:inline;">
                  @csrf @method('PATCH')
                  <button type="submit" class="btn btn-icon btn-outline" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                    <i class="ti {{ $user->is_active ? 'ti-toggle-right' : 'ti-toggle-left' }}" style="font-size:14px;"></i>
                  </button>
                </form>

                <button class="btn btn-icon btn-outline" title="Reset Password"
                        onclick="openResetModal('{{ hid($user) }}', '{{ $user->name }}')">
                  <i class="ti ti-key" style="font-size:14px;"></i>
                </button>

                <button class="btn btn-icon btn-danger" title="Hapus"
                        onclick="openDeleteModal('{{ hid($user) }}', '{{ $user->name }}')">
                  <i class="ti ti-trash" style="font-size:14px;"></i>
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" style="text-align:center;padding:30px;color:#6c8f7a;">
              <i class="ti ti-mood-empty" style="font-size:28px;display:block;margin-bottom:8px;"></i>
              Belum ada data user untuk role ini
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <x-pagination :paginator="$users" />
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{-- MODAL TAMBAH USER --}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="modalAddUser">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-title">
        <i class="ti ti-user-plus"></i> Tambah User Baru
      </div>
      <button type="button" class="modal-close" onclick="closeModal('modalAddUser')">
        <i class="ti ti-x"></i>
      </button>
    </div>

    <form method="POST" action="{{ route('users.store') }}">
      @csrf
      <div class="modal-body">

        <div class="form-group">
          <label class="form-label required">Role</label>
          <select name="role" class="form-control" required>
            @foreach(\App\Constants\RoleConstant::getAll() as $value => $label)
              @if($value !== \App\Constants\RoleConstant::SUPER_ADMIN)
                <option value="{{ $value }}" {{ $activeTab == $value ? 'selected' : '' }}>{{ $label }}</option>
              @endif
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label class="form-label required">Nama Lengkap</label>
          <input type="text" name="name" class="form-control" placeholder="Masukkan nama lengkap" required>
        </div>

        <div class="form-group">
          <label class="form-label required">Email</label>
          <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
        </div>

        <div class="form-group">
          <label class="form-label">No. Telepon</label>
          <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx">
        </div>

        <div class="form-group">
          <label class="form-label required">Sekolah</label>
          <div class="checkbox-group">
            @foreach($schools as $school)
              <label class="checkbox-item">
                <input type="checkbox" name="school_ids[]" value="{{ $school->id }}">
                {{ $school->name }}
              </label>
            @endforeach
          </div>
          <div class="form-hint">Bisa pilih lebih dari satu sekolah</div>
        </div>

        <div class="info-box">
          <i class="ti ti-info-circle"></i>
          Password default: <strong>P@ssw0rd</strong> — User wajib mengganti password saat login pertama.
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalAddUser')">Batal</button>
        <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Simpan</button>
      </div>
    </form>
  </div>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{-- MODAL EDIT USER --}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="modalEditUser">
  <div class="modal-box">
    <div class="modal-header">
      <div class="modal-title">
        <i class="ti ti-edit"></i> Edit User
      </div>
      <button type="button" class="modal-close" onclick="closeModal('modalEditUser')">
        <i class="ti ti-x"></i>
      </button>
    </div>

    <form method="POST" id="formEditUser">
      @csrf
      @method('PUT')
      <div class="modal-body">

        <div class="form-group">
          <label class="form-label required">Nama Lengkap</label>
          <input type="text" name="name" id="editName" class="form-control" required>
        </div>

        <div class="form-group">
          <label class="form-label required">Email</label>
          <input type="email" name="email" id="editEmail" class="form-control" required>
        </div>

        <div class="form-group">
          <label class="form-label">No. Telepon</label>
          <input type="text" name="phone" id="editPhone" class="form-control">
        </div>

        <div class="form-group">
          <label class="form-label required">Sekolah</label>
          <div class="checkbox-group" id="editSchoolCheckboxes">
            @foreach($schools as $school)
              <label class="checkbox-item">
                <input type="checkbox" name="school_ids[]" value="{{ $school->id }}" class="edit-school-cb">
                {{ $school->name }}
              </label>
            @endforeach
          </div>
        </div>

        <div class="form-group">
            <label class="checkbox-item">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" id="editIsActive" value="1">
                User aktif
            </label>
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalEditUser')">Batal</button>
        <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{-- MODAL RESET PASSWORD --}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="modalResetPassword">
  <div class="modal-box" style="max-width:380px;">
    <div class="modal-header">
      <div class="modal-title"><i class="ti ti-key"></i> Reset Password</div>
      <button type="button" class="modal-close" onclick="closeModal('modalResetPassword')"><i class="ti ti-x"></i></button>
    </div>
    <div class="modal-body">
      <p style="font-size:12.5px;color:var(--color-text-secondary);">
        Reset password untuk <strong id="resetUserName"></strong> ke default
        <strong>P@ssw0rd</strong>? User wajib mengganti password saat login berikutnya.
      </p>
    </div>
    <form method="POST" id="formResetPassword">
      @csrf @method('PATCH')
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalResetPassword')">Batal</button>
        <button type="submit" class="btn btn-primary"><i class="ti ti-key"></i> Reset Password</button>
      </div>
    </form>
  </div>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{-- MODAL HAPUS USER --}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="modalDeleteUser">
  <div class="modal-box" style="max-width:380px;">
    <div class="modal-header">
      <div class="modal-title" style="color:#991b1b;"><i class="ti ti-alert-triangle"></i> Hapus User</div>
      <button type="button" class="modal-close" onclick="closeModal('modalDeleteUser')"><i class="ti ti-x"></i></button>
    </div>
    <div class="modal-body">
      <p style="font-size:12.5px;color:var(--color-text-secondary);">
        Apakah kamu yakin ingin menghapus <strong id="deleteUserName"></strong>?
        Tindakan ini tidak dapat dibatalkan.
      </p>
    </div>
    <form method="POST" id="formDeleteUser">
      @csrf @method('DELETE')
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalDeleteUser')">Batal</button>
        <button type="submit" class="btn btn-danger"><i class="ti ti-trash"></i> Ya, Hapus</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
function openModal(id) {
  document.getElementById(id).classList.add('show');
}
function closeModal(id) {
  document.getElementById(id).classList.remove('show');
}

function openEditModal(user, hash) {
  document.getElementById('editName').value = user.name;
  document.getElementById('editEmail').value = user.email;
  document.getElementById('editPhone').value = user.phone || '';
  document.getElementById('editIsActive').checked = user.is_active;

  // Uncheck semua dulu
  document.querySelectorAll('.edit-school-cb').forEach(cb => cb.checked = false);

  // Check sesuai sekolah user
  const schoolIds = user.user_schools.map(us => us.school_id);
  document.querySelectorAll('.edit-school-cb').forEach(cb => {
    if (schoolIds.includes(parseInt(cb.value))) cb.checked = true;
  });

  document.getElementById('formEditUser').action = `/users/${hash}`;
  openModal('modalEditUser');
}

function openResetModal(userId, userName) {
  document.getElementById('resetUserName').textContent = userName;
  document.getElementById('formResetPassword').action = `/users/${userId}/reset-password`;
  openModal('modalResetPassword');
}

function openDeleteModal(userId, userName) {
  document.getElementById('deleteUserName').textContent = userName;
  document.getElementById('formDeleteUser').action = `/users/${userId}`;
  openModal('modalDeleteUser');
}

// Close modal saat klik backdrop
document.querySelectorAll('.modal-backdrop').forEach(modal => {
  modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.classList.remove('show');
  });
});
</script>
@endpush
