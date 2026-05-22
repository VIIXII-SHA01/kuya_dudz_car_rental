<?php
session_start();
require_once __DIR__ . '/../databases/connection1.php';
require_once __DIR__ . '/db_helpers.php';

header('Content-Type: application/json');

requireAdminSession();

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

function formatDriver(array $row): array {
    $expiry = $row['license_expiry'] ?? null;
    $expired = false;
    if (!empty($expiry)) {
        $today = new DateTimeImmutable('today');
        try {
            $d = new DateTimeImmutable($expiry);
            $expired = $d < $today;
        } catch (Exception $e) {
            $expired = false;
        }
    }

    return [
        'id' => (int) $row['id'],
        'driver_ref' => $row['driver_ref'] ?? '',
        'fname' => $row['first_name'] ?? '',
        'lname' => $row['last_name'] ?? '',
        'email' => $row['email'] ?? '',
        'phone' => $row['phone'] ?? '',
        'dob' => $row['dob'] ?? '',
        'address' => $row['address'] ?? '',
        'license' => $row['license_no'] ?? '',
        'expiry' => $expiry ?? '',
        'exp' => isset($row['experience_years']) ? (int) $row['experience_years'] : 0,
        'lictype' => $row['license_type'] ?? 'Professional',
        'status' => trim((string) ($row['status'] ?? '')) !== '' ? $row['status'] : 'available',
        'notes' => trim($row['notes'] ?? ''),
        'avatar_bg' => $row['avatar_bg'] ?? 'linear-gradient(135deg,#E8341A,#F5642A)',
        'photo' => $row['photo'] ?? null,
        'documents' => isset($row['documents']) ? json_decode($row['documents'], true) ?? null : null,
        'rating' => isset($row['rating']) ? (float) $row['rating'] : 5.0,
        'trips' => isset($row['trips']) ? (int) $row['trips'] : 0,
        'license_expired' => $expired,
    ];
}

try {
    if ($requestMethod === 'GET') {
        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            $stmt = $conn->prepare('SELECT * FROM drivers WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $id]);
            $driver = $stmt->fetch(PDO::FETCH_ASSOC);
            if (! $driver) {
                http_response_code(404);
                echo json_encode(['error' => 'Driver not found.']);
                exit;
            }
            echo json_encode(['driver' => formatDriver($driver)]);
            exit;
        }

        if (isset($_GET['for_booking'])) {
            reconcileStuckRentedDrivers($conn);

            $search = trim((string) ($_GET['search'] ?? ''));
            $excludeBookingRef = trim((string) ($_GET['exclude_booking_ref'] ?? ''));
            $includeDriverId = (int) ($_GET['include_driver_id'] ?? 0);
            $perPage = max(1, min(50, (int) ($_GET['per_page'] ?? 20)));

            $where = ["status NOT IN ('suspended', 'rented', 'dayoff', 'on-duty', 'off-duty')"];
            $params = [];
            if ($includeDriverId > 0) {
                $where = ['(status = \'available\' OR id = :include_driver_id)'];
                $params[':include_driver_id'] = $includeDriverId;
            }

            if ($search !== '') {
                $where[] = '(first_name LIKE :q OR last_name LIKE :q OR driver_ref LIKE :q OR license_no LIKE :q)';
                $params[':q'] = '%' . $search . '%';
            }

            $whereSql = 'WHERE ' . implode(' AND ', $where);

            $sql = "SELECT * FROM drivers $whereSql ORDER BY first_name ASC, last_name ASC LIMIT :perPage";
            $stmt = $conn->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $drivers = [];
            foreach ($rows as $row) {
                $formatted = formatDriver($row);
                $status = normalizeDriverStatus((string) ($formatted['status'] ?? ''));
                $selectable = isDriverSelectableForBooking((string) ($row['status'] ?? ''))
                    || ($includeDriverId > 0 && (int) $row['id'] === $includeDriverId);

                if (!$selectable) {
                    continue;
                }

                $occupied = $status === 'rented';
                $occupiedRef = '';
                if ($occupied && tableHasColumn($conn, 'bookings', 'driver_id')) {
                    $occSql = "SELECT booking_ref FROM bookings WHERE driver_id = :driver_id AND status IN ('pending','active','overdue')";
                    $occParams = [':driver_id' => (int) $row['id']];
                    if ($excludeBookingRef !== '') {
                        $occSql .= ' AND booking_ref != :exclude_ref';
                        $occParams[':exclude_ref'] = $excludeBookingRef;
                    }
                    $occSql .= ' LIMIT 1';
                    $occStmt = $conn->prepare($occSql);
                    $occStmt->execute($occParams);
                    $occRow = $occStmt->fetch(PDO::FETCH_ASSOC);
                    if ($occRow) {
                        $occupiedRef = (string) ($occRow['booking_ref'] ?? '');
                    }
                }

                $formatted['occupied'] = $occupied;
                $formatted['occupied_ref'] = $occupiedRef;
                $formatted['availability'] = $occupied
                    ? ('Rented' . ($occupiedRef !== '' ? ' · ' . $occupiedRef : ''))
                    : 'Available';
                $drivers[] = $formatted;
            }

            echo json_encode(['drivers' => $drivers]);
            exit;
        }

        // List with pagination and filters
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(1, min(100, (int) ($_GET['per_page'] ?? 20)));
        $offset = ($page - 1) * $perPage;

        $where = [];
        $params = [];

        if (!empty($_GET['status'])) {
            $where[] = 'status = :status';
            $params[':status'] = $_GET['status'];
        }

        if (!empty($_GET['search'])) {
            $where[] = '(first_name LIKE :q OR last_name LIKE :q OR driver_ref LIKE :q OR license_no LIKE :q)';
            $params[':q'] = '%' . $_GET['search'] . '%';
        }

        if (!empty($_GET['exp_level'])) {
            $expLevel = strtolower($_GET['exp_level']);
            if ($expLevel === 'junior') {
                $where[] = 'experience_years < 3';
            } elseif ($expLevel === 'mid') {
                $where[] = 'experience_years >= 3 AND experience_years < 7';
            } elseif ($expLevel === 'senior') {
                $where[] = 'experience_years >= 7';
            }
        } elseif (!empty($_GET['exp_min'])) {
            $where[] = 'experience_years >= :exp_min';
            $params[':exp_min'] = (int) $_GET['exp_min'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $countStmt = $conn->prepare("SELECT COUNT(*) FROM drivers $whereSql");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT * FROM drivers $whereSql ORDER BY id DESC LIMIT :offset, :perPage";
        $stmt = $conn->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->bindValue(':perPage', (int) $perPage, PDO::PARAM_INT);
        $stmt->execute();
        $drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $formatted = array_map('formatDriver', $drivers);
        echo json_encode([
            'drivers' => $formatted,
            'data' => $formatted,
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

    // Special POST actions that don't require the full create/update validations
    if (in_array($action, ['upload_photo', 'upload_document', 'increment_trip', 'update_rating', 'delete'], true) === false && $action !== 'create' && $action !== 'update') {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action.']);
        exit;
    }

    // Handle delete early
    if ($action === 'delete') {
        $id = isset($body['id']) ? (int) $body['id'] : null;
        if (! $id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing driver ID.']);
            exit;
        }

        $stmt = $conn->prepare('DELETE FROM drivers WHERE id = :id');
        $stmt->execute([':id' => $id]);

        echo json_encode(['success' => true]);
        exit;
    }

    // Upload photo
    if ($action === 'upload_photo') {
        $id = isset($body['id']) ? (int) $body['id'] : null;
        if (! $id || ! isset($_FILES['photo'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing driver ID or photo file.']);
            exit;
        }

        $file = $_FILES['photo'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['error' => 'Upload failed.']);
            exit;
        }

        if ($file['size'] > 10 * 1024 * 1024) {
            http_response_code(400);
            echo json_encode(['error' => 'Photo must be 10MB or smaller.']);
            exit;
        }

        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (! isset($allowed[$mime])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid image type.']);
            exit;
        }

        $ext = $allowed[$mime];
        $dir = __DIR__ . '/../uploads/drivers/';
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $filename = sprintf('driver_%d_%s.%s', $id, bin2hex(random_bytes(6)), $ext);
        $dest = $dir . $filename;
        if (! move_uploaded_file($file['tmp_name'], $dest)) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save image.']);
            exit;
        }

        $rel = 'uploads/drivers/' . $filename;
        $stmt = $conn->prepare('UPDATE drivers SET photo = :photo WHERE id = :id');
        $stmt->execute([':photo' => $rel, ':id' => $id]);

        $stmt = $conn->prepare('SELECT * FROM drivers WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $driver = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['driver' => formatDriver($driver)]);
        exit;
    }

    // Upload document
    if ($action === 'upload_document') {
        $id = isset($body['id']) ? (int) $body['id'] : null;
        if (! $id || ! isset($_FILES['document'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing driver ID or document file.']);
            exit;
        }

        $file = $_FILES['document'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['error' => 'Upload failed.']);
            exit;
        }

        if ($file['size'] > 10 * 1024 * 1024) {
            http_response_code(400);
            echo json_encode(['error' => 'Document must be 10MB or smaller.']);
            exit;
        }

        $allowed = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (! isset($allowed[$mime])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid document type.']);
            exit;
        }

        $ext = $allowed[$mime];
        $dir = __DIR__ . '/../uploads/drivers/docs/';
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $filename = sprintf('doc_%d_%s.%s', $id, bin2hex(random_bytes(6)), $ext);
        $dest = $dir . $filename;
        if (! move_uploaded_file($file['tmp_name'], $dest)) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save document.']);
            exit;
        }

        $rel = 'uploads/drivers/docs/' . $filename;

        // Append to documents JSON array
        $stmt = $conn->prepare('SELECT documents FROM drivers WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $docs = [];
        if ($row && !empty($row['documents'])) {
            $docs = json_decode($row['documents'], true) ?: [];
        }
        $docs[] = ['file' => $rel, 'uploaded_at' => date('c')];
        $stmt = $conn->prepare('UPDATE drivers SET documents = :documents WHERE id = :id');
        $stmt->execute([':documents' => json_encode($docs), ':id' => $id]);

        $stmt = $conn->prepare('SELECT * FROM drivers WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $driver = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['driver' => formatDriver($driver)]);
        exit;
    }

    // Increment trip
    if ($action === 'increment_trip') {
        $id = isset($body['id']) ? (int) $body['id'] : null;
        if (! $id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing driver ID.']);
            exit;
        }
        $conn->beginTransaction();
        $stmt = $conn->prepare('UPDATE drivers SET trips = trips + 1 WHERE id = :id');
        $stmt->execute([':id' => $id]);
        if (isset($body['rating'])) {
            $rating = max(0.0, min(5.0, (float) $body['rating']));
            $stmt = $conn->prepare('UPDATE drivers SET rating = :rating WHERE id = :id');
            $stmt->execute([':rating' => $rating, ':id' => $id]);
        }
        $conn->commit();

        $stmt = $conn->prepare('SELECT * FROM drivers WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $driver = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['driver' => formatDriver($driver)]);
        exit;
    }

    // Update rating explicitly
    if ($action === 'update_rating') {
        $id = isset($body['id']) ? (int) $body['id'] : null;
        $rating = isset($body['rating']) ? (float) $body['rating'] : null;
        if (! $id || $rating === null) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing driver ID or rating.']);
            exit;
        }
        $rating = max(0.0, min(5.0, $rating));
        $stmt = $conn->prepare('UPDATE drivers SET rating = :rating WHERE id = :id');
        $stmt->execute([':rating' => $rating, ':id' => $id]);

        $stmt = $conn->prepare('SELECT * FROM drivers WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $driver = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['driver' => formatDriver($driver)]);
        exit;
    }

    // From here on: create or update
    $id = isset($body['id']) ? (int) $body['id'] : null;
    $firstName = trim($body['first_name'] ?? '');
    $lastName = trim($body['last_name'] ?? '');
    $email = trim($body['email'] ?? '');
    $phone = trim($body['phone'] ?? '');
    $dob = trim($body['dob'] ?? '');
    $address = trim($body['address'] ?? '');
    $licenseNo = trim($body['license_no'] ?? '');
    $expiry = trim($body['license_expiry'] ?? '');
    $experience = isset($body['experience_years']) ? (int) $body['experience_years'] : 0;
    $licenseType = trim($body['license_type'] ?? 'Professional');
    $status = trim($body['status'] ?? 'available');
    $notes = trim($body['notes'] ?? '');
    $avatarBg = trim($body['avatar_bg'] ?? 'linear-gradient(135deg,#E8341A,#F5642A)');

    if ($firstName === '' || $lastName === '' || $licenseNo === '') {
        http_response_code(400);
        echo json_encode(['error' => 'First name, last name, and license number are required.']);
        exit;
    }

    $validStatuses = ['available', 'rented', 'dayoff', 'suspended', 'on-duty', 'off-duty'];
    $validLicenseTypes = ['Professional', 'Non-Professional'];
    $status = in_array($status, $validStatuses, true) ? normalizeDriverStatus($status) : 'available';
    if ($status === 'rented' && $action === 'create') {
        $status = 'available';
    }
    $licenseType = in_array($licenseType, $validLicenseTypes, true) ? $licenseType : 'Professional';

    if ($action === 'create') {
        $maxId = (int) $conn->query('SELECT MAX(id) FROM drivers')->fetchColumn();
        $driverRef = 'DRV-' . str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);

        $stmt = $conn->prepare(
            'INSERT INTO drivers (driver_ref, first_name, last_name, email, phone, dob, address, license_no, license_expiry, experience_years, license_type, status, notes, avatar_bg, rating, trips) VALUES (:driver_ref, :first_name, :last_name, :email, :phone, :dob, :address, :license_no, :license_expiry, :experience_years, :license_type, :status, :notes, :avatar_bg, :rating, :trips)'
        );
        $stmt->execute([
            ':driver_ref' => $driverRef,
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':email' => $email,
            ':phone' => $phone,
            ':dob' => $dob ?: null,
            ':address' => $address,
            ':license_no' => $licenseNo,
            ':license_expiry' => $expiry ?: null,
            ':experience_years' => $experience,
            ':license_type' => $licenseType,
            ':status' => $status,
            ':notes' => $notes,
            ':avatar_bg' => $avatarBg,
            ':rating' => 5.0,
            ':trips' => 0,
        ]);
        $id = (int) $conn->lastInsertId();
    } else {
        if (! $id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing driver ID.']);
            exit;
        }

        $stmt = $conn->prepare(
            'UPDATE drivers SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, dob = :dob, address = :address, license_no = :license_no, license_expiry = :license_expiry, experience_years = :experience_years, license_type = :license_type, status = :status, notes = :notes, avatar_bg = :avatar_bg WHERE id = :id'
        );
        $stmt->execute([
            ':first_name' => $firstName,
            ':last_name' => $lastName,
            ':email' => $email,
            ':phone' => $phone,
            ':dob' => $dob ?: null,
            ':address' => $address,
            ':license_no' => $licenseNo,
            ':license_expiry' => $expiry ?: null,
            ':experience_years' => $experience,
            ':license_type' => $licenseType,
            ':status' => $status,
            ':notes' => $notes,
            ':avatar_bg' => $avatarBg,
            ':id' => $id,
        ]);
    }

    $stmt = $conn->prepare('SELECT * FROM drivers WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $driver = $stmt->fetch(PDO::FETCH_ASSOC);
    if (! $driver) {
        http_response_code(500);
        echo json_encode(['error' => 'Unable to retrieve saved driver.']);
        exit;
    }

    echo json_encode(['driver' => formatDriver($driver)]);
    exit;
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to process the driver request.']);
    exit;
}
