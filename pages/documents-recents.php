<?php
// pages/documents-recents.php

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

// Récupérer les documents récents
$documents = $pdo->query("
    SELECT d.*, m.nom as matiere_nom, f.nom as filiere_nom, f.slug as filiere_slug
    FROM documents d
    JOIN matieres m ON d.matiere_id = m.id
    JOIN filieres f ON m.filiere_id = f.id
    WHERE d.est_public = 1
    ORDER BY d.created_at DESC
    LIMIT $offset, $per_page
")->fetchAll();

logAction($pdo, 'documents-recents', 'vue');
?>

<div class="documents-listing-page">
    <div class="listing-header">
        <h1>🆕 Documents récents</h1>
        <p class="subtitle">Les derniers documents ajoutés à la bibliothèque</p>
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
            <div class="document-card">
                <div class="card-header">
                    <span class="document-type"><?= $type['icon'] ?> <?= $type['label'] ?></span>
                    <span class="document-format">📄 <?= strtoupper($doc->format_fichier) ?></span>
                </div>
               
                <h3><?= htmlspecialchars($doc->titre) ?></h3>
               
                <div class="document-meta">
                    <span class="meta-item">📁 <?= htmlspecialchars($doc->filiere_nom) ?></span>
                    <span class="meta-item">📘 <?= htmlspecialchars($doc->matiere_nom) ?></span>
                </div>
               
                <?php if ($doc->description): ?>
                <p class="document-description"><?= htmlspecialchars(substr($doc->description, 0, 100)) ?>...</p>
                <?php endif; ?>
               
                <div class="document-footer">
                    <span class="document-date">📅 <?= date('d/m/Y', strtotime($doc->created_at)) ?></span>
                    <div class="document-actions">
                    <a href="document.php?id=<?= $doc->id ?>&action=view" class="btn-view" target="_blank">Consulter</a>
                    <a href="document.php?id=<?= $doc->id ?>&action=download" class="btn-download">📥</a>                </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
       
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page_num > 1): ?>
                <a href="?page=documents-recents&p=<?= $page_num - 1 ?>" class="page-link">← Précédent</a>
            <?php endif; ?>
           
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=documents-recents&p=<?= $i ?>"
                   class="page-link <?= $i == $page_num ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
           
            <?php if ($page_num < $total_pages): ?>
                <a href="?page=documents-recents&p=<?= $page_num + 1 ?>" class="page-link">Suivant →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>