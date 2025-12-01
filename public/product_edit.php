<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = [
    'name' => '',
    'category_id' => null,
    'description' => '',
    'price' => '',
    'stock' => ''
];

// Charger produit
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    if (!$product) die("Produit introuvable.");

    // Charger images
    $stmtImg = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ?");
    $stmtImg->execute([$id]);
    $images = $stmtImg->fetchAll(PDO::FETCH_ASSOC);
} else {
    $images = [];
}

// Charger catégories
$cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE products SET category_id=?, name=?, description=?, price=?, stock=? WHERE id=?");
        $stmt->execute([$category_id, $name, $description, $price, $stock, $id]);
    } else {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
        $stmt = $pdo->prepare("INSERT INTO products (category_id, name, slug, description, price, stock) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$category_id, $name, $slug, $description, $price, $stock]);
        $id = $pdo->lastInsertId();
    }

    // Upload multiples images
    if (!empty($_FILES['images']['name'][0])) {
        for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
            $basename = basename($_FILES['images']['name'][$i]);
            $ext = strtolower(pathinfo($basename, PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif'];

            if (in_array($ext, $allowed)) {
                $filename = time() . "_" . uniqid() . "." . $ext;
                $targetDir = __DIR__ . "/uploads/";
                if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

                move_uploaded_file($_FILES['images']['tmp_name'][$i], $targetDir . $filename);

                $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image) VALUES (?, ?)");
                $stmt->execute([$id, $filename]);
            }
        }
    }

    header("Location: dashboard.php");
    exit;
}

// Suppression image
if (isset($_GET['delimg'])) {
    $imgId = (int)$_GET['delimg'];
    $stmt = $pdo->prepare("SELECT image FROM product_images WHERE id=? AND product_id=?");
    $stmt->execute([$imgId, $id]);
    $img = $stmt->fetchColumn();

    if ($img) {
        unlink(__DIR__ . "/uploads/" . $img);
        $pdo->prepare("DELETE FROM product_images WHERE id=?")->execute([$imgId]);
        header("Location: product_edit.php?id=$id");
        exit;
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title><?= $id ? "Modifier" : "Ajouter" ?> un produit</title>
<style>
    img.thumb { width: 120px; height: 120px; object-fit: cover; margin: 5px; border-radius: 6px; }
    .img-container { display: inline-block; position: relative; }
    .delete-btn {
        position:absolute; top:5px; right:5px;
        background:red; color:#fff; border:none;
        padding:3px 7px; font-size:12px; cursor:pointer;
        border-radius:3px;
    }
</style>
</head>
<body>

<h1><?= $id ? "Modifier" : "Ajouter" ?> un produit</h1>

<?php if(isset($_GET['saved'])) echo "<p style='color:green'>Produit enregistré.</p>"; ?>
<?php if($error) echo "<p style='color:red'>$error</p>"; ?>

<form method="post" enctype="multipart/form-data">

<label>Nom</label><br>
<input type="text" name="name" value="<?= htmlspecialchars($product['name']); ?>" required><br><br>

<label>Catégorie</label><br>
<select name="category_id">
    <option value="">-- Choisir --</option>
    <?php foreach($cats as $c): ?>
        <option value="<?= $c['id']; ?>" <?= $c['id']==$product['category_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['name']); ?>
        </option>
    <?php endforeach; ?>
</select><br><br>

<label>Description</label><br>
<textarea name="description" rows="5" cols="40"><?= htmlspecialchars($product['description']); ?></textarea><br><br>

<label>Prix (FCFA)</label><br>
<input type="number" step="0.01" name="price" value="<?= $product['price']; ?>" required><br><br>

<label>Stock</label><br>
<input type="number" name="stock" value="<?= $product['stock']; ?>" required><br><br>

<label>Ajouter des images (multiples autorisées)</label><br>
<input type="file" name="images[]" multiple accept="image/*"><br><br>

<?php if($images): ?>
<h3>Images actuelles</h3>
<?php foreach($images as $img): ?>
    <div class="img-container">
        <img class="thumb" src="uploads/<?= htmlspecialchars($img['image']); ?>">
        <a href="?id=<?= $id ?>&delimg=<?= $img['id'] ?>" onclick="return confirm('Supprimer cette image ?')">
            <button type="button" class="delete-btn">X</button>
        </a>
    </div>
<?php endforeach; ?>
<br><br>
<?php endif; ?>

<button type="submit">💾 Enregistrer</button>
</form>

<p><a href="dashboard.php">← Retour</a></p>
</body>
</html>
