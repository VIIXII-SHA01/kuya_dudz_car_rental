<?php
// Endpoint: send reset code to user email
header('Content-Type: application/json');
require_once __DIR__ . '/../databases/connection1.php';
require_once __DIR__ . '/../mailer/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

if (! isset($conn) || ! ($conn instanceof PDO)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection unavailable']);
    exit;
}

$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
if (! $email) {
    echo json_encode(['success' => false, 'message' => 'Invalid email']);
    exit;
}

try {
    // check user exists
    $stmt = $conn->prepare('SELECT id, first_name, email FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (! $user) {
        echo json_encode(['success' => false, 'message' => 'Email not found']);
        exit;
    }

    // ensure password_resets table exists
    $conn->exec(<<<SQL
CREATE TABLE IF NOT EXISTS password_resets (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED DEFAULT NULL,
  email VARCHAR(150) NOT NULL,
  token VARCHAR(128) NOT NULL,
  code VARCHAR(8) NOT NULL,
  expires_at DATETIME NOT NULL,
  used TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (email),
  INDEX (token)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
SQL
    );

    $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $token = bin2hex(random_bytes(16));
    $expires = date('Y-m-d H:i:s', time() + 300); // 5 minutes

    $ins = $conn->prepare('INSERT INTO password_resets (user_id, email, token, code, expires_at) VALUES (:uid, :email, :token, :code, :expires)');
    $ins->execute([
        ':uid' => $user['id'],
        ':email' => $email,
        ':token' => $token,
        ':code' => $code,
        ':expires' => $expires
    ]);

    // send email
    $subject = 'Your KDCR password reset code';
    $link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/rent/forgotpassword';
    $body = "<p>Hi " . htmlspecialchars($user['first_name'] ?? '') . ",</p>";
    $body .= "<p>Your password reset code is: <strong>{$code}</strong>. It will expire in 5 minutes.</p>";
    $body .= "<p>If you didn't request this, ignore this email.</p>";
    $body .= "<p>If the link is not visible, open: <a href=\"{$link}\">Reset Password</a></p>";

    $plainBody = "Hi " . ($user['first_name'] ?? '') . ",\n\n";
    $plainBody .= "Your password reset code is: {$code}. It will expire in 5 minutes.\n\n";
    $plainBody .= "If you didn't request this, ignore this email.\n";
    $plainBody .= "Open the reset page: {$link}\n";

    $sent = send_mail($email, $subject, $body, $plainBody);

    if ($sent !== true) {
        $message = 'Failed sending email';
        if (is_string($sent) && getenv('MAIL_DEBUG')) {
            $message = $sent;
        }
        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Code sent']);
    exit;
} catch (Throwable $e) {
    error_log('Forgot error: ' . $e->getMessage());
    $message = 'Server error';
    if (getenv('RENT_APP_DEBUG') === '1' || getenv('MAIL_DEBUG')) {
        $message = $e->getMessage();
    }
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

