<?php
session_start();
require 'inc/db.php';
require 'inc/functions.php';
redirectIfNotLoggedIn();

$salle_id = (int)($_GET['salle'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM salles WHERE id = ?");
$stmt->execute([$salle_id]);
$salle = $stmt->fetch();
if (!$salle) {
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>Réserver : <?= htmlspecialchars($salle['nom']) ?></title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body id="page-top">
    <?php include 'includes/navbar.php'; ?>

    <header class="masthead" style="background-image: url('assets/img/header-bg.jpg');">
        <div class="container">
            <h1 class="text-uppercase text-white">Réserver une salle</h1>
        </div>
    </header>

    <section class="page-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow">
                        <img src="assets/img/portfolio/<?= $salle['photo'] ?>" class="card-img-top" alt="<?= htmlspecialchars($salle['nom']) ?>">
                        <div class="card-body">
                            <h3 class="card-title"><?= htmlspecialchars($salle['nom']) ?></h3>
                            <p><strong>Capacité :</strong> <?= $salle['capacite'] ?> personnes</p>
                            <?php if ($salle['description']): ?>
                                <p><?= nl2br(htmlspecialchars($salle['description'])) ?></p>
                            <?php endif; ?>

                            <?php if(isset($_SESSION['error'])): ?>
                                <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                            <?php endif; ?>

                            <form action="traitement_reservation.php" method="POST">
                                <input type="hidden" name="salle_id" value="<?= $salle_id ?>">

                                <div class="mb-3">
                                    <label>Date de réservation</label>
                                    <input type="date" name="date" class="form-control" min="<?= date('Y-m-d') ?>" required />
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Heure de début</label>
                                        <input type="time" name="debut" class="form-control" required />
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Heure de fin</label>
                                        <input type="time" name="fin" class="form-control" required />
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label>Motif (facultatif)</label>
                                    <textarea name="motif" class="form-control" rows="3"></textarea>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-success btn-xl text-uppercase">
                                        Confirmer la réservation
                                    </button>
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
</body>
</html>