<?php
session_start();
require_once 'inc/db.php';  // Vérifie que ce chemin est bon

// Debug : décommente la ligne ci-dessous pour voir si le fichier est appelé
// die('traitement_register.php est bien appelé !');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom     = trim($_POST['nom'] ?? '');
    $prenom  = trim($_POST['prenom'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation simple
    if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
        $_SESSION['error'] = "Tous les champs sont obligatoires.";
        header('Location: register.php');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Email invalide.";
        header('Location: register.php');
        exit;
    }

    if (strlen($password) < 6) {
        $_SESSION['error'] = "Le mot de passe doit faire au moins 6 caractères.";
        header('Location: register.php');
        exit;
    }

    try {
        // Vérifier si l'email existe
        $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $_SESSION['error'] = "Cet email est déjà utilisé.";
            header('Location: register.php');
            exit;
        }

        // Inscription
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, password, role) VALUES (?, ?, ?, ?, 'client')");
        $stmt->execute([$nom, $prenom, $email, $hash]);

        $_SESSION['success'] = "Inscription réussie ! Connectez-vous maintenant.";
        header('Location: login.php');
        exit;

    } catch (Exception $e) {
        // Si erreur base de données → on affiche un message clair
        $_SESSION['error'] = "Erreur serveur. Réessayez plus tard.";
        error_log("Erreur inscription : " . $e->getMessage());
        header('Location: register.php');
        exit;
    }
}

// Si quelqu’un accède directement sans POST
header('Location: register.php');
exit;
?>