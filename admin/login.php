<?php
session_start();
require_once '../inc/db.php'; // contient la connexion $pdo
require_once '../inc/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    // Vérifier si le téléphone existe
    $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Vérifie le mot de passe
        if (password_verify($password, $user['password'])) {
            // Vérifie si c'est un admin
            if ($user['is_admin']) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: index.php");
                exit;
            } else {
                $error = "Accès refusé. Vous n'êtes pas administrateur.";
            }
        } else {
            $error = "Mot de passe incorrect.";
        }
    } else {
        $error = "Numéro de téléphone introuvable.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Admin - Pisco Business</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
  <div class="card shadow-lg p-4" style="max-width: 400px; width: 100%;">
    <h4 class="text-center mb-4 text-success fw-bold">Pisco Business - Admin</h4>

    <?php if ($error): ?>
      <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="mb-3">
        <label for="phone" class="form-label">Téléphone</label>
        <input type="text" class="form-control" id="phone" name="phone" required placeholder="Ex : 770000000">
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Mot de passe</label>
        <input type="password" class="form-control" id="password" name="password" required placeholder="Votre mot de passe">
      </div>

      <button type="submit" class="btn btn-success w-100">Se connecter</button>
    </form>
  </div>
</div>

</body>
</html>
