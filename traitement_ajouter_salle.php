<?php
session_start();
require 'inc/db.php';
require 'inc/functions.php';
redirectIfNotAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom        = trim($_POST['nom']);
    $capacite   = (int)$_POST['capacite'];
    $description = $_POST['description'] ?? null;

    if (empty($nom) || $capacite < 1) {
        $_SESSION['error'] = "Données invalides.";
    } elseif ($_FILES['photo']['error'] === 0) {
        $dir = '../assets/img/portfolio/';
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('salle_') . '.' . strtolower($ext);

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $dir . $filename)) {
            $pdo->prepare("INSERT INTO salles (nom, capacite, description, photo) VALUES (?, ?, ?, ?)")
                ->execute([$nom, $capacite, $description, $filename]);

            $_SESSION['success'] = "Salle ajoutée !";
        } else {
            $_SESSION['error'] = "Erreur upload photo.";
        }
    } else {
        $_SESSION['error'] = "Photo obligatoire.";
    }
}
header('Location: admin.php');
exit;
?>