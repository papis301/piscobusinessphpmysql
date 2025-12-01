<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

if (!userIsLogged() || !currentUser()['is_admin']) {
    header("Location: login.php");
    exit;
}

$products = getAllProducts();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des produits - Admin</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-light">

<!-- HEADER ADMIN -->
<header class="bg-dark text-white py-3">
  <div class="container d-flex justify-content-between align-items-center">
    <a href="index.php" class="text-white text-decoration-none fs-4 fw-bold">
      <i class="fa fa-bolt text-warning"></i> Pisco<span class="text-warning">Business</span>
    </a>

    <nav>
      <a href="index.php" class="text-white mx-3">🏠 Accueil</a>
      <a href="dashboard.php" class="text-white mx-3">📊 Tableau de bord</a>
      <a href="logout.php" class="btn btn-danger btn-sm"><i class="fa fa-sign-out"></i> Déconnexion</a>
    </nav>
  </div>
</header>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <h3><i class="fa fa-box"></i> Gestion des produits</h3>
        <a href="add_product.php" class="btn btn-success">
            <i class="fa fa-plus"></i> Ajouter un produit
        </a>
    </div>

    <div class="card mt-3 shadow-sm">
        <div class="card-body">
            <table class="table table-striped align-middle">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Image</th>
            <th>Nom</th>
            <th>Prix</th>
            <th>Catégorie</th>
            <th width="200">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($products as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>

            <!-- IMAGE DU PRODUIT -->
            <td>
                <?php if (!empty($p['image']) && file_exists("uploads/" . $p['image'])): ?>
                    <img src="uploads/<?= $p['image'] ?>" 
                         alt="Produit" 
                         style="width:60px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">
                <?php else: ?>
                    <img src="assets/img/no-image.png" 
                         style="width:60px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">
                <?php endif; ?>
            </td>

            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><strong><?= number_format($p['price'], 0, ',', ' ') ?> FCFA</strong></td>
            <td><?= htmlspecialchars($p['category'] ?? '—') ?></td>

            <td>
                <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-primary btn-sm">
                    <i class="fa fa-edit"></i> Modifier
                </a>
                <button class="btn btn-danger btn-sm" 
                        data-bs-toggle="modal" 
                        data-id="<?= $p['id'] ?>" 
                        data-bs-target="#deleteModal">
                    <i class="fa fa-trash"></i> Supprimer
                </button>
            </td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>

        </div>
    </div>
</div>

<!-- MODAL CONFIRM SUPPRESSION -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="fa fa-warning"></i> Confirmation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Voulez-vous vraiment supprimer ce produit ?
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <a id="confirmDeleteBtn" href="#" class="btn btn-danger">Supprimer définitivement</a>
      </div>
    </div>
  </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('button[data-bs-target="#deleteModal"]').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('confirmDeleteBtn').href = "delete_product.php?id=" + btn.dataset.id;
    });
});
</script>

</body>
</html>
