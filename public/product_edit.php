<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';

$user = currentUser();
if (!$user || !$user['is_admin']) {
    header('Location: ../public/index.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = [
    'name' => '',
    'category_id' => null,
    'description' => '',
    'price' => '',
    'stock' => '',
    'image' => ''
];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    if (!$product) {
        die("Produit introuvable.");
    }
}

// Récupérer les catégories pour le menu déroulant
$cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];

    $filename = $product['image'];

    // Upload image si nouveau fichier
    if (!empty($_FILES['image']['name'])) {
        $targetDir = __DIR__ . 'uploads/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $basename = basename($_FILES['image']['name']);
        $ext = strtolower(pathinfo($basename, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];

        if (in_array($ext, $allowed)) {
            $filename = time() . "_" . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $basename);
            move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $filename);
        } else {
            $error = "Type de fichier non autorisé.";
        }
    }

    if (!$error) {
        if ($id) {
            // Mise à jour
            $stmt = $pdo->prepare("UPDATE products SET category_id=?, name=?, description=?, price=?, stock=?, image=? WHERE id=?");
            $stmt->execute([$category_id, $name, $description, $price, $stock, $filename, $id]);
        } else {
            // Création
            $slug = strtolower(preg_replace('/[^a-z0-9]+/i','-', $name));
            $stmt = $pdo->prepare("INSERT INTO products (category_id,name,slug,description,price,stock,image) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$category_id, $name, $slug, $description, $price, $stock, $filename]);
        }
        header('Location: products.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title><?php echo $id ? "Modifier" : "Ajouter"; ?> un produit</title>
</head>
<body>
<h1><?php echo $id ? "Modifier" : "Ajouter"; ?> un produit</h1>

<?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>

<form method="post" enctype="multipart/form-data">
    <label>Nom du produit</label><br>
    <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required><br><br>

    <label>Catégorie</label><br>
    <select name="category_id">
        <option value="">-- Choisir --</option>
        <?php foreach($cats as $c): ?>
            <option value="<?php echo $c['id']; ?>" <?php if($c['id']==$product['category_id']) echo "selected"; ?>>
                <?php echo htmlspecialchars($c['name']); ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Description</label><br>
    <textarea name="description" rows="5" cols="40"><?php echo htmlspecialchars($product['description']); ?></textarea><br><br>

    <label>Prix (FCFA)</label><br>
    <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required><br><br>

    <label>Stock</label><br>
    <input type="number" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required><br><br>

    <label>Image</label><br>
    <?php if($product['image']): ?>
        <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" width="100"><br>
    <?php endif; ?>
    <input type="file" name="image" accept="image/*"><br><br>

    <button type="submit">💾 Enregistrer</button>
</form>

<p><a href="products.php">← Retour à la liste</a></p>
</body>
</html>
