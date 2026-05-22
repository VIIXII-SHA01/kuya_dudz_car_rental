<?php
// Test script: set env vars and POST to forgot_action.php for test user
putenv('SMTP_HOST=smtp.gmail.com');
putenv('SMTP_PORT=587');
putenv('SMTP_USER=marketingj786@gmail.com');
putenv('SMTP_PASS=orxk bcjn eqdf nzsb');
putenv('FROM_EMAIL=marketingj786@gmail.com');
putenv('FROM_NAME=KDCR Support');
putenv('MAIL_DEBUG=1');

$url = 'http://localhost/rent/php/forgot_action.php';
$data = ['email' => 'testuser@example.local'];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);
if ($res === false) {
    echo 'Curl error: ' . curl_error($ch) . "\n";
} else {
    echo $res . "\n";
}
curl_close($ch);
