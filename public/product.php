<?php
require_once __DIR__.'/../inc/config.php';
require_once __DIR__.'/../inc/functions.php';

if(!isset($_GET['id'])) {
    header('Location: index.php'); exit;
}
$id = (int)$_GET['id'];
$product = getProductById($id);
if(!$product){ echo "Produit non trouvé"; exit; }

// ajout au panier
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $qty = max(1, (int)($_POST['qty'] ?? 1));
    if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    if(isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id] += $qty;
    else $_SESSION['cart'][$id] = $qty;
    header('Location: cart.php'); exit;
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title><?php echo htmlspecialchars($product['name']); ?></title></head>
<body>
<h1><?php echo htmlspecialchars($product['name']); ?></h1>
<p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
<p>Prix: <?php echo number_format($product['price'],2); ?> FCFA</p>

<form method="post">
    Quantité: <input type="number" name="qty" value="1" min="1">
    <button type="submit">Ajouter au panier</button>
</form>

<a href="index.php">Retour</a>
</body>
</html>
