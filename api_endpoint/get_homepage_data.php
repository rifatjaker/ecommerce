<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json');

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=ihrbdtop_shahedsir_ecom;charset=utf8",
        "ihrbdtop_shahedsir_ecom",
        "kjErTzngXLdNqJLhnD6g",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );


    // --- Clubs ---
    $stmt = $pdo->query("SELECT id, name, logo, alt_name FROM clubs ORDER BY id ASC");
    $clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // --- Categories ---
    $stmt = $pdo->query("SELECT id, name, image, link, description FROM categories ORDER BY id ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // --- New Arrivals with gallery ---
    $stmt = $pdo->query("
        SELECT p.id, p.name, p.price, p.description, pi.image_url, pi.is_primary
        FROM products p
        LEFT JOIN product_images pi ON p.id = pi.product_id
        WHERE p.status='active'
        ORDER BY p.created_at DESC, pi.id ASC
        LIMIT 20
    ");

    $products = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];

        if (!isset($products[$id])) {
            $products[$id] = [
                'id' => $id,
                'name' => $row['name'],
                'price' => $row['price'],
                'description' => $row['description'],
                'image_url' => '', // primary image
                'gallery' => []
            ];
        }

        // Always push to gallery if image exists
        if ($row['image_url']) {
            $products[$id]['gallery'][] = $row['image_url'];

            // Set primary image (first or marked primary)
            if ($row['is_primary'] == 1 || $products[$id]['image_url'] == '') {
                $products[$id]['image_url'] = $row['image_url'];
            }
        }
    }

    // Reindex array
    $new_arrivals = array_values($products);


    // --- Product List with gallery ---
    $stmt = $pdo->query("
        SELECT p.id, p.name, p.price, p.description, c.name AS category, pi.image_url, pi.is_primary
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN product_images pi ON p.id = pi.product_id
        WHERE p.status='active'
        ORDER BY p.created_at DESC, pi.id ASC
        LIMIT 100
    ");

    $productList = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];

        if (!isset($productList[$id])) {
            $productList[$id] = [
                'id' => $id,
                'name' => $row['name'],
                'price' => $row['price'],
                'description' => $row['description'],
                'category' => $row['category'],
                'image_url' => '', // primary image
                'gallery' => []
            ];
        }

        // Always push to gallery if image exists
        if ($row['image_url']) {
            $productList[$id]['gallery'][] = $row['image_url'];

            // Set primary image (first or marked primary)
            if ($row['is_primary'] == 1 || $productList[$id]['image_url'] == '') {
                $productList[$id]['image_url'] = $row['image_url'];
            }
        }
    }

    // Reindex array
    $product_list = array_values($productList);

    
    // --- JSON Response ---
    echo json_encode([
        "clubs" => $clubs,
        "categories" => $categories,
        "new_arrivals" => $new_arrivals,
        "product_list" => $product_list
    ]);

} catch (Exception $e) {
    echo json_encode([
        "error" => true,
        "message" => $e->getMessage()
    ]);
}
?>