<?php
// pages/404.php
http_response_code(404);
logAction($pdo, '404', 'vue');
?>

<div class="error-page">
    <div class="error-content">
        <div class="error-code">404</div>
        <h1>Page non trouvée</h1>
        <p class="error-message">
            Désolé, la page que vous recherchez n'existe pas ou a été déplacée.
        </p>
        <div class="error-actions">
            <a href="index.php" class="btn-primary">🏠 Retour à l'accueil</a>
            <a href="index.php?page=plan-site" class="btn-secondary">🗺️ Plan du site</a>
            <a href="index.php?page=contact" class="btn-secondary">📧 Nous contacter</a>
        </div>
        <div class="error-suggestion">
            <p>Vous pouvez aussi essayer :</p>
            <ul>
                <li><a href="index.php?page=documents-recents">📚 Voir les documents récents</a></li>
                <li><a href="index.php?page=recherche">🔍 Effectuer une recherche</a></li>
                <li><a href="index.php?page=faq">❓ Consulter la FAQ</a></li>
            </ul>
        </div>
    </div>
</div>