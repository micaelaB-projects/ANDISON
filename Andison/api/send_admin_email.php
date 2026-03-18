<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

function andison_email_response(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Allow: POST');
    andison_email_response(405, [
        'success' => false,
        'message' => 'Method not allowed.'
    ]);
}

$rawBody = file_get_contents('php://input');
$payload = null;

if (is_string($rawBody) && trim($rawBody) !== '') {
    $decoded = json_decode($rawBody, true);
    if (is_array($decoded)) {
        $payload = $decoded;
    }
}

if (!is_array($payload)) {
    $payload = $_POST;
}

$subject = trim((string)($payload['subject'] ?? 'Client Inquiry'));
$message = trim((string)($payload['message'] ?? ''));
$senderName = trim((string)($payload['sender_name'] ?? ''));
$senderEmail = trim((string)($payload['sender_email'] ?? ''));
$pageUrl = trim((string)($payload['page_url'] ?? ''));

$subject = str_replace(["\r", "\n"], ' ', $subject);
$senderName = str_replace(["\r", "\n"], ' ', $senderName);

if ($subject === '' || strlen($subject) > 180) {
    andison_email_response(422, [
        'success' => false,
        'message' => 'Please enter a valid subject.'
    ]);
}

if ($message === '' || strlen($message) > 6000) {
    andison_email_response(422, [
        'success' => false,
        'message' => 'Please enter a valid message.'
    ]);
}

if ($senderEmail === '' || !filter_var($senderEmail, FILTER_VALIDATE_EMAIL)) {
    andison_email_response(422, [
        'success' => false,
        'message' => 'Please enter a valid From email address.'
    ]);
}

if (strlen($senderName) > 120) {
    andison_email_response(422, [
        'success' => false,
        'message' => 'Name is too long.'
    ]);
}

if ($senderName === '') {
    $senderName = 'Website Client';
}

$adminEmail = 'ceddreyes21@gmail.com';

$mailerPath = realpath(__DIR__ . '/../includes/mailer.php');
if ($mailerPath === false || !is_file($mailerPath)) {
    andison_email_response(500, [
        'success' => false,
        'message' => 'Mail service is not available right now.'
    ]);
}

require_once $mailerPath;

if (!function_exists('andison_send_mail')) {
    andison_email_response(500, [
        'success' => false,
        'message' => 'Mail service is not available right now.'
    ]);
}

if (function_exists('andison_mailer_is_configured') && !andison_mailer_is_configured()) {
    andison_email_response(503, [
        'success' => false,
        'message' => 'Mail service is temporarily unavailable. Please try again later.'
    ]);
}

$safeSubject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
$safeSenderName = htmlspecialchars($senderName, ENT_QUOTES, 'UTF-8');
$safeSenderEmail = htmlspecialchars($senderEmail, ENT_QUOTES, 'UTF-8');
$safeMessage = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));
$safePageUrl = htmlspecialchars($pageUrl, ENT_QUOTES, 'UTF-8');
$safeIpAddress = htmlspecialchars((string)($_SERVER['REMOTE_ADDR'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8');
$safeUserAgent = htmlspecialchars((string)($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8');

$pageBlock = '';
if ($safePageUrl !== '') {
    $pageBlock = '<tr><td style="padding:8px 0;font-size:13px;color:#64748b;width:130px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Page</td><td style="padding:8px 0;font-size:14px;color:#111827;word-break:break-all;">' . $safePageUrl . '</td></tr>';
}

$emailBody = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body style="margin:0;padding:0;background:#f1f5f9;font-family:Segoe UI,Arial,sans-serif;">'
    . '<div style="max-width:640px;margin:28px auto;background:#ffffff;border-radius:14px;overflow:hidden;box-shadow:0 10px 30px rgba(15,23,42,0.12);">'
    . '<div style="padding:24px 28px;background:linear-gradient(135deg,#2B11DB,#1b0b93);color:#fff;">'
    . '<div style="font-size:21px;font-weight:800;">New Client Email</div>'
    . '<div style="font-size:13px;opacity:0.9;margin-top:6px;">Sent from website direct compose form</div>'
    . '</div>'
    . '<div style="padding:24px 28px;">'
    . '<table style="width:100%;border-collapse:collapse;margin-bottom:20px;">'
    . '<tr><td style="padding:8px 0;font-size:13px;color:#64748b;width:130px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Subject</td><td style="padding:8px 0;font-size:14px;font-weight:700;color:#111827;">' . $safeSubject . '</td></tr>'
    . '<tr style="background:#f8fafc;"><td style="padding:8px 6px;font-size:13px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">From</td><td style="padding:8px 6px;font-size:14px;color:#111827;">' . $safeSenderName . ' &lt;' . $safeSenderEmail . '&gt;</td></tr>'
    . $pageBlock
    . '<tr style="background:#f8fafc;"><td style="padding:8px 6px;font-size:13px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">IP Address</td><td style="padding:8px 6px;font-size:14px;color:#111827;">' . $safeIpAddress . '</td></tr>'
    . '<tr><td style="padding:8px 0;font-size:13px;color:#64748b;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">User Agent</td><td style="padding:8px 0;font-size:13px;color:#334155;word-break:break-all;">' . $safeUserAgent . '</td></tr>'
    . '</table>'
    . '<div style="border:1px solid #e2e8f0;border-radius:10px;padding:14px 16px;background:#fff;">'
    . '<div style="font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:0.6px;font-weight:700;margin-bottom:8px;">Message</div>'
    . '<div style="font-size:14px;color:#1f2937;line-height:1.6;">' . $safeMessage . '</div>'
    . '</div>'
    . '</div>'
    . '</div>'
    . '</body></html>';

$mailSubject = '[Website Email] ' . $subject;
$sent = andison_send_mail($adminEmail, $mailSubject, $emailBody, $senderEmail);

if (!$sent) {
    andison_email_response(500, [
        'success' => false,
        'message' => 'Unable to send your email at the moment. Please try again.'
    ]);
}

andison_email_response(200, [
    'success' => true,
    'message' => 'Your email has been sent successfully.'
]);
