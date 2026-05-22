<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send_mail($to, $subject, $htmlBody, $plainText = null)
{
    $cfg = include __DIR__ . '/config.php';
    $debug = getenv('MAIL_DEBUG');
    $debugOutput = '';

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $cfg['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = $cfg['smtp_user'];
    $mail->Password = $cfg['smtp_pass'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $cfg['smtp_port'];
    $mail->SMTPAutoTLS = true;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom($cfg['from_email'], $cfg['from_name']);
    $mail->addReplyTo($cfg['from_email'], $cfg['from_name']);
    $mail->addAddress($to);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $htmlBody;
    $mail->AltBody = $plainText ?: trim(strip_tags(preg_replace('/<style[^>]*>.*?<\/style>/is', '', $htmlBody)));
    $mail->XMailer = 'KDCR Mailer';

    if ($debug) {
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function($str, $level) use (&$debugOutput) {
            $debugOutput .= "PHPMailDebug: $str\n";
        };
    }

    try {
        $mail->send();
        return true;
    } catch (Exception $e) {
        $error = $mail->ErrorInfo ?: $e->getMessage();
        error_log('Mailer error: ' . $error);

        if ($cfg['smtp_host'] === 'smtp.gmail.com' && $cfg['smtp_port'] === 587) {
            try {
                $mail->clearAddresses();
                $mail->Port = 465;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->SMTPAutoTLS = false;
                $mail->addAddress($to);
                $mail->send();
                return true;
            } catch (Exception $e2) {
                $error = $mail->ErrorInfo ?: $e2->getMessage();
                error_log('Mailer fallback error: ' . $error);
            }
        }

        if ($debug) {
            return trim($error . '\n' . $debugOutput);
        }
        return false;
    }
}
