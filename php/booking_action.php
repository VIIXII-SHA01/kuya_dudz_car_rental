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
$bookingRef = trim($body['booking_ref'] ?? '');
$customer = trim($body['customer'] ?? '');
$customer_ref = trim($body['customer_ref'] ?? '');
$email = trim($body['email'] ?? '');
$vehicle = trim($body['vehicle'] ?? '');
$vehicle_type = trim($body['vehicle_type'] ?? '');
$driver_type = trim($body['driver_type'] ?? '');
$location = trim($body['location'] ?? '');
$plate = trim($body['plate'] ?? '');
$pickup_date = trim($body['pickup_date'] ?? '');
$return_date = trim($body['return_date'] ?? '');
$amount = isset($body['amount']) ? floatval($body['amount']) : 0.0;
$rate = isset($body['rate']) ? floatval($body['rate']) : 0.0;
$status = trim($body['status'] ?? 'pending');
$notes = trim($body['notes'] ?? '');

function findCustomerId(PDO $conn, string $customerName, string $customerRef, string $email): ?int {
    if ($customerRef !== '') {
        $stmt = $conn->prepare('SELECT id FROM customers WHERE customer_ref = :customer_ref LIMIT 1');
        $stmt->execute([':customer_ref' => $customerRef]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return (int) $row['id'];
        }
    }

    if ($email !== '') {
        $stmt = $conn->prepare('SELECT id FROM customers WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return (int) $row['id'];
        }
    }

    if ($customerName !== '') {
        $parts = preg_split('/\s+/', $customerName, 2, PREG_SPLIT_NO_EMPTY);
        if (count($parts) === 2) {
            $stmt = $conn->prepare('SELECT id FROM customers WHERE first_name = :first AND last_name = :last LIMIT 1');
            $stmt->execute([':first' => $parts[0], ':last' => $parts[1]]);
            $found = $stmt->fetchColumn();
            return $found !== false ? (int) $found : null;
        }
    }
    return null;
}

function findVehicleId(PDO $conn, string &$vehicleName, string $plate): ?int {
    if ($plate !== '') {
        $stmt = $conn->prepare('SELECT id, make, model FROM vehicles WHERE plate_no = :plate LIMIT 1');
        $stmt->execute([':plate' => $plate]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $vehicleName = trim(($row['make'] ?? '') . ' ' . ($row['model'] ?? '')) ?: $vehicleName;
            return (int) $row['id'];
        }
    }
    return null;
}

function tableHasColumn(PDO $conn, string $table, string $column): bool {
    $stmt = $conn->prepare('SHOW COLUMNS FROM ' . $table . ' LIKE :column');
    $stmt->execute([':column' => $column]);
    return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
}

function ensureBookingSchema(PDO $conn): void {
    $alterStatements = [
        "ALTER TABLE bookings ADD COLUMN customer_ref VARCHAR(32) DEFAULT NULL",
        "ALTER TABLE bookings ADD COLUMN vehicle_type VARCHAR(80) DEFAULT NULL",
        "ALTER TABLE bookings ADD COLUMN driver_type VARCHAR(50) DEFAULT NULL",
        "ALTER TABLE bookings ADD COLUMN location VARCHAR(255) DEFAULT NULL",
        "ALTER TABLE bookings ADD COLUMN rate DECIMAL(10,2) NOT NULL DEFAULT 0.00",
        "ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','active','done','canceled','overdue') NOT NULL DEFAULT 'pending'"
    ];

    foreach ($alterStatements as $sql) {
        try {
            $conn->exec($sql);
        } catch (PDOException $e) {
            // ignore if the column or modification already exists or is not supported
        }
    }
}

function formatBooking(array $data): array {
    return [
        'id' => $data['booking_ref'],
        'customer' => $data['customer'] ?: 'Guest Customer',
        'customer_ref' => $data['customer_ref'] ?: '—',
        'email' => $data['email'] ?: '—',
        'vehicle' => $data['vehicle'] ?: 'Unknown',
        'vehicle_type' => $data['vehicle_type'] ?: 'Standard',
        'driver_type' => $data['driver_type'] ?: 'Self-drive',
        'location' => $data['location'] ?: '—',
        'plate' => $data['plate'] ?: '—',
        'pickup' => (new DateTime($data['pickup_date']))->format('M j, Y'),
        'ret' => (new DateTime($data['return_date']))->format('M j, Y'),
        'days' => (int) $data['days'],
        'rate' => (float) $data['rate'],
        'amount' => (float) $data['amount'],
        'status' => $data['status'],
        'notes' => $data['notes'] ?? '',
    ];
}

try {
    ensureBookingSchema($conn);

    if ($action === 'delete') {
        if ($bookingRef === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Missing booking reference.']);
            exit;
        }

        $stmt = $conn->prepare('DELETE FROM bookings WHERE booking_ref = :booking_ref');
        $stmt->execute([':booking_ref' => $bookingRef]);
        echo json_encode(['success' => true]);
        exit;
    }

    if ($action === 'create' || $action === 'update') {
        if ($customer === '' || $vehicle === '' || $plate === '' || $pickup_date === '' || $return_date === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Please provide customer, vehicle, plate, pickup and return dates.']);
            exit;
        }

        try {
            $start = new DateTime($pickup_date);
            $end = new DateTime($return_date);
            $days = (int) $start->diff($end)->format('%a');
            if ($days < 1) {
                $days = 1;
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid date values.']);
            exit;
        }

        if ($amount <= 0 && $rate > 0) {
            $amount = round($rate * $days, 2);
        }

        $customerId = findCustomerId($conn, $customer, $customer_ref, $email);
        $vehicleId = findVehicleId($conn, $vehicle, $plate);
        $validStatus = in_array($status, ['pending', 'active', 'done', 'canceled', 'overdue'], true) ? $status : 'pending';

        if ($action === 'create') {
            $maxId = (int) $conn->query('SELECT MAX(id) FROM bookings')->fetchColumn();
            $bookingRef = 'BK-' . str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);

            $columns = ['booking_ref', 'customer_id', 'vehicle_id', 'booked_by_user_id', 'pickup_date', 'return_date', 'days', 'rate', 'amount', 'status', 'notes'];
            $params = [
                ':booking_ref' => $bookingRef,
                ':customer_id' => $customerId,
                ':vehicle_id' => $vehicleId,
                ':booked_by_user_id' => $_SESSION['user']['id'] ?? null,
                ':pickup_date' => $pickup_date,
                ':return_date' => $return_date,
                ':days' => $days,
                ':rate' => $rate,
                ':amount' => $amount,
                ':status' => $validStatus,
                ':notes' => $notes,
            ];

            if (tableHasColumn($conn, 'bookings', 'customer_ref')) {
                $columns[] = 'customer_ref';
                $params[':customer_ref'] = $customer_ref ?: null;
            }
            if (tableHasColumn($conn, 'bookings', 'vehicle_type')) {
                $columns[] = 'vehicle_type';
                $params[':vehicle_type'] = $vehicle_type ?: null;
            }
            if (tableHasColumn($conn, 'bookings', 'driver_type')) {
                $columns[] = 'driver_type';
                $params[':driver_type'] = $driver_type ?: null;
            }
            if (tableHasColumn($conn, 'bookings', 'location')) {
                $columns[] = 'location';
                $params[':location'] = $location ?: null;
            }
            if (tableHasColumn($conn, 'bookings', 'payment_status')) {
                $columns[] = 'payment_status';
                $params[':payment_status'] = 'unpaid';
            }

            $placeholders = array_map(fn($col) => ':' . $col, $columns);
            $stmt = $conn->prepare('INSERT INTO bookings (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')');
            $stmt->execute($params);
        } else {
            if ($bookingRef === '') {
                http_response_code(400);
                echo json_encode(['error' => 'Missing booking reference.']);
                exit;
            }

            $setClauses = ['customer_id = :customer_id', 'vehicle_id = :vehicle_id', 'booked_by_user_id = :booked_by_user_id', 'pickup_date = :pickup_date', 'return_date = :return_date', 'days = :days', 'rate = :rate', 'amount = :amount', 'status = :status', 'notes = :notes'];
            $params = [
                ':customer_id' => $customerId,
                ':vehicle_id' => $vehicleId,
                ':booked_by_user_id' => $_SESSION['user']['id'] ?? null,
                ':pickup_date' => $pickup_date,
                ':return_date' => $return_date,
                ':days' => $days,
                ':rate' => $rate,
                ':amount' => $amount,
                ':status' => $validStatus,
                ':notes' => $notes,
                ':booking_ref' => $bookingRef,
            ];

            if (tableHasColumn($conn, 'bookings', 'customer_ref')) {
                $setClauses[] = 'customer_ref = :customer_ref';
                $params[':customer_ref'] = $customer_ref ?: null;
            }
            if (tableHasColumn($conn, 'bookings', 'vehicle_type')) {
                $setClauses[] = 'vehicle_type = :vehicle_type';
                $params[':vehicle_type'] = $vehicle_type ?: null;
            }
            if (tableHasColumn($conn, 'bookings', 'driver_type')) {
                $setClauses[] = 'driver_type = :driver_type';
                $params[':driver_type'] = $driver_type ?: null;
            }
            if (tableHasColumn($conn, 'bookings', 'location')) {
                $setClauses[] = 'location = :location';
                $params[':location'] = $location ?: null;
            }

            $stmt = $conn->prepare('UPDATE bookings SET ' . implode(', ', $setClauses) . ' WHERE booking_ref = :booking_ref');
            $stmt->execute($params);
        }

        $bookingData = [
            'booking_ref' => $bookingRef,
            'customer' => $customer,
            'customer_ref' => $customer_ref,
            'email' => $email,
            'vehicle' => $vehicle,
            'vehicle_type' => $vehicle_type,
            'driver_type' => $driver_type,
            'location' => $location,
            'plate' => $plate,
            'pickup_date' => $pickup_date,
            'return_date' => $return_date,
            'days' => $days,
            'rate' => $rate,
            'amount' => $amount,
            'status' => $validStatus,
            'notes' => $notes,
        ];

        echo json_encode(['booking' => formatBooking($bookingData)]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['error' => 'Invalid action.']);
    exit;
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to save booking.', 'details' => $e->getMessage()]);
    exit;
}
