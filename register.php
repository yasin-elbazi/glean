<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
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
            <h1 class="title has-text-centered">Inscription</h1>
            <div class="columns is-centered">
                <div class="column is-half">
                    <form action="register.php" method="post">
                        <div class="field">
                            <label class="label" for="name">Nom :</label>
                            <div class="control">
                                <input class="input" type="text" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label" for="email">Email :</label>
                            <div class="control">
                                <input class="input" type="email" id="email" name="email" required>
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
                                <button class="button is-link" type="submit">Inscription</button>
                            </div>
                        </div>
                    </form>
                    <p class="has-text-centered">Vous avez déjà un compte ? <a href="login.php">Se connecter ici</a></p>
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
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO Utilisateur (Nom, Email, Mot_de_passe) VALUES (:name, :email, :password)";
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute(['name' => $name, 'email' => $email, 'password' => $password]);
        echo "<p class='has-text-centered has-text-success'>Nouvel utilisateur enregistré avec succès. <a href='login.php'>Se connecter ici</a></p>";
    } catch (PDOException $e) {
        echo "<p class='has-text-centered has-text-danger'>Erreur: " . $e->getMessage() . "</p>";
    }
}
?>