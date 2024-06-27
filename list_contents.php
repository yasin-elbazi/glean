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

// Récupérer la liste des contenus de l'utilisateur connecté
$sql = "SELECT c.ID_Contenu, c.Titre_Contenu, c.Description_contenu, c.URL, c.Image_contenu, c.Type_de_contenu, 
               c.Etat_d_achevement, c.Date_modification, c.Score_personnel, c.Score_critique, 
               c.Date_publication, c.Date_visionnage, c.Favori, cr.Nom_Createur 
        FROM Contenu c 
        JOIN Createur cr ON c.ID_Createur = cr.ID_Createur
        WHERE c.ID_Utilisateur = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$contents = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mes Contenus - Glean</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Mes Contenus</h1>
        </div>
    </header>
    <div class="container">
        <aside>
            <nav>
                <ul>
                    <li><a href="workspace.php">Mon Labo</a></li>
                    <li><a href="manage_notes.php">Mes Notes</a></li>
                    <li><a href="list_contents.php">Mes Contenus</a></li>
                    <li><a href="list_createurs.php">Mes Créateurs de Contenus</a></li>
                </ul>
            </nav>
        </aside>
        <main>
            <h2>Gérer mes contenus</h2>
            <p>Ici, vous pouvez voir, modifier et gérer tous vos contenus sauvegardés.</p>
            <table>
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
                <?php foreach ($contents as $content): ?>
                <tr>
                    <td><img src="<?php echo htmlspecialchars($content['Image_contenu']); ?>" alt="Image de contenu" width="100"></td>
                    <td><?php echo htmlspecialchars($content['Titre_Contenu']); ?></td>
                    <td><?php echo htmlspecialchars($content['Description_contenu']); ?></td>
                    <td><?php echo htmlspecialchars($content['Type_de_contenu']); ?></td>
                    <td><?php echo htmlspecialchars($content['Etat_d_achevement']); ?></td>
                    <td><?php echo htmlspecialchars($content['Nom_Createur']); ?></td>
                    <td><a href="<?php echo htmlspecialchars($content['URL']); ?>" target="_blank">Voir</a></td>
                    <td><?php echo htmlspecialchars($content['Date_modification']); ?></td>
                    <td><?php echo htmlspecialchars($content['Score_personnel']); ?></td>
                    <td><?php echo htmlspecialchars($content['Score_critique']); ?></td>
                    <td><?php echo htmlspecialchars($content['Date_publication']); ?></td>
                    <td><?php echo htmlspecialchars($content['Date_visionnage']); ?></td>
                    <td><?php echo $content['Favori'] ? 'Oui' : 'Non'; ?></td>
                    <td>
                        <a href="edit_content.php?id=<?php echo $content['ID_Contenu']; ?>">Modifier</a>
                        <a href="delete_content.php?id=<?php echo $content['ID_Contenu']; ?>">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </main>
    </div>
</body>
</html>