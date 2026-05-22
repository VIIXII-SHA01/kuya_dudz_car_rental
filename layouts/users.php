<?php
require_once __DIR__ . '/../databases/connection1.php';
require_once __DIR__ . '/../php/user_helpers.php';

requireAdminUser();
ensureUsersSchema($conn);

$message = '';
$error = '';
$editing = false;
$editUser = [
    'id' => '',
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'mobile' => '',
    'gender' => 'Unspecified',
    'birth_date' => '',
    'baranggay' => '',
    'city' => '',
    'province' => '',
    'zipcode' => '',
    'role' => 'staff',
    'status' => 'active',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'save_user') {
            $result = saveManagedUser($conn, $_POST);
            $message = $result['message'];
            if (!empty($_POST['user_id'])) {
                header('Location: /users?edit=' . (int) $_POST['user_id'] . '&saved=1');
                exit;
            }
        } elseif ($action === 'set_status') {
            $userId = (int) ($_POST['user_id'] ?? 0);
            $status = normalizeUserStatus((string) ($_POST['status'] ?? ''));
            $result = setUserStatus($conn, $userId, $status);
            $message = $result['message'];
        }
    } catch (RuntimeException $e) {
        $error = $e->getMessage();
    }
}

if (!empty($_GET['saved'])) {
    $message = 'User updated successfully.';
}

if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    if ($editId > 0) {
        $row = fetchUserById($conn, $editId);
        if ($row) {
            $editing = true;
            $editUser = [
                'id' => $row['id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'email' => $row['email'],
                'mobile' => $row['mobile'] ?? '',
                'gender' => $row['gender'] ?? 'Unspecified',
                'birth_date' => $row['birth_date'] ?? '',
                'baranggay' => $row['baranggay'] ?? '',
                'city' => $row['city'] ?? '',
                'province' => $row['province'] ?? '',
                'zipcode' => $row['zipcode'] ?? '',
                'role' => $row['role'],
                'status' => $row['status'] ?? 'active',
            ];
        }
    }
}

$users = listAllUsers($conn);
$currentUserId = currentUserId();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include __DIR__ . '/../includes/favicon.php'; ?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>KDCR — Users</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/rent/css/theme.css">
<link rel="stylesheet" href="/rent/css/users.css">
</head>
<body class="users-page">
<div class="app">
  <?php include(__DIR__ . '/../navs/adminnavs.php'); ?>
  <div class="sidebar-overlay" id="sidebarOverlay"></div>
  <div class="main">
    <header class="topbar">
      <button class="menu-toggle" id="menuToggle" aria-label="Toggle navigation">
        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" color="white"><path d="M3 5h12M3 9h12M3 13h12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
      </button>
      <div class="topbar-title">Users</div>
      <div class="topbar-divider"></div>
      <div style="font-size:13px;color:var(--muted2)">Create and manage admin and staff accounts</div>
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
          <div class="page-eyebrow">Admin</div>
          <div class="page-title">Users</div>
          <div class="page-sub">Add, edit, and restrict team accounts. Restricted users cannot sign in.</div>
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
              <div class="setting-card-title"><?= $editing ? 'Edit User' : 'Create New User' ?></div>
              <div class="setting-card-sub"><?= $editing ? 'Update account details, role, or access status' : 'Add a new admin or staff account' ?></div>
            </div>
            <?php if ($editing): ?>
              <a class="btn-ghost" href="/users">Cancel</a>
            <?php endif; ?>
          </div>
          <form method="post" class="setting-card-body">
            <input type="hidden" name="action" value="save_user">
            <?php if ($editing): ?>
              <input type="hidden" name="user_id" value="<?= htmlspecialchars($editUser['id'], ENT_QUOTES, 'UTF-8') ?>">
            <?php endif; ?>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">First Name</label>
                <input name="first_name" class="form-input" maxlength="50" required value="<?= htmlspecialchars($editUser['first_name'], ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Last Name</label>
                <input name="last_name" class="form-input" maxlength="50" required value="<?= htmlspecialchars($editUser['last_name'], ENT_QUOTES, 'UTF-8') ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Email</label>
                <input name="email" class="form-input" type="email" maxlength="50" required value="<?= htmlspecialchars($editUser['email'], ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Mobile</label>
                <input name="mobile" class="form-input" type="tel" maxlength="20" required value="<?= htmlspecialchars($editUser['mobile'], ENT_QUOTES, 'UTF-8') ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-input" required>
                  <?php
                    $genders = ['Unspecified', 'Male', 'Female', 'Other'];
                    foreach ($genders as $g):
                  ?>
                    <option value="<?= htmlspecialchars($g, ENT_QUOTES, 'UTF-8') ?>" <?= ($editUser['gender'] ?? '') === $g ? 'selected' : '' ?>><?= htmlspecialchars($g, ENT_QUOTES, 'UTF-8') ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Birth Date</label>
                <input name="birth_date" class="form-input" type="date" required value="<?= htmlspecialchars($editUser['birth_date'], ENT_QUOTES, 'UTF-8') ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Barangay</label>
                <input name="baranggay" class="form-input" maxlength="50" required value="<?= htmlspecialchars($editUser['baranggay'], ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">City</label>
                <input name="city" class="form-input" maxlength="50" required value="<?= htmlspecialchars($editUser['city'], ENT_QUOTES, 'UTF-8') ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Province</label>
                <input name="province" class="form-input" maxlength="50" required value="<?= htmlspecialchars($editUser['province'], ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Zip Code</label>
                <input name="zipcode" class="form-input" type="number" min="0" step="1" required value="<?= htmlspecialchars((string) $editUser['zipcode'], ENT_QUOTES, 'UTF-8') ?>">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Role</label>
                <select name="role" class="form-input">
                  <option value="admin" <?= $editUser['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                  <option value="staff" <?= $editUser['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Account Status</label>
                <select name="status" class="form-input" <?= $editing && (int) $editUser['id'] === $currentUserId ? 'disabled' : '' ?>>
                  <option value="active" <?= ($editUser['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                  <option value="restricted" <?= ($editUser['status'] ?? '') === 'restricted' ? 'selected' : '' ?>>Restricted</option>
                </select>
                <?php if ($editing && (int) $editUser['id'] === $currentUserId): ?>
                  <input type="hidden" name="status" value="active">
                  <div class="form-hint">You cannot restrict your own account.</div>
                <?php else: ?>
                  <div class="form-hint">Restricted users cannot log in until set back to Active.</div>
                <?php endif; ?>
              </div>
            </div>

            <?php if ($editing): ?>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">New Password (optional)</label>
                <input name="password" class="form-input" type="password" autocomplete="new-password">
                <div class="form-hint">Leave blank to keep the current password.</div>
              </div>
            </div>
            <?php else: ?>
            <div class="form-row full">
              <div class="form-group">
                <div class="form-hint">New users are created with the default password <strong>Kdcr2026</strong>. They can change it later from their profile.</div>
              </div>
            </div>
            <?php endif; ?>

            <div class="save-bar">
              <div class="save-bar-info"><?= $editing ? 'Save changes to this user.' : 'Create account with default password Kdcr2026.' ?></div>
              <button type="submit" class="btn-primary"><?= $editing ? 'Save User' : 'Create User' ?></button>
            </div>
          </form>
        </div>

        <div class="setting-card">
          <div class="setting-card-head">
            <div>
              <div class="setting-card-title">User List</div>
              <div class="setting-card-sub">All accounts in the system</div>
            </div>
          </div>
          <div class="setting-card-body users-card-body">
            <table class="users-table">
              <thead>
                <tr>
                  <th>User</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th>Phone</th>
                  <th>Created</th>
                  <th class="actions-col">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $user): ?>
                  <?php
                    $isRestricted = ($user['status'] ?? 'active') === 'restricted';
                    $isSelf = (int) $user['id'] === $currentUserId;
                  ?>
                  <tr class="<?= $isRestricted ? 'row-restricted' : '' ?>">
                    <td><?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><span class="role-badge role-<?= htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(ucfirst($user['role']), ENT_QUOTES, 'UTF-8') ?></span></td>
                    <td>
                      <?php if ($isRestricted): ?>
                        <span class="status-badge restricted">Restricted</span>
                      <?php else: ?>
                        <span class="status-badge active">Active</span>
                      <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($user['mobile'] ?: '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= !empty($user['created_at']) ? htmlspecialchars(substr($user['created_at'], 0, 10), ENT_QUOTES, 'UTF-8') : '—' ?></td>
                    <td class="actions-col">
                      <div class="table-actions">
                        <a class="btn-ghost small" href="/users?edit=<?= (int) $user['id'] ?>">Edit</a>
                        <?php if (!$isSelf): ?>
                          <form method="post" class="inline-form" onsubmit="return confirm('<?= $isRestricted ? 'Activate this user? They will be able to log in again.' : 'Restrict this user? They will not be able to log in.' ?>')">
                            <input type="hidden" name="action" value="set_status">
                            <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
                            <input type="hidden" name="status" value="<?= $isRestricted ? 'active' : 'restricted' ?>">
                            <button type="submit" class="btn-ghost small <?= $isRestricted ? '' : 'warn' ?>"><?= $isRestricted ? 'Activate' : 'Restrict' ?></button>
                          </form>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
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

