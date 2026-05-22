<?php
session_start();

// Route dispatcher for clean URLs under /rent/ (front controller at site root)
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$baseDir = rtrim($scriptDir, '/');

if ($baseDir !== '' && $baseDir !== '/' && strpos($requestUri, $baseDir) === 0) {
    $requestUri = substr($requestUri, strlen($baseDir));
}

$route = trim($requestUri, '/');
if ($route === '' || $route === 'index.php') {
    $route = 'login';
}

$routes = [
    'login'          => 'login.php',
    'register'       => 'signup.php',
    'forgotpassword' => 'forgotpassword.php',
    'logout'         => 'logout.php',
    'dashboard'      => 'admindashboard.php',
    'bookings'       => 'adminbooking.php',
    'vehicles'       => 'adminvehicles.php',
    'drivers'        => 'admindrivers.php',
    'customers'      => 'admincustomers.php',
    'payments'       => 'adminpayments.php',
    'reports'        => 'adminreports.php',
    'profile'        => 'profile.php',
    'users'          => 'users.php',
];

$protectedRoutes = [
    'dashboard',
    'bookings',
    'vehicles',
    'drivers',
    'customers',
    'payments',
    'reports',
    'profile',
    'users',
];

$adminOnlyRoutes = [
    'users',
];

$publicRoutes = [
    'login',
    'register',
    'forgotpassword',
];

$loggedIn = !empty($_SESSION['logged_in']) && !empty($_SESSION['user']);

if (in_array($route, $protectedRoutes, true) && ! $loggedIn) {
    header('Location: /rent/login');
    exit;
}

if ($loggedIn && in_array($route, $adminOnlyRoutes, true)) {
    $role = strtolower((string) ($_SESSION['user']['role'] ?? ''));
    if ($role !== 'admin') {
        header('HTTP/1.1 403 Forbidden');
        echo '<h1>403 Forbidden</h1><p>Only administrators can access user management.</p>';
        exit;
    }
}

if (in_array($route, $publicRoutes, true) && $loggedIn) {
    header('Location: /rent/dashboard');
    exit;
}

if ($route === 'logout' && $loggedIn) {
    // let the logout page handle destruction
} elseif ($route === 'logout' && ! $loggedIn) {
    header('Location: /rent/login');
    exit;
}

if (! array_key_exists($route, $routes)) {
    header('HTTP/1.0 404 Not Found');
    echo '<h1>404 Not Found</h1>';
    echo '<p>The requested page "' . htmlspecialchars($route, ENT_QUOTES, 'UTF-8') . '" was not found.</p>';
    exit;
}

$page = realpath(__DIR__ . '/layouts/' . $routes[$route]);
$layoutDir = realpath(__DIR__ . '/layouts');

if (! $page || ! $layoutDir || strpos($page, $layoutDir) !== 0) {
    header('HTTP/1.0 404 Not Found');
    echo '<h1>404 Not Found</h1>';
    echo '<p>Layout file is missing on the server. Re-upload the <code>layouts/</code> folder (ZIP + Unzip or FTP).</p>';
    exit;
}

require $page;
