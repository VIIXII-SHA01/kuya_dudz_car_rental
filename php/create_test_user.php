<?php
require_once __DIR__ . '/../databases/connection1.php';

$email = 'keithyvheaiv@gmail.com';
$password = '3***r***d';
$first = 'Kim';
$last = 'Fernando';

try {
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $ex = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($ex) {
        echo "User already exists: {$email}\n";
        exit(0);
    }

    // Inspect available columns
    $cols = $conn->query("SHOW COLUMNS FROM users")->fetchAll(PDO::FETCH_COLUMN);
    $cols = array_map('strtolower', $cols ?: []);
    $pw = password_hash($password, PASSWORD_DEFAULT);

    $insertCols = [];
    $params = [];

    if (in_array('username', $cols)) { $insertCols[] = 'username'; $params[':username']='testuser'; }
    if (in_array('first_name', $cols)) { $insertCols[] = 'first_name'; $params[':first_name']=$first; }
    if (in_array('last_name', $cols)) { $insertCols[] = 'last_name'; $params[':last_name']=$last; }
    if (in_array('email', $cols)) { $insertCols[] = 'email'; $params[':email']=$email; }
    if (in_array('password', $cols)) { $insertCols[] = 'password'; $params[':password']=$pw; }
    elseif (in_array('password_hash', $cols)) { $insertCols[] = 'password_hash'; $params[':password_hash']=$pw; }
    if (in_array('role', $cols)) { $insertCols[] = 'role'; $params[':role']='user'; }

    if (empty($insertCols)) {
        echo "No suitable columns found in users table to insert test user.\n";
        exit(1);
    }

    $sql = 'INSERT INTO users (' . implode(', ', $insertCols) . ') VALUES (' . implode(', ', array_keys($params)) . ')';
    $ins = $conn->prepare($sql);
    $ins->execute($params);

    echo "Created test user: {$email} with password: {$password}\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
