<?php
require_once 'config.php';
require_once 'db.php';
require_once 'functions.php';

$user = currentUser();
if(!$user || !$user['is_admin']){
    header('Location: ../login.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if(!$product){
    die("Produit introuvable");
}

// Charger les catégories
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $image = $product['image'];

    // Upload image (si nouvelle)
    if(!empty($_FILES['image']['name'])){
        $targetDir = "uploads/";
        if(!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if(in_array($ext, $allowed)){
            if(move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)){
                $image = 'uploads/' . $fileName;
            }
        }
    }

    if($name != '' && $price > 0){
        $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, stock=?, image=?, category_id=? WHERE id=?");
        $stmt->execute([$name, $description, $price, $stock, $image, $category_id, $id]);
        header("Location: products.php?success=1");
        exit;
    } else {
        $error = "Veuillez remplir tous les champs obligatoires.";
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Modifier un produit</title>
</head>
<body>
  <h2>Modifier un produit</h2>
  <p><a href="products.php">← Retour à la liste</a></p>

  <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>

  <form method="post" enctype="multipart/form-data">
      <label>Nom *</label><br>
      <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required><br><br>

      <label>Description</label><br>
      <textarea name="description" rows="3"><?= htmlspecialchars($product['description']) ?></textarea><br><br>

      <label>Prix (F CFA)</label><br>
      <input type="number" name="price" step="0.01" value="<?= $product['price'] ?>" required><br><br>

      <label>Stock</label><br>
      <input type="number" name="stock" value="<?= $product['stock'] ?>" required><br><br>

      <label>Catégorie</label><br>
      <select name="category_id">
        <option value="">-- Sélectionner --</option>
        <?php foreach($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>" <?= ($cat['id']==$product['category_id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['name']) ?>
          </option>
        <?php endforeach; ?>
      </select><br><br>

      <label>Image</label><br>
      <?php if($product['image']): ?>
          <img src="../<?= htmlspecialchars($product['image']) ?>" alt="" width="100"><br>
      <?php endif; ?>
      <input type="file" name="image" accept="image/*"><br><br>

      <button type="submit">Enregistrer</button>
  </form>
</body>
</html>
