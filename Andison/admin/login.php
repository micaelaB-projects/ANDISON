<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
 
$cfg = andison_admin_config();
$next = isset($_GET['next']) ? (string)$_GET['next'] : 'index.php';
$error = '';
$success = false;

// Parse next parameter safely
if (!empty($next)) {
    $next = basename($next) ?: 'index.php';
    if (strpos($next, '..') !== false) {
        $next = 'index.php';
    }
}

// Generate CSRF token if not exists
if (empty($_SESSION['login_csrf_token'])) {
    $_SESSION['login_csrf_token'] = bin2hex(random_bytes(32));
}

// Brute force protection constants
const MAX_LOGIN_ATTEMPTS = 5;
const LOCKOUT_DURATION = 900; // 15 minutes in seconds

// Check if account is locked
$login_attempts = $_SESSION['login_attempts'] ?? 0;
$last_attempt_time = $_SESSION['last_attempt_time'] ?? 0;
$current_time = time();
$is_locked = false;

if ($login_attempts >= MAX_LOGIN_ATTEMPTS && ($current_time - $last_attempt_time) < LOCKOUT_DURATION) {
    $is_locked = true;
    $remaining_time = LOCKOUT_DURATION - ($current_time - $last_attempt_time);
    $error = sprintf('Too many login attempts. Please try again in %d seconds.', $remaining_time);
} elseif ($login_attempts >= MAX_LOGIN_ATTEMPTS && ($current_time - $last_attempt_time) >= LOCKOUT_DURATION) {
    // Reset attempts after lockout expires
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$is_locked) {
    // Verify CSRF token
    $csrf_token = isset($_POST['csrf_token']) ? (string)$_POST['csrf_token'] : '';
    if (!hash_equals($_SESSION['login_csrf_token'] ?? '', $csrf_token)) {
        $error = 'Invalid request. Please try again.';
    } else {
        $username = isset($_POST['username']) ? trim((string)$_POST['username']) : '';
        $password = isset($_POST['password']) ? (string)$_POST['password'] : '';
        
        if ($username !== '' && $password !== '') {
            $storedPw = (string)($cfg['password'] ?? '');
            $passwordOk = str_starts_with($storedPw, '$2y$')
                ? password_verify($password, $storedPw)
                : hash_equals($storedPw, $password);
            if (
                hash_equals((string)($cfg['username'] ?? ''), $username)
                && $passwordOk
            ) {
                $_SESSION['andison_admin'] = true;
                $_SESSION['andison_admin_user'] = $username;
                $_SESSION['login_attempts'] = 0;
                $_SESSION['last_attempt_time'] = 0;
                // Record last login timestamp
                $cfgWrite = andison_admin_config();
                $cfgWrite['last_login'] = time();
                if (empty($cfgWrite['created_at'])) {
                    $cfgWrite['created_at'] = time();
                }
                @file_put_contents(__DIR__ . '/config.php', "<?php\n\nreturn " . var_export($cfgWrite, true) . ";\n", LOCK_EX);
                // Sync last_login to Supabase
                require_once __DIR__ . '/../includes/supabase.php';
                andison_sb_update('admin_users', ['last_login' => date('c')], 'username=eq.' . rawurlencode($username));
                // Write session before regenerating to prevent data loss
                session_write_close();
                session_name('ANDISON_ADMIN');
                session_start();
                $_SESSION['andison_admin'] = true;
                $_SESSION['andison_admin_user'] = $username;
                session_regenerate_id(false);
                // Build absolute redirect URL
                $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $dir    = rtrim(dirname($_SERVER['PHP_SELF']), '/');
                header('Location: ' . $scheme . '://' . $host . $dir . '/' . $next);
                exit;
            } else {
                // Increment failed attempts
                $_SESSION['login_attempts'] = $login_attempts + 1;
                $_SESSION['last_attempt_time'] = $current_time;
                $error = 'Invalid username or password.';
            }
        } else {
            $error = 'Please enter both username and password.';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="ANDISON INDUSTRIAL Admin Portal">
    <title>Admin Login - ANDISON INDUSTRIAL</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></style>
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
        h1{margin:0;font-size:32px;color:#fff;font-weight:700;letter-spacing:-0.5px}
        .form-group{margin-bottom:18px}
        label{display:block;font-size:11px;font-weight:700;margin-bottom:10px;color:rgba(255,255,255,0.95);text-transform:uppercase;letter-spacing:0.5px}
        .input-wrapper{position:relative}
        input[type="text"],input[type="password"]{width:100%;padding:14px 16px;border:1px solid rgba(255,255,255,0.3);border-radius:10px;background:rgba(255,255,255,0.95);font-size:14px;color:#111827;transition:all 0.3s ease}
        input[type="text"]::placeholder,input[type="password"]::placeholder{color:#bbb}
        input[type="text"]:hover,input[type="password"]:hover{border-color:rgba(255,255,255,0.5);background:#fff}
        input[type="text"]:focus,input[type="password"]:focus{outline:none;border-color:rgba(255,255,255,0.7);background:#fff;box-shadow:0 0 0 3px rgba(91,111,247,0.2)}
        input:disabled{background:rgba(255,255,255,0.05);cursor:not-allowed;color:rgba(255,255,255,0.5)}
        .input-icon{position:absolute;right:14px;top:50%;transform:translateY(-50%);color:#bbb;font-size:18px;pointer-events:none}
        .password-toggle{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:#999;cursor:pointer;padding:0;display:flex;align-items:center;justify-content:center;transition:all 0.2s ease}
        .password-toggle:hover:not(:disabled){color:#111827}
        .password-toggle i{font-size:18px}
        .password-toggle:focus{outline:none}
        .password-toggle:disabled{opacity:0.5;cursor:not-allowed}
        .form-options{display:flex;justify-content:space-between;align-items:center;margin:22px 0 32px;font-size:13px;gap:12px}
        .checkbox-wrapper{display:flex;align-items:center;gap:10px}
        input[type="checkbox"]:disabled{opacity:0.5;cursor:not-allowed}
        input[type="checkbox"]:checked::after{content:'✓';color:#fff;font-weight:bold;font-size:12px;display:flex;align-items:center;justify-content:center}
        .checkbox-label{color:rgba(255,255,255,0.9);cursor:pointer;user-select:none;text-transform:uppercase;font-weight:600;letter-spacing:0.4px;font-size:11px;margin:0}
        .forgot-link{color:rgba(255,255,255,0.85);text-decoration:none;transition:all 0.2s ease;font-weight:600;font-size:11px;text-transform:uppercase;letter-spacing:0.4px}
        .forgot-link:hover{color:#fff;text-decoration:underline}
        .btn{width:100%;padding:14px 20px;border-radius:12px;border:none;font-weight:700;cursor:pointer;background:#fff;color:var(--primary);transition:all 0.3s ease;position:relative;font-size:14px;letter-spacing:0.3px;text-transform:uppercase;box-shadow:0 4px 15px rgba(0,0,0,0.2)}
        .btn:hover:not(:disabled){transform:translateY(-2px);box-shadow:0 6px 20px rgba(0,0,0,0.3)}
        .btn:active:not(:disabled){transform:translateY(0)}
        .btn:disabled{opacity:0.7;cursor:not-allowed}
        .btn-spinner{display:inline-block;width:14px;height:14px;border:2px solid rgba(91,111,247,0.2);border-top-color:var(--primary);border-radius:50%;animation:spin 0.6s linear infinite}
        @keyframes spin{to{transform:rotate(360deg)}}
        .form-footer{text-align:center;margin-top:24px;color:rgba(255,255,255,0.8);font-size:13px}
        .form-footer a{color:rgba(255,255,255,0.9);text-decoration:none;font-weight:600;transition:all 0.2s ease}
        .form-footer a:hover{color:#fff;text-decoration:underline}
        .error{margin-top:16px;background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.4);padding:12px 14px;border-radius:8px;color:#ffcccc;font-size:13px;line-height:1.5}
        input[type="hidden"]{display:none}
        @media(max-width:768px){
            .login-card{max-width:100%;padding:35px;margin:20px}
            h1{font-size:28px}
            .card-logo{width:110px;height:110px}
            .card-header{gap:0;margin-bottom:24px}
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card">
            <div class="card-header">
                <div class="card-logo"><img src="../../assets/HOME/image-removebg-preview.png" alt="ANDISON Logo"></div>
                <h1>Login</h1>
            </div>
            
            <form method="post" action="login.php?next=<?php echo urlencode($next); ?>" novalidate>
                <div class="form-group">
                    <label for="username">Email ID</label>
                    <div class="input-wrapper">
                        <input id="username" name="username" type="text" autocomplete="username" placeholder="Enter your email" required aria-label="Email ID" aria-required="true" <?php echo $is_locked ? 'disabled' : ''; ?>>
                        <span class="input-icon">✉</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Enter password" required aria-label="Password" aria-required="true" <?php echo $is_locked ? 'disabled' : ''; ?>>
                        <button class="password-toggle" type="button" id="togglePassword" aria-label="Show or hide password" title="Show password" <?php echo $is_locked ? 'disabled' : ''; ?>><i class="far fa-eye"></i></button>
                    </div>
                </div>

                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['login_csrf_token'] ?? ''); ?>">

                <div class="form-options">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="remember" name="remember" value="1">
                        <label for="remember" class="checkbox-label">Remember me</label>
                    </div>
                    <a href="#" class="forgot-link">Forgot Password?</a>
                </div>

                <button class="btn" type="submit" id="submitBtn" <?php echo $is_locked ? 'disabled' : ''; ?>>
                    <span id="btnText">Login</span>
                </button>

                <?php if ($error): ?>
                    <div class="error" role="alert"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <div class="form-footer">
                    Don't have an account? <a href="../home.php">Back to site</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Password visibility toggle
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        toggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            const icon = toggleBtn.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
            toggleBtn.title = isPassword ? 'Hide password' : 'Show password';
            passwordInput.focus();
        });

        // Form submission handler
        const form = document.querySelector('form');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        
        form.addEventListener('submit', () => {
            submitBtn.disabled = true;
            btnText.innerHTML = '<span class="btn-spinner"></span>';
        });

        // Keyboard support - Tab from username to password, Enter from password to submit
        document.getElementById('username').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('password').focus();
            }
        });

        document.getElementById('password').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                form.submit();
            }
        });

        // Auto-focus username on load
        document.getElementById('username').focus();
    </script>
</body>
</html>



