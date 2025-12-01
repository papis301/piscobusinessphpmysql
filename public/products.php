<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

if (!userIsLogged() || !currentUser()['is_admin']) {
    header("Location: login_admin.php");
    exit;
}
$user = $_SESSION['user'];
$user_id = $user['id'];
$stmt = $pdo->prepare("
    SELECT p.*, 
    (SELECT image FROM product_images WHERE product_id = p.id ORDER BY id ASC LIMIT 1) AS main_image,
    c.name AS category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.created_at DESC
");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Produits | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<!-- HEADER -->
<header class="bg-dark text-white py-3 shadow-sm">
  <div class="container d-flex justify-content-between align-items-center">
    <a href="index_admin.php" class="text-white text-decoration-none fs-4 fw-bold">
      <i class="fa fa-bolt text-warning"></i> Pisco<span class="text-warning">Business</span> Admin
    </a>
    <nav>
      <a href="index_admin.php" class="text-white mx-3">🏠 Accueil</a>
      <a href="products.php" class="text-white mx-3 fw-bold text-warning">📦 Produits</a>
      <a href="logout.php" class="btn btn-danger btn-sm">
        <i class="fa fa-sign-out"></i> Déconnexion
      </a>
    </nav>
  </div>
</header>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center">
        <h3 class="fw-bold text-dark"><i class="fa fa-box"></i> Gestion des Produits</h3>
        
    </div>

    <div class="card mt-4 shadow-sm">
        <div class="card-body">

            <table class="table table-hover align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Prix</th>
                        <th>Catégorie</th>
                        <th>Statut</th>
                        <th style="width:180px">Actions</th>
                    </tr>
                </thead>
                <tbody>

                <?php if (empty($products)): ?>
                    <tr><td colspan="6" class="text-center p-3">Aucun produit trouvé</td></tr>
                <?php endif; ?>

                <?php foreach ($products as $p): ?>
                    <tr>
                        <td class="text-center"><?= $p['id'] ?></td>

                        <td>
                    <?php if ($p['main_image']): ?>
                        <img src="<?= htmlspecialchars($p['main_image']) ?>" width="60">
                    <?php else: ?>
                        <span class="text-muted">Aucune image</span>
                    <?php endif; ?>
                </td>

                        <td><?= htmlspecialchars($p['name']) ?></td>

                        <td><strong><?= number_format($p['price'], 0, ',', ' ') ?> FCFA</strong></td>

                        <td><?= htmlspecialchars($p['category_name'] ?? '—') ?></td>
                        <td>
                            <?php if ($p['status'] === 'active'): ?>
                                <span class="badge bg-success">Activé</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Désactivé</span>
                            <?php endif; ?>
                        </td>


                        <td class="text-center">
                            <a href="toggle_status.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">
                                <?= $p['status'] === 'active' ? 'Désactiver' : 'Activer' ?>
                            </a>

                            <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-primary btn-sm">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button class="btn btn-danger btn-sm"
                                data-bs-toggle="modal"
                                data-id="<?= $p['id'] ?>"
                                data-bs-target="#deleteModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

<!-- MODAL SUPPRESSION -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="fa fa-warning"></i> Confirmation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body fs-5">
        Voulez-vous supprimer ce produit définitivement ?
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <a id="confirmDeleteBtn" class="btn btn-danger">Oui, supprimer</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('button[data-bs-target="#deleteModal"]').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('confirmDeleteBtn').href = "delete_product.php?id=" + btn.dataset.id;
    });
});
</script>

</body>
</html>
