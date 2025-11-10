<?php
// inc/functions.php
require_once __DIR__.'/db.php';

function getAllProducts(){
    global $pdo;
    $stmt = $pdo->query("SELECT p.*, c.name as category FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
    return $stmt->fetchAll();
}

function getProductById($id){
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function userIsLogged(){
    return !empty($_SESSION['user_id']);
}

function currentUser(){
    if(userIsLogged()){
        global $pdo;
        $stmt = $pdo->prepare("SELECT id,phone,email,is_admin FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return null;
}
