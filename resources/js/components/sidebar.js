const sb      = document.getElementById('sb');
const overlay = document.getElementById('sbOverlay');
const tbtn    = document.getElementById('tbtn');

if (!sb || !overlay || !tbtn) return;

const MOBILE = () => window.matchMedia('(max-width:900px)').matches;

function closeDrawer() {
  sb.classList.remove('open');
  overlay.classList.remove('show');
}

tbtn.addEventListener('click', () => {
  if (MOBILE()) {
    sb.classList.toggle('open');
    overlay.classList.toggle('show');
  } else {
    sb.classList.toggle('col');
    localStorage.setItem('sidebar_collapsed', sb.classList.contains('col'));
  }
});

overlay.addEventListener('click', closeDrawer);

window.addEventListener('resize', () => {
  if (!MOBILE()) closeDrawer();
});

// Restore collapsed state
if (!MOBILE() && localStorage.getItem('sidebar_collapsed') === 'true') {
  sb.classList.add('col');
}

// Modal confirm helper
window.confirmDelete = function(action) {
  const form = document.getElementById('modalConfirmForm');
  if (form) form.action = action;
  const modal = document.getElementById('modalConfirm');
  if (modal) modal.style.display = 'flex';
}
