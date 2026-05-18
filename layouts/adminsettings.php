<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>REVV — Settings</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:ital,wght@0,300;0,400;0,500;0,600;1,300&family=Barlow+Condensed:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/adminsettings.css">
</head>
<body>
<div class="app">

  <!-- ══ SIDEBAR ══ -->
  <?php include("../navs/adminnavs.php"); ?>

  <!-- ══ MAIN ══ -->
  <div class="main">
    <header class="topbar">
      <div class="topbar-title">Settings</div>
      <div class="topbar-divider"></div>
      <div style="font-size:13px;color:var(--muted2)">System preferences &amp; account management</div>
      <div class="topbar-right">
        <div class="topbar-date">
          <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><rect x="1.5" y="2.5" width="10" height="9" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M4 2.5V1M9 2.5V1M1.5 5.5h10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
          Sat, 12 April 2026
        </div>
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
          <div class="page-sub">Manage your profile, system preferences, staff, and business configuration</div>
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
            <div class="snav-item" onclick="showSection('business',this)">
              <svg width="15" height="15" viewBox="0 0 15 15" fill="none" color="currentColor"><rect x="1.5" y="4" width="12" height="9" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 4V3a2 2 0 0 1 4 0v1" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/><path d="M1.5 8h12" stroke="currentColor" stroke-width="1.3"/></svg>
              Business Info
            </div>
            <div class="snav-item" onclick="showSection('branches',this)">
              <svg width="15" height="15" viewBox="0 0 15 15" fill="none" color="currentColor"><path d="M7.5 1.5C5.29 1.5 3.5 3.29 3.5 5.5c0 3.28 4 8 4 8s4-4.72 4-8c0-2.21-1.79-4-4-4z" stroke="currentColor" stroke-width="1.3"/><circle cx="7.5" cy="5.5" r="1.5" stroke="currentColor" stroke-width="1.2"/></svg>
              Branches
            </div>
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
            <div class="snav-item" onclick="showSection('integrations',this)">
              <svg width="15" height="15" viewBox="0 0 15 15" fill="none" color="currentColor"><rect x="1.5" y="1.5" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="8.5" y="1.5" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="1.5" y="8.5" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/><path d="M11 8.5v5M8.5 11h5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
              Integrations
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
                  <div class="profile-avatar-lg">JG</div>
                  <div class="profile-avatar-info">
                    <div class="profile-avatar-name">Jayne Gonzales</div>
                    <div class="profile-avatar-role">Administrator · REVV Car Rentals</div>
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
                    <input class="form-input" value="Jayne" oninput="markUnsaved()">
                  </div>
                  <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <input class="form-input" value="Gonzales" oninput="markUnsaved()">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input class="form-input" type="email" value="jayne.gonzales@revvrentals.ph" oninput="markUnsaved()">
                  </div>
                  <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input class="form-input" type="tel" value="+63 917 555 0182" oninput="markUnsaved()">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group">
                    <label class="form-label">Role</label>
                    <input class="form-input" value="Administrator" readonly>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Employee ID</label>
                    <input class="form-input" value="EMP-001" readonly>
                  </div>
                </div>
                <div class="form-row full">
                  <div class="form-group">
                    <label class="form-label">Bio / Notes</label>
                    <textarea class="form-textarea" oninput="markUnsaved()">Fleet administrator for REVV Car Rentals Cebu operations. Manages bookings, drivers, and customer relations.</textarea>
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
                  <button class="btn-primary" onclick="saveChanges()">
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
                    <input class="form-input" type="password" placeholder="Enter current password">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input class="form-input" type="password" placeholder="Min. 8 characters">
                  </div>
                  <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input class="form-input" type="password" placeholder="Repeat new password">
                  </div>
                </div>
                <div style="display:flex;justify-content:flex-end;margin-top:4px">
                  <button class="btn-primary" onclick="showToast('Password updated successfully','success')">
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

          <!-- ── BUSINESS INFO ── -->
          <div class="settings-section" id="sec-business">
            <div class="setting-card">
              <div class="setting-card-head">
                <div>
                  <div class="setting-card-title">Business Information</div>
                  <div class="setting-card-sub">Details used on invoices, receipts, and customer communications</div>
                </div>
              </div>
              <div class="setting-card-body">
                <div class="form-row">
                  <div class="form-group">
                    <label class="form-label">Business Name</label>
                    <input class="form-input" value="REVV Car Rentals" oninput="markUnsaved()">
                  </div>
                  <div class="form-group">
                    <label class="form-label">Trading Name</label>
                    <input class="form-input" value="REVV" oninput="markUnsaved()">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group">
                    <label class="form-label">Business Email</label>
                    <input class="form-input" type="email" value="info@revvrentals.ph" oninput="markUnsaved()">
                  </div>
                  <div class="form-group">
                    <label class="form-label">Business Phone</label>
                    <input class="form-input" type="tel" value="+63 32 555 0199" oninput="markUnsaved()">
                  </div>
                </div>
                <div class="form-row full">
                  <div class="form-group">
                    <label class="form-label">Primary Address</label>
                    <input class="form-input" value="2F Skyrise 4B, IT Park, Lahug, Cebu City, 6000" oninput="markUnsaved()">
                  </div>
                </div>
                <div class="form-row three">
                  <div class="form-group">
                    <label class="form-label">TIN / Tax ID</label>
                    <input class="form-input" value="123-456-789-000" oninput="markUnsaved()">
                  </div>
                  <div class="form-group">
                    <label class="form-label">DTI / SEC Reg No.</label>
                    <input class="form-input" value="REVV-DTI-2019-001" oninput="markUnsaved()">
                  </div>
                  <div class="form-group">
                    <label class="form-label">Currency</label>
                    <select class="form-select" onchange="markUnsaved()">
                      <option selected>PHP (₱)</option>
                      <option>USD ($)</option>
                      <option>EUR (€)</option>
                    </select>
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group">
                    <label class="form-label">Date Format</label>
                    <select class="form-select" onchange="markUnsaved()">
                      <option selected>DD MMM YYYY (12 Apr 2026)</option>
                      <option>MM/DD/YYYY</option>
                      <option>YYYY-MM-DD</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Timezone</label>
                    <select class="form-select" onchange="markUnsaved()">
                      <option selected>Asia/Manila (UTC+8)</option>
                      <option>UTC</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="save-bar">
                <div class="save-bar-info">
                  <div class="unsaved-dot" id="unsavedDot2"></div>
                  <span id="saveBarMsg2" style="color:var(--muted)">All changes saved</span>
                </div>
                <div style="display:flex;gap:10px">
                  <button class="btn-ghost" onclick="discardChanges2()">Discard</button>
                  <button class="btn-primary" onclick="saveChanges2()"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="white"><path d="M2 6.5l3.5 3.5 5.5-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Save Changes</span></button>
                </div>
              </div>
            </div>
          </div>

          <!-- ── BRANCHES ── -->
          <div class="settings-section" id="sec-branches">
            <div class="setting-card">
              <div class="setting-card-head">
                <div>
                  <div class="setting-card-title">Branches & Locations</div>
                  <div class="setting-card-sub">Manage pick-up and drop-off points across your fleet</div>
                </div>
                <button class="btn-primary" onclick="showToast('Add branch form coming soon','info')">
                  <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="white"><path d="M6.5 2v9M2 6.5h9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                  <span>Add Branch</span>
                </button>
              </div>
              <div class="setting-card-body">
                <div class="branch-grid">
                  <div class="branch-card primary">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:8px">
                      <span class="branch-tag main">Main Office</span>
                      <div style="display:flex;gap:6px">
                        <div class="act-btn" onclick="showToast('Edit branch')"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M1.5 9L8 2.5l1.5 1.5-6.5 6.5H1.5V9z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                      </div>
                    </div>
                    <div class="branch-name">Cebu City Office</div>
                    <div class="branch-addr">IT Park, Lahug, Cebu City<br>Open: 7AM – 8PM · Mon–Sun</div>
                    <div style="font-size:11px;color:var(--muted2)">📞 +63 32 555 0199</div>
                  </div>
                  <div class="branch-card">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:8px">
                      <span class="branch-tag active">Active</span>
                      <div style="display:flex;gap:6px">
                        <div class="act-btn" onclick="showToast('Edit branch')"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M1.5 9L8 2.5l1.5 1.5-6.5 6.5H1.5V9z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                        <div class="act-btn del" onclick="showToast('Branch removed','error')"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M2 3h8M4.5 3V2h3v1M9 3l-.6 7H3.6L3 3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                      </div>
                    </div>
                    <div class="branch-name">Mactan Airport</div>
                    <div class="branch-addr">Mactan–Cebu Int'l Airport, Lapu-Lapu City<br>Open: 5AM – 11PM · Daily</div>
                    <div style="font-size:11px;color:var(--muted2)">📞 +63 32 555 0210</div>
                  </div>
                  <div class="branch-card">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:8px">
                      <span class="branch-tag active">Active</span>
                      <div style="display:flex;gap:6px">
                        <div class="act-btn" onclick="showToast('Edit branch')"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M1.5 9L8 2.5l1.5 1.5-6.5 6.5H1.5V9z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                        <div class="act-btn del" onclick="showToast('Branch removed','error')"><svg width="12" height="12" viewBox="0 0 12 12" fill="none" color="currentColor"><path d="M2 3h8M4.5 3V2h3v1M9 3l-.6 7H3.6L3 3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                      </div>
                    </div>
                    <div class="branch-name">Talisay Branch</div>
                    <div class="branch-addr">Tabunok, Talisay City, Cebu<br>Open: 8AM – 7PM · Mon–Sat</div>
                    <div style="font-size:11px;color:var(--muted2)">📞 +63 32 555 0225</div>
                  </div>
                  <div class="branch-card" style="border-style:dashed;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:28px;cursor:pointer;gap:10px" onclick="showToast('Add branch form coming soon','info')">
                    <div style="width:40px;height:40px;border-radius:3px;background:var(--red-dim);border:1px solid rgba(232,52,26,0.2);display:flex;align-items:center;justify-content:center">
                      <svg width="18" height="18" viewBox="0 0 18 18" fill="none" color="var(--red)"><path d="M9 3v12M3 9h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </div>
                    <div style="font-size:13px;color:var(--muted2);text-align:center">Add New Branch</div>
                  </div>
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
                    <input class="form-input" type="number" value="1800" oninput="markUnsaved()">
                    <span class="form-label-hint">e.g. Toyota Vios, Honda City</span>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Hatchback</label>
                    <input class="form-input" type="number" value="1500" oninput="markUnsaved()">
                    <span class="form-label-hint">e.g. Mitsubishi Mirage</span>
                  </div>
                  <div class="form-group">
                    <label class="form-label">SUV</label>
                    <input class="form-input" type="number" value="4500" oninput="markUnsaved()">
                    <span class="form-label-hint">e.g. Ford Everest, Honda BR-V</span>
                  </div>
                </div>
                <div class="form-row three">
                  <div class="form-group">
                    <label class="form-label">Premium SUV</label>
                    <input class="form-input" type="number" value="9500" oninput="markUnsaved()">
                    <span class="form-label-hint">e.g. Mercedes GLE, Fortuner</span>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Van</label>
                    <input class="form-input" type="number" value="5500" oninput="markUnsaved()">
                    <span class="form-label-hint">e.g. Toyota HiAce</span>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Pickup</label>
                    <input class="form-input" type="number" value="4200" oninput="markUnsaved()">
                    <span class="form-label-hint">e.g. Ford Ranger</span>
                  </div>
                </div>
                <div class="section-div" style="margin-top:8px">Add-ons & Fees</div>
                <div class="form-row three">
                  <div class="form-group">
                    <label class="form-label">Driver Surcharge (₱/day)</label>
                    <input class="form-input" type="number" value="800" oninput="markUnsaved()">
                  </div>
                  <div class="form-group">
                    <label class="form-label">Late Return Fee (₱/hr)</label>
                    <input class="form-input" type="number" value="350" oninput="markUnsaved()">
                  </div>
                  <div class="form-group">
                    <label class="form-label">Security Deposit (₱)</label>
                    <input class="form-input" type="number" value="5000" oninput="markUnsaved()">
                  </div>
                </div>
              </div>
              <div class="save-bar">
                <div class="save-bar-info"><span style="color:var(--muted)">Rate changes apply to new rentals only</span></div>
                <div style="display:flex;gap:10px">
                  <button class="btn-ghost" onclick="showToast('Changes discarded')">Discard</button>
                  <button class="btn-primary" onclick="showToast('Rate card saved','success')"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="white"><path d="M2 6.5l3.5 3.5 5.5-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Save Rates</span></button>
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
                  <label class="toggle"><input type="checkbox" checked><span class="toggle-track"></span></label>
                </div>
                <div class="toggle-row">
                  <div class="toggle-info">
                    <div class="toggle-label">Rental Due Today</div>
                    <div class="toggle-desc">Morning reminder for rentals scheduled to be returned today</div>
                  </div>
                  <label class="toggle"><input type="checkbox" checked><span class="toggle-track"></span></label>
                </div>
                <div class="toggle-row">
                  <div class="toggle-info">
                    <div class="toggle-label">Overdue Rental Alert</div>
                    <div class="toggle-desc">Immediate alert when a rental becomes overdue past its return date</div>
                  </div>
                  <label class="toggle"><input type="checkbox" checked><span class="toggle-track"></span></label>
                </div>
                <div class="toggle-row">
                  <div class="toggle-info">
                    <div class="toggle-label">Rental Cancelled</div>
                    <div class="toggle-desc">Notify when a customer or admin cancels a booking</div>
                  </div>
                  <label class="toggle"><input type="checkbox"><span class="toggle-track"></span></label>
                </div>

                <div class="section-div" style="margin-top:8px">System Alerts</div>
                <div class="toggle-row">
                  <div class="toggle-info">
                    <div class="toggle-label">Daily Summary Email</div>
                    <div class="toggle-desc">Receive a 6AM digest of today's rentals, returns, and key metrics</div>
                  </div>
                  <label class="toggle"><input type="checkbox" checked><span class="toggle-track"></span></label>
                </div>
                <div class="toggle-row">
                  <div class="toggle-info">
                    <div class="toggle-label">Weekly Report Email</div>
                    <div class="toggle-desc">Send an auto-generated weekly performance report every Monday</div>
                  </div>
                  <label class="toggle"><input type="checkbox" checked><span class="toggle-track"></span></label>
                </div>
                <div class="toggle-row">
                  <div class="toggle-info">
                    <div class="toggle-label">New Staff Account</div>
                    <div class="toggle-desc">Alert when a new user account is created or role is changed</div>
                  </div>
                  <label class="toggle"><input type="checkbox"><span class="toggle-track"></span></label>
                </div>
              </div>
              <div class="save-bar">
                <div class="save-bar-info"><span style="color:var(--muted)">Notifications sent to jayne.gonzales@revvrentals.ph</span></div>
                <button class="btn-primary" onclick="showToast('Notification preferences saved','success')"><svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="white"><path d="M2 6.5l3.5 3.5 5.5-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Save Preferences</span></button>
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

          <!-- ── INTEGRATIONS ── -->
          <div class="settings-section" id="sec-integrations">
            <div class="setting-card">
              <div class="setting-card-head">
                <div>
                  <div class="setting-card-title">Integrations</div>
                  <div class="setting-card-sub">Connect external services to extend REVV functionality</div>
                </div>
              </div>
              <div class="setting-card-body" style="display:flex;flex-direction:column;gap:0">
                <!-- Integration rows -->
                <div class="toggle-row">
                  <div style="display:flex;align-items:center;gap:14px;flex:1">
                    <div style="width:40px;height:40px;background:#25D366;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                      <svg width="22" height="22" viewBox="0 0 22 22" fill="none"><path d="M11 2C6.03 2 2 6.03 2 11c0 1.6.42 3.1 1.15 4.4L2 20l4.72-1.12A9 9 0 1 0 11 2z" fill="white"/></svg>
                    </div>
                    <div>
                      <div class="toggle-label">WhatsApp Business API</div>
                      <div class="toggle-desc">Send booking confirmations and reminders via WhatsApp</div>
                    </div>
                  </div>
                  <label class="toggle"><input type="checkbox" checked><span class="toggle-track"></span></label>
                </div>
                <div class="toggle-row">
                  <div style="display:flex;align-items:center;gap:14px;flex:1">
                    <div style="width:40px;height:40px;background:#EA4335;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                      <svg width="22" height="22" viewBox="0 0 22 22" fill="none"><rect x="3" y="5" width="16" height="12" rx="2" stroke="white" stroke-width="1.6"/><path d="M3 8l8 5 8-5" stroke="white" stroke-width="1.6" stroke-linecap="round"/></svg>
                    </div>
                    <div>
                      <div class="toggle-label">Email (SMTP / Gmail)</div>
                      <div class="toggle-desc">Send receipts, contracts, and alerts via connected email</div>
                    </div>
                  </div>
                  <label class="toggle"><input type="checkbox" checked><span class="toggle-track"></span></label>
                </div>
                <div class="toggle-row">
                  <div style="display:flex;align-items:center;gap:14px;flex:1">
                    <div style="width:40px;height:40px;background:#003087;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                      <svg width="22" height="20" viewBox="0 0 22 20" fill="none"><path d="M4 10c0-3.86 3.14-7 7-7s7 3.14 7 7-3.14 7-7 7" stroke="white" stroke-width="1.6" stroke-linecap="round"/><path d="M8 10h6M11 7v6" stroke="white" stroke-width="1.6" stroke-linecap="round"/></svg>
                    </div>
                    <div>
                      <div class="toggle-label">PayPal / PayMongo Payments</div>
                      <div class="toggle-desc">Process online payments and deposits directly in-system</div>
                    </div>
                  </div>
                  <label class="toggle"><input type="checkbox"><span class="toggle-track"></span></label>
                </div>
                <div class="toggle-row">
                  <div style="display:flex;align-items:center;gap:14px;flex:1">
                    <div style="width:40px;height:40px;background:#4285F4;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                      <svg width="22" height="22" viewBox="0 0 22 22" fill="none"><rect x="3" y="3" width="7" height="7" rx="1" fill="white" opacity="0.9"/><rect x="12" y="3" width="7" height="7" rx="1" fill="white" opacity="0.7"/><rect x="3" y="12" width="7" height="7" rx="1" fill="white" opacity="0.7"/><rect x="12" y="12" width="7" height="7" rx="1" fill="white" opacity="0.5"/></svg>
                    </div>
                    <div>
                      <div class="toggle-label">Google Maps & GPS Tracking</div>
                      <div class="toggle-desc">Show vehicle locations on map and enable real-time tracking</div>
                    </div>
                  </div>
                  <label class="toggle"><input type="checkbox"><span class="toggle-track"></span></label>
                </div>
                <div class="toggle-row" style="border-bottom:none">
                  <div style="display:flex;align-items:center;gap:14px;flex:1">
                    <div style="width:40px;height:40px;background:var(--card2);border:1px solid var(--border2);border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                      <svg width="20" height="20" viewBox="0 0 20 20" fill="none" color="var(--muted2)"><rect x="2" y="2" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="11" y="2" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="2" y="11" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.3"/><path d="M14.5 11v7M11 14.5h7" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
                    </div>
                    <div>
                      <div class="toggle-label">API Webhooks</div>
                      <div class="toggle-desc">Send real-time event data to your own systems or third-party apps</div>
                    </div>
                  </div>
                  <button class="btn-ghost" style="font-size:11px;padding:8px 14px" onclick="showToast('API docs coming soon','info')">Configure</button>
                </div>
              </div>
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

<script src="../javascript/adminsettings.js"></script>
</body>
</html>