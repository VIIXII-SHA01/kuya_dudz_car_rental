<?php

/**
 * Shared helpers for admin API action scripts.
 */

function requireAdminSession(): void
{
    if (empty($_SESSION['logged_in']) || empty($_SESSION['user'])) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Unauthorized.']);
        exit;
    }
}

function tableHasColumn(PDO $conn, string $table, string $column): bool
{
    static $cache = [];
    $key = $table . '.' . $column;
    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }
    $stmt = $conn->prepare('SHOW COLUMNS FROM `' . str_replace('`', '', $table) . '` LIKE :column');
    $stmt->execute([':column' => $column]);
    $cache[$key] = $stmt->fetch(PDO::FETCH_ASSOC) !== false;

    return $cache[$key];
}

function isWithDriverType(string $driverType): bool
{
    $normalized = strtolower(trim($driverType));

    return str_contains($normalized, 'with') && str_contains($normalized, 'driver');
}

function normalizeDriverStatus(string $status): string
{
    $normalized = strtolower(trim($status));

    return match ($normalized) {
        'on-duty' => 'rented',
        'off-duty' => 'dayoff',
        default => $normalized,
    };
}

function isDriverSelectableForBooking(string $status): bool
{
    return normalizeDriverStatus($status) === 'available';
}

function reconcileStuckRentedDrivers(PDO $conn): void
{
    if (!tableHasColumn($conn, 'bookings', 'driver_id')) {
        return;
    }

    $sql = "UPDATE drivers d SET d.status = 'available'
            WHERE d.status = 'rented'
            AND NOT EXISTS (
                SELECT 1 FROM bookings b
                WHERE b.driver_id = d.id
                AND b.status IN ('pending','active','overdue')
            )";
    $conn->exec($sql);
}

function releaseDriverIfUnused(PDO $conn, int $driverId, string $excludeBookingRef = ''): void
{
    if ($driverId <= 0 || !tableHasColumn($conn, 'bookings', 'driver_id')) {
        return;
    }

    $sql = "SELECT COUNT(*) FROM bookings WHERE driver_id = :driver_id AND status IN ('pending','active','overdue')";
    $params = [':driver_id' => $driverId];
    if ($excludeBookingRef !== '') {
        $sql .= ' AND booking_ref != :exclude_ref';
        $params[':exclude_ref'] = $excludeBookingRef;
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    if ((int) $stmt->fetchColumn() > 0) {
        return;
    }

    $stmtFree = $conn->prepare("UPDATE drivers SET status = 'available' WHERE id = :id AND status = 'rented'");
    $stmtFree->execute([':id' => $driverId]);
}

function assignDriverRented(PDO $conn, int $driverId): void
{
    if ($driverId <= 0) {
        return;
    }

    $stmt = $conn->prepare("UPDATE drivers SET status = 'rented' WHERE id = :id AND status IN ('available', 'rented')");
    $stmt->execute([':id' => $driverId]);
}

function syncDriverForBooking(PDO $conn, ?int $driverId, string $bookingStatus, string $bookingRef, ?int $previousDriverId): void
{
    if ($previousDriverId && $previousDriverId > 0 && $previousDriverId !== $driverId) {
        releaseDriverIfUnused($conn, $previousDriverId, $bookingRef);
    }

    if (!$driverId || $driverId <= 0) {
        return;
    }

    if (in_array($bookingStatus, ['done', 'canceled'], true)) {
        releaseDriverIfUnused($conn, $driverId, $bookingRef);

        return;
    }

    assignDriverRented($conn, $driverId);
}

function jsonError(string $message, int $statusCode = 400, ?string $details = null): void
{
    http_response_code($statusCode);
    $payload = ['error' => $message];
    if ($details !== null && (getenv('RENT_APP_DEBUG') === '1' || (defined('RENT_APP_DEBUG') && RENT_APP_DEBUG))) {
        $payload['details'] = $details;
    }
    echo json_encode($payload);
    exit;
}

const BOOKING_OVERDUE_PENALTY_PER_DAY = 500.0;

function bookingTerminalStatus(string $status): bool
{
    return in_array($status, ['done', 'canceled'], true);
}

function isBookingDeletable(string $status): bool
{
    return bookingTerminalStatus($status);
}

function isPaymentDeletable(string $status): bool
{
    return in_array($status, ['paid', 'refunded'], true);
}

function computeBookingOverdueDays(string $returnDate): int
{
    if ($returnDate === '') {
        return 0;
    }

    try {
        $returnDay = new DateTime($returnDate);
        $returnDay->setTime(0, 0, 0);
        $today = new DateTime('today');
        if ($today <= $returnDay) {
            return 0;
        }

        return (int) $returnDay->diff($today)->days;
    } catch (Exception $e) {
        return 0;
    }
}

function normalizeOverdueRatePerDay($value): float
{
    if ($value === null || $value === '') {
        return BOOKING_OVERDUE_PENALTY_PER_DAY;
    }

    $rate = is_numeric($value) ? (float) $value : BOOKING_OVERDUE_PENALTY_PER_DAY;
    if ($rate < 0) {
        return 0.0;
    }
    if ($rate > 100000) {
        return 100000.0;
    }

    return round($rate, 2);
}

function bookingOverdueRatePerDay(array $row): float
{
    if (array_key_exists('overdue_rate_per_day', $row) && $row['overdue_rate_per_day'] !== null && $row['overdue_rate_per_day'] !== '') {
        return normalizeOverdueRatePerDay($row['overdue_rate_per_day']);
    }

    return BOOKING_OVERDUE_PENALTY_PER_DAY;
}

function ensureBookingOverdueSchema(PDO $conn): void
{
    $alterStatements = [
        'ALTER TABLE bookings ADD COLUMN base_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00',
        'ALTER TABLE bookings ADD COLUMN overdue_days INT UNSIGNED NOT NULL DEFAULT 0',
        'ALTER TABLE bookings ADD COLUMN overdue_penalty DECIMAL(10,2) NOT NULL DEFAULT 0.00',
        'ALTER TABLE bookings ADD COLUMN overdue_rate_per_day DECIMAL(10,2) NOT NULL DEFAULT 500.00',
    ];

    foreach ($alterStatements as $sql) {
        try {
            $conn->exec($sql);
        } catch (PDOException $e) {
            // column may already exist
        }
    }
}

function resolveBookingBaseAmount(array $row): float
{
    if (isset($row['base_amount']) && (float) $row['base_amount'] > 0) {
        return (float) $row['base_amount'];
    }

    $rate = (float) ($row['rate'] ?? 0);
    $days = max(1, (int) ($row['days'] ?? 1));
    $driverCharge = (float) ($row['driver_charge'] ?? 0);

    if ($rate > 0) {
        return round($rate * $days + $driverCharge, 2);
    }

    if ($driverCharge > 0) {
        return round($driverCharge, 2);
    }

    $amount = (float) ($row['amount'] ?? 0);
    $penalty = (float) ($row['overdue_penalty'] ?? 0);

    return round(max(0, $amount - $penalty), 2);
}

function applyBookingOverdueTotals(array $row, int $overdueDays): array
{
    $baseAmount = resolveBookingBaseAmount($row);
    $ratePerDay = bookingOverdueRatePerDay($row);
    $penalty = round($overdueDays * $ratePerDay, 2);

    return [
        'base_amount' => $baseAmount,
        'overdue_days' => $overdueDays,
        'overdue_penalty' => $penalty,
        'amount' => round($baseAmount + $penalty, 2),
    ];
}

function reconcileBookingOverdue(PDO $conn, string $bookingRef): void
{
    ensureBookingOverdueSchema($conn);

    $rateColumn = tableHasColumn($conn, 'bookings', 'overdue_rate_per_day') ? ', overdue_rate_per_day' : '';
    $stmt = $conn->prepare(
        'SELECT booking_ref, status, return_date, rate, days, driver_charge, amount, base_amount, overdue_days, overdue_penalty'
        . $rateColumn
        . ' FROM bookings WHERE booking_ref = :booking_ref LIMIT 1'
    );
    $stmt->execute([':booking_ref' => $bookingRef]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        return;
    }

    $status = (string) ($row['status'] ?? 'pending');
    if (bookingTerminalStatus($status)) {
        return;
    }

    $overdueDays = computeBookingOverdueDays((string) ($row['return_date'] ?? ''));
    $totals = applyBookingOverdueTotals($row, $overdueDays);
    $newStatus = $status;

    if ($overdueDays > 0 && in_array($status, ['pending', 'active'], true)) {
        $newStatus = 'overdue';
    } elseif ($overdueDays === 0 && $status === 'overdue') {
        $newStatus = 'active';
    }

    $stmtUpdate = $conn->prepare(
        'UPDATE bookings SET status = :status, base_amount = :base_amount, overdue_days = :overdue_days,
         overdue_penalty = :overdue_penalty, amount = :amount WHERE booking_ref = :booking_ref'
    );
    $stmtUpdate->execute([
        ':status' => $newStatus,
        ':base_amount' => $totals['base_amount'],
        ':overdue_days' => $totals['overdue_days'],
        ':overdue_penalty' => $totals['overdue_penalty'],
        ':amount' => $totals['amount'],
        ':booking_ref' => $bookingRef,
    ]);
}

function reconcileAllOpenBookingsOverdue(PDO $conn): void
{
    ensureBookingOverdueSchema($conn);

    $stmt = $conn->query("SELECT booking_ref FROM bookings WHERE status NOT IN ('done','canceled')");
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $bookingRef) {
        reconcileBookingOverdue($conn, (string) $bookingRef);
    }
}

function finalizeBookingAmountForTerminal(PDO $conn, float $baseAmount, string $returnDate, float $overdueRatePerDay = BOOKING_OVERDUE_PENALTY_PER_DAY): array
{
    $overdueDays = computeBookingOverdueDays($returnDate);
    $row = [
        'base_amount' => $baseAmount,
        'rate' => 0,
        'days' => 1,
        'driver_charge' => 0,
        'amount' => $baseAmount,
        'overdue_penalty' => 0,
        'overdue_rate_per_day' => normalizeOverdueRatePerDay($overdueRatePerDay),
    ];

    return applyBookingOverdueTotals($row, $overdueDays);
}

function runInTransaction(PDO $conn, callable $callback)
{
    $conn->beginTransaction();
    try {
        $result = $callback();
        $conn->commit();

        return $result;
    } catch (Throwable $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        throw $e;
    }
}
