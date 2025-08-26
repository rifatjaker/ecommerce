<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

// Include global configuration
require_once dirname(__DIR__) . '/config.php';

$error_message = '';
$success_message = '';

// Handle URL parameters
if (isset($_GET['expired'])) {
    $error_message = 'Your session has expired. Please login again.';
} elseif (isset($_GET['logout'])) {
    $success_message = 'You have been logged out successfully.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($username) || empty($password)) {
        $error_message = 'Please enter both username and password.';
    } else {
        // Get database connection
        $pdo = getDBConnection();
        if ($pdo) {
            try {
                // Check admin credentials
                $stmt = $pdo->prepare('SELECT id, username, password_hash, full_name, email, last_login FROM admin_users WHERE username = ? AND status = "active"');
                $stmt->execute([$username]);
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($admin && password_verify($password, $admin['password_hash'])) {
                    // Login successful
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_full_name'] = $admin['full_name'];
                    $_SESSION['admin_email'] = $admin['email'];
                    $_SESSION['login_time'] = time();
                    
                    // Update last login time
                    $stmt = $pdo->prepare('UPDATE admin_users SET last_login = NOW() WHERE id = ?');
                    $stmt->execute([$admin['id']]);
                    
                    // Log successful login
                    $stmt = $pdo->prepare('INSERT INTO admin_login_logs (admin_id, ip_address, user_agent, status) VALUES (?, ?, ?, "success")');
                    $stmt->execute([$admin['id'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']]);
                    
                    header('Location: index.php');
                    exit;
                } else {
                    // Login failed
                    $error_message = 'Invalid username or password.';
                    
                    // Log failed login attempt
                    if ($admin) {
                        $stmt = $pdo->prepare('INSERT INTO admin_login_logs (admin_id, ip_address, user_agent, status) VALUES (?, ?, ?, "failed")');
                        $stmt->execute([$admin['id'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']]);
                    }
                }
            } catch (Throwable $e) {
                $error_message = 'Login failed. Please try again.';
                if (DEBUG_MODE) {
                    error_log('Admin login error: ' . $e->getMessage());
                }
            }
        } else {
            $error_message = 'System error. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Eyesome Sports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .login-header {
            background: linear-gradient(45deg, #1e3c72, #2a5298);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 2rem;
            text-align: center;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #1e3c72;
            box-shadow: 0 0 0 0.2rem rgba(30, 60, 114, 0.25);
        }
        .btn-login {
            background: linear-gradient(45deg, #1e3c72, #2a5298);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
        }
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 10px 0 0 10px;
        }
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
    </style>
    <link rel="icon" href="../favicon.ico">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-card">
                    <div class="login-header">
                        <img src="../favicon.ico" alt="Logo" style="height: 50px; margin-bottom: 1rem;">
                        <h4 class="mb-0">Admin Login</h4>
                        <p class="mb-0 opacity-75">Eyesome Sports</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success_message): ?>
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success_message); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" novalidate>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" id="username" name="username" class="form-control" 
                                           placeholder="Enter username" required 
                                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" id="password" name="password" class="form-control" 
                                           placeholder="Enter password" required>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-login">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <a href="../index.html" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>Back to Website
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <small class="text-white opacity-75">
                        &copy; <?php echo date('Y'); ?> Eyesome Sports. All rights reserved.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-focus on username field
        document.getElementById('username').focus();
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            
            if (!username || !password) {
                e.preventDefault();
                alert('Please fill in all fields.');
                return false;
            }
        });
    </script>
</body>
</html>
