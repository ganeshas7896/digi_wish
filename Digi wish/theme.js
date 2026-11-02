/* ==================================================================
   theme.js — Dark / Light mode toggle
   ------------------------------------------------------------------
   Recommendation: this file is included on every page (index.html,
   signup.html, dashboard.php) BEFORE main.js, and stores the choice
   in localStorage so the theme stays the same across page loads.
   ================================================================== */

(function () {
  const STORAGE_KEY = 'digiwish-theme';
  const root = document.documentElement;
  const toggleBtn = document.getElementById('themeToggle');

  function applyTheme(theme) {
    if (theme === 'dark') {
      root.setAttribute('data-theme', 'dark');
      if (toggleBtn) {
        toggleBtn.querySelector('.theme-toggle-icon').textContent = '☀️';
        toggleBtn.querySelector('.theme-toggle-label').textContent = 'Light mode';
      }
    } else {
      root.removeAttribute('data-theme');
      if (toggleBtn) {
        toggleBtn.querySelector('.theme-toggle-icon').textContent = '🌙';
        toggleBtn.querySelector('.theme-toggle-label').textContent = 'Dark mode';
      }
    }
  }

  // On load: use saved preference, otherwise respect the OS setting.
  const saved = localStorage.getItem(STORAGE_KEY);
  if (saved) {
    applyTheme(saved);
  } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
    applyTheme('dark');
  }

  if (toggleBtn) {
    toggleBtn.addEventListener('click', function () {
      const isDark = root.getAttribute('data-theme') === 'dark';
      const next = isDark ? 'light' : 'dark';
      applyTheme(next);
      localStorage.setItem(STORAGE_KEY, next);
    });
  }
})();
