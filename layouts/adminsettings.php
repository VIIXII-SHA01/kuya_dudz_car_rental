<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>REVV — Settings</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:ital,wght@0,300;0,400;0,500;0,600;1,300&family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/rent/css/adminsettings.css">
<link rel="stylesheet" href="/rent/css/theme.css">
</head>
<body>
<?php
$profile = [
  'first_name' => $_SESSION['user']['first_name'] ?? 'Jayne',
  'last_name' => $_SESSION['user']['last_name'] ?? 'Gonzales',
  'email' => $_SESSION['user']['email'] ?? 'jayne.gonzales@revvrentals.ph',
  'phone' => '',
  'role' => ucfirst($_SESSION['user']['role'] ?? 'Administrator'),
  'employee_id' => '',
  'bio' => '',
];
$avatarInitials = strtoupper(substr($profile['first_name'], 0, 1) . substr($profile['last_name'], 0, 1));
$userId = $_SESSION['user']['id'] ?? null;
if ($userId) {
    require_once __DIR__ . '/../databases/connection1.php';
    try {
        $columns = array_column($conn->query('SHOW COLUMNS FROM users')->fetchAll(PDO::FETCH_ASSOC), 'Field');
        $selectFields = ['first_name', 'last_name', 'email', 'role'];
        if (in_array('mobile', $columns, true)) {
            $selectFields[] = 'mobile';
        }
        if (in_array('phone', $columns, true)) {
            $selectFields[] = 'phone';
        }
        if (in_array('notes', $columns, true)) {
            $selectFields[] = 'notes';
        }
        if (in_array('username', $columns, true)) {
            $selectFields[] = 'username';
        }
        $stmt = $conn->prepare('SELECT ' . implode(', ', $selectFields) . ' FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $profile['first_name'] = $row['first_name'] ?: $profile['first_name'];
            $profile['last_name'] = $row['last_name'] ?: $profile['last_name'];
            $profile['email'] = $row['email'] ?: $profile['email'];
            $profile['role'] = ucfirst($row['role'] ?? $profile['role']);
            if (!empty($row['mobile'])) {
                $profile['phone'] = $row['mobile'];
            } elseif (!empty($row['phone'])) {
                $profile['phone'] = $row['phone'];
            }
            if (!empty($row['notes'])) {
                $profile['bio'] = $row['notes'];
            }
            if (!empty($row['username'])) {
                $profile['employee_id'] = $row['username'];
            }
            $avatarInitials = strtoupper(substr($profile['first_name'], 0, 1) . substr($profile['last_name'], 0, 1));
        }
    } catch (PDOException $e) {
        // ignore optional user details if schema differs
    }
}
if ($profile['employee_id'] === '') {
    $profile['employee_id'] = $userId ? sprintf('EMP-%03d', $userId) : 'EMP-001';
}

$defaultRates = [
    'rate_sedan' => 1800,
    'rate_hatchback' => 1500,
    'rate_suv' => 4500,
    'rate_premium_suv' => 9500,
    'rate_van' => 5500,
    'rate_pickup' => 4200,
    'addon_driver_surcharge' => 800,
    'addon_late_fee' => 350,
    'addon_security_deposit' => 5000,
];

$defaultNotifications = [
    'new_rental_created' => true,
    'rental_due_today' => true,
    'overdue_rental_alert' => true,
    'rental_cancelled' => false,
    'daily_summary_email' => true,
    'weekly_report_email' => true,
    'new_staff_account' => false,
];

$rates = $defaultRates;
$notifications = $defaultNotifications;
if ($userId) {
    try {
        $conn->exec(
            'CREATE TABLE IF NOT EXISTS settings (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(150) NOT NULL UNIQUE,
                setting_value TEXT DEFAULT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );

        $stmt = $conn->query('SELECT setting_key, setting_value FROM settings');
        $storedSettings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stored = [];
        foreach ($storedSettings as $row) {
            $stored[$row['setting_key']] = $row['setting_value'];
        }

        foreach ($defaultRates as $key => $default) {
            if (isset($stored[$key]) && is_numeric($stored[$key])) {
                $rates[$key] = $stored[$key];
            }
        }

        foreach ($defaultNotifications as $key => $default) {
            $storageKey = 'user_' . $userId . '_notif_' . $key;
            if (isset($stored[$storageKey])) {
                $notifications[$key] = in_array(strtolower($stored[$storageKey]), ['1', 'true', 'yes'], true);
            }
        }
    } catch (PDOException $e) {
        // ignore settings load failures
    }
}
?>
<div class="app">

  <!-- ══ SIDEBAR ══ -->
  <?php include("../navs/adminnavs.php"); ?>

  <!-- Sidebar Overlay -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- ══ MAIN ══ -->
  <div class="main">
    <header class="topbar">
      <!-- Hamburger — shown only when sidebar is collapsed (via CSS) -->
      <button class="menu-toggle" id="menuToggle" aria-label="Toggle navigation">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" color="white">
          <path d="M3 5h12M3 9h12M3 13h12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
      </button>

      <div class="topbar-title">Settings</div>
      <div class="topbar-divider"></div>
      <div style="font-size:13px;color:var(--muted2)">System preferences &amp; account management</div>
      <div class="topbar-right">
        <div class="topbar-date">
          <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><rect x="1.5" y="2.5" width="10" height="9" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M4 2.5V1M9 2.5V1M1.5 5.5h10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
          Sat, 12 April 2026
        </div>
        <button id="themeToggle" class="icon-btn theme-toggle" type="button" aria-label="Toggle theme" title="Toggle theme">
          <span class="theme-toggle-icon">☀️</span>
        </button>
        <div class="icon-btn">
          <svg width="17" height="17" viewBox="0 0 17 17" fill="none" color="#9A9DA4"><path d="M8.5 2a5 5 0 0 1 5 5v3l1.5 2H2L3.5 10V7a5 5 0 0 1 5-5z" stroke="currentColor" stroke-width="1.4"/><path d="M7 13.5a1.5 1.5 0 0 0 3 0" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
          <div class="notif-dot"></div>
        </div>
        <div class="icon-btn">
          <div style="width:22px;height:22px;background:linear-gradient(135deg,var(--red),var(--orange));border-radius:2px;display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:11px;color:white">JG</div>
        </div>
      </div>
    </header>

    <div class="content">

      <div class="page-header">
        <div>
          <div class="page-eyebrow">Admin</div>
          <div class="page-title">Settings</div>
          <div class="page-sub">Manage your profile, system preferences, and staff</div>
        </div>
      </div>

      <div class="settings-layout">

        <!-- ══ SETTINGS NAV ══ -->
        <nav class="settings-nav">
          <div class="snav-section">
            <div class="snav-label">Account</div>
            <div class="snav-item active" onclick="showSection('profile',this)">
              <svg width="15" height="15" viewBox="0 0 15 15" fill="none" color="currentColor"><circle cx="7.5" cy="5" r="3" stroke="currentColor" stroke-width="1.3"/><path d="M2 13.5c0-3.04 2.46-5.5 5.5-5.5s5.5 2.46 5.5 5.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
              My Profile
            </div>
            <div class="snav-item" onclick="showSection('security',this)">
              <svg width="15" height="15" viewBox="0 0 15 15" fill="none" color="currentColor"><rect x="3" y="6.5" width="9" height="7" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 6.5V4.5a2.5 2.5 0 0 1 5 0v2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/><circle cx="7.5" cy="10" r="1" fill="currentColor"/></svg>
              Security
            </div>
          </div>
          <div class="snav-divider"></div>
          <div class="snav-section">
            <div class="snav-label">System</div>
            <div class="snav-item" onclick="showSection('pricing',this)">
              <svg width="15" height="15" viewBox="0 0 15 15" fill="none" color="currentColor"><circle cx="7.5" cy="7.5" r="6" stroke="currentColor" stroke-width="1.3"/><path d="M7.5 4v7M5.5 5.5h3a1 1 0 0 1 0 2h-2a1 1 0 0 0 0 2h3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
              Pricing & Rates
            </div>
            <div class="snav-item" onclick="showSection('notifications',this)">
              <svg width="15" height="15" viewBox="0 0 15 15" fill="none" color="currentColor"><path d="M7.5 1.5a4.5 4.5 0 0 1 4.5 4.5v3l1.5 2H2L3.5 9V6a4.5 4.5 0 0 1 4-4.48" stroke="currentColor" stroke-width="1.3"/><path d="M6 12.5a1.5 1.5 0 0 0 3 0" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
              Notifications
            </div>
          </div>
          <div class="snav-divider"></div>
          <div class="snav-section">
            <div class="snav-label">Admin</div>
            <div class="snav-item" onclick="showSection('staff',this)">
              <svg width="15" height="15" viewBox="0 0 15 15" fill="none" color="currentColor"><circle cx="6" cy="5" r="2.5" stroke="currentColor" stroke-width="1.3"/><path d="M1.5 13c0-2.49 2.01-4.5 4.5-4.5s4.5 2.01 4.5 4.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/><path d="M10.5 7.5l1.5 1.5 2.5-2.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
              Staff & Roles
            </div>
          </div>
        </nav>

        <!-- ══ PANELS ══ -->
        <div class="settings-panels">

          <!-- ── PROFILE ── -->
          <div class="settings-section active" id="sec-profile">
            <div class="setting-card">
              <div class="setting-card-head">
                <div>
                  <div class="setting-card-title">My Profile</div>
                  <div class="setting-card-sub">Update your personal information and display preferences</div>
                </div>
              </div>
              <div class="setting-card-body">
                <div class="profile-avatar-row">
                  <div class="profile-avatar-lg"><?= htmlspecialchars($avatarInitials, ENT_QUOTES, 'UTF-8') ?></div>
                  <div class="profile-avatar-info">
                    <div class="profile-avatar-name"><?= htmlspecialchars(trim($profile['first_name'] . ' ' . $profile['last_name']), ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="profile-avatar-role"><?= htmlspecialchars($profile['role'], ENT_QUOTES, 'UTF-8') ?> · REVV Car Rentals</div>
                    <div style="display:flex;gap:8px">
                      <button class="btn-ghost" style="padding:7px 14px;font-size:11px" onclick="showToast('Avatar upload coming soon','info')">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M6 1v7M3 4l3-3 3 3" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M1 9v1.5A1.5 1.5 0 0 0 2.5 12h7A1.5 1.5 0 0 0 11 10.5V9" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                        Upload Photo
                      </button>
                      <button class="btn-ghost" style="padding:7px 14px;font-size:11px;color:var(--red);border-color:rgba(232,52,26,0.25)" onclick="showToast('Photo removed','error')">Remove</button>
                    </div>
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group">
                    <label class="form-label">First Name</label>
                    <input id="profileFirstName" class="form-input" value="<?= htmlspecialchars($profile['first_name'], ENT_QUOTES, 'UTF-8') ?>" oninput="markUnsaved()">
                  </div>
                  <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <input id="profileLastName" class="form-input" value="<?= htmlspecialchars($profile['last_name'], ENT_QUOTES, 'UTF-8') ?>" oninput="markUnsaved()">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input id="profileEmail" class="form-input" type="email" value="<?= htmlspecialchars($profile['email'], ENT_QUOTES, 'UTF-8') ?>" oninput="markUnsaved()">
                  </div>
                  <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input id="profilePhone" class="form-input" type="tel" value="<?= htmlspecialchars($profile['phone'], ENT_QUOTES, 'UTF-8') ?>" oninput="markUnsaved()">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group">
                    <label class="form-label">Role</label>
                    <input id="profileRole" class="form-input" value="<?= htmlspecialchars($profile['role'], ENT_QUOTES, 'UTF-8') ?>" readonly>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Employee ID</label>
                    <input id="profileEmployeeId" class="form-input" value="<?= htmlspecialchars($profile['employee_id'], ENT_QUOTES, 'UTF-8') ?>" readonly>
                  </div>
                </div>
                <div class="form-row full">
                  <div class="form-group">
                    <label class="form-label">Bio / Notes</label>
                    <textarea id="profileBio" class="form-textarea" oninput="markUnsaved()"><?= htmlspecialchars($profile['bio'], ENT_QUOTES, 'UTF-8') ?></textarea>
                  </div>
                </div>
              </div>
              <div class="save-bar">
                <div class="save-bar-info">
                  <div class="unsaved-dot" id="unsavedDot"></div>
                  <span id="saveBarMsg" style="color:var(--muted)">All changes saved</span>
                </div>
                <div style="display:flex;gap:10px">
                  <button class="btn-ghost" onclick="discardChanges()">Discard</button>
                  <button class="btn-primary" onclick="saveChanges(this)">
                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="white"><path d="M2 6.5l3.5 3.5 5.5-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Save Changes</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- ── SECURITY ── -->
          <div class="settings-section" id="sec-security">
            <div class="setting-card">
              <div class="setting-card-head">
                <div>
                  <div class="setting-card-title">Change Password</div>
                  <div class="setting-card-sub">Use a strong password with 8+ characters</div>
                </div>
              </div>
              <div class="setting-card-body">
                <div class="form-row full">
                  <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <input id="currentPassword" class="form-input" type="password" placeholder="Enter current password">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input id="newPassword" class="form-input" type="password" placeholder="Min. 8 characters">
                  </div>
                  <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input id="confirmPassword" class="form-input" type="password" placeholder="Repeat new password">
                  </div>
                </div>
                <div style="display:flex;justify-content:flex-end;margin-top:4px">
                  <button class="btn-primary" onclick="updatePassword(this)">
                    <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="white"><rect x="3" y="6" width="7" height="6" rx="1" stroke="currentColor" stroke-width="1.3"/><path d="M5 6V4.5a2.5 2.5 0 0 1 3.5-2.3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                    <span>Update Password</span>
                  </button>
                </div>
              </div>
            </div>

            <div class="setting-card">
              <div class="setting-card-head">
                <div>
                  <div class="setting-card-title">Login & Security</div>
                  <div class="setting-card-sub">Control access and session preferences</div>
                </div>
              </div>
              <div class="setting-card-body">
                <div class="toggle-row">
                  <div class="toggle-info">
                    <div class="toggle-label">Two-Factor Authentication</div>
                    <div class="toggle-desc">Require a verification code at login via SMS or authenticator app</div>
                  </div>
                  <label class="toggle"><input type="checkbox" onchange="markUnsaved()"><span class="toggle-track"></span></label>
                </div>
                <div class="toggle-row">
                  <div class="toggle-info">
                    <div class="toggle-label">Login Notifications</div>
                    <div class="toggle-desc">Get an email alert whenever your account is accessed from a new device</div>
                  </div>
                  <label class="toggle"><input type="checkbox" checked onchange="markUnsaved()"><span class="toggle-track"></span></label>
                </div>
                <div class="toggle-row">
                  <div class="toggle-info">
                    <div class="toggle-label">Auto-Logout After Inactivity</div>
                    <div class="toggle-desc">Automatically sign out after 30 minutes of no activity</div>
                  </div>
                  <label class="toggle"><input type="checkbox" checked onchange="markUnsaved()"><span class="toggle-track"></span></label>
                </div>
              </div>
            </div>

            <div class="danger-zone">
              <div class="danger-zone-head">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" color="#E8341A"><path d="M8 2l6 11H2L8 2z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 7v3M8 11.5v.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                <div class="danger-zone-title">Danger Zone</div>
              </div>
              <div class="danger-zone-body">
                <div class="danger-row">
                  <div class="danger-info">
                    <div class="danger-label">Sign Out All Devices</div>
                    <div class="danger-desc">Log out all active sessions across every device immediately</div>
                  </div>
                  <button class="btn-danger" onclick="showToast('All sessions terminated','error')">Sign Out All</button>
                </div>
                <div style="height:1px;background:rgba(232,52,26,0.12)"></div>
                <div class="danger-row">
                  <div class="danger-info">
                    <div class="danger-label">Deactivate Account</div>
                    <div class="danger-desc">Permanently disable this admin account. This cannot be undone.</div>
                  </div>
                  <button class="btn-danger" onclick="showToast('Contact super-admin to deactivate accounts','error')">Deactivate</button>
                </div>
              </div>
            </div>
          </div>

          <!-- ── PRICING ── -->
          <div class="settings-section" id="sec-pricing">
            <div class="setting-card">
              <div class="setting-card-head">
                <div>
                  <div class="setting-card-title">Default Rate Card</div>
                  <div class="setting-card-sub">Base daily rates per vehicle type — can be overridden per rental</div>
                </div>
              </div>
              <div class="setting-card-body">
                <div class="section-div">Vehicle Type Rates (₱ per day)</div>
                <div class="form-row three">
                  <div class="form-group">
                    <label class="form-label">Sedan</label>
                    <input id="rateSedan" class="form-input" type="number" value="<?= htmlspecialchars($rates['rate_sedan'], ENT_QUOTES, 'UTF-8') ?>" oninput="markUnsaved()">
                    <span class="form-label-hint">e.g. Toyota Vios, Honda City</span>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Hatchback</label>
                    <input id="rateHatchback" class="form-input" type="number" value="<?= htmlspecialchars($rates['rate_hatchback'], ENT_QUOTES, 'UTF-8') ?>" oninput="markUnsaved()">
                    <span class="form-label-hint">e.g. Mitsubishi Mirage</span>
                  </div>
                  <div class="form-group">
                    <label class="form-label">SUV</label>
                    <input id="rateSUV" class="form-input" type="number" value="<?= htmlspecialchars($rates['rate_suv'], ENT_QUOTES, 'UTF-8') ?>" oninput="markUnsaved()">
                    <span class="form-label-hint">e.g. Ford Everest, Honda BR-V</span>
                  </div>
                </div>
                <div class="form-row three">
                  <div class="form-group">
                    <label class="form-label">Premium SUV</label>
                    <input id="ratePremiumSUV" class="form-input" type="number" value="<?= htmlspecialchars($rates['rate_premium_suv'], ENT_QUOTES, 'UTF-8') ?>" oninput="markUnsaved()">
                    <span class="form-label-hint">e.g. Mercedes GLE, Fortuner</span>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Van</label>
                    <input id="rateVan" class="form-input" type="number" value="<?= htmlspecialchars($rates['rate_van'], ENT_QUOTES, 'UTF-8') ?>" oninput="markUnsaved()">
                    <span class="form-label-hint">e.g. Toyota HiAce</span>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Pickup</label>
                    <input id="ratePickup" class="form-input" type="number" value="<?= htmlspecialchars($rates['rate_pickup'], ENT_QUOTES, 'UTF-8') ?>" oninput="markUnsaved()">
                    <span class="form-label-hint">e.g. Ford Ranger</span>
                  </div>
                </div>
                <div class="section-div" style="margin-top:8px">Add-ons & Fees</div>
                <div class="form-row three">
                  <div class="form-group">
                    <label class="form-label">Driver Surcharge (₱/day)</label>
                    <input id="addonDriverSurcharge" class="form-input" type="number" value="<?= htmlspecialchars($rates['addon_driver_surcharge'], ENT_QUOTES, 'UTF-8') ?>" oninput="markUnsaved()">
                  </div>
                  <div class="form-group">
                    <label class="form-label">Late Return Fee (₱/hr)</label>
                    <input id="addonLateFee" class="form-input" type="number" value="<?= htmlspecialchars($rates['addon_late_fee'], ENT_QUOTES, 'UTF-8') ?>" oninput="markUnsaved()">
                  </div>
                  <div class="form-group">
                    <label class="form-label">Security Deposit (₱)</label>
                    <input id="addonSecurityDeposit" class="form-input" type="number" value="<?= htmlspecialchars($rates['addon_security_deposit'], ENT_QUOTES, 'UTF-8') ?>" oninput="markUnsaved()">
                  </div>
                </div>
              </div>
              <div class="save-bar">
                <div class="save-bar-info"><span style="color:var(--muted)">Rate changes apply to new rentals only</span></div>
                <div style="display:flex;gap:10px">
                  <button class="btn-ghost" onclick="discardChanges()">Discard</button>
                  <button class="btn-primary" onclick="saveRates(this)"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="white"><path d="M2 6.5l3.5 3.5 5.5-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Save Rates</span></button>
                </div>
              </div>
            </div>
          </div>

          <!-- ── NOTIFICATIONS ── -->
          <div class="settings-section" id="sec-notifications">
            <div class="setting-card">
              <div class="setting-card-head">
                <div>
                  <div class="setting-card-title">Notification Preferences</div>
                  <div class="setting-card-sub">Choose how and when you receive alerts</div>
                </div>
              </div>
              <div class="setting-card-body">
                <div class="section-div">Rental Alerts</div>
                <div class="toggle-row">
                  <div class="toggle-info">
                    <div class="toggle-label">New Rental Created</div>
                    <div class="toggle-desc">Get notified when a new rental is added to the system</div>
                  </div>
                  <label class="toggle"><input id="notifNewRentalCreated" type="checkbox" <?= $notifications['new_rental_created'] ? 'checked' : '' ?> onchange="markUnsaved()"><span class="toggle-track"></span></label>
                </div>
                <div class="toggle-row">
                  <div class="toggle-info">
                    <div class="toggle-label">Rental Due Today</div>
                    <div class="toggle-desc">Morning reminder for rentals scheduled to be returned today</div>
                  </div>
                  <label class="toggle"><input id="notifRentalDueToday" type="checkbox" <?= $notifications['rental_due_today'] ? 'checked' : '' ?> onchange="markUnsaved()"><span class="toggle-track"></span></label>
                </div>
                <div class="toggle-row">
                  <div class="toggle-info">
                    <div class="toggle-label">Overdue Rental Alert</div>
                    <div class="toggle-desc">Immediate alert when a rental becomes overdue past its return date</div>
                  </div>
                  <label class="toggle"><input id="notifOverdueRentalAlert" type="checkbox" <?= $notifications['overdue_rental_alert'] ? 'checked' : '' ?> onchange="markUnsaved()"><span class="toggle-track"></span></label>
                </div>
                <div class="toggle-row">
                  <div class="toggle-info">
                    <div class="toggle-label">Rental Cancelled</div>
                    <div class="toggle-desc">Notify when a customer or admin cancels a booking</div>
                  </div>
                  <label class="toggle"><input id="notifRentalCancelled" type="checkbox" <?= $notifications['rental_cancelled'] ? 'checked' : '' ?> onchange="markUnsaved()"><span class="toggle-track"></span></label>
                </div>

                <div class="section-div" style="margin-top:8px">System Alerts</div>
                <div class="toggle-row">
                  <div class="toggle-info">
                    <div class="toggle-label">Daily Summary Email</div>
                    <div class="toggle-desc">Receive a 6AM digest of today's rentals, returns, and key metrics</div>
                  </div>
                  <label class="toggle"><input id="notifDailySummaryEmail" type="checkbox" <?= $notifications['daily_summary_email'] ? 'checked' : '' ?> onchange="markUnsaved()"><span class="toggle-track"></span></label>
                </div>
                <div class="toggle-row">
                  <div class="toggle-info">
                    <div class="toggle-label">Weekly Report Email</div>
                    <div class="toggle-desc">Send an auto-generated weekly performance report every Monday</div>
                  </div>
                  <label class="toggle"><input id="notifWeeklyReportEmail" type="checkbox" <?= $notifications['weekly_report_email'] ? 'checked' : '' ?> onchange="markUnsaved()"><span class="toggle-track"></span></label>
                </div>
                <div class="toggle-row">
                  <div class="toggle-info">
                    <div class="toggle-label">New Staff Account</div>
                    <div class="toggle-desc">Alert when a new user account is created or role is changed</div>
                  </div>
                  <label class="toggle"><input id="notifNewStaffAccount" type="checkbox" <?= $notifications['new_staff_account'] ? 'checked' : '' ?> onchange="markUnsaved()"><span class="toggle-track"></span></label>
                </div>
              </div>
              <div class="save-bar">
                <div class="save-bar-info"><span style="color:var(--muted)">Notifications sent to <?= htmlspecialchars($profile['email'], ENT_QUOTES, 'UTF-8') ?></span></div>
                <button class="btn-primary" onclick="saveNotificationPreferences(this)"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="white"><path d="M2 6.5l3.5 3.5 5.5-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Save Preferences</span></button>
              </div>
            </div>
          </div>

          <!-- ── STAFF & ROLES ── -->
          <div class="settings-section" id="sec-staff">
            <div class="setting-card">
              <div class="setting-card-head">
                <div>
                  <div class="setting-card-title">Staff & Roles</div>
                  <div class="setting-card-sub">Manage system users and their access levels</div>
                </div>
                <button class="btn-primary" onclick="showToast('Invite sent!','success')">
                  <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="white"><path d="M2 6.5h9M8 3.5l3 3-3 3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  <span>Invite User</span>
                </button>
              </div>
              <table class="users-table">
                <thead>
                  <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Branch</th>
                    <th>Status</th>
                    <th>Last Active</th>
                    <th style="text-align:center">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <div style="display:flex;align-items:center;gap:10px">
                        <div class="u-avatar" style="background:linear-gradient(135deg,var(--red),var(--orange))">JG</div>
                        <div><div class="u-name">Jayne Gonzales</div><div class="u-email">jayne.gonzales@revvrentals.ph</div></div>
                      </div>
                    </td>
                    <td><span class="role-badge role-admin">Admin</span></td>
                    <td style="color:var(--muted2);font-size:13px">All Branches</td>
                    <td><span class="status-dot"><span class="sdot online"></span><span style="color:var(--green)">Online</span></span></td>
                    <td style="color:var(--muted2);font-size:12px">Now</td>
                    <td><div style="display:flex;justify-content:center;gap:6px"><div class="act-btn" onclick="showToast('Cannot edit own account here')"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M1.5 9L8 2.5l1.5 1.5-6.5 6.5H1.5V9z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div></div></td>
                  </tr>
                  <tr>
                    <td>
                      <div style="display:flex;align-items:center;gap:10px">
                        <div class="u-avatar" style="background:linear-gradient(135deg,#3D8FBE,#3DBE7A)">RC</div>
                        <div><div class="u-name">Ramon Cruz</div><div class="u-email">r.cruz@revvrentals.ph</div></div>
                      </div>
                    </td>
                    <td><span class="role-badge role-manager">Manager</span></td>
                    <td style="color:var(--muted2);font-size:13px">Cebu City</td>
                    <td><span class="status-dot"><span class="sdot online"></span><span style="color:var(--green)">Online</span></span></td>
                    <td style="color:var(--muted2);font-size:12px">2 hrs ago</td>
                    <td><div style="display:flex;justify-content:center;gap:6px"><div class="act-btn" onclick="showToast('Edit user')"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M1.5 9L8 2.5l1.5 1.5-6.5 6.5H1.5V9z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div><div class="act-btn del" onclick="showToast('User removed','error')"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M2 3h8M4.5 3V2h3v1M9 3l-.6 7H3.6L3 3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div></div></td>
                  </tr>
                  <tr>
                    <td>
                      <div style="display:flex;align-items:center;gap:10px">
                        <div class="u-avatar" style="background:linear-gradient(135deg,#D4A843,#F5642A)">AL</div>
                        <div><div class="u-name">Anna Lacson</div><div class="u-email">a.lacson@revvrentals.ph</div></div>
                      </div>
                    </td>
                    <td><span class="role-badge role-staff">Staff</span></td>
                    <td style="color:var(--muted2);font-size:13px">Mactan Airport</td>
                    <td><span class="status-dot"><span class="sdot offline"></span><span style="color:var(--muted2)">Offline</span></span></td>
                    <td style="color:var(--muted2);font-size:12px">Yesterday</td>
                    <td><div style="display:flex;justify-content:center;gap:6px"><div class="act-btn" onclick="showToast('Edit user')"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M1.5 9L8 2.5l1.5 1.5-6.5 6.5H1.5V9z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div><div class="act-btn del" onclick="showToast('User removed','error')"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M2 3h8M4.5 3V2h3v1M9 3l-.6 7H3.6L3 3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div></div></td>
                  </tr>
                  <tr>
                    <td>
                      <div style="display:flex;align-items:center;gap:10px">
                        <div class="u-avatar" style="background:linear-gradient(135deg,#9A3DBE,#3D8FBE)">MR</div>
                        <div><div class="u-name">Mark Reyes</div><div class="u-email">m.reyes@revvrentals.ph</div></div>
                      </div>
                    </td>
                    <td><span class="role-badge role-staff">Staff</span></td>
                    <td style="color:var(--muted2);font-size:13px">Talisay</td>
                    <td><span class="status-dot"><span class="sdot offline"></span><span style="color:var(--muted2)">Offline</span></span></td>
                    <td style="color:var(--muted2);font-size:12px">Apr 10</td>
                    <td><div style="display:flex;justify-content:center;gap:6px"><div class="act-btn" onclick="showToast('Edit user')"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M1.5 9L8 2.5l1.5 1.5-6.5 6.5H1.5V9z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div><div class="act-btn del" onclick="showToast('User removed','error')"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M2 3h8M4.5 3V2h3v1M9 3l-.6 7H3.6L3 3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div></div></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>


        </div>
      </div>

    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast">
  <svg width="15" height="15" viewBox="0 0 15 15" fill="none" id="toastIcon">
    <circle cx="7.5" cy="7.5" r="6" stroke="var(--green)" stroke-width="1.3"/>
    <path d="M5 7.5l2 2 3.5-3.5" stroke="var(--green)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>
  <span id="toastMsg">Changes saved</span>
</div>

<script src="/rent/javascript/adminsettings.js"></script>
<script src="/rent/javascript/admindashboard.js"></script>
</body>
</html>