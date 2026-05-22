<?php
session_start();
require_once __DIR__ . '/../databases/connection1.php';
require_once __DIR__ . '/user_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /rent/login');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    header('Location: /rent/login?error=invalid');
    exit;
}

try {
    ensureUsersSchema($conn);

    $columns = getUserColumns($conn);
    $select = ['id', 'first_name', 'last_name', 'email', 'password', 'role'];
    if (in_array('status', $columns, true)) {
        $select[] = 'status';
    }

    $stmt = $conn->prepare('SELECT ' . implode(', ', $select) . ' FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Location: /rent/login?error=invalid&reason=notfound');
        exit;
    }

    $passwordValid = password_verify($password, $user['password']);
    if (!$passwordValid) {
        header('Location: /rent/login?error=invalid&reason=wrongpass');
        exit;
    }

    if (!isLoginAllowedUser($user)) {
        $reason = normalizeUserStatus((string) ($user['status'] ?? 'active')) === 'restricted'
            ? 'restricted'
            : 'role';
        header('Location: /rent/login?error=restricted&reason=' . urlencode($reason));
        exit;
    }

    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'first_name' => $user['first_name'],
        'last_name' => $user['last_name'],
        'email' => $user['email'],
        'role' => normalizeUserRole($user['role']),
        'status' => normalizeUserStatus((string) ($user['status'] ?? 'active')),
    ];
    $_SESSION['logged_in'] = true;

    header('Location: /rent/dashboard');
    exit;
} catch (PDOException $e) {
    error_log('Login error: ' . $e->getMessage());
    header('Location: /rent/login?error=invalid');
    exit;
}
