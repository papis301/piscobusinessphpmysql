<?php
require_once '../inc/config.php';
require_once '../inc/db.php';
require_once '../inc/functions.php';

$user = currentUser();
if(!$user || !$user['is_admin']){
    header('Location: ../login.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
$stmt->execute([$id]);

header("Location: categories.php?success=1");
exit;
