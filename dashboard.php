<?php
session_start();
require 'inc/db.php';

// Récupérer les salles + calcul disponibilité aujourd'hui
$aujourdhui = date('Y-m-d');
$stmt = $pdo->query("SELECT * FROM salles ORDER BY nom");
$salles = $stmt->fetchAll();

foreach ($salles as &$salle) {
    $check = $pdo->prepare("
        SELECT COUNT(*) FROM reservations 
        WHERE salle_id = ? 
          AND date_reservation = ? 
          AND statut IN ('en_attente', 'confirmée')
    ");
    $check->execute([$salle['id'], $aujourdhui]);
    $occupes = $check->fetchColumn();

    $total_creneaux = 24; // 08h-20h → 24 créneaux de 30 min
    $taux = $occupes / $total_creneaux;

    if ($taux >= 1) {
        $salle['statut'] = 'complet';
    } elseif ($taux >= 0.9) {
        $salle['statut'] = 'presque';
    } else {
        $salle['statut'] = 'dispo';
    }
}
unset($salle);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gestion de Salles - Réservation en ligne</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto+Slab:wght@400;700&display=swap" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet" />

    <!-- CSS MAGIQUE POUR UN DESIGN PARFAIT -->
    <style>
        .portfolio-item {
            position: relative;
            overflow: hidden;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.18);
            transition: all 0.4s ease;
            background: #fff;
            margin-bottom: 30px;
        }
        .portfolio-item:hover {
            transform: translateY(-15px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }
        .portfolio-item img {
            width: 100%;
            height: 320px;
            object-fit: cover;
            object-position: center;
            border-radius: 16px 16px 0 0;
            transition: transform 0.6s ease;
        }
        .portfolio-item:hover img {
            transform: scale(1.1);
        }
        .badge-dispo {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 10;
            font-weight: 800;
            font-size: 14px;
            padding: 10px 22px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.4);
            animation: glow 2s infinite alternate;
        }
        .badge-success { background: linear-gradient(135deg, #28a745, #20c997); color: white; }
        .badge-warning { background: linear-gradient(135deg, #ffc107, #fd7e14); color: black; }
        .badge-danger  { background: linear-gradient(135deg, #e74c3c, #c0392b); color: white; }

        @keyframes glow {
            from { box-shadow: 0 6px 20px rgba(0,0,0,0.4); }
            to { box-shadow: 0 10px 35px rgba(0,0,0,0.6); }
        }

        .portfolio-caption {
            padding: 20px;
            background: white;
            border-radius: 0 0 16px 16px;
        }
        .portfolio-caption-heading {
            font-size: 1.4rem;
            font-weight: 700;
            color: #212529;
        }
        .status-text {
            font-weight: 600;
            font-size: 0.95rem;
        }

        @media (max-width: 768px) {
            .badge-dispo {
                top: 15px;
                right: 15px;
                padding: 8px 16px;
                font-size: 13px;
            }
            .portfolio-item img { height: 280px; }
        }
    </style>
head>
<body id="page-top">

    <?php include 'includes/navbar.php'; ?>

    <header class="masthead">
        <div class="container">
            <div class="masthead-subheading">Bienvenue sur notre plateforme</div>
            <div class="masthead-heading text-uppercase">Gestion de Salles</div>
            <a class="btn btn-primary btn-xl text-uppercase" href="#portfolio">Voir les salles</a>
        </div>
    </header>

    <!-- NOS SALLES AVEC BADGES MAGNIFIQUES -->
    <section class="page-section bg-light" id="portfolio">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-heading text-uppercase">Nos Salles</h2>
                <h3 class="section-subheading text-muted">Disponibilités en temps réel</h3>
            </div>

            <div class="row justify-content-center">
                <?php if (empty($salles)): ?>
                    <p class="text-muted fs-4">Aucune salle disponible pour le moment.</p>
                <?php else: ?>
                    <?php foreach ($salles as $salle): ?>
                        <div class="col-lg-4 col-md-6 mb-5">
                            <div class="portfolio-item">

                                <!-- BADGE DISPONIBILITÉ -->
                                <?php if ($salle['statut'] === 'complet'): ?>
                                    <div class="badge-dispo badge-danger">Réservé</div>
                                <?php elseif ($salle['statut'] === 'presque'): ?>
                                    <div class="badge-dispo badge-warning">Presque complet</div>
                                <?php else: ?>
                                    <div class="badge-dispo badge-success">Disponible</div>
                                <?php endif; ?>

                                <a class="portfolio-link" data-bs-toggle="modal" href="#portfolioModal<?= $salle['id'] ?>">
                                    <div class="portfolio-hover">
                                        <div class="portfolio-hover-content">
                                            <i class="fas fa-plus fa-3x"></i>
                                        </div>
                                    </div>
                                    <img src="assets/img/portfolio/<?= htmlspecialchars($salle['photo']) ?>" 
                                         alt="<?= htmlspecialchars($salle['nom']) ?>" />
                                </a>

                                <div class="portfolio-caption">
                                    <div class="portfolio-caption-heading">
                                        <?= htmlspecialchars($salle['nom']) ?>
                                    </div>
                                    <div class="portfolio-caption-subheading text-muted">
                                        <?= $salle['capacite'] ?> places
                                        <?php if ($salle['statut'] === 'complet'): ?>
                                            <span class="status-text text-danger"> • Complet aujourd'hui</span>
                                        <?php elseif ($salle['statut'] === 'presque'): ?>
                                            <span class="status-text text-warning"> • Quelques places</span>
                                        <?php else: ?>
                                            <span class="status-text text-success"> • Places disponibles</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Le reste de ta page (team, clients, footer) -->
    <section class="page-section bg-light" id="team">
        <div class="container text-center">
            <h2 class="section-heading text-uppercase">Réservation simple & intuitive</h2>
            <p class="large text-muted">Visualisez les disponibilités en temps réel et réservez instantanément.</p>
        </div>
    </section>

    <div class="py-5">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-md-3 col-sm-6 my-3"><img class="img-fluid img-brand" src="assets/img/logos/microsoft.svg" alt=""></div>
                <div class="col-md-3 col-sm-6 my-3"><img class="img-fluid img-brand" src="assets/img/logos/google.svg" alt=""></div>
                <div class="col-md-3 col-sm-6 my-3"><img class="img-fluid img-brand" src="assets/img/logos/facebook.svg" alt=""></div>
                <div class="col-md-3 col-sm-6 my-3"><img class="img-fluid img-brand" src="assets/img/logos/ibm.svg" alt=""></div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <!-- MODALES DYNAMIQUES -->
    <?php foreach ($salles as $salle): ?>
    <div class="portfolio-modal modal fade" id="portfolioModal<?= $salle['id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="close-modal" data-bs-dismiss="modal">
                    <img src="assets/img/close-icon.svg" alt="Fermer" />
                </div>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="modal-body text-center py-5">
                                <h2 class="text-uppercase"><?= htmlspecialchars($salle['nom']) ?></h2>
                                <p class="item-intro text-muted"><?= htmlspecialchars($salle['description'] ?: 'Salle moderne et équipée') ?></p>
                                <img class="img-fluid rounded mb-4" src="assets/img/portfolio/<?= htmlspecialchars($salle['photo']) ?>" alt="" />
                                <ul class="list-inline mb-5">
                                    <li><strong>Capacité :</strong> <?= $salle['capacite'] ?> personnes</li>
                                    <li><strong>Équipements :</strong> Vidéoprojecteur, Wi-Fi, tableau blanc</li>
                                    <li><strong>Horaires :</strong> 8h00 - 20h00</li>
                                </ul>

                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <a href="reserver.php?salle=<?= $salle['id'] ?>" class="btn btn-success btn-xl text-uppercase">
                                        Réserver cette salle
                                    </a>
                                <?php else: ?>
                                    <a href="login.php" class="btn btn-primary btn-xl text-uppercase">
                                        Se connecter pour réserver
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>