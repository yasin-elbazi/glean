<?php
include 'connexion.php';
include 'navbar.php';  // On inclut la barre de navigation

// Vérifie si l'utilisateur est connecté
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

// Récupère les contenus de l'utilisateur
$sql = "SELECT * FROM Contenu WHERE ID_Utilisateur = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$contenus = $stmt->fetchAll();

// Récupère les créateurs de l'utilisateur
$sql = "SELECT cr.* FROM Createur cr
        JOIN UtilisateurCreateur uc ON cr.ID_Createur = uc.ID_Createur
        WHERE uc.ID_Utilisateur = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$createurs = $stmt->fetchAll();

// Récupère les biographies
$sql = "SELECT * FROM Createur WHERE Biographie = 1 AND ID_Createur IN (SELECT ID_Createur FROM UtilisateurCreateur WHERE ID_Utilisateur = :user_id)";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$biographies = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Glean</title>
    <!-- On inclut Bulma CSS pour la mise en forme -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <style>
        body {
            background-color: #ffffff;
            font-family: 'Inter', sans-serif;
            color: #050505;
        }
        .content {
            padding: 2rem;
        }
        .carousel {
            margin-bottom: 2rem;
        }
        .carousel .title {
            margin-bottom: 1rem;
        }
        .box {
            height: 100%;
        }
        .media-content a {
            color: inherit;
            text-decoration: none;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabs = document.querySelectorAll('.tabs li');
            const contents = document.querySelectorAll('.tab-content');

            tabs.forEach((tab, index) => {
                tab.addEventListener('click', () => {
                    tabs.forEach(item => item.classList.remove('is-active'));
                    tab.classList.add('is-active');

                    contents.forEach(content => content.classList.add('is-hidden'));
                    contents[index].classList.remove('is-hidden');
                });
            });
        });
    </script>
</head>
<body>
    <div class="content">
        <h1 class="title">Dashboard</h1>

        <!-- Onglets -->
        <div class="tabs is-boxed">
            <ul>
                <li class="is-active"><a>Livres</a></li>
                <li><a>Citations</a></li>
                <li><a>Articles</a></li>
                <li><a>Vidéos</a></li>
                <li><a>Podcasts</a></li>
                <li><a>Films & Séries</a></li>
                <li><a>Documentaires</a></li>
                <li><a>Biographies</a></li>
                <li><a>Personnalités Influentes</a></li>
            </ul>
        </div>

        <!-- Contenu des onglets -->
        <div class="tab-content">
            <div class="columns is-multiline">
                <?php foreach ($contenus as $contenu): ?>
                    <?php if ($contenu['Type_de_contenu'] == 'livre'): ?>
                        <div class="column is-one-quarter">
                            <div class="box">
                                <article class="media">
                                    <div class="media-left">
                                        <?php if ($contenu['Image_contenu']): ?>
                                            <figure class="image is-64x64">
                                                <img src="<?php echo htmlspecialchars($contenu['Image_contenu']); ?>" alt="Image">
                                            </figure>
                                        <?php endif; ?>
                                    </div>
                                    <div class="media-content">
                                        <div class="content">
                                            <p>
                                                <a href="notes_contenu.php?id=<?php echo $contenu['ID_Contenu']; ?>">
                                                    <strong><?php echo htmlspecialchars($contenu['Titre_Contenu']); ?></strong>
                                                </a>
                                                <br>
                                                <?php echo htmlspecialchars($contenu['Description_contenu']); ?>
                                            </p>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="tab-content is-hidden">
            <div class="columns is-multiline">
                <?php foreach ($contenus as $contenu): ?>
                    <?php if ($contenu['Type_de_contenu'] == 'citation'): ?>
                        <div class="column is-one-quarter">
                            <div class="box">
                                <article class="media">
                                    <div class="media-left">
                                        <?php if ($contenu['Image_contenu']): ?>
                                            <figure class="image is-64x64">
                                                <img src="<?php echo htmlspecialchars($contenu['Image_contenu']); ?>" alt="Image">
                                            </figure>
                                        <?php endif; ?>
                                    </div>
                                    <div class="media-content">
                                        <div class="content">
                                            <p>
                                                <a href="notes_contenu.php?id=<?php echo $contenu['ID_Contenu']; ?>">
                                                    <strong><?php echo htmlspecialchars($contenu['Titre_Contenu']); ?></strong>
                                                </a>
                                                <br>
                                                <?php echo htmlspecialchars($contenu['Description_contenu']); ?>
                                            </p>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="tab-content is-hidden">
            <div class="columns is-multiline">
                <?php foreach ($contenus as $contenu): ?>
                    <?php if ($contenu['Type_de_contenu'] == 'article'): ?>
                        <div class="column is-one-quarter">
                            <div class="box">
                                <article class="media">
                                    <div class="media-left">
                                        <?php if ($contenu['Image_contenu']): ?>
                                            <figure class="image is-64x64">
                                                <img src="<?php echo htmlspecialchars($contenu['Image_contenu']); ?>" alt="Image">
                                            </figure>
                                        <?php endif; ?>
                                    </div>
                                    <div class="media-content">
                                        <div class="content">
                                            <p>
                                                <a href="notes_contenu.php?id=<?php echo $contenu['ID_Contenu']; ?>">
                                                    <strong><?php echo htmlspecialchars($contenu['Titre_Contenu']); ?></strong>
                                                </a>
                                                <br>
                                                <?php echo htmlspecialchars($contenu['Description_contenu']); ?>
                                            </p>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="tab-content is-hidden">
            <div class="columns is-multiline">
                <?php foreach ($contenus as $contenu): ?>
                    <?php if ($contenu['Type_de_contenu'] == 'video'): ?>
                        <div class="column is-one-quarter">
                            <div class="box">
                                <article class="media">
                                    <div class="media-left">
                                        <?php if ($contenu['Image_contenu']): ?>
                                            <figure class="image is-64x64">
                                                <img src="<?php echo htmlspecialchars($contenu['Image_contenu']); ?>" alt="Image">
                                            </figure>
                                        <?php endif; ?>
                                    </div>
                                    <div class="media-content">
                                        <div class="content">
                                            <p>
                                                <a href="notes_contenu.php?id=<?php echo $contenu['ID_Contenu']; ?>">
                                                    <strong><?php echo htmlspecialchars($contenu['Titre_Contenu']); ?></strong>
                                                </a>
                                                <br>
                                                <?php echo htmlspecialchars($contenu['Description_contenu']); ?>
                                            </p>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="tab-content is-hidden">
            <div class="columns is-multiline">
                <?php foreach ($contenus as $contenu): ?>
                    <?php if ($contenu['Type_de_contenu'] == 'podcast'): ?>
                        <div class="column is-one-quarter">
                            <div class="box">
                                <article class="media">
                                    <div class="media-left">
                                        <?php if ($contenu['Image_contenu']): ?>
                                            <figure class="image is-64x64">
                                                <img src="<?php echo htmlspecialchars($contenu['Image_contenu']); ?>" alt="Image">
                                            </figure>
                                        <?php endif; ?>
                                    </div>
                                    <div class="media-content">
                                        <div class="content">
                                            <p>
                                                <a href="notes_contenu.php?id=<?php echo $contenu['ID_Contenu']; ?>">
                                                    <strong><?php echo htmlspecialchars($contenu['Titre_Contenu']); ?></strong>
                                                </a>
                                                <br>
                                                <?php echo htmlspecialchars($contenu['Description_contenu']); ?>
                                            </p>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="tab-content is-hidden">
            <div class="columns is-multiline">
                <?php foreach ($contenus as $contenu): ?>
                    <?php if ($contenu['Type_de_contenu'] == 'film' || $contenu['Type_de_contenu'] == 'serie'): ?>
                        <div class="column is-one-quarter">
                            <div class="box">
                                <article class="media">
                                    <div class="media-left">
                                        <?php if ($contenu['Image_contenu']): ?>
                                            <figure class="image is-64x64">
                                                <img src="<?php echo htmlspecialchars($contenu['Image_contenu']); ?>" alt="Image">
                                            </figure>
                                        <?php endif; ?>
                                    </div>
                                    <div class="media-content">
                                        <div class="content">
                                            <p>
                                                <a href="notes_contenu.php?id=<?php echo $contenu['ID_Contenu']; ?>">
                                                    <strong><?php echo htmlspecialchars($contenu['Titre_Contenu']); ?></strong>
                                                </a>
                                                <br>
                                                <?php echo htmlspecialchars($contenu['Description_contenu']); ?>
                                            </p>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="tab-content is-hidden">
            <div class="columns is-multiline">
                <?php foreach ($contenus as $contenu): ?>
                    <?php if ($contenu['Type_de_contenu'] == 'documentaire'): ?>
                        <div class="column is-one-quarter">
                            <div class="box">
                                <article class="media">
                                    <div class="media-left">
                                        <?php if ($contenu['Image_contenu']): ?>
                                            <figure class="image is-64x64">
                                                <img src="<?php echo htmlspecialchars($contenu['Image_contenu']); ?>" alt="Image">
                                            </figure>
                                        <?php endif; ?>
                                    </div>
                                    <div class="media-content">
                                        <div class="content">
                                            <p>
                                                <a href="notes_contenu.php?id=<?php echo $contenu['ID_Contenu']; ?>">
                                                    <strong><?php echo htmlspecialchars($contenu['Titre_Contenu']); ?></strong>
                                                </a>
                                                <br>
                                                <?php echo htmlspecialchars($contenu['Description_contenu']); ?>
                                            </p>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="tab-content is-hidden">
            <div class="columns is-multiline">
                <?php foreach ($biographies as $biographie): ?>
                    <div class="column is-one-quarter">
                        <div class="box">
                            <article class="media">
                                <div class="media-left">
                                    <?php if ($biographie['Image']): ?>
                                        <figure class="image is-64x64">
                                            <img src="<?php echo htmlspecialchars($biographie['Image']); ?>" alt="Image">
                                        </figure>
                                    <?php endif; ?>
                                </div>
                                <div class="media-content">
                                    <div class="content">
                                        <p>
                                            <a href="notes_createur.php?id=<?php echo $biographie['ID_Createur']; ?>">
                                                <strong><?php echo htmlspecialchars($biographie['Nom_Createur']); ?></strong>
                                            </a>
                                            <br>
                                            <?php echo htmlspecialchars($biographie['About']); ?>
                                        </p>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="tab-content is-hidden">
            <div class="columns is-multiline">
                <?php foreach ($createurs as $createur): ?>
                    <div class="column is-one-quarter">
                        <div class="box">
                            <article class="media">
                                <div class="media-left">
                                    <?php if ($createur['Image']): ?>
                                        <figure class="image is-64x64">
                                            <img src="<?php echo htmlspecialchars($createur['Image']); ?>" alt="Image">
                                        </figure>
                                    <?php endif; ?>
                                </div>
                                <div class="media-content">
                                    <div class="content">
                                        <p>
                                            <a href="notes_createur.php?id=<?php echo $createur['ID_Createur']; ?>">
                                                <strong><?php echo htmlspecialchars($createur['Nom_Createur']); ?></strong>
                                            </a>
                                            <br>
                                            <?php echo htmlspecialchars($createur['About']); ?>
                                        </p>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>


                              
