const RENT_THEME_KEY = 'rentThemeMode';

function updateThemeMode(mode) {
  const useLight = mode === 'light';
  document.body.classList.toggle('light-mode', useLight);

  const toggle = document.getElementById('themeToggle');
  const icon = toggle?.querySelector('.theme-toggle-icon');
  const label = toggle?.querySelector('.theme-toggle-label');

  if (toggle) {
    const labelText = useLight ? 'Dark mode' : 'Light mode';
    toggle.setAttribute('aria-label', `Switch to ${useLight ? 'dark' : 'light'} mode`);
    toggle.title = `Switch to ${useLight ? 'dark' : 'light'} mode`;
    if (label) {
      label.textContent = labelText;
    }
  }

  if (icon) {
    icon.textContent = useLight ? '🌙' : '☀️';
  }

  localStorage.setItem(RENT_THEME_KEY, useLight ? 'light' : 'dark');
}

function loadUserThemePreference() {
  const savedTheme = localStorage.getItem(RENT_THEME_KEY);
  updateThemeMode(savedTheme === 'light' ? 'light' : 'dark');
}

function initThemeMode() {
  loadUserThemePreference();

  const toggle = document.getElementById('themeToggle');
  if (toggle) {
    toggle.addEventListener('click', () => {
      const nextMode = document.body.classList.contains('light-mode') ? 'dark' : 'light';
      updateThemeMode(nextMode);
    });
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initThemeMode);
} else {
  initThemeMode();
}
