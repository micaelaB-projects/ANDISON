<?php
// Contact information for dropdown (always needed)
$contact_phone = "+1(234) 567 8900";
$contact_phone2 = "+1(234) 567 8900";
$contact_phone3 = "+1(639) 977 803 7398";
$contact_email = "info@andison-industrial.com";

// Initialize contact variables for display
$phone2 = $contact_phone2;
$phone3 = $contact_phone3;

// Handle form submission and send email
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = isset($_POST['fullname']) ? htmlspecialchars(trim($_POST['fullname'])) : '';
    $company = isset($_POST['company']) ? htmlspecialchars(trim($_POST['company'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $address = isset($_POST['address']) ? htmlspecialchars(trim($_POST['address'])) : '';
    $contact_method = isset($_POST['contact_method']) ? htmlspecialchars($_POST['contact_method']) : 'email';
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';
    $items_json = isset($_POST['items_json']) ? $_POST['items_json'] : '[]';

    // Validate required fields
    if ($fullname && $company && $email && $phone && $address) {
        // Parse items JSON
        $items = json_decode($items_json, true) ?: [];

        // Build email content
        $to = 'lizette.macalindol@gmail.com';
        $subject = 'New Inquiry Form Submission from ' . $fullname;

        $items_list = '';
        if (!empty($items)) {
            $items_list = "<h3>Inquiry Items:</h3><ul style='list-style:none;padding:0;'>";
            foreach($items as $item) {
                $items_list .= "<li style='padding:8px 0;border-bottom:1px solid #eee;'>";
                $items_list .= "<strong>" . htmlspecialchars($item['name'] ?? '') . "</strong>";
                if (!empty($item['brand'])) $items_list .= " (" . htmlspecialchars($item['brand']) . ")";
                $items_list .= " - Qty: " . htmlspecialchars($item['qty'] ?? '1');
                $items_list .= "</li>";
            }
            $items_list .= "</ul>";
        } else {
            $items_list = "<p>No specific items listed.</p>";
        }

        $body = "
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .header { background: #2B11DB; color: white; padding: 15px; border-radius: 5px; }
        .section { margin: 20px 0; }
        .label { font-weight: bold; color: #2B11DB; }
        ul { list-style: none; padding-left: 0; }
        li { padding: 8px 0; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>
    <div class=\"header\">
        <h2>New Inquiry Submission</h2>
    </div>
    <div class=\"section\">
        <p><span class=\"label\">Full Name:</span> " . $fullname . "</p>
        <p><span class=\"label\">Company:</span> " . $company . "</p>
        <p><span class=\"label\">Email:</span> " . $email . "</p>
        <p><span class=\"label\">Phone:</span> " . ($phone ?: 'Not provided') . "</p>
        <p><span class=\"label\">Address:</span> " . nl2br($address) . "</p>
        <p><span class=\"label\">Preferred Contact Method:</span> " . ucfirst($contact_method) . "</p>
    </div>
    <div class=\"section\">" . $items_list . "</div>
    <div class=\"section\">
        <p><span class=\"label\">Message:</span></p>
        <p>" . nl2br($message ?: 'No message provided') . "</p>
    </div>
    <hr>
    <p style=\"font-size: 12px; color: #666;\">This inquiry was submitted via ANDISON INDUSTRIAL website.</p>
</body>
</html>
        ";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "From: " . $email . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";

        $mail_sent = mail($to, $subject, $body, $headers);

        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) { mkdir($upload_dir, 0755, true); }
            $file_tmp = $_FILES['file']['tmp_name'];
            $file_name = basename($_FILES['file']['name']);
            $file_path = $upload_dir . time() . '_' . $file_name;
            move_uploaded_file($file_tmp, $file_path);
        }

        $success_message = $mail_sent ? "Inquiry submitted successfully! We'll contact you soon." : "Error sending inquiry. Please try again.";
        echo "<script>alert('" . addslashes($success_message) . "'); if(" . ($mail_sent ? 'true' : 'false') . ") { localStorage.removeItem('inquiryItems'); window.location.href='inquirylist.php'; }</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiry List - ANDISON INDUSTRIAL</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            height: 100%;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            padding-top: 142px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        header {
            background: linear-gradient(135deg, #2B11DB 0%, #2B11DB 100%);
            color: white;
            padding: 14px 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1200;
            width: 100%;
        }

        .header-top {
            display: flex;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            gap: 20px;
            margin-bottom: 12px;
        }

        .logo {
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 0 0 auto;
        }

        .logo-box {
            background: transparent;
            color: #2b00d9;
            padding: 0;
            border-radius: 0;
            font-weight: 800;
            letter-spacing: 0.6px;
        }

        .logo-box img {
            height: 50px;
            width: auto;
            display: block;
        }

        .header-contact {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 13px;
            flex: 0 0 auto;
        }

        .contact-link {
            color: rgba(255,255,255,0.95);
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            padding-bottom: 8px;
            white-space: nowrap;
            position: relative;
            display: inline-block;
        }

        .contact-link::after {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%) scaleX(0);
            transform-origin: center;
            width: 64px;
            height: 3px;
            background: rgba(255,255,255,0.18);
            bottom: -6px;
            border-radius: 2px;
            transition: transform 220ms ease;
        }

        .contact-link:hover::after,
        .contact-link:focus-visible::after {
            transform: translateX(-50%) scaleX(1);
        }
        /* Contact popover */
        .contact-dropdown {
            position: relative;
            display: inline-block;
        }

        .contact-popover {
            position: absolute;
            left: 50%;
            top: calc(100% + 12px);
            width: 320px;
            background: #fff;
            color: #111;
            border-radius: 8px;
            padding: 14px 16px;
            box-shadow: 0 10px 30px rgba(10,10,20,0.12);
            opacity: 0;
            visibility: hidden;
            transform: translateX(-50%) translateY(-6px) scale(0.98);
            transition: opacity 180ms ease, transform 180ms ease, visibility 180ms;
            z-index: 120;
        }

        .contact-popover::before {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            top: -8px;
            width: 0;
            height: 0;
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-bottom: 10px solid #fff;
            filter: drop-shadow(0 -1px 0 rgba(0,0,0,0.03));
        }

        .contact-dropdown:hover:not(.closed) .contact-popover,
        .contact-dropdown:focus-within:not(.closed) .contact-popover {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0) scale(1);
        }

        /* mobile: click-to-open; .open class used instead of hover */
        @media (max-width: 768px) {
            .contact-dropdown:hover:not(.closed) .contact-popover,
            .contact-dropdown:focus-within:not(.closed) .contact-popover {
                opacity: 0;
                visibility: hidden;
                transform: translateX(-50%) translateY(-6px) scale(0.98);
            }
            .contact-dropdown.open .contact-popover {
                opacity: 1;
                visibility: visible;
                transform: translateX(-50%) translateY(0) scale(1);
            }
        }

        .contact-close {
            position: absolute;
            top: 8px;
            right: 8px;
            background: transparent;
            border: none;
            color: #666;
            font-weight: 700;
            font-size: 24px;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            line-height: 1;
        }

        .contact-close:hover { background: rgba(0,0,0,0.06); color: #333; }

        /* when user explicitly closes, keep hidden until they move away */
        .contact-dropdown.closed .contact-popover {
            opacity: 0 !important;
            visibility: hidden !important;
            transform: translateX(-50%) translateY(-6px) scale(0.98) !important;
        }

        .contact-list { list-style: none; margin: 0; padding: 6px 0; }
        .contact-list li { display:flex; gap:12px; align-items:center; padding:10px 6px; }
        .contact-list .icon { font-size:18px; width:28px; text-align:center; color:#2B11DB; }
        .contact-list a { color: #111; text-decoration:none; font-weight:600; }
        .contact-list a:hover { text-decoration:underline; }

        /* compact on mobile */
        @media (max-width: 768px) {
            .contact-popover { width: 240px; padding: 8px 10px; }
            .contact-list { padding: 2px 0; }
            .contact-list li { gap: 8px; padding: 6px 4px; }
            .contact-list .icon { font-size: 14px; width: 20px; }
            .contact-list a { font-size: 12px; }
        }

        .search-bar {
            flex: 1 1 auto;
            display: flex;
            justify-content: center;
            max-width: 600px;
            margin: 0 auto;
        }

        .search-bar .search-field {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
        }

        .search-bar input {
            width: 100%;
            height: 46px;
            padding: 10px 16px 10px 40px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 6px;
            font-size: 15px;
            background: rgba(255,255,255,0.95);
            color: #333;
        }

        .search-bar input::placeholder {
            color: #999;
        }

        .search-bar .search-field i {
            position: absolute;
            left: 12px;
            font-size: 16px;
            pointer-events: none;
            color: #666;
        }

        .search-btn {
            display: none;
        }

        .inquiry-btn,
        .cart-icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            text-decoration: none;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: linear-gradient(135deg,  #00E5C8  0%, #347aec 100%);
            position: relative;
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
            box-shadow: 0 4px 15px rgba(0,188,212,0.4);
            gap: 8px;
        }

        .inquiry-btn:hover,
        .cart-icon-wrapper:hover {
            background: linear-gradient(135deg, #00ACC1, #00796B);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,188,212,0.5);
            color: white;
        }

        .inquiry-btn .btn-icon { display: inline; }
        .inquiry-btn .btn-text { display: inline; }

        .cart-badge {
            background: #c70d0d;
            color: white;
            font-size: 11px;
            font-weight: 700;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(199,13,13,0.5);
            position: static;
            margin-left: 2px;
        }

        .cart-badge.hidden {
            display: none;
        }

        .right-actions {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 0 0 auto;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #00D7B3 0%, #00bfb3 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 9px 16px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            white-space: nowrap;
            transition: opacity 0.2s, transform 0.15s;
            font-family: inherit;
        }
        .back-btn:hover { opacity: 0.88; transform: translateY(-1px); color: #fff; }

        /* Navigation */
        nav {
            position: relative;
            background: rgba(0, 215, 179, 0.85);
            backdrop-filter: blur(10px);
            overflow: visible;
        }

        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 8px 0 120px; /* space for the left Browse toggle */
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            min-height: 52px;
            gap: 0;
            justify-content: center;
        }

        /* Pin the browse toggle to the left side of the nav area */
        .browse-toggle {
            position: absolute;
            left: 12px;
            top: 20%;
            transform: translateY(-50%);
            z-index: 80;
            background: transparent;
            border: none;
            color: white;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            cursor: pointer;
            font-size: 15px;
            line: height 6px;;
        }

        .nav-list {
            list-style: none;
            display: flex;
            flex-wrap: nowrap;
            gap: 30px;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .nav-list li { position: relative; }

        .nav-list a {
            text-decoration: none;
            display: block;
        }

        .nav-list a:hover { color: rgba(255,255,255,0.8); }

        /* Glowing underline + dark active background for top-level nav links */
        .nav-list > li > a {
            position: relative;
            padding: 10px 14px;
            color: white;
            transition: color 180ms ease, background 180ms ease;
        }

        .nav-list > li > a::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: 2px;
            transform: translateX(-50%) scaleX(0);
            transform-origin: center;
            width: 44px;
            height: 5px;
            border-radius: 6px;
            background: linear-gradient(90deg, #00ffd1 0%, #00d4aa 50%, #2B11DB 100%);
            box-shadow: 0 2px 10px rgba(0,212,170,0.35);
            pointer-events: none;
            transition: transform 180ms ease, width 180ms ease;
        }

        .nav-list > li > a:hover {
            background: rgba(0,0,0,0.10);
            border-radius: 6px;
        }

        .nav-list > li > a:hover::after {
            transform: translateX(-50%) scaleX(1);
            width: 44px;
        }

        .nav-list > li > a.active {
            background: rgba(0,0,0,0.14);
            color: #fff;
            font-weight: 700;
            border-radius: 6px;
            box-shadow: inset 0 -6px 18px rgba(0,0,0,0.06);
        }

        .nav-list > li > a.active::after {
            transform: translateX(-50%) scaleX(1);
            width: 44px;
        }

        .nav-dropdown {
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(8px);
            background: white;
            min-width: 280px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
            z-index: 110;
            padding: 16px;
            margin-top: 8px;
        }

        .nav-dropdown::before {
            content: '';
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-bottom: 10px solid white;
            filter: drop-shadow(0 -2px 2px rgba(0,0,0,0.05));
        }

        .nav-list > li:hover .nav-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }

        /* Keep dropdown visible when hovering over it */
        .nav-dropdown:hover {
            opacity: 1;
            visibility: visible;
        }

        .nav-dropdown h4 {
            color: #2b00d9;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f0f0f0;
        }

        .nav-dropdown ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-dropdown ul li {
            margin: 0;
        }

        .nav-dropdown ul a {
            color: #333;
            font-size: 14px;
            padding: 8px 12px;
            display: block;
            border-radius: 4px;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .nav-dropdown ul a:hover {
            background: #f0f5ff;
            color: #2B11DB;
        }

        .nav-dropdown p {
            color: #666;
            font-size: 13px;
            line-height: 1.6;
            margin: 0;
        }

        nav li:nth-child(3) .nav-dropdown {
            min-width: 650px;
            max-width: 650px;
            padding: 24px 28px;
        }

        .nav-dropdown ul a {
            display: block;
            border-radius: 4px;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .nav-dropdown ul a:hover {
            background: #f0f5ff;
            color: #2B11DB;
        }

        nav li:nth-child(3) .nav-dropdown ul {
            display: grid !important;
            grid-template-columns: repeat(5, 1fr) !important;
            gap: 12px 20px !important;
            margin-top: 16px !important;
        }

        nav li:nth-child(3) .nav-dropdown ul li {
            min-height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        nav li:nth-child(3) .nav-dropdown ul a {
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 60px !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a img {
            max-width: 85px;
            max-height: 45px;
            object-fit: contain;
            display: block;
            pointer-events: all;
            cursor: pointer;
        }

        nav li:nth-child(3) .nav-dropdown ul a {
            cursor: pointer;
        }

        /* Hero Section */
        .hero {
            position: relative;
            background: linear-gradient(135deg, rgba(43, 17, 219, 0.8) 0%, rgba(0, 215, 179, 0.8) 100%), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><rect fill="%23888888" width="1200" height="600"/></svg>');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 80px 20px;
            aspect-ratio: 16;
            min-height: 400px;
            max-height: 700px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 80px;
            z-index: 1;
            box-shadow: inset 0 0 60px rgba(0, 0, 0, 0.1);
        }

        .hero-slider {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            perspective: 1000px;
            overflow: hidden;
        }

        .hero-slide {
            position: absolute;
            width: 40%;
            aspect-ratio: 16 / 9;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.3;
            transition: all 0.1s ease;
            transform: translateX(0) scale(0.85);
            filter: blur(4px);
            overflow: hidden;
        }

        .hero-slide.prev {
            left: 8%;
            opacity: 0.35;
            transform: translateX(-50px) scale(0.8);
            filter: blur(5px);
        }

        .hero-slide.active {
            left: 30%;
            opacity: 1;
            transform: translateX(0) scale(1);
            filter: blur(0);
            z-index: 10;
        }

        .hero-slide.next {
            right: 8%;
            opacity: 0.35;
            transform: translateX(50px) scale(0.8);
            filter: blur(5px);
        }

        /* blurred full-bleed background taken from the slide's background-image */
        .hero-slide::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: inherit;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: blur(15px) brightness(0.7) saturate(1.3);
            z-index: 0;
        }

        /* subtle dark overlay above the blur to improve text contrast */
        .hero-slide::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.2);
            z-index: 1;
        }

        /* centered clear image card on top of the blurred background */
        .hero-content {
            max-width: 900px;
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 18px;
        }

        .hero-content h1,
        .hero-content p,
        .hero-content .cta-button {
            display: none;
        }

        .hero-thumb {
            width: 100%;
            height: 100%;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            border-radius: 12px;
            box-shadow: 0 18px 40px rgba(2,6,23,0.45);
            overflow: hidden;
            background-color: rgba(255,255,255,0.05);
            aspect-ratio: 16 / 9;
        }

        .hero-content {
            width: 100%;
            height: 100%;
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .hero-content h1,
        .hero-content p,
        .hero-content .cta-button {
            display: none;
        }

        .hero-indicators {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 20;
        }

        .hero-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            cursor: pointer;
            transition: background 0.1s;
        }

        .hero-dot.active {
            background: rgba(255,255,255,0.9);
        }

        .hero-dot:hover {
            background: rgba(255,255,255,0.7);
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            font-weight: 700;
            line-height: 1.2;
        }

        .hero p {
            font-size: 18px;
            margin-bottom: 30px;
            color: rgba(255, 255, 255, 0.9);
        }

        .cta-button {
            background: linear-gradient(135deg, #00D7B3 0%, #00C99A 100%);
            color: white;
            padding: 14px 40px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(0, 215, 179, 0.3);
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0, 215, 179, 0.4);
        }

        /* Section */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            width: 100%;
            box-sizing: border-box;
        }

        section {
            width: 100%;
            padding: 100px 20px;
            position: relative;
            z-index: 10;
            background: white;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-top: 1px solid rgba(0, 0, 0, 0.04);
        }

        section h2 {
            text-align: center;
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 16px;
            color: #2B11DB;
            width: 100%;
            background: linear-gradient(90deg, #1565C0 0%, #00BCD4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }
    
        .section-description {
            text-align: center;
            max-width: 750px;
            margin: 0 auto 60px;
            color: #8B4513;
            line-height: 1.9;
            width: 100%;
            box-sizing: border-box;
            padding: 0 20px;
            font-size: 15px;
            font-weight: 500;
        }

        /* Product Highlights */
        .highlights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
            gap: 40px;
            margin-bottom: 50px;
            width: 100%;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            box-sizing: border-box;
            padding: 0 20px;
        }

        .product-card {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e8eef7;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: transform 0.4s cubic-bezier(0.23, 1, 0.32, 1), box-shadow 0.4s ease;
        }

        .product-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 40px rgba(43, 17, 219, 0.15);
        }

        .product-image {
            width: 100%;
            aspect-ratio: 16 / 9;
            min-height: 320px;
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 60px;
            position: relative;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: none;
        }

        .product-image iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .product-image video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: none;
        }

        .play-btn {
            width: 60px;
            height: 60px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 30px;
            cursor: pointer;
            transition: background 0.1s;
        }

        .play-btn:hover {
            background: rgba(0, 0, 0, 0.9);
        }

        .product-info {
            padding: 28px 24px;
            background: white;
            width: 100%;
            box-sizing: border-box;
            border-top: 1px solid #f0f0f0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .product-info h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 6px;
            color: #2B11DB;
            line-height: 1.4;
        }

        .product-info p {
            font-size: 15px;
            color: #666;
            line-height: 1.7;
            margin: 0;
        }

        /* Service Cards - Old Layout */
        .services-grid {
            display: flex;
            flex-direction: column;
            gap: 28px;
            width: 100%;
            max-width: 1050px;
        }

        .service-card {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
            background: white;
            border-radius: 16px;
            padding: 48px 44px;
            border: 1px solid #E0E3FF;
            box-shadow: 0 4px 16px rgba(30, 136, 229, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .service-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgba(30, 136, 229, 0.15), 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .service-card.reverse {
            direction: rtl;
        }

        .service-card.reverse > * {
            direction: ltr;
        }

        .service-badge {
            display: inline-block;
            background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 24px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 14px;
            box-shadow: 0 4px 12px rgba(30, 136, 229, 0.25);
        }

        .service-card.teal .service-badge {
            background: linear-gradient(135deg, #00bcd4 0%, #00897b 100%);
            box-shadow: 0 4px 12px rgba(0, 188, 212, 0.25);
        }

        .service-content h3 {
            font-size: 26px;
            font-weight: 800;
            color: #1e88e5;
            margin-bottom: 18px;
            line-height: 1.3;
            letter-spacing: -0.3px;
        }

        .service-card.teal .service-content h3 {
            color: #00bcd4;
        }

        .service-content p {
            font-size: 14px;
            color: #8B4513;
            line-height: 1.85;
            margin: 0;
        }

        .service-icon-box {
            width: 100%;
            aspect-ratio: 4 / 3;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: linear-gradient(135deg, #1e88e5 0%, #00bcd4 100%);
            font-size: 68px;
            color: white;
            box-shadow: 0 8px 24px rgba(30, 136, 229, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .service-card.teal .service-icon-box {
            background: linear-gradient(135deg, #00bcd4 0%, #00897b 100%);
            box-shadow: 0 8px 24px rgba(0, 188, 212, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        /* Featured Section */
        .featured-section {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            padding: 70px 60px;
            border-radius: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 70px;
            align-items: center;
            box-shadow: 0 4px 20px rgba(43, 17, 219, 0.08);
            overflow: hidden;
            position: relative;
            max-width: 1100px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #e8eef7;
        }

        .featured-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 100% 0%, rgba(255,255,255,0.4) 0%, transparent 70%);
            pointer-events: none;
        }

        .featured-content {
            position: relative;
            z-index: 2;
        }

        .featured-badge {
            display: inline-block;
            background: linear-gradient(135deg, #00D7B3 0%, #00C99A 100%);
            color: white;
            padding: 8px 18px;
            border-radius: 25px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1.2px;
            margin-bottom: 24px;
            text-transform: uppercase;
            box-shadow: 0 4px 12px rgba(0, 215, 179, 0.3);
        }

        .featured-content h3 {
            font-size: 40px;
            font-weight: 800;
            margin-bottom: 12px;
            color: #2B11DB;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.5px;
        }

        .featured-content h3::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #2B11DB 0%, #00d4aa 100%);
            margin-top: 16px;
            margin-bottom: 24px;
            border-radius: 2px;
        }

        .featured-meta {
            display: flex;
            gap: 24px;
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            flex-wrap: wrap;
        }

        .featured-discount {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .featured-discount-badge {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
        }

        .featured-offer-text {
            color: #ff6b6b;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .featured-event-info {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .featured-event-detail {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #333;
        }

        .featured-event-detail strong {
            color: #1a1a1a;
            font-weight: 600;
        }

        .featured-event-detail i {
            color: #2B11DB;
            font-size: 16px;
        }

        .featured-content p {
            color: #555;
            margin-bottom: 32px;
            line-height: 1.9;
            font-size: 16px;
            font-weight: 500;
        }

        .featured-btn {
            background: linear-gradient(135deg, #2B11DB 0%, #1e0aa3 100%);
            color: white;
            padding: 14px 42px;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(43, 17, 219, 0.3);
            letter-spacing: 0.5px;
        }

        .featured-btn:hover {
            background: linear-gradient(135deg, #3d1ffa 0%, #2B11DB 100%);
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(43, 17, 219, 0.4);
        }

        .featured-btn:active {
            transform: translateY(-1px);
        }

        .featured-image {
            width: 100%;
            aspect-ratio: 16 / 9;
            min-height: 400px;
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 60px;
            box-shadow: 0 20px 40px rgba(43, 17, 219, 0.15);
            position: relative;
            z-index: 2;
            overflow: hidden;
            flex-shrink: 0;
            border: 1px solid #e8eef7;
        }

        .featured-image img {
            display: block !important;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            object-position: center !important;
            border-radius: 12px;
        }

        .featured-image video {
            display: block !important;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            border-radius: 12px;
        }

        .featured-image iframe {
            display: block !important;
            width: 100% !important;
            height: 100% !important;
            border: none !important;
            border-radius: 12px;
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, #1a0d7a 0%, #2B11DB 100%);
            color: white;
            padding: 60px 0 40px;
            text-align: center;
            margin-top: auto;
            width: 100vw;
            position: relative;
            left: 0;
            right: 0;
            margin-left: 0;
            margin-right: 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-content {
            width: 100%;
            margin: 0;
            padding: 0 20px;
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.95);
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            padding-bottom: 4px;
        }

        .footer-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: #00D7B3;
            transition: width 0.3s ease;
        }

        .footer-links a:hover::after {
            width: 100%;
        }

        .footer-copyright {
            font-size: 14px;
            opacity: 0.85;
            font-weight: 500;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            padding-top: 24px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            /* Single row: logo | search | inquiry | contact */
            .header-top {
                display: flex;
                flex-direction: row;
                flex-wrap: nowrap;
                align-items: center;
                gap: 8px;
                padding: 0 10px;
                margin-bottom: 8px;
            }

            .logo {
                flex: 0 0 auto;
            }

            .logo-box img {
                height: 36px;
            }

            .search-bar {
                position: static;
                transform: none;
                flex: 1 1 0;
                min-width: 0;
                width: auto;
                max-width: none;
                margin: 0;
            }

            .search-bar .search-field {
                width: 100%;
            }

            .search-bar input {
                width: 100%;
                height: 36px;
                font-size: 12px;
                padding: 6px 8px 6px 30px;
            }

            .search-bar .search-field i {
                font-size: 13px;
                left: 8px;
            }

            .right-actions {
                flex: 0 0 auto;
                display: flex;
                flex-direction: row;
                align-items: center;
                gap: 8px;
                margin-left: 8px;
                margin-right: 8px;
                padding-right: 8px;
            }

            .inquiry-btn,
            .cart-icon-wrapper {
                background: transparent !important;
                box-shadow: none !important;
                padding: 6px !important;
                font-size: 28px !important;
                position: relative;
            }

            .inquiry-btn .btn-text { display: none; }
            .inquiry-btn .btn-icon { font-size: 28px; }

            .cart-badge {
                background: #2196F3 !important;
                box-shadow: 0 2px 8px rgba(33,150,243,0.5) !important;
                width: 26px !important;
                height: 26px !important;
                font-size: 13px !important;
                position: absolute !important;
                top: -4px !important;
                right: -8px !important;
                margin-left: 0 !important;
            }

            .cart-badge.hidden { display: inline-flex !important; }

            .header-contact {
                display: none;
            }

            nav ul {
                flex-wrap: nowrap;
                gap: 0;
            }

            nav li {
                margin-right: 0;
            }

            .nav-inner {
                padding-left: 0;
                padding-right: 0;
                gap: 0;
                min-height: auto;
                overflow-x: hidden;
                overflow-y: visible;
                justify-content: center;
                flex-wrap: wrap;
            }

            .nav-inner::-webkit-scrollbar { display: none; }

            .nav-list {
                gap: 0;
                flex-wrap: wrap;
                flex-shrink: 1;
                justify-content: center;
            }

            .nav-list > li > a {
                white-space: normal;
                font-size: 11px;
                padding: 10px 8px;
            }

            .browse-toggle {
                font-size: 12px;
                padding: 6px 8px;
                gap: 4px;
            }

            .hero h1 {
                font-size: 32px;
            }
            
            .hero {
                aspect-ratio: auto;
                min-height: 260px;
                padding: 20px 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .hero-content {
                max-width: 100%;
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .hero-slide {
                width: 92% !important;
                left: 50% !important;
                transform: translateX(-50%) scale(1) !important;
                filter: blur(0) !important;
                opacity: 0 !important;
            }

            .hero-slide.active {
                width: 92% !important;
                left: 50% !important;
                transform: translateX(-50%) scale(1) !important;
                filter: blur(0) !important;
                opacity: 1 !important;
            }

            .hero-slide.prev,
            .hero-slide.next {
                opacity: 0 !important;
                pointer-events: none;
            }

            .hero-thumb {
                width: 100%;
                height: auto;
                max-width: 100%;
                aspect-ratio: 16 / 9 !important;
            }
            
            .product-image {
                aspect-ratio: 4 / 3;
                min-height: 240px;
            }
            
            .featured-image {
                aspect-ratio: 4 / 3;
                min-height: 260px;
            }

            .featured-section {
                grid-template-columns: 1fr;
                padding: 40px 28px;
                gap: 40px;
                border-radius: 16px;
            }

            .featured-content h3 {
                font-size: 28px;
                font-weight: 800;
            }

            .featured-meta {
                gap: 12px;
                padding-bottom: 12px;
            }

            .featured-event-info {
                gap: 12px;
            }

            .featured-event-detail {
                font-size: 13px;
            }

            .featured-btn {
                padding: 12px 32px;
                font-size: 14px;
            }

            .highlights-grid {
                grid-template-columns: 1fr;
                gap: 24px;
            }

            .services-grid {
                gap: 24px;
            }

            .service-card {
                grid-template-columns: 1fr;
                gap: 24px;
                padding: 24px;
            }

            .service-card.reverse {
                direction: ltr;
            }

            .service-badge {
                margin-bottom: 8px;
                font-size: 11px;
            }

            .service-content h3 {
                font-size: 20px;
                margin-bottom: 12px;
            }

            .service-content p {
                font-size: 14px;
                line-height: 1.7;
            }

            .service-icon-box {
                aspect-ratio: 1 / 1;
                font-size: 48px;
            }

            section h2 {
                font-size: 28px;
            }

            .section-description {
                font-size: 14px;
                margin-bottom: 28px;
            }

            .sidebar-overlay {
                width: 75%;
                max-width: 320px;
                padding: 12px 0;
            }

            .sidebar-overlay h3 {
                font-size: 14px;
                margin-bottom: 8px;
                padding: 0 12px;
            }

            .sidebar-list { 
                padding: 0;
            }

            .sidebar-list li { 
                border-bottom: none;
            }

            .sidebar-list a {
                font-size: 13px;
                padding: 10px 14px;
                gap: 12px;
                min-height: 36px;
                align-items: center;
            }

            .sidebar-list a:active {
                background: rgba(43, 17, 219, 0.08);
            }

            .sidebar-icon {
                width: 20px;
                height: 20px;
                font-size: 16px;
            }

            .sidebar-sublist {
                background: #f8f9fa;
                border-left: 3px solid #2B11DB;
                margin: 2px 0;
                padding: 4px 0 4px 12px;
            }

            .sidebar-sublist a {
                font-size: 12px;
                padding: 8px 14px 8px 42px;
                min-height: 32px;
            }

            .sidebar-nested-sublist {
                margin: 4px 0;
                padding: 6px 0 6px 14px;
                background: rgba(43, 17, 219, 0.06);
                border-left: 2px solid rgba(43, 17, 219, 0.3);
            }

            .sidebar-nested-sublist a {
                font-size: 11px;
                padding: 8px 14px 8px 36px;
                color: #5a6b7d;
                min-height: 30px;
                margin: 0;
            }

            .sidebar-nested-sublist a:hover,
            .sidebar-nested-sublist a:active {
                background: rgba(43, 17, 219, 0.14);
                color: #2B11DB;
                padding-left: 44px;
            }
        }

        /* Overlay sidebar (full-height left panel) */
        .overlay-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.3);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s;
            z-index: 60;
        }

        .overlay-backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        .sidebar-overlay {
            position: fixed;
            left: 0;
            top: calc(14px + 50px + 14px + 12px + 52px);
            bottom: 0;
            width: 320px;
            max-width: 80%;
            background: #fff;
            box-shadow: 4px 0 24px rgba(0,0,0,0.15);
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 70;
            padding: 20px 12px;
            overflow-y: auto;
        }

        .sidebar-overlay.active {
            transform: translateX(0);
        }

        .sidebar-overlay h3 {
            font-size: 18px;
            margin-bottom: 24px;
            color: #222;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .sidebar-list { list-style: none; padding: 0; margin: 0; }
        .sidebar-list li { border-bottom: 1px solid #e5e7eb; }
        .sidebar-list li:last-child { border-bottom: none; }
        .sidebar-list a { 
            display: flex; 
            gap: 12px; 
            padding: 16px 12px; 
            color: #1f2937; 
            text-decoration: none; 
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
            font-size: 15px;
            min-height: 48px;
        }
        .sidebar-list a:hover { 
            background: #f3f4f6; 
            color: #2B11DB;
            padding-left: 16px;
        }
        .sidebar-list a:active {
            background: rgba(43, 17, 219, 0.08);
        }
        .sidebar-list li a.active {
            background: #f3f4f6;
            color: #2B11DB;
            font-weight: 600;
            border-left: 4px solid #2B11DB;
            padding-left: 12px;
        }
        .sidebar-list li a.active .sidebar-icon {
            color: #2B11DB;
        }
        .sidebar-icon { 
            color: #5b21b6; 
            width: 24px; 
            height: 24px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-list a .sidebar-label {
            flex: 1;
        }

        .sidebar-list a .sidebar-arrow {
            width: 20px;
            height: 20px;
            display: none;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-size: 14px;
            flex-shrink: 0;
            margin-left: 8px;
        }

        .sidebar-list li.has-sub a .sidebar-arrow {
            display: flex;
        }


        .sidebar-sublist li { 
            padding: 0; 
            border: none;
        }
        .sidebar-sublist a { 
            color: #4b5563; 
            font-size: 14px; 
            padding: 10px 16px 10px 48px; 
            display: block; 
            text-decoration: none;
            justify-content: flex-start;
            min-height: 42px;
            align-items: center;
        }
        .sidebar-sublist a:hover { 
            color: #2B11DB; 
            background: rgba(43, 17, 219, 0.08);
            padding-left: 52px;
        }

        /* Nested sublists */
        .sidebar-sublist li.has-nested-sub { position: relative; }
        .sidebar-sublist li.has-nested-sub > a { padding-right: 24px; }
        
        .nested-toggle {
            position: absolute;
            right: 0;
            top: 6px;
            background: transparent;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 0;
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        .nested-toggle:focus { outline: none; }
        .nested-toggle .bi { transition: transform 200ms ease; }
        .nested-toggle[aria-expanded="true"] .bi { transform: rotate(90deg); }


        .sidebar-nested-sublist li { 
            padding: 0;
            border: none;
        }
        .sidebar-nested-sublist a { 
            color: #5a6b7d; 
            font-size: 11px; 
            padding: 8px 10px 8px 32px; 
            display: block; 
            text-decoration: none;
            position: relative;
            transition: all 0.25s ease;
            border-radius: 4px;
            margin: 0;
            min-height: 30px;
        }

        .sidebar-list li.has-sub { position: relative; }
        .has-sub > a { padding-right: 40px; }


        .sidebar-close { 
            background: transparent; 
            border: none; 
            color: #9ca3af; 
            font-weight: 700; 
            cursor: pointer; 
            position: static;
            font-size: 16px;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
            flex-shrink: 0;
        }
        .sidebar-close:hover {
            color: #374151;
        }

        /* ============================================
           ANIMATIONS
           ============================================ */

        /* 1. HOVER EFFECTS */
        @keyframes hoverGlow {
            0% { box-shadow: 0 0 0px rgba(0, 212, 170, 0); }
            100% { box-shadow: 0 0 20px rgba(0, 212, 170, 0.4); }
        }

        @keyframes hoverScale {
            from { transform: scale(1); }
            to { transform: scale(1.05); }
        }

        @keyframes buttonBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }

        .product-card {
            transition: all 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            opacity: 1;
            transform: translateY(0);
            will-change: transform, opacity, box-shadow;
        }

        .product-card:hover {
            transform: translateY(-12px) scale(1.03);
            box-shadow: 0 25px 50px rgba(43,17,219,0.2);
            z-index: 1000;
        }

        .featured-btn:hover,
        .cta-button:hover {
            animation: buttonBounce 0.6s ease;
        }

        .nav-list a:hover {
            animation: hoverScale 0.3s ease;
        }

        .inquiry-btn:hover {
            animation: hoverGlow 0.4s ease forwards;
        }

        /* 2. SCROLLING ANIMATIONS */
        /* Use shared fadeUp keyframe for consistent reveals */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-60px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(60px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes zoomIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .scroll-animate { opacity: 0; transform: translateY(40px); transition: opacity 0s ease, transform 0s ease; }
        .scroll-animate.visible { }

        /* Match brands.php staggered reveal timings (faster) */
        .product-card { opacity: 1; transform: translateY(0); will-change: transform,opacity; }
        .product-card:nth-of-type(1){ --i:1; }
        .product-card:nth-of-type(2){ --i:2; }

        section h2 { opacity: 1; }
        .section-description { opacity: 1; }
        .featured-section { opacity: 1; }

        /* 3. PAGE TRANSITIONS */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pageExit {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(20px);
            }
        }

        body {
            opacity: 1;
        }

        section {
            opacity: 1;
        }

        section:nth-of-type(1) { animation-delay: 0s; }
        section:nth-of-type(2) { animation-delay: 0.1s; }
        section:nth-of-type(3) { animation-delay: 0.2s; }
        section:nth-of-type(4) { animation-delay: 0.3s; }

        /* 4. SELF-DRAWING ANIMATIONS */
        @keyframes drawBorder {
            to {
                stroke-dashoffset: 0;
            }
        }

        @keyframes pulseGlow {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(0, 212, 170, 0.7);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(0, 212, 170, 0);
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }
            100% {
                background-position: 1000px 0;
            }
        }

        .featured-badge {
            animation: pulseGlow 2s infinite;
        }

        .product-image {
            position: relative;
            overflow: hidden;
        }

        .product-image::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shimmer 2s infinite;
        }

        /* 5. TEXT ANIMATIONS */
        @keyframes typeWriter {
            from {
                width: 0;
            }
            to {
                width: 100%;
            }
        }

        @keyframes blinkCursor {
            0%, 49% {
                border-right-color: transparent;
            }
            50%, 100% {
                border-right-color: #00d4aa;
            }
        }

        @keyframes textGradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes textFadeIn {
            0% {
                opacity: 0;
                transform: translateY(10px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero h1 {
            animation: textFadeIn 0.8s ease;
        }

        .hero p {
            animation: textFadeIn 0.8s ease 0.2s both;
        }

        .product-info h3,
        .featured-content h3 {
            animation: textFadeIn 0.6s ease;
            position: relative;
        }

        
        .footer-links a {
            position: relative;
            animation: textFadeIn 0.6s ease;
        }

        .footer-links a::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #00d4aa;
            transition: width 0.3s ease;
        }

        .footer-links a:hover::before {
            width: 100%;
        }

        /* Stagger text animations */
        .nav-list li { opacity: 1; }

        .nav-list li:nth-child(1) { animation-delay: 0.1s; }
        .nav-list li:nth-child(2) { animation-delay: 0.2s; }
        .nav-list li:nth-child(3) { animation-delay: 0.3s; }
        .nav-list li:nth-child(4) { animation-delay: 0.4s; }
        .nav-list li:nth-child(5) { animation-delay: 0.5s; }
        .nav-list li:nth-child(6) { animation-delay: 0.6s; }

        /* Smooth transitions for all interactive elements */
        a, button, input, [role="button"] {
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @media (max-width: 768px) {
            .main-wrapper {
                grid-template-columns: 1fr;
                padding: 0 12px;
            }

            .sidebar {
                position: static;
            }
            .nav-inner { padding-left: 50px; padding-right: 6px; min-height: 40px; overflow-x: auto; overflow-y: visible; justify-content: flex-start; -webkit-overflow-scrolling: touch; scrollbar-width: none; }
            .nav-inner::-webkit-scrollbar { display: none; }
            .nav-list { position: static; transform: none; left: auto; flex-wrap: nowrap; flex-shrink: 0; gap: 0; }
            .browse-toggle { position: static; transform: none; left: auto; top: auto; padding: 6px 10px; }
        }

        /* Global animation utilities (shared) */
        @keyframes fadeUp { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }

        .reveal-hidden { opacity: 0; transform: translateY(18px); transition: opacity .6s ease, transform .6s ease; }
        .reveal { opacity: 1; transform: none; }
        .reveal-stagger > * { opacity: 0; transform: translateY(18px); }
        .reveal-stagger.revealed > * { opacity: 1; transform: none; transition: all .48s ease; }

        h1, .page-title { opacity: 1; }
        h1 + p, .page-subtitle { opacity: 1; }
        img:not(.no-anim) { opacity: 1; }

        @media (prefers-reduced-motion: reduce) {
            .reveal, .reveal-hidden, img { animation: none !important; transition: none !important; }
        }
        /* Ensure header/navigation/footer do not animate or move */
        header, nav, footer, .header-top, .nav-inner, .browse-toggle, .nav-list, .right-actions, .footer-content {
            animation: none !important;
            transition: none !important;
            transform: none !important;
            opacity: 1 !important;
        }

        /* Prevent individual nav items from receiving reveal animations */
        .nav-list li { animation: none !important; opacity: 1 !important; transform: none !important; }

        /* Overlay sidebar (full-height left panel) */
        .overlay-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.3);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s;
            z-index: 60;
        }

        .overlay-backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        .sidebar-overlay {
            position: fixed;
            left: 0;
            top: calc(14px + 50px + 14px + 12px + 52px);
            bottom: 0;
            right: auto;
            width: 380px;
            max-width: 90%;
            background: #fff;
            box-shadow: 4px 0 24px rgba(0,0,0,0.15);
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 70;
            padding: 12px 8px;
            overflow-y: auto;
        }

        .sidebar-overlay.active {
            transform: translateX(0);
        }

        .sidebar-overlay h3 {
            font-size: 14px;
            margin-bottom: 10px;
            color: #222;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .sidebar-list { list-style: none; padding: 8px 8px 0 8px; margin: 0; }
        .sidebar-list li { border-bottom: 1px solid #e5e7eb; }
        .sidebar-list li:last-child { border-bottom: none; }
        .sidebar-list a { 
            display: flex; 
            gap: 12px; 
            padding: 10px 10px; 
            color: #1f2937; 
            text-decoration: none; 
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
            font-size: 13px;
            min-height: 36px;
        }
        .sidebar-list a:hover { 
            background: #f3f4f6; 
            color: #2B11DB;
            padding-left: 16px;
        }
        .sidebar-list li a.active {
            background: #f3f4f6;
            color: #2B11DB;
            font-weight: 600;
            border-left: 4px solid #2B11DB;
            padding-left: 12px;
        }
        .sidebar-list li a.active .sidebar-icon {
            color: #2B11DB;
        }
        .sidebar-icon { 
            color: #5b21b6; 
            width: 24px; 
            height: 24px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-list a .sidebar-label {
            flex: 1;
        }

        .sidebar-list a .sidebar-arrow {
            width: 20px;
            height: 20px;
            display: none;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-size: 14px;
            flex-shrink: 0;
            margin-left: 8px;
        }

        .sidebar-list li.has-sub a .sidebar-arrow {
            display: flex;
        }

        .sidebar-sublist { 
            list-style: none; 
            margin: 2px 0; 
            padding: 4px 0 4px 12px;
            background: #f8f9fa;
            border-left: 3px solid #2B11DB;
            max-height: 500px;
            overflow: hidden;
            transition: max-height 0.3s ease, opacity 0.3s ease;
            opacity: 1;
            display: block;
        }
        
        .sidebar-sublist.collapsed {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }
        .sidebar-sublist li { 
            padding: 0; 
            border: none;
        }
        .sidebar-sublist a { 
            color: #4b5563; 
            font-size: 12px; 
            padding: 8px 12px 8px 38px; 
            display: block; 
            text-decoration: none;
            justify-content: flex-start;
            min-height: 32px;
            align-items: center;
        }
        .sidebar-sublist a:hover { 
            color: #2B11DB; 
            background: rgba(43, 17, 219, 0.08);
            padding-left: 52px;
        }

        /* Nested sublists */
        .sidebar-sublist li.has-nested-sub { position: relative; }
        .sidebar-sublist li.has-nested-sub > a { padding-right: 24px; }
        
        .nested-toggle {
            position: absolute;
            right: 0;
            top: 6px;
            background: transparent;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 0;
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }
        .nested-toggle:focus { outline: none; }
        .nested-toggle .bi { transition: transform 200ms ease; }
        .nested-toggle[aria-expanded="true"] .bi { transform: rotate(90deg); }

        .sidebar-nested-sublist { 
            list-style: none; 
            margin: 2px 0;
            padding: 4px 0 4px 12px;
            max-height: 500px;
            overflow: hidden;
            transition: max-height 0.3s ease, opacity 0.3s ease;
            opacity: 1;
            background: rgba(43, 17, 219, 0.05);
            border-left: 2px solid rgba(43, 17, 219, 0.3);
        }
        }
        
        .sidebar-nested-sublist.collapsed {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }
        .sidebar-nested-sublist li { 
            padding: 0;
            border: none;
        }
        .sidebar-nested-sublist a { 
            color: #5a6b7d; 
            font-size: 11px; 
            padding: 8px 10px 8px 32px; 
            display: block; 
            text-decoration: none;
            position: relative;
            transition: all 0.25s ease;
            border-radius: 4px;
            margin: 0;
            min-height: 30px;
            align-items: center;
        }
        .sidebar-nested-sublist a::before {
            content: '';
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 5px;
            height: 5px;
            background: linear-gradient(135deg, #2B11DB 0%, #6d28d9 100%);
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(43, 17, 219, 0.2);
        }
        .sidebar-nested-sublist a:hover { 
            color: #2B11DB;
            background: rgba(43, 17, 219, 0.12);
            padding-left: 36px;
            transform: none;
        }

        .sidebar-list li.has-sub { position: relative; }
        .has-sub > a { padding-right: 40px; }
        .sub-toggle {
            position: absolute;
            right: 8px;
            top: 12px;
            transform: none;
            background: transparent;
            border: 2px solid #d1d5db;
            color: #2B11DB;
            cursor: pointer;
            padding: 4px;
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            box-shadow: none;
            transition: all 0.2s ease;
            font-size: 0;
            z-index: 10;
        }
        .sub-toggle:hover {
            background: rgba(43, 17, 219, 0.1);
            border-color: #2B11DB;
            transform: scale(1.1);
        }
        .sub-toggle:active {
            transform: scale(0.95);
        }
        .sub-toggle:focus { outline: none; }
        .sub-toggle .bi { 
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            font-size: 14px;
            display: inline-flex;
        }
        .sub-toggle[aria-expanded="true"] .bi { transform: rotate(180deg); }

        .sidebar-close { 
            background: transparent; 
            border: none; 
            color: #9ca3af; 
            font-weight: 700; 
            cursor: pointer; 
            position: static;
            font-size: 16px;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s ease;
            flex-shrink: 0;
        }
        .sidebar-close:hover {
            color: #374151;
        }

        /* Mini Sidebar (always visible icon bar) */
        .mini-sidebar {
            position: fixed;
            left: 0;
            top: calc(14px + 50px + 14px + 52px);
            bottom: 0;
            width: 80px;
            background: linear-gradient(180deg, #2B11DB 0%, #1a0a7f 100%);
            box-shadow: 2px 0 16px rgba(0,0,0,0.2);
            z-index: 65;
            padding: 24px 12px;
            overflow-y: auto;
            overflow-x: hidden;
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.1) transparent;
        }

        .mini-sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .mini-sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .mini-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.1);
            border-radius: 3px;
        }

        .mini-sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.2);
        }

        .mini-sidebar.expanded {
            width: 280px;
            overflow-y: auto;
            padding: 24px 16px;
            scrollbar-width: thin;
            scrollbar-color: rgba(0,0,0,0.1) transparent;
            align-items: stretch;
        }

        .mini-sidebar.expanded::-webkit-scrollbar {
            width: 6px;
        }

        .mini-sidebar.expanded::-webkit-scrollbar-track {
            background: transparent;
        }

        .mini-sidebar.expanded::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.1);
            border-radius: 3px;
        }

        .mini-sidebar.expanded::-webkit-scrollbar-thumb:hover {
            background: rgba(0,0,0,0.2);
        }

        .mini-sidebar.active {
            display: flex !important;
            flex-direction: column;
            align-items: center;
        }

        .mini-sidebar.active.expanded {
            align-items: stretch;
        }

        .mini-sidebar-icon {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 24px;
            cursor: pointer;
            position: relative;
            border-radius: 8px;
            margin-bottom: 16px;
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1), justify-content 0.5s cubic-bezier(0.4, 0, 0.2, 1), padding 0.5s cubic-bezier(0.4, 0, 0.2, 1), background 0.3s ease, transform 0.2s ease;
            gap: 12px;
            padding: 0;
            flex-shrink: 0;
            min-width: 56px;
        }

        .mini-sidebar-icon .label {
            display: none;
            font-size: 11px;
            font-weight: 500;
            white-space: nowrap;
            flex: 1;
            text-align: left;
            opacity: 0;
            transition: opacity 0.5s cubic-bezier(0.4, 0, 0.2, 1) 0.1s;
        }

        .mini-sidebar.expanded .mini-sidebar-icon {
            width: 100%;
            justify-content: flex-start;
            padding: 14px;
            min-width: auto;
            margin-bottom: 12px;
            border-radius: 8px;
            background: rgba(255,255,255,0.08);
        }

        .mini-sidebar.expanded .mini-sidebar-icon:hover {
            background: rgba(255,255,255,0.15);
            transform: translateX(4px);
        }

        .mini-sidebar.expanded .mini-sidebar-icon .label {
            display: block;
            opacity: 1;
            color: #ffffff;
        }

        #miniSidebarMenuBar {
            justify-content: center;
            width: 56px;
            height: 56px;
            margin-bottom: 8px;
            margin-top: 0;
            flex-shrink: 0;
        }

        .mini-sidebar.expanded #miniSidebarMenuBar {
            justify-content: flex-start;
            width: 100%;
            height: auto;
            padding: 12px;
            margin-bottom: 8px;
        }

        .mini-sidebar.expanded .browse-label {
            display: inline-block !important;
        }

        .mini-sidebar-icon:hover {
            background: rgba(0, 215, 179, 0.15);
            transform: scale(1.08);
        }

        .mini-sidebar.expanded .mini-sidebar-icon:hover {
            transform: translateX(6px);
            background: rgba(0, 215, 179, 0.2);
        }

        .mini-sidebar-icon.active-icon {
            background: #00D7B3;
            color: #2B11DB;
            font-weight: 600;
        }

        .mini-sidebar-icon.active-icon .label {
            color: #2B11DB;
            font-weight: 600;
        }

        .mini-sidebar-icon .sub-indicator {
            position: absolute;
            bottom: 2px;
            right: 2px;
            background: rgba(0, 215, 179, 0.9);
            color: #ffffff;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            opacity: 0.9;
            transition: background 0.15s ease, color 0.15s ease, transform 0.2s ease;
            z-index: 999;
            cursor: pointer;
            pointer-events: auto;
            border: 1px solid #2B11DB;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .mini-sidebar-icon:hover .sub-indicator {
            opacity: 1;
            background: #00D7B3;
            color: #2B11DB;
            transform: scale(1.15);
        }

        .mini-sidebar-icon .sub-indicator:active {
            transform: translateY(0);
        }

        .mini-sidebar.expanded .mini-sidebar-icon .sub-indicator {
            position: static;
            background: #00D7B3;
            color: #2B11DB;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            margin-left: auto;
            opacity: 0.9;
            border: 1px solid #2B11DB;
            cursor: pointer;
            pointer-events: auto;
            z-index: 100;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .mini-sidebar-toggle {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 215, 179, 0.2);
            border: 1px solid rgba(0, 215, 179, 0.4);
            color: #00D7B3;
            cursor: pointer;
            border-radius: 8px;
            font-size: 20px;
            margin-top: auto;
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1), padding 0.5s cubic-bezier(0.4, 0, 0.2, 1), background 0.3s ease, transform 0.2s ease, border-color 0.3s ease;
            flex-shrink: 0;
        }

        .mini-sidebar-toggle:hover {
            background: rgba(0, 215, 179, 0.3);
            border-color: rgba(0, 215, 179, 0.6);
            transform: scale(1.08);
        }

        .mini-sidebar-toggle:active {
            transform: scale(0.95);
        }

        .mini-sidebar-toggle i {
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-block;
        }

        .mini-sidebar.expanded .mini-sidebar-toggle i {
            transform: rotate(180deg);
        }

        .mini-sidebar.expanded .mini-sidebar-toggle {
            width: 100%;
            padding: 14px;
            min-width: auto;
            margin-bottom: 12px;
        }

        /* Adjust main container for mini sidebar — collapsed by default on desktop */
        section,
        footer,
        .page-content,
        .main-content, 
        .category-container {
            margin-left: 0px;
            transition: margin-left 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* When expanded, increase margin */
        .mini-sidebar.expanded ~ section,
        .mini-sidebar.expanded ~ footer,
        .mini-sidebar.expanded ~ .page-content,
        .mini-sidebar.expanded ~ .main-content,
        .mini-sidebar.expanded ~ .category-container {
            margin-left: 280px;
        }

        @media (max-width: 992px) {
            section,
            footer,
            .page-content, 
            .main-content, 
            .category-container {
                margin-left: 0 !important;
            }

            .mini-sidebar {
                display: none !important;
            }
        }

        /* When sidebar is expanded (collapsed mini) */
        .sidebar-overlay.expanded {
            width: 380px;
        }

        .overlay-backdrop.expanded {
            display: none !important;
        }

        @media (max-width: 768px) {
            .mini-sidebar {
                top: calc(14px + 36px + 14px + 40px);
                width: 56px !important;
                transform: translateX(-100%);
                transition: transform 0.3s ease, width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .mini-sidebar.mobile-visible {
                transform: translateX(0);
            }
            .mini-sidebar.expanded {
                width: 240px !important;
            }
            .browse-toggle {
                display: inline-flex !important;
            }
            .browse-toggle .browse-text {
                display: inline !important;
            }
            .main-content, .category-container {
                margin-left: 0 !important;
            }
            /* Floating toggle button to show/hide mini sidebar on mobile */
            .mobile-sidebar-fab {
                display: flex !important;
            }
        }

        /* FAB button to toggle mini sidebar on mobile */
        .mobile-sidebar-fab {
            display: none;
            position: fixed;
            left: 0;
            top: 50%;
            transform: translateY(-50%) translateX(0);
            z-index: 70;
            width: 16px;
            height: 36px;
            background: #2B11DB;
            color: #fff;
            border: none;
            border-radius: 0 6px 6px 0;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            box-shadow: 2px 0 8px rgba(0,0,0,0.25);
            transition: transform 0.3s ease, background 0.2s;
        }
        .mobile-sidebar-fab:hover { background: #1a0aa8; }
        .mobile-sidebar-fab.open { transform: translateY(-50%) translateX(56px); }
        .mobile-sidebar-fab.open.wide { transform: translateY(-50%) translateX(240px); }

        /* Mini popover — mobile overrides */
        @media (max-width: 768px) {
            .mini-popover {
                border-radius: 0 12px 12px 0 !important;
                box-shadow: 4px 8px 24px rgba(0,0,0,0.28) !important;
                overflow: hidden !important;
            }
            .mini-popover::before { display: none !important; }
            .mini-popover-header {
                border-radius: 0 !important;
                padding: 10px 14px !important;
                font-size: 13px !important;
                letter-spacing: 0.5px !important;
            }
            .mini-popover-body {
                padding: 6px 8px 8px 8px !important;
            }
            .mini-popover-list {
                padding: 0 !important;
            }
            .mini-popover-list::before {
                display: none !important;
            }
            .mini-popover-item {
                margin: 2px 0 !important;
                min-height: auto !important;
                padding-left: 4px !important;
                align-items: center !important;
            }
            .mini-popover-item .square {
                width: 6px !important;
                height: 6px !important;
                min-width: 6px !important;
                border-radius: 2px !important;
            }
            .mini-popover-item a {
                font-size: 12px !important;
                padding: 8px 10px !important;
                line-height: 1.2 !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                font-weight: 600 !important;
            }
            .mini-popover-item a:active {
                background: rgba(255,255,255,0.18) !important;
            }
            .mini-popover-item.has-subitems {
                padding-right: 30px !important;
            }
            .popover-expand-btn {
                height: 28px !important;
                width: 28px !important;
                right: 4px !important;
            }
            .popover-expand-btn .bi {
                font-size: 14px !important;
            }
            .popover-subitem {
                padding: 5px 10px 5px 18px !important;
                font-size: 11px !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
        }

        /* Mini popover styles for subcategories */
        .mini-popover {
            position: fixed;
            top: -9999px;
            left: -9999px;
            width: 380px;
            max-width: calc(100vw - 40px);
            background: linear-gradient(135deg, #1E88E5 0%, #00BCD4 100%);
            color: #fff;
            border-radius: 16px;
            box-shadow: 0 16px 40px rgba(30, 136, 229, 0.3), 0 2px 8px rgba(0,0,0,0.2);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px) scale(0.95);
            transition: opacity 180ms cubic-bezier(0.34, 1.56, 0.64, 1), transform 180ms cubic-bezier(0.34, 1.56, 0.64, 1), visibility 180ms ease;
            z-index: 1300;
            display: flex;
            flex-direction: column;
            height: auto;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.15);
        }
        .mini-popover.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }
        .mini-popover::before {
            content: '';
            position: absolute;
            left: -10px;
            top: calc(26px + var(--arrow-offset, 0px));
            width: 0; height: 0;
            border-top: 10px solid transparent;
            border-bottom: 10px solid transparent;
            border-right: 10px solid #1976D2;
            filter: drop-shadow(-2px 2px 2px rgba(0,0,0,0.12));
        }
        .mini-popover-header {
            background: linear-gradient(135deg, #1E88E5 0%, #1565C0 100%);
            color: #ffffff;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
            padding: 16px 20px;
            font-weight: 700;
            font-size: 16px;
            letter-spacing: 0.4px;
            line-height: 1.3;
        }
        .mini-popover-title { color: #ffffff; }
        .mini-popover-body {
            padding: 14px 16px 18px 16px;
            overflow: visible;
            flex: 1;
            background: linear-gradient(180deg, rgba(255,255,255,0.04) 0%, transparent 100%);
        }
        .mini-popover-list {
            list-style: none;
            margin: 0;
            padding: 0;
            position: relative;
        }
        .mini-popover-list::before {
            content: '';
            position: absolute;
            left: 24px;
            top: 6px;
            bottom: 6px;
            width: 2px;
            background: rgba(255,255,255,0.35);
            border-radius: 2px;
            display: none;
        }
        .mini-popover-item {
            position: relative;
            padding-left: 0;
            margin: 3px 0;
            display: flex;
            align-items: stretch;
            min-height: auto;
        }
        .mini-popover-item .square {
            position: absolute;
            left: 16px;
            top: 0;
            bottom: 0;
            margin: auto;
            width: 14px; height: 14px;
            border-radius: 3px;
            background: #7aa7ff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.18), inset 0 -1px 0 rgba(0,0,0,0.08);
            flex-shrink: 0;
            pointer-events: none;
            display: none;
        }
        .mini-popover-item a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            display: block;
            padding: 12px 14px;
            border-radius: 8px;
            transition: all 160ms cubic-bezier(0.34, 1.56, 0.64, 1);
            width: 100%;
            white-space: normal;
            overflow: visible;
            text-overflow: clip;
            line-height: 1.4;
            font-size: 14px;
            background: rgba(255,255,255,0.06);
            border-left: 3px solid transparent;
        }
        .mini-popover-item a:hover {
            background: rgba(255,255,255,0.16);
            transform: translateX(4px);
            border-left-color: rgba(255,255,255,0.5);
        }
        
        /* Expandable popover items */
        .mini-popover-item.has-subitems {
            flex-wrap: wrap;
            padding-right: 36px;
        }
        .popover-expand-btn {
            position: absolute;
            right: 8px;
            top: 0;
            bottom: 0;
            height: 32px;
            width: 32px;
            margin: auto;
            background: rgba(255,255,255,0.1);
            border: none;
            color: #ffffff;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 160ms cubic-bezier(0.34, 1.56, 0.64, 1);
            flex-shrink: 0;
            border-radius: 8px;
        }
        .popover-expand-btn:hover {
            background: rgba(255,255,255,0.22);
            transform: scale(1.08);
        }
        .popover-expand-btn:active {
            background: rgba(255,255,255,0.3);
            transform: scale(0.95);
        }
        .popover-expand-btn .bi {
            font-size: 18px;
            transition: transform 200ms cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .popover-expand-btn[aria-expanded="true"] .bi {
            transform: rotate(90deg);
        }
        
        .popover-subitems {
            width: 100%;
            margin-top: 8px;
            max-height: none;
            overflow: visible;
            transition: opacity 250ms ease;
            opacity: 1;
            padding-left: 0;
        }
        .popover-subitems.collapsed {
            display: none;
        }
        
        .popover-subitem {
            color: rgba(255,255,255,0.85) !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            padding: 6px 10px 6px 28px !important;
            display: block !important;
            text-decoration: none !important;
            border-radius: 6px !important;
            transition: all 120ms ease !important;
            position: relative;
        }
        .popover-subitem::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: #ffffff;
            opacity: 0.6;
        }
        .popover-subitem:hover {
            background: rgba(255,255,255,0.12) !important;
            transform: translateX(2px) !important;
            color: #ffffff !important;
        }

        /* ===== INQUIRY FORM STYLES ===== */

        /* Page Banner */
        .inq-banner {
            background: linear-gradient(135deg, #2B11DB 0%, #00D7B3 100%);
            padding: 28px 28px 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .inq-banner::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 220px; height: 220px;
            background: rgba(255,255,255,0.06);
            border-radius: 50%;
        }
        .inq-banner::after {
            content: '';
            position: absolute;
            bottom: -80px; left: -40px;
            width: 260px; height: 260px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
        }
        .inq-banner-icon {
            width: 46px; height: 46px;
            background: rgba(255,255,255,0.15);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #fff;
            margin-bottom: 10px;
            backdrop-filter: blur(8px);
            border: 2px solid rgba(255,255,255,0.25);
        }
        .inquiry-page-title {
            font-size: 22px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }
        .inquiry-page-subtitle {
            font-size: 13px;
            color: rgba(255,255,255,0.82);
            margin: 0;
        }
        .inq-banner-steps {
            display: flex;
            justify-content: center;
            gap: 0;
            margin-top: 18px;
            position: relative;
            z-index: 1;
        }
        .inq-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            position: relative;
            flex: 1;
            max-width: 130px;
        }
        .inq-step + .inq-step::before {
            content: '';
            position: absolute;
            left: -50%;
            top: 15px;
            width: 100%;
            height: 2px;
            background: rgba(255,255,255,0.25);
        }
        .inq-step {
            cursor: pointer;
        }
        .inq-step-num {
            width: 30px; height: 30px;
            background: rgba(255,255,255,0.18);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 800;
            color: #fff;
            border: 2px solid rgba(255,255,255,0.4);
            transition: background 0.25s, border-color 0.25s, transform 0.2s;
        }
        .inq-step:hover .inq-step-num {
            background: rgba(255,255,255,0.3);
            transform: scale(1.1);
        }
        .inq-step.active .inq-step-num {
            background: #fff;
            color: #2B11DB;
            border-color: #fff;
            transform: scale(1.12);
            box-shadow: 0 0 0 4px rgba(255,255,255,0.25);
        }
        .inq-step.active .inq-step-label {
            color: #fff;
            font-weight: 800;
        }
        .inq-step.completed .inq-step-num {
            background: #00D7B3;
            border-color: #00D7B3;
            color: #fff;
        }
        .inq-step.completed .inq-step-num::before {
            content: '\2713';
        }
        .inq-step.completed .inq-step-label {
            color: rgba(255,255,255,0.95);
        }
        .inq-step + .inq-step.completed::before,
        .inq-step.completed + .inq-step::before {
            background: rgba(0,215,179,0.5);
        }
        .inq-step-label {
            font-size: 10px;
            font-weight: 700;
            color: rgba(255,255,255,0.8);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            transition: color 0.25s;
        }

        /* Wrapper */
        .inquiry-wrapper {
            max-width: 820px;
            margin: 0 auto;
            padding: 0 0 60px;
        }
        .inq-form-body {
            padding: 32px 28px 0;
        }

        /* Cards */
        .inq-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(43,17,219,0.07), 0 1px 4px rgba(0,0,0,0.04);
            padding: 0;
            margin-bottom: 22px;
            overflow: hidden;
            border: 1px solid rgba(43,17,219,0.06);
            transition: box-shadow 0.25s;
        }
        .inq-card:hover {
            box-shadow: 0 8px 32px rgba(43,17,219,0.11), 0 2px 8px rgba(0,0,0,0.06);
        }
        .inq-card-body {
            padding: 24px 28px 28px;
        }
        .inq-section-header {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px 28px;
            background: linear-gradient(90deg, #f4f2ff 0%, #f0fff9 100%);
            border-bottom: 1px solid rgba(43,17,219,0.08);
        }
        .inq-section-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, #2B11DB 0%, #6247EA 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 16px;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(43,17,219,0.25);
        }
        .inq-section-icon.teal {
            background: linear-gradient(135deg, #00D7B3 0%, #00b49a 100%);
            box-shadow: 0 4px 12px rgba(0,215,179,0.3);
        }
        .inq-section-icon.warm {
            background: linear-gradient(135deg, #f7931e 0%, #f06292 100%);
            box-shadow: 0 4px 12px rgba(247,147,30,0.3);
        }
        .inq-section-title {
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: #2B11DB;
            line-height: 1.2;
        }
        .inq-section-desc {
            font-size: 12px;
            color: #888;
            margin-top: 2px;
        }

        /* Items table */
        .inq-table { width: 100%; border-collapse: collapse; }
        .inq-table thead tr {
            background: linear-gradient(90deg, #f0f2ff 0%, #e8f8f5 100%);
        }
        .inq-table thead th {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: #5b4de0;
            padding: 11px 16px;
            text-align: left;
        }
        .inq-table thead th:last-child { text-align: center; }
        .inq-table tbody tr {
            border-bottom: 1px solid #f5f5f5;
            transition: background 0.15s;
        }
        .inq-table tbody tr:hover { background: #fafbff; }
        .inq-table tbody tr:last-child { border-bottom: none; }
        .inq-table tbody td {
            padding: 13px 16px;
            font-size: 13px;
            color: #333;
            vertical-align: middle;
        }
        .inq-table tbody td:last-child { text-align: center; }
        .inq-product-name {
            font-weight: 700;
            color: #1a1a2e;
            display: block;
            line-height: 1.3;
        }
        .inq-product-brand {
            font-size: 11px;
            color: #2B11DB;
            font-weight: 600;
            display: block;
            margin-top: 2px;
        }
        .inq-qty-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .inq-qty-btn {
            width: 28px; height: 28px;
            background: #f0f2ff;
            border: 1px solid #d1d5f8;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 700;
            color: #2B11DB;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s;
            line-height: 1;
        }
        .inq-qty-btn:hover { background: #e0e4ff; }
        .inq-qty-input {
            width: 52px;
            border: 1.5px solid #d1d5f8;
            border-radius: 8px;
            padding: 5px 8px;
            font-size: 13px;
            font-weight: 700;
            text-align: center;
            outline: none;
            transition: border-color 0.2s;
            color: #1a1a2e;
        }
        .inq-qty-input:focus { border-color: #2B11DB; box-shadow: 0 0 0 3px rgba(43,17,219,0.08); }
        .inq-remove-btn {
            background: #fff0ef;
            color: #e53935;
            border: 1.5px solid #ffd7d6;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .inq-remove-btn:hover { background: #e53935; color: #fff; border-color: #e53935; }
        .inq-empty-msg {
            text-align: center;
            padding: 48px 20px;
        }
        .inq-empty-msg i { font-size: 40px; color: #d0d5f8; display: block; margin-bottom: 10px; }
        .inq-empty-msg span { font-size: 14px; color: #bbb; display: block; }
        .inq-empty-msg small { font-size: 12px; color: #ccc; margin-top: 4px; display: block; }

        /* Form fields */
        .form-row { margin-bottom: 20px; }
        .form-row:last-child { margin-bottom: 0; }
        .form-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 20px; }
        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 800;
            color: #444;
            margin-bottom: 7px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .form-label .required { color: #e53935; margin-left: 2px; }
        .input-wrap {
            position: relative;
        }
        .input-wrap .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0a8c8;
            font-size: 15px;
            pointer-events: none;
            transition: color 0.2s;
        }
        .input-wrap.textarea-wrap .input-icon {
            top: 14px;
            transform: none;
        }
        .input-wrap .input-icon.icon-right {
            left: auto;
            right: 14px;
        }
        .form-input {
            width: 100%;
            border: 1.5px solid #e4e8f0;
            border-radius: 12px;
            padding: 12px 14px 12px 42px;
            font-size: 14px;
            color: #1a1a2e;
            outline: none;
            background: #f8f9fd;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            font-family: inherit;
        }
        .form-input.no-icon {
            padding-left: 14px;
        }
        .form-input:focus {
            border-color: #2B11DB;
            box-shadow: 0 0 0 3px rgba(43,17,219,0.09);
            background: #fff;
        }
        .form-input:focus + .input-icon,
        .input-wrap:focus-within .input-icon { color: #2B11DB; }
        textarea.form-input { resize: vertical; min-height: 120px; padding-top: 12px; }

        /* Card-style radio */
        .radio-card-group { display: flex; gap: 12px; margin-top: 4px; flex-wrap: wrap; }
        .radio-card {
            flex: 1;
            min-width: 120px;
        }
        .radio-card input[type="radio"] { display: none; }
        .radio-card-label {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 2px solid #e4e8f0;
            border-radius: 12px;
            padding: 14px 18px;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
            background: #f8f9fd;
            font-size: 14px;
            font-weight: 600;
            color: #444;
        }
        .radio-card-label i { font-size: 20px; color: #b0b8d8; transition: color 0.2s; }
        .radio-card-label span { display: flex; flex-direction: column; gap: 2px; }
        .radio-card-label .rc-title { font-size: 14px; font-weight: 700; color: #1a1a2e; }
        .radio-card-label .rc-sub { font-size: 11px; color: #999; font-weight: 400; }
        .radio-card input[type="radio"]:checked + .radio-card-label {
            border-color: #2B11DB;
            background: #f0f2ff;
            box-shadow: 0 0 0 3px rgba(43,17,219,0.09);
        }
        .radio-card input[type="radio"]:checked + .radio-card-label i { color: #2B11DB; }
        .radio-card input[type="radio"]:checked + .radio-card-label .rc-title { color: #2B11DB; }

        /* Custom file upload */
        .file-drop-zone {
            border: 2px dashed #c8d0ee;
            border-radius: 14px;
            padding: 28px 20px;
            text-align: center;
            background: #f8f9fd;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
            position: relative;
        }
        .file-drop-zone:hover, .file-drop-zone.drag-over {
            border-color: #2B11DB;
            background: #f0f2ff;
        }
        .file-drop-zone input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }
        .file-drop-icon { font-size: 32px; color: #c8d0ee; margin-bottom: 8px; display: block; }
        .file-drop-text { font-size: 14px; font-weight: 700; color: #444; display: block; }
        .file-drop-sub { font-size: 12px; color: #999; margin-top: 2px; display: block; }
        .file-chosen-name { font-size: 12px; color: #2B11DB; font-weight: 700; margin-top: 8px; display: none; }

        /* Tips & hints */
        .inq-tip {
            font-size: 12px;
            color: #d97706;
            display: flex;
            align-items: flex-start;
            gap: 7px;
            margin-top: 10px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 9px 12px;
        }
        .inq-tip i { margin-top: 1px; flex-shrink: 0; }
        .inq-file-hint { font-size: 12px; color: #888; margin-top: 6px; }

        /* Form actions */
        .form-actions-wrap {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(43,17,219,0.07), 0 1px 4px rgba(0,0,0,0.04);
            border: 1px solid rgba(43,17,219,0.06);
            padding: 20px 28px;
            margin-bottom: 22px;
        }
        .form-security {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #888;
            padding-bottom: 16px;
            margin-bottom: 16px;
            border-bottom: 1px solid #f0f0f4;
        }
        .form-security i { color: #00D7B3; font-size: 15px; }
        .form-btns {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }
        .btn-clear {
            background: transparent;
            border: 2px solid #e4e8f0;
            color: #666;
            border-radius: 12px;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 7px;
            transition: border-color 0.2s, color 0.2s, background 0.2s;
            font-family: inherit;
        }
        .btn-clear:hover { border-color: #c0c8e0; color: #333; background: #f8f9fd; }
        .btn-submit {
            background: linear-gradient(135deg, #2B11DB 0%, #5a3dea 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px 32px;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
            font-family: inherit;
            box-shadow: 0 6px 20px rgba(43,17,219,0.3);
            letter-spacing: 0.3px;
        }
        .btn-submit:hover { opacity: 0.9; transform: translateY(-2px); box-shadow: 0 10px 28px rgba(43,17,219,0.38); }
        .btn-submit:active { transform: translateY(0); }

        @media (max-width: 700px) {
            .inq-banner { padding: 36px 20px 32px; margin-left: 0; }
            .inq-banner-steps { gap: 0; }
            .inq-step-label { font-size: 9px; }
            .inq-form-body { padding: 20px 16px 0; }
            .inq-card-body { padding: 18px 18px 22px; }
            .inq-section-header { padding: 14px 18px; }
            .form-cols { grid-template-columns: 1fr; }
            .form-btns { flex-direction: column; }
            .btn-submit, .btn-clear { justify-content: center; }
            .inq-table thead th:nth-child(2),
            .inq-table tbody td:nth-child(2) { display: none; }
            .radio-card-group { flex-direction: column; }
            .form-actions-wrap { padding: 16px 18px; }
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>
        <?php
        // Set page title
        $page_title = "Inquiry Form";
        $company_name = "ANDISON INDUSTRIAL";
        
        // Contact information
        $phone = "+1(234) 567 8900";
        $phone2 = "+1(234) 567 8900";
        $phone3 = "+1(639) 977 803 7398";
        $email = "info@andison-industrial.com";
    ?>

    <!-- Header -->
    <header>
        <div class="header-top">
            <div class="logo">
                <div class="logo-box"><a href="home.php"><img src="assets/HOME/image-removebg-preview.png" alt="Andison Industrial" /></a></div>
            </div>

            <div class="search-bar">
                <form class="search-field" action="search.php" method="get">
                    <i class="bi bi-search"></i>
                    <input type="text" name="q" placeholder="Search for products" value="<?php echo htmlspecialchars(isset($_GET['q']) ? $_GET['q'] : '', ENT_QUOTES); ?>" onkeydown="if(event.key==='Enter') this.form.submit();">
                </form>
            </div>

            <div class="right-actions">
                <a href="javascript:history.back()" class="back-btn"><i class="bi bi-arrow-left btn-icon"></i> <span class="btn-text">BACK</span></a>
                <a href="inquirylist.php" class="inquiry-btn"><i class="bi bi-card-checklist btn-icon"></i> <span class="btn-text">INQUIRY LIST</span> <span class="cart-badge hidden" id="cartBadge">0</span></a>
                <div class="header-contact">
                        <div class="contact-dropdown" tabindex="0" aria-haspopup="true">
                            <a href="#contact" class="contact-link" aria-label="Contact Us">Contact Us ▾</a>
                            <div class="contact-popover" role="menu" aria-hidden="true">
                                <button class="contact-close" aria-label="Close contact popover">✕</button>
                                <ul class="contact-list">
                                    <li><span class="icon"><i class="bi bi-telephone"></i></span><a href="tel:<?php echo $phone; ?>"><?php echo $phone; ?></a></li>
                                    <li><span class="icon"><i class="bi bi-telephone"></i></span><a href="tel:<?php echo $phone2; ?>"><?php echo $phone2; ?></a></li>
                                    <li><span class="icon"><i class="bi bi-telephone"></i></span><a href="tel:<?php echo $phone3; ?>"><?php echo $phone3; ?></a></li>
                                    <li><span class="icon"><i class="bi bi-envelope"></i></span><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav>
            <div class="nav-inner">
                <ul class="nav-list">
                    <li>
                        <a href="home.php">Home</a>
                        <div class="nav-dropdown">
                            <h4>Welcome</h4>
                            <p>Discover our complete range of industrial welding solutions and equipment.</p>
                        </div>
                    </li>
                    <li>
                        <a href="aboutus.php">About Us</a>
                        <div class="nav-dropdown">
                            <h4>Our Company</h4>
                            <ul>
                                <li><a href="aboutus.php#mission">Our Mission</a></li>
                                <li><a href="aboutus.php#history">Company History</a></li>
                                <li><a href="aboutus.php#team">Our Team</a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="brands.php">Brands</a>
                        <div class="nav-dropdown">
                            <h4>Featured Brands</h4>
                            <ul>
                               <li><a href="brand.php?name=Panasonic%20Connect"><img src="assets/brands/PANASONIC.jpg" alt="Panasonic Connect" title="Panasonic Connect"></a></li>
                                <li><a href="/brand.php?name=Robot%20Systems"><img src="/assets/brands/ROBOT SYSTEMS.png" alt="Robot Systems Peripherals" title="Robot Systems Peripherals"></a></li>
                                <li><a href="brand.php?name=Kobelco"><img src="assets/brands/KOBELCO.jpg" alt="Kobelco" title="Kobelco"></a></li>
                                <li><a href="brand.php?name=Metrode"><img src="assets/brands/METRODE.jpg" alt="Metrode" title="Metrode"></a></li>
                                <li><a href="brand.php?name=DryRod.%20II"><img src="assets/brands/DRYROD.jpg" alt="DryRod. II" title="DryRod. II"></a></li>
                                <li><a href="brand.php?name=Weldcraft"><img src="assets/brands/WELDCRAFT.png" alt="Weldcraft" title="Weldcraft"></a></li>
                                <li><a href="brand.php?name=Truweld"><img src="assets/brands/TRUWELD.jpg" alt="Truweld" title="Truweld"></a></li>
                                <li><a href="brand.php?name=Arcair"><img src="assets/brands/ARCAIR.jpg" alt="Arcair" title="Arcair"></a></li>
                                <li><a href="brand.php?name=MAGNAFLUX"><img src="assets/brands/MAGNAFLUX.jpg" alt="Magnaflux" title="Magnaflux"></a></li>
                                <li><a href="brand.php?name=Tempilstik"><img src="assets/brands/TEMPILSTIK.jpg" alt="Tempilstik" title="Tempilstik"></a></li>
                                <li><a href="brand.php?name=TANAKA"><img src="assets/brands/TANAKA.jpg" alt="Tanaka" title="Tanaka"></a></li>
                                <li><a href="brand.php?name=CHIYODA"><img src="assets/brands/CHIYODA.jpg" alt="Chiyoda" title="Chiyoda"></a></li>
                                <li><a href="brand.php?name=Yutaka"><img src="assets/brands/YUTAKA.jpg" alt="Yutaka" title="Yutaka"></a></li>
                                <li><a href="brand.php?name=HARDWORKER"><img src="assets/brands/HARDWORKER.jpg" alt="Hard Workers" title="Hard Workers"></a></li>
                                <li><a href="brand.php?name=Soyer"><img src="assets/brands/SOYER.jpg" alt="Soyer" title="Soyer"></a></li>
                                <li><a href="brand.php?name=Aquasol"><img src="assets/brands/AQUASOL.jpg" alt="Aquasol" title="Aquasol"></a></li>
                                <li><a href="brand.php?name=SK%20And%20GAL%20GAGE"><img src="assets/brands/SK%20AND%20GAL%20GAGE.jpg" alt="SK And GAL GAGE" title="SK And GAL GAGE"></a></li>
                                <li><a href="brand.php?name=COPPUS"><img src="assets/brands/COPPUS.jpg" alt="Coppus" title="Coppus"></a></li>
                                <li><a href="brand.php?name=BW%20Technologies"><img src="assets/brands/BW%20TECHNOLOGIES.jpg" alt="BW Technologies" title="BW Technologies"></a></li>
                                <li><a href="brand.php?name=RAC"><img src="assets/brands/RAE%20SYSTEMS.jpg" alt="RAE Systems" title="RAE Systems"></a></li>
                                <li><a href="brand.php?name=WELDAS"><img src="assets/brands/WELDAS.jpg" alt="Weldas" title="Weldas"></a></li>
                                <li><a href="brand.php?name=UVEX"><img src="assets/brands/UVEX.jpg" alt="Uvex" title="Uvex"></a></li>
                                <li><a href="brand.php?name=ACES"><img src="assets/brands/ACES.jpg" alt="Aces" title="Aces"></a></li>
                                <li><a href="brand.php?name=MICROGARD"><img src="assets/brands/MICROGARD.jpg" alt="Microgard" title="Microgard"></a></li>
                                <li><a href="brand.php?name=ANSELL"><img src="assets/brands/ANSELL.jpg" alt="Ansell" title="Ansell"></a></li>
                                <li><a href="brand.php?name=Alfra"><img src="assets/brands/ALFRA.jpg" alt="Alfra" title="Alfra"></a></li>
                                <li><a href="brand.php?name=BOSCH"><img src="assets/brands/BOSCH.jpg" alt="Bosch" title="Bosch"></a></li>
                                <li><a href="brand.php?name=Makita"><img src="assets/brands/MAKITA.jpg" alt="Makita" title="Makita"></a></li>
                                <li><a href="brand.php?name=Weller"><img src="assets/brands/WEILER.jpg" alt="Weller" title="Weller"></a></li>
                                <li><a href="brand.php?name=Garryson"><img src="assets/brands/GARRYSON.jpg" alt="Garryson" title="Garryson"></a></li>
                                <li><a href="brand.php?name=REVOLT"><img src="assets/brands/REVOLT.png" alt="REVOLT" title="REVOLT"></a></li>
                                <li><a href="brand.php?name=Technotex"><img src="assets/brands/TECHNOTEX.png" alt="Technotex" title="Technotex"></a></li>
                                <li><a href="brand.php?name=Spilfyter"><img src="assets/brands/SPILFYTER.jpg" alt="Spilfyter" title="Spilfyter"></a></li>
                                <li><a href="brand.php?name=Dalo"><img src="assets/brands/DALO.jpg" alt="Dalo" title="Dalo"></a></li>
                                <li><a href="brand.php?name=MOTOLITE"><img src="assets/brands/MOTOLITE.jpg" alt="Motolite" title="Motolite"></a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="industries.php">Industries</a>
                        <div class="nav-dropdown">
                            <h4>Industries We Serve</h4>
                            <ul>
                                <li><a href="industries.php#manufacturing">Manufacturing</a></li>
                                <li><a href="industries.php#construction">Construction</a></li>
                                <li><a href="industries.php#automotive">Automotive</a></li>
                                <li><a href="industries.php#shipbuilding">Shipbuilding</a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="services.php">Services</a>
                        <div class="nav-dropdown">
                            <h4>Our Services</h4>
                            <ul>
                                <li><a href="services.php#consultation">Technical Consultation</a></li>
                                <li><a href="services.php#training">Training Programs</a></li>
                                <li><a href="services.php#maintenance">Equipment Maintenance</a></li>
                                <li><a href="services.php#support">After-Sales Support</a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="contact.php">Contact Us</a>
                        <div class="nav-dropdown">
                            <h4>Get In Touch</h4>
                            <p>Reach out to our team for inquiries, quotes, or technical support.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Overlay Backdrop -->
    <div class="overlay-backdrop" id="overlayBackdrop"></div>

    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="sidebar-overlay" aria-hidden="true">
        <div style="padding: 14px 20px; background: linear-gradient(135deg, #00D7B3 0%, #00D7B3 100%); display: flex; align-items: center; justify-content: space-between; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 8px; color: white; flex: 1;">
                <i class="bi bi-list" style="font-size: 20px; font-weight: 700;"></i>
                <span style="font-size: 14px; font-weight: 700; letter-spacing: 0.5px;">BROWSE</span>
            </div>
            <button id="closeSidebar" style="background: transparent; border: none; color: white; cursor: pointer; font-size: 24px; padding: 0; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; line-height: 1;">×</button>
        </div>
        <ul class="sidebar-list">
            <li class="has-sub">
                <a href="arc-welding-machine/arc-welding-machine.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-charge"></i></span><span class="sidebar-label">Arc Welding Machines</span></a>
                <button class="sub-toggle" aria-controls="sub-arc-welding" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-arc-welding" class="sidebar-sublist collapsed">
                    <li><a href="arc-welding-machine/mig-welding-machine.php">MIG Welding Machine</a></li>
                    <li><a href="arc-welding-machine/accessories-and-consumables.php">Accessories and Consumables</a></li>
                    <li><a href="arc-welding-machine/co1-mag-welding-machine.php">CO2/MAG Welding Machine</a></li>
                    <li><a href="arc-welding-machine/stud-welding-machine.php">STUD Welding Machine</a></li>
                    <li><a href="arc-welding-machine/tig-welding-machine.php">TIG Welding Machine</a></li>
                    <li><a href="arc-welding-machine/plasma-cutting-machine.php">Plasma Cutting Machine</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="arc-welding-robots/arc-welding-robot.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-robot"></i></span><span class="sidebar-label">Arc Welding Robots</span></a>
                <button class="sub-toggle" aria-controls="sub-arc-robots" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-arc-robots" class="sidebar-sublist collapsed">
                    <li><a href="arc-welding-robots/g3-controller-series.php">G3 Controller Series</a></li>
                    <li><a href="arc-welding-robots/g4-controller-series.php">G4 Controller Series</a></li>
                    <li><a href="arc-welding-robots/featured-products-and-solution.php">Featured Products and Solutions</a></li>
                    <li><a href="arc-welding-robots/robot-system-peripherals.php">Robot System Peripherals</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="batteries/batteries.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-fill"></i></span><span class="sidebar-label">Batteries</span></a>
                <button class="sub-toggle" aria-controls="sub-batteries" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-batteries" class="sidebar-sublist collapsed">
                    <li><a href="batteries/maintenance-free.php">Maintenance Free</a></li>
                    <li><a href="batteries/low-maintenance.php">Low Maintenance</a></li>
                    <li><a href="batteries/special-batteries.php">Special Batteries</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="drilling-and-lifting/drilling-and-lifting.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-hammer"></i></span><span class="sidebar-label">Drilling and Lifting</span></a>
                <button class="sub-toggle" aria-controls="sub-drilling-lifting" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-drilling-lifting" class="sidebar-sublist collapsed">
                    <li><a href="drilling-and-lifting/lifting.php">Lifting</a></li>
                    <li class="has-nested-sub">
                        <a href="drilling-and-lifting/magnetic-drill.php">Magnetic Drill</a>
                        <button class="nested-toggle" aria-controls="nested-magnetic-drill" aria-expanded="false"><i class="bi bi-chevron-right"></i></button>
                        <ul id="nested-magnetic-drill" class="sidebar-nested-sublist collapsed">
                            <li><a href="drilling-and-lifting/magnetic-drill/b-line-series.php">B-Line Series</a></li>
                            <li><a href="drilling-and-lifting/magnetic-drill/rl-e-line-series.php">RL-E Line Series</a></li>
                            <li><a href="drilling-and-lifting/magnetic-drill/rbx-line-series.php">RBX-Line Series</a></li>
                            <li><a href="drilling-and-lifting/magnetic-drill/sp-line-series.php">SP-Line Series</a></li>
                            <li><a href="drilling-and-lifting/magnetic-drill/v-line-series.php">V-Line Series</a></li>
                        </ul>
                    </li>
                    <li><a href="drilling-and-lifting/cutters.php">Cutters</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="gas-detectors/gas-detectors.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-bullseye"></i></span><span class="sidebar-label">Gas Detectors</span></a>
                <button class="sub-toggle" aria-controls="sub-gas-detectors" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-gas-detectors" class="sidebar-sublist collapsed">
                    <li><a href="gas-detectors/single-gas-detector.php">Single Gas Detector</a></li>
                    <li><a href="gas-detectors/multi-gas-detector.php">Multi Gas Detector</a></li>
                    <li><a href="gas-detectors/portable-gas-detectors.php">Portable Gas Detectors</a></li>
                    <li><a href="gas-detectors/docking-data-management.php">Docking and Data Management</a></li>
                    <li><a href="gas-detectors/calibration-gas-regulators.php">Calibration Gas and Regulators</a></li>
                </ul>
            </li>
            <li class="">
                <a href="portable-ventilators/portable-ventilators.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-fan"></i></span><span class="sidebar-label">Portable Ventilators</span></a>
            </li>
            <li class="has-sub">
                <a href="power-tools/power-tools.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-tools"></i></span><span class="sidebar-label">Power Tools</span></a>
                <button class="sub-toggle" aria-controls="sub-power-tool" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-power-tool" class="sidebar-sublist collapsed">
                    <li><a href="power-tools/grinder.php">Grinder</a></li>
                    <li><a href="power-tools/saw.php">Saw</a></li>
                    <li><a href="power-tools/drill-and-wrench.php">Drill and Wrench</a></li>
                    <li><a href="power-tools/rotary-and-demolition-hammer.php">Rotary and Demolition Hammer</a></li>
                    <li><a href="power-tools/accessories.php">Accessories</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="protection/protection.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-shield-check"></i></span><span class="sidebar-label">Personal Protective Equipment</span></a>
                <button class="sub-toggle" aria-controls="sub-protection-safety" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-protection-safety" class="sidebar-sublist collapsed">
                    <li><a href="protection/eye-protection.php">Eye Protection</a></li>
                    <li class="has-nested-sub">
                        <a href="protection/hand-protection.php">Hand Protection</a>
                        <button class="nested-toggle" aria-controls="nested-hand-protection" aria-expanded="false"><i class="bi bi-chevron-right"></i></button>
                        <ul id="nested-hand-protection" class="sidebar-nested-sublist collapsed">
                            <li><a href="protection/working-gloves.php">Working Gloves</a></li>
                            <li><a href="protection/chemical-liquid-protection-gloves.php">Chemical and Liquid Protection Gloves</a></li>
                            <li><a href="protection/disposable-gloves.php">Disposable Gloves</a></li>
                            <li><a href="protection/welding-gloves.php">Welding Gloves</a></li>
                        </ul>
                    </li>
                    <li><a href="protection/hearing-respiratory-protection.php">Hearing &amp; Respiratory Protection</a></li>
                    <li><a href="protection/welding-head-and-face-protection.php">Welding Head and Face Protection</a></li>
                    <li class="has-nested-sub">
                        <a href="protection/body-protection.php">Body Protection</a>
                        <button class="nested-toggle" aria-controls="nested-body-protection" aria-expanded="false"><i class="bi bi-chevron-right"></i></button>
                        <ul id="nested-body-protection" class="sidebar-nested-sublist collapsed">
                            <li><a href="protection/chemical-flame-retardant.php">Chemical and Flame Retardant</a></li>
                            <li><a href="protection/liquid-spray-splash.php">Liquid Spray and Splash</a></li>
                            <li><a href="protection/particulate-low-hazard.php">Particulate and Low Hazard</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="welding-accessories/welding-accessories.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-gear"></i></span><span class="sidebar-label">Welding Accessories</span></a>
                <button class="sub-toggle" aria-controls="sub-welding-accessories" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-welding-accessories" class="sidebar-sublist collapsed">
                    <li><a href="welding-accessories/welding-electrode-oven.php">Welding Electrode Oven</a></li>
                    <li><a href="welding-accessories/non-destructive-crack-detection.php">Non-Destructive Crack Detection</a></li>
                    <li><a href="welding-accessories/gas-saving-regulator.php">Gas Saving Regulator</a></li>
                    <li><a href="welding-accessories/gas-cutting-equipment.php">Gas Cutting Equipment</a></li>
                    <li><a href="welding-accessories/industrial-markers.php">Industrial Markers</a></li>
                    <li><a href="welding-accessories/measuring-gauge.php">Measuring Gauge</a></li>
                    <li><a href="welding-accessories/others.php">Others</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="welding-consumables/welding-consumables.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-box"></i></span><span class="sidebar-label">Welding Consumables</span></a>
                <button class="sub-toggle" aria-controls="sub-welding-consumables" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-welding-consumables" class="sidebar-sublist collapsed">
                    <li><a href="welding-consumables/kobelco.php">Kobelco</a></li>
                    <li><a href="welding-consumables/metrode.php">Metrode</a></li>
                </ul>
            </li>
        </ul>
    </aside>

    <!-- Mini Sidebar (Icon Bar) -->
    <div class="mini-sidebar active" id="miniSidebar">
        <div id="miniSidebarMenuBar" style="background: linear-gradient(135deg, #00D7B3 0%, #00D7B3 100%); border-radius: 0; display: flex; align-items: center; gap: 8px; cursor: pointer;">
            <i class="bi bi-list" style="font-size: 18px; font-weight: 700; color: white;"></i>
            <span style="font-size: 13px; font-weight: 700; color: white; letter-spacing: 0.5px; display: none;" class="browse-label">BROWSE CATEGORIES</span>
        </div>
        <div class="mini-sidebar-icon has-sub" data-target="arc-welding-machine/arc-welding-machine.php" title="Arc Welding Machines"><i class="bi bi-lightning-charge"></i><span class="label">Arc Welding Machines</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="arc-welding-robots/arc-welding-robot.php" title="Arc Welding Robots"><i class="bi bi-robot"></i><span class="label">Arc Welding Robots</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="batteries/batteries.php" title="Batteries"><i class="bi bi-lightning-fill"></i><span class="label">Batteries</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="drilling-and-lifting/drilling-and-lifting.php" title="Drilling and Lifting"><i class="bi bi-hammer"></i><span class="label">Drilling and Lifting</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="gas-detectors/gas-detectors.php" title="Gas Detectors"><i class="bi bi-bullseye"></i><span class="label">Gas Detectors</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="portable-ventilators/portable-ventilators.php" title="Portable Ventilators"><i class="bi bi-fan"></i><span class="label">Portable Ventilators</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="power-tools/power-tools.php" title="Power Tools"><i class="bi bi-tools"></i><span class="label">Power Tools</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="protection/protection.php" title="Personal Protective Equipment"><i class="bi bi-shield-check"></i><span class="label">PPE</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="welding-accessories/welding-accessories.php" title="Welding Accessories"><i class="bi bi-gear"></i><span class="label">Welding Accessories</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="welding-consumables/welding-consumables.php" title="Welding Consumables"><i class="bi bi-box"></i><span class="label">Welding Consumables</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <button class="mini-sidebar-toggle" id="expandSidebar" title="Toggle Sidebar"><i class="bi bi-chevron-right"></i></button>
    </div>

    <!-- Mobile FAB to show/hide mini sidebar -->
    <button class="mobile-sidebar-fab" id="mobileSidebarFab"><i class="bi bi-chevron-right" id="mobileFabIcon"></i></button>

    <!-- Floating popover for mini sidebar subcategories -->
    <div id="miniPopover" class="mini-popover" aria-hidden="true">
        <div class="mini-popover-header">
            <div class="mini-popover-title">Arc Welding Robots</div>
        </div>
        <div class="mini-popover-body">
            <ul class="mini-popover-list"></ul>
        </div>
    </div>

    <!-- Inquiry Form Content -->
    <div class="page-content">
    <div class="inquiry-wrapper">

        <!-- Banner / Page Header -->
        <div class="inq-banner">
            <div class="inq-banner-icon"><i class="bi bi-clipboard2-check"></i></div>
            <h1 class="inquiry-page-title">Inquiry Form</h1>
            <p class="inquiry-page-subtitle">Share your requirements and we'll respond within 24 hours</p>
            <div class="inq-banner-steps">
                <div class="inq-step active" id="step-1" data-target="card-items"><div class="inq-step-num">1</div><div class="inq-step-label">Items</div></div>
                <div class="inq-step" id="step-2" data-target="card-contact"><div class="inq-step-num">2</div><div class="inq-step-label">Contact</div></div>
                <div class="inq-step" id="step-3" data-target="card-prefs"><div class="inq-step-num">3</div><div class="inq-step-label">Preferences</div></div>
                <div class="inq-step" id="step-4" data-target="card-submit"><div class="inq-step-num">4</div><div class="inq-step-label">Submit</div></div>
            </div>
        </div>

        <div class="inq-form-body">
        <form id="inquiryForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="items_json" id="itemsJsonInput" value="[]">

            <!-- Inquiry Items -->
            <div class="inq-card" id="card-items">
                <div class="inq-section-header">
                    <div class="inq-section-icon"><i class="bi bi-cart3"></i></div>
                    <div>
                        <div class="inq-section-title">Inquiry Items</div>
                        <div class="inq-section-desc">Products added to your inquiry list</div>
                    </div>
                </div>
                <div id="inquiryItemsContainer">
                    <table class="inq-table" id="inquiryTable">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Remove</th>
                            </tr>
                        </thead>
                        <tbody id="inquiryTableBody">
                            <!-- Items populated by JS -->
                        </tbody>
                    </table>
                    <div class="inq-empty-msg" id="emptyMsg" style="display:none;">
                        <i class="bi bi-cart-x"></i>
                        <span>No items in your inquiry list</span>
                        <small>Browse our products and click "Add to Inquiry" to get started</small>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="inq-card" id="card-contact">
                <div class="inq-section-header">
                    <div class="inq-section-icon teal"><i class="bi bi-person-vcard"></i></div>
                    <div>
                        <div class="inq-section-title">Contact Information</div>
                        <div class="inq-section-desc">We'll use these details to reach you</div>
                    </div>
                </div>
                <div class="inq-card-body">

                <div class="form-cols">
                    <div>
                        <label class="form-label" for="fullname">Full Name <span class="required">*</span></label>
                        <div class="input-wrap">
                            <input class="form-input" type="text" id="fullname" name="fullname" placeholder="Juan dela Cruz" required>
                            <i class="bi bi-person input-icon"></i>
                        </div>
                    </div>
                    <div>
                        <label class="form-label" for="company">Company / Organization <span class="required">*</span></label>
                        <div class="input-wrap">
                            <input class="form-input" type="text" id="company" name="company" placeholder="Your Company Name" required>
                            <i class="bi bi-building input-icon"></i>
                        </div>
                    </div>
                </div>

                <div class="form-cols">
                    <div>
                        <label class="form-label" for="email">Email Address <span class="required">*</span></label>
                        <div class="input-wrap">
                            <input class="form-input" type="email" id="email" name="email" placeholder="juan@company.com" required>
                            <i class="bi bi-envelope input-icon"></i>
                        </div>
                    </div>
                    <div>
                        <label class="form-label" for="phone">Phone Number <span class="required">*</span></label>
                        <div class="input-wrap">
                            <input class="form-input" type="tel" id="phone" name="phone" placeholder="+63 912 345 6789" required>
                            <i class="bi bi-telephone input-icon"></i>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <label class="form-label" for="address">Office Address <span class="required">*</span></label>
                    <div class="input-wrap textarea-wrap">
                        <textarea class="form-input" id="address" name="address" placeholder="Street, Barangay, City, Province, ZIP Code" required></textarea>
                        <i class="bi bi-geo-alt input-icon"></i>
                    </div>
                </div>

                </div>
            </div>

            <!-- Communication Preferences -->
            <div class="inq-card" id="card-prefs">
                <div class="inq-section-header">
                    <div class="inq-section-icon warm"><i class="bi bi-chat-dots"></i></div>
                    <div>
                        <div class="inq-section-title">Communication Preferences</div>
                        <div class="inq-section-desc">How would you like us to contact you?</div>
                    </div>
                </div>
                <div class="inq-card-body">
                <label class="form-label">Preferred Contact Method <span class="required">*</span></label>
                <div class="radio-card-group">
                    <label class="radio-card">
                        <input type="radio" name="contact_method" value="email">
                        <span class="radio-card-label">
                            <i class="bi bi-envelope-fill"></i>
                            <span>
                                <span class="rc-title">Email</span>
                                <span class="rc-sub">Get a written response</span>
                            </span>
                        </span>
                    </label>
                    <label class="radio-card">
                        <input type="radio" name="contact_method" value="phone">
                        <span class="radio-card-label">
                            <i class="bi bi-telephone-fill"></i>
                            <span>
                                <span class="rc-title">Phone</span>
                                <span class="rc-sub">Talk to our team directly</span>
                            </span>
                        </span>
                    </label>
                    <label class="radio-card">
                        <input type="radio" name="contact_method" value="viber">
                        <span class="radio-card-label">
                            <i class="bi bi-phone-vibrate"></i>
                            <span>
                                <span class="rc-title">Viber</span>
                                <span class="rc-sub">Quick chat messaging</span>
                            </span>
                        </span>
                    </label>
                </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="inq-card" id="card-submit">
                <div class="inq-section-header">
                    <div class="inq-section-icon"><i class="bi bi-clipboard-text"></i></div>
                    <div>
                        <div class="inq-section-title">Additional Information</div>
                        <div class="inq-section-desc">Any extra details to help us serve you better</div>
                    </div>
                </div>
                <div class="inq-card-body">

                <div class="form-row">
                    <label class="form-label" for="message">Message / Special Requirements</label>
                    <div class="input-wrap textarea-wrap">
                        <textarea class="form-input" id="message" name="message" placeholder="Tell us about your project, timeline, specifications, or any special requirements..."></textarea>
                        <i class="bi bi-chat-left-text input-icon"></i>
                    </div>
                    <div class="inq-tip"><i class="bi bi-lightbulb-fill"></i> Tip: Include product model numbers, quantities needed, and your preferred delivery date to speed up our response.</div>
                </div>

                <div class="form-row">
                    <label class="form-label">Attachments <span style="color:#999;font-weight:400;text-transform:none;letter-spacing:0;">(Optional — Drawings, Specs, Photos)</span></label>
                    <div class="file-drop-zone" id="fileDropZone">
                        <input type="file" id="file" name="file" accept=".jpg,.jpeg,.png,.pdf" onchange="handleFileChange(this)">
                        <i class="bi bi-cloud-arrow-up file-drop-icon"></i>
                        <span class="file-drop-text">Click to upload or drag &amp; drop</span>
                        <span class="file-drop-sub">JPG, PNG, PDF — max 10MB</span>
                        <span class="file-chosen-name" id="fileChosenName"></span>
                    </div>
                </div>

                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions-wrap">
                <div class="form-security">
                    <i class="bi bi-shield-check"></i>
                    Your information is encrypted and used solely to process your inquiry. We respect your privacy.
                </div>
                <div class="form-btns">
                    <button type="button" class="btn-clear" id="clearFormBtn">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset Form
                    </button>
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-send-fill"></i> Submit Inquiry
                    </button>
                </div>
            </div>

        </form>
        </div><!-- /.inq-form-body -->
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-links">
                <a href="#privacy">Privacy Policy</a>
                <a href="#terms">Terms of Service</a>
                <a href="#sitemap">Sitemap</a>
            </div>
            <div class="footer-copyright">
                <p>&copy; 2026 <?php echo $company_name; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    </div><!-- /.page-content -->
    <script>
        (function(){
            var browseToggle = document.getElementById('browseToggle');
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('overlay');
            var closeBtn = document.getElementById('closeSidebar');

            function openSidebar(){
                sidebar.classList.add('active');
                overlay.classList.add('active');
                sidebar.setAttribute('aria-hidden','false');
                overlay.setAttribute('aria-hidden','false');
            }

            function closeSidebar(){
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                sidebar.setAttribute('aria-hidden','true');
                overlay.setAttribute('aria-hidden','true');
            }

            if(browseToggle){
                browseToggle.addEventListener('click', function(e){ e.preventDefault(); openSidebar(); });
            }
            if(closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if(overlay) overlay.addEventListener('click', closeSidebar);
            document.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeSidebar(); });
        })();
        // Sidebar sublist toggle behavior with persistent state
        (function(){
            var toggles = document.querySelectorAll('.sub-toggle');
            toggles.forEach(function(btn){
                var targetId = btn.getAttribute('aria-controls');
                var list = document.getElementById(targetId);
                if(!list) return;
                var storageKey = 'sidebar_sub_' + targetId;
                try {
                    var stored = localStorage.getItem(storageKey);
                    if(stored === 'true'){
                        btn.setAttribute('aria-expanded','true');
                        list.classList.remove('collapsed');
                    } else {
                        btn.setAttribute('aria-expanded','false');
                        list.classList.add('collapsed');
                    }
                } catch(e){
                    btn.setAttribute('aria-expanded','false');
                    list.classList.add('collapsed');
                }

                btn.addEventListener('click', function(e){
                    e.preventDefault();
                    var expanded = btn.getAttribute('aria-expanded') === 'true';
                    if(expanded){
                        btn.setAttribute('aria-expanded','false');
                        list.classList.add('collapsed');
                        try { localStorage.setItem(storageKey,'false'); } catch(e){}
                    } else {
                        btn.setAttribute('aria-expanded','true');
                        list.classList.remove('collapsed');
                        try { localStorage.setItem(storageKey,'true'); } catch(e){}
                    }
                });
            });
        })();
        // Nested sublist toggle behavior
        (function(){
            var nestedToggles = document.querySelectorAll('.nested-toggle');
            nestedToggles.forEach(function(btn){
                var targetId = btn.getAttribute('aria-controls');
                var list = document.getElementById(targetId);
                if(!list) return;
                var storageKey = 'sidebar_nested_' + targetId;
                try {
                    var stored = localStorage.getItem(storageKey);
                    if(stored === 'true'){
                        btn.setAttribute('aria-expanded','true');
                        list.classList.remove('collapsed');
                    } else {
                        btn.setAttribute('aria-expanded','false');
                        list.classList.add('collapsed');
                    }
                } catch(e){
                    btn.setAttribute('aria-expanded','false');
                    list.classList.add('collapsed');
                }

                btn.addEventListener('click', function(e){
                    e.preventDefault();
                    var expanded = btn.getAttribute('aria-expanded') === 'true';
                    if(expanded){
                        btn.setAttribute('aria-expanded','false');
                        list.classList.add('collapsed');
                        try { localStorage.setItem(storageKey,'false'); } catch(e){}
                    } else {
                        btn.setAttribute('aria-expanded','true');
                        list.classList.remove('collapsed');
                        try { localStorage.setItem(storageKey,'true'); } catch(e){}
                    }
                });
            });
        })();
    </script>
    <script>
        // Manage aria states for contact dropdown (improves accessibility)
        (function(){
            var dropdowns = document.querySelectorAll('.contact-dropdown');
            dropdowns.forEach(function(dd){
                var pop = dd.querySelector('.contact-popover');
                var link = dd.querySelector('.contact-link');
                dd.addEventListener('keydown', function(e){
                    if(e.key === 'Escape') { link.blur(); pop.setAttribute('aria-hidden','true'); }
                });
                dd.addEventListener('focusin', function(){ pop.setAttribute('aria-hidden','false'); dd.setAttribute('aria-expanded','true'); });
                dd.addEventListener('focusout', function(){ setTimeout(function(){ if(!dd.contains(document.activeElement)){ pop.setAttribute('aria-hidden','true'); dd.setAttribute('aria-expanded','false'); } }, 10); });
                dd.addEventListener('mouseenter', function(){ 
                    if(dd.classList.contains('closed')) return;
                    pop.setAttribute('aria-hidden','false'); dd.setAttribute('aria-expanded','true'); 
                });
                dd.addEventListener('mouseleave', function(){ pop.setAttribute('aria-hidden','true'); dd.setAttribute('aria-expanded','false'); dd.classList.remove('closed'); });

                // Mobile: click to toggle
                dd.addEventListener('click', function(e){
                    if(window.innerWidth > 768) return;
                    e.stopPropagation();
                    var isOpen = dd.classList.contains('open');
                    document.querySelectorAll('.contact-dropdown').forEach(function(d){ d.classList.remove('open'); });
                    if(!isOpen) dd.classList.add('open');
                });

                // Close button
                var closeBtn = dd.querySelector('.contact-close');
                if(closeBtn){
                    closeBtn.addEventListener('click', function(e){
                        e.stopPropagation();
                        e.preventDefault();
                        pop.setAttribute('aria-hidden','true');
                        dd.setAttribute('aria-expanded','false');
                        dd.classList.add('closed');
                        dd.classList.remove('open');
                        document.activeElement.blur();
                    });
                }
            });

            // Mobile: click outside closes all
            document.addEventListener('click', function(){
                if(window.innerWidth > 768) return;
                document.querySelectorAll('.contact-dropdown').forEach(function(d){ d.classList.remove('open'); });
            });
        })();
    </script>
    <script>
        // Hero slider functionality
        (function(){
            var slider = document.getElementById('heroSlider');
            var slides = slider.querySelectorAll('.hero-slide');
            var dots = slider.querySelectorAll('.hero-dot');
            var currentSlide = 0;
            var autoplayInterval;

            function showSlide(n) {
                slides.forEach(function(slide) { 
                    slide.classList.remove('active', 'prev', 'next'); 
                });
                dots.forEach(function(dot) { dot.classList.remove('active'); });
                
                var prevIndex = (n - 1 + slides.length) % slides.length;
                var nextIndex = (n + 1) % slides.length;
                
                slides[prevIndex].classList.add('prev');
                slides[n].classList.add('active');
                slides[nextIndex].classList.add('next');
                
                dots[n].classList.add('active');
                currentSlide = n;
            }

            function nextSlide() {
                showSlide((currentSlide + 1) % slides.length);
            }

            function goToSlide(n) {
                showSlide(n);
                clearInterval(autoplayInterval);
                autoplayInterval = setInterval(nextSlide, 5000);
            }

            // Dot click handlers
            dots.forEach(function(dot, index) {
                dot.addEventListener('click', function() {
                    goToSlide(index);
                });
            });

            // Initialize first slide
            showSlide(0);
            
            // Auto-play
            autoplayInterval = setInterval(nextSlide, 5000);
        })();
    </script>

    <script>
        // ============================================
        // SCROLL ANIMATIONS - Trigger animations when elements come into view
        // ============================================
        (function(){
            var observerOptions = {
                threshold: 0.15,
                rootMargin: '0px 0px -100px 0px'
            };

            var observer = new IntersectionObserver(function(entries){
                entries.forEach(function(entry){
                    if(entry.isIntersecting){
                        entry.target.classList.add('visible');
                        // Optional: stop observing once animated
                        // observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Observe all elements with scroll-animate class
            var animatedElements = document.querySelectorAll('.scroll-animate, .product-card, section h2, .section-description, .featured-section');
            animatedElements.forEach(function(el){
                observer.observe(el);
            });

            // Stagger animations for product cards on page load
            setTimeout(function(){
                var cards = document.querySelectorAll('.product-card');
                cards.forEach(function(card, index){
                    setTimeout(function(){
                        card.style.opacity = '1';
                    }, index * 150);
                });
            }, 300);
        })();
    </script>

    <script>
        // ============================================
        // BRAND DROPDOWN NAVIGATION (priority handler)
        // ============================================
        (function(){
            // Handle brand dropdown clicks with immediate navigation
            document.addEventListener('click', function(e){
                // Check if click is within brands dropdown
                var brandLink = e.target.closest('.nav-list li:nth-child(3) .nav-dropdown a');
                if(brandLink){
                    e.preventDefault();
                    e.stopPropagation();
                    var href = brandLink.getAttribute('href');
                    if(href){
                        window.location.href = href;
                    }
                    return;
                }
            }, true); // Use capture phase for priority
        })();
    </script>

    <script>
        // ============================================
        // PAGE TRANSITION EFFECTS
        // ============================================
        (function(){
            // Smooth page transitions on link clicks
            document.addEventListener('click', function(e){
                var link = e.target.closest('a[href*=".php"], a[href^="#"]');
                if(!link) return;
                
                var href = link.getAttribute('href');
                
                // Skip if it's an anchor link or javascript link
                if(href.startsWith('#') || href.startsWith('javascript:')) return;
                
                // Check if it's an internal PHP file
                if(!href.includes('.php')) return;
                
                // Prevent default and add exit animation
                e.preventDefault();
                
                var body = document.body;
                body.style.animation = 'none';

                setTimeout(function(){
                    window.location.href = href;
                }, 0);
            });

            // Add page entry animation on load
            window.addEventListener('load', function(){
                document.body.style.animation = 'none';
            });
        })();
    </script>

    <script>
        // ============================================
        // TEXT ANIMATIONS - Enhanced text reveal effects
        // ============================================
        (function(){
            // Add text animation to headings and descriptions
            var headings = document.querySelectorAll('h2, h3');
            headings.forEach(function(heading, index){
                heading.style.animationDelay = (index * 0.1) + 's';
            });

            // Animate footer links on hover
            var footerLinks = document.querySelectorAll('.footer-links a');
            footerLinks.forEach(function(link, index){
                link.style.animationDelay = (index * 0.1) + 's';
            });

            // Stagger contact list items
            var contactItems = document.querySelectorAll('.contact-list li');
            contactItems.forEach(function(item, index){
                item.style.opacity = '0';
                item.style.animation = 'fadeInUp 0.5s ease ' + (index * 0.1) + 's forwards';
            });
        })();
    </script>

    <script>
        // ============================================
        // HOVER EFFECTS - Enhanced interactive feedback
        // ============================================
        (function(){
            // Add hover effects to product cards
            var cards = document.querySelectorAll('.product-card');
            cards.forEach(function(card){
                card.addEventListener('mouseenter', function(){
                    this.style.boxShadow = '0 20px 40px rgba(0, 212, 170, 0.2)';
                });
                card.addEventListener('mouseleave', function(){
                    this.style.boxShadow = '';
                });
            });

            // Enhance button interactions
            var buttons = document.querySelectorAll('button, .cta-button, .featured-btn');
            buttons.forEach(function(btn){
                btn.addEventListener('mousedown', function(){
                    this.style.transform = 'scale(0.98)';
                });
                btn.addEventListener('mouseup', function(){
                    this.style.transform = '';
                });
                btn.addEventListener('mouseleave', function(){
                    this.style.transform = '';
                });
            });

            // Enhance navigation link hover effects
            var navLinks = document.querySelectorAll('.nav-list a');
            navLinks.forEach(function(link){
                link.addEventListener('mouseenter', function(){
                    this.style.color = '#ffffff';
                });
                link.addEventListener('mouseleave', function(){
                    if(!this.classList.contains('active')){
                        this.style.color = '';
                    }
                });
            });
        })();
    </script>

    <script>
        // ============================================
        // PARALLAX & SCROLL EFFECTS
        // ============================================
        (function(){
            var heroSlider = document.getElementById('heroSlider');
            if(!heroSlider) return;

            window.addEventListener('scroll', function(){
                var scrolled = window.pageYOffset;
                if(scrolled < 500){
                    heroSlider.style.transform = 'translateY(' + (scrolled * 0.5) + 'px)';
                    heroSlider.style.opacity = 1 - (scrolled / 800);
                }
            }, false);
        })();
    </script>

    <script>
        // Sidebar overlay functionality (for backdrop close)
        (function(){
            var overlayBackdrop = document.querySelector('.overlay-backdrop');
            var sidebar = document.getElementById('sidebar');
            
            if(overlayBackdrop) {
                overlayBackdrop.addEventListener('click', function(){
                    if(sidebar) sidebar.classList.remove('active');
                    overlayBackdrop.classList.remove('active');
                });
            }
            
            // Sidebar sub-toggle functionality - Show popover card
            var mainSidebarPopover = document.getElementById('miniPopover');
            var popoverTitle = mainSidebarPopover ? mainSidebarPopover.querySelector('.mini-popover-title') : null;
            var popoverList = mainSidebarPopover ? mainSidebarPopover.querySelector('.mini-popover-list') : null;
            var currentMainSidebarKey = null;
            
            function showMainSidebarPopover(toggle) {
                if (!mainSidebarPopover || !popoverList || !popoverTitle) return;
                
                var sublistId = toggle.getAttribute('aria-controls');
                var sublist = document.getElementById(sublistId);
                if (!sublist) return;
                
                // Extract title from parent li's main link
                var parentLi = toggle.closest('.has-sub');
                var mainLink = parentLi ? parentLi.querySelector(':scope > a:not([class])') : null;
                var title = mainLink ? mainLink.textContent.trim() : 'Items';
                
                // Extract items from the sublist
                var items = [];
                var listItems = sublist.querySelectorAll('li > a');
                listItems.forEach(function(link) {
                    items.push({
                        text: link.textContent.trim(),
                        href: link.getAttribute('href') || '#'
                    });
                });
                
                // Populate popover
                popoverTitle.textContent = title;
                popoverList.innerHTML = '';
                items.forEach(function(item) {
                    var li = document.createElement('li');
                    li.className = 'mini-popover-item';
                    li.innerHTML = '<span class="square"></span><a href="' + item.href + '">' + item.text + '</a>';
                    popoverList.appendChild(li);
                });
                
                // Position popover next to toggle button
                setTimeout(function() {
                    mainSidebarPopover.style.left = '-9999px';
                    mainSidebarPopover.style.top = '-9999px';
                    mainSidebarPopover.classList.add('show');
                    
                    var toggleRect = toggle.getBoundingClientRect();
                    var pw = mainSidebarPopover.offsetWidth;
                    var ph = mainSidebarPopover.offsetHeight;
                    var toggleCenterY = toggleRect.top + toggleRect.height / 2;
                    
                    // Position to the right of the toggle button
                    var left = Math.round(toggleRect.right + 14);
                    var top = Math.round(toggleCenterY - ph / 2);
                    
                    // Adjust if off-screen horizontally
                    if (left + pw + 12 > window.innerWidth) {
                        left = Math.round(toggleRect.left - pw - 14);
                    }
                    
                    // Adjust if off-screen vertically
                    var headerHeight = 100;
                    var minTop = headerHeight + 12;
                    var maxTop = window.innerHeight - ph - 12;
                    if (top < minTop) top = minTop;
                    if (top > maxTop) top = maxTop;
                    
                    var arrowOffset = toggleCenterY - top - 26;
                    mainSidebarPopover.style.setProperty('--arrow-offset', arrowOffset + 'px');
                    
                    mainSidebarPopover.style.left = left + 'px';
                    mainSidebarPopover.style.top = top + 'px';
                    mainSidebarPopover.setAttribute('aria-hidden', 'false');
                    currentMainSidebarKey = sublistId;
                }, 5);
            }
            
            function hideMainSidebarPopover() {
                if (!mainSidebarPopover) return;
                mainSidebarPopover.classList.remove('show');
                mainSidebarPopover.setAttribute('aria-hidden', 'true');
                currentMainSidebarKey = null;
            }
            
            var subToggles = document.querySelectorAll('.sub-toggle');
            subToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (currentMainSidebarKey === toggle.getAttribute('aria-controls') && mainSidebarPopover.classList.contains('show')) {
                        hideMainSidebarPopover();
                    } else {
                        showMainSidebarPopover(toggle);
                    }
                });
            });
            
            // Close popover on outside click
            document.addEventListener('click', function(e) {
                if (!mainSidebarPopover) return;
                if (!mainSidebarPopover.classList.contains('show')) return;
                if (e.target.closest('.mini-popover') || e.target.closest('.sub-toggle')) return;
                hideMainSidebarPopover();
            });
            
            // Close popover on Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') hideMainSidebarPopover();
            });
            
            // Nested toggle functionality
            var nestedToggles = document.querySelectorAll('.nested-toggle');
            nestedToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var nested = document.getElementById(toggle.getAttribute('aria-controls'));
                    if(nested) {
                        nested.classList.toggle('collapsed');
                        toggle.setAttribute('aria-expanded', nested.classList.contains('collapsed') ? 'false' : 'true');
                    }
                });
            });
        })();
    </script>

    <script>
        // ============================================
        // ACTIVE SIDEBAR CATEGORY HIGHLIGHTING
        // ============================================
        setTimeout(function(){
            var currentPath = window.location.pathname.toLowerCase();
            var sidebar = document.getElementById('sidebar');
            if(sidebar) {
                var pathParts = currentPath.split('/').filter(function(p) { return p && p !== 'andison-1'; });
                var currentCategory = null;
                
                var categoryList = [
                    'arc-welding-machine',
                    'arc-welding-robots',
                    'batteries',
                    'drilling-and-lifting',
                    'gas-detectors',
                    'portable-ventilators',
                    'power-tools',
                    'protection',
                    'welding-accessories',
                    'welding-consumables'
                ];
                
                for(var i = 0; i < pathParts.length; i++) {
                    if(categoryList.indexOf(pathParts[i]) !== -1) {
                        currentCategory = pathParts[i];
                        break;
                    }
                }

                if(currentCategory){
                    var links = sidebar.querySelectorAll('.sidebar-list > li > a');
                    links.forEach(function(link){
                        var href = link.getAttribute('href').toLowerCase();
                        if(href.includes(currentCategory)){
                            link.classList.add('active');
                        }
                    });
                }
            }
        }, 500);
    </script>

    <script>
        // ============================================
        // MINI SIDEBAR AND BROWSE TOGGLE FUNCTIONALITY
        // ============================================
        var miniSidebar = document.getElementById('miniSidebar');
        var mainSidebar = document.getElementById('sidebar');
        var backdrop = document.getElementById('overlayBackdrop');
        var expandBtn = document.getElementById('expandSidebar');
        var browseToggle = document.getElementById('browseToggle');
        var miniIcons = document.querySelectorAll('.mini-sidebar-icon');
        var miniPopover = document.getElementById('miniPopover');
        var popoverTitle = miniPopover ? miniPopover.querySelector('.mini-popover-title') : null;
        var popoverList = miniPopover ? miniPopover.querySelector('.mini-popover-list') : null;
        var currentPopoverKey = null;
        var lastIconCenterY = 0; // track icon center Y for arrow realignment
        var lastIconRight   = 0; // track icon right edge for mobile popover left
        var lastIconTop     = 0; // track icon top for mobile popover start

        // Responsive function to show/hide browse toggle
        function updateBrowseToggleVisibility() {
            if(!browseToggle) return;
            if(window.innerWidth <= 1024) {
                browseToggle.classList.add('active');
            } else {
                browseToggle.classList.remove('active');
            }
        }

        // Initialize on load
        if(browseToggle) updateBrowseToggleVisibility();

        // Update on window resize
        window.addEventListener('resize', updateBrowseToggleVisibility);

        // Helpers for popover
        function getCategoryKeyFromTarget(dataTarget) {
            if (!dataTarget) return null;
            var keys = [
                'arc-welding-machine','arc-welding-robots','batteries','drilling-and-lifting','gas-detectors','portable-ventilators','power-tools','protection','welding-accessories','welding-consumables'
            ];
            for (var i=0;i<keys.length;i++) { if (dataTarget.indexOf('/'+keys[i]+'/') !== -1 || dataTarget.indexOf(keys[i]+'/') !== -1) return keys[i]; }
            return null;
        }
        function getCategoryTitle(key) {
            var map = {
                'arc-welding-machine': 'Arc Welding Machines',
                'arc-welding-robots': 'Arc Welding Robots',
                'batteries': 'Batteries',
                'drilling-and-lifting': 'Drilling and Lifting',
                'gas-detectors': 'Gas Detectors',
                'portable-ventilators': 'Portable Ventilators',
                'power-tools': 'Power Tools',
                'protection': 'Personal Protective Equipment',
                'welding-accessories': 'Welding Accessories',
                'welding-consumables': 'Welding Consumables'
            };
            return map[key] || 'Categories';
        }
        function getPopoverItems(key) {
            var base = '.';
            var maps = {
                'arc-welding-robots': [
                    { label: 'G3 Controller Series', href: base + '/arc-welding-robots/g3-controller-series.php' },
                    { label: 'G4 Controller Series', href: base + '/arc-welding-robots/g4-controller-series.php' },
                    { label: 'Featured Products and Solutions', href: base + '/arc-welding-robots/featured-products-and-solution.php' },
                    { label: 'Robot System Peripherals', href: base + '/arc-welding-robots/robot-system-peripherals.php' }
                ],
                'arc-welding-machine': [
                    { label: 'MIG Welding Machine', href: base + '/arc-welding-machine/mig-welding-machine.php' },
                    { label: 'Accessories and Consumables', href: base + '/arc-welding-machine/accessories-and-consumables.php', subitems: [
                        { label: 'Welding Torch / Gun', href: base + '/arc-welding-machine/accessories-and-consumables/welding-torch-gun.php' },
                        { label: 'Torch Consumables', href: base + '/arc-welding-machine/accessories-and-consumables/torch-consumables.php' },
                        { label: 'Accessories', href: base + '/arc-welding-machine/accessories-and-consumables/accessories.php' }
                    ]},
                    { label: 'CO2/MAG Welding Machine', href: base + '/arc-welding-machine/co1-mag-welding-machine.php' },
                    { label: 'STUD Welding Machine', href: base + '/arc-welding-machine/stud-welding-machine.php' },
                    { label: 'TIG Welding Machine', href: base + '/arc-welding-machine/tig-welding-machine.php' },
                    { label: 'Plasma Cutting Machine', href: base + '/arc-welding-machine/plasma-cutting-machine.php' }
                ],
                'batteries': [
                    { label: 'Maintenance Free', href: base + '/batteries/maintenance-free.php' },
                    { label: 'Low Maintenance', href: base + '/batteries/low-maintenance.php' },
                    { label: 'Special Batteries', href: base + '/batteries/special-batteries.php' }
                ],
                'drilling-and-lifting': [
                    { label: 'Lifting', href: base + '/drilling-and-lifting/lifting.php' },
                    { label: 'Magnetic Drill', href: base + '/drilling-and-lifting/magnetic-drill.php', subitems: [
                        { label: 'B-Line Series', href: base + '/drilling-and-lifting/magnetic-drill/b-line-series.php' },
                        { label: 'RL-E Line Series', href: base + '/drilling-and-lifting/magnetic-drill/rl-e-line-series.php' },
                        { label: 'RBX-Line Series', href: base + '/drilling-and-lifting/magnetic-drill/rbx-line-series.php' },
                        { label: 'SP-Line Series', href: base + '/drilling-and-lifting/magnetic-drill/sp-line-series.php' },
                        { label: 'V-Line Series', href: base + '/drilling-and-lifting/magnetic-drill/v-line-series.php' }
                    ]},
                    { label: 'Cutters', href: base + '/drilling-and-lifting/cutters.php' }
                ],
                'gas-detectors': [
                    { label: 'Single Gas Detector', href: base + '/gas-detectors/single-gas-detector.php' },
                    { label: 'Multi Gas Detector', href: base + '/gas-detectors/multi-gas-detector.php' },
                    { label: 'Portable Gas Detectors', href: base + '/gas-detectors/portable-gas-detectors.php' },
                    { label: 'Docking and Data Management', href: base + '/gas-detectors/docking-data-management.php' },
                    { label: 'Calibration Gas and Regulators', href: base + '/gas-detectors/calibration-gas-regulators.php' }
                ],
                'power-tools': [
                    { label: 'Grinder', href: base + '/power-tools/grinder.php' },
                    { label: 'Saw', href: base + '/power-tools/saw.php' },
                    { label: 'Drill and Wrench', href: base + '/power-tools/drill-and-wrench.php' },
                    { label: 'Rotary and Demolition Hammer', href: base + '/power-tools/rotary-and-demolition-hammer.php' },
                    { label: 'Accessories', href: base + '/power-tools/accessories.php' }
                ],
                'portable-ventilators': [
                    { label: 'Electric Driven', href: base + '/portable-ventilators/electric-driven.php' },
                    { label: 'Pneumatic Driven', href: base + '/portable-ventilators/pneumatic-driven.php' }
                ],
                'protection': [
                    { label: 'Eye Protection', href: base + '/protection/eye-protection.php' },
                    { label: 'Hand Protection', href: base + '/protection/hand-protection.php', subitems: [
                        { label: 'Welding Gloves', href: base + '/protection/welding-gloves.php' },
                        { label: 'Working Gloves', href: base + '/protection/working-gloves.php' },
                        { label: 'Chemical and Liquid Protection Gloves', href: base + '/protection/chemical-liquid-protection-gloves.php' },
                        { label: 'Disposable Gloves', href: base + '/protection/disposable-gloves.php' }
                    ]},
                    { label: 'Hearing & Respiratory Protection', href: base + '/protection/hearing-respiratory-protection.php' },
                    { label: 'Welding Head and Face Protection', href: base + '/protection/welding-head-and-face-protection.php' },
                    { label: 'Body Protection', href: base + '/protection/body-protection.php', subitems: [
                        { label: 'Particulate and Low Hazard', href: base + '/protection/particulate-low-hazard.php' },
                        { label: 'Liquid Spray and Splash', href: base + '/protection/liquid-spray-splash.php' },
                        { label: 'Chemical and Flame Retardant', href: base + '/protection/chemical-flame-retardant.php' }
                    ]}
                ],
                'welding-accessories': [
                    { label: 'Welding Electrode Oven', href: base + '/welding-accessories/welding-electrode-oven.php' },
                    { label: 'Non-Destructive Crack Detection', href: base + '/welding-accessories/non-destructive-crack-detection.php' },
                    { label: 'Gas Saving Regulator', href: base + '/welding-accessories/gas-saving-regulator.php' },
                    { label: 'Gas Cutting Equipment', href: base + '/welding-accessories/gas-cutting-equipment.php' },
                    { label: 'Industrial Markers', href: base + '/welding-accessories/industrial-markers.php' },
                    { label: 'Measuring Gauge', href: base + '/welding-accessories/measuring-gauge.php' },
                    { label: 'Others', href: base + '/welding-accessories/others.php' }
                ],
                'welding-consumables': [
                    { label: 'Kobelco', href: base + '/welding-consumables/kobelco.php' },
                    { label: 'Metrode', href: base + '/welding-consumables/metrode.php' }
                ]
            };
            return maps[key] || [];
        }
        function renderPopover(key) {
            if (!miniPopover || !popoverList) return;
            popoverList.innerHTML = '';
            var items = getPopoverItems(key);
            items.forEach(function(it){
                var li = document.createElement('li');
                li.className = 'mini-popover-item';
                
                if (it.subitems && it.subitems.length > 0) {
                    // Item with subitems - add container for expanded view
                    li.innerHTML = '<span class="square"></span><a href="'+ it.href +'">'+ it.label +'</a><button class="popover-expand-btn" aria-expanded="false"><i class="bi bi-chevron-right"></i></button>';
                    li.className += ' has-subitems';
                    
                    // Create subitems container
                    var subContainer = document.createElement('div');
                    subContainer.className = 'popover-subitems collapsed';
                    subContainer.innerHTML = it.subitems.map(function(sub){
                        return '<a href="'+ sub.href +'" class="popover-subitem">'+ sub.label +'</a>';
                    }).join('');
                    
                    li.appendChild(subContainer);
                    
                    // Add expand/collapse handler
                    var expandBtn = li.querySelector('.popover-expand-btn');
                    expandBtn.addEventListener('click', function(e){
                        e.preventDefault();
                        e.stopPropagation();
                        var isExpanded = expandBtn.getAttribute('aria-expanded') === 'true';
                        if (isExpanded) {
                            expandBtn.setAttribute('aria-expanded', 'false');
                            subContainer.classList.add('collapsed');
                        } else {
                            expandBtn.setAttribute('aria-expanded', 'true');
                            subContainer.classList.remove('collapsed');
                        }
                        // Adjust popover height after expanding/collapsing
                        setTimeout(function(){
                            adjustPopoverHeight();
                        }, 10);
                    });
                } else {
                    // Regular item
                    li.innerHTML = '<span class="square"></span><a href="'+ it.href +'">'+ it.label +'</a>';
                }
                
                popoverList.appendChild(li);
            });
            if (popoverTitle) popoverTitle.textContent = getCategoryTitle(key);
        }
        /* Shared: compute top + arrow for a given height, centered on lastIconCenterY */
        function _applyPosition(finalHeight) {
            var vh = window.innerHeight;
            var isMobile = window.innerWidth <= 768;

            if (isMobile) {
                // On mobile: align to icon top, but shift up if it would overflow the bottom
                var popLeft   = Math.round(lastIconRight);
                var headerMin = 90; // don't go above header
                var bottomPad = 8;
                var popTop    = Math.round(lastIconTop);
                var maxH      = vh - headerMin - bottomPad;
                var contentH  = Math.min(finalHeight, maxH);
                // If it would overflow the bottom, shift top upward just enough
                if (popTop + contentH > vh - bottomPad) {
                    popTop = vh - contentH - bottomPad;
                }
                // Never overlap the header
                if (popTop < headerMin) popTop = headerMin;

                miniPopover.style.left   = popLeft + 'px';
                miniPopover.style.right  = '0px';
                miniPopover.style.width  = 'auto';
                miniPopover.style.top    = popTop + 'px';
                miniPopover.style.height = contentH + 'px';
                miniPopover.style.setProperty('--arrow-offset', '-9999px');
            } else {
                // Desktop: center on icon
                var headerBottom = 140;
                var top = Math.round(lastIconCenterY - finalHeight / 2);
                if (top < headerBottom)                top = headerBottom;
                if (top + finalHeight > vh - 8)        top = vh - finalHeight - 8;
                if (top < headerBottom)                top = headerBottom;

                miniPopover.style.right  = '';
                miniPopover.style.width  = '';
                var arrowOffset = Math.round(lastIconCenterY - top - 26);
                arrowOffset = Math.max(8, Math.min(finalHeight - 44, arrowOffset));

                miniPopover.style.top    = top + 'px';
                miniPopover.style.height = finalHeight + 'px';
                miniPopover.style.setProperty('--arrow-offset', arrowOffset + 'px');
            }
        }
        function adjustPopoverHeight() {
            if (!miniPopover) return;
            var isMobile = window.innerWidth <= 768;

            miniPopover.style.height = 'auto';
            var naturalH = miniPopover.offsetHeight; 
            var finalH   = Math.min(naturalH, window.innerHeight * 0.88);
            _applyPosition(finalH);
        }
        function positionPopoverForIcon(icon) {
            if (!miniPopover || !icon) return;

            // Always reset mobile-specific styles first so they don't bleed into desktop
            miniPopover.style.right = '';
            miniPopover.style.width = '';

            var rect = icon.getBoundingClientRect();
            lastIconCenterY = rect.top + rect.height / 2;
            lastIconRight   = rect.right;
            lastIconTop     = rect.top;

            var isMobile = window.innerWidth <= 768;

            if (!isMobile) {
                // Desktop: position to the right of the icon
                var pw   = miniPopover.offsetWidth;
                var left = Math.round(rect.right + 14);
                if (left + pw + 12 > window.innerWidth) {
                    left = Math.round(rect.left - pw - 14);
                }
                miniPopover.style.left = left + 'px';
            }

            // Measure true height off-screen, then position
            miniPopover.style.height = 'auto';
            miniPopover.style.top    = '-9999px';
            miniPopover.classList.add('show');

            var naturalH = miniPopover.offsetHeight;
            var finalH   = Math.min(naturalH, window.innerHeight * 0.88);

            _applyPosition(finalH);
        }
        function hidePopover() {
            if (!miniPopover) return;
            miniPopover.classList.remove('show');
            miniPopover.setAttribute('aria-hidden', 'true');
            // Reset all inline styles so next open starts clean
            miniPopover.style.right  = '';
            miniPopover.style.width  = '';
            miniPopover.style.height = '';
            currentPopoverKey = null;
        }
        function showPopoverForKey(key, icon) {
            if (!miniPopover) return;
            if (currentPopoverKey === key && miniPopover.classList.contains('show')) {
                hidePopover();
                return;
            }
            // Reset while we measure new content
            miniPopover.classList.remove('show');
            miniPopover.style.left = '-9999px';
            miniPopover.style.top  = '-9999px';
            miniPopover.style.height = 'auto';

            renderPopover(key);
            currentPopoverKey = key;

            // positionPopoverForIcon makes it visible off-screen, measures, then places it
            positionPopoverForIcon(icon);
            miniPopover.setAttribute('aria-hidden', 'false');
        }

        // Close on outside click / Escape
        document.addEventListener('click', function(e){
            if (!miniPopover) return;
            if (!miniPopover.classList.contains('show')) return;
            if (e.target.closest('.mini-popover') || e.target.closest('.sub-indicator')) return;
            hidePopover();
        });
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') hidePopover(); });

        // On resize, force-close and fully reset the popover so mobile styles never bleed into desktop
        var _resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(_resizeTimer);
            _resizeTimer = setTimeout(function() {
                if (!miniPopover) return;
                miniPopover.classList.remove('show');
                miniPopover.setAttribute('aria-hidden', 'true');
                miniPopover.style.right  = '';
                miniPopover.style.width  = '';
                miniPopover.style.height = '';
                miniPopover.style.top    = '';
                miniPopover.style.left   = '';
                currentPopoverKey = null;
            }, 150);
        });

        // Browse toggle click
        if(browseToggle) {
            browseToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var isMiniSidebarVisible = window.getComputedStyle(miniSidebar).display !== 'none';
                
                if(window.innerWidth > 1024 && isMiniSidebarVisible) {
                    miniSidebar.classList.toggle('expanded');
                    browseToggle.classList.toggle('expanded');
                } else {
                    if(mainSidebar.classList.contains('active')) {
                        mainSidebar.classList.remove('active');
                        backdrop.classList.remove('active');
                    } else {
                        mainSidebar.classList.add('active');
                        backdrop.classList.add('active');
                        mainSidebar.style.display = 'block';
                        backdrop.style.display = 'block';
                    }
                }
            });
        }

        // Expand/collapse sidebar when clicking expand button
        expandBtn.addEventListener('click', function() {
            miniSidebar.classList.toggle('expanded');
            if(browseToggle) browseToggle.classList.toggle('expanded');
        });

        // On mobile, collapse by default
        if(window.innerWidth <= 768) {
            miniSidebar.classList.remove('expanded');
            if(browseToggle) browseToggle.classList.remove('expanded');
        }

        // Menu bar click handler
        var menuBar = document.getElementById('miniSidebarMenuBar');
        if(menuBar) {
            menuBar.addEventListener('click', function() {
                miniSidebar.classList.toggle('expanded');
                if(browseToggle) browseToggle.classList.toggle('expanded');
            });
        }

        // ARROW CLICK HANDLER
        var arrowHandler = function(e) {
            e.stopPropagation();
            e.preventDefault();

            var arrow = (e.target && e.target.closest('.sub-indicator')) || e.currentTarget;
            var icon = arrow ? arrow.closest('.mini-sidebar-icon') : null;
            if (!icon) return;

            var dataTarget = icon.getAttribute('data-target') || '';
            var categoryKey = getCategoryKeyFromTarget(dataTarget);
            if (!categoryKey) return;

            showPopoverForKey(categoryKey, icon);
        };
        
        document.querySelectorAll('.sub-indicator').forEach(function(arrow) {
            arrow.addEventListener('click', arrowHandler, true);
        });
        
        document.addEventListener('click', function(e) {
            if (e.target.closest('.sub-indicator')) {
                arrowHandler(e);
            }
        }, true);

        // Mini icon navigation
        miniIcons.forEach(function(icon) {
            icon.addEventListener('click', function(e) {
                if (e.target.closest('.sub-indicator')) {
                    e.stopPropagation();
                    return;
                }
                
                var target = this.getAttribute('data-target');
                if (target) {
                    window.location.href = target;
                }
            }, true);
        });

        // Close sidebar backdrop click
        backdrop.addEventListener('click', function() {
            if(mainSidebar.classList.contains('active')) {
                mainSidebar.classList.remove('active');
                backdrop.classList.remove('active');
            }
        });

        // Close sidebar button click
        var closeSidebarBtn = document.getElementById('closeSidebar');
        if(closeSidebarBtn) {
            closeSidebarBtn.addEventListener('click', function() {
                if(mainSidebar.classList.contains('active')) {
                    mainSidebar.classList.remove('active');
                    backdrop.classList.remove('active');
                }
            });
        }
    </script>

    <script>
        // ============================================
        // UPDATE CART BADGE COUNT IN REAL-TIME
        // ============================================
        (function(){
            function updateCartBadge() {
                var badge = document.getElementById('cartBadge');
                if(!badge) return;
                
                var items = JSON.parse(localStorage.getItem('inquiryItems') || '[]');
                var count = items.length;
                
                if(count > 0) {
                    badge.textContent = count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
            
            // Update on page load
            updateCartBadge();
            
            // Update on storage change (when items added from other pages)
            window.addEventListener('storage', updateCartBadge);
            
            // Update frequently to catch changes
            setInterval(updateCartBadge, 500);
        })();
    </script>

    <script>
        // Mobile FAB toggle for mini sidebar
        (function() {
            var fab = document.getElementById('mobileSidebarFab');
            var sidebar = document.getElementById('miniSidebar');
            var fabIcon = document.getElementById('mobileFabIcon');
            if (!fab || !sidebar) return;

            function isMobile() { return window.innerWidth <= 768; }

            function syncFab() {
                if (!isMobile()) { fab.classList.remove('open', 'wide'); return; }
                var isOpen = sidebar.classList.contains('mobile-visible');
                var isExpanded = sidebar.classList.contains('expanded');
                fab.classList.toggle('open', isOpen);
                fab.classList.toggle('wide', isOpen && isExpanded);
                fabIcon.className = isOpen ? 'bi bi-chevron-left' : 'bi bi-chevron-right';
            }

            fab.addEventListener('click', function(e) {
                e.stopPropagation();
                if (!isMobile()) return;
                sidebar.classList.toggle('mobile-visible');
                syncFab();
            });

            // Keep FAB in sync when sidebar expand/collapse changes its width
            var observer = new MutationObserver(function() { syncFab(); });
            observer.observe(sidebar, { attributes: true, attributeFilter: ['class'] });

            window.addEventListener('resize', syncFab);
        })();
    </script>

    <!-- Step Navigation Logic -->
    <script>
    (function() {
        // Allow clicking a selected radio card to deselect it (highlight updates via :checked CSS)
        document.querySelectorAll('.radio-card').forEach(function(card) {
            var radio = card.querySelector('input[type="radio"]');
            // Record state before click (label click re-checks the radio after our handler)
            card.addEventListener('mousedown', function() {
                radio._wasChecked = radio.checked;
            });
            card.addEventListener('click', function() {
                if (radio._wasChecked) {
                    // Uncheck after browser finishes processing the label click
                    setTimeout(function() {
                        radio.checked = false;
                        radio.dispatchEvent(new Event('change', { bubbles: true }));
                    }, 0);
                }
            });
        });
        var steps = document.querySelectorAll('.inq-step');
        var cards = [
            document.getElementById('card-items'),
            document.getElementById('card-contact'),
            document.getElementById('card-prefs'),
            document.getElementById('card-submit')
        ];

        // Click step → scroll to card
        steps.forEach(function(step) {
            step.addEventListener('click', function() {
                var targetId = step.getAttribute('data-target');
                var target = document.getElementById(targetId);
                if (target) {
                    var offset = 160;
                    var top = target.getBoundingClientRect().top + window.scrollY - offset;
                    window.scrollTo({ top: top, behavior: 'smooth' });
                }
            });
        });

        // Set active step based on scroll position
        function setActiveStep(idx) {
            steps.forEach(function(s, i) {
                s.classList.toggle('active', i === idx);
            });
        }

        // IntersectionObserver — highlight step when card enters viewport
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var idx = cards.indexOf(entry.target);
                    if (idx !== -1) setActiveStep(idx);
                }
            });
        }, { rootMargin: '-40% 0px -50% 0px', threshold: 0 });

        cards.forEach(function(card) { if (card) observer.observe(card); });

        // Mark step as completed when required fields in that card are filled
        function checkCompletion() {
            // Step 2 (Contact) — all required fields filled
            var contactFields = ['fullname','company','email','phone','address'];
            var contactDone = contactFields.every(function(id) {
                var el = document.getElementById(id);
                return el && el.value.trim() !== '';
            });
            steps[1].classList.toggle('completed', contactDone);

            // Step 3 (Preferences) — check if any contact_method radio is actually selected
            var prefsDone = !!document.querySelector('input[name="contact_method"]:checked');
            steps[2].classList.toggle('completed', prefsDone);

            // Step 1 (Items) — at least 1 item
            var hasItems = document.getElementById('inquiryTableBody') &&
                           document.getElementById('inquiryTableBody').children.length > 0;
            steps[0].classList.toggle('completed', hasItems);
        }

        // Listen for input changes
        document.getElementById('inquiryForm').addEventListener('input', checkCompletion);
        document.getElementById('inquiryForm').addEventListener('change', checkCompletion);

        // Re-check after table renders (called from inquiry logic)
        window._inqStepCheck = checkCompletion;
    })();
    </script>

    <!-- Inquiry Form Logic -->
    <script>
    (function() {
        var STORAGE_KEY = 'inquiryItems';

        function getItems() {
            try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || []; }
            catch(e) { return []; }
        }

        function saveItems(items) {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
        }

        function renderTable() {
            var items = getItems();
            var tbody = document.getElementById('inquiryTableBody');
            var emptyMsg = document.getElementById('emptyMsg');
            var table = document.getElementById('inquiryTable');

            tbody.innerHTML = '';

            if (items.length === 0) {
                table.style.display = 'none';
                emptyMsg.style.display = 'block';
            } else {
                table.style.display = 'table';
                emptyMsg.style.display = 'none';
                items.forEach(function(item, idx) {
                    var tr = document.createElement('tr');
                    tr.innerHTML =
                        '<td>' + escHtml(item.name || '') + (item.brand ? '<br><span style="font-size:11px;color:#888;">(' + escHtml(item.brand) + ')</span>' : '') + '</td>' +
                        '<td><input class="inq-qty-input" type="number" min="1" value="' + (item.qty || 1) + '" data-idx="' + idx + '"></td>' +
                        '<td><button class="inq-remove-btn" data-idx="' + idx + '" type="button">Remove</button></td>';
                    tbody.appendChild(tr);
                });
            }

            // Update hidden input
            document.getElementById('itemsJsonInput').value = JSON.stringify(items);

            // Update step completion state
            if (typeof window._inqStepCheck === 'function') window._inqStepCheck();
        }

        function escHtml(str) {
            return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        // Event delegation for remove, qty input, and qty +/- buttons
        document.getElementById('inquiryTableBody').addEventListener('click', function(e) {
            var btn = e.target.closest('.inq-remove-btn');
            if (btn) {
                var idx = parseInt(btn.getAttribute('data-idx'));
                var items = getItems();
                items.splice(idx, 1);
                saveItems(items);
                renderTable();
                return;
            }
            var decBtn = e.target.closest('.inq-qty-dec');
            if (decBtn) {
                var idx = parseInt(decBtn.getAttribute('data-idx'));
                var items = getItems();
                if (items[idx] && items[idx].qty > 1) {
                    items[idx].qty = (items[idx].qty || 1) - 1;
                    saveItems(items);
                    renderTable();
                }
                return;
            }
            var incBtn = e.target.closest('.inq-qty-inc');
            if (incBtn) {
                var idx = parseInt(incBtn.getAttribute('data-idx'));
                var items = getItems();
                if (items[idx]) {
                    items[idx].qty = (items[idx].qty || 1) + 1;
                    saveItems(items);
                    renderTable();
                }
                return;
            }
        });

        document.getElementById('inquiryTableBody').addEventListener('change', function(e) {
            if (e.target.classList.contains('inq-qty-input')) {
                var idx = parseInt(e.target.getAttribute('data-idx'));
                var items = getItems();
                var val = parseInt(e.target.value);
                if (!isNaN(val) && val > 0) { items[idx].qty = val; }
                saveItems(items);
                document.getElementById('itemsJsonInput').value = JSON.stringify(items);
            }
        });

        // Sync itemsJsonInput before submit
        document.getElementById('inquiryForm').addEventListener('submit', function() {
            document.getElementById('itemsJsonInput').value = JSON.stringify(getItems());
        });

        // Clear button
        document.getElementById('clearFormBtn').addEventListener('click', function() {
            if (confirm('Clear the form and all inquiry items?')) {
                localStorage.removeItem(STORAGE_KEY);
                document.getElementById('inquiryForm').reset();
                renderTable();
            }
        });

        // Initial render
        renderTable();

        // Re-render if localStorage changes from another tab
        window.addEventListener('storage', function(e) {
            if (e.key === STORAGE_KEY) renderTable();
        });
    })();
    </script>

    <script>
    // File drop zone & file name display
    function handleFileChange(input) {
        var nameEl = document.getElementById('fileChosenName');
        if (input.files && input.files.length > 0) {
            nameEl.textContent = '\u2714 ' + input.files[0].name;
            nameEl.style.display = 'block';
        } else {
            nameEl.style.display = 'none';
        }
    }
    (function() {
        var zone = document.getElementById('fileDropZone');
        if (!zone) return;
        zone.addEventListener('dragover', function(e) {
            e.preventDefault();
            zone.classList.add('drag-over');
        });
        zone.addEventListener('dragleave', function() {
            zone.classList.remove('drag-over');
        });
        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            zone.classList.remove('drag-over');
            var fileInput = zone.querySelector('input[type="file"]');
            if (fileInput && e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                handleFileChange(fileInput);
            }
        });
    })();
    </script>
</body>
</html>

