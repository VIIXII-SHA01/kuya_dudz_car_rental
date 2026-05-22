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

function tableExists(PDO $conn, string $table): bool {
    $stmt = $conn->prepare('SHOW TABLES LIKE :table');
    $stmt->execute([':table' => $table]);
    return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
}

function formatMonthName(int $month): string {
    $date = DateTime::createFromFormat('!m', (string) $month);
    return $date ? $date->format('M') : '';
}

try {
    $hasBookings = tableExists($conn, 'bookings');
    $hasRentals = tableExists($conn, 'rentals');
    $hasPayments = tableExists($conn, 'payments');
    $year = (int) date('Y');

    $totalRevenue = 0.0;
    $totalBookings = 0;
    $avgBookingValue = 0.0;
    $overdueAmount = 0.0;
    $revenueByMonth = [];
    $fleetMix = [];
    $topVehicles = [];
    $statusData = [];
    $insights = [];
    $totalMix = 0;

    if ($hasPayments) {
        $stmt = $conn->query('SELECT COALESCE(SUM(paid),0) AS revenue, COALESCE(SUM(balance),0) AS overdue FROM payments');
        $summary = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalRevenue = (float) ($summary['revenue'] ?? 0);
        $overdueAmount = (float) ($summary['overdue'] ?? 0);
    }

    if ($hasBookings) {
        $stmt = $conn->query('SELECT COUNT(*) AS cnt, COALESCE(SUM(amount),0) AS revenue, COALESCE(AVG(amount),0) AS avg_amount FROM bookings');
        $summary = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalBookings = (int) ($summary['cnt'] ?? 0);
        $bookingRevenue = (float) ($summary['revenue'] ?? 0);
        $avgBookingValue = round((float) ($summary['avg_amount'] ?? 0));

        $stmt = $conn->prepare(
            'SELECT MONTH(pickup_date) AS m, DATE_FORMAT(pickup_date, "%b") AS month,
                    COUNT(*) AS bookings, COALESCE(SUM(amount),0) AS revenue
             FROM bookings
             WHERE YEAR(pickup_date) = :year
             GROUP BY m
             ORDER BY m'
        );
        $stmt->execute([':year' => $year]);
        $revenueByMonth = array_map(static function ($row) {
            return [
                'month' => $row['month'] ?: formatMonthName((int) $row['m']),
                'revenue' => (float) $row['revenue'],
                'bookings' => (int) $row['bookings'],
            ];
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));

        $stmt = $conn->query(
            'SELECT COALESCE(NULLIF(TRIM(b.vehicle_type), ""), NULLIF(TRIM(v.category), ""), "Other") AS vehicle_type, COUNT(*) AS count
             FROM bookings b
             LEFT JOIN vehicles v ON v.id = b.vehicle_id
             GROUP BY vehicle_type
             ORDER BY count DESC'
        );
        $colors = ['#D4A843', '#3D8FBE', '#9A3DBE', '#3DBE7A', '#6A6E75', '#E8341A'];
        $fleetMix = array_map(static function ($row) use (&$colors) {
            $color = array_shift($colors) ?? '#3D8FBE';
            return [
                'type' => $row['vehicle_type'] ?: 'Other',
                'count' => (int) $row['count'],
                'color' => $color,
                'bar' => $color,
            ];
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
        $totalMix = array_sum(array_column($fleetMix, 'count'));

        $stmt = $conn->query(
            'SELECT COALESCE(CONCAT(v.make, " ", v.model), b.vehicle_type, "Unknown Vehicle") AS vehicle_name,
                    COALESCE(v.plate_no, "—") AS plate_no,
                    COALESCE(SUM(b.amount),0) AS revenue
             FROM bookings b
             LEFT JOIN vehicles v ON v.id = b.vehicle_id
             GROUP BY vehicle_name, plate_no
             ORDER BY revenue DESC
             LIMIT 5'
        );
        $vehicleRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $maxRevenue = 0.0;
        foreach ($vehicleRows as $row) {
            $maxRevenue = max($maxRevenue, (float) $row['revenue']);
        }
        $topVehicles = array_map(static function ($row) use ($maxRevenue) {
            return [
                'name' => $row['vehicle_name'] ?: 'Unknown Vehicle',
                'plate' => $row['plate_no'] ?: '—',
                'revenue' => (float) $row['revenue'],
                'max' => $maxRevenue ?: 1,
            ];
        }, $vehicleRows);

        $statusMap = [
            'pending' => ['Pending', 'var(--gold)', '#D4A843'],
            'active' => ['Active', 'var(--blue)', '#3D8FBE'],
            'done' => ['Completed', 'var(--green)', '#3DBE7A'],
            'canceled' => ['Canceled', 'var(--red)', '#E8341A'],
            'overdue' => ['Overdue', 'var(--red)', '#E8341A'],
        ];
        $stmt = $conn->query('SELECT status, COUNT(*) AS count FROM bookings GROUP BY status');
        $statusData = array_map(static function ($row) use ($statusMap) {
            $status = strtolower((string) $row['status']);
            $label = $statusMap[$status][0] ?? ucfirst($status);
            $color = $statusMap[$status][1] ?? 'var(--muted)';
            $bar = $statusMap[$status][2] ?? '#6A6E75';
            return [
                'label' => $label,
                'val' => (int) $row['count'],
                'color' => $color,
                'bar' => $bar,
            ];
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));

        $stmt = $conn->query(
            'SELECT customer_ref, COUNT(*) AS cnt FROM bookings
             WHERE customer_ref IS NOT NULL AND customer_ref != ""
             GROUP BY customer_ref HAVING cnt > 1'
        );
        $repeatRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $repeatBookings = array_sum(array_column($repeatRows, 'cnt'));
        $repeatRate = $totalBookings > 0 ? round(($repeatBookings / $totalBookings) * 100) : 0;

        $topType = $fleetMix[0]['type'] ?? null;
        $topMonthName = null;
        if (!empty($revenueByMonth)) {
            $topMonth = array_reduce($revenueByMonth, static function ($carry, $item) {
                if ($carry === null || $item['revenue'] > $carry['revenue']) {
                    return $item;
                }
                return $carry;
            });
            $topMonthName = $topMonth['month'] ?? null;
        }

        if ($topMonthName) {
            $insights[] = [
                'icon' => '📈',
                'iconBg' => 'var(--green-dim)',
                'iconBdr' => 'rgba(61,190,122,0.2)',
                'title' => 'Revenue Peaking in ' . $topMonthName,
                'desc' => 'Your strongest revenue month shows the highest booking income in the current period.',
            ];
        }

        $overdueCount = (int) $conn->query('SELECT COUNT(*) FROM bookings WHERE status = "overdue"')->fetchColumn();
        $insights[] = [
            'icon' => '⚠️',
            'iconBg' => 'var(--red-dim)',
            'iconBdr' => 'rgba(232,52,26,0.2)',
            'title' => $overdueCount . ' overdue bookings require action',
            'desc' => $overdueCount > 0
                ? 'There are currently ' . $overdueCount . ' overdue bookings waiting follow-up.'
                : 'No overdue bookings were found for the current period.',
        ];

        if ($topType) {
            $insights[] = [
                'icon' => '🚗',
                'iconBg' => 'var(--blue-dim)',
                'iconBdr' => 'rgba(61,143,190,0.2)',
                'title' => $topType . 's are your top earning vehicle type',
                'desc' => 'The fleet mix shows that ' . $topType . ' bookings drive the largest share of activity.',
            ];
        }

        $insights[] = [
            'icon' => '🔁',
            'iconBg' => 'var(--gold-dim)',
            'iconBdr' => 'rgba(212,168,67,0.2)',
            'title' => 'Repeat booking rate is ' . $repeatRate . '%',
            'desc' => $repeatRate > 0
                ? 'Repeat customers accounted for ' . $repeatRate . '% of all bookings in the current dataset.'
                : 'No repeat customer bookings were detected yet.',
        ];
    } elseif ($hasRentals) {
        $stmt = $conn->query('SELECT COUNT(*) AS cnt, COALESCE(SUM(total),0) AS revenue FROM rentals');
        $summary = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalBookings = (int) ($summary['cnt'] ?? 0);
        if ($totalRevenue <= 0) {
            $totalRevenue = (float) ($summary['revenue'] ?? 0);
        }
        if ($totalBookings > 0) {
            $avgBookingValue = round($totalRevenue / $totalBookings);
        }

        if ($hasPayments && empty($revenueByMonth)) {
            $stmt = $conn->prepare(
                'SELECT MONTH(payment_date) AS m, DATE_FORMAT(payment_date, "%b") AS month, COUNT(*) AS bookings, COALESCE(SUM(paid),0) AS revenue
                 FROM payments WHERE YEAR(payment_date) = :year GROUP BY m ORDER BY m'
            );
            $stmt->execute([':year' => $year]);
            $revenueByMonth = array_map(static function ($row) {
                return [
                    'month' => $row['month'] ?: formatMonthName((int) $row['m']),
                    'revenue' => (float) $row['revenue'],
                    'bookings' => (int) $row['bookings'],
                ];
            }, $stmt->fetchAll(PDO::FETCH_ASSOC));
        }
    } elseif ($hasPayments) {
        $stmt = $conn->prepare(
            'SELECT MONTH(payment_date) AS m, DATE_FORMAT(payment_date, "%b") AS month, COUNT(*) AS bookings, COALESCE(SUM(paid),0) AS revenue
             FROM payments WHERE YEAR(payment_date) = :year GROUP BY m ORDER BY m'
        );
        $stmt->execute([':year' => $year]);
        $revenueByMonth = array_map(static function ($row) {
            return [
                'month' => $row['month'] ?: formatMonthName((int) $row['m']),
                'revenue' => (float) $row['revenue'],
                'bookings' => (int) $row['bookings'],
            ];
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    sendJson([
        'kpis' => [
            'revenue' => round($totalRevenue),
            'bookings' => $totalBookings,
            'avg' => $avgBookingValue,
            'overdue' => round($overdueAmount),
        ],
        'revenueByMonth' => $revenueByMonth,
        'fleetMix' => $fleetMix,
        'totalMix' => $totalMix,
        'topVehicles' => $topVehicles,
        'statusData' => $statusData,
        'insights' => $insights,
    ]);
} catch (PDOException $e) {
    error_log('report_action: ' . $e->getMessage());
    sendJson(['error' => 'Unable to generate report data.'], 500);
}
