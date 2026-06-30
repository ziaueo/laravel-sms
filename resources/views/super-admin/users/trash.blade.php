@extends('layouts.app')

@section('title', 'User Terhapus')

@section('content')

<div class="page-header">
  <div>
    <div class="page-breadcrumb">
      <i class="ti ti-home" style="font-size:11px;"></i> Beranda
      <span>/ <a href="{{ route('users.index') }}" style="color:#2d6a4f;">Manajemen User</a> / Sampah</span>
    </div>
    <div class="page-title">User Terhapus</div>
    <div class="page-subtitle">Data user yang sudah dihapus, masih bisa dipulihkan</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('users.index') }}" class="btn btn-outline">
      <i class="ti ti-arrow-left"></i> Kembali
    </a>
  </div>
</div>

{{-- TABS --}}
<div class="tabs-wrap">
  @foreach(\App\Constants\RoleConstant::getAll() as $value => $label)
    @if($value !== \App\Constants\RoleConstant::SUPER_ADMIN)
      <a href="{{ route('users.trash', ['tab' => $value]) }}"
         class="tab-item {{ $activeTab == $value ? 'active' : '' }}">
        {{ $label }}
      </a>
    @endif
  @endforeach
</div>

<div class="card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Nama</th>
          <th>Email</th>
          <th>Sekolah</th>
          <th>Dihapus Pada</th>
          <th style="text-align:right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
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
            <td>{{ format_datetime($user->deleted_at) }}</td>
            <td>
              <div style="display:flex;gap:6px;justify-content:flex-end;">
                <form method="POST" action="{{ route('users.restore', hid($user)) }}" style="display:inline;">
                  @csrf @method('PATCH')
                  <button type="submit" class="btn btn-sm btn-outline" title="Pulihkan">
                    <i class="ti ti-restore" style="font-size:13px;"></i> Pulihkan
                  </button>
                </form>

                <button class="btn btn-sm btn-danger" title="Hapus Permanen"
                        onclick="openForceDeleteModal('{{ hid($user) }}', '{{ $user->name }}')">
                  <i class="ti ti-trash-x" style="font-size:13px;"></i> Hapus Permanen
                </button>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" style="text-align:center;padding:30px;color:#6c8f7a;">
              <i class="ti ti-trash-off" style="font-size:28px;display:block;margin-bottom:8px;"></i>
              Tidak ada user yang terhapus untuk role ini
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <x-pagination :paginator="$users" />
</div>

{{-- MODAL HAPUS PERMANEN --}}
<div class="modal-backdrop" id="modalForceDelete">
  <div class="modal-box" style="max-width:400px;">
    <div class="modal-header">
      <div class="modal-title" style="color:#991b1b;">
        <i class="ti ti-alert-triangle"></i> Hapus Permanen
      </div>
      <button type="button" class="modal-close" onclick="closeModal('modalForceDelete')"><i class="ti ti-x"></i></button>
    </div>
    <div class="modal-body">
      <p style="font-size:12.5px;color:var(--color-text-secondary);">
        Hapus permanen <strong id="forceDeleteUserName"></strong>?
        Data ini <strong>tidak bisa dipulihkan lagi</strong> setelah dihapus permanen.
      </p>
    </div>
    <form method="POST" id="formForceDelete">
      @csrf @method('DELETE')
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeModal('modalForceDelete')">Batal</button>
        <button type="submit" class="btn btn-danger"><i class="ti ti-trash-x"></i> Ya, Hapus Permanen</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
function openModal(id) { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }

function openForceDeleteModal(userId, userName) {
  document.getElementById('forceDeleteUserName').textContent = userName;
  document.getElementById('formForceDelete').action = `/users/${userId}/force-delete`;
  openModal('modalForceDelete');
}

document.querySelectorAll('.modal-backdrop').forEach(modal => {
  modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.classList.remove('show');
  });
});
</script>
@endpush
