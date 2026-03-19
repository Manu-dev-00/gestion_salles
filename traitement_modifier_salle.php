<?php
session_start();
require 'inc/db.php';
require 'inc/functions.php';
redirectIfNotAdmin();

$id = (int)$_POST['id'];
$nom = trim($_POST['nom']);
$capacite = (int)$_POST['capacite'];
$description = $_POST['description'] ?? null;

$sql = "UPDATE salles SET nom = ?, capacite = ?, description = ? WHERE id = ?";
$params = [$nom, $capacite, $description, $id];

if ($_FILES['nouvelle_photo']['error'] === 0) {
    $dir = '../assets/img/portfolio/';
    $ext = pathinfo($_FILES['nouvelle_photo']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('salle_') . '.' . strtolower($ext);
    if (move_uploaded_file($_FILES['nouvelle_photo']['tmp_name'], $dir . $filename)) {
        $old = $pdo->query("SELECT photo FROM salles WHERE id = $id")->fetchColumn();
        if ($old && file_exists($dir . $old)) @unlink($dir . $old);

        $sql = "UPDATE salles SET nom = ?, capacite = ?, description = ?, photo = ? WHERE id = ?";
        $params = [$nom, $capacite, $description, $filename, $id];
    }
}

$pdo->prepare($sql)->execute($params);
$_SESSION['success'] = "Salle modifiée.";
header('Location: admin.php');
exit;
?>