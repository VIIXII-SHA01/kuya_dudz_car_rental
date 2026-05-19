<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

if (empty($_SESSION['user']['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized.']);
    exit;
}

require_once __DIR__ . '/../databases/connection1.php';

function sendJson(array $payload, int $status = 200): void {
    http_response_code($status);
    echo json_encode($payload);
    exit;
}

function getUserColumns(PDO $conn): array {
    $stmt = $conn->query('SHOW COLUMNS FROM users');
    return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field');
}

function columnExists(PDO $conn, string $column): bool {
    $stmt = $conn->prepare('SHOW COLUMNS FROM users LIKE :column');
    $stmt->execute([':column' => $column]);
    return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
}

function ensureSettingsTable(PDO $conn): void {
    $conn->exec(
        'CREATE TABLE IF NOT EXISTS settings (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(150) NOT NULL UNIQUE,
            setting_value TEXT DEFAULT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );
}

function upsertSetting(PDO $conn, string $key, string $value): void {
    $stmt = $conn->prepare(
        'INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value)
        ON DUPLICATE KEY UPDATE setting_value = :value, updated_at = CURRENT_TIMESTAMP'
    );
    $stmt->execute([':key' => $key, ':value' => $value]);
}

function getAllSettings(PDO $conn): array {
    $stmt = $conn->query('SELECT setting_key, setting_value FROM settings');
    return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'setting_value', 'setting_key');
}

$userId = (int) $_SESSION['user']['id'];
$action = trim($_POST['action'] ?? '');

try {
    if ($action === 'save_profile') {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $bio = trim($_POST['bio'] ?? '');

        if ($firstName === '' || $lastName === '' || $email === '') {
            sendJson(['error' => 'First name, last name, and email are required.'], 400);
        }

        $stmt = $conn->prepare('SELECT id FROM users WHERE email = :email AND id != :id LIMIT 1');
        $stmt->execute([':email' => $email, ':id' => $userId]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            sendJson(['error' => 'That email address is already used by another account.'], 409);
        }

        $columns = getUserColumns($conn);
        $updates = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
        ];

        if (in_array('mobile', $columns, true)) {
            $updates['mobile'] = $phone;
        } elseif (in_array('phone', $columns, true)) {
            $updates['phone'] = $phone;
        }

        if (in_array('notes', $columns, true)) {
            $updates['notes'] = $bio;
        }

        $setClauses = [];
        $params = [];
        foreach ($updates as $field => $value) {
            $setClauses[] = "$field = :$field";
            $params[":$field"] = $value;
        }
        $params[':id'] = $userId;

        $stmt = $conn->prepare('UPDATE users SET ' . implode(', ', $setClauses) . ' WHERE id = :id');
        $stmt->execute($params);

        $_SESSION['user']['first_name'] = $firstName;
        $_SESSION['user']['last_name'] = $lastName;
        $_SESSION['user']['email'] = $email;

        sendJson([
            'success' => true,
            'user' => [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
            ],
        ]);
    }

    if ($action === 'save_rates') {
        ensureSettingsTable($conn);

        $rateFields = [
            'rate_sedan',
            'rate_hatchback',
            'rate_suv',
            'rate_premium_suv',
            'rate_van',
            'rate_pickup',
            'addon_driver_surcharge',
            'addon_late_fee',
            'addon_security_deposit',
        ];

        foreach ($rateFields as $field) {
            $value = trim((string)($_POST[$field] ?? ''));
            if ($value === '' || !is_numeric($value) || (float)$value < 0) {
                sendJson(['error' => 'Invalid value for ' . str_replace('_', ' ', $field) . '.'], 400);
            }
            upsertSetting($conn, $field, (string)(float)$value);
        }

        sendJson(['success' => true]);
    }

    if ($action === 'save_notification_preferences') {
        ensureSettingsTable($conn);

        $prefKeys = [
            'new_rental_created',
            'rental_due_today',
            'overdue_rental_alert',
            'rental_cancelled',
            'daily_summary_email',
            'weekly_report_email',
            'new_staff_account',
        ];

        foreach ($prefKeys as $key) {
            $storageKey = 'user_' . $userId . '_notif_' . $key;
            $value = isset($_POST[$key]) && in_array($_POST[$key], ['1', 'true', 'yes', 'on'], true) ? '1' : '0';
            upsertSetting($conn, $storageKey, $value);
        }

        sendJson(['success' => true]);
    }

    if ($action === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            sendJson(['error' => 'All password fields are required.'], 400);
        }

        if ($newPassword !== $confirmPassword) {
            sendJson(['error' => 'New passwords do not match.'], 400);
        }

        if (strlen($newPassword) < 8) {
            sendJson(['error' => 'New password must be at least 8 characters.'], 400);
        }

        $passwordColumn = columnExists($conn, 'password') ? 'password' : (columnExists($conn, 'password_hash') ? 'password_hash' : null);
        if ($passwordColumn === null) {
            sendJson(['error' => 'Password storage column not found.'], 500);
        }

        $stmt = $conn->prepare("SELECT $passwordColumn AS stored_password FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (! $row) {
            sendJson(['error' => 'User not found.'], 404);
        }

        $storedPassword = $row['stored_password'] ?? '';
        $isValid = password_verify($currentPassword, $storedPassword) || $currentPassword === $storedPassword;
        if (! $isValid) {
            sendJson(['error' => 'Current password is incorrect.'], 401);
        }

        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET $passwordColumn = :password WHERE id = :id");
        $stmt->execute([':password' => $passwordHash, ':id' => $userId]);

        sendJson(['success' => true]);
    }

    sendJson(['error' => 'Invalid action.'], 400);
} catch (PDOException $e) {
    sendJson(['error' => 'Database error: ' . $e->getMessage()], 500);
}
