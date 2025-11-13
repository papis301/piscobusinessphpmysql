<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'config.php';
require_once 'db.php';
require_once 'functions.php';

$user = currentUser();
if(!$user || !$user['is_admin']){
    header('Location: ../login.php');
    exit;
}

$stmt = $pdo->query("SELECT * FROM categories ORDER BY id DESC");
$categories = $stmt->fetchAll();
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Catégories - Pisco Business</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <h2>Liste des catégories</h2>

  <p><a href="index_admin.php">🏠 Tableau de bord</a> | <a href="add_category.php">+ Ajouter une catégorie</a></p>

  <?php if(isset($_GET['success'])) echo "<p style='color:green'>Action réussie.</p>"; ?>

  <table border="1" cellpadding="8" cellspacing="0">
    <tr>
      <th>ID</th>
      <th>Nom</th>
      <th>Description</th>
      <th>Créée le</th>
      <th>Actions</th>
    </tr>
    <?php foreach($categories as $cat): ?>
      <tr>
        <td><?= htmlspecialchars($cat['id']) ?></td>
        <td><?= htmlspecialchars($cat['name']) ?></td>
        <td><?= htmlspecialchars($cat['description']) ?></td>
        <td><?= htmlspecialchars($cat['created_at']) ?></td>
        <td>
          <a href="edit_category.php?id=<?= $cat['id'] ?>">Modifier</a> |
          <a href="delete_category.php?id=<?= $cat['id'] ?>" onclick="return confirm('Supprimer cette catégorie ?')">Supprimer</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
