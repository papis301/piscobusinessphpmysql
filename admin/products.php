<?php
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/functions.php';
require_once __DIR__ . '/../inc/db.php';

$user = currentUser();
if (!$user || !$user['is_admin']) {
    header('Location: ../public/index.php');
    exit;
}

// Récupération produits
$stmt = $pdo->query("SELECT p.*, c.name AS category_name FROM products p 
LEFT JOIN categories c ON p.category_id = c.id 
ORDER BY p.created_at DESC");
$products = $stmt->fetchAll();
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Admin - Produits</title>
<style>
table {border-collapse: collapse; width: 100%;}
td, th {border:1px solid #ccc; padding:8px;}
a.button {background:#388E3C; color:white; padding:6px 12px; text-decoration:none;}
</style>
</head>
<body>
<h1>Gestion des produits</h1>
<p><a class="button" href="product_edit.php">+ Nouveau produit</a></p>

<table>
<tr>
  <th>ID</th>
  <th>Nom</th>
  <th>Catégorie</th>
  <th>Prix</th>
  <th>Stock</th>
  <th>Image</th>
  <th>Actions</th>
</tr>
<?php foreach($products as $p): ?>
<tr>
  <td><?php echo $p['id']; ?></td>
  <td><?php echo htmlspecialchars($p['name']); ?></td>
  <td><?php echo htmlspecialchars($p['category_name'] ?? ''); ?></td>
  <td><?php echo number_format($p['price'], 2); ?> FCFA</td>
  <td><?php echo $p['stock']; ?></td>
  <td>
    <?php if($p['image']): ?>
      <img src="../uploads/<?php echo htmlspecialchars($p['image']); ?>" width="60">
    <?php endif; ?>
  </td>
  <td>
    <a href="product_edit.php?id=<?php echo $p['id']; ?>">Modifier</a> |
    <a href="delete_product.php?id=<?php echo $p['id']; ?>" onclick="return confirm('Supprimer ce produit ?')">Supprimer</a>
  </td>
</tr>
<?php endforeach; ?>
</table>
</body>
</html>
