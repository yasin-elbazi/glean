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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $creator_id = $_POST['creator'] ? $_POST['creator'] : null;
    $new_creator = $_POST['new_creator'];
    $type = $_POST['type'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $url = isset($_POST['url']) ? $_POST['url'] : '';
    $image = isset($_POST['image']) ? $_POST['image'] : '';

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

    $sql = "INSERT INTO Contenu (ID_Createur, ID_Utilisateur, Type_de_contenu, Titre_Contenu, Description_contenu, URL, Image_contenu) 
            VALUES (:creator_id, :user_id, :type, :title, :description, :url, :image)";
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute([
            'creator_id' => $creator_id,
            'user_id' => $user_id,
            'type' => $type,
            'title' => $title,
            'description' => $description,
            'url' => $url,
            'image' => $image
        ]);
        echo "Nouveau contenu ajouté avec succès !";
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
    }
}

// Récupérer la liste des contenus de l'utilisateur connecté
$sql = "SELECT c.ID_Contenu, c.Titre_Contenu, c.Description_contenu, c.URL, c.Image_contenu, c.Type_de_contenu, 
               c.Etat_d_achevement, c.Date_modification, c.Score_personnel, c.Score_critique, 
               c.Date_publication, c.Date_visionnage, c.Favori, cr.Nom_Createur 
        FROM Contenu c 
        LEFT JOIN Createur cr ON c.ID_Createur = cr.ID_Createur
        WHERE c.ID_Utilisateur = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$contents = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Contenus - Glean</title>
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
        .sidebar {
            background-color: #f5f5f5;
            padding: 1rem;
            height: 100vh;
        }
        .sidebar a {
            display: block;
            padding: 0.75rem 1rem;
            color: #000000;
            text-decoration: none;
            margin-bottom: 1rem;
        }
        .sidebar a:hover {
            background-color: #e5e5e5;
        }
        .content {
            padding: 2rem;
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
    <div class="columns">
        <div class="column is-three-quarters content">
            <h1 class="title">Mes Contenus</h1>
            <p>Ici, vous pouvez ajouter, voir, modifier et gérer tous vos contenus sauvegardés.</p>

            <!-- Formulaire pour ajouter du contenu -->
            <form action="mes_contenus.php" method="post" class="box">
                <div class="field">
                    <label class="label" for="creator">Créateur de Contenu:</label>
                    <div class="control">
                        <div class="select">
                            <select id="creator" name="creator">
                                <option value="">Aucun</option>
                                <?php
                                $sql = "SELECT ID_Createur, Nom_Createur FROM Createur";
                                $stmt = $pdo->query($sql);
                                while ($row = $stmt->fetch()) {
                                    echo "<option value='{$row['ID_Createur']}'>{$row['Nom_Createur']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <label class="label" for="new_creator">Ou Ajouter un Nouveau Créateur de Contenu :</label>
                    <div class="control">
                        <input class="input" type="text" id="new_creator" name="new_creator">
                    </div>
                </div>
                <div class="field">
                    <label class="label" for="type">Type du contenu:</label>
                    <div class="control">
                        <div class="select">
                            <select id="type" name="type" required>
                                <option value="article">Article</option>
                                <option value="video">Vidéo</option>
                                <option value="livre">Livre</option>
                                <option value="film">Film</option>
                                <option value="serie">Série</option>
                                <option value="documentaire">Documentaire</option>
                                <option value="citation">Citation</option>
                                <option value="divers">Divers</option>
                                <option value="formation">Formation</option>
                                <option value="podcast">Podcast</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <label class="label" for="title">Titre:</label>
                    <div class="control">
                        <input class="input" type="text" id="title" name="title" required>
                    </div>
                </div>
                <div class="field">
                    <label class="label" for="description">Description:</label>
                    <div class="control">
                        <textarea class="textarea" id="description" name="description" required></textarea>
                    </div>
                </div>
                <div class="field">
                    <label class="label" for="url">URL:</label>
                    <div class="control">
                        <input class="input" type="url" id="url" name="url">
                    </div>
                </div>
                <div class="field">
                    <label class="label" for="image">Image URL:</label>
                    <div class="control">
                        <input class="input" type="url" id="image" name="image">
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <button class="button is-link" type="submit">Ajouter Contenu</button>
                    </div>
                </div>
            </form>

            <!-- Liste des contenus existants -->
            <h2 class="title">Mes Contenus</h2>
            <table class="table is-fullwidth is-striped">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Type</th>
                        <th>État</th>
                        <th>Créateur</th>
                        <th>URL</th>
                        <th>Dernière modification</th>
                        <th>Score personnel</th>
                        <th>Score critique</th>
                        <th>Publication</th>
                        <th>Visionnage</th>
                        <th>Favori</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contents as $content): ?>
                    <tr>
                        <td><?php echo $content['Image_contenu'] ? "<img src='".htmlspecialchars($content['Image_contenu'])."' alt='Image de contenu' width='100'>" : "N/A"; ?></td>
                        <td><?php echo htmlspecialchars($content['Titre_Contenu']); ?></td>
                        <td><?php echo htmlspecialchars($content['Description_contenu']); ?></td>
                        <td><?php echo htmlspecialchars($content['Type_de_contenu']); ?></td>
                        <td><?php echo htmlspecialchars($content['Etat_d_achevement']); ?></td>
                        <td><?php echo htmlspecialchars($content['Nom_Createur'] ? $content['Nom_Createur'] : "N/A"); ?></td>
                        <td><a href="<?php echo htmlspecialchars($content['URL']); ?>" target="_blank">Voir</a></td>
                        <td><?php echo htmlspecialchars($content['Date_modification']); ?></td>
                        <td><?php echo htmlspecialchars($content['Score_personnel']); ?></td>
                        <td><?php echo htmlspecialchars($content['Score_critique']); ?></td>
                        <td><?php echo htmlspecialchars($content['Date_publication']); ?></td>
                        <td><?php echo htmlspecialchars($content['Date_visionnage']); ?></td>
                        <td><?php echo $content['Favori'] ? 'Oui' : 'Non'; ?></td>
                        <td>
                            <a href="edit_content.php?id=<?php echo $content['ID_Contenu']; ?>" class="button is-small is-link">Modifier</a>
                            <a href="delete_content.php?id=<?php echo $content['ID_Contenu']; ?>" class="button is-small is-danger">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
