<?php
session_start();
require 'inc/db.php';

// Récupérer toutes les salles
$stmt = $pdo->query("SELECT * FROM salles ORDER BY nom");
$salles = $stmt->fetchAll();

// Calculer la disponibilité du jour pour chaque salle
$aujourdhui = date('Y-m-d');
foreach ($salles as &$salle) {
    $check = $pdo->prepare("
        SELECT COUNT(*) FROM reservations 
        WHERE salle_id = ? 
          AND date_reservation = ? 
          AND statut IN ('en_attente', 'confirmée')
    ");
    $check->execute([$salle['id'], $aujourdhui]);
    $reservations_aujourdhui = $check->fetchColumn();

    // 08h00 → 20h00 = 12 heures = 24 créneaux de 30 min
    $total_creneaux = 24;
    $taux = $reservations_aujourdhui / $total_creneaux;

    if ($taux >= 1) {
        $salle['etat'] = 'complet';
    } elseif ($taux >= 0.9) {
        $salle['etat'] = 'presque';
    } else {
        $salle['etat'] = 'dispo';
    }
    $salle['taux'] = $taux;
}
unset($salle);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Réservation de salles de réunion - Simple, rapide et sécurisé" />
    <title>Gestion de Salles - Réservation en ligne</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700|Roboto+Slab:400,100,300,700" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />

    <!-- CSS PERSONNALISÉ ULTRA BEAU -->
    <style>
        .portfolio-item {
            position: relative !important;
            overflow: hidden;
            border-radius: 14px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            transition: all 0.4s ease;
            background: white;
        }
        .portfolio-item:hover {
            transform: translateY(-15px);
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
        }
        .portfolio-item img {
            width: 100% !important;
            height: 320px !important;
            object-fit: cover !important;
            object-position: center !important;
            border-radius: 14px 14px 0 0 !important;
            transition: transform 0.6s ease !important;
        }
        .portfolio-item:hover img {
            transform: scale(1.1) !important;
        }
        .badge-dispo {
            position: absolute !important;
            top: 18px !important;
            right:18px !important;
            z-index:10 !important;
            font-weight:800 !important;
            letter-spacing:1px !important;
            padding:10px 20px !important;
            border-radius:50px !important;
            font-size:14px !important;
            text-transform:uppercase;
            box-shadow:0 6px 20px rgba(0,0,0,0.4) !important;
            animation:pulse 2.5s infinite;
        }
        .badge-success {background:linear-gradient(135deg,#28a745,#20c997)!important;color:white!important;}
        .badge-warning {background:linear-gradient(135deg,#ffc107,#fd7e14)!important;color:black!important;}
        .badge-danger  {background:linear-gradient(135deg,#dc3545,#c0392b)!important;color:white!important;}
        @keyframes pulse{
            0%{box-shadow:0 6px 20px rgba(0,0,0,0.4);}
            50%{box-shadow:0 12px 35px rgba(0,0,0,0.6);}
            100%{box-shadow:0 6px 20px rgba(0,0,0,0.4);}
        }
        .status-text{font-weight:700;}
        @media (max-width:768px){
            .badge-dispo{top:12px!important;right:12px!important;padding:8px 16px!important;font-size:13px!important;}
            .portfolio-item img{height:260px!important;}
        }
    </style>
</head>
<body id="page-top">

    <?php include 'includes/navbar.php'; ?>

    <header class="masthead">
        <div class="container">
            <div class="masthead-subheading">Bienvenue sur notre plateforme</div>
            <div class="masthead-heading text-uppercase">Gestion de Salles</div>
            <a class="btn btn-primary btn-xl text-uppercase" href="#portfolio">Découvrir les salles</a>
        </div>
    </header>

    <section class="page-section" id="services">
        <!-- (tes services restent inchangés) -->
        <div class="container">
            <div class="text-center">
                <h2 class="section-heading text-uppercase">Nos Services</h2>
                <h3 class="section-subheading text-muted">Une réservation simple, rapide et sécurisée</h3>
            </div>
            <div class="row text-center">
                <div class="col-md-4">
                    <span class="fa-stack fa-4x">
                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                        <i class="fas fa-calendar-check fa-stack-1x fa-inverse"></i>
                    </span>
                    <h4 class="my-3">Réservation Facile</h4>
                    <p class="text-muted">Réservez votre salle en quelques clics, 24h/24.</p>
                </div>
                <div class="col-md-4">
                    <span class="fa-stack fa-4x">
                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                        <i class="fas fa-mobile-alt fa-stack-1x fa-inverse"></i>
                    </span>
                    <h4 class="my-3">100% Responsive</h4>
                    <p class="text-muted">Accédez depuis tous vos appareils.</p>
                </div>
                <div class="col-md-4">
                    <span class="fa-stack fa-4x">
                        <i class="fas fa-circle fa-stack-2x text-primary"></i>
                        <i class="fas fa-shield-alt fa-stack-1x fa-inverse"></i>
                    </span>
                    <h4 class="my-3">Sécurité Maximale</h4>
                    <p class="text-muted">Vos données sont protégées.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- NOS SALLES - AVEC SUPER BADGES -->
    <section class="page-section bg-light" id="portfolio">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-heading text-uppercase">Nos Salles Disponibles</h2>
                <h3 class="section-subheading text-muted">Cliquez sur une salle pour voir les détails</h3>
            </div>

            <div class="row">
                <?php if (empty($salles)): ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-muted fs-3">Aucune salle disponible pour le moment.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($salles as $salle): ?>
                        <div class="col-lg-4 col-sm-6 mb-5">
                            <div class="portfolio-item">

                                <!-- BADGE DISPONIBILITÉ -->
                                <?php if ($salle['etat'] === 'complet'): ?>
                                    <div class="badge-dispo badge-danger">Réservé</div>
                                <?php elseif ($salle['etat'] === 'presque'): ?>
                                    <div class="badge-dispo badge-warning">Presque complet</div>
                                <?php else: ?>
                                    <div class="badge-dispo badge-success">Disponible</div>
                                <?php endif; ?>

                                <a class="portfolio-link" data-bs-toggle="modal" href="#portfolioModal<?= $salle['id'] ?>">
                                    <div class="portfolio-hover">
                                        <div class="portfolio-hover-content"><i class="fas fa-plus fa-3x"></i></div>
                                    </div>
                                    <img src="assets/img/portfolio/<?= htmlspecialchars($salle['photo']) ?>" 
                                         alt="<?= htmlspecialchars($salle['nom']) ?>" />
                                </a>

                                <div class="portfolio-caption">
                                    <div class="portfolio-caption-heading"><?= htmlspecialchars($salle['nom']) ?></div>
                                    <div class="portfolio-caption-subheading text-muted">
                                        <?= $salle['capacite'] ?> places
                                        <?php if ($salle['etat'] === 'complet'): ?>
                                            <span class="status-text text-danger"> • Complet aujourd'hui</span>
                                        <?php elseif ($salle['etat'] === 'presque'): ?>
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

    <!-- Le reste de ta page (à propos, team, clients, footer) reste identique -->
    <!-- Je te les remets propres ici aussi -->

    <section class="page-section" id="about">
        <div class="container">
            <div class="text-center">
                <h2 class="section-heading text-uppercase">À propos de nous</h2>
            </div>
            <div class="row"><div class="col-lg-12">
                <ul class="timeline">
                    <li><div class="timeline-image"><img class="rounded-circle img-fluid" src="assets/img/about/1.jpg" alt="" /></div>
                        <div class="timeline-panel"><div class="timeline-heading"><h4>2010</h4><h4 class="subheading">Fondation</h4></div>
                        <div class="timeline-body"><p class="text-muted">Faciliter la réservation de salles pour tous.</p></div></div></li>
                    <li class="timeline-inverted"><div class="timeline-image"><img class="rounded-circle img-fluid" src="assets/img/about/2.jpg" alt="" /></div>
                        <div class="timeline-panel"><div class="timeline-heading"><h4>2015</h4><h4 class="subheading">Digitalisation</h4></div>
                        <div class="timeline-body"><p class="text-muted">Lancement de la plateforme en ligne.</p></div></div></li>
                    <li><div class="timeline-image"><img class="rounded-circle img-fluid" src="assets/img/about/3.jpg" alt="" /></div>
                        <div class="timeline-panel"><div class="timeline-heading"><h4>Aujourd'hui</h4><h4 class="subheading">Leader</h4></div>
                        <div class="timeline-body"><p class="text-muted">Plus de 1000 réservations par mois.</p></div></div></li>
                </ul>
            </div></div>
        </div>
    </section>

    <section class="page-section bg-light" id="team">
        <div class="container text-center">
            <h2 class="section-heading text-uppercase">Réservation simple & intuitive</h2>
            <p class="large text-muted mt-4">Visualisez les disponibilités en temps réel et réservez en quelques clics.</p>
        </div>
    </section>

    <div class="py-5 bg-white">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-md-3 col-sm-6 my-3"><img class="img-fluid img-brand d-block mx-auto" src="assets/img/logos/microsoft.svg" alt="" /></div>
                <div class="col-md-3 col-sm-6 my-3"><img class="img-fluid img-brand d-block mx-auto" src="assets/img/logos/google.svg" alt="" /></div>
                <div class="col-md-3 col-sm-6 my-3"><img class="img-fluid img-brand d-block mx-auto" src="assets/img/logos/facebook.svg" alt="" /></div>
                <div class="col-md-3 col-sm-6 my-3"><img class="img-fluid img-brand d-block mx-auto" src="assets/img/logos/ibm.svg" alt="" /></div>
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
                                <p class="item-intro text-muted"><?= htmlspecialchars($salle['description'] ?: 'Salle idéale pour vos réunions') ?></p>
                                <img class="img-fluid rounded mb-4" src="assets/img/portfolio/<?= htmlspecialchars($salle['photo']) ?>" alt="" />
                                <ul class="list-inline mb-5">
                                    <li><strong>Capacité :</strong> <?= $salle['capacite'] ?> personnes</li>
                                    <li><strong>Équipements :</strong> Vidéoprojecteur, Wi-Fi, tableau blanc</li>
                                    <li><strong>Horaires :</strong> 8h - 20h</li>
                                </ul>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <a href="reserver.php?salle=<?= $salle['id'] ?>" class="btn btn-success btn-xl text-uppercase px-5">
                                        Réserver cette salle
                                    </a>
                                <?php else: ?>
                                    <a href="login.php" class="btn btn-primary btn-xl text-uppercase px-5">
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