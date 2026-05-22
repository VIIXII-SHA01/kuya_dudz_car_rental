<?php
// Fallback when mod_rewrite is unavailable: /rent/login.php
$_SERVER['REQUEST_URI'] = '/rent/login';
require __DIR__ . '/index.php';
