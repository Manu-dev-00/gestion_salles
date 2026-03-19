<?php 
session_start();
 ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Connexion - Gestion de Salles</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700" rel="stylesheet" type="text/css" />
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body id="page-top">

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="index.php"><img src="assets/img/navbar-logo.svg" alt="Logo" /></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive">
                Menu <i class="fas fa-bars ms-1"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav text-uppercase ms-auto py-4 py-lg-0">
                    <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Salles</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.html">À propos</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <header class="masthead" style="background-image: url('assets/img/header-bg.jpg');">
        <div class="container">
            <div class="masthead-heading text-uppercase">Connexion</div>
            <div class="masthead-subheading">Accédez à votre espace de réservation</div>
        </div>
    </header>

    <!-- Login Form -->
    <section class="page-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5">
                    <form action="traitement_login.php" method="POST" class="bg-light p-5 rounded shadow">
                        <h2 class="text-center mb-4 text-uppercase">Se connecter</h2>
                        <?php if(isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger text-center"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control form-control-lg" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" name="password" class="form-control form-control-lg" required />
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-xl text-uppercase">Se connecter</button>
                        </div>
                        <p class="text-center mt-3">
                            Pas de compte ? <a href="register.php" class="text-primary">Inscrivez-vous</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>