<?php

declare(strict_types=1);

require_once __DIR__ . '/phpmailer/Exception.php';
require_once __DIR__ . '/phpmailer/PHPMailer.php';
require_once __DIR__ . '/phpmailer/SMTP.php';
require_once __DIR__ . '/mailer_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Send an HTML email via SMTP.
 * Returns true on success, false on failure.
 */
function andison_send_mail(string $to, string $subject, string $htmlBody, string $replyTo = ''): bool
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = MAIL_SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_SMTP_USER;
        $mail->Password   = MAIL_SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = MAIL_SMTP_PORT;

        $mail->setFrom(MAIL_SMTP_USER, MAIL_FROM_NAME);
        $mail->addAddress($to);
        if ($replyTo !== '') {
            $mail->addReplyTo($replyTo);
        }

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->CharSet = 'UTF-8';

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('andison_send_mail error: ' . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Build and send inquiry notification email to the company.
 */
function andison_send_inquiry_notification(array $data, array $items): void
{
    $fullname      = htmlspecialchars($data['fullname'] ?? '');
    $company       = htmlspecialchars($data['company'] ?? '');
    $email         = htmlspecialchars($data['email'] ?? '');
    $phone         = htmlspecialchars($data['phone'] ?? '');
    $address       = htmlspecialchars($data['address'] ?? '');
    $contact_m     = htmlspecialchars($data['contact_method'] ?? 'email');
    $message       = htmlspecialchars($data['message'] ?? '');

    $items_html = '';
    if (!empty($items)) {
        $items_html = '<table style="width:100%;border-collapse:collapse;margin-top:8px;">'
            . '<tr style="background:#2B11DB;color:#fff;">'
            . '<th style="padding:10px 14px;text-align:left;font-size:13px;">Product</th>'
            . '<th style="padding:10px 14px;text-align:left;font-size:13px;">Brand</th>'
            . '<th style="padding:10px 14px;text-align:center;font-size:13px;">Qty</th>'
            . '</tr>';
        foreach ($items as $i => $item) {
            $bg = $i % 2 === 0 ? '#f9fafb' : '#fff';
            $items_html .= '<tr style="background:' . $bg . ';">'
                . '<td style="padding:10px 14px;font-size:13px;font-weight:700;">' . htmlspecialchars($item['name'] ?? '') . '</td>'
                . '<td style="padding:10px 14px;font-size:13px;color:#555;">' . htmlspecialchars($item['brand'] ?? '—') . '</td>'
                . '<td style="padding:10px 14px;font-size:13px;text-align:center;">' . (int)($item['qty'] ?? 1) . '</td>'
                . '</tr>';
        }
        $items_html .= '</table>';
    } else {
        $items_html = '<p style="color:#888;font-size:13px;">No specific products listed.</p>';
    }

    $body = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body style="margin:0;padding:0;background:#f0f2f8;font-family:Segoe UI,Arial,sans-serif;">
<div style="max-width:620px;margin:30px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 8px 32px rgba(43,17,219,0.10);">
  <div style="background:linear-gradient(135deg,#2B11DB,#1a0a8f);padding:28px 32px;color:#fff;">
    <div style="font-size:22px;font-weight:900;letter-spacing:-0.5px;">📋 New Inquiry Received</div>
    <div style="font-size:14px;opacity:0.85;margin-top:4px;">ANDISON INDUSTRIAL — Website Submission</div>
  </div>
  <div style="padding:28px 32px;">
    <table style="width:100%;border-collapse:collapse;margin-bottom:24px;">
      <tr><td style="padding:8px 0;font-size:13px;color:#888;width:140px;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;">Full Name</td><td style="padding:8px 0;font-size:14px;font-weight:700;color:#111;">' . $fullname . '</td></tr>
      <tr style="background:#f9fafb;"><td style="padding:8px 6px;font-size:13px;color:#888;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;">Company</td><td style="padding:8px 6px;font-size:14px;color:#111;">' . ($company ?: '—') . '</td></tr>
      <tr><td style="padding:8px 0;font-size:13px;color:#888;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;">Email</td><td style="padding:8px 0;font-size:14px;"><a href="mailto:' . $email . '" style="color:#2B11DB;">' . $email . '</a></td></tr>
      <tr style="background:#f9fafb;"><td style="padding:8px 6px;font-size:13px;color:#888;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;">Phone</td><td style="padding:8px 6px;font-size:14px;color:#111;">' . ($phone ?: '—') . '</td></tr>
      <tr><td style="padding:8px 0;font-size:13px;color:#888;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;">Address</td><td style="padding:8px 0;font-size:14px;color:#111;">' . nl2br($address) . '</td></tr>
      <tr style="background:#f9fafb;"><td style="padding:8px 6px;font-size:13px;color:#888;font-weight:700;text-transform:uppercase;letter-spacing:0.4px;">Preferred Contact</td><td style="padding:8px 6px;font-size:14px;color:#111;">' . ucfirst($contact_m) . '</td></tr>
    </table>

    <div style="margin-bottom:20px;">
      <div style="font-size:13px;font-weight:900;text-transform:uppercase;letter-spacing:0.5px;color:#888;margin-bottom:10px;">Products Requested</div>
      ' . $items_html . '
    </div>

    ' . ($message ? '<div style="background:#f3f4f6;border-radius:10px;padding:14px 16px;margin-bottom:20px;"><div style="font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:0.5px;color:#888;margin-bottom:6px;">Message</div><div style="font-size:14px;color:#374151;line-height:1.6;">' . nl2br($message) . '</div></div>' : '') . '

    <a href="mailto:' . $email . '?subject=Re: Your Inquiry - ANDISON INDUSTRIAL" style="display:inline-block;background:#00D7B3;color:#0b1b16;padding:12px 24px;border-radius:10px;font-weight:900;font-size:14px;text-decoration:none;margin-top:4px;">Reply to Customer</a>
  </div>
  <div style="background:#f9fafb;padding:16px 32px;font-size:12px;color:#aaa;border-top:1px solid #eee;">
    This email was automatically sent from the ANDISON INDUSTRIAL website inquiry form.
  </div>
</div>
</body></html>';

    andison_send_mail(
        MAIL_NOTIFY_TO,
        'New Inquiry from ' . ($data['fullname'] ?? 'a customer') . ' — ANDISON INDUSTRIAL',
        $body,
        $data['email'] ?? ''
    );
}
