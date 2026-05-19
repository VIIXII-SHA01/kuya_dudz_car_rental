<?php
session_start();
require_once __DIR__ . '/../databases/connection1.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) && !isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized.']);
    exit;
}

$requestMethod = $_SERVER['REQUEST_METHOD'];
$contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';

$body = [];
if ($requestMethod === 'POST') {
    if (stripos($contentType, 'application/json') !== false) {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
    } else {
        $body = $_POST;
    }
}

$action = trim($body['action'] ?? ($requestMethod === 'GET' ? 'list' : 'create'));

function formatCustomer(array $row): array {
    $documents = [];
    if (!empty($row['documents'])) {
        $decoded = json_decode($row['documents'], true);
        if (is_array($decoded)) {
            foreach ($decoded as $doc) {
                if (is_string($doc)) {
                    $documents[] = ['file' => '/rent/' . ltrim($doc, '/\\')];
                } elseif (is_array($doc) && isset($doc['file'])) {
                    $documents[] = ['file' => '/rent/' . ltrim($doc['file'], '/\\')];
                }
            }
        }
    }
    return [
        'id' => (int) ($row['id'] ?? 0),
        'ref' => $row['customer_ref'] ?? '',
        'fname' => $row['first_name'] ?? '',
        'lname' => $row['last_name'] ?? '',
        'email' => $row['email'] ?? '',
        'phone' => $row['phone'] ?? '',
        'dob' => $row['dob'] ?? '',
        'address' => $row['address'] ?? '',
        'idtype' => $row['id_type'] ?? $row['idtype'] ?? "Driver's License",
        'idnum' => $row['id_number'] ?? $row['idnum'] ?? '',
        'emergency' => $row['emergency_contact'] ?? $row['emergency'] ?? '',
        'tier' => $row['tier'] ?? $row['membership_tier'] ?? 'Basic',
        'status' => $row['status'] ?? 'active',
        'rentals' => isset($row['rentals']) ? (int) $row['rentals'] : 0,
        'spent' => isset($row['spent']) ? (float) $row['spent'] : 0.0,
        'joined' => $row['created_at'] ?? $row['joined'] ?? '',
        'notes' => trim($row['notes'] ?? ''),
        'bg' => $row['avatar_bg'] ?? 'linear-gradient(135deg,#3D8FBE,#3DBE7A)',
        'photo' => !empty($row['profile_photo']) ? '/rent/' . ltrim($row['profile_photo'], '/\\') : null,
        'documents' => $documents,
    ];
}

function generateCustomerRef(PDO $conn): string {
    do {
        $ref = 'CUS-' . strtoupper(bin2hex(random_bytes(3)));
        $stmt = $conn->prepare('SELECT COUNT(*) FROM customers WHERE customer_ref = :ref');
        $stmt->execute([':ref' => $ref]);
        $exists = (int) $stmt->fetchColumn() > 0;
    } while ($exists);
    return $ref;
}

function getCustomerById(PDO $conn, int $id): ?array {
    $stmt = $conn->prepare('SELECT * FROM customers WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    return $customer ?: null;
}

function customerColumnExists(PDO $conn, string $column): bool {
    try {
        $stmt = $conn->query("SHOW COLUMNS FROM customers LIKE " . $conn->quote($column));
        return (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}

function ensureCustomerColumn(PDO $conn, string $column, string $definition): bool {
    if (customerColumnExists($conn, $column)) {
        return true;
    }
    try {
        $conn->exec("ALTER TABLE customers ADD COLUMN $column $definition");
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function getCustomerTierColumn(PDO $conn): string {
    static $column = null;
    if ($column !== null) {
        return $column;
    }

    try {
        $stmt = $conn->query("SHOW COLUMNS FROM customers LIKE 'tier'");
        if ($stmt && $stmt->fetch(PDO::FETCH_ASSOC)) {
            $column = 'tier';
            return $column;
        }
    } catch (PDOException $e) {
        // ignore; fallback to other column names
    }

    try {
        $stmt = $conn->query("SHOW COLUMNS FROM customers LIKE 'membership_tier'");
        if ($stmt && $stmt->fetch(PDO::FETCH_ASSOC)) {
            $column = 'membership_tier';
            return $column;
        }
    } catch (PDOException $e) {
        // ignore; fallback
    }

    $column = 'tier';
    return $column;
}

function validateUploadFile(array $file, array $allowedMimeTypes, int $maxBytes): array {
    if (! isset($file['error']) || ! isset($file['tmp_name']) || ! isset($file['size'])) {
        throw new RuntimeException('Invalid upload payload.');
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('File exceeds the allowed size.');
            case UPLOAD_ERR_PARTIAL:
                throw new RuntimeException('File upload was incomplete.');
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file was uploaded.');
            default:
                throw new RuntimeException('Upload failed with error code ' . $file['error'] . '.');
        }
    }

    if (! is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('Invalid uploaded file.');
    }

    if ($file['size'] > $maxBytes) {
        throw new RuntimeException('File must be ' . ($maxBytes / 1024 / 1024) . 'MB or smaller.');
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (! $mime || ! isset($allowedMimeTypes[$mime])) {
        throw new RuntimeException('Invalid file type.');
    }

    return [
        'mime' => $mime,
        'ext' => $allowedMimeTypes[$mime],
    ];
}

try {
    if ($requestMethod === 'GET') {
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            $stmt = $conn->prepare('SELECT * FROM customers WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $id]);
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);
            if (! $customer) {
                http_response_code(404);
                echo json_encode(['error' => 'Customer not found.']);
                exit;
            }
            echo json_encode(['customer' => formatCustomer($customer)]);
            exit;
        }

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(1, min(100, (int) ($_GET['per_page'] ?? 20)));
        $offset = ($page - 1) * $perPage;

        $where = [];
        $params = [];

        if (!empty($_GET['status'])) {
            $where[] = 'status = :status';
            $params[':status'] = $_GET['status'];
        }

        if (!empty($_GET['tier'])) {
            $tierColumn = getCustomerTierColumn($conn);
            $where[] = "$tierColumn = :tier";
            $params[':tier'] = $_GET['tier'];
        }

        if (!empty($_GET['search'])) {
            $where[] = '(first_name LIKE :q OR last_name LIKE :q OR email LIKE :q OR phone LIKE :q OR address LIKE :q)';
            $params[':q'] = '%' . $_GET['search'] . '%';
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $countStmt = $conn->prepare("SELECT COUNT(*) FROM customers $whereSql");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT * FROM customers $whereSql ORDER BY id DESC LIMIT :offset, :perPage";
        $stmt = $conn->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->execute();
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'customers' => array_map('formatCustomer', $customers),
            'data' => array_map('formatCustomer', $customers),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
        ]);
        exit;
    }

    if ($requestMethod !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed.']);
        exit;
    }

    if (!in_array($action, ['create', 'update', 'delete', 'upload_photo', 'upload_document'], true)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action.']);
        exit;
    }

    if ($action === 'delete') {
        $id = isset($body['id']) ? (int) $body['id'] : null;
        if (! $id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing customer ID.']);
            exit;
        }

        $stmt = $conn->prepare('DELETE FROM customers WHERE id = :id');
        $stmt->execute([':id' => $id]);

        echo json_encode(['success' => true]);
        exit;
    }

    if ($action === 'upload_photo') {
        $id = isset($body['id']) ? (int) $body['id'] : null;
        if (! $id || ! isset($_FILES['photo'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing customer ID or photo file.']);
            exit;
        }

        $customer = getCustomerById($conn, $id);
        if (! $customer) {
            http_response_code(404);
            echo json_encode(['error' => 'Customer not found.']);
            exit;
        }

        try {
            $validation = validateUploadFile($_FILES['photo'], ['image/jpeg' => 'jpg', 'image/png' => 'png'], 10 * 1024 * 1024);
        } catch (RuntimeException $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }

        $dir = __DIR__ . '/../uploads/customers/';
        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create upload directory.']);
            exit;
        }

        $filename = sprintf('customer_%d_%s.%s', $id, bin2hex(random_bytes(6)), $validation['ext']);
        $dest = $dir . $filename;
        if (! move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save image.']);
            exit;
        }

        $rel = 'uploads/customers/' . $filename;
        $stmt = $conn->prepare('UPDATE customers SET profile_photo = :photo WHERE id = :id');
        $stmt->execute([':photo' => $rel, ':id' => $id]);

        $customer = getCustomerById($conn, $id);
        echo json_encode(['customer' => formatCustomer($customer)]);
        exit;
    }

    if ($action === 'upload_document') {
        $id = isset($body['id']) ? (int) $body['id'] : null;
        if (! $id || ! isset($_FILES['document'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing customer ID or document file.']);
            exit;
        }

        $customer = getCustomerById($conn, $id);
        if (! $customer) {
            http_response_code(404);
            echo json_encode(['error' => 'Customer not found.']);
            exit;
        }

        if (! ensureCustomerColumn($conn, 'documents', 'TEXT DEFAULT NULL')) {
            http_response_code(500);
            echo json_encode(['error' => 'Unable to store document metadata.']);
            exit;
        }

        try {
            $validation = validateUploadFile($_FILES['document'], ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png'], 10 * 1024 * 1024);
        } catch (RuntimeException $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }

        $dir = __DIR__ . '/../uploads/customers/';
        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create upload directory.']);
            exit;
        }

        $filename = sprintf('customer_doc_%d_%s.%s', $id, bin2hex(random_bytes(6)), $validation['ext']);
        $dest = $dir . $filename;
        if (! move_uploaded_file($_FILES['document']['tmp_name'], $dest)) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save document.']);
            exit;
        }

        $relativePath = 'uploads/customers/' . $filename;
        $stmt = $conn->prepare('SELECT documents FROM customers WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $current = $stmt->fetchColumn();
        $documents = [];
        if (!empty($current)) {
            $decoded = json_decode($current, true);
            if (is_array($decoded)) {
                $documents = $decoded;
            }
        }
        $documents[] = $relativePath;

        $stmt = $conn->prepare('UPDATE customers SET documents = :documents WHERE id = :id');
        $stmt->execute([':documents' => json_encode($documents), ':id' => $id]);

        $customer = getCustomerById($conn, $id);
        echo json_encode(['customer' => formatCustomer($customer)]);
        exit;
    }

    $id = isset($body['id']) ? (int) $body['id'] : null;
    $firstName = trim($body['first_name'] ?? '');
    $lastName = trim($body['last_name'] ?? '');
    $email = trim($body['email'] ?? '');
    $phone = trim($body['phone'] ?? '');
    $dob = trim($body['dob'] ?? '');
    $address = trim($body['address'] ?? '');
    $idType = trim($body['idtype'] ?? $body['id_type'] ?? "Driver's License");
    $idNum = trim($body['idnum'] ?? $body['id_number'] ?? '');
    $emergency = trim($body['emergency'] ?? $body['emergency_contact'] ?? '');
    $tier = trim($body['tier'] ?? 'Basic');
    $status = trim($body['status'] ?? 'active');
    $notes = trim($body['notes'] ?? '');
    $avatarBg = trim($body['avatar_bg'] ?? 'linear-gradient(135deg,#3D8FBE,#3DBE7A)');

    if ($firstName === '' || $lastName === '' || $email === '') {
        http_response_code(400);
        echo json_encode(['error' => 'First name, last name, and email are required.']);
        exit;
    }

    $tierColumn = getCustomerTierColumn($conn);

    if ($action === 'create') {
        $customerRef = generateCustomerRef($conn);
        $stmt = $conn->prepare(
            "INSERT INTO customers (customer_ref, first_name, last_name, email, phone, dob, address, id_type, id_number, emergency_contact, $tierColumn, status, notes, avatar_bg, rentals, spent) VALUES (:customer_ref, :first_name, :last_name, :email, :phone, :dob, :address, :id_type, :id_number, :emergency_contact, :tier, :status, :notes, :avatar_bg, :rentals, :spent)"
        );
        $stmt->execute([
            ':customer_ref' => $customerRef,
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':email' => $email,
            ':phone' => $phone,
            ':dob' => $dob ?: null,
            ':address' => $address,
            ':id_type' => $idType,
            ':id_number' => $idNum,
            ':emergency_contact' => $emergency,
            ':tier' => $tier,
            ':status' => $status,
            ':notes' => $notes,
            ':avatar_bg' => $avatarBg,
            ':rentals' => 0,
            ':spent' => 0.0,
        ]);
        $id = (int) $conn->lastInsertId();
    } else {
        if (! $id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing customer ID.']);
            exit;
        }

        $stmt = $conn->prepare(
            "UPDATE customers SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, dob = :dob, address = :address, id_type = :id_type, id_number = :id_number, emergency_contact = :emergency_contact, $tierColumn = :tier, status = :status, notes = :notes, avatar_bg = :avatar_bg WHERE id = :id"
        );
        $stmt->execute([
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':email' => $email,
            ':phone' => $phone,
            ':dob' => $dob ?: null,
            ':address' => $address,
            ':id_type' => $idType,
            ':id_number' => $idNum,
            ':emergency_contact' => $emergency,
            ':tier' => $tier,
            ':status' => $status,
            ':notes' => $notes,
            ':avatar_bg' => $avatarBg,
            ':id' => $id,
        ]);
    }

    $stmt = $conn->prepare('SELECT * FROM customers WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    if (! $customer) {
        http_response_code(500);
        echo json_encode(['error' => 'Unable to retrieve saved customer.']);
        exit;
    }

    echo json_encode(['customer' => formatCustomer($customer)]);
    exit;
} catch (Throwable $e) {
    error_log('customer_action error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Unable to process the customer request. ' . $e->getMessage()]);
    exit;
}
