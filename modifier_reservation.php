<?php
session_start();
require 'inc/db.php';
require 'inc/functions.php';

redirectIfNotLoggedIn();

$user_id = $_SESSION['user_id'];
$resa_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("
    SELECT r.*, s.nom AS salle_nom 
    FROM reservations r 
    JOIN salles s ON r.salle_id = s.id 
    WHERE r.id = ? AND r.utilisateur_id = ?
");
$stmt->execute([$resa_id, $user_id]);
$resa = $stmt->fetch();

if (!$resa) {
    $_SESSION['error'] = "Réservation non trouvée.";
    header('Location: mes_reservations.php');
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date   = $_POST['date'];
    $debut  = $_POST['heure_debut'];  // ex: "14:30"
    $fin    = $_POST['heure_fin'];
    $motif  = trim($_POST['motif']);

    // Validation des heures (format HH:MM + tranches de 30 min + entre 08:00 et 20:00)
    function validerHeure($h) {
        return preg_match('/^(0[8-9]|1[0-9]|20):([03]0|00)$/', $h);
    }

    if (empty($date) || empty($debut) || empty($fin)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif ($date < date('Y-m-d')) {
        $error = "Vous ne pouvez pas réserver dans le passé.";
    } elseif (!validerHeure($debut) || !validerHeure($fin)) {
        $error = "Heure invalide. Utilisez le format HH:MM (ex: 09:30, 14:00) et uniquement par tranches de 30 minutes entre 08:00 et 20:00.";
    } elseif ($fin <= $debut) {
        $error = "L'heure de fin doit être après l'heure de début.";
    } else {
        // Ajouter les secondes pour la BDD
        $debut = $debut . ':00';
        $fin   = $fin . ':00';

        // Vérifier les conflits
        $check = $pdo->prepare("
            SELECT 1 FROM reservations 
            WHERE salle_id = ? AND date_reservation = ? AND statut != 'annulée'
              AND id != ?
              AND (
                heure_debut < ? AND heure_fin > ? OR
                heure_debut < ? AND heure_fin > ? OR
                heure_debut >= ? AND heure_fin <= ?
              )
        ");
        $check->execute([
            $resa['salle_id'], $date, $resa_id,
            $fin, $debut, $fin, $debut, $debut, $fin
        ]);

        if ($check->fetch()) {
            $error = "Ce créneau est déjà réservé.";
        } else {
            $update = $pdo->prepare("
                UPDATE reservations 
                SET date_reservation = ?, heure_debut = ?, heure_fin = ?, motif = ?
                WHERE id = ? AND utilisateur_id = ?
            ");
            $update->execute([$date, $debut, $fin, $motif, $resa_id, $user_id]);

            $_SESSION['success'] = "Réservation modifiée avec succès !";
            header('Location: mes_reservations.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Modifier la réservation - <?= htmlspecialchars($resa['salle_nom']) ?></title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700|Roboto+Slab:400,100,300,700" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <style>
        .input-heure { font-family: 'Courier New', monospace; letter-spacing: 1px; }
    </style>
</head>
<body id="page-top">

    <?php include 'includes/navbar.php'; ?>

    <header class="masthead bg-warning text-dark text-center py-5">
        <div class="container">
            <h1 class="text-uppercase fw-bold mb-3">Modifier la réservation #<?= $resa_id ?></h1>
            <p class="lead fw-bold">Salle : <?= htmlspecialchars($resa['salle_nom']) ?></p>
        </div>
    </header>

    <section class="page-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <div class="card shadow border-0">
                        <div class="card-header bg-primary text-white text-center py-4">
                            <h3 class="mb-0">Modifier la réservation</h3>
                        </div>

                        <div class="card-body p-5">

                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger text-center mb-4 rounded-pill">
                                    <?= $error ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Date</label>
                                    <input type="date" name="date" class="form-control form-control-lg rounded-pill text-center" 
                                           value="<?= $resa['date_reservation'] ?>" required min="<?= date('Y-m-d') ?>">
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Heure de début</label>
                                        <input type="text" name="heure_debut" class="form-control form-control-lg rounded-pill text-center input-heure" 
                                               value="<?= substr($resa['heure_debut'], 0, 5) ?>" 
                                               placeholder="14:30" maxlength="5" required 
                                               pattern="(0[8-9]|1[0-9]|20):[03]0">
                                        <small class="text-muted">Format HH:MM (ex: 09:30, 14:00)</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Heure de fin</label>
                                        <input type="text" name="heure_fin" class="form-control form-control-lg rounded-pill text-center input-heure" 
                                               value="<?= substr($resa['heure_fin'], 0, 5) ?>" 
                                               placeholder="16:00" maxlength="5" required 
                                               pattern="(0[8-9]|1[0-9]|20):[03]0">
                                        <small class="text-muted">Doit être après le début</small>
                                    </div>
                                </div>

                                <div class="mb-5">
                                    <label class="form-label fw-bold">Motif (facultatif)</label>
                                    <textarea name="motif" rows="4" class="form-control rounded-3" 
                                              placeholder="Ex: Réunion commerciale, formation..."><?= htmlspecialchars($resa['motif'] ?? '') ?></textarea>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-warning btn-xl text-uppercase text-dark px-5 py-3 me-3">
                                        Enregistrer
                                    </button>
                                    <a href="mes_reservations.php" class="btn btn-secondary btn-xl text-uppercase px-5 py-3">
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
</body>
</html>