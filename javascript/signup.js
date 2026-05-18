 /* ────────────────────────────────
     STATE
  ──────────────────────────────── */
  let currentStep = 1;
  const totalSteps = 3;

  // Set max birthdate to 18 years ago
  const maxDate = new Date();
  maxDate.setFullYear(maxDate.getFullYear() - 18);
  document.getElementById('birthdate').max = maxDate.toISOString().split('T')[0];

  /* ────────────────────────────────
     TOAST
  ──────────────────────────────── */
  function showToast(msg, type = 'error') {
    const t = document.getElementById('toast');
    const tm = document.getElementById('toastMsg');
    const ti = document.getElementById('toastIcon');
    tm.textContent = msg;
    t.className = 'toast' + (type === 'success' ? ' success' : '');
    const color = type === 'success' ? '#3DBE7A' : '#E8341A';
    ti.innerHTML = type === 'success'
      ? `<circle cx="7.5" cy="7.5" r="6" stroke="${color}" stroke-width="1.3"/><path d="M5 7.5l2 2 3.5-3.5" stroke="${color}" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>`
      : `<circle cx="7.5" cy="7.5" r="6" stroke="${color}" stroke-width="1.3"/><path d="M7.5 5v3" stroke="${color}" stroke-width="1.5" stroke-linecap="round"/><circle cx="7.5" cy="10" r="0.7" fill="${color}"/>`;
    void t.offsetWidth;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3500);
  }

  /* ────────────────────────────────
     FIELD VALIDATION HELPERS
  ──────────────────────────────── */
  function setErr(grpId, el, valid) {
    const g = document.getElementById('grp-' + grpId);
    if (!g) return;
    g.classList.toggle('has-error', !valid);
    if (el) {
      el.classList.toggle('invalid', !valid);
      el.classList.toggle('valid', valid);
    }
  }
  function clearErr(grpId, el) {
    const g = document.getElementById('grp-' + grpId);
    if (!g) return;
    g.classList.remove('has-error');
    if (el) el.classList.remove('invalid', 'valid');
  }

  /* ────────────────────────────────
     STEP 1 VALIDATION
  ──────────────────────────────── */
  function validateStep1() {
    let ok = true;
    const fn = document.getElementById('firstName');
    const ln = document.getElementById('lastName');
    const ag = document.getElementById('age');
    const bd = document.getElementById('birthdate');
    const genderSel = document.querySelector('.gender-opt.selected');

    if (!fn.value.trim()) { setErr('firstName', fn, false); ok = false; } else setErr('firstName', fn, true);
    if (!ln.value.trim()) { setErr('lastName', ln, false); ok = false; } else setErr('lastName', ln, true);
    if (!genderSel) {
      document.getElementById('grp-gender').classList.add('has-error'); ok = false;
    } else {
      document.getElementById('grp-gender').classList.remove('has-error');
    }
    const ageVal = parseInt(ag.value);
    if (!ag.value || ageVal < 18 || ageVal > 110) { setErr('age', ag, false); ok = false; } else setErr('age', ag, true);
    if (!bd.value) { setErr('birthdate', bd, false); ok = false; } else setErr('birthdate', bd, true);

    return ok;
  }

  /* ────────────────────────────────
     STEP 2 VALIDATION
  ──────────────────────────────── */
  function validateStep2() {
    let ok = true;
    const ph = document.getElementById('phone');
    const em = document.getElementById('email');
    const st = document.getElementById('street');
    const cy = document.getElementById('city');
    const pr = document.getElementById('province');
    const zp = document.getElementById('zip');

    const phoneClean = ph.value.replace(/\s/g, '');
    const phoneOk = /^9\d{9}$/.test(phoneClean) || /^09\d{9}$/.test(phoneClean);
    if (!phoneOk) { setErr('phone', ph, false); document.getElementById('grp-phone').classList.add('has-error'); ok = false; }
    else { setErr('phone', ph, true); document.getElementById('grp-phone').classList.remove('has-error'); }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em.value.trim())) { setErr('email', em, false); ok = false; } else setErr('email', em, true);
    if (!st.value.trim()) { setErr('street', st, false); ok = false; } else setErr('street', st, true);
    if (!cy.value.trim()) { setErr('city', cy, false); ok = false; } else setErr('city', cy, true);
    if (!pr.value.trim()) { setErr('province', pr, false); ok = false; } else setErr('province', pr, true);
    if (!/^\d{4}$/.test(zp.value.trim())) { setErr('zip', zp, false); ok = false; } else setErr('zip', zp, true);

    return ok;
  }

  /* ────────────────────────────────
     STEP 3 VALIDATION
  ──────────────────────────────── */
  function validateStep3() {
    let ok = true;
    const pw  = document.getElementById('password');
    const cpw = document.getElementById('confirmPass');
    const terms = document.getElementById('terms');

    if (pw.value.length < 8) { setErr('password', pw, false); ok = false; } else setErr('password', pw, true);
    if (pw.value !== cpw.value || !cpw.value) { setErr('confirm', cpw, false); ok = false; } else setErr('confirm', cpw, true);
    if (!terms.checked) {
      showToast('You must agree to the Terms of Service.');
      ok = false;
    }
    return ok;
  }

  /* ────────────────────────────────
     NAVIGATION
  ──────────────────────────────── */
  function updateUI(step) {
    currentStep = step;
    // Panels
    document.querySelectorAll('.step-panel').forEach((p, i) => {
      p.classList.toggle('active', i + 1 === step);
    });
    // Sidebar steps
    document.querySelectorAll('#sideSteps .step-item').forEach((el, i) => {
      el.classList.remove('active', 'done');
      if (i + 1 === step) el.classList.add('active');
      if (i + 1 < step) el.classList.add('done');
      // Checkmark for done
      const num = el.querySelector('.step-num');
      if (i + 1 < step) {
        num.innerHTML = `<svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M3 6.5l3 3 4-5" stroke="#3DBE7A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>`;
      } else {
        num.textContent = i + 1;
      }
    });
    // Mobile pills
    document.querySelectorAll('.mobile-step-pill').forEach((el, i) => {
      el.classList.remove('active', 'done');
      if (i + 1 === step) el.classList.add('active');
      if (i + 1 < step) el.classList.add('done');
    });
    // Progress
    const pct = Math.round((step / totalSteps) * 100);
    document.getElementById('progressFill').style.width = pct + '%';
    document.getElementById('progressPct').textContent = pct + '%';

    // Scroll to top of form
    document.querySelector('.form-scroll').scrollTo({ top: 0, behavior: 'smooth' });
  }

  function goNext(step) {
    let valid = false;
    if (step === 1) valid = validateStep1();
    if (step === 2) valid = validateStep2();
    if (!valid) { showToast('Please complete all required fields.'); return; }
    updateUI(step + 1);
  }

  function goPrev(step) {
    updateUI(step - 1);
  }

  function submitForm() {
    if (!validateStep3()) { showToast('Please fix the errors above.'); return; }
    const btn = document.getElementById('submitBtn');
    btn.classList.add('loading');
    setTimeout(() => {
      btn.classList.remove('loading');
      // Hide all step panels, show success
      document.querySelectorAll('.step-panel').forEach(p => p.style.display = 'none');
      document.getElementById('successScreen').classList.add('show');
      // Update sidebar to all done
      document.querySelectorAll('#sideSteps .step-item').forEach(el => {
        el.classList.remove('active');
        el.classList.add('done');
        const num = el.querySelector('.step-num');
        num.innerHTML = `<svg width="13" height="13" viewBox="0 0 13 13" fill="none"><path d="M3 6.5l3 3 4-5" stroke="#3DBE7A" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>`;
      });
      document.getElementById('progressFill').style.width = '100%';
      document.getElementById('progressPct').textContent = '100%';
      showToast('Account created successfully!', 'success');
    }, 2200);
  }

  /* ────────────────────────────────
     GENDER TOGGLE
  ──────────────────────────────── */
  document.querySelectorAll('.gender-opt').forEach(opt => {
    opt.addEventListener('click', function() {
      document.querySelectorAll('.gender-opt').forEach(o => o.classList.remove('selected'));
      this.classList.add('selected');
      this.querySelector('input').checked = true;
      document.getElementById('grp-gender').classList.remove('has-error');
    });
  });

  /* ────────────────────────────────
     PASSWORD STRENGTH
  ──────────────────────────────── */
  const passInput = document.getElementById('password');
  passInput.addEventListener('input', function() {
    const v = this.value;
    const len     = v.length >= 8;
    const upper   = /[A-Z]/.test(v);
    const num     = /[0-9]/.test(v);
    const special = /[^a-zA-Z0-9]/.test(v);
    let score = [len, upper, num, special].filter(Boolean).length;

    // Update requirement indicators
    updateReq('len', len);
    updateReq('upper', upper);
    updateReq('num', num);
    updateReq('special', special);

    // Strength bars
    const bars = ['sb1','sb2','sb3','sb4'];
    const classes = ['','weak','fair','fair','strong'];
    bars.forEach((id, i) => {
      const el = document.getElementById(id);
      el.className = 'sbar';
      if (i < score) el.classList.add(score <= 1 ? 'weak' : score === 2 ? 'fair' : score === 3 ? 'fair' : 'strong');
    });

    const labels = ['—', 'Weak', 'Fair', 'Good', 'Strong'];
    const colors = ['var(--muted)', 'var(--red)', 'var(--gold)', 'var(--gold)', 'var(--green)'];
    const st = document.getElementById('strengthText');
    st.textContent = v.length ? labels[score] : '—';
    st.style.color = v.length ? colors[score] : 'var(--muted)';

    if (this.classList.contains('invalid') && v.length >= 8) setErr('password', this, true);
  });

  function updateReq(id, met) {
    const el = document.getElementById('req-' + id);
    const icon = document.getElementById('ri-' + id);
    el.style.color = met ? '#3DBE7A' : 'var(--muted)';
    icon.innerHTML = met
      ? `<circle cx="6.5" cy="6.5" r="5.5" fill="rgba(61,190,122,0.15)" stroke="#3DBE7A" stroke-width="1.2"/><path d="M4 6.5l2 2 3.5-3.5" stroke="#3DBE7A" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>`
      : `<circle cx="6.5" cy="6.5" r="5.5" stroke="currentColor" stroke-width="1.2"/>`;
  }

  /* ────────────────────────────────
     EYE TOGGLES
  ──────────────────────────────── */
  function makeEyeToggle(btnId, inputId) {
    const btn = document.getElementById(btnId);
    const inp = document.getElementById(inputId);
    let vis = false;
    btn.addEventListener('click', () => {
      vis = !vis;
      inp.type = vis ? 'text' : 'password';
      btn.style.opacity = vis ? '0.7' : '0.3';
    });
  }
  makeEyeToggle('eyePass', 'password');
  makeEyeToggle('eyeConfirm', 'confirmPass');

  /* ────────────────────────────────
     LIVE VALIDATION ON BLUR
  ──────────────────────────────── */
  document.getElementById('firstName').addEventListener('blur', function() {
    if (this.value) setErr('firstName', this, this.value.trim().length > 0);
  });
  document.getElementById('lastName').addEventListener('blur', function() {
    if (this.value) setErr('lastName', this, this.value.trim().length > 0);
  });
  document.getElementById('age').addEventListener('blur', function() {
    if (this.value) { const v = parseInt(this.value); setErr('age', this, v >= 18 && v <= 110); }
  });
  document.getElementById('email').addEventListener('blur', function() {
    if (this.value) setErr('email', this, /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value));
  });
  document.getElementById('confirmPass').addEventListener('input', function() {
    const pw = document.getElementById('password').value;
    if (this.value && this.classList.contains('invalid')) {
      if (this.value === pw) setErr('confirm', this, true);
    }
  });

  /* ────────────────────────────────
     AGE AUTO-FILL FROM BIRTHDATE
  ──────────────────────────────── */
  document.getElementById('birthdate').addEventListener('change', function() {
    if (!this.value) return;
    const bd = new Date(this.value);
    const today = new Date();
    let age = today.getFullYear() - bd.getFullYear();
    const m = today.getMonth() - bd.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < bd.getDate())) age--;
    const ageEl = document.getElementById('age');
    ageEl.value = age;
    setErr('age', ageEl, age >= 18 && age <= 110);
    setErr('birthdate', this, true);
  });

  /* ────────────────────────────────
     MOBILE PILL CLICK (read-only nav)
  ──────────────────────────────── */
  document.querySelectorAll('.mobile-step-pill').forEach(pill => {
    pill.addEventListener('click', function() {
      const target = parseInt(this.dataset.step);
      if (target < currentStep) updateUI(target); // allow going back
    });
  });

    (function() {
    if (window.signupThemeToggleInitialized) return;
    window.signupThemeToggleInitialized = true;

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
      if (!themeToggle) return;
      const savedTheme = localStorage.getItem('themeMode');
      updateTheme(savedTheme === 'light' ? 'light' : 'dark');
      themeToggle.addEventListener('click', function() {
        const nextMode = document.body.classList.contains('light-mode') ? 'dark' : 'light';
        updateTheme(nextMode);
      });
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initThemeToggle);
    } else {
      initThemeToggle();
    }
  })();