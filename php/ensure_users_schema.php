<?php
require_once __DIR__ . '/../databases/connection1.php';
require_once __DIR__ . '/user_helpers.php';

ensureUsersSchema($conn);
echo "Users schema ready.\n";
foreach ($conn->query('SHOW COLUMNS FROM users') as $col) {
    echo $col['Field'] . "\n";
}
