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
  // Fetch products with category names and images
  $stmt = $pdo->query("
    SELECT 
      p.id,
      p.category_id,
      p.name,
      p.description,
      p.price,
      p.status,
      p.created_at,
      c.name as category_name,
      GROUP_CONCAT(
        JSON_OBJECT(
          'id', pi.id,
          'image_url', pi.image_url,
          'is_primary', pi.is_primary
        ) ORDER BY pi.is_primary DESC, pi.id ASC
      ) as images_json
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN product_images pi ON p.id = pi.product_id
    GROUP BY p.id
    ORDER BY p.id DESC
  ");
  
  $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  // Process images for each product
  foreach ($products as &$product) {
    $product['images'] = [];
    $product['primary_image'] = null;
    
    if ($product['images_json']) {
      $images = explode(',', $product['images_json']);
      foreach ($images as $imageJson) {
        $imageData = json_decode($imageJson, true);
        if ($imageData) {
          $product['images'][] = $imageData;
          if ($imageData['is_primary']) {
            $product['primary_image'] = $imageData['image_url'];
          }
        }
      }
    }
    
    // Remove the raw JSON field
    unset($product['images_json']);
  }
  
  sendSuccessResponse($products, 'Products retrieved successfully');
} catch (Throwable $e) {
  if (DEBUG_MODE) {
    sendErrorResponse('Failed to fetch products: ' . $e->getMessage(), 500);
  } else {
    sendErrorResponse('Failed to fetch products', 500);
  }
}
?>
