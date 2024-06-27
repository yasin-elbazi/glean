<?php
// Démarrer la session
session_start();

// Supprimer le cookie de session
if (isset($_COOKIE['session_token'])) {
    setcookie('session_token', '', time() - 3600, '/'); // Définir une date d'expiration passée pour supprimer le cookie
}

// Détruire la session
session_destroy();

// Rediriger vers la page d'accueil
header('Location: index.php');
exit();
?>