<?php
require_once __DIR__.'/config.php';
require_once __DIR__.'/db.php';
require_once __DIR__.'/functions.php';

if(empty($_SESSION['cart'])) { header('Location: cart.php'); exit; }

// si utilisateur non connecté => rediriger ou proposer guest
if(!userIsLogged()){
    // redirige vers login et revient après si tu veux
    header('Location: login.php'); exit;
}

$cart = $_SESSION['cart'];
$ids = array_keys($cart);
$in = str_repeat('?,', count($ids)-1) . '?';
$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($in)");
$stmt->execute($ids);
$rows = $stmt->fetchAll();

$total = 0;
foreach($rows as $r) $total += $r['price'] * $cart[$r['id']];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // créer ordre
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO orders (user_id,total,status) VALUES (?, ?, 'paid')");
        $stmt->execute([$_SESSION['user_id'], $total]);
        $order_id = $pdo->lastInsertId();

        $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id,product_id,quantity,price) VALUES (?, ?, ?, ?)");
        foreach($rows as $r){
            $qty = $cart[$r['id']];
            $stmtItem->execute([$order_id, $r['id'], $qty, $r['price']]);
            // Optionnel : diminuer stock
            $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?")->execute([$qty, $r['id']]);
        }

        $pdo->commit();
        unset($_SESSION['cart']);
        echo "Commande passée ! ID: ".$order_id;
        exit;
    } catch(Exception $e){
        $pdo->rollBack();
        die("Erreur commande : ".$e->getMessage());
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Checkout</title></head><body>
<h1>Checkout</h1>
<p>Total à payer : <?php echo number_format($total,2); ?> FCFA</p>
<form method="post">
    <!-- Ici, intégrer les infos d'adresse, paiement -->
    <button type="submit">Payer (simulate)</button>
</form>
</body></html>
