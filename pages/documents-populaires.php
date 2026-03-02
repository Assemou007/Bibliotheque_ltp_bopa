<?php
// pages/documents-populaires.php

$page_num = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$per_page = 18;
$offset = ($page_num - 1) * $per_page;

// Compter le total
$total = $pdo->query("
    SELECT COUNT(*) as count
    FROM documents
    WHERE est_public = 1
")->fetch()->count;

$total_pages = ceil($total / $per_page);

// Récupérer les documents populaires
$documents = $pdo->query("
    SELECT d.*, m.nom as matiere_nom, f.nom as filiere_nom, f.slug as filiere_slug
    FROM documents d
    JOIN matieres m ON d.matiere_id = m.id
    JOIN filieres f ON m.filiere_id = f.id
    WHERE d.est_public = 1
    ORDER BY d.vue_count DESC, d.telechargement_count DESC
    LIMIT $offset, $per_page
")->fetchAll();

logAction($pdo, 'documents-populaires', 'vue');
?>

<div class="documents-listing-page">
    <div class="listing-header">
        <h1>🔥 Documents populaires</h1>
        <p class="subtitle">Les documents les plus consultés par les apprenants</p>
    </div>
   
    <?php if (empty($documents)): ?>
        <div class="no-documents">
            <p>Aucun document disponible pour le moment.</p>
        </div>
    <?php else: ?>
        <div class="documents-grid">
            <?php foreach ($documents as $doc):
                $type = getDocumentTypeLabel($doc->type_document);
            ?>
            <div class="document-card popular">
                <div class="card-header">
                    <span class="document-type"><?= $type['icon'] ?> <?= $type['label'] ?></span>
                    <span class="popular-rank">#<?= $offset + $loop->index + 1 ?></span>
                </div>
               
                <h3><?= htmlspecialchars($doc->titre) ?></h3>
               
                <div class="document-meta">
                    <span class="meta-item">📁 <?= htmlspecialchars($doc->filiere_nom) ?></span>
                    <span class="meta-item">📘 <?= htmlspecialchars($doc->matiere_nom) ?></span>
                </div>
               
                <div class="popular-stats">
                    <span class="stat" title="Vues">
                        <span class="stat-icon">👁️</span>
                        <?= number_format($doc->vue_count, 0, ',', ' ') ?>
                    </span>
                    <span class="stat" title="Téléchargements">
                        <span class="stat-icon">📥</span>
                        <?= number_format($doc->telechargement_count, 0, ',', ' ') ?>
                    </span>
                </div>
               
                <div class="document-footer">
                    <span class="document-date">📅 <?= date('d/m/Y', strtotime($doc->created_at)) ?></span>
                    <a href="assets/uploads/<?= $doc->chemin_fichier ?>" class="btn-view" target="_blank">Consulter</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
       
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page_num > 1): ?>
                <a href="?page=documents-populaires&p=<?= $page_num - 1 ?>" class="page-link">← Précédent</a>
            <?php endif; ?>
           
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=documents-populaires&p=<?= $i ?>"
                   class="page-link <?= $i == $page_num ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
           
            <?php if ($page_num < $total_pages): ?>
                <a href="?page=documents-populaires&p=<?= $page_num + 1 ?>" class="page-link">Suivant →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>