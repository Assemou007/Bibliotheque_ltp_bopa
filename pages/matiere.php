<?php
// pages/matiere.php

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    include 'pages/404.php';
    return;
}

// Récupérer les informations de la matière
$stmt = $pdo->prepare("
    SELECT m.*, f.nom as filiere_nom, f.slug as filiere_slug, f.couleur as filiere_couleur
    FROM matieres m
    JOIN filieres f ON m.filiere_id = f.id
    WHERE m.id = ?
");
$stmt->execute([$id]);
$matiere = $stmt->fetch();

if (!$matiere) {
    include 'pages/404.php';
    return;
}

// Récupérer les documents de la matière avec pagination
$page_num = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$per_page = 12;
$offset = ($page_num - 1) * $per_page;

// Compter le total des documents
$count_stmt = $pdo->prepare("
    SELECT COUNT(*) as total
    FROM documents
    WHERE matiere_id = ? AND est_public = 1
");
$count_stmt->execute([$id]);
$total_documents = $count_stmt->fetch()->total;
$total_pages = ceil($total_documents / $per_page);

// Récupérer les documents
$stmt = $pdo->prepare("
    SELECT * FROM documents
    WHERE matiere_id = ? AND est_public = 1
    ORDER BY
        CASE type_document
            WHEN 'cours' THEN 1
            WHEN 'td' THEN 2
            WHEN 'tp' THEN 3
            WHEN 'exercices' THEN 4
            WHEN 'examen' THEN 5
            ELSE 6
        END,
        created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->execute([$id, $per_page, $offset]);
$documents = $stmt->fetchAll();

// Statistiques par type de document
$stats_stmt = $pdo->prepare("
    SELECT type_document, COUNT(*) as count
    FROM documents
    WHERE matiere_id = ? AND est_public = 1
    GROUP BY type_document
    ORDER BY count DESC
");
$stats_stmt->execute([$id]);
$stats_types = $stats_stmt->fetchAll();

// Loguer la visite
logAction($pdo, "matiere_{$id}", 'vue');
updateDailyStats($pdo, 'vues_total');
?>

<div class="matiere-page">
    <!-- En-tête de la matière -->
    <div class="matiere-header" style="background: linear-gradient(135deg, <?= $matiere->filiere_couleur ?> 0%, <?= $matiere->filiere_couleur ?>90 100%);">
        <div class="matiere-header-content">
            <div class="matiere-breadcrumb">
                <a href="index.php?page=accueil">Accueil</a>
                <span class="separator">›</span>
                <a href="index.php?page=filiere&slug=<?= $matiere->filiere_slug ?>"><?= htmlspecialchars($matiere->filiere_nom) ?></a>
                <span class="separator">›</span>
                <span><?= htmlspecialchars($matiere->nom) ?></span>
            </div>
           
            <h1><?= htmlspecialchars($matiere->nom) ?></h1>
           
            <?php if ($matiere->description): ?>
                <p class="matiere-description"><?= htmlspecialchars($matiere->description) ?></p>
            <?php endif; ?>
           
            <div class="matiere-stats">
                <div class="stat-item">
                    <span class="stat-value"><?= $total_documents ?></span>
                    <span class="stat-label">Documents</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value"><?= count($stats_types) ?></span>
                    <span class="stat-label">Types</span>
                </div>
            </div>
        </div>
    </div>
   
    <div class="matiere-content">
        <!-- Filtres par type -->
        <?php if (!empty($stats_types)): ?>
        <div class="type-filters">
            <button class="type-filter active" data-type="all">Tous les types</button>
            <?php foreach ($stats_types as $stat):
                $type = getDocumentTypeLabel($stat->type_document);
            ?>
            <button class="type-filter" data-type="<?= $stat->type_document ?>">
                <?= $type['icon'] ?> <?= $type['label'] ?> (<?= $stat->count ?>)
            </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
       
        <!-- Liste des documents -->
        <?php if (empty($documents)): ?>
            <div class="no-documents">
                <div class="no-docs-icon">📭</div>
                <h3>Aucun document disponible</h3>
                <p>Aucun document n'a encore été ajouté pour cette matière.</p>
            </div>
        <?php else: ?>
            <div class="documents-grid" id="documentsGrid">
                <?php foreach ($documents as $doc):
                    $type = getDocumentTypeLabel($doc->type_document);
                    $file_ext = strtoupper($doc->format_fichier);
                    $file_icon = $file_ext == 'PDF' ? '📕' : ($file_ext == 'DOCX' ? '📘' : '📗');
                 ?>
                <div class="document-card" data-type="<?= $doc->type_document ?>">
                    <div class="document-card-header">
                        <div class="document-type-badge" style="background-color: <?= $matiere->filiere_couleur ?>20; color: <?= $matiere->filiere_couleur ?>">
                            <?= $type['icon'] ?> <?= $type['label'] ?>
                        </div>
                        <div class="document-format">
                            <?= $file_icon ?> <?= $file_ext ?>
                        </div>
                    </div>
                   
                    <h3 class="document-title">
                        <a href="assets/uploads/<?= $doc->chemin_fichier ?>" target="_blank">
                            <?= htmlspecialchars($doc->titre) ?>
                        </a>
                    </h3>
                   
                    <?php if ($doc->description): ?>
                        <p class="document-description">
                            <?= htmlspecialchars(substr($doc->description, 0, 100)) ?>...
                        </p>
                    <?php endif; ?>
                   
                    <div class="document-meta">
                        <?php if ($doc->auteur): ?>
                        <span class="meta-item">
                            <span class="meta-icon">👤</span>
                            <?= htmlspecialchars($doc->auteur) ?>
                        </span>
                        <?php endif; ?>
                       
                        <?php if ($doc->annee_scolaire): ?>
                        <span class="meta-item">
                            <span class="meta-icon">📅</span>
                            <?= htmlspecialchars($doc->annee_scolaire) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                   
                    <div class="document-stats">
                        <span class="stat" title="Vues">
                            <span class="stat-icon">👁️</span>
                            <?= $doc->vue_count ?>
                        </span>
                        <span class="stat" title="Téléchargements">
                            <span class="stat-icon">📥</span>
                            <?= $doc->telechargement_count ?>
                        </span>
                        <span class="stat" title="Ajouté le">
                            <span class="stat-icon">📌</span>
                            <?= date('d/m/Y', strtotime($doc->created_at)) ?>
                        </span>
                    </div>
                   
                    <div class="document-actions">
                        <a href="assets/uploads/<?= $doc->chemin_fichier ?>" class="btn-view" target="_blank">
                            <span class="btn-icon">👁️</span>
                            Consulter
                        </a>
                        <a href="assets/uploads/<?= $doc->chemin_fichier ?>" class="btn-download" download>
                            <span class="btn-icon">📥</span>
                            Télécharger
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
           
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page_num > 1): ?>
                    <a href="?page=matiere&id=<?= $id ?>&p=<?= $page_num - 1 ?>" class="page-link prev">← Précédent</a>
                <?php endif; ?>
               
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=matiere&id=<?= $id ?>&p=<?= $i ?>"
                       class="page-link <?= $i == $page_num ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
               
                <?php if ($page_num < $total_pages): ?>
                    <a href="?page=matiere&id=<?= $id ?>&p=<?= $page_num + 1 ?>" class="page-link next">Suivant →</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
// Filtrage par type de document
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.type-filter');
    const documentCards = document.querySelectorAll('.document-card');
   
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Mettre à jour l'état actif
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
           
            const type = this.dataset.type;
           
            // Filtrer les documents
            documentCards.forEach(card => {
                if (type === 'all' || card.dataset.type === type) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>