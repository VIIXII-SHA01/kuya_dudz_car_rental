  function updateTheme(mode) {
    const isLight = mode === 'light';
    document.body.classList.toggle('light-mode', isLight);
    const themeToggle = document.getElementById('themeToggle');
    const themeToggleLabel = themeToggle?.querySelector('.theme-toggle-label');
    const themeToggleIcon = themeToggle?.querySelector('.theme-toggle-icon');
    if (themeToggleLabel) themeToggleLabel.textContent = isLight ? 'Dark mode' : 'Light mode';
    if (themeToggleIcon) themeToggleIcon.textContent = isLight ? '🌙' : '☀️';
    localStorage.setItem('themeMode', mode);
  }

  function initThemeToggle() {
    const themeToggle = document.getElementById('themeToggle');
    const savedTheme = localStorage.getItem('themeMode');
    updateTheme(savedTheme === 'light' ? 'light' : 'dark');
    themeToggle?.addEventListener('click', () => {
      const nextMode = document.body.classList.contains('light-mode') ? 'dark' : 'light';
      updateTheme(nextMode);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initThemeToggle);
  } else {
    initThemeToggle();
  }

  // ── Password toggle ──
  const eyeToggle = document.getElementById('eyeToggle');
  const passInput = document.getElementById('passwordInput');
  const eyeIcon   = document.getElementById('eyeIcon');

  let passVisible = false;
  if (eyeToggle && passInput && eyeIcon) {
    eyeToggle.addEventListener('click', () => {
      passVisible = !passVisible;
      passInput.type = passVisible ? 'text' : 'password';
      eyeToggle.style.opacity = passVisible ? '0.7' : '0.3';
      eyeIcon.innerHTML = passVisible
        ? `<path d="M2 2l14 14M7.5 6.5A3.5 3.5 0 0 1 12.5 11M1 9s3.5-5.5 8-5.5c1.5 0 2.9.4 4.1 1.1M17 9s-1.2 1.9-3.1 3.4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>`
        : `<path d="M1 9s3.5-5.5 8-5.5S17 9 17 9s-3.5 5.5-8 5.5S1 9 1 9z" stroke="currentColor" stroke-width="1.4"/><circle cx="9" cy="9" r="2.5" stroke="currentColor" stroke-width="1.4"/>`;
    });
  }

  // ── Validation helpers ──
  function validateEmail(v) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim()); }
  function validatePassword(v) { return v.length >= 6; }

  function setFieldState(groupId, inputEl, valid) {
    const group = document.getElementById(groupId);
    if (valid === null) {
      group.classList.remove('has-error');
      inputEl.classList.remove('valid', 'invalid');
    } else if (valid) {
      group.classList.remove('has-error');
      inputEl.classList.add('valid');
      inputEl.classList.remove('invalid');
    } else {
      group.classList.add('has-error');
      inputEl.classList.add('invalid');
      inputEl.classList.remove('valid');
    }
  }

  // Live validation on blur
  const emailInput = document.getElementById('emailInput');
  const passwordInput = document.getElementById('passwordInput');

  if (emailInput) {
    emailInput.addEventListener('blur', function() {
      if (this.value) setFieldState('emailGroup', this, validateEmail(this.value));
    });
    emailInput.addEventListener('input', function() {
      if (this.classList.contains('invalid') && validateEmail(this.value))
        setFieldState('emailGroup', this, true);
    });
  }
  if (passwordInput) {
    passwordInput.addEventListener('blur', function() {
      if (this.value) setFieldState('passwordGroup', this, validatePassword(this.value));
    });
    passwordInput.addEventListener('input', function() {
      if (this.classList.contains('invalid') && validatePassword(this.value))
        setFieldState('passwordGroup', this, true);
    });
  }

  // ── Toast ──
  function showToast(msg, type = 'error') {
    const toast = document.getElementById('toast');
    const toastMsg = document.getElementById('toastMsg');
    const toastIcon = document.getElementById('toastIcon');
    toastMsg.textContent = msg;
    toast.className = 'toast' + (type === 'success' ? ' success' : '');
    void toast.offsetWidth;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3500);
  }

  function applyServerLoginError() {
    const params = new URLSearchParams(window.location.search);
    const error = params.get('error');
    if (!error) return;

    if (error === 'restricted') {
      const reason = params.get('reason');
      const msg = reason === 'restricted'
        ? 'This account has been restricted and cannot sign in. Contact an administrator to restore access.'
        : 'This account is not allowed to sign in. Contact an administrator.';
      window.alert(msg);
      return;
    }

    if (error !== 'invalid') return;
    if (emailInput) setFieldState('emailGroup', emailInput, false);
    if (passwordInput) setFieldState('passwordGroup', passwordInput, false);

    const reason = params.get('reason');
    if (reason === 'notfound') {
      window.alert('We couldn’t find an account with that email. Please check the email address or sign up.');
    } else if (reason === 'wrongpass') {
      window.alert('The password is incorrect. Please try again or reset your password if you need help.');
    } else {
      window.alert('Oops! That email/password combination isn’t right. Please try again.');
    }
  }

  // ── Form submit ──
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
      e.preventDefault();

      const email = document.getElementById('emailInput');
      const pass  = document.getElementById('passwordInput');
      const btn   = document.getElementById('submitBtn');

      if (!email || !pass || !btn) return;

      const emailOk = validateEmail(email.value);
      const passOk  = validatePassword(pass.value);

      setFieldState('emailGroup', email, emailOk);
      setFieldState('passwordGroup', pass, passOk);

      if (!emailOk || !passOk) {
        showToast('Please fix the errors above.');
        return;
      }

      btn.classList.add('loading');
      loginForm.submit();
    });

    applyServerLoginError();
  }

