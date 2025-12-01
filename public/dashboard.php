<?php
session_start();
require_once 'db.php';

// Sécurité : accès réservé aux utilisateurs connectés
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$user_id = $user['id'];

// Récupération des produits de l'utilisateur avec leur première image
$stmt = $pdo->prepare("
    SELECT p.*, 
    (SELECT image FROM product_images WHERE product_id = p.id ORDER BY id ASC LIMIT 1) AS main_image,
    c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.user_id = ?
    ORDER BY p.created_at DESC
");
$stmt->execute([$user_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Dashboard | <?= htmlspecialchars($user['name']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'partials/header.php'; ?>

<div class="container mt-5">
    <h3 class="mb-4">Bienvenue, <?= htmlspecialchars($user['name']) ?> 👋</h3>

    <div class="d-flex justify-content-between align-items-center">
        <h4>Mes Produits</h4>
        <a href="add_product.php" class="btn btn-success">+ Ajouter un produit</a>
    </div>

    <table class="table table-bordered table-hover mt-4 bg-white">
        <thead class="table-success">
            <tr>
                <th>Image</th>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Prix</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>

        <?php if (!$products): ?>
            <tr><td colspan="6" class="text-center">Aucun produit ajouté pour le moment</td></tr>
        <?php endif; ?>

        <?php foreach ($products as $p): ?>
            <tr>
                <td>
                    <?php if ($p['main_image']): ?>
                        <img src="<?= htmlspecialchars($p['main_image']) ?>" width="60">
                    <?php else: ?>
                        <span class="text-muted">Aucune image</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars($p['category_name'] ?? '—') ?></td>
                <td><?= number_format($p['price'], 0, ',', ' ') ?> F CFA</td>
                <td><?= intval($p['stock']) ?></td>
                <td>
                    <a href="product_edit.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                    <a href="delete_product.php?id=<?= $p['id'] ?>" onclick="return confirm('Supprimer ce produit ?')" class="btn btn-danger btn-sm">
                        Supprimer
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>
</div>

<?php include 'partials/footer.php'; ?>
</body>
</html>
