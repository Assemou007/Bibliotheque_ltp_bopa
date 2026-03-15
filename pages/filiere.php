
<?php
// pages/filiere.php

$slug = isset($_GET['slug']) ? cleanInput($_GET['slug']) : '';

if (!$slug) {
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

<div class="filieres-listing-page">
    <div class="page-header"> 
        <h1>Nos filières</h1>
        <p class="subtitle">Découvrez toutes les filières du LTP-BOPA et accédez à leurs ressources pédagogiques.</p>
    </div>

    <div class="filieres-grid-large">
        <?php foreach ($filieres as $filiere): ?>
            <div class="filiere-card-large" style="border-color: <?= $filiere->couleur ?>;">
                <div class="card-header">
                    <span class="filiere-icon"><?= $filiere->icone ?></span>
                    <h2><?= htmlspecialchars($filiere->nom) ?></h2>
                </div>
                <p class="filiere-description"><?= htmlspecialchars($filiere->description) ?></p>
                <div class="filiere-stats">
                    <span>📚 <?= $filiere->nb_matieres ?> matière<?= $filiere->nb_matieres > 1 ? 's' : '' ?></span>
                    <span>📄 <?= $filiere->nb_documents ?> document<?= $filiere->nb_documents > 1 ? 's' : '' ?></span>
                </div>
                <a href="index.php?page=filiere&slug=<?= $filiere->slug ?>" class="btn-filiere" style="background-color: <?= $filiere->couleur ?>;">
                    Voir les matières →
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
} else {

    $stmt = $pdo->prepare("SELECT * FROM filieres WHERE slug = ?");
    $stmt->execute([$slug]);
    $filiere = $stmt->fetch();

    if (!$filiere) {
        include 'pages/404.php';
        return;
    }

    // Récupérer les matières de la filière avec le nombre de documents
    $matieres = $pdo->prepare("
        SELECT m.*, COUNT(d.id) as nb_documents
        FROM matieres m
        LEFT JOIN documents d ON m.id = d.matiere_id AND d.est_public = 1
        WHERE m.filiere_id = ?
        GROUP BY m.id
        ORDER BY m.ordre
    ");
    $matieres->execute([$filiere->id]);
    $matieres = $matieres->fetchAll();

    // Récupérer quelques documents récents pour illustrer
    $documents = $pdo->prepare("
        SELECT d.*, m.nom as matiere_nom
        FROM documents d
        JOIN matieres m ON d.matiere_id = m.id
        WHERE m.filiere_id = ? AND d.est_public = 1
        ORDER BY d.created_at DESC
        LIMIT 6
    ");
    $documents->execute([$filiere->id]);
    $documents = $documents->fetchAll();

    logAction($pdo, "filiere_{$filiere->slug}", 'vue');
?>

<div class="filiere-detail-page">
    <!-- Bandeau Hero avec la couleur de la filière -->
    <div class="filiere-hero" style="background: linear-gradient(135deg, <?= $filiere->couleur ?> 0%, <?= $filiere->couleur ?>90 100%);">
        <div class="container">
            <div class="filiere-hero-content">
                <span class="filiere-icon-large"><?= $filiere->icone ?></span>
                <h1><?= htmlspecialchars($filiere->nom) ?></h1>
                <p><?= htmlspecialchars($filiere->description) ?></p>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Section : Liste des matières -->
        <section class="matieres-section">
            <h2>Matières disponibles</h2>
            <?php if (empty($matieres)): ?>
                <p class="no-data">Aucune matière n'a encore été créée pour cette filière.</p>
            <?php else: ?>
                <div class="matieres-grid">
                    <?php foreach ($matieres as $matiere): ?>
                        <!-- ✅ Lien CORRECT vers la page matière avec l'ID -->
                        <a href="index.php?page=matiere&id=<?= $matiere->id ?>" class="matiere-card">
                            <h3><?= htmlspecialchars($matiere->nom) ?></h3>
                            <?php if ($matiere->description): ?>
                                <p><?= htmlspecialchars($matiere->description) ?></p>
                            <?php endif; ?>
                            <span class="matiere-count">
                                📄 <?= $matiere->nb_documents ?> document<?= $matiere->nb_documents > 1 ? 's' : '' ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <?php if (!empty($documents)): ?>
        <section class="recent-documents-section">
            <h2>Derniers documents ajoutés</h2>
            <div class="documents-mini-grid">
                <?php foreach ($documents as $doc): ?>
                    <div class="document-mini-card">
                        <span class="docs-icon">📄</span>
                        <div class="docs-info">
                            <a href="document.php?id=<;?= $doc->id ?>&action=view" class="docs-title"><?= htmlspecialchars($doc->titre) ?></a>
                            <span class="docs-meta"><?= htmlspecialchars($doc->matiere_nom) ?> • <?= date('d/m/Y', strtotime($doc->created_at)) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </div>
</div>

<?php
}
?>