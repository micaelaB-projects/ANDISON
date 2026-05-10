<?php
require 'C:/xamppp/htdocs/ANDISON/Andison/includes/mailer.php';

// Enable verbose debug output temporarily for testing
// I'll need to modify the mailer or just instantiate PHPMailer here.
// Actually, let's just make a standalone test script to see the exact auth error.
require_once __DIR__ . '/Andison/includes/phpmailer/PHPMailer.php';
require_once __DIR__ . '/Andison/includes/phpmailer/SMTP.php';
require_once __DIR__ . '/Andison/includes/phpmailer/Exception.php';
require_once __DIR__ . '/Andison/includes/mailer_config.php';

$mail = new PHPMailer\PHPMailer\PHPMailer(true);
try {
    $mail->SMTPDebug = 2; // Enable verbose debug output
    $mail->isSMTP();
    $mail->Host       = MAIL_SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_SMTP_USER;
    $mail->Password   = MAIL_SMTP_PASS;
    $mail->SMTPSecure = (MAIL_SMTP_PORT === 465) ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = MAIL_SMTP_PORT;

    $mail->setFrom(MAIL_SMTP_USER, MAIL_FROM_NAME);
    $mail->addAddress('johncedricreyes14@gmail.com');

    $mail->Subject = 'Test Auth';
    $mail->Body    = 'Test Auth Body';

    $mail->send();
    echo "Success!\n";
} catch (Exception $e) {
    echo "Error: {$mail->ErrorInfo}\n";
}

