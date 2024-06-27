<?php
include 'connexion.php';
include 'navbar.php';  // On inclut la barre de navigation

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

$id_createur = isset($_GET['id']) ? $_GET['id'] : null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_createur = $_POST['id_createur'];
    $nom_createur = $_POST['nom_createur'];
    $about = $_POST['about'];
    $biographie = isset($_POST['biographie']) ? 1 : 0;
    $description = $_POST['description'];
    $chaine_site = isset($_POST['chaine_site']) ? 1 : 0;
    $date_naissance = !empty($_POST['date_naissance']) ? $_POST['date_naissance'] : null;
    $date_deces = !empty($_POST['date_deces']) ? $_POST['date_deces'] : null;
    $url = $_POST['url'];
    $image = $_POST['image'];

    $sql = "UPDATE Createur SET 
            Nom_Createur = :nom_createur, 
            About = :about, 
            Biographie = :biographie, 
            Description = :description, 
            Chaine_site = :chaine_site, 
            Date_naissance = :date_naissance, 
            Date_deces = :date_deces, 
            URL = :url, 
            Image = :image 
            WHERE ID_Createur = :id_createur";
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([
            'nom_createur' => $nom_createur,
            'about' => $about,
            'biographie' => $biographie,
            'description' => $description,
            'chaine_site' => $chaine_site,
            'date_naissance' => $date_naissance,
            'date_deces' => $date_deces,
            'url' => $url,
            'image' => $image,
            'id_createur' => $id_createur
        ]);
        echo "Créateur de contenu mis à jour avec succès !";
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
    }
} else {
    if ($id_createur) {
        $sql = "SELECT * FROM Createur WHERE ID_Createur = :id_createur";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_createur' => $id_createur]);
        $createur = $stmt->fetch();
        if (!$createur) {
            echo "Créateur non trouvé ou vous n'avez pas les droits pour le modifier.";
            exit();
        }
    } else {
        echo "ID du créateur non spécifié.";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Créateur de Contenu - Glean</title>
    <!-- Bulma CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <!-- Google Fonts: Inter -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap">
</head>
<body>
    <div class="container">
        <h1 class="title">Modifier Créateur de Contenu</h1>
        <form action="edit_createur.php?id=<?php echo htmlspecialchars($id_createur); ?>" method="post">
            <input type="hidden" id="id_createur" name="id_createur" value="<?php echo htmlspecialchars($createur['ID_Createur']); ?>">
            <div class="field">
                <label class="label" for="nom_createur">Nom:</label>
                <div class="control">
                    <input class="input" type="text" id="nom_createur" name="nom_createur" value="<?php echo htmlspecialchars($createur['Nom_Createur']); ?>" required>
                </div>
            </div>
            <div class="field">
                <label class="label" for="about">À propos:</label>
                <div class="control">
                    <textarea class="textarea" id="about" name="about" required><?php echo htmlspecialchars($createur['About']); ?></textarea>
                </div>
            </div>
            <div class="field">
                <label class="checkbox">
                    <input type="checkbox" name="biographie" <?php echo $createur['Biographie'] ? 'checked' : ''; ?>>
                    Cocher si c'est une biographie
                </label>
            </div>
            <div class="field">
                <label class="label" for="description">Description:</label>
                <div class="control">
                    <textarea class="textarea" id="description" name="description"><?php echo htmlspecialchars($createur['Description']); ?></textarea>
                </div>
            </div>
            <div class="field">
                <label class="checkbox">
                    <input type="checkbox" name="chaine_site" <?php echo $createur['Chaine_site'] ? 'checked' : ''; ?>>
                    Cocher si c'est une chaine ou un site
                </label>
            </div>
            <div class="field">
                <label class="label" for="date_naissance">Date de Naissance:</label>
                <div class="control">
                    <input class="input" type="date" id="date_naissance" name="date_naissance" value="<?php echo htmlspecialchars($createur['Date_naissance']); ?>">
                </div>
            </div>
            <div class="field">
                <label class="label" for="date_deces">Date de Décès:</label>
                <div class="control">
                    <input class="input" type="date" id="date_deces" name="date_deces" value="<?php echo htmlspecialchars($createur['Date_deces']); ?>">
                </div>
            </div>
            <div class="field">
                <label class="label" for="url">URL:</label>
                <div class="control">
                    <input class="input" type="url" id="url" name="url" value="<?php echo htmlspecialchars($createur['URL']); ?>">
                </div>
            </div>
            <div class="field">
                <label class="label" for="image">Image URL:</label>
                <div class="control">
                    <input class="input" type="url" id="image" name="image" value="<?php echo htmlspecialchars($createur['Image']); ?>">
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <button class="button is-link" type="submit">Mettre à jour</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>