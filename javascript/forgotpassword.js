/* ════════════════════════════════
   THEME TOGGLE
════════════════════════════════ */
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

/* ════════════════════════════════
   TOAST
════════════════════════════════ */
function showToast(msg, type = 'error') {
  const t = document.getElementById('toast');
  const tm = document.getElementById('toastMsg');
  const ti = document.getElementById('toastIcon');
  tm.textContent = msg;
  t.className = 'toast' + (type === 'success' ? ' success' : '');
  const c = type === 'success' ? '#3DBE7A' : '#E8341A';
  ti.innerHTML = type === 'success'
    ? `<circle cx="7.5" cy="7.5" r="6" stroke="${c}" stroke-width="1.3"/><path d="M5 7.5l2 2 3.5-3.5" stroke="${c}" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>`
    : `<circle cx="7.5" cy="7.5" r="6" stroke="${c}" stroke-width="1.3"/><path d="M7.5 5v3" stroke="${c}" stroke-width="1.5" stroke-linecap="round"/><circle cx="7.5" cy="10" r="0.7" fill="${c}"/>`;
  void t.offsetWidth;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3600);
}

/* ════════════════════════════════
   STAGE NAVIGATION
════════════════════════════════ */
let userEmail = '';
let timerInterval = null;

function goStage(n) {
  document.querySelectorAll('.stage').forEach((s, i) => {
    s.classList.toggle('active', i + 1 === n);
  });
  updateDots(n);
  document.querySelector('.form-side').scrollTo({ top: 0, behavior: 'smooth' });
}

function updateDots(active) {
  [1, 2, 3].forEach(i => {
    const dot = document.getElementById('dot' + i);
    dot.className = 'step-dot';
    if (i < active)  dot.classList.add('done');
    else if (i === active) dot.classList.add('active');
    else dot.classList.add('idle');
  });
}

/* ════════════════════════════════
   STAGE 1 — Send Code
════════════════════════════════ */
function sendCode() {
  const email = document.getElementById('emailInput');
  const grp   = document.getElementById('grp-email');
  const btn   = document.getElementById('sendCodeBtn');

  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
    grp.classList.add('has-error');
    email.classList.add('invalid');
    showToast('Please enter a valid email address.');
    return;
  }
  grp.classList.remove('has-error');
  email.classList.remove('invalid');
  email.classList.add('valid');

  userEmail = email.value.trim();
  btn.classList.add('loading');

  setTimeout(() => {
    btn.classList.remove('loading');
    document.getElementById('sentToEmail').textContent = maskEmail(userEmail);
    document.getElementById('emailDisplay').textContent = maskEmail(userEmail);
    goStage(2);
    startTimer(300); // 5 minutes
    showToast('Code sent! Check your inbox.', 'success');
    // Auto-focus first OTP input
    setTimeout(() => document.querySelector('.otp-input').focus(), 300);
  }, 1800);
}

function maskEmail(email) {
  const [user, domain] = email.split('@');
  const masked = user.slice(0, 2) + '***' + user.slice(-1);
  return masked + '@' + domain;
}

/* ════════════════════════════════
   COUNTDOWN TIMER
════════════════════════════════ */
function startTimer(seconds) {
  clearInterval(timerInterval);
  const total = seconds;
  const circle = document.getElementById('timerCircle');
  const circumference = 87.96;
  const resendBtn = document.getElementById('resendBtn');
  resendBtn.disabled = true;

  let remaining = seconds;
  updateTimerDisplay(remaining, total, circle, circumference);

  timerInterval = setInterval(() => {
    remaining--;
    updateTimerDisplay(remaining, total, circle, circumference);
    if (remaining <= 0) {
      clearInterval(timerInterval);
      resendBtn.disabled = false;
      document.getElementById('timerNum').textContent = '0:00';
      circle.style.strokeDashoffset = circumference;
    }
  }, 1000);
}

function updateTimerDisplay(remaining, total, circle, circ) {
  const m = Math.floor(remaining / 60);
  const s = remaining % 60;
  document.getElementById('timerNum').textContent = `${m}:${s.toString().padStart(2, '0')}`;
  const progress = remaining / total;
  circle.style.strokeDashoffset = circ * (1 - progress);
}

function resendCode() {
  showToast('New code sent to ' + maskEmail(userEmail) + '!', 'success');
  document.querySelectorAll('.otp-input').forEach(i => { i.value = ''; i.classList.remove('filled', 'invalid'); });
  document.querySelector('.otp-input').focus();
  document.getElementById('resendBtn').disabled = true;
  startTimer(300);
}

/* ════════════════════════════════
   OTP INPUTS
════════════════════════════════ */
const otpInputs = document.querySelectorAll('.otp-input');

otpInputs.forEach((input, index) => {
  // Only allow digits
  input.addEventListener('keydown', (e) => {
    if (!/^[0-9]$/.test(e.key) && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes(e.key)) {
      e.preventDefault();
    }
  });

  input.addEventListener('input', (e) => {
    const val = e.target.value.replace(/\D/g, '');
    e.target.value = val ? val[0] : '';

    if (val) {
      e.target.classList.add('filled');
      e.target.classList.remove('invalid');
      // Move to next
      if (index < otpInputs.length - 1) {
        otpInputs[index + 1].focus();
      }
    } else {
      e.target.classList.remove('filled');
    }
    checkOTPComplete();
  });

  input.addEventListener('keydown', (e) => {
    if (e.key === 'Backspace' && !e.target.value && index > 0) {
      otpInputs[index - 1].focus();
      otpInputs[index - 1].value = '';
      otpInputs[index - 1].classList.remove('filled');
    }
    if (e.key === 'ArrowLeft' && index > 0) otpInputs[index - 1].focus();
    if (e.key === 'ArrowRight' && index < otpInputs.length - 1) otpInputs[index + 1].focus();
  });

  // Handle paste
  input.addEventListener('paste', (e) => {
    e.preventDefault();
    const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
    if (!pasted) return;
    otpInputs.forEach((inp, i) => {
      if (pasted[i]) {
        inp.value = pasted[i];
        inp.classList.add('filled');
      }
    });
    const last = Math.min(pasted.length - 1, otpInputs.length - 1);
    otpInputs[last].focus();
    checkOTPComplete();
  });
});

function checkOTPComplete() {
  const full = [...otpInputs].every(i => i.value.length === 1);
  const hint = document.getElementById('otpHint');
  hint.textContent = full ? 'All digits entered. Click verify to continue.' : 'Enter all 6 digits to continue.';
  hint.style.color = full ? 'var(--green)' : 'var(--muted)';
}

function getOTPValue() {
  return [...otpInputs].map(i => i.value).join('');
}

/* ════════════════════════════════
   STAGE 2 — Verify OTP
════════════════════════════════ */
function verifyOTP() {
  const otp = getOTPValue();
  const btn = document.getElementById('verifyBtn');

  if (otp.length < 6) {
    otpInputs.forEach(i => { if (!i.value) i.classList.add('invalid'); });
    showToast('Please enter all 6 digits.');
    return;
  }

  btn.classList.add('loading');

  setTimeout(() => {
    btn.classList.remove('loading');
    clearInterval(timerInterval);
    // For demo purposes, any 6-digit code works
    goStage(3);
    showToast('Identity verified! Set your new password.', 'success');
  }, 1600);
}

/* ════════════════════════════════
   PASSWORD STRENGTH
════════════════════════════════ */
document.getElementById('newPass').addEventListener('input', function() {
  const v = this.value;
  const len     = v.length >= 8;
  const upper   = /[A-Z]/.test(v);
  const num     = /[0-9]/.test(v);
  const special = /[^a-zA-Z0-9]/.test(v);
  const score   = [len, upper, num, special].filter(Boolean).length;

  updateReq('len',     len);
  updateReq('upper',   upper);
  updateReq('num',     num);
  updateReq('special', special);

  const bars = ['sb1','sb2','sb3','sb4'];
  bars.forEach((id, i) => {
    const el = document.getElementById(id);
    el.className = 'sbar';
    if (i < score) el.classList.add(score <= 1 ? 'weak' : score <= 3 ? 'fair' : 'strong');
  });

  const labels = ['—','Weak','Fair','Good','Strong'];
  const colors = ['var(--muted)','var(--red)','var(--gold)','var(--gold)','var(--green)'];
  const st = document.getElementById('strengthText');
  st.textContent = v.length ? labels[score] : '—';
  st.style.color  = v.length ? colors[score] : 'var(--muted)';

  if (this.classList.contains('invalid') && v.length >= 8) {
    document.getElementById('grp-newpass').classList.remove('has-error');
    this.classList.remove('invalid'); this.classList.add('valid');
  }
});

function updateReq(id, met) {
  const el   = document.getElementById('req-' + id);
  const icon = document.getElementById('ri-' + id);
  el.classList.toggle('met', met);
  icon.innerHTML = met
    ? `<circle cx="6.5" cy="6.5" r="5.5" fill="rgba(61,190,122,0.15)" stroke="#3DBE7A" stroke-width="1.2"/><path d="M4 6.5l2 2 3.5-3.5" stroke="#3DBE7A" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>`
    : `<circle cx="6.5" cy="6.5" r="5.5" stroke="#6A6E75" stroke-width="1.2"/>`;
}

/* ════════════════════════════════
   STAGE 3 — Reset Password
════════════════════════════════ */
function resetPassword() {
  const np  = document.getElementById('newPass');
  const cp  = document.getElementById('confirmPass');
  const btn = document.getElementById('resetBtn');
  let ok = true;

  if (np.value.length < 8) {
    document.getElementById('grp-newpass').classList.add('has-error');
    np.classList.add('invalid'); ok = false;
  } else {
    document.getElementById('grp-newpass').classList.remove('has-error');
    np.classList.remove('invalid'); np.classList.add('valid');
  }

  if (cp.value !== np.value || !cp.value) {
    document.getElementById('grp-confirm').classList.add('has-error');
    cp.classList.add('invalid'); ok = false;
  } else {
    document.getElementById('grp-confirm').classList.remove('has-error');
    cp.classList.remove('invalid'); cp.classList.add('valid');
  }

  if (!ok) { showToast('Please fix the errors above.'); return; }

  btn.classList.add('loading');
  setTimeout(() => {
    btn.classList.remove('loading');
    // Hide all stages, show success
    document.querySelectorAll('.stage').forEach(s => s.style.display = 'none');
    document.getElementById('stepDots').style.display = 'none';
    document.querySelector('.back-link').style.display = 'none';
    document.getElementById('successBox').classList.add('show');
    showToast('Password reset successfully!', 'success');
  }, 2000);
}

/* ════════════════════════════════
   EYE TOGGLES
════════════════════════════════ */
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
makeEyeToggle('eyeNew', 'newPass');
makeEyeToggle('eyeConfirm', 'confirmPass');

/* ════════════════════════════════
   LIVE VALIDATION
════════════════════════════════ */
document.getElementById('emailInput').addEventListener('input', function() {
  const grp = document.getElementById('grp-email');
  if (grp.classList.contains('has-error') && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value)) {
    grp.classList.remove('has-error');
    this.classList.remove('invalid'); this.classList.add('valid');
  }
});
document.getElementById('confirmPass').addEventListener('input', function() {
  const np = document.getElementById('newPass').value;
  if (this.value === np && this.classList.contains('invalid')) {
    document.getElementById('grp-confirm').classList.remove('has-error');
    this.classList.remove('invalid'); this.classList.add('valid');
  }
});

// Allow Enter key on email field
document.getElementById('emailInput').addEventListener('keydown', e => {
  if (e.key === 'Enter') sendCode();
});