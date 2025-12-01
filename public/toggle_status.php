<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// Vérifie si admin connecté
if (!userIsLogged() || !currentUser()['is_admin']) {
    header("Location: login_admin.php");
    exit;
}

// Vérifie si ID transmis
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$product_id = intval($_GET['id']);

// Récupère le statut actuel
$stmt = $pdo->prepare("SELECT status FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: products.php");
    exit;
}

// Nouveau statut
$new_status = ($product['status'] === 'active') ? 'inactive' : 'active';

// Mise à jour
$stmt = $pdo->prepare("UPDATE products SET status = ? WHERE id = ?");
$stmt->execute([$new_status, $product_id]);

// Retour à la page produits
header("Location: products.php?status_changed=1");
exit;
