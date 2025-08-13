<?php
// Allow from anywhere (or set to a specific origin)
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET');
// Allow specific headers
header('Content-Type: application/json');

// DB connection
$pdo = new PDO("mysql:host=localhost;dbname=ihrbdtop_shahedsir_ecom;charset=utf8", "ihrbdtop_shahedsir_ecom", "kjErTzngXLdNqJLhnD6g");

// Fetch categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($categories);
?>