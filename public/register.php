<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__.'/config.php';
require_once __DIR__.'/db.php';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if(strlen($phone) < 6){
        $error = "Numéro de téléphone invalide.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (name, phone, password) VALUES (?, ?, ?)");
        try {
            $stmt->execute([$name, $phone, $password]);
            header('Location: login.php');
            exit;
        } catch(Exception $e){
            $error = "Erreur : le numéro existe déjà ou problème de base de données.";
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><title>Inscription</title></head>
<body>
<h2>Créer un compte</h2>

<?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>

<form method="post">
    <label>Nom complet</label><br>
    <input type="text" name="name" required><br><br>

    <label>Téléphone</label><br>
    <input type="text" name="phone" required><br><br>

    <label>Mot de passe</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">S'inscrire</button>
</form>

<p>Déjà un compte ? <a href="login.php">Se connecter</a></p>
</body>
</html>
