<?php
session_start();
require_once __DIR__ . '/../databases/connection1.php';

header('Content-Type: application/json');

function sendJson(array $payload, int $statusCode = 200): void {
    http_response_code($statusCode);
    echo json_encode($payload);
    exit;
}

function tableHasColumn(PDO $conn, string $table, string $column): bool {
    $stmt = $conn->prepare('SHOW COLUMNS FROM ' . $table . ' LIKE :column');
    $stmt->execute([':column' => $column]);
    return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
}

function formatPayment(array $row): array {
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
        'status' => $row['status'] ?? 'pending',
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

$balance = $due - $paid;
$referenceNo = $referenceNo === '' ? null : $referenceNo;

try {
    if ($action === 'create') {
        $maxId = (int) $conn->query('SELECT MAX(id) FROM payments')->fetchColumn();
        $paymentRef = 'PAY-' . str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);

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

    $stmt = $conn->prepare('SELECT * FROM payments WHERE payment_ref = :payment_ref LIMIT 1');
    $stmt->execute([':payment_ref' => $paymentRef]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (! $row) {
        sendJson(['error' => 'Payment could not be found after saving.'], 500);
    }

    sendJson(['payment' => formatPayment($row)]);
} catch (PDOException $e) {
    sendJson(['error' => 'Unable to save payment.'], 500);
}
