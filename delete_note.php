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

// Vérifier si l'ID de la note à supprimer est passé
if (isset($_GET['id'])) {
    $note_id = $_GET['id'];

    // Vérifier que la note appartient à l'utilisateur
    $sql = "SELECT * FROM Note WHERE ID_Note = :note_id AND ID_Utilisateur = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['note_id' => $note_id, 'user_id' => $user_id]);
    $note = $stmt->fetch();

    if ($note) {
        // Supprimer les enregistrements associés dans la table Epingles
        $sql = "DELETE FROM Epingles WHERE ID_Note = :note_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['note_id' => $note_id]);

        // Supprimer la note
        $sql = "DELETE FROM Note WHERE ID_Note = :note_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['note_id' => $note_id]);

        // Rediriger vers la page Mes Notes
        header('Location: mes_notes.php');
        exit();
    } else {
        echo "Erreur: Note introuvable ou vous n'avez pas les permissions nécessaires pour la supprimer.";
    }
} else {
    echo "Erreur: Aucun ID de note spécifié.";
}
?>