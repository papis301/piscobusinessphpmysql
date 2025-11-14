<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'db.php';
require_once 'functions.php';

// Vérifier l'ID du produit
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Produit introuvable.");
}

$product_id = intval($_GET['id']);

// Récupération du produit
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$product){
    die("Produit introuvable.");
}

// Récupération des images
$stmtImg = $pdo->prepare("SELECT image FROM product_images WHERE product_id = ? ORDER BY id ASC");
$stmtImg->execute([$product_id]);
$images = $stmtImg->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?> - Pisco Business</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body { background-color: #f8f9fa; font-family: 'Poppins', sans-serif; }

        .product-gallery {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .product-gallery img.main-image {
            width: 100%;
            max-height: 420px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .product-thumbs {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .product-thumbs img {
            width: 80px;
            height: 80px;
            border-radius: 6px;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid transparent;
            transition: .2s;
        }
        .product-thumbs img:hover, .product-thumbs img.active {
            border-color: #198754;
        }

        .product-info h2 {
            font-weight: 700;
        }
        .product-price {
            color: #198754;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .btn-buy {
            background-color: #ffc107;
            color: #000;
            font-weight: 600;
            border: none;
            transition: 0.3s;
        }
        .btn-buy:hover {
            background-color: #e0a800;
        }
    </style>
</head>
<body>

<?php include 'partials/header.php'; ?>

<div class="container py-5">
    <a href="index.php" class="text-decoration-none mb-4 d-inline-block">
        <i class="fa fa-arrow-left"></i> Retour à la boutique
    </a>

    <div class="row g-5">
        <!-- Galerie images -->
        <div class="col-md-6">
            <div class="product-gallery">
                <?php if(!empty($images)): ?>
                    <img id="mainImage" src="../<?= htmlspecialchars($images[0]) ?>" class="main-image mb-3" alt="<?= htmlspecialchars($product['name']) ?>">
                    <div class="product-thumbs">
                        <?php foreach($images as $key => $img): ?>
                            <img src="../<?= htmlspecialchars($img) ?>" class="<?= $key === 0 ? 'active' : '' ?>" data-src="../<?= htmlspecialchars($img) ?>">
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <img src="uploads/default.jpg" class="main-image" alt="Image non disponible">
                <?php endif; ?>
            </div>
        </div>

        <!-- Infos produit -->
        <div class="col-md-6 product-info">
            <h2><?= htmlspecialchars($product['name']) ?></h2>
            <p class="product-price"><?= number_format($product['price'], 0, ',', ' ') ?> F CFA</p>

            <p class="text-muted"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

            <p><strong>Disponibilité :</strong> 
                <?= $product['stock'] > 0 ? "<span class='text-success'>En stock</span>" : "<span class='text-danger'>Rupture</span>" ?>
            </p>

            <div class="mt-4">
                <button class="btn btn-buy btn-lg px-5">
                    <i class="fa fa-shopping-cart me-2"></i> Acheter maintenant
                </button>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>

<script>
    const thumbs = document.querySelectorAll('.product-thumbs img');
    const mainImage = document.getElementById('mainImage');

    thumbs.forEach(img => {
        img.addEventListener('click', function() {
            mainImage.src = this.dataset.src;
            thumbs.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
        });
    });
</script>

</body>
</html>
