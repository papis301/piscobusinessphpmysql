<?php
//session_start();
require_once __DIR__ . '/db.php';

/**
 * Récupère tous les produits avec leur catégorie
 */
function getAllProducts() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT p.*, c.name AS category 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.created_at DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Récupère un produit spécifique par son ID
 */
function getProductById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Vérifie si un utilisateur est connecté
 */
function userIsLogged() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Retourne les infos de l'utilisateur connecté
 */
function currentUser() {
    global $pdo;
    if (!empty($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT id, name, phone, is_admin FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    return null;
}


/**
 * Vérifie si l'utilisateur actuel est un admin
 */
function isAdmin() {
    $user = currentUser();
    return $user && $user['is_admin'] == 1;
}
?>
