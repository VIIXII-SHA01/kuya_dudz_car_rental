<?php
session_start();
require_once __DIR__ . '/../databases/connection1.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

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
$seats = isset($body['seats']) ? (int) $body['seats'] : 0;
$fuel = trim($body['fuel'] ?? '');
$trans = trim($body['trans'] ?? '');
$rate = isset($body['rate']) ? floatval($body['rate']) : 0.0;
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

    if ($brand === '' || $model === '' || $year < 1900 || $type === '' || $plate === '' || $seats < 1 || $rate <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Please provide brand, model, year, type, plate, seats and rate.']);
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
        $insertValues[':seats'] = $seats;
        $updateSets[] = 'seats = :seats';
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

    if ($action === 'create') {
        $maxId = (int) $conn->query('SELECT MAX(id) FROM vehicles')->fetchColumn();
        $vehicleRef = 'VH-' . str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);
        $insertValues[':vehicle_ref'] = $vehicleRef;

        $placeholders = array_map(fn($field) => ':' . $field, $insertFields);
        $stmt = $conn->prepare('INSERT INTO vehicles (' . implode(', ', $insertFields) . ') VALUES (' . implode(', ', $placeholders) . ')');
        $stmt->execute($insertValues);
        $id = (int) $conn->lastInsertId();
    } else {
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing vehicle ID.']);
            exit;
        }

        if ($photoFilename) {
            $stmtExisting = $conn->prepare('SELECT main_photo FROM vehicles WHERE id = :id');
            $stmtExisting->execute([':id' => $id]);
            $existingPhoto = $stmtExisting->fetchColumn();
            if ($existingPhoto) {
                $existingPath = $uploadDir . basename($existingPhoto);
                if (is_file($existingPath)) {
                    @unlink($existingPath);
                }
            }
        }

        $stmt = $conn->prepare('UPDATE vehicles SET ' . implode(', ', $updateSets) . ' WHERE id = :id');
        $insertValues[':id'] = $id;
        $stmt->execute($insertValues);
    }

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
