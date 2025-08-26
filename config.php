<?php
// Global configuration file for Eyesome Sports E-commerce

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'ihrbdtop_shahedsir_ecom');
define('DB_USER', 'ihrbdtop_shahedsir_ecom');
define('DB_PASS', 'kjErTzngXLdNqJLhnD6g');

// API endpoints
define('API_BASE_URL', 'https://ihr1.bd24.top/shahedsir_api_endpoint');
define('API_CATEGORIES', API_BASE_URL . '/get_categories.php');
define('API_CREATE_CATEGORY', API_BASE_URL . '/create_category.php');
define('API_CLUBS', API_BASE_URL . '/get_clubs.php');
define('API_HOMEPAGE_DATA', API_BASE_URL . '/get_homepage_data.php');

// File upload settings
define('UPLOAD_DIR', __DIR__ . '/category');
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Site settings
define('SITE_NAME', 'Eyesome Sports');
define('SITE_URL', 'https://ihr1.bd24.top/shahedsir');
define('ADMIN_EMAIL', 'eyesomebd@gmail.com');

// CORS settings
define('ALLOWED_ORIGINS', ['*']); // For production, specify actual domains

// Error reporting (set to false in production)
define('DEBUG_MODE', true);

// Database connection function
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        if (DEBUG_MODE) {
            throw $e;
        }
        return false;
    }
}

// CORS headers function
function setCORSHeaders() {
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Content-Type: application/json');
}

// Error response function
function sendErrorResponse($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

// Success response function
function sendSuccessResponse($data = null, $message = 'Success') {
    echo json_encode(['success' => true, 'message' => $message, 'data' => $data]);
    exit;
}
?>
