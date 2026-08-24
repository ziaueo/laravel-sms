{{--
  Lightbox foto — dipakai layout aplikasi maupun layout publik.

  Gaya dan skripnya sengaja ditulis di sini, bukan di bundle Vite: layout publik
  tidak punya pipeline Vite sama sekali, jadi partial mandiri adalah satu-satunya
  cara memakai kode yang sama di kedua tempat.

  Cara pakai: <img src="..." alt="Keterangan" data-lightbox="nama-grup">
  Gambar dengan nama grup sama bisa digeser bolak-balik. Keterangan diambil dari
  data-caption, jatuh ke alt kalau kosong.
--}}

<style>
  [data-lightbox]{ cursor:zoom-in; }
  [data-lightbox]:focus-visible{ outline:3px solid #52b788; outline-offset:2px; }

  .lb{ display:none; position:fixed; inset:0; z-index:1000; padding:28px 16px;
       background:rgba(10,12,11,.92); align-items:center; justify-content:center; }
  .lb.is-open{ display:flex; }

  .lb-figure{ margin:0; display:flex; flex-direction:column; align-items:center; gap:14px; max-width:100%; }
  .lb-img{ max-width:92vw; max-height:82vh; width:auto; height:auto; display:block;
           border-radius:10px; box-shadow:0 20px 60px rgba(0,0,0,.55); }

  .lb-cap{ display:flex; flex-direction:column; align-items:center; gap:4px;
           max-width:min(92vw,720px); text-align:center; color:#e9efec; font-size:14px; line-height:1.5; }
  .lb-counter{ font-size:12.5px; color:#9fb0a7; letter-spacing:.4px; }

  .lb-close,.lb-nav{ position:absolute; background:rgba(255,255,255,.10); color:#fff; border:none;
                     border-radius:50%; cursor:pointer; display:flex; align-items:center;
                     justify-content:center; transition:background .15s ease; }
  .lb-close:hover,.lb-nav:hover{ background:rgba(255,255,255,.22); }
  .lb-close{ top:18px; right:18px; width:42px; height:42px; font-size:20px; }
  .lb-nav{ top:50%; transform:translateY(-50%); width:48px; height:48px; font-size:26px; }
  .lb-prev{ left:18px; }
  .lb-next{ right:18px; }

  /* Grup berisi satu foto: tidak ada yang bisa dituju */
  .lb.is-single .lb-nav,
  .lb.is-single .lb-counter{ display:none; }

  @media(max-width:560px){
    .lb-nav{ width:40px; height:40px; font-size:22px; }
    .lb-prev{ left:8px; }
    .lb-next{ right:8px; }
    .lb-img{ max-width:96vw; max-height:70vh; }
  }
</style>

<div class="lb" id="lightbox" role="dialog" aria-modal="true" aria-label="Pratinjau foto">
  <button type="button" class="lb-close" data-lb-close aria-label="Tutup"><i class="ti ti-x"></i></button>
  <button type="button" class="lb-nav lb-prev" data-lb-prev aria-label="Foto sebelumnya"><i class="ti ti-chevron-left"></i></button>

  <figure class="lb-figure">
    <img class="lb-img" src="" alt="">
    <figcaption class="lb-cap">
      <span class="lb-caption-text"></span>
      <span class="lb-counter"></span>
    </figcaption>
  </figure>

  <button type="button" class="lb-nav lb-next" data-lb-next aria-label="Foto berikutnya"><i class="ti ti-chevron-right"></i></button>
</div>

<script>
(function () {
  const lb = document.getElementById('lightbox');
  if (!lb) return;

  const imgEl   = lb.querySelector('.lb-img');
  const capEl   = lb.querySelector('.lb-caption-text');
  const countEl = lb.querySelector('.lb-counter');
  const closeEl = lb.querySelector('[data-lb-close]');

  let group  = [];    // gambar sekelompok, urut sesuai DOM
  let index  = 0;
  let opener = null;  // gambar yang diklik, untuk mengembalikan fokus

  const captionOf = (el) => el.getAttribute('data-caption') || el.getAttribute('alt') || '';

  function render() {
    const el = group[index];
    imgEl.src        = el.currentSrc || el.src;
    imgEl.alt        = captionOf(el);
    capEl.textContent   = captionOf(el);
    countEl.textContent = (index + 1) + ' / ' + group.length;
    lb.classList.toggle('is-single', group.length < 2);
  }

  function open(el) {
    const name = el.getAttribute('data-lightbox');
    group  = Array.from(document.querySelectorAll('[data-lightbox="' + CSS.escape(name) + '"]'));
    index  = Math.max(0, group.indexOf(el));
    opener = el;

    render();
    lb.classList.add('is-open');
    document.body.style.overflow = 'hidden';
    closeEl.focus();
  }

  function close() {
    lb.classList.remove('is-open');
    document.body.style.overflow = '';
    imgEl.src = '';
    if (opener) { opener.focus(); opener = null; }
  }

  function step(delta) {
    if (group.length < 2) return;
    index = (index + delta + group.length) % group.length;
    render();
  }

  // Gambar yang bisa diklik juga harus bisa dijangkau keyboard.
  document.querySelectorAll('[data-lightbox]').forEach((el) => {
    if (!el.hasAttribute('tabindex')) el.setAttribute('tabindex', '0');
    el.setAttribute('role', 'button');
  });

  document.addEventListener('click', function (e) {
    const trigger = e.target.closest('[data-lightbox]');
    if (trigger) { e.preventDefault(); open(trigger); return; }

    if (e.target.closest('[data-lb-close]')) { close();  return; }
    if (e.target.closest('[data-lb-prev]'))  { step(-1); return; }
    if (e.target.closest('[data-lb-next]'))  { step(1);  return; }

    // Hanya latar yang menutup — klik pada gambar atau keterangan tidak.
    if (e.target === lb) close();
  });

  document.addEventListener('keydown', function (e) {
    if (!lb.classList.contains('is-open')) {
      // Enter / Spasi pada gambar yang sedang difokus membuka lightbox.
      const focused = document.activeElement;
      if ((e.key === 'Enter' || e.key === ' ') && focused && focused.matches('[data-lightbox]')) {
        e.preventDefault();
        open(focused);
      }
      return;
    }

    if (e.key === 'Escape')          { close(); }
    else if (e.key === 'ArrowLeft')  { step(-1); }
    else if (e.key === 'ArrowRight') { step(1); }
  });
})();
</script>
