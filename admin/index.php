<?php
session_start();

// Vérification que l'utilisateur est connecté et admin
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
if (!$user['is_admin']) {
    header("Location: ../index.php");
    exit;
}

require_once '../db.php';

// Compter les produits
$productCount = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'] ?? 0;

// Compter les catégories
$categoryCount = $conn->query("SELECT COUNT(*) AS total FROM categories")->fetch_assoc()['total'] ?? 0;

// Compter les utilisateurs
$userCount = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'] ?? 0;

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord - Admin Pisco</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold text-white" href="index.php">Pisco Business</a>
    <div>
      <a href="products.php" class="btn btn-light btn-sm me-2">Produits</a>
      <a href="categories.php" class="btn btn-light btn-sm me-2">Catégories</a>
      <a href="logout.php" class="btn btn-danger btn-sm">Déconnexion</a>
    </div>
  </div>
</nav>

<div class="container py-5">
  <h2 class="mb-4">Bienvenue, <?= htmlspecialchars($user['name']) ?> 👋</h2>

  <div class="row g-4">
    <div class="col-md-4">
      <div class="card shadow border-0">
        <div class="card-body text-center">
          <h5 class="card-title text-success">Produits</h5>
          <p class="display-6 fw-bold"><?= $productCount ?></p>
          <a href="products.php" class="btn btn-outline-success btn-sm">Gérer</a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow border-0">
        <div class="card-body text-center">
          <h5 class="card-title text-success">Catégories</h5>
          <p class="display-6 fw-bold"><?= $categoryCount ?></p>
          <a href="categories.php" class="btn btn-outline-success btn-sm">Gérer</a>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card shadow border-0">
        <div class="card-body text-center">
          <h5 class="card-title text-success">Utilisateurs</h5>
          <p class="display-6 fw-bold"><?= $userCount ?></p>
          <a href="users.php" class="btn btn-outline-success btn-sm">Voir</a>
        </div>
      </div>
    </div>
  </div>
</div>

<footer class="text-center mt-5 text-muted small">
  &copy; <?= date('Y') ?> Pisco Business - Tableau de bord Admin
</footer>

</body>
</html>
