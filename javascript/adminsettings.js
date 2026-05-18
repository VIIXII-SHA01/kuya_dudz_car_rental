/* ════ SECTION NAV ════ */
function showSection(id, el) {
  document.querySelectorAll('.settings-section').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.snav-item').forEach(i => i.classList.remove('active'));
  document.getElementById('sec-' + id).classList.add('active');
  el.classList.add('active');
}

/* ════ UNSAVED STATE ════ */
let hasUnsaved = false;
function markUnsaved() {
  hasUnsaved = true;
  const dot = document.getElementById('unsavedDot');
  const msg = document.getElementById('saveBarMsg');
  if (dot) { dot.classList.add('show'); }
  if (msg) { msg.textContent = 'Unsaved changes'; msg.style.color = 'var(--gold)'; }
  const dot2 = document.getElementById('unsavedDot2');
  const msg2 = document.getElementById('saveBarMsg2');
  if (dot2) { dot2.classList.add('show'); }
  if (msg2) { msg2.textContent = 'Unsaved changes'; msg2.style.color = 'var(--gold)'; }
}
function saveChanges() {
  hasUnsaved = false;
  const dot = document.getElementById('unsavedDot');
  const msg = document.getElementById('saveBarMsg');
  if (dot) { dot.classList.remove('show'); }
  if (msg) { msg.textContent = 'All changes saved'; msg.style.color = 'var(--muted)'; }
  showToast('Profile updated successfully', 'success');
}
function discardChanges() {
  hasUnsaved = false;
  const dot = document.getElementById('unsavedDot');
  const msg = document.getElementById('saveBarMsg');
  if (dot) { dot.classList.remove('show'); }
  if (msg) { msg.textContent = 'All changes saved'; msg.style.color = 'var(--muted)'; }
  showToast('Changes discarded');
}
function saveChanges2() {
  const dot2 = document.getElementById('unsavedDot2');
  const msg2 = document.getElementById('saveBarMsg2');
  if (dot2) { dot2.classList.remove('show'); }
  if (msg2) { msg2.textContent = 'All changes saved'; msg2.style.color = 'var(--muted)'; }
  showToast('Business info saved', 'success');
}
function discardChanges2() {
  const dot2 = document.getElementById('unsavedDot2');
  const msg2 = document.getElementById('saveBarMsg2');
  if (dot2) { dot2.classList.remove('show'); }
  if (msg2) { msg2.textContent = 'All changes saved'; msg2.style.color = 'var(--muted)'; }
  showToast('Changes discarded');
}

/* ════ TOAST ════ */
function showToast(msg, type) {
  const t   = document.getElementById('toast');
  const tm  = document.getElementById('toastMsg');
  const ti  = document.getElementById('toastIcon');
  tm.textContent = msg;
  const colors = { success: 'var(--green)', error: 'var(--red)', info: 'var(--blue)' };
  const c = colors[type] || 'var(--green)';
  t.style.borderLeftColor = c;
  if (type === 'error') {
    ti.innerHTML = `<circle cx="7.5" cy="7.5" r="6" stroke="${c}" stroke-width="1.3"/><path d="M7.5 5v3" stroke="${c}" stroke-width="1.5" stroke-linecap="round"/><circle cx="7.5" cy="10" r="0.7" fill="${c}"/>`;
  } else if (type === 'info') {
    ti.innerHTML = `<circle cx="7.5" cy="7.5" r="6" stroke="${c}" stroke-width="1.3"/><path d="M7.5 7v4" stroke="${c}" stroke-width="1.5" stroke-linecap="round"/><circle cx="7.5" cy="5.5" r="0.7" fill="${c}"/>`;
  } else {
    ti.innerHTML = `<circle cx="7.5" cy="7.5" r="6" stroke="${c}" stroke-width="1.3"/><path d="M5 7.5l2 2 3.5-3.5" stroke="${c}" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>`;
  }
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3200);
}