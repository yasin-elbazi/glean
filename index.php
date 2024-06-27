<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glean - Phare dans l'océan des informations</title>
    <!-- On inclut Bulma CSS pour la mise en forme -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <!-- On ajoute la police Google "Inter" pour un style cohérent -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap">
    <!-- Notre propre fichier CSS pour des styles personnalisés -->
    <link rel="stylesheet" href="ressources/css/style.css">
    <style>
        /* Styles personnalisés pour notre page */
        body {
            background-color: #ffffff; /* Fond blanc pour toute la page */
            font-family: 'Inter', sans-serif; /* Utiliser la police Inter pour tout le texte */
            color: #050505; /* Couleur noire par défaut pour le texte */
        }
        .navbar {
            background-color: transparent; /* Barre de navigation invisible */
            box-shadow: none; /* Supprimer l'ombre */
        }
        .navbar-item img {
            max-height: 3rem; /* Ajustez la taille selon vos besoins */
        }
        .navbar-end .navbar-item, .navbar-end .navbar-item a {
            font-family: 'Inter', sans-serif;
            font-weight: 400;
            color: #050505; /* Couleur noire pour le texte */
        }
        .button.is-black {
            background-color: #000000; /* Bouton noir */
            color: #ffffff !important; /* Texte blanc sur le bouton noir */
        }
        .hero.is-dark {
            background-color: transparent; /* Fond transparent pour la section héroïque */
        }
        .hero-body {
            background-color: #f5f5f5; /* Fond gris clair pour la section héroïque */
            color: #050505; /* Couleur noire pour le texte */
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Inter', serif; /* Utiliser la police Inter pour les titres */
            color: #050505; /* Couleur noire pour les titres */
        }
        .section {
            background-color: #ffffff; /* Fond blanc pour toutes les sections */
        }
        .section-features {
            padding: 3rem 0;
        }
        .footer {
            background-color: #ffffff; /* Fond blanc pour le footer */
            padding: 1rem 0;
        }
        .navbar-menu {
            margin-top: 0.5rem; /* Ajout d'une marge pour espacer le bouton du haut de la page */
        }
        .button.is-black:hover {
            color: #ffffff !important; /* Texte blanc sur le bouton noir au survol */
        }
    </style>
</head>
<body>
    <!-- Barre de navigation -->
    <nav class="navbar is-transparent">
        <div class="navbar-brand">
            <!-- Logo de Glean qui redirige vers la page d'accueil -->
            <a class="navbar-item" href="index.php">
                <img src="ressources/images/glean_logo_sfb.png" alt="Glean Logo">
            </a>
            <!-- Burger menu pour navigation mobile -->
            <div class="navbar-burger" data-target="navMenu">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
        <div id="navMenu" class="navbar-menu">
            <div class="navbar-end">
                <!-- Lien vers la démo -->
                <a class="navbar-item" href="demo.php">Accéder à une démo</a>
                <!-- Lien vers la page de connexion -->
                <a class="navbar-item" href="login.php">Connexion</a>
                <div class="navbar-item">
                    <!-- Bouton pour s'inscrire -->
                    <a class="button is-black" href="register.php">Essayer Glean</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Section héroïque -->
    <section class="hero is-dark">
        <div class="hero-body">
            <div class="container has-text-centered">
                <!-- Titre principal de la page d'accueil -->
                <h1 class="title">Bienvenue sur Glean</h1>
                <!-- Sous-titre -->
                <h2 class="subtitle">Votre plateforme de curation de contenu digitale</h2>
                <!-- Boutons pour essayer Glean ou se connecter -->
                <a href="register.php" class="button is-primary is-medium">Essayer Glean</a>
                <a href="login.php" class="button is-secondary is-medium">Connexion</a>
            </div>
        </div>
    </section>

    <!-- Contenu principal -->
    <div class="section section-features">
        <div class="container">
            <!-- Titre de la section des fonctionnalités -->
            <h2 class="title has-text-centered">Pourquoi utiliser Glean ?</h2>
            <div class="columns is-multiline">
                <div class="column is-one-third">
                    <div class="box">
                        <!-- Première fonctionnalité -->
                        <h3 class="subtitle">Organisez vos contenus</h3>
                        <p>Archivez et catégorisez vos articles, vidéos, livres, et plus encore.</p>
                    </div>
                </div>
                <div class="column is-one-third">
                    <div class="box">
                        <!-- Deuxième fonctionnalité -->
                        <h3 class="subtitle">Analysez et synthétisez</h3>
                        <p>Utilisez nos outils d'analyse pour extraire des insights pertinents de vos contenus.</p>
                    </div>
                </div>
                <div class="column is-one-third">
                    <div class="box">
                        <!-- Troisième fonctionnalité -->
                        <h3 class="subtitle">Créez et partagez</h3>
                        <p>Partagez vos découvertes et vos analyses avec une communauté active de curateurs de contenu.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer du site -->
    <footer class="footer">
        <div class="content has-text-centered">
            <p>&copy; 2024 Glean. Tous droits réservés à YS & Saya.</p>
        </div>
    </footer>

    <!-- Script pour le fonctionnement du burger menu -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);
            if ($navbarBurgers.length > 0) {
                $navbarBurgers.forEach(el => {
                    el.addEventListener('click', () => {
                        const target = el.dataset.target;
                        const $target = document.getElementById(target);
                        el.classList.toggle('is-active');
                        $target.classList.toggle('is-active');
                    });
                });
            }
        });
    </script>
</body>
</html>
