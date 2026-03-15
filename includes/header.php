<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Bibliothèque numérique du LTP-BOPA - Accédez à toutes les ressources pédagogiques de votre établissement">
    <meta name="author" content="LTP-BOPA">

    <title>Bibliothèque Numérique - LTP BOPA</title>

    <meta property="og:type" content="website">
    <meta property="og:url" content="https://<?= $_SERVER['HTTP_HOST'] ?>">
    <meta property="og:title" content="Bibliothèque Numérique LTP-BOPA">
    <meta property="og:description" content="Accédez à toutes les ressources pédagogiques du LTP-BOPA">
    <link rel="stylesheet" href="assets/css/style.css?v=<;?= filemtime('assets/css/style.css') ?>">
</head>
<body>
    <header class="main-header" role="banner">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="index.php" aria-label="Accueil">
                        <span class="logo-icon">📚</span>
                        <span class="logo-text">LTP-BOPA<span class="logo-highlight">Numérique</span></span>
                    </a>
                </div>            
                <!-- Navigation principale -->
                <nav class="main-nav" role="navigation" aria-label="Navigation principale">
                    <button class="menu-toggle" aria-expanded="false" aria-controls="main-menu">
                        <span class="sr-only">Menu</span>
                        <span class="hamburger"></span>
                    </button>
                   
                    <ul id="main-menu" class="nav-menu">
                        <li><a href="index.php" <?= $page=='accueil' ? 'aria-current="page"' : '' ?>>Accueil</a></li>
                        <li class="dropdown">
                            <a href="index.php?page=filiere" aria-expanded="false">Filières</a>
                           
                        </li>
                        <li><a href="index.php?page=documents-recents">Nouveautés</a></li>
                        <li><a href="index.php?page=faq">FAQ</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="index.php?page=dashboard">Mon compte</a></li>
                        <li><a href="index.php?page=deconnexion">Déconnexion</a></li>
                    <?php else: ?>
                        <li><a href="index.php?page=connexion" class="btn btn-succes">Connexion</a></li>
                        <li><a href="index.php?page=inscription">Inscription</a></li>
                    <?php endif; ?>
                    </ul>
                </nav>
                <!-- Barre de recherche -->

            </div>
        </div>
    </header>

    <!-- Main content -->
    <main id="main-content" class="main-content" role="main">
        <div class="container">