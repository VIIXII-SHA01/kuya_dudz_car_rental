<?php
session_start();
require_once __DIR__ . '/../databases/connection1.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true) ?? [];
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
    ];
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
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to save vehicle.']);
    exit;
}
