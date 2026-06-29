<div class="sb" id="sb">

  {{-- User Card --}}
  <div class="sb-user-card">
    <div class="sb-user-ava">{{ auth()->user()->initials }}</div>
    <div class="sb-user-info">
      <div class="sb-user-name">{{ auth()->user()->name }}</div>
      <div class="sb-user-role">
        {{ auth()->user()->getRoleNames()->first()
            ? role_label(\App\Constants\RoleConstant::SUPER_ADMIN)
            : auth()->user()->getRoleNames()->first() }}
        @php
          $roleName = auth()->user()->getRoleNames()->first();
          $roleMap = [
            'super_admin'    => \App\Constants\RoleConstant::SUPER_ADMIN,
            'kepala_sekolah' => \App\Constants\RoleConstant::KEPALA_SEKOLAH,
            'guru'           => \App\Constants\RoleConstant::GURU,
            'staff'          => \App\Constants\RoleConstant::STAFF,
            'siswa'          => \App\Constants\RoleConstant::SISWA,
            'orang_tua'      => \App\Constants\RoleConstant::ORANG_TUA,
          ];
          $roleValue = $roleMap[$roleName] ?? null;
        @endphp
        {{ $roleValue ? role_label($roleValue) : $roleName }}
      </div>
    </div>
  </div>

  {{-- Menu Card --}}
  <div class="sb-menu-card">

    <div class="sb-section">Utama</div>
    <a href="{{ route('dashboard') }}"
       class="sb-menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <i class="ti ti-layout-dashboard"></i>
      <span class="sb-menu-label">Dashboard</span>
    </a>

    @canany(['students.view', 'teachers.view', 'classrooms.view'])
    <div class="sb-section">Akademik</div>
    @endcanany

    @can('students.view')
    <a href="{{ route('students.index') }}"
       class="sb-menu-item {{ request()->routeIs('students.*') ? 'active' : '' }}">
      <i class="ti ti-users"></i>
      <span class="sb-menu-label">Data Siswa</span>
    </a>
    @endcan

    @can('teachers.view')
    <a href="{{ route('teachers.index') }}"
       class="sb-menu-item {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
      <i class="ti ti-school"></i>
      <span class="sb-menu-label">Guru & Staff</span>
    </a>
    @endcan

    @can('classrooms.view')
    <a href="{{ route('classrooms.index') }}"
       class="sb-menu-item {{ request()->routeIs('classrooms.*') ? 'active' : '' }}">
      <i class="ti ti-door"></i>
      <span class="sb-menu-label">Kelas</span>
    </a>
    @endcan

    @can('schedules.view')
    <a href="{{ route('schedules.index') }}"
       class="sb-menu-item {{ request()->routeIs('schedules.*') ? 'active' : '' }}">
      <i class="ti ti-calendar-event"></i>
      <span class="sb-menu-label">Jadwal</span>
    </a>
    @endcan

    @can('attendances.view')
    <a href="{{ route('attendances.index') }}"
       class="sb-menu-item {{ request()->routeIs('attendances.*') ? 'active' : '' }}">
      <i class="ti ti-clipboard-check"></i>
      <span class="sb-menu-label">Absensi</span>
    </a>
    @endcan

    @can('scores.view')
    <a href="{{ route('scores.index') }}"
       class="sb-menu-item {{ request()->routeIs('scores.*') ? 'active' : '' }}">
      <i class="ti ti-chart-bar"></i>
      <span class="sb-menu-label">Penilaian</span>
    </a>
    @endcan

    @can('report_cards.view')
    <a href="{{ route('report-cards.index') }}"
       class="sb-menu-item {{ request()->routeIs('report-cards.*') ? 'active' : '' }}">
      <i class="ti ti-report"></i>
      <span class="sb-menu-label">Rapot</span>
    </a>
    @endcan

    @can('students.view')
    <a href="{{ route('extracurriculars.index') }}"
       class="sb-menu-item {{ request()->routeIs('extracurriculars.*') ? 'active' : '' }}">
      <i class="ti ti-ball-football"></i>
      <span class="sb-menu-label">Ekstrakurikuler</span>
    </a>
    @endcan

    @canany(['announcements.view', 'ppdb.view', 'cms.view'])
    <div class="sb-section">Manajemen</div>
    @endcanany

    @can('announcements.view')
    <a href="{{ route('announcements.index') }}"
       class="sb-menu-item {{ request()->routeIs('announcements.*') ? 'active' : '' }}">
      <i class="ti ti-speakerphone"></i>
      <span class="sb-menu-label">Pengumuman</span>
    </a>
    @endcan

    @can('ppdb.view')
    <a href="{{ route('ppdb.index') }}"
       class="sb-menu-item {{ request()->routeIs('ppdb.*') ? 'active' : '' }}">
      <i class="ti ti-user-plus"></i>
      <span class="sb-menu-label">PPDB</span>
    </a>
    @endcan

    @can('cms.view')
    <a href="{{ route('cms.index') }}"
       class="sb-menu-item {{ request()->routeIs('cms.*') ? 'active' : '' }}">
      <i class="ti ti-world"></i>
      <span class="sb-menu-label">Website</span>
    </a>
    @endcan

    @canany(['schools.view', 'users.view'])
    <div class="sb-section">Sistem</div>
    @endcanany

    @can('schools.view')
    <a href="{{ route('master.index') }}"
       class="sb-menu-item {{ request()->routeIs('master.*') ? 'active' : '' }}">
      <i class="ti ti-building-school"></i>
      <span class="sb-menu-label">Master Data</span>
    </a>
    @endcan

    @can('users.view')
    <a href="{{ route('users.index') }}"
       class="sb-menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
      <i class="ti ti-users-group"></i>
      <span class="sb-menu-label">Manajemen User</span>
    </a>
    @endcan

    @can('users.view')
    <a href="{{ route('registrations.index') }}"
       class="sb-menu-item {{ request()->routeIs('registrations.*') ? 'active' : '' }}">
      <i class="ti ti-user-check"></i>
      <span class="sb-menu-label">Verifikasi Pendaftaran</span>
    </a>
    @endcan

    <a href="{{ route('settings.index') }}"
       class="sb-menu-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
      <i class="ti ti-settings"></i>
      <span class="sb-menu-label">Pengaturan</span>
    </a>

    {{-- Logout --}}
    <form method="POST" action="{{ route('auth.logout') }}" style="margin-top:auto;">
      @csrf
      <button type="submit" class="sb-menu-item" style="width:100%;border:none;background:none;cursor:pointer;color:#e63946;">
        <i class="ti ti-logout" style="color:#e63946;"></i>
        <span class="sb-menu-label">Keluar</span>
      </button>
    </form>

  </div>

  {{-- School Switcher Card --}}
  @if(active_school())
  <a href="{{ route('select.school') }}" class="sb-school-card">
    <div class="sb-school-icon">
      <i class="ti ti-building-school"></i>
    </div>
    <div class="sb-school-info">
      <div class="sb-school-name">{{ active_school()->name }}</div>
      <div class="sb-school-type">{{ active_school()->schoolType->name }}</div>
    </div>
    <i class="ti ti-chevron-right sb-school-arrow"></i>
  </a>
  @endif

</div>
