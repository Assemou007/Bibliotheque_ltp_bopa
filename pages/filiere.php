<?php
// pages/filiere.php

$slug = isset($_GET['slug']) ? cleanInput($_GET['slug']) : '';

if (!$slug) {
    // Afficher toutes les filières
    $filieres = $pdo->query("
        SELECT f.*,
               COUNT(DISTINCT m.id) as nb_matieres,
               COUNT(DISTINCT d.id) as nb_documents
        FROM filieres f
        LEFT JOIN matieres m ON f.id = m.filiere_id
        LEFT JOIN documents d ON m.id = d.matiere_id
        GROUP BY f.id
        ORDER BY f.ordre
    ")->fetchAll();
   
    logAction($pdo, 'filieres', 'vue');
?>

<div class="filieres-listing">
    <h1>Nos filières</h1>
    <p class="page-description">Découvrez toutes les filières du LTP-BOPA et accédez à leurs ressources pédagogiques.</p>
   
    <div class="filieres-grid-large">
        <?php foreach ($filieres as $filiere): ?>
        <div class="filiere-card-large" style="border-color: <?= $filiere->couleur ?>">
            <div class="card-header">
                <span class="filiere-icon"><?= $filiere->icone ?></span>
                <h2><?= $filiere->nom ?></h2>
            </div>
           
            <p class="filiere-description"><?= $filiere->description ?></p>
           
            <div class="filiere-stats">
                <span>📚 <?= $filiere->nb_matieres ?> matières</span>
                <span>📄 <?= $filiere->nb_documents ?> documents</span>
            </div>
           
            <a href="index.php?page=filiere&slug=<;?= $filiere->slug ?>" class="btn-filiere" style="background-color: <?= $filiere->couleur ?>">
                Voir les matières →
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
} else {
    // Afficher une filière spécifique
    $stmt = $pdo->prepare("SELECT * FROM filieres WHERE slug = ?");
    $stmt->execute([$slug]);
    $filiere = $stmt->fetch();
   
    if (!$filiere) {
        include 'pages/404.php';
        return;
    }
   
    // Récupérer les matières de la filière
    $matieres = $pdo->prepare("
        SELECT m.*, COUNT(d.id) as nb_documents
        FROM matieres m
        LEFT JOIN documents d ON m.id = d.matiere_id
        WHERE m.filiere_id = ?
        GROUP BY m.id
        ORDER BY m.ordre
    ");
    $matieres->execute([$filiere->id]);
    $matieres = $matieres->fetchAll();
   
    // Récupérer les documents récents de la filière
    $documents = $pdo->prepare("
        SELECT d.*, m.nom as matiere_nom
        FROM documents d
        JOIN matieres m ON d.matiere_id = m.id
        WHERE m.filiere_id = ? AND d.est_public = 1
        ORDER BY d.created_at DESC
        LIMIT 10
    ");
    $documents->execute([$filiere->id]);
    $documents = $documents->fetchAll();
   
    logAction($pdo, "filiere_{$filiere->slug}", 'vue');
?>

<div class="filiere-detail">
    <div class="filiere-hero" style="background-color: <?= $filiere->couleur ?>">
        <div class="filiere-hero-content">
            <span class="filiere-icon-large"><?= $filiere->icone ?></span>
            <h1><?= $filiere->nom ?></h1>
            <p><?= $filiere->description ?></p>
        </div>
    </div>
   
    <div class="filiere-content">
        <h2>Matières disponibles</h2>
       
        <?php if (empty($matieres)): ?>
            <p class="no-data">Aucune matière pour le moment.</p>
        <?php else: ?>
            <div class="matieres-grid">
                <?php foreach ($matieres as $matiere): ?>
                <a href="index.php?page=matiere&id=<;?= $matiere->id ?>" class="matiere-card">
                    <h3><?= htmlspecialchars($matiere->nom) ?></h3>
                    <p><?= htmlspecialchars($matiere->description) ?></p>
                    <span class="matiere-count">📄 <?= $matiere->nb_documents ?> documents</span>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
       
        <?php if (!empty($documents)): ?>
        <h3>Derniers documents ajoutés</h3>
        <div class="documents-mini-list">
            <?php foreach ($documents as $doc): ?>
            <div class="document-mini-card">
                <span class="doc-icon">📄</span>
                <div class="doc-info">
                    <a href="assets/uploads/<?= $doc->chemin_fichier ?>"><?= htmlspecialchars($doc->titre) ?></a>
                    <span class="doc-meta"><?= $doc->matiere_nom ?> • <?= date('d/m/Y', strtotime($doc->created_at)) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
}
?>