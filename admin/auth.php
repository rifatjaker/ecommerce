<?php
session_start();

// Check if user is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Check if session is still valid (30 minutes timeout)
function isSessionValid() {
    if (!isset($_SESSION['login_time'])) {
        return false;
    }
    
    $timeout = 30 * 60; // 30 minutes
    return (time() - $_SESSION['login_time']) < $timeout;
}

// Require authentication for admin pages
function requireAdminAuth() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit;
    }
    
    if (!isSessionValid()) {
        // Session expired
        session_destroy();
        header('Location: login.php?expired=1');
        exit;
    }
    
    // Update session time to prevent timeout
    $_SESSION['login_time'] = time();
}

// Get current admin info
function getCurrentAdmin() {
    if (!isAdminLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['admin_id'] ?? null,
        'username' => $_SESSION['admin_username'] ?? null,
        'full_name' => $_SESSION['admin_full_name'] ?? null,
        'email' => $_SESSION['admin_email'] ?? null,
        'login_time' => $_SESSION['login_time'] ?? null
    ];
}

// Log admin action
function logAdminAction($action, $details = '') {
    if (!isAdminLoggedIn()) {
        return false;
    }
    
    try {
        require_once dirname(__DIR__) . '/config.php';
        $pdo = getDBConnection();
        
        if ($pdo) {
            $stmt = $pdo->prepare('INSERT INTO admin_activity_logs (admin_id, action, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([
                $_SESSION['admin_id'],
                $action,
                $details,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
            return true;
        }
    } catch (Throwable $e) {
        if (DEBUG_MODE) {
            error_log('Failed to log admin action: ' . $e->getMessage());
        }
    }
    
    return false;
}

// Check admin permissions (for future use)
function hasPermission($permission) {
    // For now, all logged-in admins have all permissions
    // This can be extended later with role-based permissions
    return isAdminLoggedIn();
}
?>
