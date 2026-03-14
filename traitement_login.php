<?php
session_start();
require 'inc/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['prenom']  = $user['prenom'];
            $_SESSION['role']    = $user['role'];

            $_SESSION['success'] = "Bienvenue {$user['prenom']} !";
            header('Location: dashboard.php');
            exit;
        } else {
            $_SESSION['error'] = "Email ou mot de passe incorrect.";
        }
    } else {
        $_SESSION['error'] = "Tous les champs sont requis.";
    }
}
header('Location: login.php');
exit;
?>