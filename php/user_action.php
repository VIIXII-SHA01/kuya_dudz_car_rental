<?php
session_start();

require_once __DIR__ . '/../databases/connection1.php';
require_once __DIR__ . '/user_helpers.php';

header('Content-Type: application/json');

requireLoggedInUser();

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    ensureUsersSchema($conn);

    if ($method === 'GET') {
        if ($action === 'list') {
            if (!isAdminUser()) {
                http_response_code(403);
                echo json_encode(['error' => 'Admin access required.']);
                exit;
            }
            echo json_encode(['users' => listAllUsers($conn)]);
            exit;
        }

        $userId = currentUserId();
        $row = fetchUserById($conn, $userId);
        if (!$row) {
            http_response_code(404);
            echo json_encode(['error' => 'User not found.']);
            exit;
        }

        echo json_encode(['user' => formatUserRow($row)]);
        exit;
    }

    if ($method !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed.']);
        exit;
    }

    $body = $_POST;
    if (empty($body) && str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
        $decoded = json_decode(file_get_contents('php://input'), true);
        if (is_array($decoded)) {
            $body = $decoded;
        }
    }

    $action = $body['action'] ?? $action;

    switch ($action) {
        case 'save_user':
            if (!isAdminUser()) {
                http_response_code(403);
                echo json_encode(['error' => 'Admin access required.']);
                exit;
            }
            $result = saveManagedUser($conn, $body);
            echo json_encode(['success' => true] + $result);
            break;

        case 'set_status':
            if (!isAdminUser()) {
                http_response_code(403);
                echo json_encode(['error' => 'Admin access required.']);
                exit;
            }
            $userId = (int) ($body['user_id'] ?? 0);
            $status = normalizeUserStatus((string) ($body['status'] ?? ''));
            $result = setUserStatus($conn, $userId, $status);
            echo json_encode(['success' => true] + $result);
            break;

        case 'update_profile':
            $userId = currentUserId();
            $result = saveOwnProfile($conn, $userId, $body);
            echo json_encode(['success' => true, 'user' => formatUserRow(fetchUserById($conn, $userId) ?: [])] + $result);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action.']);
    }
} catch (RuntimeException $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} catch (Throwable $e) {
    error_log('user_action error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error. Please try again.']);
}
