<?php
session_start();
require_once __DIR__ . '/../databases/connection1.php';
require_once __DIR__ . '/db_helpers.php';

header('Content-Type: application/json');

function sendJson(array $payload, int $status = 200): void {
    http_response_code($status);
    echo json_encode($payload);
    exit;
}

function normalizeStatus(string $status): string {
    $value = strtolower(trim($status));
    return $value === '' ? 'unknown' : $value;
}

function labelForStatus(string $status): string {
    $status = normalizeStatus($status);
    $map = [
        'available' => 'Available',
        'rented' => 'Rented',
        'maintenance' => 'Maintenance',
        'unavailable' => 'Unavailable',
        'unknown' => 'Unknown',
    ];
    return $map[$status] ?? ucfirst($status);
}

function badgeClassForStatus(string $status): string {
    $status = normalizeStatus($status);
    $map = [
        'available' => 'active',
        'rented' => 'pending',
        'maintenance' => 'canceled',
        'unavailable' => 'overdue',
        'unknown' => 'pending',
    ];
    return $map[$status] ?? 'pending';
}

function formatCurrency(float $value): string {
    return '₱' . number_format($value, 0, '.', ',');
}

try {
    reconcileAllOpenBookingsOverdue($conn);

    $currentYear = (int) date('Y');
    $currentMonth = (int) date('m');
    $previousYear = $currentYear - 1;

    $totalCars = (int) $conn->query('SELECT COUNT(*) FROM vehicles')->fetchColumn();
    $availableCars = (int) $conn->query('SELECT COUNT(*) FROM vehicles WHERE status = "available"')->fetchColumn();
    $activeBookings = (int) $conn->query('SELECT COUNT(*) FROM bookings WHERE status IN ("active","overdue")')->fetchColumn();
    $totalCustomers = (int) $conn->query('SELECT COUNT(*) FROM customers')->fetchColumn();

    $stmt = $conn->prepare(
        'SELECT MONTH(payment_date) AS month_number, DATE_FORMAT(payment_date, "%b") AS month, COALESCE(SUM(paid),0) AS revenue
         FROM payments
         WHERE YEAR(payment_date) = :year
         GROUP BY month_number
         ORDER BY month_number'
    );
    $stmt->execute([':year' => $currentYear]);
    $revenueChart = array_map(function ($row) {
        return [
            'month' => $row['month'] ?: '',
            'revenue' => (float) $row['revenue'],
        ];
    }, $stmt->fetchAll(PDO::FETCH_ASSOC));

    $stmt = $conn->prepare('SELECT COALESCE(SUM(paid),0) FROM payments WHERE YEAR(payment_date) = :year');
    $stmt->execute([':year' => $currentYear]);
    $totalRevenue = (float) $stmt->fetchColumn();

    $stmt = $conn->prepare('SELECT COALESCE(SUM(paid),0) FROM payments WHERE YEAR(payment_date) = :year');
    $stmt->execute([':year' => $previousYear]);
    $previousYearRevenue = (float) $stmt->fetchColumn();

    $stmt = $conn->prepare('SELECT COALESCE(SUM(paid),0) FROM payments WHERE YEAR(payment_date) = :year AND MONTH(payment_date) = :month');
    $stmt->execute([':year' => $currentYear, ':month' => $currentMonth]);
    $currentMonthRevenue = (float) $stmt->fetchColumn();

    $revenueChangePercent = 0;
    if ($previousYearRevenue > 0) {
        $revenueChangePercent = (int) round((($totalRevenue - $previousYearRevenue) / $previousYearRevenue) * 100);
    }

    $targetValue = $totalRevenue > 0 && count($revenueChart) > 0
        ? round(max($totalRevenue / max(count($revenueChart), 1), $currentMonthRevenue) * 1.1)
        : 0;

    foreach ($revenueChart as &$chartItem) {
        $chartItem['target'] = $targetValue;
    }
    unset($chartItem);

    $stmt = $conn->query(
        'SELECT b.booking_ref, b.customer_ref, b.vehicle_type, b.status, b.amount, b.created_at,
                COALESCE(CONCAT(c.first_name, " ", c.last_name), b.customer_ref, "Guest Customer") AS customer_name,
                COALESCE(CONCAT(v.make, " ", v.model), b.vehicle_type, "Unknown Vehicle") AS vehicle_name,
                COALESCE(v.plate_no, "—") AS plate_no
         FROM bookings b
         LEFT JOIN customers c ON b.customer_id = c.id
         LEFT JOIN vehicles v ON b.vehicle_id = v.id
         ORDER BY b.created_at DESC
         LIMIT 5'
    );
    $recentBookings = array_map(function ($row) {
        return [
            'booking_ref' => $row['booking_ref'],
            'customer' => trim($row['customer_name']) ?: 'Guest Customer',
            'vehicle' => trim($row['vehicle_name']) ?: 'Unknown Vehicle',
            'plate' => trim($row['plate_no']) ?: '—',
            'status' => $row['status'] ?? 'pending',
            'amount' => (float) $row['amount'],
        ];
    }, $stmt->fetchAll(PDO::FETCH_ASSOC));

    $payments = $conn->query(
        'SELECT created_at, customer_name, paid AS amount, status, reference_no
         FROM payments
         ORDER BY created_at DESC
         LIMIT 3'
    )->fetchAll(PDO::FETCH_ASSOC);

    $bookingsActivity = $conn->query(
        'SELECT created_at, booking_ref, status, amount
         FROM bookings
         ORDER BY created_at DESC
         LIMIT 3'
    )->fetchAll(PDO::FETCH_ASSOC);

    $activityItems = [];
    foreach ($payments as $row) {
        $activityItems[] = [
            'type' => 'payment',
            'time' => $row['created_at'],
            'message' => sprintf(
                'Payment of %s received from %s.',
                formatCurrency((float) $row['amount']),
                $row['customer_name'] ?: 'Customer'
            ),
            'status' => $row['status'] ?? 'pending',
        ];
    }
    foreach ($bookingsActivity as $row) {
        $activityItems[] = [
            'type' => 'booking',
            'time' => $row['created_at'],
            'message' => sprintf(
                'Booking %s is currently %s.',
                $row['booking_ref'], ucfirst($row['status'] ?? 'pending')
            ),
            'status' => $row['status'] ?? 'pending',
        ];
    }

    usort($activityItems, function ($a, $b) {
        return strcmp($b['time'], $a['time']);
    });
    $activityItems = array_slice($activityItems, 0, 6);

    $stmt = $conn->query(
        'SELECT make, model, plate_no, status
         FROM vehicles
         ORDER BY FIELD(status, "available", "rented", "maintenance", "unavailable"), id ASC
         LIMIT 6'
    );
    $fleetStatus = array_map(function ($row) {
        $rawStatus = trim((string) ($row['status'] ?? ''));
        $status = $rawStatus === '' ? 'unknown' : strtolower($rawStatus);
        return [
            'model' => trim(($row['make'] ?? '') . ' ' . ($row['model'] ?? '')) ?: 'Unknown Vehicle',
            'plate' => $row['plate_no'] ?: '—',
            'status' => $status,
            'label' => labelForStatus($status),
            'badge' => badgeClassForStatus($status),
        ];
    }, $stmt->fetchAll(PDO::FETCH_ASSOC));

    $sessionUser = $_SESSION['user'] ?? null;
    $userFullName = 'Valued User';
    if (is_array($sessionUser)) {
        $first = trim((string) ($sessionUser['first_name'] ?? ''));
        $last = trim((string) ($sessionUser['last_name'] ?? ''));
        $userFullName = trim(($first ? $first . ' ' : '') . $last) ?: 'Valued User';
    }

    sendJson([
        'user' => [
            'first_name' => $sessionUser['first_name'] ?? '',
            'last_name' => $sessionUser['last_name'] ?? '',
            'full_name' => $userFullName,
            'role' => $sessionUser['role'] ?? 'Administrator',
        ],
        'stats' => [
            'totalCars' => $totalCars,
            'availableCars' => $availableCars,
            'activeBookings' => $activeBookings,
            'totalCustomers' => $totalCustomers,
            'totalRevenue' => $totalRevenue,
            'currentMonthRevenue' => $currentMonthRevenue,
            'revenueChange' => $revenueChangePercent,
        ],
        'revenueChart' => $revenueChart,
        'recentBookings' => $recentBookings,
        'activity' => $activityItems,
        'fleetStatus' => $fleetStatus,
    ]);
} catch (PDOException $e) {
    sendJson(['error' => 'Unable to load dashboard data.'], 500);
}
