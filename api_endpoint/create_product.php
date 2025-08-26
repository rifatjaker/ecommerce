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
$category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
$price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
$status = isset($_POST['status']) ? trim($_POST['status']) : 'active';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';

if ($name === '' || $category_id <= 0 || $price <= 0 || $description === '') {
  sendErrorResponse('Name, category, price, and description are required', 422);
}

// Validate status
if (!in_array($status, ['active', 'inactive'])) {
  sendErrorResponse('Invalid status value', 422);
}

// Check if category exists
try {
  $stmt = $pdo->prepare('SELECT id FROM categories WHERE id = ?');
  $stmt->execute([$category_id]);
  if (!$stmt->fetch()) {
    sendErrorResponse('Category not found', 404);
  }
} catch (Throwable $e) {
  sendErrorResponse('Failed to validate category', 500);
}

// Handle image uploads
$uploadedImages = [];
if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
  $uploadDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'products';
  if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0755, true);
  }

  $fileCount = count($_FILES['images']['name']);
  for ($i = 0; $i < $fileCount; $i++) {
    if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
      $file = [
        'name' => $_FILES['images']['name'][$i],
        'type' => $_FILES['images']['type'][$i],
        'tmp_name' => $_FILES['images']['tmp_name'][$i],
        'size' => $_FILES['images']['size'][$i]
      ];

      // Validate file
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mime = finfo_file($finfo, $file['tmp_name']);
      finfo_close($finfo);

      $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
      if (!in_array($mime, $allowedMime)) {
        sendErrorResponse('Only JPG, PNG, and WebP allowed', 415);
      }

      if ($file['size'] > MAX_FILE_SIZE) {
        sendErrorResponse('File size too large', 413);
      }

      $ext = ($mime === 'image/jpeg') ? 'jpg' : (($mime === 'image/png') ? 'png' : 'webp');
      $safeBase = preg_replace('/[^a-zA-Z0-9_-]+/', '-', strtolower($name));
      $fileName = $safeBase . '-' . time() . '-' . $i . '.' . $ext;
      $destination = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

      if (move_uploaded_file($file['tmp_name'], $destination)) {
        $uploadedImages[] = 'products/' . $fileName;
      } else {
        sendErrorResponse('Failed to save image', 500);
      }
    }
  }
}

if (empty($uploadedImages)) {
  sendErrorResponse('At least one product image is required', 422);
}

try {
  $pdo->beginTransaction();

  // Insert product
  $stmt = $pdo->prepare('INSERT INTO products (`category_id`, `name`, `description`, `price`, `status`) VALUES (?, ?, ?, ?, ?)');
  $stmt->execute([$category_id, $name, $description, $price, $status]);
  $productId = (int)$pdo->lastInsertId();

  // Insert product images
  $stmt = $pdo->prepare('INSERT INTO product_images (`product_id`, `image_url`, `is_primary`) VALUES (?, ?, ?)');
  foreach ($uploadedImages as $index => $imagePath) {
    $isPrimary = ($index === 0) ? 1 : 0; // First image is primary
    $stmt->execute([$productId, $imagePath, $isPrimary]);
  }

  $pdo->commit();
  sendSuccessResponse(['id' => $productId], 'Product created successfully');
} catch (Throwable $e) {
  $pdo->rollBack();
  if (DEBUG_MODE) {
    sendErrorResponse('Insert failed: ' . $e->getMessage(), 500);
  } else {
    sendErrorResponse('Insert failed', 500);
  }
}
?>
