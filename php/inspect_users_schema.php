<?php
require_once __DIR__ . '/../databases/connection1.php';
try {
    $stmt = $conn->query('SHOW COLUMNS FROM users');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo $col['Field'] . ':' . $col['Type'] . ':' . $col['Null'] . "\n";
    }
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
