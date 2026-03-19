<?php
session_start();
require 'inc/db.php';
require 'inc/functions.php';

// Sécurité admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// === AJOUTER UNE SALLE ===
if (isset($_POST['ajouter_salle'])) {
    $nom = trim($_POST['nom']);
    $capacite = (int)$_POST['capacite'];
    $description = trim($_POST['description']);
    $photo = $_FILES['photo']['name'] ?? '';

    if ($nom && $capacite && !empty($_FILES['photo']['tmp_name'])) {
        $target = "assets/img/portfolio/" . basename($photo);
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
            $stmt = $pdo->prepare("INSERT INTO salles (nom, capacite, description, photo) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nom, $capacite, $description, $photo]);
            $_SESSION['success'] = "Salle ajoutée avec succès !";
        } else {
            $_SESSION['error'] = "Erreur lors de l'upload de l'image.";
        }
    } else {
        $_SESSION['error'] = "Tous les champs sont obligatoires.";
    }
    header('Location: admin.php#gestionsalles');
    exit;
}

// === MODIFIER UNE SALLE ===
if (isset($_POST['modifier_salle'])) {
    $id = (int)$_POST['id'];
    $nom = trim($_POST['nom']);
    $capacite = (int)$_POST['capacite'];
    $description = trim($_POST['description']);
    $photo = $_FILES['photo']['name'] ?? '';

    if ($nom && $capacite) {
        if (!empty($_FILES['photo']['tmp_name'])) {
            $target = "assets/img/portfolio/" . basename($photo);
            move_uploaded_file($_FILES['photo']['tmp_name'], $target);
            $pdo->prepare("UPDATE salles SET nom=?, capacite=?, description=?, photo=? WHERE id=?")
                ->execute([$nom, $capacite, $description, $photo, $id]);
        } else {
            $pdo->prepare("UPDATE salles SET nom=?, capacite=?, description=? WHERE id=?")
                ->execute([$nom, $capacite, $description, $id]);
        }
        $_SESSION['success'] = "Salle modifiée avec succès !";
    }
    header('Location: admin.php#gestionsalles');
    exit;
}

// === SUPPRIMER UNE SALLE ===
if (isset($_GET['supprimer_salle'])) {
    $id = (int)$_GET['supprimer_salle'];
    $pdo->prepare("DELETE FROM salles WHERE id = ?")->execute([$id]);
    $_SESSION['success'] = "Salle supprimée définitivement.";
    header('Location: admin.php#gestionsalles');
    exit;
}

// === CONFIRMER / ANNULER RÉSERVATION ===
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'] === 'confirmer' ? 'confirmée' : 'annulée';
    $pdo->prepare("UPDATE reservations SET statut = ? WHERE id = ?")->execute([$action, $id]);
    $_SESSION['success'] = "Réservation $action.";
    header('Location: admin.php');
    exit;
}

// Récupération données
$reservations = $pdo->query("
    SELECT r.*, u.nom, u.prenom, u.email, s.nom as salle_nom
    FROM reservations r
    JOIN utilisateurs u ON r.utilisateur_id = u.id
    JOIN salles s ON r.salle_id = s.id
    ORDER BY r.date_reservation DESC
")->fetchAll();

$salles = $pdo->query("SELECT * FROM salles ORDER BY nom")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>Administration - Gestion Complète</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        .badge-confirme { background: #28a745; color: white; }
        .badge-attente { background: #ffc107; color: black; }
        .badge-annule { background: #dc3545; color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .nav-tabs .nav-link.active { background: #343a40; color: white; }
    </style>
</head>
<body id="page-top">
    <?php include 'includes/navbar.php'; ?>

    <header class="masthead bg-dark text-white text-center py-5">
        <div class="container">
            <h1 class="text-uppercase fw-bold mb-3">
                <i class="fas fa-crown"></i> Administration
            </h1>
            <p class="lead">Gestion des réservations et des salles</p>
        </div>
    </header>

    <section class="page-section">
        <div class="container">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success text-center"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger text-center"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <!-- Onglets -->
            <ul class="nav nav-tabs mb-4" id="adminTabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#reservations" onclick="openTab('reservations')">
                        Réservations (<?= count($reservations) ?>)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#gestionsalles" onclick="openTab('gestionsalles')">
                        Gérer les salles (<?= count($salles) ?>)
                    </a>
                </li>
            </ul>

            <!-- === ONGLET RÉSERVATIONS === -->
            <div id="reservations" class="tab-content active">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Toutes les réservations</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th>Utilisateur</th>
                                        <th>Salle</th>
                                        <th>Date</th>
                                        <th>Heure</th>
                                        <th>Motif</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reservations as $r): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($r['prenom'].' '.$r['nom']) ?><br><small><?= htmlspecialchars($r['email']) ?></small></td>
                                        <td><strong><?= htmlspecialchars($r['salle_nom']) ?></strong></td>
                                        <td><?= date('d/m/Y', strtotime($r['date_reservation'])) ?></td>
                                        <td><?= substr($r['heure_debut'],0,5) ?> - <?= substr($r['heure_fin'],0,5) ?></td>
                                        <td><em><?= $r['motif'] ? htmlspecialchars($r['motif']) : '-' ?></em></td>
                                        <td>
                                            <span class="badge badge-<?= $r['statut']=='confirmée'?'confirme':($r['statut']=='annulée'?'annule':'attente') ?>">
                                                <?= ucfirst($r['statut']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($r['statut'] == 'en_attente'): ?>
                                                <a href="admin.php?action=confirmer&id=<?= $r['id'] ?>" class="btn btn-success btn-sm" title="Confirmer">
                                                    Confirmer
                                                </a>
                                                <a href="admin.php?action=annuler&id=<?= $r['id'] ?>" class="btn btn-danger btn-sm" title="Annuler">
                                                    Annuler
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">Traitée</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- === ONGLET GESTION DES SALLES === -->
            <div id="gestionsalles" class="tab-content">
                <!-- Ajouter une salle -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Ajouter une nouvelle salle</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <input type="text" name="nom" class="form-control" placeholder="Nom de la salle" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="number" name="capacite" class="form-control" placeholder="Capacité (personnes)" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <textarea name="description" class="form-control" rows="3" placeholder="Description (facultatif)"></textarea>
                            </div>
                            <div class="mb-3">
                                <input type="file" name="photo" accept="image/*" class="form-control" required>
                                <small class="text-muted">Image → assets/img/portfolio/</small>
                            </div>
                            <button type="submit" name="ajouter_salle" class="btn btn-success">
                                Ajouter la salle
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Liste des salles -->
                <div class="row">
                    <?php foreach ($salles as $s): ?>
                    <div class="col-lg-4 mb-4">
                        <div class="card shadow h-100">
                            <img src="assets/img/portfolio/<?= $s['photo'] ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5><?= htmlspecialchars($s['nom']) ?></h5>
                                <p><strong><?= $s['capacite'] ?> places</strong></p>
                                <p class="text-muted"><?= $s['description'] ?: 'Aucune description' ?></p>
                                <div class="mt-auto">
                                    <!-- Bouton Modifier -->
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $s['id'] ?>">
                                        Modifier
                                    </button>
                                    <a href="admin.php?supprimer_salle=<?= $s['id'] ?>" class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Supprimer définitivement cette salle ?')">
                                        Supprimer
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Modifier -->
                        <div class="modal fade" id="editModal<?= $s['id'] ?>">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-warning">
                                        <h5 class="modal-title">Modifier la salle</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                            <div class="mb-3">
                                                <label>Nom</label>
                                                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($s['nom']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Capacité</label>
                                                <input type="number" name="capacite" class="form-control" value="<?= $s['capacite'] ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Description</label>
                                                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($s['description']) ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label>Nouvelle photo (laisser vide pour garder l'actuelle)</label>
                                                <input type="file" name="photo" accept="image/*" class="form-control">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" name="modifier_salle" class="btn btn-success">
                                                Enregistrer les modifications
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
            document.getElementById(tabName).classList.add('active');
            document.querySelector(`a[href="#${tabName}"]`).classList.add('active');
        }
    </script>
</body>
</html>