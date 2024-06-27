<!-- Page de Suppression de Contenu --> 

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

if (isset($_GET['id'])) {
    $content_id = $_GET['id'];

    // Supprimer le contenu de l'utilisateur
    $sql = "DELETE FROM Contenu WHERE ID_Contenu = :content_id AND ID_Utilisateur = :user_id";
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute(['content_id' => $content_id, 'user_id' => $user_id]);
        echo "Contenu supprimé avec succès !";
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
    }
}

header('Location: mes_contenus.php');
exit();
?>