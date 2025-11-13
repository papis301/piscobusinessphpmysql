<?php
require_once __DIR__.'config.php';
require_once __DIR__.'functions.php';
require_once __DIR__.'db.php';

$cart = $_SESSION['cart'] ?? [];

if(isset($_GET['remove'])){
    $rid = (int)$_GET['remove'];
    unset($_SESSION['cart'][$rid]);
    header('Location: cart.php'); exit;
}

// update quantities
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    foreach($_POST['qty'] as $pid => $q){
        $pid = (int)$pid;
        $q = max(0,(int)$q);
        if($q === 0) unset($_SESSION['cart'][$pid]);
        else $_SESSION['cart'][$pid] = $q;
    }
    header('Location: cart.php'); exit;
}

// fetch product details
$items = [];
$total = 0.0;
if($cart){
    $ids = array_keys($cart);
    $in = str_repeat('?,', count($ids)-1) . '?';
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($in)");
    $stmt->execute($ids);
    $rows = $stmt->fetchAll();
    foreach($rows as $r){
        $qty = $cart[$r['id']];
        $r['qty'] = $qty;
        $r['sub'] = $qty * $r['price'];
        $total += $r['sub'];
        $items[] = $r;
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Panier</title></head><body>
<h1>Panier</h1>
<form method="post">
<table border="1" cellpadding="6">
<tr><th>Produit</th><th>Prix</th><th>Qté</th><th>Sous-total</th><th>Action</th></tr>
<?php foreach($items as $it): ?>
<tr>
  <td><?php echo htmlspecialchars($it['name']); ?></td>
  <td><?php echo number_format($it['price'],2); ?></td>
  <td><input type="number" name="qty[<?php echo $it['id']; ?>]" value="<?php echo $it['qty']; ?>" min="0"></td>
  <td><?php echo number_format($it['sub'],2); ?></td>
  <td><a href="?remove=<?php echo $it['id']; ?>">Retirer</a></td>
</tr>
<?php endforeach; ?>
</table>
<button type="submit">Mettre à jour</button>
</form>

<p>Total : <?php echo number_format($total,2); ?> FCFA</p>
<p><a href="checkout.php">Passer la commande</a></p>
<p><a href="index.php">Continuer mes achats</a></p>
</body></html>
