<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="assets/img/navbar-logo.svg" alt="Logo" height="50" />
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" 
                aria-expanded="false" aria-label="Toggle navigation">
            Menu <i class="fas fa-bars ms-1"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav text-uppercase ms-auto py-4 py-lg-0">
                <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Salles</a></li>
                <!-- <li class="nav-item"><a class="nav-link" href="index.php#about">À propos</a></li> -->
                <li class="nav-item"><a class="nav-link" href="admin.php">Administration</a></li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Utilisateur connecté -->
                    <li class="nav-item">
                        <a class="nav-link" href="mes_reservations.php">
                            <i class="fas fa-calendar-check me-1"></i>Mes Réservations
                        </a>
                    </li>

                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link text-warning fw-bold" href="admin.php">
                                <i class="fas fa-crown me-1"></i>Administration
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link text-danger" href="logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
                        </a>
                    </li>

                <?php else: ?>
                    <!-- Visiteur non connecté -->
                    <li class="nav-item"><a class="nav-link" href="login.php">Connexion</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-primary text-white px-4 ms-2" href="register.php">Inscription</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>