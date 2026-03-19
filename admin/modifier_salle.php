<?php 
session_start(); 
// Protection admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Récupération de l'ID de la salle depuis l'URL (ex: ?id=3)
$salle_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($salle_id <= 0) {
    header('Location: admin.php');
    exit;
}

// À adapter avec ta vraie connexion BDD plus tard
// Pour l'exemple, on simule les données d'une salle
$salle = [
    'id' => $salle_id,
    'nom' => 'Salle Lumière',
    'capacite' => 12,
    'description' => 'Salle lumineuse avec vidéoprojecteur et tableau blanc interactif.',
    'photo' => 'salle1.jpg'
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Modifier une salle - Admin</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700" rel="stylesheet" type="text/css" />
    <link href="css/styles.css" rel="stylesheet" />
</head>
<body id="page-top">

    <?php include 'includes/navbar.php'; ?>

    <header class="masthead bg-warning text-dark text-center">
        <div class="container">
            <h1 class="text-uppercase fw-bold">Modifier la salle #<?php echo $salle['id']; ?></h1>
            <p class="lead">Mettez à jour les informations de la salle</p>
        </div>
    </header>

    <section class="page-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <img src="assets/img/portfolio/<?php echo $salle['photo']; ?>" class="card-img-top" alt="<?php echo $salle['nom']; ?>">
                        <div class="card-body">
                            <form action="traitement_modifier_salle.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?php echo $salle['id']; ?>">

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nom de la salle</label>
                                    <input type="text" name="nom" class="form-control form-control-lg" 
                                           value="<?php echo htmlspecialchars($salle['nom']); ?>" required />
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Capacité (personnes)</label>
                                    <input type="number" name="capacite" class="form-control form-control-lg" 
                                           value="<?php echo $salle['capacite']; ?>" min="1" required />
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Description</label>
                                    <textarea name="description" rows="5" class="form-control" required><?php echo htmlspecialchars($salle['description']); ?></textarea>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Photo actuelle</label><br>
                                    <img src="assets/img/portfolio/<?php echo $salle['photo']; ?>" alt="Photo actuelle" class="img-fluid rounded mb-2" style="max-height: 200px;">
                                    <p class="text-muted"><small>Laisser vide pour conserver la photo actuelle</small></p>
                                    <input type="file" name="nouvelle_photo" accept="image/*" class="form-control" />
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-warning btn-xl text-uppercase text-dark">
                                        <i class="fas fa-save me-2"></i> Enregistrer les modifications
                                    </button>
                                    <a href="admin.php" class="btn btn-secondary btn-xl text-uppercase">
                                        Annuler
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>