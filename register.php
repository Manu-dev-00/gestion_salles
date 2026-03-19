<?php
 session_start();
 require_once 'inc/functions.php';

// Si déjà connecté → on va directement au dashboard
// if (isset($_SESSION['user_id'])) {
//     header('Location: dashboard.php');
//     exit;
// }
 ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Inscription - Gestion de Salles</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700" rel="stylesheet" type="text/css" />
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body id="page-top">

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="index.php"><img src="assets/img/navbar-logo.svg" alt="Logo" /></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive">
                Menu <i class="fas fa-bars ms-1"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav text-uppercase ms-auto py-4 py-lg-0">
                    <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Connexion</a></li>
                     <li class="nav-item"><a class="nav-link" href="about.html">A propos</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <header class="masthead" style="background-image: url('assets/img/header-bg.jpg');">
        <div class="container">
            <div class="masthead-heading text-uppercase">Inscription</div>
            <div class="masthead-subheading">Rejoignez-nous dès aujourd'hui</div>
        </div>
    </header>

    <section class="page-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <form action="traitement_register.php" method="POST" class="bg-light p-5 rounded shadow">
                        <h2 class="text-center mb-4 text-uppercase">Créer un compte</h2>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Nom</label>
                                <input type="text" name="nom" class="form-control form-control-lg" required />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Prénom</label>
                                <input type="text" name="prenom" class="form-control form-control-lg" required />
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control form-control-lg" required />
                        </div>
                        <div class="mb-3">
                            <label>Mot de passe</label>
                            <input type="password" name="password" class="form-control form-control-lg" required />
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-xl text-uppercase">S'inscrire</button>
                        </div>
                        <p class="text-center mt-3">
                            Déjà inscrit ? <a href="login.php" class="text-primary">Se connecter</a>
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

