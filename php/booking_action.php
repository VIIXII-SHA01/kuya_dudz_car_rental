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
$email = trim($body['email'] ?? '');
$vehicle = trim($body['vehicle'] ?? '');
$plate = trim($body['plate'] ?? '');
$pickup_date = trim($body['pickup_date'] ?? '');
$return_date = trim($body['return_date'] ?? '');
$amount = isset($body['amount']) ? floatval($body['amount']) : 0.0;
$status = trim($body['status'] ?? 'pending');
$notes = trim($body['notes'] ?? '');

function findCustomerId(PDO $conn, string $customerName, string $email): ?int {
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

function formatBooking(array $data): array {
    return [
        'id' => $data['booking_ref'],
        'customer' => $data['customer'] ?: 'Guest Customer',
        'email' => $data['email'] ?: '—',
        'vehicle' => $data['vehicle'] ?: 'Unknown',
        'plate' => $data['plate'] ?: '—',
        'pickup' => (new DateTime($data['pickup_date']))->format('M j, Y'),
        'ret' => (new DateTime($data['return_date']))->format('M j, Y'),
        'days' => (int) $data['days'],
        'amount' => (float) $data['amount'],
        'status' => $data['status'],
        'notes' => $data['notes'] ?? '',
    ];
}

try {
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

        $customerId = findCustomerId($conn, $customer, $email);
        $vehicleId = findVehicleId($conn, $vehicle, $plate);
        $validStatus = in_array($status, ['pending', 'active', 'done', 'canceled'], true) ? $status : 'pending';

        if ($action === 'create') {
            $maxId = (int) $conn->query('SELECT MAX(id) FROM bookings')->fetchColumn();
            $bookingRef = 'BK-' . str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);

            $stmt = $conn->prepare('INSERT INTO bookings (booking_ref, customer_id, vehicle_id, booked_by_user_id, pickup_date, return_date, days, amount, status, notes) VALUES (:booking_ref, :customer_id, :vehicle_id, :booked_by_user_id, :pickup_date, :return_date, :days, :amount, :status, :notes)');
            $stmt->execute([
                ':booking_ref' => $bookingRef,
                ':customer_id' => $customerId,
                ':vehicle_id' => $vehicleId,
                ':booked_by_user_id' => $_SESSION['user']['id'] ?? null,
                ':pickup_date' => $pickup_date,
                ':return_date' => $return_date,
                ':days' => $days,
                ':amount' => $amount,
                ':status' => $validStatus,
                ':notes' => $notes,
            ]);
        } else {
            if ($bookingRef === '') {
                http_response_code(400);
                echo json_encode(['error' => 'Missing booking reference.']);
                exit;
            }

            $stmt = $conn->prepare('UPDATE bookings SET customer_id = :customer_id, vehicle_id = :vehicle_id, booked_by_user_id = :booked_by_user_id, pickup_date = :pickup_date, return_date = :return_date, days = :days, amount = :amount, status = :status, notes = :notes WHERE booking_ref = :booking_ref');
            $stmt->execute([
                ':customer_id' => $customerId,
                ':vehicle_id' => $vehicleId,
                ':booked_by_user_id' => $_SESSION['user']['id'] ?? null,
                ':pickup_date' => $pickup_date,
                ':return_date' => $return_date,
                ':days' => $days,
                ':amount' => $amount,
                ':status' => $validStatus,
                ':notes' => $notes,
                ':booking_ref' => $bookingRef,
            ]);
        }

        $bookingData = [
            'booking_ref' => $bookingRef,
            'customer' => $customer,
            'email' => $email,
            'vehicle' => $vehicle,
            'plate' => $plate,
            'pickup_date' => $pickup_date,
            'return_date' => $return_date,
            'days' => $days,
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
    echo json_encode(['error' => 'Unable to save booking.']);
    exit;
}
