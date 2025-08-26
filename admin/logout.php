<?php
session_start();

// Log the logout action before destroying session
if (isset($_SESSION['admin_id'])) {
    try {
        require_once dirname(__DIR__) . '/config.php';
        $pdo = getDBConnection();
        
        if ($pdo) {
            $stmt = $pdo->prepare('INSERT INTO admin_activity_logs (admin_id, action, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([
                $_SESSION['admin_id'],
                'logout',
                'Admin logged out',
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
        }
    } catch (Throwable $e) {
        // Ignore logging errors during logout
    }
}

// Destroy all session data
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session
session_destroy();

// Redirect to login page
header('Location: login.php?logout=1');
exit;
?>
