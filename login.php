<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
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
            margin-top: 50px;
        }
        .button.is-link {
            background-color: #000000;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <section class="section">
        <div class="container">
            <h1 class="title has-text-centered">Connexion</h1>
            <div class="columns is-centered">
                <div class="column is-half">
                    <form action="login.php" method="post">
                        <div class="field">
                            <label class="label" for="username_or_email">Nom d'utilisateur ou Email :</label>
                            <div class="control">
                                <input class="input" type="text" id="username_or_email" name="username_or_email" required>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label" for="password">Mot de passe :</label>
                            <div class="control">
                                <input class="input" type="password" id="password" name="password" required>
                            </div>
                        </div>
                        <div class="field">
                            <div class="control">
                                <button class="button is-link" type="submit">Connexion</button>
                            </div>
                        </div>
                    </form>
                    <p class="has-text-centered">Vous n'avez pas de compte ? <a href="register.php">Inscrivez-vous ici</a></p>
                    <p class="has-text-centered"><a href="index.php">Retour à l'accueil</a></p>
                </div>
            </div>
        </div>
    </section>
</body>
</html>

<?php
include 'connexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_or_email = $_POST['username_or_email'];
    $password = $_POST['password'];

    // Vérifier si l'entrée est un email ou un nom d'utilisateur
    if (filter_var($username_or_email, FILTER_VALIDATE_EMAIL)) {
        // Entrée est une adresse email
        $sql = "SELECT * FROM Utilisateur WHERE Email = :username_or_email";
    } else {
        // Entrée est un nom d'utilisateur
        $sql = "SELECT * FROM Utilisateur WHERE Nom = :username_or_email";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username_or_email' => $username_or_email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['Mot_de_passe'])) {
        // Créer un jeton de session
        $session_token = bin2hex(random_bytes(16));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $sql = "INSERT INTO Sessions (User_ID, Session_Token, Expiry) VALUES (:user_id, :session_token, :expiry)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $user['ID_Utilisateur'],
            'session_token' => $session_token,
            'expiry' => $expiry
        ]);

        setcookie('session_token', $session_token, time() + 3600, "/");
        header("Location: workspace.php");
        exit();
    } else {
        echo "<p class='has-text-centered has-text-danger'>Nom d'utilisateur/email ou mot de passe invalide.</p>";
    }
}
?>