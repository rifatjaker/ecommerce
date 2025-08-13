<?php
// Allow from anywhere (or set to a specific origin)
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET');
// Allow specific headers
header('Content-Type: application/json');

// DB connection
$pdo = new PDO("mysql:host=localhost;dbname=ihrbdtop_shahedsir_ecom;charset=utf8", "ihrbdtop_shahedsir_ecom", "kjErTzngXLdNqJLhnD6g");

// Get clubs
$stmt = $pdo->query("SELECT id, name, logo, alt_name FROM clubs ORDER BY id ASC");
$clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories
$stmt = $pdo->query("SELECT id, name, image, link, description FROM categories ORDER BY id ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Send combined JSON
echo json_encode([
    "clubs" => $clubs,
    "categories" => $categories
]);
?>