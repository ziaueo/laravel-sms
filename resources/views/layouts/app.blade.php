<!DOCTYPE html>
<html lang="id">
<head>
    <script>
        (function() {
            const saved = localStorage.getItem('theme');
            const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (saved === 'dark' || (!saved && systemDark)) {
            document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo_sms.png') }}">
    <title>@yield('title', 'Dashboard') — {{ active_school()?->name ?? config('app.name') }}</title>

    {{-- Tabler Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.44.0/dist/tabler-icons.min.css">

    {{-- Vite CSS --}}
    @vite(['resources/css/app.css'])

    @stack('styles')
</head>
<body>

<div class="app-root">

  {{-- NAVBAR --}}
  @include('components.navbar')

  {{-- BODY --}}
  <div class="app-body">

    {{-- SIDEBAR OVERLAY (mobile) --}}
    <div class="sb-overlay" id="sbOverlay"></div>

    {{-- SIDEBAR --}}
    @include('components.sidebar')

    {{-- CONTENT --}}
    <div class="app-content" id="appContent">

      {{-- ALERT --}}
      @if(session('success'))
        <x-alert type="success" :message="session('success')" />
      @endif
      @if(session('error'))
        <x-alert type="error" :message="session('error')" />
      @endif
      @if(session('warning'))
        <x-alert type="warning" :message="session('warning')" />
      @endif

      {{-- VALIDATION ERRORS (global) --}}
      @if(isset($errors) && $errors->any())
        <div class="alert alert-error" id="alertErrors">
          <i class="ti ti-circle-x" style="font-size:18px;flex-shrink:0;"></i>
          <div>
            @foreach($errors->all() as $error)
              <div style="font-size:13px;">{{ $error }}</div>
            @endforeach
          </div>
          <button onclick="document.getElementById('alertErrors').remove()"
                  style="margin-left:auto;background:none;border:none;cursor:pointer;font-size:16px;color:inherit;">
            <i class="ti ti-x"></i>
          </button>
        </div>
      @endif

      {{-- PAGE CONTENT --}}
      @yield('content')

    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{-- LIGHTBOX FOTO --}}
{{-- ═══════════════════════════════════════════════ --}}
@include('components.lightbox')

{{-- ═══════════════════════════════════════════════ --}}
{{-- MODAL GANTI SEKOLAH --}}
{{-- ═══════════════════════════════════════════════ --}}
@if(can_switch_school())
  @include('components.school-switcher-modal')
@endif

{{-- ═══════════════════════════════════════════════ --}}
{{-- MODAL KONFIRMASI (GLOBAL) --}}
{{--
  Satu modal untuk semua konfirmasi di aplikasi. Dipicu otomatis oleh:
  - tombol .btn-primary (aksi simpan), atau
  - atribut data-confirm pada tombol maupun form.

  Aksi berbahaya (.btn-danger) memakai warna merah dan ikon berbeda.
  Pasang data-no-confirm untuk melewatinya.
--}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="modal-backdrop" id="globalConfirmModal">
  <div class="modal-box" style="max-width:400px;">
    <div class="modal-header">
      <div class="modal-title">
        <i class="ti ti-help-circle" id="globalConfirmIcon"></i>
        <span id="globalConfirmTitle">Konfirmasi</span>
      </div>
      <button type="button" class="modal-close" id="globalConfirmClose"><i class="ti ti-x"></i></button>
    </div>
    <div class="modal-body">
      <p id="globalConfirmText" style="font-size:13px;color:var(--color-text-secondary);margin:0;">
        Apakah Anda yakin ingin menyimpan data ini?
      </p>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-outline" id="globalConfirmCancel">Batal</button>
      <button type="button" class="btn btn-primary" id="globalConfirmOk">
        <i class="ti ti-check" id="globalConfirmOkIcon"></i> <span id="globalConfirmOkText">Lanjutkan</span>
      </button>
    </div>
  </div>
</div>

<script>
(function () {
  const modal     = document.getElementById('globalConfirmModal');
  const okBtn     = document.getElementById('globalConfirmOk');
  const cancelBtn = document.getElementById('globalConfirmCancel');
  const closeBtn  = document.getElementById('globalConfirmClose');
  const textEl    = document.getElementById('globalConfirmText');
  const iconEl    = document.getElementById('globalConfirmIcon');
  const titleEl   = document.getElementById('globalConfirmTitle');
  const okIconEl  = document.getElementById('globalConfirmOkIcon');
  const okTextEl  = document.getElementById('globalConfirmOkText');
  let pending = null;

  const PRESET = {
    normal: {
      icon: 'ti ti-help-circle', title: 'Konfirmasi',
      okClass: 'btn btn-primary', okIcon: 'ti ti-check', okText: 'Lanjutkan',
      message: 'Apakah Anda yakin ingin menyimpan data ini?',
    },
    danger: {
      icon: 'ti ti-alert-triangle', title: 'Tindakan Berbahaya',
      okClass: 'btn btn-danger', okIcon: 'ti ti-trash', okText: 'Ya, Lanjutkan',
      message: 'Tindakan ini tidak bisa dibatalkan. Lanjutkan?',
    },
  };

  function applyPreset(kind, message) {
    const p = PRESET[kind];
    iconEl.className   = p.icon;
    titleEl.textContent = p.title;
    okBtn.className    = p.okClass;
    okIconEl.className = p.okIcon;
    okTextEl.textContent = p.okText;
    textEl.textContent = message || p.message;
  }

  function openModal()  { modal.classList.add('show'); }
  function closeModal() { modal.classList.remove('show'); pending = null; }

  cancelBtn.addEventListener('click', closeModal);
  closeBtn.addEventListener('click', closeModal);
  modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

  okBtn.addEventListener('click', function () {
    if (!pending) return;
    const { form, submitter } = pending;
    form.dataset.confirmed = '1';
    modal.classList.remove('show');
    pending = null;
    if (form.requestSubmit) form.requestSubmit(submitter || undefined);
    else form.submit();
  });

  // Tangkap submit dari tombol "Simpan" (btn-primary) atau apa pun yang
  // membawa data-confirm — pada tombolnya sendiri maupun pada form.
  document.addEventListener('submit', function (e) {
    const form = e.target;

    // Sudah dikonfirmasi → lanjutkan submit asli
    if (form.dataset.confirmed === '1') { delete form.dataset.confirmed; return; }

    const btn = e.submitter;
    if (!btn || btn.tagName !== 'BUTTON' || btn.type !== 'submit') return;
    if (btn.hasAttribute('data-no-confirm') || form.hasAttribute('data-no-confirm')) return;

    const message = btn.getAttribute('data-confirm') || form.getAttribute('data-confirm');

    // Tanpa pesan eksplisit, hanya tombol simpan yang dikonfirmasi.
    if (!message && !btn.classList.contains('btn-primary')) return;

    // Validasi HTML5 dulu — jika tidak valid, biarkan browser yang menandai
    if (typeof form.reportValidity === 'function' && !form.reportValidity()) {
      e.preventDefault();
      return;
    }

    e.preventDefault();
    pending = { form, submitter: btn };
    applyPreset(btn.classList.contains('btn-danger') ? 'danger' : 'normal', message);
    openModal();
  });

  // Esc menutup modal, seperti modal lain di aplikasi.
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modal.classList.contains('show')) closeModal();
  });
})();
</script>

{{-- Vite JS --}}
@vite(['resources/js/app.js'])

@stack('scripts')
</body>
</html>
