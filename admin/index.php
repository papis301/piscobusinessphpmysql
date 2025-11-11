<?php
session_start();
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/functions.php';

// Récupère l'utilisateur connecté
$user = currentUser();

// Si pas connecté ou pas admin → redirection vers login
if (!$user || !$user['is_admin']) {
    header("Location: login_admin.php");
    exit;
}

// Récupérer les stats
try {
    $product_count  = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $category_count = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    $user_count     = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
} catch (PDOException $e) {
    die("Erreur lors de la récupération des données : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord - Admin Pisco Business</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Bienvenue, <?= htmlspecialchars($user['name'] ?? 'Invité') ?> 👋</h1>
        <a href="logout.php" class="btn btn-danger">Déconnexion</a>
    </div>

    <h2>Tableau de bord</h2>
    <div class="row mt-3">
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Produits</h5>
                    <p class="card-text fs-3"><?= $product_count ?></p>
                    <a href="products.php" class="btn btn-success btn-sm">Gérer</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Catégories</h5>
                    <p class="card-text fs-3"><?= $category_count ?></p>
                    <a href="categories.php" class="btn btn-success btn-sm">Gérer</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Utilisateurs</h5>
                    <p class="card-text fs-3"><?= $user_count ?></p>
                    <a href="#" class="btn btn-success btn-sm">Gérer</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
