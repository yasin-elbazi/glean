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

$note_id = $_GET['id'];
$sql = "SELECT * FROM Note WHERE ID_Note = :note_id AND ID_Utilisateur = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['note_id' => $note_id, 'user_id' => $user_id]);
$note = $stmt->fetch();

if (!$note) {
    echo "Note non trouvée.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $content_id = $_POST['content'] ?: null; // Permettre NULL si non sélectionné
    $title = $_POST['title'];
    $text = $_POST['text'];
    $share = isset($_POST['share']) ? 1 : 0;
    $date_modification = date('Y-m-d H:i:s');

    // Mise à jour de la note
    $sql = "UPDATE Note SET Titre_Note = :title, Texte_de_Note = :text, Derniere_modification = :date_modification, ID_Contenu = :content_id, Partage = :share WHERE ID_Note = :note_id";
    $stmt = $pdo->prepare($sql);
    $params = [
        'title' => $title,
        'text' => $text,
        'date_modification' => $date_modification,
        'content_id' => $content_id,
        'share' => $share,
        'note_id' => $note_id
    ];

    try {
        $stmt->execute($params);
        header("Location: mes_notes.php");
        exit();
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
    }
}

// Récupérer la liste des contenus de l'utilisateur connecté
$sql = "SELECT ID_Contenu, Titre_Contenu FROM Contenu WHERE ID_Utilisateur = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$contenus = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Note - Glean</title>
    <!-- Bulma CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
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
            <h1 class="title">Détails de la Note</h1>

            <form action="note_details.php?id=<?php echo $note['ID_Note']; ?>" method="post" class="box">
                <div class="field">
                    <label class="label" for="content">Contenu (optionnel):</label>
                    <div class="control">
                        <div class="select">
                            <select id="content" name="content">
                                <option value="">Aucun</option>
                                <?php foreach ($contenus as $contenu): ?>
                                    <option value="<?php echo $contenu['ID_Contenu']; ?>" <?php echo $contenu['ID_Contenu'] == $note['ID_Contenu'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($contenu['Titre_Contenu']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <label class="label" for="title">Titre de Note :</label>
                    <div class="control">
                        <input class="input" type="text" id="title" name="title" value="<?php echo htmlspecialchars($note['Titre_Note']); ?>" required>
                    </div>
                </div>
                <div class="field">
                    <label class="label" for="text">Note :</label>
                    <div id="editor-container" style="height: 200px;"><?php echo htmlspecialchars_decode($note['Texte_de_Note']); ?></div>
                    <textarea name="text" id="text" class="is-hidden"><?php echo htmlspecialchars($note['Texte_de_Note']); ?></textarea>
                </div>
                <div class="field">
                    <div class="control">
                        <label class="checkbox">
                            <input type="checkbox" name="share" <?php echo $note['Partage'] ? 'checked' : ''; ?>>
                            Partager cette note
                        </label>
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <button class="button is-link" type="submit">Sauvegarder</button>
                    </div>
                </div>
            </form>

            <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
            <script>
                var quill = new Quill('#editor-container', {
                    theme: 'snow'
                });

                document.querySelector('form').onsubmit = function() {
                    document.querySelector('textarea[name=text]').value = quill.root.innerHTML;
                };
            </script>
        </div>
    </div>
</body>
</html>