<?php
require_once __DIR__ . '/../databases/connection1.php';
require_once __DIR__ . '/../php/user_helpers.php';

requireLoggedInUser();
ensureUsersSchema($conn);

$message = '';
$error = '';
$userId = currentUserId();
$profile = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'phone' => '',
    'role' => 'Staff',
    'employee_id' => '',
    'bio' => '',
];
$avatarInitials = 'US';

if ($userId) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $result = saveOwnProfile($conn, $userId, $_POST);
            $message = $result['message'];
        } catch (RuntimeException $e) {
            $error = $e->getMessage();
        }
    }

    $row = fetchUserById($conn, $userId);
    if ($row) {
        $formatted = formatUserRow($row);
        $profile['first_name'] = $formatted['first_name'];
        $profile['last_name'] = $formatted['last_name'];
        $profile['email'] = $formatted['email'];
        $profile['phone'] = $formatted['mobile'];
        $profile['role'] = ucfirst($formatted['role']);
        if (!empty($row['notes'])) {
            $profile['bio'] = $row['notes'];
        }
        if (!empty($row['username'])) {
            $profile['employee_id'] = $row['username'];
        }
        $avatarInitials = strtoupper(substr($profile['first_name'], 0, 1) . substr($profile['last_name'], 0, 1)) ?: 'US';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include __DIR__ . '/../includes/favicon.php'; ?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KDCR — Profile</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/rent/css/profile.css">
<link rel="stylesheet" href="/rent/css/theme.css">
</head>
<body>
<div class="app">
  <?php include(__DIR__ . '/../navs/adminnavs.php'); ?>
  <div class="sidebar-overlay" id="sidebarOverlay"></div>
  <div class="main">
    <header class="topbar">
      <button class="menu-toggle" id="menuToggle" aria-label="Toggle navigation">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" color="white"><path d="M3 5h12M3 9h12M3 13h12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
      </button>
      <div class="topbar-title">Profile</div>
      <div class="topbar-divider"></div>
      <div style="font-size:13px;color:var(--muted2)">Manage your account details</div>
      <div class="topbar-right">
        <div class="topbar-date">
          <svg width="13" height="13" viewBox="0 0 13 13" fill="none" color="currentColor"><rect x="1.5" y="2.5" width="10" height="9" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M4 2.5V1M9 2.5V1M1.5 5.5h10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
          <span id="topbarDateText">Today</span>
        </div>
        <button id="themeToggle" class="icon-btn theme-toggle" type="button" aria-label="Toggle theme" title="Toggle theme">
          <span class="theme-toggle-icon">☀️</span>
        </button>
      </div>
    </header>

    <div class="content">
      <div class="page-header">
        <div>
          <div class="page-eyebrow">Account</div>
          <div class="page-title">My Profile</div>
          <div class="page-sub">Staff can update their own details here. User management is admin-only.</div>
        </div>
      </div>

      <div class="page-body">
        <?php if ($message): ?>
          <div class="alert success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
        <?php elseif ($error): ?>
          <div class="alert error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="setting-card">
          <div class="setting-card-head">
            <div>
              <div class="setting-card-title">Profile Information</div>
              <div class="setting-card-sub">Edit your personal information and contact details</div>
            </div>
          </div>
          <form method="post" class="setting-card-body">
            <input type="hidden" name="action" value="save_profile">

            <div class="profile-avatar-row">
              <div class="profile-avatar-lg"><?= htmlspecialchars($avatarInitials, ENT_QUOTES, 'UTF-8') ?></div>
              <div class="profile-avatar-info">
                <div class="profile-avatar-name"><?= htmlspecialchars(trim($profile['first_name'] . ' ' . $profile['last_name']), ENT_QUOTES, 'UTF-8') ?></div>
                <div class="profile-avatar-role"><?= htmlspecialchars($profile['role'], ENT_QUOTES, 'UTF-8') ?></div>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">First Name</label>
                <input name="first_name" class="form-input" required value="<?= htmlspecialchars($profile['first_name'], ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Last Name</label>
                <input name="last_name" class="form-input" required value="<?= htmlspecialchars($profile['last_name'], ENT_QUOTES, 'UTF-8') ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Email Address</label>
                <input name="email" class="form-input" type="email" required value="<?= htmlspecialchars($profile['email'], ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input name="phone" class="form-input" type="tel" value="<?= htmlspecialchars($profile['phone'], ENT_QUOTES, 'UTF-8') ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Role</label>
                <input class="form-input" value="<?= htmlspecialchars($profile['role'], ENT_QUOTES, 'UTF-8') ?>" readonly>
              </div>
              <div class="form-group">
                <label class="form-label">Employee ID</label>
                <input class="form-input" value="<?= htmlspecialchars($profile['employee_id'], ENT_QUOTES, 'UTF-8') ?>" readonly placeholder="Not set">
              </div>
            </div>

            <div class="form-row full">
              <div class="form-group">
                <label class="form-label">About Me</label>
                <textarea name="bio" class="form-textarea"><?= htmlspecialchars($profile['bio'], ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
            </div>

            <div class="setting-card-head" style="margin-top:8px;padding-top:18px;border-top:1px solid var(--border)">
              <div>
                <div class="setting-card-title" style="font-size:1rem">Change Password</div>
                <div class="setting-card-sub">Leave blank to keep your current password</div>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">New Password</label>
                <input name="password" class="form-input" type="password" autocomplete="new-password">
              </div>
              <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input name="password_confirm" class="form-input" type="password" autocomplete="new-password">
              </div>
            </div>

            <div class="save-bar">
              <div class="save-bar-info"><span id="saveBarMsg">Update your profile details</span></div>
              <div style="display:flex;gap:10px;flex-wrap:wrap">
                <button type="reset" class="btn-ghost">Reset</button>
                <button type="submit" class="btn-primary">Save Changes</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="toast" id="toast"><span id="toastMsg"></span></div>
<script src="/rent/javascript/theme.js"></script>
<script src="/rent/javascript/admindashboard.js"></script>
</body>
</html>
