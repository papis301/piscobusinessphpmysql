<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db.php'; // connexion $pdo

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone    = trim($_POST['phone']);
    $password = trim($_POST['password']);

    // Vérification des champs
    if (empty($phone) || empty($password)) {
        $errors[] = "Veuillez remplir tous les champs.";
    } else {
        // Récupérer l'utilisateur
        $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ?");
        $stmt->execute([$phone]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Connexion OK
            $_SESSION['user'] = [
                'id'    => $user['id'],
                'name'  => $user['name'],
                'phone' => $user['phone'],
                'is_admin' => $user['is_admin']
            ];

            header("Location: dashboard.php"); // page d'accueil
            exit;
        } else {
            $errors[] = "Téléphone ou mot de passe incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Pisco Business</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="height:100vh;">
    <div class="card shadow-lg p-4" style="max-width:450px; width:100%;">
        <h3 class="text-center mb-4 text-success fw-bold">Connexion</h3>

        <!-- Erreurs -->
        <?php if(!empty($errors)): ?>
            <div class="alert alert-danger">
                <?= implode("<br>", array_map('htmlspecialchars', $errors)) ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire -->
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Téléphone</label>
                <input type="text" class="form-control" name="phone" required placeholder="Ex : 770001122">
            </div>

            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input type="password" class="form-control" name="password" required placeholder="********">
            </div>

            <button class="btn btn-success w-100">Se connecter</button>
        </form>

        <p class="mt-3 text-center">
            Pas de compte ? <a href="register.php">Créer un compte</a>
        </p>
    </div>
</div>

</body>
</html>
