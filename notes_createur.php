<?php
include 'connexion.php';
include 'navbar.php';

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
$createur_id = $_GET['id'];

// Récupérer les notes associées aux contenus créés par le créateur de contenu sélectionné
$sql = "SELECT n.* 
        FROM Note n
        JOIN Contenu c ON n.ID_Contenu = c.ID_Contenu
        WHERE c.ID_Createur = :createur_id AND n.ID_Utilisateur = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['createur_id' => $createur_id, 'user_id' => $user_id]);
$notes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes pour le créateur de contenu</title>
    <!-- Bulma CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <!-- Google Fonts: Inter -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap">
    <!-- Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
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
        .note-content {
            border-bottom: 1px solid #ddd;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="columns">
        <div class="column is-three-quarters content">
            <h1 class="title">Notes concernant le Créateur de Contenu</h1>
            <div class="content">
                <?php foreach ($notes as $note): ?>
                    <div class="note-content">
                        <h2><?php echo htmlspecialchars($note['Titre_Note']); ?></h2>
                        <div class="quill-content"><?php echo $note['Texte_de_Note']; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Quill JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quillContents = document.querySelectorAll('.quill-content');
            quillContents.forEach(function(element) {
                const quill = new Quill(element, {
                    theme: 'snow',
                    readOnly: true,
                    modules: {
                        toolbar: false
                    }
                });
                quill.setContents(JSON.parse(element.innerText));
            });
        });
    </script>
</body>
</html>