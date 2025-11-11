<?php
require_once '../inc/db.php';
require_once '../inc/functions.php';

// Récupérer les catégories
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Gestion de la recherche et filtre catégorie
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;

$query = "SELECT p.id, p.name, p.price,
         (SELECT image FROM product_images WHERE product_id = p.id ORDER BY id ASC LIMIT 1) AS first_image
         FROM products p
         WHERE 1=1";
$params = [];

if($search !== ''){
    $query .= " AND p.name LIKE ?";
    $params[] = "%$search%";
}
if($category_id > 0){
    $query .= " AND p.category_id = ?";
    $params[] = $category_id;
}
$query .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Pisco Business - Boutique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .navbar { background-color: #388E3C; }
        .navbar-brand { color: #fff; font-weight: bold; font-size: 1.5rem; }
        .card { border: none; border-radius: 10px; overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; position: relative; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
        .card-img-overlay { display: flex; justify-content: center; align-items: center; position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); opacity: 0; transition: opacity 0.3s; }
        .card:hover .card-img-overlay { opacity: 1; }
        .card-title { font-size: 1.1rem; font-weight: bold; }
        .price { color: #388E3C; font-weight: bold; font-size: 1.1rem; }
        .btn-details { background-color: #388E3C; color: #fff; }
        .btn-details:hover { background-color: #2e7030; color: #fff; }
        .filter-select, .search-input { max-width: 300px; display:inline-block; margin-right:10px; }
        .carousel-item img { height: 300px; object-fit: cover; border-radius: 10px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">Pisco Business</a>
    </div>
</nav>

<div class="container">

    <!-- Slider publicitaire -->
    <div id="promoCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="../uploads/slider1.jpg" class="d-block w-100" alt="Promo 1">
            </div>
            <div class="carousel-item">
                <img src="../uploads/slider2.jpg" class="d-block w-100" alt="Promo 2">
            </div>
            <div class="carousel-item">
                <img src="../uploads/slider3.jpg" class="d-block w-100" alt="Promo 3">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#promoCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#promoCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    <!-- Recherche et filtres -->
    <form method="get" class="mb-4 d-flex align-items-center flex-wrap">
        <input type="text" name="search" class="form-control search-input" placeholder="Rechercher un produit..." value="<?= htmlspecialchars($search) ?>">
        <select name="category" class="form-select filter-select">
            <option value="0">Toutes les catégories</option>
            <?php foreach($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $category_id == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-success">Filtrer</button>
    </form>

    <!-- Grille produits -->
    <div class="row g-4">
        <?php if(empty($products)): ?>
            <p>Aucun produit trouvé.</p>
        <?php else: ?>
            <?php foreach($products as $product): ?>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <?php if($product['first_image']): ?>
                            <img src="../<?= htmlspecialchars($product['first_image']) ?>" class="card-img-top" style="height:200px; object-fit:cover;" alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php else: ?>
                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height:200px;">
                                Pas d'image
                            </div>
                        <?php endif; ?>
                        <div class="card-img-overlay">
                            <a href="product.php?id=<?= $product['id'] ?>" class="btn btn-success">Ajouter au panier</a>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="price mb-2"><?= number_format($product['price'], 0, ',', ' ') ?> F CFA</p>
                            <a href="product.php?id=<?= $product['id'] ?>" class="btn btn-details mt-auto">Voir détails</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<footer class="mt-5 py-4 text-center bg-white border-top">
    &copy; <?= date('Y') ?> Pisco Business. Tous droits réservés.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
