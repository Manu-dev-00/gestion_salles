<?php
session_start();
require 'inc/db.php';
require 'inc/functions.php';

redirectIfNotLoggedIn();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$salle_id = (int)($_POST['salle_id'] ?? 0);
$date     = $_POST['date'] ?? '';
$debut    = $_POST['debut'] ?? '';
$fin      = $_POST['fin'] ?? '';
$motif    = trim($_POST['motif'] ?? '');

if (!$salle_id || !$date || !$debut || !$fin || $debut >= $fin) {
    $_SESSION['error'] = "Veuillez remplir tous les champs correctement.";
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

// Vérifier que la salle existe
$salle = $pdo->prepare("SELECT nom FROM salles WHERE id = ?");
$salle->execute([$salle_id]);
if (!$salle->fetch()) {
    $_SESSION['error'] = "Salle invalide.";
    header('Location: dashboard.php');
    exit;
    exit;
}

// Vérifier les conflits
$check = $pdo->prepare("
    SELECT id FROM reservations 
    WHERE salle_id = ? 
      AND date_reservation = ? 
      AND statut != 'annulée'
      AND (
        (heure_debut < ? AND heure_fin > ?) OR
        (heure_debut < ? AND heure_fin > ?) OR
        (heure_debut >= ? AND heure_fin <= ?)
      )
");
$check->execute([$salle_id, $date, $fin, $debut, $fin, $debut, $debut, $fin]);

if ($check->fetch()) {
    $_SESSION['error'] = "Ce créneau est déjà réservé !";
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

// Enregistrement de la réservation
try {
    $stmt = $pdo->prepare("
        INSERT INTO reservations 
        (utilisateur_id, salle_id, date_reservation, heure_debut, heure_fin, motif, statut) 
        VALUES (?, ?, ?, ?, ?, ?, 'en_attente')
    ");
    $stmt->execute([$_SESSION['user_id'], $salle_id, $date, $debut, $fin, $motif]);

    $_SESSION['success'] = "Réservation confirmée ! Vous pouvez la voir dans vos réservations.";
    header('Location: mes_reservations.php');
    exit;

} catch (Exception $e) {
    $_SESSION['error'] = "Erreur lors de la réservation.";
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
?>