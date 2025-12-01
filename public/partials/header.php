
<header class="bg-dark text-white py-3">
  <div class="container d-flex justify-content-between align-items-center">

    <!-- Logo -->
    <a href="index.php" class="text-white text-decoration-none fs-4 fw-bold">
      <i class="fa fa-bolt text-warning"></i> Pisco<span class="text-warning">Business</span>
    </a>

    <!-- Navigation -->
    <nav class="d-flex align-items-center">

      <a href="index.php" class="text-white text-decoration-none mx-3">Accueil</a>

      <?php if (!isset($_SESSION['user'])): ?>
        <!-- Utilisateur NON connecté -->
        <a href="login.php" class="btn btn-warning btn-sm mx-2 text-dark fw-bold">
          <i class="fa fa-sign-in-alt"></i> Se connecter
        </a>
        <a href="register.php" class="btn btn-outline-light btn-sm mx-2">
          <i class="fa fa-user-plus"></i> S'inscrire
        </a>

      <?php else: ?>
        <!-- Utilisateur connecté -->
        <a href="dashboard.php" class="btn btn-success btn-sm mx-2">
          <i class="fa fa-dashboard"></i> Dashboard
        </a>
        <a href="logout.php" class="btn btn-danger btn-sm mx-2">
          <i class="fa fa-power-off"></i> Déconnexion
        </a>
      <?php endif; ?>

    </nav>

  </div>
</header>
