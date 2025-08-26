<?php
// Include global configuration
require_once dirname(__DIR__) . '/config.php';

// Get database connection
$pdo = getDBConnection();
if (!$pdo) {
    die('Database connection failed');
}

try {
    // Create admin_users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `admin_users` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `username` varchar(50) NOT NULL,
            `password_hash` varchar(255) NOT NULL,
            `full_name` varchar(100) NOT NULL,
            `email` varchar(100) NOT NULL,
            `role` enum('super_admin','admin','moderator') DEFAULT 'admin',
            `status` enum('active','inactive','suspended') DEFAULT 'active',
            `last_login` timestamp NULL DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `username` (`username`),
            UNIQUE KEY `email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Admin users table created successfully\n";

    // Create admin_login_logs table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `admin_login_logs` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `admin_id` int(11) NOT NULL,
            `ip_address` varchar(45) NOT NULL,
            `user_agent` text DEFAULT NULL,
            `status` enum('success','failed') NOT NULL,
            `created_at` timestamp NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `admin_id` (`admin_id`),
            KEY `created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Admin login logs table created successfully\n";

    // Create admin_activity_logs table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `admin_activity_logs` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `admin_id` int(11) NOT NULL,
            `action` varchar(100) NOT NULL,
            `details` text DEFAULT NULL,
            `ip_address` varchar(45) NOT NULL,
            `user_agent` text DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `admin_id` (`admin_id`),
            KEY `action` (`action`),
            KEY `created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ Admin activity logs table created successfully\n";

    // Check if admin user already exists
    $stmt = $pdo->prepare('SELECT id FROM admin_users WHERE username = ?');
    $stmt->execute(['admin']);
    
    if (!$stmt->fetch()) {
        // Create default admin user
        $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('
            INSERT INTO admin_users (username, password_hash, full_name, email, role, status) 
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            'admin',
            $password_hash,
            'System Administrator',
            'admin@eyesome.com',
            'super_admin',
            'active'
        ]);
        echo "✓ Default admin user created successfully\n";
        echo "Username: admin\n";
        echo "Password: admin123\n";
    } else {
        echo "✓ Admin user already exists\n";
    }

    echo "\n🎉 Admin system setup completed successfully!\n";
    echo "You can now login at: /admin/login.php\n";

} catch (Throwable $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
