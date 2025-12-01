<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db.php'; // Connexion $pdo

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $phone    = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm']);

    // Vérifications de base
    if (empty($name) || empty($phone) || empty($password) || empty($confirm)) {
        $errors[] = "Tous les champs sont obligatoires.";
    }

    if (!preg_match('/^7[0-9]{8}$/', $phone)) { // Format Sénégal
        $errors[] = "Numéro de téléphone invalide.";
    }

    if ($password !== $confirm) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    if (empty($errors)) {
        // Vérifier si phone existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
        $stmt->execute([$phone]);
        if ($stmt->fetch()) {
            $errors[] = "Ce numéro est déjà enregistré.";
        } else {
            // Hashage du mot de passe
            $hashed = password_hash($password, PASSWORD_BCRYPT);

            // Insertion sécurisée
            $stmt = $pdo->prepare("INSERT INTO users (name, phone, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $phone, $hashed])) {
                $success = "Compte créé avec succès ! Vous pouvez maintenant vous connecter.";
            } else {
                $errors[] = "Erreur lors de l'inscription. Veuillez réessayer.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Pisco Business</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="height:100vh;">
    <div class="card shadow-lg p-4" style="max-width:450px; width:100%;">
        <h3 class="text-center mb-4 text-success fw-bold">Créer un compte</h3>

        <!-- Messages -->
        <?php if(!empty($errors)): ?>
            <div class="alert alert-danger">
                <?= implode("<br>", array_map('htmlspecialchars', $errors)) ?>
            </div>
        <?php endif; ?>

        <?php if($success): ?>
            <div class="alert alert-success text-center">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire -->
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nom complet</label>
                <input type="text" class="form-control" name="name" required placeholder="Ex : Mamadou Faye">
            </div>
            <div class="mb-3">
                <label class="form-label">Téléphone</label>
                <input type="text" class="form-control" name="phone" required placeholder="Ex : 770001122">
            </div>
            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input type="password" class="form-control" name="password" required placeholder="********">
            </div>
            <div class="mb-3">
                <label class="form-label">Confirmer le mot de passe</label>
                <input type="password" class="form-control" name="confirm" required placeholder="********">
            </div>

            <button class="btn btn-success w-100">Créer mon compte</button>
        </form>

        <p class="mt-3 text-center">
            Déjà inscrit ? <a href="login.php">Se connecter</a>
        </p>
    </div>
</div>

</body>
</html>
