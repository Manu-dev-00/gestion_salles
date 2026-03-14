<?php 
session_start(); 
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$salle_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($salle_id <= 0) {
    header('Location: admin.php');
    exit;
}

// Simulation des données de la salle
$salle = [
    'id' => $salle_id,
    'nom' => 'Salle Lumière',
    'photo' => 'salle1.jpg'
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Supprimer une salle - Admin</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body id="page-top">

    <?php include 'includes/navbar.php'; ?>

    <header class="masthead bg-danger text-white text-center">
        <div class="container">
            <h1 class="text-uppercase fw-bold">Supprimer une salle</h1>
            <p class="lead">Attention : cette action est irréversible</p>
        </div>
    </header>

    <section class="page-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="card border-danger shadow">
                        <div class="card-header bg-danger text-white text-center">
                            <h4 class="mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Confirmation de suppression
                            </h4>
                        </div>
                        <div class="card-body text-center p-5">
                            <img src="assets/img/portfolio/<?php echo $salle['photo']; ?>" 
                                 alt="<?php echo $salle['nom']; ?>" 
                                 class="img-fluid rounded mb-4" style="max-height: 250px;">

                            <h3 class="text-danger mb-3">Êtes-vous sûr de vouloir supprimer définitivement ?</h3>
                            <h2 class="text-dark"><?php echo htmlspecialchars($salle['nom']); ?></h2>
                            <p class="text-muted mt-3">
                                Toutes les réservations associées seront également supprimées.
                            </p>

                            <div class="mt-5">
                                <form action="traitement_supprimer_salle.php" method="POST" class="d-inline">
                                    <input type="hidden" name="id" value="<?php echo $salle['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-xl text-uppercase px-5">
                                        <i class="fas fa-trash-alt me-2"></i> Oui, supprimer
                                    </button>
                                </form>
                                <a href="admin.php" class="btn btn-secondary btn-xl text-uppercase px-5 ms-3">
                                    Non, annuler
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>