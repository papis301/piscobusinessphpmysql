<?php
require_once '../inc/db.php';
require_once '../inc/functions.php';

// Vérifier que l'ID du produit est passé
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die("Produit introuvable.");
}

$product_id = intval($_GET['id']);

// Récupérer le produit
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$product){
    die("Produit introuvable.");
}

// Récupérer toutes les images du produit
$stmtImages = $pdo->prepare("SELECT image FROM product_images WHERE product_id = ? ORDER BY id ASC");
$stmtImages->execute([$product_id]);
$images = $stmtImages->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?> - Pisco Business</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .thumbnail img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid transparent;
        }
        .thumbnail img.active {
            border-color: #198754;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <a href="index.php" class="btn btn-secondary mb-4">← Retour aux produits</a>

    <div class="row">
        <div class="col-md-6">
            <?php if(!empty($images)): ?>
                <div>
                    <img id="mainImage" src="../<?= htmlspecialchars($images[0]) ?>" class="img-fluid mb-3" style="height:400px; object-fit:cover;">
                </div>
                <div class="d-flex gap-2 thumbnail">
                    <?php foreach($images as $key => $img): ?>
                        <img src="../<?= htmlspecialchars($img) ?>" class="<?= $key === 0 ? 'active' : '' ?>" data-src="../<?= htmlspecialchars($img) ?>">
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height:400px;">
                    Pas d'image
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-6">
            <h2><?= htmlspecialchars($product['name']) ?></h2>
            <p class="fs-4 fw-bold"><?= number_format($product['price'], 0, ',', ' ') ?> F CFA</p>
            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            <p><strong>Stock :</strong> <?= intval($product['stock']) ?></p>
        </div>
    </div>
</div>

<script>
    const thumbnails = document.querySelectorAll('.thumbnail img');
    const mainImage = document.getElementById('mainImage');

    thumbnails.forEach(img => {
        img.addEventListener('click', function(){
            // Changer l'image principale
            mainImage.src = this.dataset.src;

            // Gérer la classe active
            thumbnails.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
        });
    });
</script>

</body>
</html>
