const SETTINGS_API = '/rent/php/settings_action.php';

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
}

async function saveChanges(button) {
  const firstName = document.getElementById('profileFirstName').value.trim();
  const lastName = document.getElementById('profileLastName').value.trim();
  const email = document.getElementById('profileEmail').value.trim();
  const phone = document.getElementById('profilePhone').value.trim();
  const bio = document.getElementById('profileBio').value.trim();

  if (!firstName || !lastName || !email) {
    showToast('First name, last name, and email are required.', 'error');
    return;
  }

  if (button) { button.disabled = true; }

  const formData = new FormData();
  formData.append('action', 'save_profile');
  formData.append('first_name', firstName);
  formData.append('last_name', lastName);
  formData.append('email', email);
  formData.append('phone', phone);
  formData.append('bio', bio);

  try {
    const response = await fetch(SETTINGS_API, {
      method: 'POST',
      body: formData,
    });
    const data = await response.json();

    if (!response.ok || data.error) {
      showToast(data.error || 'Unable to save profile.', 'error');
      return;
    }

    hasUnsaved = false;
    const dot = document.getElementById('unsavedDot');
    const msg = document.getElementById('saveBarMsg');
    if (dot) { dot.classList.remove('show'); }
    if (msg) { msg.textContent = 'All changes saved'; msg.style.color = 'var(--muted)'; }
    showToast('Profile updated successfully.', 'success');

    if (data.user && typeof data.user.first_name === 'string' && typeof data.user.last_name === 'string') {
      const fullName = `${data.user.first_name} ${data.user.last_name}`;
      const nameElement = document.querySelector('.profile-avatar-name');
      if (nameElement) { nameElement.textContent = fullName; }
    }
  } catch {
    showToast('Unable to save profile.', 'error');
  } finally {
    if (button) { button.disabled = false; }
  }
}

async function saveRates(button) {
  const rateSedan = document.getElementById('rateSedan').value.trim();
  const rateHatchback = document.getElementById('rateHatchback').value.trim();
  const rateSUV = document.getElementById('rateSUV').value.trim();
  const ratePremiumSUV = document.getElementById('ratePremiumSUV').value.trim();
  const rateVan = document.getElementById('rateVan').value.trim();
  const ratePickup = document.getElementById('ratePickup').value.trim();
  const addonDriverSurcharge = document.getElementById('addonDriverSurcharge').value.trim();
  const addonLateFee = document.getElementById('addonLateFee').value.trim();
  const addonSecurityDeposit = document.getElementById('addonSecurityDeposit').value.trim();

  if (button) { button.disabled = true; }

  const formData = new FormData();
  formData.append('action', 'save_rates');
  formData.append('rate_sedan', rateSedan);
  formData.append('rate_hatchback', rateHatchback);
  formData.append('rate_suv', rateSUV);
  formData.append('rate_premium_suv', ratePremiumSUV);
  formData.append('rate_van', rateVan);
  formData.append('rate_pickup', ratePickup);
  formData.append('addon_driver_surcharge', addonDriverSurcharge);
  formData.append('addon_late_fee', addonLateFee);
  formData.append('addon_security_deposit', addonSecurityDeposit);

  try {
    const response = await fetch(SETTINGS_API, {
      method: 'POST',
      body: formData,
    });
    const data = await response.json();

    if (!response.ok || data.error) {
      showToast(data.error || 'Unable to save rates.', 'error');
      return;
    }

    showToast('Rate card saved successfully.', 'success');
  } catch {
    showToast('Unable to save rates.', 'error');
  } finally {
    if (button) { button.disabled = false; }
  }
}

async function saveNotificationPreferences(button) {
  const prefs = {
    new_rental_created: document.getElementById('notifNewRentalCreated').checked,
    rental_due_today: document.getElementById('notifRentalDueToday').checked,
    overdue_rental_alert: document.getElementById('notifOverdueRentalAlert').checked,
    rental_cancelled: document.getElementById('notifRentalCancelled').checked,
    daily_summary_email: document.getElementById('notifDailySummaryEmail').checked,
    weekly_report_email: document.getElementById('notifWeeklyReportEmail').checked,
    new_staff_account: document.getElementById('notifNewStaffAccount').checked,
  };

  if (button) { button.disabled = true; }

  const formData = new FormData();
  formData.append('action', 'save_notification_preferences');
  Object.keys(prefs).forEach(key => {
    formData.append(key, prefs[key] ? '1' : '0');
  });

  try {
    const response = await fetch(SETTINGS_API, {
      method: 'POST',
      body: formData,
    });
    const data = await response.json();

    if (!response.ok || data.error) {
      showToast(data.error || 'Unable to save notification preferences.', 'error');
      return;
    }

    showToast('Notification preferences saved successfully.', 'success');
  } catch {
    showToast('Unable to save notification preferences.', 'error');
  } finally {
    if (button) { button.disabled = false; }
  }
}

async function updatePassword(button) {
  const currentPassword = document.getElementById('currentPassword').value.trim();
  const newPassword = document.getElementById('newPassword').value.trim();
  const confirmPassword = document.getElementById('confirmPassword').value.trim();

  if (!currentPassword || !newPassword || !confirmPassword) {
    showToast('All password fields are required.', 'error');
    return;
  }

  if (newPassword.length < 8) {
    showToast('New password must be at least 8 characters.', 'error');
    return;
  }

  if (newPassword !== confirmPassword) {
    showToast('New passwords do not match.', 'error');
    return;
  }

  if (button) { button.disabled = true; }

  const formData = new FormData();
  formData.append('action', 'change_password');
  formData.append('current_password', currentPassword);
  formData.append('new_password', newPassword);
  formData.append('confirm_password', confirmPassword);

  try {
    const response = await fetch(SETTINGS_API, {
      method: 'POST',
      body: formData,
    });
    const data = await response.json();

    if (!response.ok || data.error) {
      showToast(data.error || 'Unable to update password.', 'error');
      return;
    }

    document.getElementById('currentPassword').value = '';
    document.getElementById('newPassword').value = '';
    document.getElementById('confirmPassword').value = '';
    showToast('Password changed successfully.', 'success');
  } catch {
    showToast('Unable to update password.', 'error');
  } finally {
    if (button) { button.disabled = false; }
  }
}

function discardChanges() {
  hasUnsaved = false;
  const dot = document.getElementById('unsavedDot');
  const msg = document.getElementById('saveBarMsg');
  if (dot) { dot.classList.remove('show'); }
  if (msg) { msg.textContent = 'All changes saved'; msg.style.color = 'var(--muted)'; }
  window.location.reload();
}

/* ════ TOAST ════ */
function showToast(msg, type) {
  const t = document.getElementById('toast');
  const tm = document.getElementById('toastMsg');
  const ti = document.getElementById('toastIcon');
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
