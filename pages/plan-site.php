<?php
// pages/plan-site.php

// Récupérer toutes les filières
$filieres = $pdo->query("
    SELECT f.*, COUNT(m.id) as nb_matieres
    FROM filieres f
    LEFT JOIN matieres m ON f.id = m.filiere_id
    GROUP BY f.id
    ORDER BY f.ordre
")->fetchAll();

// Récupérer les matières pour chaque filière
foreach ($filieres as $filiere) {
    $stmt = $pdo->prepare("
        SELECT * FROM matieres
        WHERE filiere_id = ?
        ORDER BY ordre
    ");
    $stmt->execute([$filiere->id]);
    $filiere->matieres = $stmt->fetchAll();
}

logAction($pdo, 'plan-site', 'vue');
?>

<div class="sitemap-page">
    <div class="sitemap-header">
        <h1>🗺️ Plan du site</h1>
        <p class="subtitle">Retrouvez facilement toutes les pages de la bibliothèque numérique</p>
    </div>
   
    <div class="sitemap-grid">
        <!-- Pages principales -->
        <div class="sitemap-section">
            <h2>📌 Pages principales</h2>
            <ul class="sitemap-list">
                <li><a href="index.php">🏠 Accueil</a></li>
                <li><a href="index.php?page=documents-recents">🆕 Documents récents</a></li>
                <li><a href="index.php?page=documents-populaires">🔥 Documents populaires</a></li>
                <li><a href="index.php?page=recherche">🔍 Recherche</a></li>
                <li><a href="index.php?page=messages">💬 Espace public</a></li>
                <li><a href="index.php?page=statistiques">📊 Statistiques</a></li>
            </ul>
        </div>
       
        <!-- Filières -->
        <div class="sitemap-section">
            <h2>🏫 Filières</h2>
            <ul class="sitemap-list">
                <?php foreach ($filieres as $filiere): ?>
                <li>
                    <a href="index.php?page=filiere&slug=<;?= $filiere->slug ?>" style="color: <?= $filiere->couleur ?>">
                        <?= $filiere->icone ?> <?= htmlspecialchars($filiere->nom) ?>
                    </a>
                    <?php if (!empty($filiere->matieres)): ?>
                    <ul class="sitemap-sublist">
                        <?php foreach ($filiere->matieres as $matiere): ?>
                        <li>
                            <a href="index.php?page=matiere&id=<;?= $matiere->id ?>">
                                📘 <?= htmlspecialchars($matiere->nom) ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
       
        <!-- Informations -->
        <div class="sitemap-section">
            <h2>ℹ️ Informations</h2>
            <ul class="sitemap-list">
                <li><a href="index.php?page=a-propos">📖 À propos</a></li>
                <li><a href="index.php?page=guide">📚 Guide d'utilisation</a></li>
                <li><a href="index.php?page=faq">❓ FAQ</a></li>
                <li><a href="index.php?page=contact">📧 Contact</a></li>
                <li><a href="index.php?page=accessibilite">♿ Accessibilité</a></li>
                <li><a href="index.php?page=mentions-legales">⚖️ Mentions légales</a></li>
                <li><a href="index.php?page=plan-site">🗺️ Plan du site</a></li>
            </ul>
        </div>
    </div>
   
    <!-- Légende -->
    <div class="sitemap-legend">
        <h3>📋 Légende</h3>
        <ul>
            <li>🏠 Page d'accueil</li>
            <li>📌 Pages principales</li>
            <li>🏫 Filières et matières</li>
            <li>ℹ️ Pages d'information</li>
            <li>📄 Documents disponibles</li>
        </ul>
    </div>
   
    <!-- Statistiques rapides -->
    <div class="sitemap-stats">
        <p>Le site contient actuellement :</p>
        <ul>
            <li><strong><?= count($filieres) ?></strong> filières</li>
            <li><strong><?= array_sum(array_map(function($f) { return count($f->matieres); }, $filieres)) ?></strong> matières</li>
            <li><strong><?= $pdo->query("SELECT COUNT(*) FROM documents WHERE est_public = 1")->fetchColumn() ?></strong> documents</li>
            <li><strong><?= $pdo->query("SELECT COUNT(*) FROM messages_publics WHERE statut = 'approuve'")->fetchColumn() ?></strong> messages publics</li>
        </ul>
    </div>
</div>