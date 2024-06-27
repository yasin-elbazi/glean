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

// Récupérer les notes épinglées
$sql = "SELECT n.ID_Note, n.Titre_Note, n.Texte_de_Note, n.Date_creation, n.Derniere_modification, u.Nom AS Utilisateur, c.Titre_Contenu, e.ID_Epingles, n.ID_Utilisateur AS Original_User_ID
        FROM Epingles e
        JOIN Note n ON e.ID_Note = n.ID_Note
        JOIN Utilisateur u ON n.ID_Utilisateur = u.ID_Utilisateur
        LEFT JOIN Contenu c ON n.ID_Contenu = c.ID_Contenu
        WHERE e.ID_Utilisateur = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$notes = $stmt->fetchAll();

// Gérer la suppression des épingles
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_epingle'])) {
    $epingle_id = $_POST['epingle_id'];

    $sql = "DELETE FROM Epingles WHERE ID_Epingles = :epingle_id";
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute(['epingle_id' => $epingle_id]);
        echo "Épinglage supprimé avec succès !";
        header('Location: notes_epinglees.php'); // Rafraîchir la page
        exit();
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
    }
}

// Gérer la copie des notes épinglées
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['copy_note'])) {
    $note_id = $_POST['note_id'];

    // Récupérer la note à copier
    $sql = "SELECT * FROM Note WHERE ID_Note = :note_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['note_id' => $note_id]);
    $note = $stmt->fetch();

    if ($note) {
        // Copier la note dans les notes de l'utilisateur
        $sql = "INSERT INTO Note (ID_Utilisateur, ID_Contenu, Titre_Note, Texte_de_Note, Date_creation, Derniere_modification, Partage) 
                VALUES (:user_id, :content_id, :title, :text, NOW(), NOW(), 0)";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([
                'user_id' => $user_id,
                'content_id' => $note['ID_Contenu'],
                'title' => $note['Titre_Note'],
                'text' => $note['Texte_de_Note']
            ]);
            echo "Note copiée avec succès !";
            header('Location: notes_epinglees.php'); // Rafraîchir la page
            exit();
        } catch (PDOException $e) {
            echo "Erreur: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes Épinglées - Glean</title>
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
        .media-content .title {
            font-weight: 700;
            font-size: 1.25rem;
        }
        .media-content .subtitle {
            font-weight: 400;
            font-size: 1rem;
        }
        .media-content .content {
            margin-top: 0.5rem;
        }
    </style>
    <!-- Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <!-- Quill JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
</head>
<body>
    <div class="container content">
        <h1 class="title">Notes Épinglées</h1>
        <p>Ici, vous pouvez voir les notes que vous avez épinglées et les gérer.</p>

        <!-- Liste des notes épinglées -->
        <div class="columns is-multiline">
            <?php foreach ($notes as $note): ?>
                <div class="column is-half">
                    <div class="box">
                        <article class="media">
                            <div class="media-content">
                                <div class="content">
                                    <p>
                                        <strong><?php echo htmlspecialchars($note['Titre_Note']); ?></strong> 
                                        <br>
                                        <span class="subtitle">Créée par : <?php echo htmlspecialchars($note['Utilisateur']); ?></span>
                                        <br>
                                        <span class="tag"><?php echo htmlspecialchars($note['Titre_Contenu']); ?></span>
                                        <br>
                                        <div class="note-content">
                                            <?php echo $note['Texte_de_Note']; ?>
                                        </div>
                                    </p>
                                    <form action="notes_epinglees.php" method="post">
                                        <input type="hidden" name="epingle_id" value="<?php echo $note['ID_Epingles']; ?>">
                                        <button class="button is-small is-danger" type="submit" name="delete_epingle">Retirer l'épingle</button>
                                    </form>
                                    <?php if ($note['Original_User_ID'] != $user_id): ?>
                                    <form action="notes_epinglees.php" method="post">
                                        <input type="hidden" name="note_id" value="<?php echo $note['ID_Note']; ?>">
                                        <button class="button is-small is-link" type="submit" name="copy_note">Copier et Modifier</button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.note-content').forEach(function(div) {
                var quill = new Quill(div, {
                    readOnly: true,
                    theme: 'bubble'
                });
                quill.clipboard.dangerouslyPasteHTML(div.innerHTML);
            });
        });
    </script>
</body>
</html>