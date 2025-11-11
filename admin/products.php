<?php
session_start();
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/../inc/functions.php';

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
    <title>Gestion des produits</title>
</head>
<body>
    <h1>Produits</h1>
    <a href="add_product.php">➕ Ajouter un produit</a> | 
    <a href="index.php">🏠 Tableau de bord</a><br><br>

    <table border="1" cellpadding="5">
        <tr>
            <th>ID</th><th>Nom</th><th>Prix</th><th>Catégorie</th><th>Actions</th>
        </tr>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= number_format($p['price'], 0, ',', ' ') ?> FCFA</td>
                <td><?= htmlspecialchars($p['category'] ?? '—') ?></td>
                <td>
                    <a href="edit_product.php?id=<?= $p['id'] ?>">Modifier</a> |
                    <a href="delete_product.php?id=<?= $p['id'] ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
