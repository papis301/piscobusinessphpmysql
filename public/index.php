<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db.php';
require_once 'functions.php';

/* ------------------------------
   1️⃣ Charger catégories
--------------------------------*/
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")
                  ->fetchAll(PDO::FETCH_ASSOC);

/* ------------------------------
   2️⃣ Filtre + recherche
--------------------------------*/
$where   = [];
$params  = [];

// Filtre catégorie
if (!empty($_GET['category_id'])) {
    $where[] = "p.category_id = :category_id";
    $params['category_id'] = $_GET['category_id'];
}

// Recherche mot-clé
if (!empty($_GET['search'])) {
    $where[] = "(p.name LIKE :search OR p.description LIKE :search)";
    $params['search'] = "%" . $_GET['search'] . "%";
}

// Construire requête finale
$sql = "
    SELECT p.*, (
        SELECT image FROM product_images 
        WHERE product_id = p.id 
        ORDER BY id ASC LIMIT 1
    ) AS main_image
    FROM products p
";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Pisco Business - Boutique</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap + Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { background-color: #f8f9fa; font-family: 'Poppins', sans-serif; }
        .product-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all .3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        .product-card img {
            height: 220px;
            object-fit: cover;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        .product-title { font-size: 1.1rem; font-weight: 600; color: #333; }
        .product-price { font-size: 1.2rem; color: #198754; font-weight: bold; }
        .btn-buy { background-color: #ffc107; border: none; color: #000; font-weight: 600; }
        .btn-buy:hover { background-color: #e0a800; }
    </style>
</head>
<body>

<?php include 'partials/header.php'; ?>

<!-- 🔎 BARRE DE RECHERCHE + FILTRE CATÉGORIE -->
<div class="container mt-4">
    <form method="GET" class="row g-3 p-3 bg-white shadow-sm rounded">

        <!-- Champ recherche -->
        <div class="col-md-6">
            <input type="text" name="search" class="form-control"
                   placeholder="Rechercher un produit..."
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        </div>

        <!-- Sélection catégorie -->
        <div class="col-md-4">
            <select name="category_id" class="form-control">
                <option value="">Toutes les catégories</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id'] ?>"
                        <?= (isset($_GET['category_id']) && $_GET['category_id'] == $c['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-success w-100">Filtrer</button>
        </div>
    </form>
</div>

<!-- 🖼️ SLIDER BOOTSTRAP (après le header) -->
<div id="carouselExampleIndicators" class="carousel slide mt-3" data-bs-ride="carousel">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
  </div>
  <div class="carousel-inner rounded">
    <div class="carousel-item active">
      <img src="uploads/slider1.png" class="d-block w-100" alt="Slide 1">
    </div>
    <div class="carousel-item">
      <img src="uploads/slider2.png" class="d-block w-100" alt="Slide 2">
    </div>
    <div class="carousel-item">
      <img src="uploads/slider3.jpg" class="d-block w-100" alt="Slide 3">
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Précédent</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Suivant</span>
  </button>
</div>

<!-- 🛍️ LISTE DES PRODUITS -->
<div class="container my-5">
  <div class="row g-4">

    <?php if(count($products) > 0): ?>

      <?php foreach($products as $p): ?>
        <div class="col-lg-3 col-md-4 col-sm-6">
          <div class="product-card">
            <a href="product.php?id=<?= $p['id'] ?>" class="text-decoration-none text-dark">

              <img src="<?= htmlspecialchars($p['main_image'] ?: 'uploads/default.jpg') ?>" class="w-100">

              <div class="p-3">
                <h6 class="product-title mb-1"><?= htmlspecialchars($p['name']) ?></h6>
                <p class="product-price mb-2">
                    <?= number_format($p['price'], 0, ',', ' ') ?> F CFA
                </p>
                <a href="product.php?id=<?= $p['id'] ?>" class="btn btn-buy w-100">Voir le produit</a>
              </div>

            </a>
          </div>
        </div>
      <?php endforeach; ?>

    <?php else: ?>
      <p class="text-center text-muted">Aucun produit trouvé.</p>
    <?php endif; ?>

  </div>
</div>

<?php include 'partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
