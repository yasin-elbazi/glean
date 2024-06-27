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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord</title>
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
    </style>
</head>
<body>
    <div class="columns">
        <div class="column is-three-quarters content">
            <h1 class="title">Bienvenue dans votre espace de travail</h1>
            <p>Cet espace vous permet de gérer et organiser vos notes, contenus, et créateurs de contenus préférés.</p>
            <div class="columns is-multiline">
                <div class="column is-one-third">
                    <div class="box">
                        <h2 class="subtitle">Mes Notes</h2>
                        <p>Accédez à toutes vos notes personnelles.</p>
                        <a href="mes_notes.php" class="button is-link">Voir Mes Notes</a>
                    </div>
                </div>
                <div class="column is-one-third">
                    <div class="box">
                        <h2 class="subtitle">Mes Contenus</h2>
                        <p>Consultez et gérez vos contenus favoris.</p>
                        <a href="mes_contenus.php" class="button is-link">Voir Mes Contenus</a>
                    </div>
                </div>
                <div class="column is-one-third">
                    <div class="box">
                        <h2 class="subtitle">Mes Créateurs de Contenus</h2>
                        <p>Gérez vos créateurs de contenus préférés.</p>
                        <a href="mes_createurs.php" class="button is-link">Voir Mes Créateurs</a>
                    </div>
                </div>
                <div class="column is-one-third">
                    <div class="box">
                        <h2 class="subtitle">Notes Partagées</h2>
                        <p>Découvrez les notes partagées par d'autres utilisateurs.</p>
                        <a href="notes_partagees.php" class="button is-link">Voir Notes Partagées</a>
                    </div>
                </div>
                <div class="column is-one-third">
                    <div class="box">
                        <h2 class="subtitle">Notes Épinglées</h2>
                        <p>Accédez à vos notes épinglées.</p>
                        <a href="notes_epinglees.php" class="button is-link">Voir Notes Épinglées</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>