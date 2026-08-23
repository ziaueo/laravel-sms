// Modal Ganti Sekolah.
// Daftar sekolah diambil sebagai potongan HTML dari /select-school/list,
// jadi markup kartunya tetap hidup di Blade — tidak ada templating di sini.

const modal = document.getElementById('schoolSwitcherModal');

if (modal) {
  const listEl   = document.getElementById('schoolSwitcherList');
  const searchEl = document.getElementById('schoolSwitcherSearch');
  const formEl   = document.getElementById('schoolSwitcherForm');
  const inputEl  = document.getElementById('schoolSwitcherInput');
  const listUrl  = modal.dataset.listUrl;

  const DEBOUNCE_MS = 250;

  let controller = null;  // request daftar yang sedang berjalan
  let debounceId  = null;
  let submitting  = false;

  const skeleton = () =>
    '<div class="ss-skeleton"></div>'.repeat(3);

  const errorState = () => `
    <div class="ss-error">
      <i class="ti ti-alert-triangle"></i>
      <div class="ss-error-title">Gagal memuat daftar sekolah</div>
      <button type="button" class="btn btn-outline btn-sm" data-ss-retry>
        <i class="ti ti-refresh"></i> Coba lagi
      </button>
    </div>`;

  async function load(q, { keepOld = false } = {}) {
    if (controller) controller.abort();
    controller = new AbortController();

    // Saat mencari, isi lama dipertahankan supaya daftar tidak berkedip tiap ketikan.
    if (keepOld) listEl.classList.add('is-loading');
    else listEl.innerHTML = skeleton();

    try {
      const url = q ? `${listUrl}?q=${encodeURIComponent(q)}` : listUrl;
      const res = await fetch(url, {
        signal: controller.signal,
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
      });

      if (!res.ok) throw new Error(`HTTP ${res.status}`);

      listEl.innerHTML = await res.text();
    } catch (err) {
      // Pembatalan itu kita sendiri yang lakukan — bukan kegagalan.
      if (err.name === 'AbortError') return;
      listEl.innerHTML = errorState();
    } finally {
      listEl.classList.remove('is-loading');
    }
  }

  function open() {
    modal.classList.add('show');
    searchEl.value = '';
    load('');
    setTimeout(() => searchEl.focus(), 50);
  }

  function close() {
    modal.classList.remove('show');
    clearTimeout(debounceId);
    if (controller) controller.abort();
  }

  // ── Pemicu ──────────────────────────────────────────
  document.querySelectorAll('[data-ss-open]').forEach((el) => {
    el.addEventListener('click', (e) => {
      e.preventDefault();
      open();
    });
  });

  // ── Penutup ─────────────────────────────────────────
  modal.querySelectorAll('[data-ss-close]').forEach((el) => {
    el.addEventListener('click', close);
  });

  modal.addEventListener('click', (e) => {
    if (e.target === modal) close();
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal.classList.contains('show')) close();
  });

  // ── Pencarian ───────────────────────────────────────
  searchEl.addEventListener('input', () => {
    clearTimeout(debounceId);
    debounceId = setTimeout(
      () => load(searchEl.value.trim(), { keepOld: true }),
      DEBOUNCE_MS,
    );
  });

  // ── Pilih sekolah / coba lagi ───────────────────────
  listEl.addEventListener('click', (e) => {
    if (e.target.closest('[data-ss-retry]')) {
      load(searchEl.value.trim());
      return;
    }

    const item = e.target.closest('[data-school-id]');
    if (!item || submitting) return;

    submitting = true;
    listEl.classList.add('is-submitting');
    item.classList.add('is-switching');

    inputEl.value = item.dataset.schoolId;
    formEl.submit();
  });
}
