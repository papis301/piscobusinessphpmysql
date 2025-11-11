<?php
session_start();
require_once '../inc/db.php';
require_once '../inc/functions.php';

// Vérification admin
$user = currentUser();
if(!$user || !$user['is_admin']){
    header('Location: login_admin.php');
    exit;
}

// Charger les catégories
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;

    // Validation
    if($name == '' || $price <= 0){
        $error = "Veuillez remplir tous les champs obligatoires.";
    } else {
        try {
          function slugify($text){
                  $text = strtolower(trim($text));
                  $text = preg_replace('/[^a-z0-9]+/i','-', $text);
                  $text = trim($text, '-');
                  return $text;
              }

              $slug = slugify($name);
            // 1️⃣ Insertion du produit
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock, category_id, slug) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $stock, $category_id, $slug]);

            $product_id = $pdo->lastInsertId();

            // 2️⃣ Upload des images
            if(!empty($_FILES['images']['name'][0])){
                $targetDir = "../uploads/";
                if(!is_dir($targetDir)) mkdir($targetDir, 0777, true);

                foreach($_FILES['images']['name'] as $key => $filename){
                    $tmpName = $_FILES['images']['tmp_name'][$key];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $allowed = ['jpg','jpeg','png','gif','webp'];

                    if(in_array($ext, $allowed)){
                        $newName = time().'_'.$key.'_'.basename($filename);
                        $targetFile = $targetDir . $newName;

                        if(move_uploaded_file($tmpName, $targetFile)){
                            $imgPath = 'uploads/' . $newName;
                            // Insertion dans product_images
                            $stmtImg = $pdo->prepare("INSERT INTO product_images (product_id, image) VALUES (?, ?)");
                            $stmtImg->execute([$product_id, $imgPath]);
                        }
                    }
                }
            }

            $success = "Produit ajouté avec succès !";
        } catch(PDOException $e){
            $error = "Erreur : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un produit - Admin Pisco Business</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2>Ajouter un produit</h2>
    <p><a href="products.php">← Retour à la liste des produits</a></p>

    <?php if($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Nom *</label>
            <input type="text" class="form-control" name="name" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Prix (F CFA) *</label>
            <input type="number" class="form-control" name="price" step="0.01" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Stock</label>
            <input type="number" class="form-control" name="stock" value="0">
        </div>

        <div class="mb-3">
            <label class="form-label">Catégorie</label>
            <select class="form-select" name="category_id">
                <option value="">-- Sélectionner --</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Images (plusieurs possibles)</label>
            <input type="file" class="form-control" name="images[]" accept="image/*" multiple>
        </div>

        <button type="submit" class="btn btn-success">Ajouter</button>
    </form>
</div>

</body>
</html>
