<?php
include 'connexion.php';

$username = "admin";
$password = "admin";

// Vérifier les informations d'identification admin
$sql = "SELECT * FROM Utilisateur WHERE Nom = :username";
$stmt = $pdo->prepare($sql);
$stmt->execute(['username' => $username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['Mot_de_passe'])) {
    // Créer un jeton de session
    $session_token = bin2hex(random_bytes(16));
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $sql = "INSERT INTO Sessions (User_ID, Session_Token, Expiry) VALUES (:user_id, :session_token, :expiry)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'user_id' => $user['ID_Utilisateur'],
        'session_token' => $session_token,
        'expiry' => $expiry
    ]);

    setcookie('session_token', $session_token, time() + 3600, "/");
    header("Location: workspace.php");
    exit();
} else {
    echo "Erreur de connexion à la démo.";
}
?>