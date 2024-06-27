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

// Récupérer la liste des créateurs de contenu de l'utilisateur connecté
$sql = "SELECT cr.ID_Createur, cr.Nom_Createur, cr.About, cr.Image 
        FROM Createur cr 
        JOIN Contenu c ON cr.ID_Createur = c.ID_Createur 
        WHERE c.ID_Utilisateur = :user_id 
        GROUP BY cr.ID_Createur";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$createurs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mes Créateurs de Contenus - Glean</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>Mes Créateurs de Contenus</h1>
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
            <h2>Gérer mes créateurs de contenus</h2>
            <p>Ici, vous pouvez voir et gérer tous vos créateurs de contenus préférés.</p>
            <table>
                <tr>
                    <th>Image</th>
                    <th>Nom</th>
                    <th>À propos</th>
                </tr>
                <?php foreach ($createurs as $createur): ?>
                <tr>
                    <td><img src="<?php echo htmlspecialchars($createur['Image']); ?>" alt="Image du créateur" width="100"></td>
                    <td><?php echo htmlspecialchars($createur['Nom_Createur']); ?></td>
                    <td><?php echo htmlspecialchars($createur['About']); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </main>
    </div>
</body>
</html>