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
  // Fetch clubs
  $stmt = $pdo->query("SELECT * FROM clubs ORDER BY id ASC");
  $clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  sendSuccessResponse($clubs, 'Clubs retrieved successfully');
} catch (Throwable $e) {
  if (DEBUG_MODE) {
    sendErrorResponse('Failed to fetch clubs: ' . $e->getMessage(), 500);
  } else {
    sendErrorResponse('Failed to fetch clubs', 500);
  }
}
?>