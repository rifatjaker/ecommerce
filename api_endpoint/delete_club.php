<?php
// Include global configuration
require_once dirname(__DIR__) . '/config.php';

// Set CORS headers
setCORSHeaders();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(204);
  exit;
}

// Get database connection
$pdo = getDBConnection();
if (!$pdo) {
  sendErrorResponse('Database connection failed', 500);
}

// Parse JSON body
$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
  sendErrorResponse('Invalid JSON body', 400);
}

$id = isset($input['id']) ? (int)$input['id'] : 0;

if ($id <= 0) {
  sendErrorResponse('Valid club ID is required', 422);
}

// Check if club exists and get logo path
try {
  $stmt = $pdo->prepare('SELECT * FROM clubs WHERE id = ?');
  $stmt->execute([$id]);
  $club = $stmt->fetch();
  
  if (!$club) {
    sendErrorResponse('Club not found', 404);
  }
} catch (Throwable $e) {
  sendErrorResponse('Failed to check club existence', 500);
}

// Delete the club from database
try {
  $stmt = $pdo->prepare('DELETE FROM clubs WHERE id = ?');
  $stmt->execute([$id]);
  
  if ($stmt->rowCount() > 0) {
    // Delete logo file if it exists
    if ($club['logo']) {
      $logoPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . $club['logo'];
      if (file_exists($logoPath)) {
        @unlink($logoPath);
      }
    }
    
    sendSuccessResponse(['id' => $id], 'Club deleted successfully');
  } else {
    sendErrorResponse('Failed to delete club', 500);
  }
} catch (Throwable $e) {
  if (DEBUG_MODE) {
    sendErrorResponse('Delete failed: ' . $e->getMessage(), 500);
  } else {
    sendErrorResponse('Delete failed', 500);
  }
}
?>
