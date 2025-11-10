<?php
require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/functions.php';

$products = getAllProducts();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Pisco Business - Boutique</title></head>
<body>
<h1>Pisco Business - Boutique</h1>
<a href="cart.php">Panier (<?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>)</a>
<hr>
<div class="products">
<?php foreach($products as $p): ?>
  <div class="product">
    <h3><?php echo htmlspecialchars($p['name']); ?></h3>
    <p>Prix: <?php echo number_format($p['price'],2); ?> FCFA</p>
    <p><a href="product.php?id=<?php echo $p['id']; ?>">Voir</a></p>
  </div>
<?php endforeach; ?>
</div>
</body>
</html>
