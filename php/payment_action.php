<?php
session_start();
require_once __DIR__ . '/../databases/connection1.php';
require_once __DIR__ . '/db_helpers.php';

header('Content-Type: application/json');

requireAdminSession();

function sendJson(array $payload, int $statusCode = 200): void {
    http_response_code($statusCode);
    echo json_encode($payload);
    exit;
}

function formatPayment(array $row): array {
    $status = $row['status'] ?? 'pending';

    return [
        'id' => isset($row['id']) ? (int) $row['id'] : 0,
        'payId' => $row['payment_ref'] ?? '',
        'customer' => $row['customer_name'] ?: 'Guest Customer',
        'cusid' => $row['customer_ref'] ?: '—',
        'rentalId' => $row['rental_ref'] ?: '—',
        'date' => $row['payment_date'] ?? '',
        'due' => isset($row['due']) ? (float) $row['due'] : 0.0,
        'paid' => isset($row['paid']) ? (float) $row['paid'] : 0.0,
        'balance' => isset($row['balance']) ? (float) $row['balance'] : 0.0,
        'method' => $row['method'] ?? 'Cash',
        'ref' => $row['reference_no'] ?: '—',
        'status' => $status,
        'can_delete' => isPaymentDeletable((string) $status),
        'notes' => $row['notes'] ?? '',
    ];
}

function normalizeStatus(string $status): string {
    $valid = ['paid', 'pending', 'overdue', 'partial', 'refunded'];
    return in_array($status, $valid, true) ? $status : 'pending';
}

function updateBookingPaymentStatus(PDO $conn, string $rentalRef, string $status): void {
    if ($rentalRef === '' || !tableHasColumn($conn, 'bookings', 'payment_status')) {
        return;
    }

    $paymentStatus = in_array($status, ['paid', 'partial'], true) ? $status : 'unpaid';
    $stmt = $conn->prepare('UPDATE bookings SET payment_status = :payment_status WHERE booking_ref = :booking_ref');
    $stmt->execute([
        ':payment_status' => $paymentStatus,
        ':booking_ref' => $rentalRef,
    ]);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    reconcileAllOpenBookingsOverdue($conn);

    $action = trim((string) ($_GET['action'] ?? ''));

    if ($action === 'suggest') {
        $search = trim((string) ($_GET['q'] ?? ''));
        $limit = max(1, min(50, (int) ($_GET['limit'] ?? 10)));
        $params = [':limit' => $limit];

        $sql = 'SELECT
            COALESCE(CONCAT(c.first_name, " ", c.last_name), b.customer_ref, "Guest Customer") AS customer_name,
            COALESCE(c.customer_ref, b.customer_ref) AS customer_ref,
            b.booking_ref,
            b.amount AS total_due
        FROM bookings b
        LEFT JOIN customers c ON c.id = b.customer_id OR (c.customer_ref = b.customer_ref AND b.customer_id IS NULL)';

        if ($search !== '') {
            $sql .= ' WHERE (c.first_name LIKE :q OR c.last_name LIKE :q OR CONCAT(c.first_name, " ", c.last_name) LIKE :q OR c.customer_ref LIKE :q OR b.customer_ref LIKE :q OR b.booking_ref LIKE :q)';
            $params[':q'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY b.booking_ref DESC LIMIT :limit';

        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            foreach ($params as $key => $value) {
                if ($key !== ':limit') {
                    $stmt->bindValue($key, $value, PDO::PARAM_STR);
                }
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendJson(['suggestions' => $rows]);
        } catch (PDOException $e) {
            sendJson(['error' => 'Unable to load suggestions.'], 500);
        }
    }

    $search = trim((string) ($_GET['q'] ?? ''));
    $statusFilter = trim((string) ($_GET['status'] ?? ''));
    $sql = 'SELECT * FROM payments';
    $clauses = [];
    $params = [];

    if ($statusFilter !== '') {
        $clauses[] = 'status = :status';
        $params[':status'] = normalizeStatus($statusFilter);
    }

    if ($search !== '') {
        $clauses[] = '(payment_ref LIKE :q OR customer_name LIKE :q OR customer_ref LIKE :q OR rental_ref LIKE :q OR reference_no LIKE :q)';
        $params[':q'] = '%' . $search . '%';
    }

    if ($clauses) {
        $sql .= ' WHERE ' . implode(' AND ', $clauses);
    }
    $sql .= ' ORDER BY payment_date DESC, id DESC';

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $payments = array_map('formatPayment', $rows);
        sendJson(['payments' => $payments]);
    } catch (PDOException $e) {
        sendJson(['error' => 'Unable to load payments.'], 500);
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(['error' => 'Method not allowed.'], 405);
}

$body = json_decode(file_get_contents('php://input'), true) ?? [];
$action = trim((string) ($body['action'] ?? 'create'));
$paymentRef = trim((string) ($body['payment_ref'] ?? ''));
$customerName = trim((string) ($body['customer'] ?? ''));
$customerRef = trim((string) ($body['cusid'] ?? ''));
$rentalRef = trim((string) ($body['rentalId'] ?? ''));
$paymentDate = trim((string) ($body['date'] ?? ''));
$due = isset($body['due']) ? floatval($body['due']) : 0.0;
$paid = isset($body['paid']) ? floatval($body['paid']) : 0.0;
$balance = isset($body['balance']) ? floatval($body['balance']) : ($due - $paid);
$method = trim((string) ($body['method'] ?? 'Cash'));
$referenceNo = trim((string) ($body['ref'] ?? ''));
$status = normalizeStatus(trim((string) ($body['status'] ?? 'pending')));
$notes = trim((string) ($body['notes'] ?? ''));

if ($action === 'delete') {
    if ($paymentRef === '') {
        sendJson(['error' => 'Missing payment reference.'], 400);
    }

    try {
        $stmt = $conn->prepare('SELECT status FROM payments WHERE payment_ref = :payment_ref LIMIT 1');
        $stmt->execute([':payment_ref' => $paymentRef]);
        $paymentRow = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$paymentRow) {
            sendJson(['error' => 'Payment not found.'], 404);
        }
        if (!isPaymentDeletable((string) ($paymentRow['status'] ?? ''))) {
            sendJson(['error' => 'Only paid or refunded payments can be deleted.'], 400);
        }

        $stmt = $conn->prepare('DELETE FROM payments WHERE payment_ref = :payment_ref');
        $stmt->execute([':payment_ref' => $paymentRef]);
        sendJson(['success' => true]);
    } catch (PDOException $e) {
        sendJson(['error' => 'Unable to delete payment.'], 500);
    }
}

if ($customerName === '' || $rentalRef === '' || $paymentDate === '') {
    sendJson(['error' => 'Customer name, rental ID, and payment date are required.'], 400);
}

if ($customerRef === '' && $rentalRef !== '') {
    try {
        $stmt = $conn->prepare('SELECT b.customer_ref AS booking_customer_ref, c.customer_ref AS customer_ref FROM bookings b LEFT JOIN customers c ON c.id = b.customer_id WHERE b.booking_ref = :booking_ref LIMIT 1');
        $stmt->execute([':booking_ref' => $rentalRef]);
        $found = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($found) {
            $customerRef = trim((string) ($found['customer_ref'] ?? $found['booking_customer_ref'] ?? ''));
        }
    } catch (PDOException $e) {
        // continue with provided customer_ref if lookup fails
    }
}

$balance = $due - $paid;
$referenceNo = $referenceNo === '' ? null : $referenceNo;

try {
    $paymentRef = runInTransaction($conn, function () use (
        $conn,
        $action,
        $paymentRef,
        $customerName,
        $customerRef,
        $rentalRef,
        $paymentDate,
        $due,
        $paid,
        $balance,
        $method,
        $referenceNo,
        $status,
        $notes
    ) {
        if ($action === 'create') {
            $maxId = (int) $conn->query('SELECT MAX(id) FROM payments')->fetchColumn();
            $paymentRef = 'PAY-' . str_pad((string) ($maxId + 1), 4, '0', STR_PAD_LEFT);

            $stmt = $conn->prepare(
                'INSERT INTO payments (payment_ref, customer_name, customer_ref, rental_ref, payment_date, due, paid, balance, method, reference_no, status, notes) VALUES (:payment_ref, :customer_name, :customer_ref, :rental_ref, :payment_date, :due, :paid, :balance, :method, :reference_no, :status, :notes)'
            );
            $stmt->execute([
                ':payment_ref' => $paymentRef,
                ':customer_name' => $customerName,
                ':customer_ref' => $customerRef !== '' ? $customerRef : null,
                ':rental_ref' => $rentalRef,
                ':payment_date' => $paymentDate,
                ':due' => $due,
                ':paid' => $paid,
                ':balance' => $balance,
                ':method' => $method,
                ':reference_no' => $referenceNo,
                ':status' => $status,
                ':notes' => $notes,
            ]);
            updateBookingPaymentStatus($conn, $rentalRef, $status);
        } elseif ($action === 'update') {
            if ($paymentRef === '') {
                sendJson(['error' => 'Missing payment reference.'], 400);
            }

            $stmt = $conn->prepare(
                'UPDATE payments SET customer_name = :customer_name, customer_ref = :customer_ref, rental_ref = :rental_ref, payment_date = :payment_date, due = :due, paid = :paid, balance = :balance, method = :method, reference_no = :reference_no, status = :status, notes = :notes WHERE payment_ref = :payment_ref'
            );
            $stmt->execute([
                ':customer_name' => $customerName,
                ':customer_ref' => $customerRef !== '' ? $customerRef : null,
                ':rental_ref' => $rentalRef,
                ':payment_date' => $paymentDate,
                ':due' => $due,
                ':paid' => $paid,
                ':balance' => $balance,
                ':method' => $method,
                ':reference_no' => $referenceNo,
                ':status' => $status,
                ':notes' => $notes,
                ':payment_ref' => $paymentRef,
            ]);
            updateBookingPaymentStatus($conn, $rentalRef, $status);
        } else {
            sendJson(['error' => 'Unknown action.'], 400);
        }

        return $paymentRef;
    });

    $stmt = $conn->prepare('SELECT * FROM payments WHERE payment_ref = :payment_ref LIMIT 1');
    $stmt->execute([':payment_ref' => $paymentRef]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (! $row) {
        sendJson(['error' => 'Payment could not be found after saving.'], 500);
    }

    sendJson(['payment' => formatPayment($row)]);
} catch (PDOException $e) {
    sendJson(['error' => 'Unable to save payment.'], 500);
} catch (Throwable $e) {
    sendJson(['error' => 'Unable to save payment.'], 500);
}
