// Perilaku situs publik: menu mobile, bayangan navbar saat digulir, dan slider hero.
// Situs publik sebelumnya tidak punya JavaScript sama sekali.

// ── BAYANGAN NAVBAR SAAT DIGULIR ──────────────────────
const nav = document.querySelector('.pnav');

if (nav) {
  const syncStuck = () => nav.classList.toggle('is-stuck', window.scrollY > 8);
  syncStuck();
  window.addEventListener('scroll', syncStuck, { passive: true });
}

// ── MENU MOBILE ───────────────────────────────────────
const panel = document.getElementById('navPanel');

if (panel) {
  const openBtn = document.querySelector('[data-nav-open]');

  function openPanel() {
    panel.classList.add('is-open');
    document.body.style.overflow = 'hidden';
    panel.querySelector('a')?.focus();
  }

  function closePanel() {
    panel.classList.remove('is-open');
    document.body.style.overflow = '';
    openBtn?.focus();
  }

  openBtn?.addEventListener('click', openPanel);

  panel.addEventListener('click', (e) => {
    // Latar, tombol tutup, atau salah satu menu diklik — semuanya menutup panel.
    if (e.target === panel || e.target.closest('[data-nav-close]') || e.target.closest('a')) {
      closePanel();
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && panel.classList.contains('is-open')) closePanel();
  });
}

// ── SLIDER HERO ───────────────────────────────────────
const hero = document.getElementById('heroSlider');

if (hero) {
  const slides = Array.from(hero.querySelectorAll('.phero-slide'));
  const dots   = Array.from(hero.querySelectorAll('.phero-dot'));

  const INTERVAL_MS = 6000;
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  let index = 0;
  let timer = null;

  function show(i) {
    index = (i + slides.length) % slides.length;
    slides.forEach((s, n) => s.classList.toggle('is-active', n === index));
    dots.forEach((d, n) => {
      d.classList.toggle('is-active', n === index);
      d.setAttribute('aria-selected', n === index ? 'true' : 'false');
    });
  }

  function start() {
    // Satu banner tidak perlu berputar, dan reduce-motion berarti jangan bergerak sendiri.
    if (slides.length < 2 || reduceMotion) return;
    stop();
    timer = setInterval(() => show(index + 1), INTERVAL_MS);
  }

  function stop() {
    if (timer) { clearInterval(timer); timer = null; }
  }

  dots.forEach((dot, n) => {
    dot.addEventListener('click', () => { show(n); start(); });
  });

  hero.addEventListener('mouseenter', stop);
  hero.addEventListener('mouseleave', start);

  // Berhenti saat tab tidak terlihat — tidak ada gunanya berputar di latar.
  document.addEventListener('visibilitychange', () => {
    document.hidden ? stop() : start();
  });

  show(0);
  start();
}
