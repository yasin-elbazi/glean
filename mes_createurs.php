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

// Ajouter un nouveau créateur de contenu ou associer un créateur existant à l'utilisateur
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $existing_creator_id = $_POST['creator'];
    $new_creator = $_POST['new_creator'];
    $about = $_POST['about'];
    $image = $_POST['image'];
    $biographie = isset($_POST['biographie']) ? 1 : 0;
    $description = $_POST['description'];
    $chaine_site = isset($_POST['chaine_site']) ? 1 : 0;
    $date_naissance = $_POST['date_naissance'] ?: null;
    $date_deces = $_POST['date_deces'] ?: null;
    $url = $_POST['url'];

    if (!empty($new_creator)) {
        // Ajouter un nouveau créateur
        $sql = "INSERT INTO Createur (Nom_Createur, About, Image, Biographie, Description, Chaine_site, Date_naissance, Date_deces, URL) 
                VALUES (:new_creator, :about, :image, :biographie, :description, :chaine_site, :date_naissance, :date_deces, :url)";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([
                'new_creator' => $new_creator,
                'about' => $about,
                'image' => $image,
                'biographie' => $biographie,
                'description' => $description,
                'chaine_site' => $chaine_site,
                'date_naissance' => $date_naissance,
                'date_deces' => $date_deces,
                'url' => $url
            ]);
            $existing_creator_id = $pdo->lastInsertId();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit();
        }
    }

    // Associer le créateur à l'utilisateur
    if ($existing_creator_id) {
        $sql = "INSERT INTO UtilisateurCreateur (ID_Utilisateur, ID_Createur) VALUES (:user_id, :creator_id)";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([
                'user_id' => $user_id,
                'creator_id' => $existing_creator_id
            ]);
            echo "Créateur de contenu associé avec succès !";
        } catch (PDOException $e) {
            echo "Erreur: " . $e->getMessage();
        }
    }
}

// Récupérer la liste des créateurs de contenu de l'utilisateur connecté
$sql = "SELECT cr.ID_Createur, cr.Nom_Createur, cr.About, cr.Image 
        FROM Createur cr 
        JOIN UtilisateurCreateur uc ON cr.ID_Createur = uc.ID_Createur 
        WHERE uc.ID_Utilisateur = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$createurs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Créateurs de Contenus - Glean</title>
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
            <h1 class="title">Mes Créateurs de Contenus</h1>
            <p>Ici, vous pouvez voir et gérer tous vos créateurs de contenus préférés.</p>

            <!-- Formulaire pour ajouter un nouveau créateur de contenu -->
            <form action="mes_createurs.php" method="post" class="box">
                <div class="field">
                    <label class="label" for="creator">Sélectionner un Créateur de Contenu:</label>
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
                    <label class="label" for="about">À propos:</label>
                    <div class="control">
                        <textarea class="textarea" id="about" name="about"></textarea>
                    </div>
                </div>
                <div class="field">
                    <label class="checkbox">
                        <input type="checkbox" name="biographie">
                        Cocher si c'est une biographie
                    </label>
                </div>
                <div class="field">
                    <label class="label" for="description">Description:</label>
                    <div class="control">
                        <textarea class="textarea" id="description" name="description"></textarea>
                    </div>
                </div>
                <div class="field">
                    <label class="checkbox">
                        <input type="checkbox" name="chaine_site">
                        Cocher si c'est une chaine ou un site
                    </label>
                </div>
                <div class="field">
                    <label class="label" for="date_naissance">Date de Naissance:</label>
                    <div class="control">
                        <input class="input" type="date" id="date_naissance" name="date_naissance">
                    </div>
                </div>
                <div class="field">
                    <label class="label" for="date_deces">Date de Décès:</label>
                    <div class="control">
                        <input class="input" type="date" id="date_deces" name="date_deces">
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
                        <button class="button is-link" type="submit">Ajouter/Associer Créateur</button>
                    </div>
                </div>
            </form>

            <!-- Liste des créateurs de contenu -->
            <h2 class="title">Liste des Créateurs de Contenus</h2>
            <table class="table is-fullwidth is-striped">
                <thead>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>À propos</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($createurs as $createur): ?>
                    <tr>
                        <td>
                            <?php if ($createur['Image']): ?>
                                <img src="<?php echo htmlspecialchars($createur['Image']); ?>" alt="Image du créateur" width="100">
                            <?php else: ?>
                                <span>N/A</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($createur['Nom_Createur']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($createur['About'])); ?></td>
                        <td>
                            <a href="edit_createur.php?id=<?php echo $createur['ID_Createur']; ?>" class="button is-small is-link">Modifier</a>
                            <a href="delete_createur.php?id=<?php echo $createur['ID_Createur']; ?>" class="button is-small is-danger">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>