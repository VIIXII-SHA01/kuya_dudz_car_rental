<?php
// Endpoint: verify 6-digit code and return token for reset
header('Content-Type: application/json');
require_once __DIR__ . '/../databases/connection1.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$code = trim($_POST['code'] ?? '');

if (! $email || $code === '') {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    $stmt = $conn->prepare('SELECT token, expires_at FROM password_resets WHERE email = :email AND code = :code AND used = 0 ORDER BY id DESC LIMIT 1');
    $stmt->execute([':email' => $email, ':code' => $code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (! $row) {
        echo json_encode(['success' => false, 'message' => 'Code not found']);
        exit;
    }

    if (strtotime($row['expires_at']) < time()) {
        echo json_encode(['success' => false, 'message' => 'Code expired']);
        exit;
    }

    // return token to client for resetting password
    echo json_encode(['success' => true, 'token' => $row['token']]);
    exit;
} catch (PDOException $e) {
    error_log('Verify OTP error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error']);
    exit;
}
