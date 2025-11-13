<?php
require_once 'config.php';
require_once 'db.php';
require_once 'functions.php';

$user = currentUser();
if(!$user || !$user['is_admin']){
    header('Location: ../login.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    if($name != ''){
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            $stmt->execute([$name, $description]);
            header("Location: categories.php?success=1");
            exit;
        } catch(Exception $e){
            $error = "Erreur : cette catégorie existe déjà.";
        }
    } else {
        $error = "Le nom est obligatoire.";
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Ajouter une catégorie</title>
</head>
<body>
  <h2>Ajouter une catégorie</h2>
  <p><a href="categories.php">← Retour à la liste</a></p>

  <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>

  <form method="post">
      <label>Nom *</label><br>
      <input type="text" name="name" required><br><br>

      <label>Description</label><br>
      <textarea name="description" rows="3"></textarea><br><br>

      <button type="submit">Ajouter</button>
  </form>
</body>
</html>
