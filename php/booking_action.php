<?php
session_start();
require_once __DIR__ . '/../databases/connection1.php';
require_once __DIR__ . '/db_helpers.php';

header('Content-Type: application/json');

requireAdminSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
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
$driver_id = isset($body['driver_id']) && $body['driver_id'] !== '' ? (int) $body['driver_id'] : null;
$driver_charge = isset($body['driver_charge']) ? max(0, floatval($body['driver_charge'])) : null;
$location = trim($body['location'] ?? '');
$plate = trim($body['plate'] ?? '');
$pickup_date = trim($body['pickup_date'] ?? '');
$return_date = trim($body['return_date'] ?? '');
$amount = isset($body['amount']) ? floatval($body['amount']) : 0.0;
$rate = isset($body['rate']) ? floatval($body['rate']) : 0.0;
$overdue_rate_per_day = normalizeOverdueRatePerDay($body['overdue_rate_per_day'] ?? null);
$status = trim($body['status'] ?? 'pending');
$notes = trim($body['notes'] ?? '');

function findCustomerId(PDO $conn, string $customerName, string $customerRef, string $email): ?int
{
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

function findVehicleId(PDO $conn, string &$vehicleName, string $plate): ?int
{
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

function ensureBookingSchema(PDO $conn): void
{
    ensureBookingOverdueSchema($conn);

    $alterStatements = [
        "ALTER TABLE bookings ADD COLUMN customer_ref VARCHAR(32) DEFAULT NULL",
        "ALTER TABLE bookings ADD COLUMN vehicle_type VARCHAR(80) DEFAULT NULL",
        "ALTER TABLE bookings ADD COLUMN driver_type VARCHAR(50) DEFAULT NULL",
        "ALTER TABLE bookings ADD COLUMN driver_id INT UNSIGNED DEFAULT NULL",
        "ALTER TABLE bookings ADD COLUMN driver_charge DECIMAL(10,2) NOT NULL DEFAULT 0.00",
        "ALTER TABLE bookings ADD COLUMN location VARCHAR(255) DEFAULT NULL",
        "ALTER TABLE bookings ADD COLUMN rate DECIMAL(10,2) NOT NULL DEFAULT 0.00",
        "ALTER TABLE bookings MODIFY COLUMN status ENUM('pending','active','done','canceled','overdue') NOT NULL DEFAULT 'pending'",
    ];

    foreach ($alterStatements as $sql) {
        try {
            $conn->exec($sql);
        } catch (PDOException $e) {
            // ignore if the column or modification already exists
        }
    }
}

function formatBooking(array $data): array
{
    return [
        'id' => $data['booking_ref'],
        'customer' => $data['customer'] ?: 'Guest Customer',
        'customer_ref' => $data['customer_ref'] ?: '—',
        'email' => $data['email'] ?: '—',
        'vehicle' => $data['vehicle'] ?: 'Unknown',
        'vehicle_type' => $data['vehicle_type'] ?: 'Standard',
        'driver_type' => $data['driver_type'] ?: 'Self-drive',
        'driver_id' => isset($data['driver_id']) ? (int) $data['driver_id'] : null,
        'driver_charge' => isset($data['driver_charge']) ? (float) $data['driver_charge'] : 0.0,
        'driver_name' => $data['driver_name'] ?? '',
        'location' => $data['location'] ?: '—',
        'plate' => $data['plate'] ?: '—',
        'pickup' => (new DateTime($data['pickup_date']))->format('M j, Y'),
        'ret' => (new DateTime($data['return_date']))->format('M j, Y'),
        'days' => (int) $data['days'],
        'rate' => (float) $data['rate'],
        'amount' => (float) $data['amount'],
        'base_amount' => isset($data['base_amount']) ? (float) $data['base_amount'] : (float) $data['amount'],
        'overdue_days' => isset($data['overdue_days']) ? (int) $data['overdue_days'] : 0,
        'overdue_penalty' => isset($data['overdue_penalty']) ? (float) $data['overdue_penalty'] : 0.0,
        'overdue_rate_per_day' => bookingOverdueRatePerDay($data),
        'status' => $data['status'],
        'can_delete' => isBookingDeletable((string) ($data['status'] ?? '')),
        'notes' => $data['notes'] ?? '',
    ];
}

function fetchBookingRow(PDO $conn, string $bookingRef): ?array
{
    $stmt = $conn->prepare(
        'SELECT b.booking_ref, b.customer_ref, b.vehicle_type, b.driver_type, b.driver_id, b.driver_charge,
                b.location, b.rate, b.pickup_date, b.return_date, b.days, b.amount, b.base_amount,
                b.overdue_days, b.overdue_penalty, b.overdue_rate_per_day, b.status, b.notes,
                COALESCE(CONCAT(c.first_name, " ", c.last_name), "") AS customer,
                COALESCE(c.email, "") AS email,
                COALESCE(CONCAT(v.make, " ", v.model), "") AS vehicle,
                COALESCE(v.plate_no, "") AS plate,
                COALESCE(CONCAT(d.first_name, " ", d.last_name), "") AS driver_name
         FROM bookings b
         LEFT JOIN customers c ON c.id = b.customer_id
         LEFT JOIN vehicles v ON v.id = b.vehicle_id
         LEFT JOIN drivers d ON d.id = b.driver_id
         WHERE b.booking_ref = :booking_ref LIMIT 1'
    );
    $stmt->execute([':booking_ref' => $bookingRef]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}

function assertDriverAvailable(PDO $conn, int $driverId, string $excludeBookingRef = ''): void
{
    $stmt = $conn->prepare('SELECT id, status FROM drivers WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $driverId]);
    $driver = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$driver) {
        jsonError('Selected driver was not found.');
    }

    $status = normalizeDriverStatus((string) ($driver['status'] ?? ''));
    if ($status === 'suspended') {
        jsonError('This driver is suspended and cannot be assigned.');
    }
    if ($status === 'dayoff') {
        jsonError('This driver is on day off and cannot be assigned.');
    }

    $assignedToThisBooking = false;
    if ($excludeBookingRef !== '' && tableHasColumn($conn, 'bookings', 'driver_id')) {
        $stmtCurrent = $conn->prepare('SELECT booking_ref FROM bookings WHERE booking_ref = :booking_ref AND driver_id = :driver_id LIMIT 1');
        $stmtCurrent->execute([':booking_ref' => $excludeBookingRef, ':driver_id' => $driverId]);
        $assignedToThisBooking = $stmtCurrent->fetch(PDO::FETCH_ASSOC) !== false;
    }

    if ($status === 'rented' && !$assignedToThisBooking) {
        jsonError('This driver is currently rented on another booking.');
    }

    if (!isDriverSelectableForBooking((string) ($driver['status'] ?? '')) && !$assignedToThisBooking) {
        jsonError('This driver is not available for assignment.');
    }

    if (!tableHasColumn($conn, 'bookings', 'driver_id')) {
        return;
    }

    $sql = "SELECT booking_ref FROM bookings WHERE driver_id = :driver_id AND status IN ('pending','active','overdue')";
    $params = [':driver_id' => $driverId];
    if ($excludeBookingRef !== '') {
        $sql .= ' AND booking_ref != :exclude_ref';
        $params[':exclude_ref'] = $excludeBookingRef;
    }
    $sql .= ' LIMIT 1';

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $conflict = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($conflict) {
        jsonError('This driver is already assigned to booking ' . ($conflict['booking_ref'] ?? '') . '.');
    }
}

function syncVehicleStatus(PDO $conn, int $vehicleId, string $bookingStatus, string $bookingRef): void
{
    if ($vehicleId <= 0) {
        return;
    }

    if ($bookingStatus !== 'done' && $bookingStatus !== 'canceled') {
        $stmt = $conn->prepare('UPDATE vehicles SET status = :status WHERE id = :id');
        $stmt->execute([':status' => 'rented', ':id' => $vehicleId]);

        return;
    }

    $stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE vehicle_id = :vehicle_id AND status NOT IN ('done','canceled') AND booking_ref != :booking_ref");
    $stmt->execute([':vehicle_id' => $vehicleId, ':booking_ref' => $bookingRef]);
    $statusToSet = ((int) $stmt->fetchColumn() === 0) ? 'available' : 'rented';
    $stmtVehicle = $conn->prepare('UPDATE vehicles SET status = :status WHERE id = :id');
    $stmtVehicle->execute([':status' => $statusToSet, ':id' => $vehicleId]);
}

function releaseVehicleIfUnused(PDO $conn, int $vehicleId): void
{
    if ($vehicleId <= 0) {
        return;
    }

    $stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE vehicle_id = :vehicle_id AND status NOT IN ('done','canceled')");
    $stmt->execute([':vehicle_id' => $vehicleId]);
    if ((int) $stmt->fetchColumn() === 0) {
        $stmtFree = $conn->prepare('UPDATE vehicles SET status = :status WHERE id = :id');
        $stmtFree->execute([':status' => 'available', ':id' => $vehicleId]);
    }
}

try {
    ensureBookingSchema($conn);
    reconcileStuckRentedDrivers($conn);
    reconcileAllOpenBookingsOverdue($conn);

    if ($action === 'delete') {
        if ($bookingRef === '') {
            jsonError('Missing booking reference.');
        }

        $stmtStatus = $conn->prepare('SELECT status FROM bookings WHERE booking_ref = :booking_ref LIMIT 1');
        $stmtStatus->execute([':booking_ref' => $bookingRef]);
        $deleteRow = $stmtStatus->fetch(PDO::FETCH_ASSOC);
        if (!$deleteRow) {
            jsonError('Booking not found.');
        }
        if (!isBookingDeletable((string) ($deleteRow['status'] ?? ''))) {
            jsonError('Only completed or canceled bookings can be deleted.');
        }

        runInTransaction($conn, function () use ($conn, $bookingRef) {
            $stmtBooking = $conn->prepare('SELECT vehicle_id, driver_id FROM bookings WHERE booking_ref = :booking_ref LIMIT 1');
            $stmtBooking->execute([':booking_ref' => $bookingRef]);
            $bookingRow = $stmtBooking->fetch(PDO::FETCH_ASSOC);

            $stmt = $conn->prepare('DELETE FROM bookings WHERE booking_ref = :booking_ref');
            $stmt->execute([':booking_ref' => $bookingRef]);

            if ($bookingRow && !empty($bookingRow['vehicle_id'])) {
                releaseVehicleIfUnused($conn, (int) $bookingRow['vehicle_id']);
            }
            if ($bookingRow && !empty($bookingRow['driver_id'])) {
                releaseDriverIfUnused($conn, (int) $bookingRow['driver_id']);
            }
        });

        echo json_encode(['success' => true]);
        exit;
    }

    if ($action !== 'create' && $action !== 'update') {
        jsonError('Invalid action.');
    }

    if ($customer === '' || $vehicle === '' || $plate === '' || $pickup_date === '' || $return_date === '') {
        jsonError('Please provide customer, vehicle, plate, pickup and return dates.');
    }

    try {
        $start = new DateTime($pickup_date);
        $end = new DateTime($return_date);
        $days = (int) $start->diff($end)->format('%a');
        if ($days < 1) {
            $days = 1;
        }
    } catch (Exception $e) {
        jsonError('Invalid date values.');
    }

    $needsDriver = isWithDriverType($driver_type);
    if ($needsDriver) {
        if (!$driver_id || $driver_id <= 0) {
            jsonError('Please select a driver for chauffeur bookings.');
        }
        if ($driver_charge === null) {
            $driver_charge = 600.0;
        }
    } else {
        $driver_id = null;
        $driver_charge = 0.0;
    }

    if ($rate > 0) {
        $baseAmount = round($rate * $days + $driver_charge, 2);
    } elseif ($needsDriver && $driver_charge > 0) {
        $baseAmount = round($driver_charge, 2);
    } else {
        $baseAmount = round($amount, 2);
    }

    $overdueDays = 0;
    $overduePenalty = 0.0;
    $amount = $baseAmount;

    $customerId = findCustomerId($conn, $customer, $customer_ref, $email);
    if ($customerId === null) {
        jsonError('Customer must be registered before making a booking. Please select an existing customer.');
    }

    $vehicleId = findVehicleId($conn, $vehicle, $plate);
    if (!$vehicleId) {
        jsonError('Selected vehicle not found.');
    }

    $validStatus = in_array($status, ['pending', 'active', 'done', 'canceled', 'overdue'], true) ? $status : 'pending';

    if (bookingTerminalStatus($validStatus)) {
        $finalTotals = finalizeBookingAmountForTerminal($conn, $baseAmount, $return_date, $overdue_rate_per_day);
        $baseAmount = $finalTotals['base_amount'];
        $overdueDays = $finalTotals['overdue_days'];
        $overduePenalty = $finalTotals['overdue_penalty'];
        $amount = $finalTotals['amount'];
    }

    $existingBooking = null;
    if ($action === 'update' && $bookingRef !== '') {
        $stmtExistingBooking = $conn->prepare('SELECT vehicle_id, status, driver_id, driver_charge FROM bookings WHERE booking_ref = :booking_ref LIMIT 1');
        $stmtExistingBooking->execute([':booking_ref' => $bookingRef]);
        $existingBooking = $stmtExistingBooking->fetch(PDO::FETCH_ASSOC) ?: null;
        if (!$existingBooking) {
            jsonError('Booking not found.');
        }
    }

    $excludeRef = ($action === 'update') ? $bookingRef : '';
    if ($needsDriver && $driver_id) {
        assertDriverAvailable($conn, $driver_id, $excludeRef);
    }

    $stmtActive = $conn->prepare(
        'SELECT COUNT(*) FROM bookings WHERE vehicle_id = :vehicle_id AND status NOT IN (\'done\', \'canceled\')'
        . ($action === 'update' && $bookingRef !== '' ? ' AND booking_ref != :booking_ref' : '')
    );
    $paramsActive = [':vehicle_id' => $vehicleId];
    if ($action === 'update' && $bookingRef !== '') {
        $paramsActive[':booking_ref'] = $bookingRef;
    }
    $stmtActive->execute($paramsActive);
    if ((int) $stmtActive->fetchColumn() > 0) {
        jsonError('This vehicle is currently booked. Only canceled or completed bookings can be replaced.');
    }

    if ($validStatus !== 'done' && $validStatus !== 'canceled') {
        $stmtVehicle = $conn->prepare('SELECT status FROM vehicles WHERE id = :id LIMIT 1');
        $stmtVehicle->execute([':id' => $vehicleId]);
        $vehicleRow = $stmtVehicle->fetch(PDO::FETCH_ASSOC);
        $vehicleStatus = $vehicleRow['status'] ?? 'available';
        $isSameBookingVehicle = $existingBooking && (int) $existingBooking['vehicle_id'] === $vehicleId;

        if ($vehicleStatus !== 'available' && !$isSameBookingVehicle) {
            jsonError('This vehicle is not available for booking.');
        }
    }

    $driverName = '';
    if ($driver_id) {
        $stmtDriver = $conn->prepare('SELECT first_name, last_name FROM drivers WHERE id = :id LIMIT 1');
        $stmtDriver->execute([':id' => $driver_id]);
        $driverRow = $stmtDriver->fetch(PDO::FETCH_ASSOC);
        if ($driverRow) {
            $driverName = trim(($driverRow['first_name'] ?? '') . ' ' . ($driverRow['last_name'] ?? ''));
        }
    }

    $bookingRef = runInTransaction($conn, function () use (
        $conn,
        $action,
        $bookingRef,
        $customerId,
        $vehicleId,
        $customer_ref,
        $vehicle_type,
        $driver_type,
        $driver_id,
        $driver_charge,
        $location,
        $pickup_date,
        $return_date,
        $days,
        $rate,
        $baseAmount,
        $overdueDays,
        $overduePenalty,
        $amount,
        $validStatus,
        $notes,
        $overdue_rate_per_day,
        $existingBooking
    ) {
        $previousDriverId = $existingBooking && !empty($existingBooking['driver_id'])
            ? (int) $existingBooking['driver_id']
            : null;
        if ($action === 'create') {
            $maxId = (int) $conn->query('SELECT MAX(id) FROM bookings')->fetchColumn();
            $bookingRef = 'BK-' . str_pad((string) ($maxId + 1), 4, '0', STR_PAD_LEFT);

            $columns = ['booking_ref', 'customer_id', 'vehicle_id', 'booked_by_user_id', 'pickup_date', 'return_date', 'days', 'rate', 'base_amount', 'overdue_days', 'overdue_penalty', 'amount', 'status', 'notes'];
            $params = [
                ':booking_ref' => $bookingRef,
                ':customer_id' => $customerId,
                ':vehicle_id' => $vehicleId,
                ':booked_by_user_id' => $_SESSION['user']['id'] ?? null,
                ':pickup_date' => $pickup_date,
                ':return_date' => $return_date,
                ':days' => $days,
                ':rate' => $rate,
                ':base_amount' => $baseAmount,
                ':overdue_days' => $overdueDays,
                ':overdue_penalty' => $overduePenalty,
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
            if (tableHasColumn($conn, 'bookings', 'driver_id')) {
                $columns[] = 'driver_id';
                $params[':driver_id'] = $driver_id;
            }
            if (tableHasColumn($conn, 'bookings', 'driver_charge')) {
                $columns[] = 'driver_charge';
                $params[':driver_charge'] = $driver_charge;
            }
            if (tableHasColumn($conn, 'bookings', 'location')) {
                $columns[] = 'location';
                $params[':location'] = $location ?: null;
            }
            if (tableHasColumn($conn, 'bookings', 'overdue_rate_per_day')) {
                $columns[] = 'overdue_rate_per_day';
                $params[':overdue_rate_per_day'] = $overdue_rate_per_day;
            }
            if (tableHasColumn($conn, 'bookings', 'payment_status')) {
                $columns[] = 'payment_status';
                $params[':payment_status'] = 'unpaid';
            }

            $placeholders = array_map(fn ($col) => ':' . $col, $columns);
            $stmt = $conn->prepare('INSERT INTO bookings (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')');
            $stmt->execute($params);
        } else {
            if ($bookingRef === '') {
                jsonError('Missing booking reference.');
            }

            $setClauses = [
                'customer_id = :customer_id',
                'vehicle_id = :vehicle_id',
                'booked_by_user_id = :booked_by_user_id',
                'pickup_date = :pickup_date',
                'return_date = :return_date',
                'days = :days',
                'rate = :rate',
                'base_amount = :base_amount',
                'overdue_days = :overdue_days',
                'overdue_penalty = :overdue_penalty',
                'amount = :amount',
                'status = :status',
                'notes = :notes',
            ];
            $params = [
                ':customer_id' => $customerId,
                ':vehicle_id' => $vehicleId,
                ':booked_by_user_id' => $_SESSION['user']['id'] ?? null,
                ':pickup_date' => $pickup_date,
                ':return_date' => $return_date,
                ':days' => $days,
                ':rate' => $rate,
                ':base_amount' => $baseAmount,
                ':overdue_days' => $overdueDays,
                ':overdue_penalty' => $overduePenalty,
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
            if (tableHasColumn($conn, 'bookings', 'driver_id')) {
                $setClauses[] = 'driver_id = :driver_id';
                $params[':driver_id'] = $driver_id;
            }
            if (tableHasColumn($conn, 'bookings', 'driver_charge')) {
                $setClauses[] = 'driver_charge = :driver_charge';
                $params[':driver_charge'] = $driver_charge;
            }
            if (tableHasColumn($conn, 'bookings', 'location')) {
                $setClauses[] = 'location = :location';
                $params[':location'] = $location ?: null;
            }
            if (tableHasColumn($conn, 'bookings', 'overdue_rate_per_day')) {
                $setClauses[] = 'overdue_rate_per_day = :overdue_rate_per_day';
                $params[':overdue_rate_per_day'] = $overdue_rate_per_day;
            }

            $stmt = $conn->prepare('UPDATE bookings SET ' . implode(', ', $setClauses) . ' WHERE booking_ref = :booking_ref');
            $stmt->execute($params);

            if ($existingBooking && (int) $existingBooking['vehicle_id'] !== $vehicleId && (int) $existingBooking['vehicle_id'] > 0) {
                releaseVehicleIfUnused($conn, (int) $existingBooking['vehicle_id']);
            }
        }

        syncVehicleStatus($conn, $vehicleId, $validStatus, $bookingRef);
        syncDriverForBooking($conn, $driver_id, $validStatus, $bookingRef, $previousDriverId);

        if (!bookingTerminalStatus($validStatus)) {
            reconcileBookingOverdue($conn, $bookingRef);
        }

        return $bookingRef;
    });

    $savedRow = fetchBookingRow($conn, $bookingRef);
    if (!$savedRow) {
        jsonError('Booking could not be loaded after saving.');
    }

    echo json_encode(['booking' => formatBooking($savedRow)]);
} catch (PDOException $e) {
    jsonError('Unable to save booking.', 500, $e->getMessage());
} catch (Throwable $e) {
    jsonError('Unable to save booking.', 500, $e->getMessage());
}
