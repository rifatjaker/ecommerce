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
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$alt_name = isset($_POST['alt_name']) ? trim($_POST['alt_name']) : '';

if ($name === '' || $alt_name === '') {
  sendErrorResponse('Name and alternative name are required', 422);
}

if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
  sendErrorResponse('Logo file is required', 422);
}

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

try {
  $stmt = $pdo->prepare('INSERT INTO clubs (`name`, `logo`, `alt_name`) VALUES (?, ?, ?)');
  $stmt->execute([$name, $logoPath, $alt_name]);
  $id = (int)$pdo->lastInsertId();
  sendSuccessResponse(['id' => $id], 'Club created successfully');
} catch (Throwable $e) {
  if (DEBUG_MODE) {
    sendErrorResponse('Insert failed: ' . $e->getMessage(), 500);
  } else {
    sendErrorResponse('Insert failed', 500);
  }
}
?>
