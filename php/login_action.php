<?php
session_start();
require_once __DIR__ . '/../databases/connection1.php';

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
    $stmt = $conn->prepare('SELECT id, first_name, last_name, email, password, role FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (! $user) {
        header('Location: /rent/login?error=invalid');
        exit;
    }

    $passwordValid = password_verify($password, $user['password']) || $password === $user['password'];
    if (! $passwordValid) {
        header('Location: /rent/login?error=invalid');
        exit;
    }

    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'first_name' => $user['first_name'],
        'last_name' => $user['last_name'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];
    $_SESSION['logged_in'] = true;

    header('Location: /rent/dashboard');
    exit;
} catch (PDOException $e) {
    error_log('Login error: ' . $e->getMessage());
    header('Location: /rent/login?error=invalid');
    exit;
}
