<?php
// Endpoint: reset password using token
header('Content-Type: application/json');
require_once __DIR__ . '/../databases/connection1.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$token = trim($_POST['token'] ?? '');
$newpass = $_POST['password'] ?? '';

if ($token === '' || strlen($newpass) < 8) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    $stmt = $conn->prepare('SELECT id, user_id, email, expires_at, used FROM password_resets WHERE token = :token LIMIT 1');
    $stmt->execute([':token' => $token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (! $row) {
        echo json_encode(['success' => false, 'message' => 'Invalid token']);
        exit;
    }
    if ($row['used']) {
        echo json_encode(['success' => false, 'message' => 'Token already used']);
        exit;
    }
    if (strtotime($row['expires_at']) < time()) {
        echo json_encode(['success' => false, 'message' => 'Token expired']);
        exit;
    }

    $userId = $row['user_id'];

    // Determine whether users table uses 'password' or 'password_hash'
    $colCheck = $conn->query("SHOW COLUMNS FROM users LIKE 'password'")->fetch(PDO::FETCH_ASSOC);
    $hashed = password_hash($newpass, PASSWORD_DEFAULT);

    if ($colCheck) {
        $u = $conn->prepare('UPDATE users SET password = :pw WHERE id = :id');
        $u->execute([':pw' => $hashed, ':id' => $userId]);
    } else {
        // try password_hash column
        $u = $conn->prepare('UPDATE users SET password_hash = :pw WHERE id = :id');
        $u->execute([':pw' => $hashed, ':id' => $userId]);
    }

    // mark token used
    $m = $conn->prepare('UPDATE password_resets SET used = 1 WHERE id = :id');
    $m->execute([':id' => $row['id']]);

    echo json_encode(['success' => true, 'message' => 'Password reset']);
    exit;
} catch (PDOException $e) {
    error_log('Reset error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error']);
    exit;
}
