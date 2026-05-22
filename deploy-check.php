<?php
header('Content-Type: text/html; charset=utf-8');
$checks = [
    'index.php (front controller)' => __DIR__ . '/index.php',
    'login.php (rewrite fallback)' => __DIR__ . '/login.php',
    '.htaccess' => __DIR__ . '/.htaccess',
    'layouts/login.php' => __DIR__ . '/layouts/login.php',
    'css/theme.css' => __DIR__ . '/css/theme.css',
    'javascript/login.js' => __DIR__ . '/javascript/login.js',
    'php/login_action.php' => __DIR__ . '/php/login_action.php',
    'databases/connection1.php' => __DIR__ . '/databases/connection1.php',
    'vendor/autoload.php' => __DIR__ . '/vendor/autoload.php',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>KDCR deploy check</title>
  <style>
    body { font-family: system-ui, sans-serif; margin: 2rem; }
    .ok { color: #0a7; }
    .bad { color: #c33; }
    code { background: #f4f4f4; padding: 0.1rem 0.35rem; }
  </style>
</head>
<body>
  <h1>KDCR deploy check</h1>
  <p>Delete <code>deploy-check.php</code> after your site works.</p>
  <ul>
<?php foreach ($checks as $label => $path): ?>
    <li>
      <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>:
      <?php if (is_file($path)): ?>
        <strong class="ok">found</strong>
      <?php else: ?>
        <strong class="bad">MISSING</strong>
      <?php endif; ?>
    </li>
<?php endforeach; ?>
  </ul>
  <p>mod_rewrite test: open <a href="/rent/login">/rent/login</a> or <a href="/rent/login.php">/rent/login.php</a></p>
</body>
</html>
