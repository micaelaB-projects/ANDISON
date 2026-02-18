<?php
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
    if ($fullname && $company && $email && $address) {
        // Parse items JSON
        $items = json_decode($items_json, true) ?: [];
        
        // Build email content
        $to = 'lizette.macalindol@gmail.com';
        $subject = 'New Inquiry Form Submission from ' . $fullname;
        
        $items_list = '';
        if (!empty($items)) {
            $items_list = "<h3>Inquiry Items:</h3><ul>";
            foreach ($items as $item) {
                $model = htmlspecialchars($item['model'] ?? 'N/A');
                $type = htmlspecialchars($item['type'] ?? 'N/A');
                $brand = htmlspecialchars($item['brand'] ?? 'N/A');
                $qty = intval($item['qty'] ?? 1);
                $items_list .= "<li><strong>$model</strong> ($type) - $brand - Qty: $qty</li>";
            }
            $items_list .= "</ul>";
        } else {
            $items_list = "<p>No items selected</p>";
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
    
    <div class=\"section\">
        " . $items_list . "
    </div>
    
    <div class=\"section\">
        <p><span class=\"label\">Message:</span></p>
        <p>" . nl2br($message ?: 'No message provided') . "</p>
    </div>
    
    <hr>
    <p style=\"font-size: 12px; color: #666;\">This inquiry was submitted via ANDISON INDUSTRIAL website.</p>
</body>
</html>
        ";

        // Email headers
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "From: " . $email . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";

        // Send email
        $mail_sent = mail($to, $subject, $body, $headers);

        // Handle file upload if provided
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_tmp = $_FILES['file']['tmp_name'];
            $file_name = basename($_FILES['file']['name']);
            $file_path = $upload_dir . time() . '_' . $file_name;
            move_uploaded_file($file_tmp, $file_path);
        }

        // Show success message and clear localStorage
        $success_message = $mail_sent ? "Inquiry submitted successfully! We'll contact you soon." : "Error sending inquiry. Please try again.";
        echo "<script>alert('" . addslashes($success_message) . "'); if(" . ($mail_sent ? 'true' : 'false') . ") { localStorage.removeItem('inquiryItems'); window.location.href='inquirylist.php'; }</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Inquiry Form - ANDISON INDUSTRIAL</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root{--accent:#2B11DB;--muted:#f5f7fb;--card:#ffffff;--success:#10b981;--danger:#ef4444}
        *{box-sizing:border-box}
        body{font-family:'Segoe UI', -apple-system, BlinkMacSystemFont, Tahoma, Geneva, Verdana, sans-serif;background:linear-gradient(135deg, #f5f7fb 0%, #eff2ff 100%);color:#1f2937;margin:0;padding:142px 20px 48px 20px}
        .container{max-width:800px;margin:0 auto}
        .form-card{background:var(--card);border-radius:16px;padding:32px 28px;box-shadow:0 10px 40px rgba(43,17,219,0.08);border:1px solid rgba(43,17,219,0.05)}
        h1{color:var(--accent);font-size:24px;margin:0 0 6px;font-weight:700;letter-spacing:-0.5px}
        .form-subtitle{color:#6b7280;font-size:13px;margin-bottom:24px;line-height:1.5}
        .form-section{margin-bottom:20px;padding-bottom:16px;border-bottom:2px solid #f3f4f6}
        .form-section:last-of-type{border-bottom:none}
        .section-title{font-size:12px;font-weight:700;color:var(--accent);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px}
        .form-row{margin-bottom:14px}
        label{display:block;font-size:13px;margin-bottom:6px;color:#374151;font-weight:600;letter-spacing:0.3px}
        input[type="text"], input[type="email"], input[type="tel"], select, textarea, input[type="number"]{
            width:100%;padding:12px 14px;border-radius:8px;border:2px solid #e5e7eb;background:#fff;font-size:14px;color:#1f2937;transition:all 0.3s;font-family:inherit}
        input[type="text"]:focus, input[type="email"]:focus, input[type="tel"]:focus, select:focus, textarea:focus, input[type="number"]:focus{
            outline:none;border-color:var(--accent);background:#fafbff;box-shadow:0 0 0 3px rgba(43,17,219,0.1)}
        input[type="text"]::placeholder, input[type="email"]::placeholder, input[type="tel"]::placeholder, textarea::placeholder{
            color:#9ca3af}
        textarea{min-height:120px;resize:vertical;line-height:1.6}
        #address, #message{font-family:inherit;font-size:14px;line-height:1.6;color:#1f2937}
        .small{font-size:12px;color:#9ca3af;margin-top:8px;line-height:1.5}
        .row{display:flex;gap:14px}
        .col{flex:1}
        .actions{display:flex;justify-content:flex-end;gap:12px;margin-top:20px;padding-top:16px;border-top:2px solid #f3f4f6}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:10px 22px;border-radius:8px;border:2px solid;font-weight:700;cursor:pointer;font-size:13px;transition:all 0.3s;text-decoration:none}
        .btn-clear{background:#f3f4f6;border-color:#d1d5db;color:#374151}
        .btn-clear:hover{background:#e5e7eb;border-color:#9ca3af;transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,0.1)}
        .btn-submit{background:var(--accent);border-color:var(--accent);color:#fff}
        .btn-submit:hover{background:#2008c0;border-color:#2008c0;transform:translateY(-2px);box-shadow:0 6px 20px rgba(43,17,219,0.3)}
        .btn-submit:active{transform:translateY(0)}
        .required{color:#ef4444;margin-left:4px;font-weight:700}
        @media (max-width:640px){.row{flex-direction:column};.form-card{padding:24px 18px}}
        .form-row .options{display:flex;gap:20px;align-items:center;margin-top:10px;flex-wrap:wrap}
        .form-row .options label{display:inline-flex;align-items:center;gap:8px;cursor:pointer;font-weight:500;margin:0}
        .form-row .options input[type="radio"]{width:20px;height:20px;margin:0;cursor:pointer;accent-color:var(--accent)}
        .inquiry-items-section{background:#f9fafb;border-radius:8px;padding:16px;border:2px dashed #e5e7eb}
        .inquiry-items-section .small{margin-top:0}
        /* Header */
        header {
            background: linear-gradient(135deg, #2B11DB 0%, #2B11DB 100%);
            color: white;
            padding: 14px 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
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
            min-height: 50px;
        }

        .header-buttons {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: auto;
        }

        .right-actions {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo {
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 0 0 auto;
        }

        .logo-box img {
            height: 50px;
            width: auto;
            display: block;
        }

        .back-button {
            background: linear-gradient(135deg, #00D7B3 0%, #00C8A8 100%);
            color: #1f2937;
            border: none;
            padding: 12px 28px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            margin-left: auto;
            font-size: 14px;
            box-shadow: 0 4px 15px rgba(0, 215, 179, 0.3);
            position: relative;
            overflow: hidden;
            letter-spacing: 0.3px;
        }

        .back-button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .back-button:hover::before {
            width: 300px;
            height: 300px;
        }

        .back-button:hover {
            background: linear-gradient(135deg, #00E6FF 0%, #00C8F7 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 217, 255, 0.5);
        }

        .back-button:active {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 215, 179, 0.3);
        }

        .back-button i {
            display: none;
        }

        @media (max-width: 768px) {
            .header-buttons {
                gap: 8px;
            }

            .back-button {
                padding: 11px 22px;
                font-size: 13px;
                border-radius: 50px;
                gap: 6px;
            }

            .header-top {
                gap: 12px;
                flex-wrap: wrap;
            }

            .header-buttons {
                width: 100%;
                flex-basis: 100%;
                margin-left: 0 !important;
            }
        }

        @media (max-width: 480px) {
            .back-button {
                padding: 10px 20px;
                font-size: 12px;
                flex: 1;
                justify-content: center;
                border-radius: 50px;
            }

            .header-buttons {
                width: 100%;
            }
        }

        .header-contact {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 13px;
            flex: 0 0 auto;
            line-height: 1.4;
        }

        .contact-link {
            color: rgba(255,255,255,0.95);
            text-decoration: none;
            font-weight: 600;
            padding-bottom: 8px;
            white-space: nowrap;
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 4px;
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
            height: 40px;
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

        .search-bar .search-field::before {
            content: '🔍';
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
            gap: 8px;
            cursor: pointer;
            text-decoration: none;
            color: #333;
            font-weight: 600;
            padding: 10px 18px;
            border-radius: 6px;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #00D7B3 0%, #00D7B3 100%);
        }

        .inquiry-btn:hover,
        .cart-icon-wrapper:hover {
            background: linear-gradient(135deg, #00FFD1 0%, #00FFD1 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 255, 209, 0.4);
            color: #333;
        }

        .inquiry-btn {
            position: relative;
        }

        .cart-badge {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 100%);
            color: white;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 50%;
            min-width: 20px;
            height: 20px;
            text-align: center;
            line-height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(255, 102, 102, 0.4);
            flex-shrink: 0;
        }

        .cart-badge.hidden {
            display: none;
        }

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
            padding: 0 20px;
            display: flex;
            align-items: center;
            min-height: 52px;
            gap: 18px;
            justify-content: flex-start;
            padding-left: 160px;
        }

        .browse-toggle {
            position: absolute;
            left: 12px;
            top: 50%;
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
            line-height: 6px;
        }

        .nav-list {
            list-style: none;
            display: flex;
            gap: 28px;
            margin: 0;
            padding: 0;
        }

        .nav-list li { position: relative; }

        .nav-list a {
            color: white;
            text-decoration: none;
            font-size: 15px;
            padding: 12px 6px;
            display: block;
            transition: color 0.2s;
            position: relative;
        }

        .nav-list a:hover { color: rgba(255,255,255,0.8); }

        .nav-list > li > a {
            position: relative;
            padding: 10px 14px;
            color: white;
            transition: color 180ms ease, background 180ms ease;
        }

        .nav-list > li > a.active {
            background: rgba(0,0,0,0.14);
            color: #fff;
            font-weight: 700;
            border-radius: 6px;
            box-shadow: inset 0 -6px 18px rgba(0,0,0,0.06);
        }

        .nav-list > li > a.active::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -8px;
            transform: translateX(-50%);
            width: 44px;
            height: 6px;
            border-radius: 6px;
            background: linear-gradient(90deg, #00ffd1 0%, #00d4aa 50%, #2B11DB 100%);
            box-shadow: 0 8px 28px rgba(0,212,170,0.18), 0 0 40px rgba(43,17,219,0.08);
            pointer-events: none;
        }

        .nav-list > li > a:hover::after {
            width: 56px;
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

        nav li:nth-child(3) .nav-dropdown ul {
            display: grid !important;
            grid-template-columns: repeat(5, 1fr) !important;
            gap: 12px 20px !important;
            margin-top: 16px !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a {
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 60px !important;
        }

        nav li:nth-child(3) .nav-dropdown ul li {
            min-height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        nav li:nth-child(3) .nav-dropdown ul a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 70px;
        }

        nav li:nth-child(3) .nav-dropdown ul a img {
            max-width: 85px;
            max-height: 45px;
            object-fit: contain;
            display: block;
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
            width: 380px;
            max-width: 90%;
            background: #fff;
            box-shadow: 4px 0 24px rgba(0,0,0,0.15);
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 70;
            padding: 28px 20px;
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
        }
        .sidebar-list a:hover { 
            background: #f3f4f6; 
            color: #2B11DB;
            padding-left: 16px;
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
            margin: 0; 
            padding: 8px 0 8px 44px; 
            display: none;
            background: #fafafa;
            margin-left: 12px;
            margin-right: 12px;
            padding-left: 16px;
            border-left: 2px solid #e5e7eb;
            padding-top: 8px;
            padding-bottom: 8px;
        }
        .sidebar-sublist li { 
            padding: 4px 0; 
            border: none;
        }
        .sidebar-sublist a { 
            color: #4b5563; 
            font-size: 14px; 
            padding: 6px 8px; 
            display: block; 
            text-decoration: none;
            justify-content: flex-start;
        }
        .sidebar-sublist a:hover { 
            color: #2B11DB; 
            background: transparent;
            padding-left: 12px;
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
            margin: 10px 0 10px -12px; 
            padding: 0; 
            display: none;
        }
        .sidebar-nested-sublist li { 
            padding: 0;
            border: none;
        }
        .sidebar-nested-sublist a { 
            color: #5a6b7d; 
            font-size: 13px; 
            padding: 10px 12px 10px 28px; 
            display: block; 
            text-decoration: none;
            position: relative;
            transition: all 0.25s ease;
            border-radius: 6px;
            margin: 2px 0;
        }
        .sidebar-nested-sublist a::before {
            content: '';
            position: absolute;
            left: 8px;
            top: 50%;
            transform: translateY(-50%);
            width: 6px;
            height: 6px;
            background: linear-gradient(135deg, #2B11DB 0%, #6d28d9 100%);
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(43, 17, 219, 0.2);
        }
        .sidebar-nested-sublist a:hover { 
            color: #2B11DB;
            background: rgba(43, 17, 219, 0.08);
            padding-left: 32px;
            transform: translateX(4px);
        }

        .sidebar-nested-sublist.collapsed { display: none; }
        .sidebar-nested-sublist:not(.collapsed) { display: block; }
        .sidebar-list li.has-sub { position: relative; }
        .has-sub > a { padding-right: 40px; }
        .sub-toggle {
            position: absolute;
            right: 12px;
            top: 16px;
            transform: none;
            background: transparent;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 0;
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0;
            box-shadow: none;
        }
        .sub-toggle:focus { outline: none; }
        .sub-toggle .bi { transition: transform 200ms ease; font-size: 16px; }
        .sub-toggle[aria-expanded="true"] .bi { transform: rotate(90deg); }
        .sidebar-sublist.collapsed { display: none; }
        .sidebar-sublist:not(.collapsed) { display: block; }

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
            top: calc(14px + 50px + 14px + 12px + 52px);
            bottom: 0;
            width: 80px;
            background: #2B11DB;
            box-shadow: 2px 0 16px rgba(0,0,0,0.1);
            z-index: 65;
            padding: 20px 12px;
            overflow: hidden;
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .mini-sidebar.expanded {
            width: 280px;
            overflow-y: auto;
            padding: 20px 12px;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .mini-sidebar.expanded::-webkit-scrollbar {
            display: none;
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
            margin-bottom: 8px;
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1), justify-content 0.5s cubic-bezier(0.4, 0, 0.2, 1), padding 0.5s cubic-bezier(0.4, 0, 0.2, 1), background 0.3s ease, transform 0.2s ease;
            gap: 12px;
            padding: 0;
            flex-shrink: 0;
            min-width: 56px;
        }

        .mini-sidebar-icon .label {
            display: none;
            font-size: 13px;
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
            padding: 12px;
            min-width: auto;
        }

        .mini-sidebar.expanded .mini-sidebar-icon .label {
            display: block;
            opacity: 1;
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
            background: rgba(255,255,255,0.2);
            transform: scale(1.05);
        }

        .mini-sidebar.expanded .mini-sidebar-icon:hover {
            transform: translateX(4px);
        }

        .mini-sidebar-icon.active-icon {
            background: #00D7B3;
            color: #2B11DB;
        }

        .mini-sidebar-icon .sub-indicator {
            position: absolute;
            bottom: -1px;
            right: -1px;
            background: rgba(255,255,255,0.1);
            color: #ffffff;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            opacity: 0.95;
            transition: background 0.15s ease, color 0.15s ease;
            z-index: 999;
            cursor: pointer;
            pointer-events: auto;
            border: 1px solid #ffffff;
            box-shadow: none;
        }

        .mini-sidebar-icon:hover .sub-indicator {
            opacity: 1;
            background: #00D7B3;
            color: #2B11DB;
        }

        .mini-sidebar-icon .sub-indicator:active {
            transform: translateY(0);
        }

        .mini-sidebar.expanded .mini-sidebar-icon .sub-indicator {
            position: static;
            background: transparent;
            color: #2B11DB;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-left: auto;
            opacity: 1;
            border: 0;
            cursor: pointer;
            pointer-events: auto;
            z-index: 100;
            box-shadow: none;
        }

        .mini-sidebar-toggle {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.15);
            border: none;
            color: #fff;
            cursor: pointer;
            border-radius: 8px;
            font-size: 20px;
            margin-top: auto;
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1), padding 0.5s cubic-bezier(0.4, 0, 0.2, 1), background 0.3s ease, transform 0.2s ease;
            flex-shrink: 0;
        }

        .mini-sidebar-toggle:hover {
            background: rgba(255,255,255,0.25);
            transform: scale(1.05);
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
            padding: 12px;
            min-width: auto;
        }

        /* Adjust main container for mini sidebar */
        .main-content, .category-container {
            margin-left: 80px;
            transition: margin-left 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .mini-sidebar.expanded ~ .main-content,
        .mini-sidebar.expanded ~ .category-container {
            margin-left: 280px;
        }

        /* When sidebar is expanded (collapsed mini) */
        .sidebar-overlay.expanded {
            width: 380px;
        }

        .overlay-backdrop.expanded {
            display: none !important;
        }

        @media (max-width: 1024px) {
            .mini-sidebar {
                display: none !important;
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
        }

        /* Mini popover styles for subcategories */
        .mini-popover {
            position: fixed;
            top: -9999px;
            left: -9999px;
            width: 320px;
            max-width: calc(100vw - 32px);
            background: linear-gradient(180deg, #1976D2FF 0%, #19D2B6FF 100%);
            color: #fff;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.25);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-6px);
            transition: opacity 160ms ease, transform 160ms ease, visibility 160ms ease;
            z-index: 200;
        }
        .mini-popover.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
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
            background: #f5f9ff;
            color: #0f5132;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
            padding: 12px 16px;
            font-weight: 800;
            font-size: 15px;
            letter-spacing: 0.3px;
        }
        .mini-popover-title { color: #0f5132; }
        .mini-popover-body {
            padding: 12px 16px 16px 16px;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
        }
        .mini-popover-list {
            list-style: none;
            margin: 0;
            padding: 6px 0 6px 0;
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
        }
        .mini-popover-item {
            position: relative;
            padding-left: 42px;
            margin: 12px 0;
            display: flex;
            align-items: stretch;
            min-height: 32px;
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
        }
        .mini-popover-item a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            display: block;
            padding: 8px 10px;
            border-radius: 8px;
            transition: background 140ms ease, transform 120ms ease;
            width: 100%;
        }
        .mini-popover-item a:hover {
            background: rgba(255,255,255,0.12);
            transform: translateX(2px);
        }

        .sidebar-sublist {
            max-height: 1000px;
            overflow: hidden;
            transition: max-height 0.3s ease, opacity 0.3s ease;
            opacity: 1;
        }
        
        .sidebar-sublist.collapsed {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <?php
    $company_name = "ANDISON INDUSTRIAL";
    $phone = "+1(234) 567 8900";
    $phone2 = "+1(234) 567 8900";
    $phone3 = "+1(639) 977 803 7398";
    $email = "info@andison-industrial.com";
    ?>
    <header>
        <div class="header-top">
            <div class="logo">
                <div class="logo-box"><a href="home.php"><img src="assets/HOME/image-removebg-preview.png" alt="Andison Industrial" /></a></div>
            </div>

            <div class="search-bar">
                <form class="search-field" action="search.php" method="get">
                    <input type="text" name="q" placeholder="Search for products" value="<?php echo htmlspecialchars(isset($_GET['q']) ? $_GET['q'] : '', ENT_QUOTES); ?>" onkeydown="if(event.key==='Enter') this.form.submit();">
                </form>
            </div>

            <div class="right-actions">
                <a href="javascript:history.back()" class="inquiry-btn" style="margin-right: 12px;">BACK</a>
                <a href="inquirylist.php" class="cart-icon-wrapper" title="Inquiry List">
                    <span>INQUIRY LIST</span>
                    <span class="cart-badge hidden" id="cartBadge">0</span>
                </a>
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
                <button id="browseToggle" class="browse-toggle"></button>
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
                        <a href="brands.php" class="active">Brands</a>
                        <div class="nav-dropdown">
                            <h4>Featured Brands</h4>
                            <ul>
                                <li><a href="brand.php?name=Panasonic%20Connect"><img src="assets/brands/PANASONIC.jpg" alt="Panasonic Connect" title="Panasonic Connect"></a></li>
                                <li><a href="brand.php?name=Kobelco"><img src="assets/brands/KOBELCO.jpg" alt="Kobelco" title="Kobelco"></a></li>
                                <li><a href="brand.php?name=Metrode"><img src="assets/brands/METRODE.jpg" alt="Metrode" title="Metrode"></a></li>
                                <li><a href="brand.php?name=DryRod.%20II"><img src="assets/brands/DRYROD.jpg" alt="DryRod. II" title="DryRod. II"></a></li>
                                <li><a href="brand.php?name=Weldcraft"><img src="assets/brands/WELDCRAFT.jpg" alt="Weldcraft" title="Weldcraft"></a></li>
                                <li><a href="brand.php?name=Truweld"><img src="assets/brands/TRUWELD.jpg" alt="Truweld" title="Truweld"></a></li>
                                <li><a href="brand.php?name=Arcair"><img src="assets/brands/ARCAIR.jpg" alt="Arcair" title="Arcair"></a></li>
                                <li><a href="brand.php?name=Magnaflux"><img src="assets/brands/MAGNAFLUX.jpg" alt="Magnaflux" title="Magnaflux"></a></li>
                                <li><a href="brand.php?name=Tempilstik"><img src="assets/brands/TEMPILSTIK.jpg" alt="Tempilstik" title="Tempilstik"></a></li>
                                <li><a href="brand.php?name=Tanaka"><img src="assets/brands/TANAKA.jpg" alt="Tanaka" title="Tanaka"></a></li>
                                <li><a href="brand.php?name=Chiyoda"><img src="assets/brands/CHIYODA.jpg" alt="Chiyoda" title="Chiyoda"></a></li>
                                <li><a href="brand.php?name=Yutaka"><img src="assets/brands/YUTAKA.jpg" alt="Yutaka" title="Yutaka"></a></li>
                                <li><a href="brand.php?name=Hard%20Workers"><img src="assets/brands/HARDWORKER.jpg" alt="Hard Workers" title="Hard Workers"></a></li>
                                <li><a href="brand.php?name=Soyer"><img src="assets/brands/SOYER.jpg" alt="Soyer" title="Soyer"></a></li>
                                <li><a href="brand.php?name=Aquasol"><img src="assets/brands/AQUASOL.jpg" alt="Aquasol" title="Aquasol"></a></li>
                                <li><a href="brand.php?name=SK%20And%20GAL%20GAGE"><img src="assets/brands/SK%20AND%20GAL%20GAGE.jpg" alt="SK And GAL GAGE" title="SK And GAL GAGE"></a></li>
                                <li><a href="brand.php?name=Coppus"><img src="assets/brands/COPPUS.jpg" alt="Coppus" title="Coppus"></a></li>
                                <li><a href="brand.php?name=BW%20Technologies"><img src="assets/brands/BW%20TECHNOLOGIES.jpg" alt="BW Technologies" title="BW Technologies"></a></li>
                                <li><a href="brand.php?name=RAC"><img src="assets/brands/RAE%20SYSTEMS.jpg" alt="RAC" title="RAC"></a></li>
                                <li><a href="brand.php?name=Weldas"><img src="assets/brands/WELDAS.jpg" alt="Weldas" title="Weldas"></a></li>
                                <li><a href="brand.php?name=Uvex"><img src="assets/brands/UVEX.jpg" alt="Uvex" title="Uvex"></a></li>
                                <li><a href="brand.php?name=Aces"><img src="assets/brands/ACES.jpg" alt="Aces" title="Aces"></a></li>
                                <li><a href="brand.php?name=Microgard"><img src="assets/brands/MICROGARD.jpg" alt="Microgard" title="Microgard"></a></li>
                                <li><a href="brand.php?name=Ansell"><img src="assets/brands/ANSELL.jpg" alt="Ansell" title="Ansell"></a></li>
                                <li><a href="brand.php?name=Alfra"><img src="assets/brands/ALFRA.jpg" alt="Alfra" title="Alfra"></a></li>
                                <li><a href="brand.php?name=Bosch"><img src="assets/brands/BOSCH.jpg" alt="Bosch" title="Bosch"></a></li>
                                <li><a href="brand.php?name=Makita"><img src="assets/brands/MAKITA.jpg" alt="Makita" title="Makita"></a></li>
                                <li><a href="brand.php?name=Weller"><img src="assets/brands/WEILER.jpg" alt="Weller" title="Weller"></a></li>
                                <li><a href="brand.php?name=Garryson"><img src="assets/brands/GARRYSON.jpg" alt="Garryson" title="Garryson"></a></li>
                                <li><a href="brand.php?name=Spilfyter"><img src="assets/brands/SPILFYTER.jpg" alt="Spilfyter" title="Spilfyter"></a></li>
                                <li><a href="brand.php?name=Dalo"><img src="assets/brands/DALO.jpg" alt="Dalo" title="Dalo"></a></li>
                                <li><a href="brand.php?name=Motolite"><img src="assets/brands/MOTOLITE.jpg" alt="Motolite" title="Motolite"></a></li>
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

    <!-- Sidebar overlay -->
    <div id="overlay" class="overlay-backdrop" aria-hidden="true"></div>
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
                <a href="/ANDISON/arc-welding-machine/arc-welding-machine.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-charge"></i></span><span class="sidebar-label">Arc Welding Machines</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-arc-welding" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-arc-welding" class="sidebar-sublist collapsed">
                    <li><a href="/ANDISON/arc-welding-machine/mig-welding-machine.php">MIG Welding Machine</a></li>
                    <li><a href="/ANDISON/arc-welding-machine/co1-mag-welding-machine.php">CO1/MAG Welding Machine</a></li>
                    <li><a href="/ANDISON/arc-welding-machine/stud-welding-machine.php">STUD Welding Machine</a></li>
                    <li><a href="/ANDISON/arc-welding-machine/tig-welding-machine.php">TIG Welding Machine</a></li>
                    <li><a href="/ANDISON/arc-welding-machine/plasma-cutting-machine.php">Plasma Cutting Machine</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="/ANDISON/arc-welding-robots/arc-welding-robot.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-robot"></i></span><span class="sidebar-label">Arc Welding Robots</span></a>
                <button class="sub-toggle" aria-controls="sub-arc-robots" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-arc-robots" class="sidebar-sublist collapsed">
                    <li><a href="/ANDISON/arc-welding-robots/g3-controller-series.php">G3 Controller Series</a></li>
                    <li><a href="/ANDISON/arc-welding-robots/g4-controller-series.php">G4 Controller Series</a></li>
                    <li><a href="/ANDISON/arc-welding-robots/featured-products-and-solution.php">Featured Products and Solutions</a></li>
                    <li><a href="/ANDISON/arc-welding-robots/robot-system-peripherals.php">Robot System Peripherals</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="/ANDISON/batteries/batteries.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-fill"></i></span><span class="sidebar-label">Batteries</span></a>
                <button class="sub-toggle" aria-controls="sub-batteries" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-batteries" class="sidebar-sublist collapsed">
                    <li><a href="/ANDISON/batteries/maintenance-free.php">Maintenance Free</a></li>
                    <li><a href="/ANDISON/batteries/low-maintenance.php">Low Maintenance</a></li>
                    <li><a href="/ANDISON/batteries/special-batteries.php">Special Batteries</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="/ANDISON/drilling-and-lifting/drilling-and-lifting.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-hammer"></i></span><span class="sidebar-label">Drilling and Lifting</span></a>
                <button class="sub-toggle" aria-controls="sub-drilling-lifting" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-drilling-lifting" class="sidebar-sublist collapsed">
                    <li><a href="/ANDISON/drilling-and-lifting/lifting.php">Lifting</a></li>
                    <li><a href="/ANDISON/drilling-and-lifting/magnetic-drill.php">Magnetic Drill</a></li>
                    <li><a href="/ANDISON/drilling-and-lifting/cutters.php">Cutters</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="/ANDISON/gas-detectors/gas-detectors.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-bullseye"></i></span><span class="sidebar-label">Gas Detectors</span></a>
                <button class="sub-toggle" aria-controls="sub-gas-detectors" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-gas-detectors" class="sidebar-sublist collapsed">
                    <li><a href="/ANDISON/gas-detectors/single-gas-detector.php">Single Gas Detector</a></li>
                    <li><a href="/ANDISON/gas-detectors/multi-gas-detector.php">Multi Gas Detector</a></li>
                    <li><a href="/ANDISON/gas-detectors/portable-gas-detectors.php">Portable Gas Detectors</a></li>
                    <li><a href="/ANDISON/gas-detectors/docking-data-management.php">Docking and Data Management</a></li>
                    <li><a href="/ANDISON/gas-detectors/calibration-gas-regulators.php">Calibration Gas and Regulators</a></li>
                </ul>
            </li>
            <li class="">
                <a href="/ANDISON/portable-ventilators/portable-ventilators.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-fan"></i></span><span class="sidebar-label">Portable Ventilators</span></a>
            </li>
            <li class="has-sub">
                <a href="/ANDISON/power-tools/power-tools.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-tools"></i></span><span class="sidebar-label">Power Tools</span></a>
                <button class="sub-toggle" aria-controls="sub-power-tool" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-power-tool" class="sidebar-sublist collapsed">
                    <li><a href="/ANDISON/power-tools/grinder.php">Grinder</a></li>
                    <li><a href="/ANDISON/power-tools/saw.php">Saw</a></li>
                    <li><a href="/ANDISON/power-tools/drill-and-wrench.php">Drill and Wrench</a></li>
                    <li><a href="/ANDISON/power-tools/rotary-and-demolition-hammer.php">Rotary and Demolition Hammer</a></li>
                    <li><a href="/ANDISON/power-tools/accessories.php">Accessories</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="/ANDISON/protection/protection.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-shield-check"></i></span><span class="sidebar-label">Personal Protective Equipment</span></a>
                <button class="sub-toggle" aria-controls="sub-protection-safety" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-protection-safety" class="sidebar-sublist collapsed">
                    <li><a href="/ANDISON/protection/eye-protection.php">Eye Protection</a></li>
                    <li class="has-nested-sub">
                        <a href="/ANDISON/protection/hand-protection.php">Hand Protection</a>
                        <ul id="nested-hand-protection" class="sidebar-nested-sublist collapsed">
                            <li><a href="/ANDISON/protection/working-gloves.php">Working Gloves</a></li>
                            <li><a href="/ANDISON/protection/chemical-liquid-protection-gloves.php">Chemical and Liquid Protection Gloves</a></li>
                            <li><a href="/ANDISON/protection/disposable-gloves.php">Disposable Gloves</a></li>
                            <li><a href="/ANDISON/protection/welding-gloves.php">Welding Gloves</a></li>
                        </ul>
                    </li>
                    <li><a href="/ANDISON/protection/hearing-respiratory-protection.php">Hearing &amp; Respiratory Protection</a></li>
                    <li class="has-nested-sub">
                        <a href="/ANDISON/protection/body-protection.php">Body Protection</a>
                        <ul id="nested-body-protection" class="sidebar-nested-sublist collapsed">
                            <li><a href="/ANDISON/protection/chemical-flame-retardant.php">Chemical and Flame Retardant</a></li>
                            <li><a href="/ANDISON/protection/liquid-spray-splash.php">Liquid Spray and Splash</a></li>
                            <li><a href="/ANDISON/protection/particulate-low-hazard.php">Particulate and Low Hazard</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="/ANDISON/welding-accessories/welding-accessories.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-gear"></i></span><span class="sidebar-label">Welding Accessories</span></a>
                <button class="sub-toggle" aria-controls="sub-welding-accessories" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-welding-accessories" class="sidebar-sublist collapsed">
                    <li><a href="/ANDISON/welding-accessories/welding-electrode-oven.php">Welding Electrode Oven</a></li>
                    <li><a href="/ANDISON/welding-accessories/non-destructive-crack-detection.php">Non-Destructive Crack Detection</a></li>
                    <li><a href="/ANDISON/welding-accessories/gas-saving-regulator.php">Gas Saving Regulator</a></li>
                    <li><a href="/ANDISON/welding-accessories/gas-cutting-equipment.php">Gas Cutting Equipment</a></li>
                    <li><a href="/ANDISON/welding-accessories/industrial-markers.php">Industrial Markers</a></li>
                    <li><a href="/ANDISON/welding-accessories/measuring-gauge.php">Measuring Gauge</a></li>
                    <li><a href="/ANDISON/welding-accessories/others.php">Others</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="/ANDISON/welding-consumables/welding-consumables.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-box"></i></span><span class="sidebar-label">Welding Consumables</span></a>
                <button class="sub-toggle" aria-controls="sub-welding-consumables" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-welding-consumables" class="sidebar-sublist collapsed">
                    <li><a href="/ANDISON/welding-consumables/kobelco.php">Kobelco</a></li>
                    <li><a href="/ANDISON/welding-consumables/metrode.php">Metrode</a></li>
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
        <div class="mini-sidebar-icon has-sub" data-target="/ANDISON/arc-welding-machine/arc-welding-machine.php" title="Arc Welding Machines"><i class="bi bi-lightning-charge"></i><span class="label">Arc Welding Machines</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="/ANDISON/arc-welding-robots/arc-welding-robot.php" title="Arc Welding Robots"><i class="bi bi-robot"></i><span class="label">Arc Welding Robots</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="/ANDISON/batteries/batteries.php" title="Batteries"><i class="bi bi-lightning-fill"></i><span class="label">Batteries</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="/ANDISON/drilling-and-lifting/drilling-and-lifting.php" title="Drilling and Lifting"><i class="bi bi-hammer"></i><span class="label">Drilling and Lifting</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="/ANDISON/gas-detectors/gas-detectors.php" title="Gas Detectors"><i class="bi bi-bullseye"></i><span class="label">Gas Detectors</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="/ANDISON/portable-ventilators/portable-ventilators.php" title="Portable Ventilators"><i class="bi bi-fan"></i><span class="label">Portable Ventilators</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="/ANDISON/power-tools/power-tools.php" title="Power Tools"><i class="bi bi-tools"></i><span class="label">Power Tools</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="/ANDISON/protection/protection.php" title="Personal Protective Equipment"><i class="bi bi-shield-check"></i><span class="label">PPE</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="/ANDISON/welding-accessories/welding-accessories.php" title="Welding Accessories"><i class="bi bi-gear"></i><span class="label">Welding Accessories</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="/ANDISON/welding-consumables/welding-consumables.php" title="Welding Consumables"><i class="bi bi-box"></i><span class="label">Welding Consumables</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <button class="mini-sidebar-toggle" id="expandSidebar" title="Toggle Sidebar"><i class="bi bi-chevron-right"></i></button>
    </div>

    <!-- Floating popover for mini sidebar subcategories -->
    <div id="miniPopover" class="mini-popover" aria-hidden="true">
        <div class="mini-popover-header">
            <div class="mini-popover-title">Arc Welding Robots</div>
        </div>
        <div class="mini-popover-body">
            <ul class="mini-popover-list"></ul>
        </div>
    </div>

    <div class="container">
        <div class="form-card" role="region" aria-labelledby="inquiryHeading">
            <h1 id="inquiryHeading">Inquiry Form</h1>
            <p class="form-subtitle">Share your product requirements and we'll get back to you within 24 hours</p>
            
            <form id="inquiryForm" action="inquirylist.php" method="post" enctype="multipart/form-data">
                <!-- Inquiry Items Section -->
                <div class="form-section">
                    <div class="section-title"><i class="bi bi-box-seam"></i> Inquiry Items</div>
                    <div class="inquiry-items-section" id="inquiryItemsContainer">
                        <p class="small">📦 No items added yet. Use <strong>"ADD TO INQUIRY LIST"</strong> on product pages to add items.</p>
                    </div>
                </div>
                <input type="hidden" id="items_json" name="items_json" value="">

                <!-- Contact Information Section -->
                <div class="form-section">
                    <div class="section-title"><i class="bi bi-person-lines-fill"></i> Contact Information</div>
                    
                    <div class="form-row">
                        <label for="fullname">Full Name <span class="required">*</span></label>
                        <input id="fullname" name="fullname" type="text" placeholder="John Doe" required>
                    </div>

                    <div class="form-row">
                        <label for="company">Company <span class="required">*</span></label>
                        <input id="company" name="company" type="text" placeholder="Your Company Name" required>
                    </div>

                    <div class="row">
                        <div class="col form-row">
                            <label for="email">Email <span class="required">*</span></label>
                            <input id="email" name="email" type="email" placeholder="john@example.com" required>
                        </div>
                        <div class="col form-row">
                            <label for="phone">Phone <span style="color:#9ca3af;font-weight:400">(Optional)</span></label>
                            <input id="phone" name="phone" type="tel" placeholder="+63 912 345 6789">
                        </div>
                    </div>

                    <div class="form-row">
                        <label for="address">Delivery Address <span class="required">*</span></label>
                        <textarea id="address" name="address" placeholder="Street address, city, state, postal code" required></textarea>
                    </div>
                </div>

                <!-- Preferences Section -->
                <div class="form-section">
                    <div class="section-title"><i class="bi bi-chat-dots"></i> Communication Preferences</div>
                    
                    <div class="form-row">
                        <label>Preferred Contact Method <span class="required">*</span></label>
                        <div class="options">
                            <label><input type="radio" name="contact_method" value="email" checked> <i class="bi bi-envelope"></i> Email</label>
                            <label><input type="radio" name="contact_method" value="phone"> <i class="bi bi-telephone"></i> Phone</label>
                        </div>
                    </div>
                </div>

                <!-- Additional Information Section -->
                <div class="form-section">
                    <div class="section-title"><i class="bi bi-chat-left-text"></i> Additional Information</div>
                    
                    <div class="form-row">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" placeholder="Tell us about your project, specifications, timeline, or any special requirements..."></textarea>
                        <p class="small">💡 Tip: Include project details to help us serve you better</p>
                    </div>

                    <div class="form-row">
                        <label for="file">Attachments <span style="color:#9ca3af;font-weight:400">(Optional)</span></label>
                        <input id="file" name="file" type="file" accept="image/*,application/pdf">
                        <p class="small">📎 Supported: Images (JPG, PNG) and PDF documents</p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="actions">
                    <button type="button" class="btn btn-clear" id="clearBtn"><i class="bi bi-arrow-clockwise"></i> Clear</button>
                    <button type="submit" class="btn btn-submit"><i class="bi bi-send-fill"></i> Submit Inquiry</button>
                </div>
                <p class="small" style="margin-top:16px;text-align:center">✓ Your information is secure and will be used solely to respond to your inquiry</p>
            </form>
        </div>
    </div>

    <script>
        // Sidebar toggle functionality
        (function(){
            var browseToggle = document.getElementById('browseToggle');
            var sidebarOverlay = document.querySelector('.sidebar-overlay');
            var overlayBackdrop = document.querySelector('.overlay-backdrop');
            var sidebarClose = document.querySelector('.sidebar-close');
            
            if(browseToggle && sidebarOverlay && overlayBackdrop) {
                browseToggle.addEventListener('click', function(){
                    sidebarOverlay.classList.toggle('active');
                    overlayBackdrop.classList.toggle('active');
                });
                
                overlayBackdrop.addEventListener('click', function(){
                    sidebarOverlay.classList.remove('active');
                    overlayBackdrop.classList.remove('active');
                });
                
                if(sidebarClose) {
                    sidebarClose.addEventListener('click', function(){
                        sidebarOverlay.classList.remove('active');
                        overlayBackdrop.classList.remove('active');
                    });
                }
            }
            
            // Sidebar sub-toggle functionality
            var subToggles = document.querySelectorAll('.sub-toggle');
            subToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var sublist = document.getElementById(toggle.getAttribute('aria-controls'));
                    if(sublist) {
                        sublist.classList.toggle('collapsed');
                        toggle.setAttribute('aria-expanded', sublist.classList.contains('collapsed') ? 'false' : 'true');
                    }
                });
            });
            
            // Nested toggle functionality
            var nestedToggles = document.querySelectorAll('.nested-toggle');
            nestedToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var nestedlist = document.getElementById(toggle.getAttribute('aria-controls'));
                    if(nestedlist) {
                        nestedlist.classList.toggle('collapsed');
                        toggle.setAttribute('aria-expanded', nestedlist.classList.contains('collapsed') ? 'false' : 'true');
                    }
                });
            });

            // Contact dropdown toggle functionality
            var contactDropdowns = document.querySelectorAll('.contact-dropdown');
            contactDropdowns.forEach(function(dropdown) {
                var closeBtn = dropdown.querySelector('.contact-close');
                if(closeBtn) {
                    closeBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        dropdown.classList.add('closed');
                    });
                }
                
                dropdown.addEventListener('mouseleave', function() {
                    dropdown.classList.remove('closed');
                });
                
                dropdown.addEventListener('focusout', function(e) {
                    if(!dropdown.contains(e.relatedTarget)) {
                        dropdown.classList.remove('closed');
                    }
                });
            });
        })();
    </script>

    <script>
        (function(){
            var form = document.getElementById('inquiryForm');
            var clear = document.getElementById('clearBtn');
            clear.addEventListener('click', function(){ form.reset(); });
            // basic client-side validation feedback
            form.addEventListener('submit', function(e){
                if(!form.checkValidity()){
                    e.preventDefault();
                    form.reportValidity();
                    return;
                }
                // attach inquiry items to form
                try{
                    var items = JSON.parse(localStorage.getItem('inquiryItems')||'[]');
                    document.getElementById('items_json').value = JSON.stringify(items);
                }catch(err){ document.getElementById('items_json').value = '[]'; }
            });
        })();
    </script>
    <script>
        // Render and manage inquiry list stored in localStorage
        (function(){
            function getItems(){ try{ return JSON.parse(localStorage.getItem('inquiryItems')||'[]'); }catch(e){ return []; } }
            function setItems(items){ localStorage.setItem('inquiryItems', JSON.stringify(items)); }
            var container = document.getElementById('inquiryItemsContainer');

            function render(){
                var items = getItems();
                if(!items || items.length === 0){ container.innerHTML = '<p class="small">No items added yet. Use "add to inquiry list" on product pages to add items.</p>'; return; }
                var html = '<ul style="list-style:none;padding:0;margin:0;">';
                items.forEach(function(it, idx){
                    html += '<li style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f0f2f5;">'
                         + '<div style="flex:1">'
                         + '<strong>' + (it.model||'Unnamed') + '</strong>'
                         + '<div style="font-size:13px;color:#666">' + (it.brand||'') + ' • ' + (it.type||'') + '</div>'
                         + '</div>'
                         + '<div style="display:flex;gap:8px;align-items:center">'
                         + '<input data-idx="'+idx+'" class="item-qty" type="number" min="1" value="'+(it.qty||1)+'" style="width:64px;padding:6px;border:1px solid #e6e9ef;border-radius:6px">'
                         + '<button data-idx="'+idx+'" class="item-remove" type="button" style="background:#fff;border:1px solid #e6e9ef;padding:6px 8px;border-radius:6px;cursor:pointer">Remove</button>'
                         + '</div>'
                         + '</li>';
                });
                html += '</ul>';
                container.innerHTML = html;
            }

            // Function to update cart badge
            function triggerBadgeUpdate() {
                var badge = document.getElementById('cartBadge');
                if(badge) {
                    var items = getItems();
                    var count = items.length;
                    if(count > 0) {
                        badge.textContent = count > 99 ? '99+': count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
            }

            // events
            container.addEventListener('click', function(e){
                var rem = e.target.closest('.item-remove');
                if(rem){ var idx = parseInt(rem.dataset.idx,10); var items = getItems(); items.splice(idx,1); setItems(items); render(); triggerBadgeUpdate(); }
            });
            container.addEventListener('change', function(e){
                var q = e.target.closest('.item-qty');
                if(q){ var idx = parseInt(q.dataset.idx,10); var items = getItems(); var val = parseInt(q.value,10) || 1; items[idx].qty = val; setItems(items); render(); triggerBadgeUpdate(); }
            });

            // clear button also clears items
            var clearBtn = document.getElementById('clearBtn');
            if(clearBtn){ clearBtn.addEventListener('click', function(){ localStorage.removeItem('inquiryItems'); render(); triggerBadgeUpdate(); }); }

            render();
        })();
    </script>
    <script>
        // Update cart badge count in real-time
        (function(){
            function updateCartBadge() {
                var badge = document.getElementById('cartBadge');
                if(!badge) return;
                
                try {
                    var items = JSON.parse(localStorage.getItem('inquiryItems') || '[]');
                    var count = items.length;
                    
                    if(count > 0) {
                        badge.textContent = count > 99 ? '99+' : count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                } catch(e) {
                    badge.classList.add('hidden');
                }
            }

            // Update on page load
            updateCartBadge();

            // Listen for storage changes from other pages
            window.addEventListener('storage', updateCartBadge);
            
            // Also check periodically in case other tabs update
            setInterval(updateCartBadge, 500);
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
        var backdrop = document.getElementById('overlay');
        var expandBtn = document.getElementById('expandSidebar');
        var browseToggle = document.getElementById('browseToggle');
        var miniIcons = document.querySelectorAll('.mini-sidebar-icon');
        var miniPopover = document.getElementById('miniPopover');
        var popoverTitle = miniPopover ? miniPopover.querySelector('.mini-popover-title') : null;
        var popoverList = miniPopover ? miniPopover.querySelector('.mini-popover-list') : null;
        var currentPopoverKey = null;

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
            var base = '/ANDISON';
            var maps = {
                'arc-welding-robots': [
                    { label: 'G3 Controller Series', href: base + '/arc-welding-robots/G3-Controller-Series.php' },
                    { label: 'G4 Controller Series', href: base + '/arc-welding-robots/G4-Controller-Series.php' },
                    { label: 'Featured Products and Solutions', href: base + '/arc-welding-robots/featured-products-and-solution.php' },
                    { label: 'Robot System Peripherals', href: base + '/arc-welding-robots/robot-system-peripherals.php' },
                    { label: 'Arc Welding Robot', href: base + '/arc-welding-robots/arc-welding-robot.php' }
                ],
                'arc-welding-machine': [
                    { label: 'MIG Welding Machine', href: base + '/arc-welding-machine/mig-welding-machine.php' },
                    { label: 'CO1/MAG Welding Machine', href: base + '/arc-welding-machine/co1-mag-welding-machine.php' },
                    { label: 'STUD Welding Machine', href: base + '/arc-welding-machine/stud-welding-machine.php' },
                    { label: 'TIG Welding Machine', href: base + '/arc-welding-machine/tig-welding-machine.php' },
                    { label: 'Plasma Cutting Machine', href: base + '/arc-welding-machine/plasma-cutting-machine.php' },
                    { label: 'Accessories & Consumables', href: base + '/arc-welding-machine/accessories-and-consumables.php' }
                ],
                'batteries': [
                    { label: 'Maintenance Free', href: base + '/batteries/maintenance-free.php' },
                    { label: 'Low Maintenance', href: base + '/batteries/low-maintenance.php' },
                    { label: 'Special Batteries', href: base + '/batteries/special-batteries.php' }
                ],
                'drilling-and-lifting': [
                    { label: 'Lifting', href: base + '/drilling-and-lifting/lifting.php' },
                    { label: 'Magnetic Drill', href: base + '/drilling-and-lifting/magnetic-drill.php' },
                    { label: 'Cutters', href: base + '/drilling-and-lifting/cutters.php' },
                    { label: 'B-Line Series', href: base + '/drilling-and-lifting/b-line-series.php' },
                    { label: 'RBX Line Series', href: base + '/drilling-and-lifting/rbx-line-series.php' },
                    { label: 'RL/E Line Series', href: base + '/drilling-and-lifting/rl-e-line-series.php' },
                    { label: 'SP Line Series', href: base + '/drilling-and-lifting/sp-line-series.php' },
                    { label: 'V Line Series', href: base + '/drilling-and-lifting/v-line-series.php' }
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
                    { label: 'Hand Protection', href: base + '/protection/hand-protection.php' },
                    { label: 'Working Gloves', href: base + '/protection/working-gloves.php' },
                    { label: 'Welding Gloves', href: base + '/protection/welding-gloves.php' },
                    { label: 'Disposable Gloves', href: base + '/protection/disposable-gloves.php' },
                    { label: 'Chemical Liquid Protection Gloves', href: base + '/protection/chemical-liquid-protection-gloves.php' },
                    { label: 'Hearing & Respiratory Protection', href: base + '/protection/hearing-respiratory-protection.php' },
                    { label: 'Body Protection', href: base + '/protection/body-protection.php' },
                    { label: 'Chemical Flame Retardant', href: base + '/protection/chemical-flame-retardant.php' },
                    { label: 'Liquid Spray Splash Protection', href: base + '/protection/liquid-spray-splash.php' },
                    { label: 'Particulate Low Hazard', href: base + '/protection/particulate-low-hazard.php' },
                    { label: 'Welding Head & Face Protection', href: base + '/protection/welding-head-and-face-protection.php' }
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
                li.innerHTML = '<span class="square"></span><a href="'+ it.href +'">'+ it.label +'</a>';
                popoverList.appendChild(li);
            });
            if (popoverTitle) popoverTitle.textContent = getCategoryTitle(key);
        }
        function positionPopoverForIcon(icon) {
            if (!miniPopover || !icon) return;
            miniPopover.style.left = '-9999px';
            miniPopover.style.top = '-9999px';
            miniPopover.classList.add('show');
            var rect = icon.getBoundingClientRect();
            var pw = miniPopover.offsetWidth;
            var ph = miniPopover.offsetHeight;
            var iconCenterY = rect.top + rect.height / 2;

            var left = Math.round(rect.right + 14);
            var top = Math.round(iconCenterY - ph / 2);

            if (left + pw + 12 > window.innerWidth) {
                left = Math.round(rect.left - pw - 14);
            }

            var headerHeight = 170;
            var minTop = headerHeight + 12;
            var maxTop = window.innerHeight - ph - 12;
            if (top < minTop) top = minTop;
            if (top > maxTop) top = maxTop;

            var arrowOffset = iconCenterY - top - 26;
            miniPopover.style.setProperty('--arrow-offset', arrowOffset + 'px');

            miniPopover.style.left = left + 'px';
            miniPopover.style.top = top + 'px';
        }
        function hidePopover() {
            if (!miniPopover) return;
            miniPopover.classList.remove('show');
            miniPopover.setAttribute('aria-hidden', 'true');
            currentPopoverKey = null;
        }
        function showPopoverForKey(key, icon) {
            if (!miniPopover) return;
            if (currentPopoverKey === key && miniPopover.classList.contains('show')) {
                hidePopover();
                return;
            }
            renderPopover(key);
            positionPopoverForIcon(icon);
            miniPopover.classList.add('show');
            miniPopover.setAttribute('aria-hidden', 'false');
            currentPopoverKey = key;
        }

        // Close on outside click / Escape
        document.addEventListener('click', function(e){
            if (!miniPopover) return;
            if (!miniPopover.classList.contains('show')) return;
            if (e.target.closest('.mini-popover') || e.target.closest('.sub-indicator')) return;
            hidePopover();
        });
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') hidePopover(); });

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
</body>
</html>



