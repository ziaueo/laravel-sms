(function () {
  const root = document.documentElement;
  const toggleBtn = document.getElementById('themeToggle');
  const icon = document.getElementById('themeIcon');

  function applyTheme(theme) {
    if (theme === 'dark') {
      root.setAttribute('data-theme', 'dark');
      if (icon) {
        icon.classList.remove('ti-moon');
        icon.classList.add('ti-sun');
      }
    } else {
      root.removeAttribute('data-theme');
      if (icon) {
        icon.classList.remove('ti-sun');
        icon.classList.add('ti-moon');
      }
    }
  }

  // Load preferensi tersimpan, atau ikut sistem OS jika belum pernah diset
  const saved = localStorage.getItem('theme');
  const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const initialTheme = saved || (systemPrefersDark ? 'dark' : 'light');

  applyTheme(initialTheme);

  if (toggleBtn) {
    toggleBtn.addEventListener('click', () => {
      const isDark = root.getAttribute('data-theme') === 'dark';
      const newTheme = isDark ? 'light' : 'dark';
      applyTheme(newTheme);
      localStorage.setItem('theme', newTheme);
    });
  }
})();
