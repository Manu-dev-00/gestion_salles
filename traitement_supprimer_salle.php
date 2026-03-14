<?php
session_start();
require 'inc/db.php';
require 'inc/functions.php';
redirectIfNotAdmin();

$id = (int)$_POST['id'];
$photo = $pdo->query("SELECT photo FROM salles WHERE id = $id")->fetchColumn();

$pdo->prepare("DELETE FROM salles WHERE id = ?")->execute([$id]);
if ($photo) @unlink('../assets/img/portfolio/' . $photo);

$_SESSION['success'] = "Salle supprimée.";
header('Location: admin.php');
exit;
?>