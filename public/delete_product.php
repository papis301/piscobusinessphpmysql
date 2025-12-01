<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$user_id = $user['id'];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Vérifier que le produit appartient bien à l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);
$product = $stmt->fetch();

if (!$product) {
    // Tentative d'accès illégal
    header("Location: dashboard.php");
    exit;
}

// Supprimer images du produit
$stmt = $pdo->prepare("SELECT image FROM product_images WHERE product_id = ?");
$stmt->execute([$id]);
$images = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($images as $img) {
    $file = __DIR__ . '/uploads/' . $img;
    if (file_exists($file)) unlink($file);
}

$pdo->prepare("DELETE FROM product_images WHERE product_id=?")->execute([$id]);

// Supprimer le produit
$pdo->prepare("DELETE FROM products WHERE id=?")->execute([$id]);

header("Location: dashboard.php");
exit;
