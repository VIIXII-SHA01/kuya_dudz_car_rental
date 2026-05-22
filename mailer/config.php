<?php

/**
 * SMTP settings. Loads rent/.env without putenv (works on InfinityFree).
 * Override with mailer/config.local.php on the server if needed.
 */

function parse_env_file($path): array
{
    $vars = [];
    if (! is_readable($path)) {
        return $vars;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return $vars;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        if (strpos($line, '=') === false) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if ($key === '') {
            continue;
        }
        if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"')
            || (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
            $value = substr($value, 1, -1);
        }
        $vars[$key] = $value;
    }

    return $vars;
}

function env_value(array $vars, string $key, $default = null)
{
    if (array_key_exists($key, $vars) && $vars[$key] !== '') {
        return $vars[$key];
    }
    $fromServer = $_ENV[$key] ?? $_SERVER[$key] ?? null;
    if ($fromServer !== null && $fromServer !== '') {
        return $fromServer;
    }
    $fromGetenv = getenv($key);
    if ($fromGetenv !== false && $fromGetenv !== '') {
        return $fromGetenv;
    }

    return $default;
}

function env_bool(array $vars, string $key, bool $default = false): bool
{
    $raw = env_value($vars, $key, $default ? '1' : '0');

    return in_array(strtolower((string) $raw), ['1', 'true', 'yes', 'on'], true);
}

function normalizeSmtpPassword($pass)
{
    $pass = trim((string) $pass);
    if (preg_match('/^[A-Za-z0-9]{4}( [A-Za-z0-9]{4}){3}$/', $pass)) {
        return str_replace(' ', '', $pass);
    }

    return $pass;
}

$envVars = array_merge(
    parse_env_file(__DIR__ . '/../.env'),
    parse_env_file(__DIR__ . '/.env')
);

$config = [
    'smtp_host' => env_value($envVars, 'SMTP_HOST', 'smtp.gmail.com'),
    'smtp_port' => (int) env_value($envVars, 'SMTP_PORT', 587),
    'smtp_user' => env_value($envVars, 'SMTP_USER', ''),
    'smtp_pass' => normalizeSmtpPassword(env_value($envVars, 'SMTP_PASS', '')),
    'smtp_secure' => strtolower((string) env_value($envVars, 'SMTP_SECURE', 'tls')),
    'from_email' => env_value($envVars, 'FROM_EMAIL', env_value($envVars, 'SMTP_USER', '')),
    'from_name' => env_value($envVars, 'FROM_NAME', 'KDCR Support'),
    'mail_debug' => env_bool($envVars, 'MAIL_DEBUG', false),
];

$localFile = __DIR__ . '/config.local.php';
if (is_readable($localFile)) {
    $overrides = require $localFile;
    if (is_array($overrides)) {
        if (! empty($overrides['smtp_pass'])) {
            $overrides['smtp_pass'] = normalizeSmtpPassword($overrides['smtp_pass']);
        }
        $config = array_merge($config, $overrides);
    }
}

if ($config['from_email'] === '' && $config['smtp_user'] !== '') {
    $config['from_email'] = $config['smtp_user'];
}

return $config;
