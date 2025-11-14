<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';

$user = currentUser();
if (!$user || !$user['is_admin']) {
    header('Location: index.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Supprimer image si existante
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id=?");
    $stmt->execute([$id]);
    $p = $stmt->fetch();
    if ($p && $p['image']) {
        $file = __DIR__ . 'uploads/' . $p['image'];
        if (file_exists($file)) unlink($file);
    }

    $pdo->prepare("DELETE FROM products WHERE id=?")->execute([$id]);
}

header('Location: products.php');
exit;
