<?php
require_once __DIR__ . '/../vendor/autoload.php';
echo class_exists('PHPMailer\\PHPMailer\\PHPMailer') ? "PHPMailer OK\n" : "PHPMailer MISSING\n";
