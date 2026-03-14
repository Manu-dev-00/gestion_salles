<?php session_start(); if ($_SESSION['role'] !== 'admin') { header('Location: login.php'); exit; } ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>Ajouter une salle</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body id="page-top">
    <?php include 'includes/navbar.php'; ?>

    <header class="masthead bg-success text-white text-center">
        <div class="container">
            <h1 class="text-uppercase">Ajouter une nouvelle salle</h1>
        </div>
    </header>

    <section class="page-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <form action="traitement_ajouter_salle.php" method="POST" enctype="multipart/form-data" class="bg-light p-5 rounded shadow">
                        <div class="mb-3">
                            <label>Nom de la salle</label>
                            <input type="text" name="nom" class="form-control form-control-lg" required />
                        </div>
                        <div class="mb-3">
                            <label>Capacité (personnes)</label>
                            <input type="number" name="capacite" class="form-control form-control-lg" required />
                        </div>
                        <div class="mb-3">
                            <label>Description</label>
                            <textarea name="description" rows="4" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Photo de la salle</label>
                            <input type="file" name="photo" accept="image/*" class="form-control" required />
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success btn-xl text-uppercase">
                                Ajouter la salle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>