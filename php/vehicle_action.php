<?php
session_start();
require_once __DIR__ . '/../databases/connection1.php';
require_once __DIR__ . '/db_helpers.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['plates_for'])) {
        $vehicleId = (int) $_GET['plates_for'];
        if ($vehicleId <= 0) {
            echo json_encode(['plates' => []]);
            exit;
        }

        $stmt = $conn->prepare('SELECT make, model FROM vehicles WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $vehicleId]);
        $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$vehicle) {
            echo json_encode(['plates' => []]);
            exit;
        }

        $stmt = $conn->prepare('SELECT id, vehicle_ref, make, model, year, color, plate_no, category, daily_rate, status FROM vehicles WHERE make = :make AND model = :model ORDER BY plate_no ASC');
        $stmt->execute([':make' => $vehicle['make'], ':model' => $vehicle['model']]);
        $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $plates = array_map(function ($row) {
            return [
                'id' => (int) $row['id'],
                'plate' => $row['plate_no'] ?? '',
                'brand' => $row['make'] ?? '',
                'model' => $row['model'] ?? '',
                'type' => $row['category'] ?? '',
                'rate' => isset($row['daily_rate']) ? (float) $row['daily_rate'] : 0.0,
            ];
        }, $matches);

        echo json_encode(['plates' => $plates]);
        exit;
    }

    if (isset($_GET['categories'])) {
        $stmt = $conn->prepare("SELECT DISTINCT category FROM vehicles WHERE status = 'available' AND category IS NOT NULL AND TRIM(category) != '' ORDER BY category ASC");
        $stmt->execute();
        $categories = array_values(array_filter($stmt->fetchAll(PDO::FETCH_COLUMN), fn($c) => $c !== null && trim($c) !== ''));
        echo json_encode(['categories' => $categories]);
        exit;
    }

    $page = max(1, (int) ($_GET['page'] ?? 1));
    $perPage = max(1, min(100, (int) ($_GET['per_page'] ?? 10)));
    $offset = ($page - 1) * $perPage;
    $search = trim((string) ($_GET['search'] ?? ''));

    $where = [];
    $params = [];
    if ($search !== '') {
        $where[] = '(make LIKE :q OR model LIKE :q OR plate_no LIKE :q OR category LIKE :q)';
        $params[':q'] = '%' . $search . '%';
    }

    if (trim((string) ($_GET['available'] ?? '')) === '1') {
        $where[] = 'status = :status';
        $params[':status'] = 'available';
        $where[] = 'id NOT IN (SELECT vehicle_id FROM bookings WHERE status NOT IN (\'done\', \'canceled\'))';
    }

    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $countStmt = $conn->prepare('SELECT COUNT(*) FROM vehicles ' . $whereSql);
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();

    $sql = 'SELECT * FROM vehicles ' . $whereSql . ' ORDER BY id DESC LIMIT :offset, :perPage';
    $stmt = $conn->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->execute();
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['vehicles' => array_map('formatVehicle', $vehicles), 'total' => $total, 'page' => $page, 'per_page' => $perPage]);
    exit;
}

requireAdminSession();

$contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
if (stripos($contentType, 'application/json') !== false) {
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
} else {
    $body = $_POST;
}

$action = trim($body['action'] ?? 'create');
$id = isset($body['id']) ? (int) $body['id'] : null;
$brand = trim($body['brand'] ?? '');
$model = trim($body['model'] ?? '');
$year = isset($body['year']) ? (int) $body['year'] : 0;
$color = trim($body['color'] ?? '');
$type = trim($body['type'] ?? '');
$plate = trim($body['plate'] ?? '');
$seats = array_key_exists('seats', $body) && $body['seats'] !== '' ? (int) $body['seats'] : null;
$fuel = trim($body['fuel'] ?? '');
$trans = trim($body['trans'] ?? '');
$rate = isset($body['rate']) && $body['rate'] !== '' ? floatval($body['rate']) : null;
$mileage = isset($body['mileage']) ? (int) $body['mileage'] : 0;
$status = trim($body['status'] ?? 'available');
$notes = trim($body['notes'] ?? '');

$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

function formatVehicle(array $row): array {
    return [
        'id' => (int) $row['id'],
        'brand' => $row['make'] ?? '',
        'model' => $row['model'] ?? '',
        'year' => (int) $row['year'],
        'color' => $row['color'] ?? '',
        'type' => $row['category'] ?? '',
        'plate' => $row['plate_no'] ?? '',
        'seats' => isset($row['seats']) ? (int) $row['seats'] : 0,
        'fuel' => $row['fuel_type'] ?? '',
        'trans' => $row['transmission'] ?? '',
        'rate' => (float) $row['daily_rate'],
        'mileage' => isset($row['mileage']) ? (int) $row['mileage'] : 0,
        'status' => $row['status'] ?? 'available',
        'notes' => trim($row['remarks'] ?? ''),
        'photo' => !empty($row['main_photo']) ? '/rent/uploads/' . basename($row['main_photo']) : '',
    ];
}

function processUploadedPhoto(array $file, string $uploadDir): string {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Photo upload failed.');
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('Photo must be 5MB or smaller.');
    }
    $allowed = ['image/jpeg' => 'jpg', 'image/jpg' => 'jpg', 'image/png' => 'png'];
    if (!isset($allowed[$file['type']])) {
        throw new RuntimeException('Only JPG and PNG images are allowed.');
    }
    $ext = $allowed[$file['type']];
    $baseName = pathinfo($file['name'], PATHINFO_FILENAME);
    $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $baseName);
    $filename = sprintf('%s_%s.%s', substr($safeName, 0, 50), bin2hex(random_bytes(5)), $ext);
    $target = $uploadDir . $filename;
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('Unable to save uploaded photo.');
    }
    return $filename;
}

$photoFilename = '';
if (!empty($_FILES['photo']) && !empty($_FILES['photo']['name'])) {
    $photoFilename = processUploadedPhoto($_FILES['photo'], $uploadDir);
}

try {
    if ($action === 'delete') {
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing vehicle ID.']);
            exit;
        }

        $stmt = $conn->prepare('SELECT main_photo FROM vehicles WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $existingPhoto = $stmt->fetchColumn();
        if ($existingPhoto) {
            $existingPath = $uploadDir . basename($existingPhoto);
            if (is_file($existingPath)) {
                @unlink($existingPath);
            }
        }

        $stmt = $conn->prepare('DELETE FROM vehicles WHERE id = :id');
        $stmt->execute([':id' => $id]);

        echo json_encode(['success' => true]);
        exit;
    }

    if ($action !== 'create' && $action !== 'update') {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action.']);
        exit;
    }

    if ($brand === '' || $model === '' || $year < 1900 || $type === '' || $plate === '' || $rate === null || $rate <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Please provide brand, model, year, type, plate and rate.']);
        exit;
    }

    $validStatus = in_array($status, ['available', 'rented', 'reserved', 'maintenance'], true) ? $status : 'available';
    $optional = ['seats', 'fuel_type', 'transmission', 'mileage'];
    $columns = [];
    foreach ($optional as $col) {
        $stmtCol = $conn->prepare('SHOW COLUMNS FROM vehicles LIKE :column');
        $stmtCol->execute([':column' => $col]);
        $columns[$col] = (bool) $stmtCol->fetch(PDO::FETCH_ASSOC);
    }

    $insertFields = ['vehicle_ref', 'make', 'model', 'year', 'color', 'plate_no', 'category', 'daily_rate', 'status', 'remarks'];
    $insertValues = [':vehicle_ref' => '', ':make' => $brand, ':model' => $model, ':year' => $year, ':color' => $color, ':plate_no' => $plate, ':category' => $type, ':daily_rate' => $rate, ':status' => $validStatus, ':remarks' => $notes];
    $updateSets = ['make = :make', 'model = :model', 'year = :year', 'color = :color', 'plate_no = :plate_no', 'category = :category', 'daily_rate = :daily_rate', 'status = :status', 'remarks = :remarks'];

    if ($columns['seats']) {
        $insertFields[] = 'seats';
        $insertValues[':seats'] = $seats ?? 0;
        if ($seats !== null) {
            $updateSets[] = 'seats = :seats';
        }
    }
    if ($columns['fuel_type']) {
        $insertFields[] = 'fuel_type';
        $insertValues[':fuel_type'] = $fuel;
        $updateSets[] = 'fuel_type = :fuel_type';
    }
    if ($columns['transmission']) {
        $insertFields[] = 'transmission';
        $insertValues[':transmission'] = $trans;
        $updateSets[] = 'transmission = :transmission';
    }
    if ($columns['mileage']) {
        $insertFields[] = 'mileage';
        $insertValues[':mileage'] = $mileage;
        $updateSets[] = 'mileage = :mileage';
    }

    if ($photoFilename) {
        $insertFields[] = 'main_photo';
        $insertValues[':main_photo'] = $photoFilename;
        $updateSets[] = 'main_photo = :main_photo';
    }

    $id = runInTransaction($conn, function () use (
        $conn,
        $action,
        $id,
        $insertFields,
        $insertValues,
        $updateSets,
        $brand,
        $model,
        $plate,
        $rate,
        $photoFilename,
        $uploadDir
    ) {
        if ($action === 'create') {
            $maxId = (int) $conn->query('SELECT MAX(id) FROM vehicles')->fetchColumn();
            $vehicleRef = 'VH-' . str_pad((string) ($maxId + 1), 4, '0', STR_PAD_LEFT);
            $insertValues[':vehicle_ref'] = $vehicleRef;

            $placeholders = array_map(fn ($field) => ':' . $field, $insertFields);
            $stmt = $conn->prepare('INSERT INTO vehicles (' . implode(', ', $insertFields) . ') VALUES (' . implode(', ', $placeholders) . ')');
            $stmt->execute($insertValues);

            return (int) $conn->lastInsertId();
        }

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing vehicle ID.']);
            exit;
        }

        $stmtExisting = $conn->prepare('SELECT daily_rate, main_photo FROM vehicles WHERE id = :id LIMIT 1');
        $stmtExisting->execute([':id' => $id]);
        $existingVehicle = $stmtExisting->fetch(PDO::FETCH_ASSOC);
        $existingPhoto = $existingVehicle ? $existingVehicle['main_photo'] : null;

        if ($photoFilename && $existingPhoto) {
            $existingPath = $uploadDir . basename($existingPhoto);
            if (is_file($existingPath)) {
                @unlink($existingPath);
            }
        }

        $stmt = $conn->prepare('UPDATE vehicles SET ' . implode(', ', $updateSets) . ' WHERE id = :id');
        $updateValues = $insertValues;
        unset($updateValues[':vehicle_ref']);
        $updateValues[':id'] = $id;
        $stmt->execute($updateValues);

        if ($rate !== null) {
            $vehicleName = trim($brand . ' ' . $model);
            $stmtBookingUpdate = $conn->prepare(
                'UPDATE bookings
                 SET rate = :rate,
                     amount = ROUND(days * :rate, 2)
                 WHERE status NOT IN (\'done\', \'canceled\')
                   AND vehicle_id = :vehicle_id'
            );
            $stmtBookingUpdate->execute([
                ':rate' => $rate,
                ':vehicle_id' => $id,
            ]);
        }

        return $id;
    });

    $select = 'SELECT id, vehicle_ref, make, model, year, color, plate_no, category, daily_rate, status, main_photo, remarks';
    if ($columns['seats']) $select .= ', seats';
    if ($columns['fuel_type']) $select .= ', fuel_type';
    if ($columns['transmission']) $select .= ', transmission';
    if ($columns['mileage']) $select .= ', mileage';
    $select .= ' FROM vehicles WHERE id = :id LIMIT 1';

    $stmt = $conn->prepare($select);
    $stmt->execute([':id' => $id]);
    $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$vehicle) {
        http_response_code(500);
        echo json_encode(['error' => 'Unable to retrieve saved vehicle.']);
        exit;
    }

    echo json_encode(['vehicle' => formatVehicle($vehicle)]);
    exit;
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to save vehicle.']);
    exit;
}
