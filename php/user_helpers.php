<?php

/**
 * Users & profile helpers (roles: admin, staff).
 */

require_once __DIR__ . '/db_helpers.php';

const USER_ALLOWED_ROLES = ['admin', 'staff'];
const USER_ALLOWED_STATUSES = ['active', 'restricted'];
const DEFAULT_NEW_USER_PASSWORD = 'Kdcr2026';

const USER_FIELD_LIMITS = [
    'first_name' => 50,
    'last_name' => 50,
    'gender' => 50,
    'mobile' => 20,
    'email' => 50,
    'baranggay' => 50,
    'city' => 50,
    'province' => 50,
];

function getUserColumns(PDO $conn): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $cache = array_column($conn->query('SHOW COLUMNS FROM users')->fetchAll(PDO::FETCH_ASSOC), 'Field');

    return $cache;
}

function ensureUsersSchema(PDO $conn): void
{
    if (!tableHasColumn($conn, 'users', 'status')) {
        try {
            $conn->exec("ALTER TABLE users ADD COLUMN status ENUM('active','restricted') NOT NULL DEFAULT 'active' AFTER role");
        } catch (PDOException $e) {
            // column may already exist under different definition
        }
    }
}

function getPasswordColumn(PDO $conn): string
{
    $columns = getUserColumns($conn);
    if (in_array('password', $columns, true)) {
        return 'password';
    }
    if (in_array('password_hash', $columns, true)) {
        return 'password_hash';
    }

    return 'password';
}

function normalizeUserRole(string $role): string
{
    $role = strtolower(trim($role));

    return in_array($role, USER_ALLOWED_ROLES, true) ? $role : 'staff';
}

function normalizeUserStatus(string $status): string
{
    $status = strtolower(trim($status));

    return in_array($status, USER_ALLOWED_STATUSES, true) ? $status : 'active';
}

function currentSessionUser(): ?array
{
    return isset($_SESSION['user']) && is_array($_SESSION['user']) ? $_SESSION['user'] : null;
}

function currentUserId(): int
{
    $user = currentSessionUser();

    return (int) ($user['id'] ?? 0);
}

function currentUserRole(): string
{
    $user = currentSessionUser();

    return normalizeUserRole((string) ($user['role'] ?? ''));
}

function isAdminUser(?array $user = null): bool
{
    $user = $user ?? currentSessionUser();

    return normalizeUserRole((string) ($user['role'] ?? '')) === 'admin';
}

function requireLoggedInUser(): void
{
    if (empty($_SESSION['logged_in']) || empty($_SESSION['user'])) {
        if (isApiRequest()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized.']);
            exit;
        }
        header('Location: /rent/login');
        exit;
    }
}

function requireAdminUser(): void
{
    requireLoggedInUser();
    if (!isAdminUser()) {
        if (isApiRequest()) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Admin access required.']);
            exit;
        }
        header('HTTP/1.1 403 Forbidden');
        echo '<h1>403 Forbidden</h1><p>Admin access is required for this page.</p>';
        exit;
    }
}

function isApiRequest(): bool
{
    $uri = $_SERVER['REQUEST_URI'] ?? '';

    return str_contains($uri, '_action.php') || str_contains($uri, 'user_action.php');
}

function truncateUserField(string $value, string $field): string
{
    $limit = USER_FIELD_LIMITS[$field] ?? null;
    if ($limit === null) {
        return $value;
    }

    if (function_exists('mb_substr')) {
        return mb_substr($value, 0, $limit);
    }

    return substr($value, 0, $limit);
}

function parseManagedUserInput(array $input, bool $isCreate): array
{
    $firstName = truncateUserField(trim((string) ($input['first_name'] ?? '')), 'first_name');
    $lastName = truncateUserField(trim((string) ($input['last_name'] ?? '')), 'last_name');
    $email = truncateUserField(trim((string) ($input['email'] ?? '')), 'email');
    $mobile = truncateUserField(trim((string) ($input['mobile'] ?? '')), 'mobile');
    $gender = truncateUserField(trim((string) ($input['gender'] ?? '')), 'gender');
    $baranggay = truncateUserField(trim((string) ($input['baranggay'] ?? '')), 'baranggay');
    $city = truncateUserField(trim((string) ($input['city'] ?? '')), 'city');
    $province = truncateUserField(trim((string) ($input['province'] ?? '')), 'province');
    $birthDateRaw = trim((string) ($input['birth_date'] ?? ''));
    $zipcodeRaw = trim((string) ($input['zipcode'] ?? ''));
    $role = normalizeUserRole((string) ($input['role'] ?? 'staff'));
    $status = normalizeUserStatus((string) ($input['status'] ?? 'active'));
    $password = trim((string) ($input['password'] ?? ''));

    if ($firstName === '' || $lastName === '' || $email === '') {
        throw new RuntimeException('First name, last name, and email are required.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new RuntimeException('Please enter a valid email address.');
    }

    if ($mobile === '') {
        throw new RuntimeException('Mobile number is required.');
    }

    if ($gender === '') {
        $gender = 'Unspecified';
    }

    if ($birthDateRaw === '') {
        throw new RuntimeException('Birth date is required.');
    }

    $birthDate = DateTime::createFromFormat('Y-m-d', $birthDateRaw);
    if (!$birthDate || $birthDate->format('Y-m-d') !== $birthDateRaw) {
        throw new RuntimeException('Birth date must be a valid date (YYYY-MM-DD).');
    }

    if ($baranggay === '' || $city === '' || $province === '') {
        throw new RuntimeException('Barangay, city, and province are required.');
    }

    if ($zipcodeRaw === '' || !preg_match('/^\d+$/', $zipcodeRaw)) {
        throw new RuntimeException('Zip code must be a valid number.');
    }

    $zipcode = (int) $zipcodeRaw;
    if ($zipcode < 0) {
        throw new RuntimeException('Zip code must be zero or greater.');
    }

    if ($isCreate && $password === '') {
        $password = DEFAULT_NEW_USER_PASSWORD;
    }

    return [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'mobile' => $mobile,
        'gender' => $gender,
        'birth_date' => $birthDateRaw,
        'baranggay' => $baranggay,
        'city' => $city,
        'province' => $province,
        'zipcode' => $zipcode,
        'role' => $role,
        'status' => $status,
        'password' => $password,
    ];
}

function fetchUserById(PDO $conn, int $userId): ?array
{
    $columns = getUserColumns($conn);
    $select = [
        'id', 'first_name', 'last_name', 'email', 'role', 'mobile',
        'gender', 'birth_date', 'baranggay', 'city', 'province', 'zipcode',
    ];
    if (in_array('status', $columns, true)) {
        $select[] = 'status';
    }
    if (in_array('notes', $columns, true)) {
        $select[] = 'notes';
    }
    if (in_array('phone', $columns, true)) {
        $select[] = 'phone';
    }
    if (in_array('username', $columns, true)) {
        $select[] = 'username';
    }
    if (in_array('created_at', $columns, true)) {
        $select[] = 'created_at';
    }

    $stmt = $conn->prepare('SELECT ' . implode(', ', $select) . ' FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}

function formatUserRow(array $row, bool $includeStatus = true): array
{
    $first = trim((string) ($row['first_name'] ?? ''));
    $last = trim((string) ($row['last_name'] ?? ''));
    $fullName = trim(($first ? $first . ' ' : '') . $last) ?: 'User';
    $formatted = [
        'id' => (int) ($row['id'] ?? 0),
        'first_name' => $first,
        'last_name' => $last,
        'full_name' => $fullName,
        'email' => (string) ($row['email'] ?? ''),
        'mobile' => (string) ($row['mobile'] ?? $row['phone'] ?? ''),
        'role' => normalizeUserRole((string) ($row['role'] ?? 'staff')),
    ];

    if ($includeStatus) {
        $formatted['status'] = normalizeUserStatus((string) ($row['status'] ?? 'active'));
        $formatted['is_restricted'] = $formatted['status'] === 'restricted';
    }

    if (!empty($row['created_at'])) {
        $formatted['created_at'] = (string) $row['created_at'];
    }

    return $formatted;
}

function syncSessionUser(PDO $conn, int $userId): void
{
    $row = fetchUserById($conn, $userId);
    if (!$row) {
        return;
    }

    $_SESSION['user'] = [
        'id' => (int) $row['id'],
        'first_name' => $row['first_name'],
        'last_name' => $row['last_name'],
        'email' => $row['email'],
        'role' => normalizeUserRole($row['role']),
        'status' => normalizeUserStatus((string) ($row['status'] ?? 'active')),
    ];
}

function emailInUse(PDO $conn, string $email, int $excludeId = 0): bool
{
    $sql = 'SELECT id FROM users WHERE email = :email';
    $params = [':email' => $email];
    if ($excludeId > 0) {
        $sql .= ' AND id != :id';
        $params[':id'] = $excludeId;
    }
    $sql .= ' LIMIT 1';
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
}

function saveManagedUser(PDO $conn, array $input): array
{
    ensureUsersSchema($conn);

    $userId = (int) ($input['user_id'] ?? 0);
    $isCreate = $userId <= 0;
    $data = parseManagedUserInput($input, $isCreate);

    if (emailInUse($conn, $data['email'], $userId)) {
        throw new RuntimeException('That email address is already in use.');
    }

    $passwordColumn = getPasswordColumn($conn);

    if ($userId > 0) {
        if ($userId === currentUserId() && $data['status'] === 'restricted') {
            throw new RuntimeException('You cannot restrict your own account.');
        }

        $fields = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'mobile' => $data['mobile'],
            'gender' => $data['gender'],
            'birth_date' => $data['birth_date'],
            'baranggay' => $data['baranggay'],
            'city' => $data['city'],
            'province' => $data['province'],
            'zipcode' => $data['zipcode'],
            'status' => $data['status'],
        ];

        if ($data['password'] !== '') {
            $fields[$passwordColumn] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $setParts = [];
        $params = [':id' => $userId];
        foreach ($fields as $field => $value) {
            $setParts[] = "$field = :$field";
            $params[":$field"] = $value;
        }

        if (tableHasColumn($conn, 'users', 'updated_At')) {
            $setParts[] = 'updated_At = CURRENT_TIMESTAMP(6)';
        }

        $stmt = $conn->prepare('UPDATE users SET ' . implode(', ', $setParts) . ' WHERE id = :id');
        $stmt->execute($params);

        if ($userId === currentUserId()) {
            syncSessionUser($conn, $userId);
        }

        return ['message' => 'User updated successfully.', 'user_id' => $userId];
    }

    $insertFields = [
        'first_name', 'last_name', 'gender', 'birth_date', 'mobile', 'email',
        'baranggay', 'city', 'province', 'zipcode', $passwordColumn, 'role', 'status',
    ];
    $params = [
        ':first_name' => $data['first_name'],
        ':last_name' => $data['last_name'],
        ':gender' => $data['gender'],
        ':birth_date' => $data['birth_date'],
        ':mobile' => $data['mobile'],
        ':email' => $data['email'],
        ':baranggay' => $data['baranggay'],
        ':city' => $data['city'],
        ':province' => $data['province'],
        ':zipcode' => $data['zipcode'],
        ':' . $passwordColumn => password_hash($data['password'], PASSWORD_DEFAULT),
        ':role' => $data['role'],
        ':status' => $data['status'],
    ];

    $placeholders = array_map(static fn ($field) => ':' . $field, $insertFields);
    $sql = 'INSERT INTO users (' . implode(', ', $insertFields) . ') VALUES (' . implode(', ', $placeholders) . ')';
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    $newId = (int) $conn->lastInsertId();

    return [
        'message' => 'User created successfully. Default password is ' . DEFAULT_NEW_USER_PASSWORD . '.',
        'user_id' => $newId,
    ];
}

function setUserStatus(PDO $conn, int $userId, string $status): array
{
    ensureUsersSchema($conn);
    $status = normalizeUserStatus($status);

    if ($userId <= 0) {
        throw new RuntimeException('Invalid user.');
    }

    if ($userId === currentUserId() && $status === 'restricted') {
        throw new RuntimeException('You cannot restrict your own account.');
    }

    if (!tableHasColumn($conn, 'users', 'status')) {
        throw new RuntimeException('User status is not available in the database.');
    }

    $stmt = $conn->prepare('UPDATE users SET status = :status WHERE id = :id');
    $stmt->execute([':status' => $status, ':id' => $userId]);

    if ($stmt->rowCount() === 0) {
        throw new RuntimeException('User not found.');
    }

    $label = $status === 'restricted' ? 'restricted' : 'activated';

    return ['message' => 'User ' . $label . ' successfully.'];
}

function saveOwnProfile(PDO $conn, int $userId, array $input): array
{
    ensureUsersSchema($conn);

    if ($userId <= 0) {
        throw new RuntimeException('Not logged in.');
    }

    $firstName = trim((string) ($input['first_name'] ?? ''));
    $lastName = trim((string) ($input['last_name'] ?? ''));
    $email = trim((string) ($input['email'] ?? ''));
    $phone = trim((string) ($input['phone'] ?? ''));
    $bio = trim((string) ($input['bio'] ?? ''));
    $password = trim((string) ($input['password'] ?? ''));
    $passwordConfirm = trim((string) ($input['password_confirm'] ?? ''));

    if ($firstName === '' || $lastName === '' || $email === '') {
        throw new RuntimeException('First name, last name, and email are required.');
    }

    if (emailInUse($conn, $email, $userId)) {
        throw new RuntimeException('That email address is already used by another account.');
    }

    if ($password !== '' && $password !== $passwordConfirm) {
        throw new RuntimeException('Password confirmation does not match.');
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

    if ($password !== '') {
        $passwordColumn = getPasswordColumn($conn);
        $updates[$passwordColumn] = password_hash($password, PASSWORD_DEFAULT);
    }

    $setClauses = [];
    $params = [':id' => $userId];
    foreach ($updates as $field => $value) {
        $setClauses[] = "$field = :$field";
        $params[":$field"] = $value;
    }

    $stmt = $conn->prepare('UPDATE users SET ' . implode(', ', $setClauses) . ' WHERE id = :id');
    $stmt->execute($params);

    syncSessionUser($conn, $userId);

    return ['message' => 'Profile updated successfully.'];
}

function listAllUsers(PDO $conn): array
{
    ensureUsersSchema($conn);
    $columns = getUserColumns($conn);
    $select = ['id', 'first_name', 'last_name', 'email', 'role', 'mobile'];
    if (in_array('status', $columns, true)) {
        $select[] = 'status';
    }
    if (in_array('created_at', $columns, true)) {
        $select[] = 'created_at';
    }

    $rows = $conn->query('SELECT ' . implode(', ', $select) . ' FROM users ORDER BY role, last_name, first_name')->fetchAll(PDO::FETCH_ASSOC);

    return array_map(static fn ($row) => formatUserRow($row), $rows);
}

function isLoginAllowedUser(array $user): bool
{
    if (!in_array(normalizeUserRole((string) ($user['role'] ?? '')), USER_ALLOWED_ROLES, true)) {
        return false;
    }

    if (isset($user['status']) && normalizeUserStatus((string) $user['status']) === 'restricted') {
        return false;
    }

    return true;
}
