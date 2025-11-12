<?php
require_once '../inc/db.php';
require_once '../inc/functions.php';

// Récupérer tous les produits avec leur première image
$stmt = $pdo->query("
    SELECT p.*, (
        SELECT image FROM product_images WHERE product_id = p.id ORDER BY id ASC LIMIT 1
    ) AS main_image
    FROM products p
    ORDER BY p.created_at DESC
");
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Style personnalisé -->
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
        .product-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }
        .product-price {
            font-size: 1.2rem;
            color: #198754;
            font-weight: bold;
        }
        .btn-buy {
            background-color: #ffc107;
            border: none;
            color: #000;
            font-weight: 600;
        }
        .btn-buy:hover {
            background-color: #e0a800;
        }
        .carousel-item img {
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<?php include 'partials/header.php'; ?>

<!-- 🔸 SLIDER PUBLICITAIRE -->
<div id="promoCarousel" class="carousel slide container mt-4" data-bs-ride="carousel">
  <div class="carousel-inner rounded-3 shadow-lg">
    <div class="carousel-item active">
      <img src="assets/img/slider1.jpg" class="d-block w-100" alt="Promo 1">
      <div class="carousel-caption d-none d-md-block">
        <h5 class="fw-bold">Bienvenue sur Pisco Business</h5>
        <p>Découvrez nos dernières offres exclusives !</p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="assets/img/slider2.jpg" class="d-block w-100" alt="Promo 2">
      <div class="carousel-caption d-none d-md-block">
        <h5 class="fw-bold">Livraison rapide</h5>
        <p>Partout au Sénégal 🇸🇳</p>
      </div>
    </div>
    <div class="carousel-item">
      <img src="assets/img/slider3.jpg" class="d-block w-100" alt="Promo 3">
      <div class="carousel-caption d-none d-md-block">
        <h5 class="fw-bold">Des prix imbattables</h5>
        <p>Équipez-vous au meilleur tarif !</p>
      </div>
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#promoCarousel" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#promoCarousel" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>

<!-- 🔸 PRODUITS -->
<div class="container my-5">
  <div class="text-center mb-5">
    <h2 class="fw-bold text-dark">Nos Produits</h2>
    <p class="text-muted">Les meilleurs articles à des prix compétitifs</p>
  </div>

  <div class="row g-4">
    <?php if(count($products) > 0): ?>
      <?php foreach($products as $p): ?>
        <div class="col-lg-3 col-md-4 col-sm-6">
          <div class="product-card">
            <a href="product.php?id=<?= $p['id'] ?>" class="text-decoration-none text-dark">
              <img src="../<?= htmlspecialchars($p['main_image'] ?: 'uploads/default.jpg') ?>" class="w-100" alt="<?= htmlspecialchars($p['name']) ?>">
              <div class="p-3">
                <h6 class="product-title mb-1"><?= htmlspecialchars($p['name']) ?></h6>
                <p class="product-price mb-2"><?= number_format($p['price'], 0, ',', ' ') ?> F CFA</p>
                <a href="product.php?id=<?= $p['id'] ?>" class="btn btn-buy w-100">Voir le produit</a>
              </div>
            </a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center text-muted">Aucun produit disponible pour le moment.</p>
    <?php endif; ?>
  </div>
</div>

<?php include 'partials/footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
