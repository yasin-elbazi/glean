<?php
include 'connexion.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_COOKIE['session_token'])) {
    header('Location: login.php');
    exit();
}

$session_token = $_COOKIE['session_token'];
$sql = "SELECT * FROM Sessions WHERE Session_Token = :session_token AND Expiry > NOW()";
$stmt = $pdo->prepare($sql);
$stmt->execute(['session_token' => $session_token]);
$session = $stmt->fetch();

if (!$session) {
    header('Location: login.php');
    exit();
}

$user_id = $session['User_ID'];

$id_createur = $_GET['id'];

// Supprimer l'association entre l'utilisateur et le créateur de contenu
$sql = "DELETE FROM UtilisateurCreateur WHERE ID_Createur = :id_createur AND ID_Utilisateur = :user_id";
$stmt = $pdo->prepare($sql);
try {
    $stmt->execute(['id_createur' => $id_createur, 'user_id' => $user_id]);
    echo "Créateur de contenu supprimé avec succès !";
    header('Location: mes_createurs.php');
} catch (PDOException $e) {
    echo "Erreur: " . $e->getMessage();
}
?>