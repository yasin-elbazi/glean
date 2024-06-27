<!-- Page de Modification de Contenu --> 

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

$content_id = $_GET['id'];
$sql = "SELECT * FROM Contenu WHERE ID_Contenu = :content_id AND ID_Utilisateur = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['content_id' => $content_id, 'user_id' => $user_id]);
$content = $stmt->fetch();

if (!$content) {
    echo "Contenu non trouvé ou vous n'avez pas les droits pour le modifier.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $url = $_POST['url'];
    $type = $_POST['type'];
    $etat = $_POST['etat'];
    $score_personnel = is_numeric($_POST['score_personnel']) ? intval($_POST['score_personnel']) : null;
    $score_critique = is_numeric($_POST['score_critique']) ? intval($_POST['score_critique']) : null;
    $date_publication = !empty($_POST['date_publication']) ? $_POST['date_publication'] : null;
    $date_visionnage = !empty($_POST['date_visionnage']) ? $_POST['date_visionnage'] : null;
    $favori = isset($_POST['favori']) ? 1 : 0;
    $date_modification = date('Y-m-d H:i:s');

    $sql = "UPDATE Contenu SET 
            Titre_Contenu = :titre, 
            Description_contenu = :description, 
            URL = :url, 
            Type_de_contenu = :type, 
            Etat_d_achevement = :etat, 
            Score_personnel = :score_personnel, 
            Score_critique = :score_critique, 
            Date_publication = :date_publication, 
            Date_visionnage = :date_visionnage, 
            Favori = :favori, 
            Date_modification = :date_modification 
            WHERE ID_Contenu = :content_id AND ID_Utilisateur = :user_id";

    $stmt = $pdo->prepare($sql);
    $params = [
        'titre' => $titre,
        'description' => $description,
        'url' => $url,
        'type' => $type,
        'etat' => $etat,
        'score_personnel' => $score_personnel,
        'score_critique' => $score_critique,
        'date_publication' => $date_publication,
        'date_visionnage' => $date_visionnage,
        'favori' => $favori,
        'date_modification' => $date_modification,
        'content_id' => $content_id,
        'user_id' => $user_id
    ];

    try {
        $stmt->execute($params);
        header('Location: mes_contenus.php');
        exit();
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Contenu - Glean</title>
    <!-- Bulma CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <!-- Google Fonts: Inter -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap">
    <style>
        body {
            background-color: #ffffff;
            font-family: 'Inter', sans-serif;
            color: #050505;
        }
        .container {
            max-width: 800px;
            margin: 2rem auto;
        }
        .button.is-link {
            background-color: #000000;
            color: #ffffff;
        }
        .button.is-link:hover {
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="title">Modifier Contenu</h1>
        <form action="edit_content.php?id=<?php echo $content_id; ?>" method="post">
            <div class="field">
                <label class="label" for="titre">Titre:</label>
                <div class="control">
                    <input class="input" type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($content['Titre_Contenu']); ?>" required>
                </div>
            </div>
            <div class="field">
                <label class="label" for="description">Description:</label>
                <div class="control">
                    <textarea class="textarea" id="description" name="description" required><?php echo htmlspecialchars($content['Description_contenu']); ?></textarea>
                </div>
            </div>
            <div class="field">
                <label class="label" for="url">URL:</label>
                <div class="control">
                    <input class="input" type="url" id="url" name="url" value="<?php echo htmlspecialchars($content['URL']); ?>" required>
                </div>
            </div>
            <div class="field">
                <label class="label" for="type">Type de Contenu:</label>
                <div class="control">
                    <div class="select">
                        <select id="type" name="type" required>
                            <option value="article" <?php echo $content['Type_de_contenu'] == 'article' ? 'selected' : ''; ?>>Article</option>
                            <option value="video" <?php echo $content['Type_de_contenu'] == 'video' ? 'selected' : ''; ?>>Vidéo</option>
                            <option value="livre" <?php echo $content['Type_de_contenu'] == 'livre' ? 'selected' : ''; ?>>Livre</option>
                            <option value="film" <?php echo $content['Type_de_contenu'] == 'film' ? 'selected' : ''; ?>>Film</option>
                            <option value="serie" <?php echo $content['Type_de_contenu'] == 'serie' ? 'selected' : ''; ?>>Série</option>
                            <option value="documentaire" <?php echo $content['Type_de_contenu'] == 'documentaire' ? 'selected' : ''; ?>>Documentaire</option>
                            <option value="citation" <?php echo $content['Type_de_contenu'] == 'citation' ? 'selected' : ''; ?>>Citation</option>
                            <option value="divers" <?php echo $content['Type_de_contenu'] == 'divers' ? 'selected' : ''; ?>>Divers</option>
                            <option value="formation" <?php echo $content['Type_de_contenu'] == 'formation' ? 'selected' : ''; ?>>Formation</option>
                            <option value="podcast" <?php echo $content['Type_de_contenu'] == 'podcast' ? 'selected' : ''; ?>>Podcast</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="field">
                <label class="label" for="etat">État d'Achèvement:</label>
                <div class="control">
                    <div class="select">
                        <select id="etat" name="etat" required>
                            <option value="à consommer" <?php echo $content['Etat_d_achevement'] == 'à consommer' ? 'selected' : ''; ?>>À consommer</option>
                            <option value="en cours" <?php echo $content['Etat_d_achevement'] == 'en cours' ? 'selected' : ''; ?>>En cours</option>
                            <option value="terminé" <?php echo $content['Etat_d_achevement'] == 'terminé' ? 'selected' : ''; ?>>Terminé</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="field">
                <label class="label" for="score_personnel">Score Personnel:</label>
                <div class="control">
                    <input class="input" type="number" id="score_personnel" name="score_personnel" value="<?php echo htmlspecialchars($content['Score_personnel']); ?>" min="0" max="100">
                </div>
            </div>
            <div class="field">
                <label class="label" for="score_critique">Score Critique:</label>
                <div class="control">
                    <input class="input" type="number" id="score_critique" name="score_critique" value="<?php echo htmlspecialchars($content['Score_critique']); ?>" min="0" max="100">
                </div>
            </div>
            <div class="field">
                <label class="label" for="date_public">Date de Publication: </label>
                <div class="control">
                    <input class="input" type="date" id="date_publication" name="date_publication" value="<?php echo htmlspecialchars($content['Date_publication']); ?>">
                </div>
            </div>
            <div class="field">
                <label class="label" for="date_visionnage">Date de Visionnage:</label>
                <div class="control">
                    <input class="input" type="date" id="date_visionnage" name="date_visionnage" value="<?php echo htmlspecialchars($content['Date_visionnage']); ?>">
                </div>
            </div>
            <div class="field">
                <label class="label" for="favori">Favori:</label>
                <div class="control">
                    <label class="checkbox">
                        <input type="checkbox" id="favori" name="favori" <?php echo $content['Favori'] ? 'checked' : ''; ?>>
                        Ajouter aux favoris
                    </label>
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <button class="button is-link" type="submit">Sauvegarder</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>

