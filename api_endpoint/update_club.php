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

// Expect multipart/form-data
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$alt_name = isset($_POST['alt_name']) ? trim($_POST['alt_name']) : '';

if ($id <= 0 || $name === '' || $alt_name === '') {
  sendErrorResponse('Valid ID, name and alternative name are required', 422);
}

// Check if club exists
try {
  $stmt = $pdo->prepare('SELECT * FROM clubs WHERE id = ?');
  $stmt->execute([$id]);
  $existingClub = $stmt->fetch();
  
  if (!$existingClub) {
    sendErrorResponse('Club not found', 404);
  }
} catch (Throwable $e) {
  sendErrorResponse('Failed to check club existence', 500);
}

$logoPath = $existingClub['logo']; // Keep existing logo by default

// Handle logo upload if provided
if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
  // Validate and move upload
  $finfo = finfo_open(FILEINFO_MIME_TYPE);
  $mime = finfo_file($finfo, $_FILES['logo']['tmp_name']);
  finfo_close($finfo);

  $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
  if (!in_array($mime, $allowedMime)) {
    sendErrorResponse('Only JPG, PNG, and WebP allowed', 415);
  }

  if ($_FILES['logo']['size'] > MAX_FILE_SIZE) {
    sendErrorResponse('File size too large', 413);
  }

  $ext = ($mime === 'image/jpeg') ? 'jpg' : (($mime === 'image/png') ? 'png' : 'webp');

  // Create upload directory if it doesn't exist
  $uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'clubs_logo';
  if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
  }

  $safeBase = preg_replace('/[^a-zA-Z0-9_-]+/', '-', strtolower($name));
  $fileName = $safeBase . '-' . time() . '.' . $ext;
  $destination = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

  if (!move_uploaded_file($_FILES['logo']['tmp_name'], $destination)) {
    sendErrorResponse('Failed to save logo', 500);
  }

  $logoPath = 'clubs_logo/' . $fileName;

  // Delete old logo file if it exists and is different
  if ($existingClub['logo'] && $existingClub['logo'] !== $logoPath) {
    $oldLogoPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . $existingClub['logo'];
    if (file_exists($oldLogoPath)) {
      @unlink($oldLogoPath);
    }
  }
}

try {
  $stmt = $pdo->prepare('UPDATE clubs SET `name` = ?, `logo` = ?, `alt_name` = ? WHERE id = ?');
  $stmt->execute([$name, $logoPath, $alt_name, $id]);
  
  if ($stmt->rowCount() > 0) {
    sendSuccessResponse(['id' => $id], 'Club updated successfully');
  } else {
    sendErrorResponse('No changes made to club', 400);
  }
} catch (Throwable $e) {
  if (DEBUG_MODE) {
    sendErrorResponse('Update failed: ' . $e->getMessage(), 500);
  } else {
    sendErrorResponse('Update failed', 500);
  }
}
?>
