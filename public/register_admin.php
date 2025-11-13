<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// Vérifier si un admin est connecté
$loggedUser = currentUser();
$alreadyAdminLogged = ($loggedUser && !empty($loggedUser['is_admin']));

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    // Validations
    if ($name === '') $errors[] = "Le nom est obligatoire.";
    if ($phone === '') $errors[] = "Le numéro de téléphone est obligatoire.";
    if ($password === '' || $password2 === '') $errors[] = "Le mot de passe et la confirmation sont obligatoires.";
    if ($password !== $password2) $errors[] = "Les mots de passe ne correspondent pas.";
    if (strlen($password) < 6) $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";

    // Vérifier l’unicité du téléphone
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
        $stmt->execute([$phone]);
        if ($stmt->fetch()) {
            $errors[] = "Un utilisateur avec ce numéro de téléphone existe déjà.";
        }
    }

    // Insertion
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, phone, password, is_admin) VALUES (?, ?, ?, 1)");
            $stmt->execute([$name, $phone, $hash]);

            $success = "Compte administrateur créé avec succès. Vous pouvez maintenant vous connecter.";
            header("Refresh:2; url=login.php");
        } catch (Exception $e) {
            $errors[] = "Erreur lors de l’insertion : " . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Créer un admin - Pisco Business</title>
  <link href="../public/electro/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f8f9fa; }
    .card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
  </style>
</head>
<body>
<div class="container d-flex justify-content-center align-items-center" style="min-height:80vh">
  <div class="card p-4" style="max-width:520px; width:100%">
    <h4 class="mb-3 text-center text-primary">Créer un compte administrateur</h4>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $err): ?>
            <li><?= htmlspecialchars($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="alert alert-success text-center"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
      <div class="mb-3">
        <label class="form-label">Nom complet</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Téléphone</label>
        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required placeholder="Ex : 770000000">
      </div>

      <div class="mb-3">
        <label class="form-label">Mot de passe</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Confirmer le mot de passe</label>
        <input type="password" name="password2" class="form-control" required>
      </div>

      <button class="btn btn-success w-100" type="submit">Créer le compte admin</button>

      <div class="text-center mt-3">
        <a href="login.php" class="text-decoration-none">← Se connecter</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
