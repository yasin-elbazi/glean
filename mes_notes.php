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
    $note_id = $_POST['note_id'];
    $content_id = $_POST['content'] ?: null; // Permettre NULL si non sélectionné
    $title = $_POST['title'];
    $text = $_POST['text'];
    $share = isset($_POST['share']) ? 1 : 0;
    $date_modification = date('Y-m-d H:i:s');

    if (empty($note_id)) {
        // Nouvelle note
        $sql = "INSERT INTO Note (ID_Utilisateur, ID_Contenu, Titre_Note, Texte_de_Note, Date_creation, Derniere_modification, Partage) 
                VALUES (:user_id, :content_id, :title, :text, :date_modification, :date_modification, :share)";
        $stmt = $pdo->prepare($sql);
        $params = [
            'user_id' => $user_id,
            'content_id' => $content_id,
            'title' => $title,
            'text' => $text,
            'date_modification' => $date_modification,
            'share' => $share
        ];
    } else {
        // Mise à jour de la note
        $sql = "UPDATE Note SET Titre_Note=:title, Texte_de_Note=:text, Derniere_modification=:date_modification, ID_Contenu=:content_id, Partage=:share WHERE ID_Note=:note_id";
        $stmt = $pdo->prepare($sql);
        $params = [
            'title' => $title,
            'text' => $text,
            'date_modification' => $date_modification,
            'content_id' => $content_id,
            'share' => $share,
            'note_id' => $note_id
        ];
    }

    try {
        $stmt->execute($params);
        echo "Note sauvegardée avec succès";
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
    }
}

// Récupérer la liste des notes de l'utilisateur connecté
$sql = "SELECT n.*, c.Titre_Contenu FROM Note n LEFT JOIN Contenu c ON n.ID_Contenu = c.ID_Contenu WHERE n.ID_Utilisateur = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$notes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Notes - Glean</title>
    <!-- Bulma CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <!-- Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
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
        .ql-container {
            height: auto;
        }
        .note-box {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="columns">
        <div class="column is-three-quarters content">
            <h1 class="title">Mes Notes</h1>
            <h3 class="title">Ajout d'une Nouvelle Note</h3>
            <p>Ici, vous pouvez ajouter, éditer et supprimer vos notes.</p>

            <!-- Formulaire pour ajouter/modifier une note -->
            <form action="mes_notes.php" method="post" class="box">
                <input type="hidden" id="note_id" name="note_id">
                <div class="field">
                    <label class="label" for="content">Contenu (optionnel):</label>
                    <div class="control">
                        <div class="select">
                            <select id="content" name="content">
                                <option value="">Aucun</option>
                                <?php
                                $sql = "SELECT ID_Contenu, Titre_Contenu FROM Contenu WHERE ID_Utilisateur = :user_id";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute(['user_id' => $user_id]);
                                while ($row = $stmt->fetch()) {
                                    echo "<option value='{$row['ID_Contenu']}'>{$row['Titre_Contenu']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field">
                    <label class="label" for="title">Titre de Note :</label>
                    <div class="control">
                        <input class="input" type="text" id="title" name="title" required>
                    </div>
                </div>
                <div class="field">
                    <label class="label" for="text">Note :</label>
                    <div id="editor-container" style="height: 200px;"></div>
                    <textarea name="text" id="text" class="is-hidden"></textarea>
                </div>
                <div class="field">
                    <div class="control">
                        <label class="checkbox">
                            <input type="checkbox" name="share">
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

            <!-- Liste des notes existantes -->
            <h2 class="title">Mes Notes</h2>
            <div class="columns is-multiline">
                <?php foreach ($notes as $note): ?>
                <div class="column is-one-third note-box">
                    <div class="box">
                        <article class="media">
                            <div class="media-content">
                                <div class="content">
                                    <p>
                                        <strong><?php echo htmlspecialchars($note['Titre_Note']); ?></strong>
                                        <br>
                                        <?php if ($note['Titre_Contenu']): ?>
                                            <small><?php echo htmlspecialchars($note['Titre_Contenu']); ?></small>
                                            <br>
                                        <?php endif; ?>
                                        <div class="note-content">
                                            <?php
                                            $note_preview = strip_tags($note['Texte_de_Note']);
                                            if (strlen($note_preview) > 150) {
                                                $note_preview = substr($note_preview, 0, 150) . '...';
                                            }
                                            echo $note_preview;
                                            ?>
                                        </div>
                                    </p>
                                </div>
                                <nav class="level is-mobile">
                                    <div class="level-left buttons">
                                        <a href="note_details.php?id=<?php echo $note['ID_Note']; ?>" class="button is-small is-link">Voir / Modifier</a>
                                        <a href="delete_note.php?id=<?php echo $note['ID_Note']; ?>" class="button is-small is-danger">Supprimer</a>
                                    </div>
                                </nav>
                            </div>
                        </article>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
            <script>
                var quill = new Quill('#editor-container', {
                    theme: 'snow'
                });

                document.querySelector('form').onsubmit = function() {
                    document.querySelector('textarea[name=text]').value = quill.root.innerHTML;
                };

                function editNote(id, title, text, content, share) {
                    document.getElementById('note_id').value = id;
                    document.getElementById('title').value = title;
                    quill.root.innerHTML = text;
                    document.getElementById('content').value = content;
                    document.querySelector('input[name="share"]').checked = share == 1 ? true : false;
                }

                document.querySelectorAll('.note-content').forEach(function(div) {
                    div.innerHTML = marked(div.textContent);
                });
            </script>
        </div>
    </div>
</body>
</html>