<nav class="nb">
  <div class="nb-l">
    {{-- Toggle Sidebar --}}
    <button class="nb-toggle" id="tbtn" aria-label="Toggle Sidebar">
      <i class="ti ti-menu-2" style="font-size:17px;"></i>
    </button>

    {{-- Logo --}}
    <div class="nb-logo">
      @if(active_school()?->logo)
        <img src="{{ asset(active_school()->logo) }}"
             alt="Logo"
             style="width:100%;height:100%;object-fit:cover;border-radius:9px;">
      @else
        <span>SMS</span>
      @endif
    </div>

    {{-- School Name --}}
    <div>
      <div class="nb-school-name">
        {{ active_school()?->name ?? config('app.name') }}
      </div>
      @if(active_school()?->activeSchoolYear)
        <div class="nb-school-sub">
          TA {{ active_school()->activeSchoolYear->year }}
          — Semester {{ active_school()->activeSchoolYear->semester }}
        </div>
      @endif
    </div>
  </div>

  <div class="nb-r">
    {{-- Ganti Sekolah (jika punya lebih dari 1) --}}
    @if(auth()->user()->userSchools->count() > 1 || auth()->user()->hasRole('super_admin'))
      <button class="nb-switcher" onclick="window.location='{{ route('select.school') }}'">
        <i class="ti ti-switch-horizontal" style="font-size:11px;"></i>
        Ganti Sekolah
      </button>
    @endif

    <div class="nb-divider"></div>

    {{-- Notifikasi --}}
    <button class="nb-icon-btn" aria-label="Notifikasi">
    <i class="ti ti-bell" style="font-size:17px;"></i>
    @php
        $unreadCount = auth()->user()->notifications()->where('is_read', false)->count();
    @endphp
    @if($unreadCount > 0)
        <div class="nb-notif-dot"></div>
    @endif
    </button>

    <div class="nb-divider"></div>

    {{-- Avatar --}}
    <div class="nb-avatar" title="{{ auth()->user()->name }}">
      {{ auth()->user()->initials }}
    </div>
  </div>
</nav>
