<?php
session_start();
require 'inc/db.php';
require 'inc/functions.php';

redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];

// Suppression d'une réservation
if (isset($_GET['supprimer']) && is_numeric($_GET['supprimer'])) {
    $resa_id = (int)$_GET['supprimer'];
    $pdo->prepare("DELETE FROM reservations WHERE id = ? AND utilisateur_id = ?")
        ->execute([$resa_id, $user_id]);
    $_SESSION['success'] = "Réservation supprimée avec succès.";
    header('Location: mes_reservations.php');
    exit;
}

// Récupération des réservations de l'utilisateur
$stmt = $pdo->prepare("
    SELECT r.*, s.nom as salle_nom, s.photo 
    FROM reservations r 
    JOIN salles s ON r.salle_id = s.id 
    WHERE r.utilisateur_id = ? 
    ORDER BY r.date_reservation DESC, r.heure_debut DESC
");
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mes Réservations - Gestion de Salles</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body id="page-top">
    <?php include 'includes/navbar.php'; ?>

    <header class="masthead" style="background-image: url('assets/img/header-bg.jpg');">
        <div class="container position-relative">
            <div class="row justify-content-center">
                <div class="col-xl-6">
                    <div class="text-center text-white">
                        <h1 class="text-uppercase mb-3">Mes Réservations</h1>
                        <p class="lead">Gérez toutes vos réservations en un coup d'œil</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section class="page-section bg-light">
        <div class="container">
            <!-- Messages -->
            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert alert-success text-center"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger text-center"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <?php if (empty($reservations)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-5x text-muted mb-4"></i>
                    <h3>Aucune réservation pour le moment</h3>
                    <a href="dashboard.php" class="btn btn-primary btn-xl mt-3">Réserver une salle</a>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($reservations as $r): ?>
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow h-100 position-relative">
                                <img src="assets/img/portfolio/<?= $r['photo'] ?>" class="card-img-top" alt="<?= e($r['salle_nom']) ?>" style="height: 200px; object-fit: cover;">
                                <div class="card-body d-flex flex-column">
                                    <h4 class="card-title"><?= e($r['salle_nom']) ?></h4>
                                    <p class="card-text">
                                        <strong>Date :</strong> <?= date('d/m/Y', strtotime($r['date_reservation'])) ?><br>
                                        <strong>Heure :</strong> <?= substr($r['heure_debut'], 0, 5) ?> - <?= substr($r['heure_fin'], 0, 5) ?><br>
                                        <strong>Statut :</strong>
                                        <span class="badge bg-<?= $r['statut']=='confirmée'?'success':($r['statut']=='annulée'?'danger':'warning') ?>">
                                            <?= ucfirst(str_replace('_', ' ', $r['statut'])) ?>
                                        </span>
                                    </p>
                                    <?php if ($r['motif']): ?>
                                        <p class="text-muted"><em>"<?= e($r['motif']) ?>"</em></p>
                                    <?php endif; ?>

                                    <div class="mt-auto text-end">
                                        <a href="modifier_reservation.php?id=<?= $r['id'] ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                        <a href="?supprimer=<?= $r['id'] ?>" class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Supprimer cette réservation ?')">
                                            <i class="fas fa-trash"></i> Supprimer
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>