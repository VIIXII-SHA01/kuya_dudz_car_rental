<?php
putenv('SMTP_HOST=smtp.gmail.com');
putenv('SMTP_PORT=587');
putenv('SMTP_USER=marketingj786@gmail.com');
putenv('SMTP_PASS=orxk bcjn eqdf nzsb');
putenv('FROM_EMAIL=marketingj786@gmail.com');
putenv('FROM_NAME=KDCR Support');
putenv('MAIL_DEBUG=1');

require_once __DIR__ . '/../mailer/mailer.php';

$to = 'testuser@example.local';
$subject = 'CLI test';
$body = '<p>Test mail body</p>';

$result = send_mail($to, $subject, $body);
if ($result === true) {
    echo "send_mail returned true\n";
} elseif (is_string($result)) {
    echo "send_mail returned error: {$result}\n";
} else {
    echo "send_mail returned false\n";
}
