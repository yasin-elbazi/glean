<!DOCTYPE html>
<html>
<head>
    <title>Ajouter du Contenu</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>Ajouter du Contenu</h1>
    <form action="add_content.php" method="post">
        <label for="creator">Créateur de Contenu:</label><br>
        <select id="creator" name="creator" required>
            <?php
            include 'connexion.php';
            $sql = "SELECT ID_Createur, Nom_Createur FROM Createur";
            $stmt = $pdo->query($sql);
            while ($row = $stmt->fetch()) {
                echo "<option value='{$row['ID_Createur']}'>{$row['Nom_Createur']}</option>";
            }
            ?>
        </select><br>
        <label for="new_creator">Ou Ajouter un Nouveau Créateur de Contenu :</label><br>
        <input type="text" id="new_creator" name="new_creator"><br>
        <label for="type">Type du contenu:</label><br>
        <select id="type" name="type" required>
            <option value="article">Article</option>
            <option value="video">Vidéo</option>
            <option value="livre">Livre</option>
        </select><br>
        <label for="title">Titre:</label><br>
        <input type="text" id="title" name="title" required><br>
        <label for="description">Description:</label><br>
        <textarea id="description" name="description" required></textarea><br>
        <label for="url">URL:</label><br>
        <input type="url" id="url" name="url"><br><br>
        <input type="submit" value="Ajout Contenu">
    </form>
</body>
</html>


<?php
include 'connexion.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_COOKIE['session_token'])) {
    header('Location: index.php');
    exit();
}

$session_token = $_COOKIE['session_token'];
$sql = "SELECT * FROM Sessions WHERE Session_Token = :session_token AND Expiry > NOW()";
$stmt = $pdo->prepare($sql);
$stmt->execute(['session_token' => $session_token]);
$session = $stmt->fetch();

if (!$session) {
    header('Location: index.php');
    exit();
}

$user_id = $session['User_ID'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $creator_id = $_POST['creator'];
    $new_creator = $_POST['new_creator'];
    $type = $_POST['type'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $url = isset($_POST['url']) ? $_POST['url'] : '';

    if (!empty($new_creator)) {
        // Ajouter un nouveau créateur
        $sql = "INSERT INTO Createur (Nom_Createur) VALUES (:new_creator)";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute(['new_creator' => $new_creator]);
            $creator_id = $pdo->lastInsertId();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit();
        }
    }

    $sql = "INSERT INTO Contenu (ID_Createur, ID_Utilisateur, Type_de_contenu, Titre_Contenu, Description_contenu, URL) 
            VALUES (:creator_id, :user_id, :type, :title, :description, :url)";
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([
            'creator_id' => $creator_id,
            'user_id' => $user_id,
            'type' => $type,
            'title' => $title,
            'description' => $description,
            'url' => $url
        ]);
        echo "Nouveau contenu ajouté avec succés !";
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
    }
}
?>

