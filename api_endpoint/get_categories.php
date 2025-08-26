<?php
// Include global configuration
require_once dirname(__DIR__) . '/config.php';

// Set CORS headers
setCORSHeaders();

// Get database connection
$pdo = getDBConnection();
if (!$pdo) {
  sendErrorResponse('Database connection failed', 500);
}

try {
  // Fetch categories
  $stmt = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
  $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  sendSuccessResponse($categories, 'Categories retrieved successfully');
} catch (Throwable $e) {
  if (DEBUG_MODE) {
    sendErrorResponse('Failed to fetch categories: ' . $e->getMessage(), 500);
  } else {
    sendErrorResponse('Failed to fetch categories', 500);
  }
}
?>