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
$link = isset($_POST['link']) ? trim($_POST['link']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';

if ($name === '' || $link === '' || $description === '') {
  sendErrorResponse('Name, link and description are required', 422);
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
  sendErrorResponse('Image file is required', 422);
}

// Validate and move upload
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
finfo_close($finfo);

if (!in_array($mime, ALLOWED_IMAGE_TYPES)) {
  sendErrorResponse('Only JPG and PNG allowed', 415);
}

if ($_FILES['image']['size'] > MAX_FILE_SIZE) {
  sendErrorResponse('File size too large', 413);
}

$ext = ($mime === 'image/jpeg') ? 'jpg' : 'png';

// Create upload directory if it doesn't exist
if (!is_dir(UPLOAD_DIR)) {
  @mkdir(UPLOAD_DIR, 0755, true);
}

$safeBase = preg_replace('/[^a-zA-Z0-9_-]+/', '-', strtolower($name));
$fileName = $safeBase . '-' . time() . '.' . $ext;
$destination = UPLOAD_DIR . DIRECTORY_SEPARATOR . $fileName;

if (!move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
  sendErrorResponse('Failed to save image', 500);
}

$imagePath = 'category/' . $fileName;

try {
  $stmt = $pdo->prepare('INSERT INTO categories (`name`, `image`, `link`, `description`) VALUES (?, ?, ?, ?)');
  $stmt->execute([$name, $imagePath, $link, $description]);
  $id = (int)$pdo->lastInsertId();
  sendSuccessResponse(['id' => $id], 'Category created successfully');
} catch (Throwable $e) {
  if (DEBUG_MODE) {
    sendErrorResponse('Insert failed: ' . $e->getMessage(), 500);
  } else {
    sendErrorResponse('Insert failed', 500);
  }
}
?>


