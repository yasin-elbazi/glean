<!-- navbar.php -->
<nav class="navbar" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item" href="workspace.php">
            <img src="ressources/images/glean_logo_sfb.png" alt="Glean Logo">
        </a>
        <div class="navbar-burger" data-target="navbarMain">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>

    <div id="navbarMain" class="navbar-menu">
        <div class="navbar-start">
            <a class="navbar-item" href="workspace.php">Mon Labo</a>
            <a class="navbar-item" href="dashboard.php">Dashboard</a>

            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">
                    Notes
                </a>
                <div class="navbar-dropdown">
                    <a class="navbar-item" href="mes_notes.php">Mes Notes</a>
                    <a class="navbar-item" href="notes_partagees.php">Notes Partagées</a>
                    <a class="navbar-item" href="notes_epinglees.php">Notes Épinglées</a>
                </div>
            </div>

            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">
                    Contenus
                </a>
                <div class="navbar-dropdown">
                    <a class="navbar-item" href="mes_contenus.php">Mes Contenus</a>
                    <a class="navbar-item" href="mes_createurs.php">Mes Créateurs de Contenus</a>
                </div>
            </div>
        </div>

        <div class="navbar-end">
            <div class="navbar-item">
                <a class="button is-danger" href="logout.php">Déconnexion</a>
            </div>
        </div>
    </div>
</nav>