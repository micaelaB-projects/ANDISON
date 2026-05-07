<?php
declare(strict_types=1);

require_once __DIR__ . '/_auth.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim((string)$_POST['email']) : '';

    if ($email === '') {
        $error = 'Please enter your email address.';
    } else {
        require_once __DIR__ . '/../includes/supabase.php';
        
        // Find user by email
        $sbData = andison_sb_select('admin_users', 'email=eq.' . rawurlencode($email) . '&limit=1');
        
        if (is_array($sbData) && !empty($sbData[0])) {
            $user = $sbData[0];
            $username = $user['username'];
            
            // Generate a random temporary password
            $tempPassword = bin2hex(random_bytes(6)); // 12 characters
            $newHash = password_hash($tempPassword, PASSWORD_BCRYPT);
            
            // Update the user's password in Supabase
            $updated = andison_sb_update('admin_users', [
                'password_hash' => $newHash,
            ], 'username=eq.' . rawurlencode($username));
            
            if ($updated) {
                // Send email
                require_once __DIR__ . '/../includes/mailer.php';
                $subject = "ANDISON Admin - Password Reset";
                $body = "
                <p>Hello {$username},</p>
                <p>Your password has been reset. Your temporary password is: <strong>{$tempPassword}</strong></p>
                <p>Please log in using this temporary password and change it immediately from your Profile page.</p>
                <p><a href=\"http://{$_SERVER['HTTP_HOST']}/ANDISON/Andison/admin/login.php\">Proceed to Login</a></p>
                ";
                
                if (andison_send_mail($email, $subject, $body)) {
                    $success = 'A temporary password has been sent to your email address.';
                } else {
                    // Fallback securely when mail server isn't properly configured yet
                    $success = 'Email server not configured properly. For testing, your temporary password is: <strong>' . $tempPassword . '</strong>';
                }
            } else {
                $error = 'Failed to reset password in database. Please try again.';
            }
            
        } else {
            $error = 'No admin account found with that email address.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="Forgot Password - ANDISON INDUSTRIAL">
    <title>Forgot Password - ANDISON INDUSTRIAL</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root{--primary:#5b6ff7;--primary-light:#6b7eff;--text:#fff;--dark-text:#111827;--muted:#999;--border:#e0e0e0}
        *{box-sizing:border-box}
        body{margin:0;font-family:'Segoe UI',system-ui,-apple-system,Tahoma,Verdana,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;color:#111827;background:linear-gradient(135deg,#5b6ff7 0%,#3b4dd6 100%)}
        body::before{content:'';position:fixed;top:0;left:0;right:0;bottom:0;background-image:url('../../assets/about\ us/Andison\ Manila\ Picture\ -\ Edited.jpg');background-size:cover;background-position:center;background-repeat:no-repeat;background-attachment:fixed;z-index:-1}
        body::after{content:'';position:fixed;top:0;left:0;right:0;bottom:0;background:linear-gradient(135deg,rgba(91,111,247,0.75) 0%,rgba(59,77,214,0.75) 100%);z-index:-1}
        .container{width:100%;height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;position:relative}
        .login-card{width:100%;max-width:380px;background:rgba(91,111,247,0.15);border:1px solid rgba(255,255,255,0.2);border-radius:20px;padding:40px;backdrop-filter:blur(12px);box-shadow:0 8px 32px rgba(0,0,0,0.1);position:relative;display:flex;flex-direction:column;align-items:center}
        .card-header{display:flex;flex-direction:column;align-items:center;gap:0;margin-bottom:32px;text-align:center}
        .card-logo{width:130px;height:130px;flex-shrink:0;display:flex;align-items:center;justify-content:center}
        .card-logo img{width:100%;height:100%;object-fit:contain;filter:drop-shadow(0 2px 8px rgba(0,0,0,0.3))}
        h1{margin:0;font-size:28px;color:#fff;font-weight:700;letter-spacing:-0.5px}
        p.subtitle{color:rgba(255,255,255,0.8);font-size:14px;text-align:center;margin-top:10px;margin-bottom:20px;line-height:1.4}
        .form-group{margin-bottom:18px; width: 100%}
        label{display:block;font-size:11px;font-weight:700;margin-bottom:10px;color:rgba(255,255,255,0.95);text-transform:uppercase;letter-spacing:0.5px}
        .input-wrapper{position:relative}
        input[type="email"],input[type="text"]{width:100%;padding:14px 16px;border:1px solid rgba(255,255,255,0.3);border-radius:10px;background:rgba(255,255,255,0.95);font-size:14px;color:#111827;transition:all 0.3s ease}
        input[type="email"]::placeholder,input[type="text"]::placeholder{color:#bbb}
        input[type="email"]:hover,input[type="text"]:hover{border-color:rgba(255,255,255,0.5);background:#fff}
        input[type="email"]:focus,input[type="text"]:focus{outline:none;border-color:rgba(255,255,255,0.7);background:#fff;box-shadow:0 0 0 3px rgba(91,111,247,0.2)}
        .input-icon{position:absolute;right:14px;top:50%;transform:translateY(-50%);color:#bbb;font-size:18px;pointer-events:none}
        .btn{width:100%;padding:14px 20px;border-radius:12px;border:none;font-weight:700;cursor:pointer;background:#fff;color:var(--primary);transition:all 0.3s ease;position:relative;font-size:14px;letter-spacing:0.3px;text-transform:uppercase;box-shadow:0 4px 15px rgba(0,0,0,0.2);margin-top: 10px;}
        .btn:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,0.3)}
        .btn:active{transform:translateY(0)}
        .btn-spinner{display:inline-block;width:14px;height:14px;border:2px solid rgba(91,111,247,0.2);border-top-color:var(--primary);border-radius:50%;animation:spin 0.6s linear infinite}
        @keyframes spin{to{transform:rotate(360deg)}}
        .form-footer{text-align:center;margin-top:24px;color:rgba(255,255,255,0.8);font-size:13px}
        .form-footer a{color:rgba(255,255,255,0.9);text-decoration:none;font-weight:600;transition:all 0.2s ease}
        .form-footer a:hover{color:#fff;text-decoration:underline}
        .error{margin-top:16px;background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.4);padding:12px 14px;border-radius:8px;color:#ffcccc;font-size:13px;line-height:1.5; width: 100%; text-align: center;}
        .success{margin-top:16px;background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.4);padding:12px 14px;border-radius:8px;color:#d1fae5;font-size:13px;line-height:1.5; width: 100%; text-align: center;}
        @media(max-width:768px){
            .login-card{max-width:100%;padding:35px;margin:20px}
            h1{font-size:24px}
            .card-logo{width:110px;height:110px}
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card">
            <div class="card-header">
                <div class="card-logo"><img src="../../assets/HOME/image-removebg-preview.png" alt="ANDISON Logo"></div>
                <h1>Reset Password</h1>
            </div>
            
            <?php if ($success): ?>
                <div class="success" role="alert"><?php echo htmlspecialchars($success); ?></div>
                <div class="form-footer">
                    <a href="login.php">Back to Login</a>
                </div>
            <?php else: ?>
                <p class="subtitle">Enter your admin email address to receive a temporary password.</p>
                <form method="post" action="forgot_password.php" novalidate style="width: 100%;">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <input id="email" name="email" type="email" autocomplete="email" placeholder="Enter your email" required>
                            <span class="input-icon">✉</span>
                        </div>
                    </div>

                    <button class="btn" type="submit" id="submitBtn">
                        <span id="btnText">Send Email</span>
                    </button>

                    <?php if ($error): ?>
                        <div class="error" role="alert"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <div class="form-footer">
                        Remember your password? <a href="login.php">Back to Login</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <script>
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', () => {
                const btn = document.getElementById('submitBtn');
                btn.disabled = true;
                document.getElementById('btnText').innerHTML = '<span class="btn-spinner"></span>';
            });
        }
    </script>
</body>
</html>
