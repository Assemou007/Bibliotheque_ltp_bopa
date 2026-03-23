<?php
//Le count des statistique
$stats = [
    'documents' => $pdo->query("SELECT COUNT(*) as count FROM documents")->fetch()->count,
    'filieres' => $pdo->query("SELECT COUNT(*) as count FROM filieres")->fetch()->count,
    'matieres' => $pdo->query("SELECT COUNT(*) as count FROM matieres")->fetch()->count,
    'messages' => $pdo->query("SELECT COUNT(*) as count FROM messages_publics WHERE statut = 'approuve'")->fetch()->count
];

// les filiere
$filieres = $pdo->query("SELECT * FROM filieres ORDER BY ordre")->fetchAll();


$documents_recents = $pdo->query("
    SELECT d.*, m.nom as matiere_nom, f.nom as filiere_nom, f.slug as filiere_slug
    FROM documents d
    JOIN matieres m ON d.matiere_id = m.id
    JOIN filieres f ON m.filiere_id = f.id
    WHERE d.est_public = 1
    ORDER BY d.created_at DESC
    LIMIT 6
")->fetchAll();

//les documents qui ont plus de vue
$documents_populaires = $pdo->query("
    SELECT d.*, m.nom as matiere_nom, f.nom as filiere_nom, f.slug as filiere_slug
    FROM documents d
    JOIN matieres m ON d.matiere_id = m.id
    JOIN filieres f ON m.filiere_id = f.id
    WHERE d.est_public = 1
    ORDER BY d.vue_count DESC
    LIMIT 6
")->fetchAll();

// message
$messages_recents = $pdo->query("
    SELECT * FROM messages_publics
    WHERE statut = 'approuve' AND parent_id IS NULL
    ORDER BY created_at DESC
    LIMIT 5
")->fetchAll();

logAction($pdo, 'accueil', 'vue');
?>

<section class="hero">
    <div class="hero-content">
        <h1>Bibliothèque Numérique <span class="highlight">LTP-BOPA</span></h1>
        <p class="hero-text">Accédez à toutes les ressources pédagogiques de votre établissement, disponibles 24h/24 et 7j/7</p>
       
        <div class="hero-stats">
            <div class="stat-item">
                <span class="stat-number"><?= $stats['documents'] ?></span>
                <span class="stat-label">Documents</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?= $stats['filieres'] ?></span>
                <span class="stat-label">Filières</span>
            </div>
            <div class="stat-item">
                <span class="stat-number"><?= $stats['matieres'] ?></span>
                <span class="stat-label">Matières</span>
            </div>
        </div>
       
        <div class="hero-search">
            <form action="index.php" method="get" class="search-large">
                <input type="hidden" name="page" value="recherche">
                <input type="search"
                       name="q"
                       placeholder="Que cherchez-vous ? (cours, TD, exercices...)"
                       aria-label="Rechercher"
                       required>
                <button type="submit">Rechercher</button>
            </form>
        </div>
    </div>
</section>


<section class="filieres-section">
    <div class="section-header">
        <h2>Nos Filières</h2>
        <p>Explorez les ressources par filière</p>
    </div>
   
    <div class="filieres-grid">
        <?php foreach ($filieres as $filiere): ?>
        <a href="index.php?page=filiere&slug=<?= $filiere->slug ?>" class="filiere-card" style="border-top-color: <?= $filiere->couleur ?>">
            <div class="card-icon"><img class="img" src="<?= $filiere->icone ?>" alt=""></div>
            <h3><?= $filiere->nom ?></h3>
            <p><?= $filiere->description ?></p>
            <span class="card-link">Voir les matières →</span>
        </a>
        <?php endforeach; ?>
    </div>
</section>



<div class="home-columns">

    <section class="popular-section">
        <div class="section-header">
            <h2>🔥 Documents populaires</h2>
            <a href="index.php?page=documents-populaires" class="section-link">Voir tout →</a>
        </div>
       
        <div class="popular-list">
            <?php foreach ($documents_populaires as $doc): ?>
            <div class="popular-item">
                <span class="popular-icon"><i class="fas fa-file-pdf"></i></span>
                <div class="popular-info">
                    <h4><a href="assets/uploads/<?= $doc->chemin_fichier ?>"><?= htmlspecialchars($doc->titre) ?></a></h4>
                    <span class="popular-meta"><?= $doc->filiere_nom ?> • <?= $doc->matiere_nom ?></span>
                </div>
                <span class="popular-count">👁️ <?= $doc->vue_count ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
   
    <!-- mssage récemment publier -->
    <section class="messages-section">
        <div class="section-header">
            <h2>💬 Espace public</h2>
            <a href="index.php?page=messages" class="section-link">Voir tout →</a>
        </div>
       
        <div class="messages-list">
            <?php if (empty($messages_recents)): ?>
                <p class="no-messages">Aucun message pour le moment.</p>
            <?php else: ?>
                <?php foreach ($messages_recents as $msg):
                    $type = getMessageTypeLabel($msg->type_message);
                ?>
                <div class="message-item">
                    <span class="message-type" style="background-color: <?= $type['color'] ?>20; color: <?= $type['color'] ?>">
                        <?= $type['icon'] ?> <?= $type['label'] ?>
                    </span>
                    <h4><a href="index.php?page=message-detail&id=<?= $msg->id ?>"><?= htmlspecialchars($msg->titre) ?></a></h4>
                    <span class="message-author">Par <?= htmlspecialchars($msg->nom_utilisateur) ?></span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>