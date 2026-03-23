<?php
// pages/matiere.php - AMÉLIORÉ avec filtres, stats, pagination

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    include 'pages/404.php';
    return;
}

$stmt = $pdo->prepare("
    SELECT m.*, f.nom as filiere_nom, f.slug as filiere_slug, f.couleur as filiere_couleur
    FROM matieres m
    JOIN filieres f ON m.filiere_id = f.id
    WHERE m.id = ?
");
$stmt->execute([$id]);
$matiere = $stmt->fetch(PDO::FETCH_OBJ);

if (!$matiere) {
    include 'pages/404.php';
    return;
}

$page_num = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
$per_page = 12;
$offset = ($page_num - 1) * $per_page;

// Total SAFE
$count_stmt = $pdo->prepare("SELECT COUNT(*) as total FROM documents WHERE matiere_id = ? AND est_public = 1");
$count_stmt->execute([$id]);
$row = $count_stmt->fetch(PDO::FETCH_OBJ);
$total_documents = $row ? (int)$row->total : 0;
$total_pages = ceil($total_documents / $per_page);

// Documents SAFE
$stmt = $pdo->prepare("
    SELECT * FROM documents 
    WHERE matiere_id = ? AND est_public = 1 
    ORDER BY created_at DESC 
    LIMIT ? OFFSET ?
");
$stmt->execute([$id, $per_page, $offset]);
$documents = $stmt->fetchAll(PDO::FETCH_OBJ);

// Stats types
$stats_stmt = $pdo->prepare("
    SELECT type_document, COUNT(*) as count 
    FROM documents 
    WHERE matiere_id = ? AND est_public = 1 
    GROUP BY type_document 
    ORDER BY count DESC
");
$stats_stmt->execute([$id]);
$stats_types = $stats_stmt->fetchAll(PDO::FETCH_OBJ);

// Log safe
@logAction($pdo, "matiere_{$id}", 'vue');
?>

<div class="matiere-page">
    <!-- Header amélioré -->
    <div class="matiere-header" style="background: linear-gradient(135deg, <?= $matiere->filiere_couleur ?? '#007bff' ?> 0%, <?= $matiere->filiere_couleur ?? '#007bff' ?>80 100%);">
        <div class="container">
            <div class="matiere-header-content">
                <div class="matiere-breadcrumb">
                    <a href="index.php?page=accueil">🏠 Accueil</a>
                    <span>›</span>
                    <a href="index.php?page=filiere&slug=<?= htmlspecialchars($matiere->filiere_slug ?? '') ?>"><?= htmlspecialchars($matiere->filiere_nom ?? 'Filière') ?></a>
                    <span>›</span>
                    <strong><?= htmlspecialchars($matiere->nom) ?></strong>
                </div>
                <h1><?= htmlspecialchars($matiere->nom) ?></h1>
                <?php if (!empty($matiere->description)): ?>
                    <p><?= htmlspecialchars($matiere->description) ?></p>
                <?php endif; ?>
                <div class="matiere-stats">
                    <div class="stat-item">
                        <span class="stat-number"><?= $total_documents ?></span>
                        <span>📄 Documents</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?= count($stats_types) ?></span>
                        <span>📂 Types</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Filtres types -->
        <?php if (!empty($stats_types)): ?>
        <div class="type-filters">
            <button class="type-filter active" data-type="all">🗂️ Tous (<?= $total_documents ?>)</button>
            <?php foreach ($stats_types as $stat):
                $type = getDocumentTypeLabel($stat->type_document ?? 'autre');
            ?>
            <button class="type-filter" data-type="<?= htmlspecialchars($stat->type_document) ?>">
                <?= $type['icon'] ?> <?= $type['label'] ?> (<?= $stat->count ?>)
            </button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Documents grid -->
        <?php if (empty($documents)): ?>
            <div class="no-documents" style="text-align: center; padding: 4rem 2rem; background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                <div style="font-size: 4rem; margin-bottom: 1rem;">📭</div>
                <h3>Aucun document disponible</h3>
                <p>Les documents seront bientôt disponibles pour cette matière.</p>
            </div>
        <?php else: ?>
            <div class="documents-grid" id="documentsGrid">
                <?php foreach ($documents as $doc): ?>
                    <?php 
                    $type = getDocumentTypeLabel($doc->type_document ?? 'autre');
                    $vue = $doc->vue_count ?? 0;
                    $tel = $doc->telechargement_count ?? 0;
                    $ext = strtoupper(pathinfo($doc->chemin_fichier, PATHINFO_EXTENSION));
                    $icon = $ext == 'PDF' ? '📕' : ($ext == 'DOCX' ? '📘' : '📄');
                    ?>
                    <div class="document-card" data-type="<?= htmlspecialchars($doc->type_document ?? '') ?>">
                        <div class="document-card-header">
                            <div class="document-type-badge" style="background: <?= $type['color'] ?? '#3498db' ?>20; color: <?= $type['color'] ?? '#3498db' ?>;">
                                <?= $type['icon'] ?> <?= $type['label'] ?>
                            </div>
                            <div class="document-format"><?= $icon ?> <?= $ext ?></div>
                        </div>
                        
                        <h3 class="document-title"><?= htmlspecialchars($doc->titre) ?></h3>
                        
                        <?php if (!empty($doc->description)): ?>
                            <p class="document-description"><?= htmlspecialchars(substr($doc->description, 0, 120)) ?>...</p>
                        <?php endif; ?>
                        
                        <div class="document-meta">
                            <?php if ($doc->auteur): ?>
                                <span>👤 <?= htmlspecialchars($doc->auteur) ?></span>
                            <?php endif; ?>
                            <?php if ($doc->annee_scolaire): ?>
                                <span>📅 <?= htmlspecialchars($doc->annee_scolaire) ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="document-stats">
                            <span title="Vues">👁️ <?= $vue ?></span>
                            <span title="Téléchargements">📥 <?= $tel ?></span>
                            <span title="Ajouté">📅 <?= date('d/m/Y', strtotime($doc->created_at ?? 'now')) ?></span>
                        </div>
                        
                        <div class="document-actions">
                            <a href="document.php?id=<?= $doc->id ?>&action=view" class="btn-view" target="_blank">👁️ Consulter</a>
                            <a href="document.php?id=<?= $doc->id ?>&action=download" class="btn-download">📥 Télécharger</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination améliorée -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page_num > 1): ?>
                    <a href="?page=matiere&id=<?= $id ?>&p=<?= $page_num - 1 ?>" class="page-link prev">← Préc.</a>
                <?php endif; ?>
                <?php 
                $start = max(1, $page_num - 2);
                $end = min($total_pages, $page_num + 2);
                ?>
                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <a href="?page=matiere&id=<?= $id ?>&p=<?= $i ?>" class="page-link <?= $i == $page_num ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                <?php if ($page_num < $total_pages): ?>
                    <a href="?page=matiere&id=<?= $id ?>&p=<?= $page_num + 1 ?>" class="page-link next">Suiv. →</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.matiere-header { color: white; padding: 3rem 0; border-radius: 12px; margin-bottom: 2rem; position: relative; overflow: hidden; }
.matiere-header::before { content: ''; position: absolute; top: 0; right: 0; bottom: 0; width: 300px; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%); }
.matiere-breadcrumb a { color: rgba(255,255,255,0.9); }
.matiere-stats { display: flex; gap: 2rem; justify-content: center; margin-top: 1rem; }
.stat-item { text-align: center; }
.stat-number { font-size: 2.5rem; font-weight: 700; }
.stat-label { opacity: 0.9; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 1px; }
.type-filters { display: flex; flex-wrap: wrap; gap: 1rem; justify-content: center; margin: 2rem 0; }
.type-filter { padding: 0.75rem 1.5rem; background: white; border: 2px solid var(--ltp-gray-300); border-radius: 50px; cursor: pointer; transition: all 0.3s; font-weight: 600; }
.type-filter:hover, .type-filter.active { background: var(--ltp-gold); border-color: var(--ltp-gold); color: var(--ltp-blue); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255,179,71,0.3); }
.documents-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 2rem; }
.pagination { display: flex; justify-content: center; gap: 0.5rem; margin: 3rem 0; }
.page-link { padding: 0.75rem 1.25rem; border: 2px solid var(--ltp-gray-300); border-radius: 50px; text-decoration: none; font-weight: 600; transition: all 0.3s; }
.page-link:hover, .page-link.active { border-color: var(--ltp-gold); background: var(--ltp-gold); color: var(--ltp-blue); transform: translateY(-2px); }
.page-link.prev, .page-link.next { min-width: 100px; text-align: center; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const filters = document.querySelectorAll('.type-filter');
    const cards = document.querySelectorAll('.document-card');
    
    filters.forEach(btn => btn.addEventListener('click', () => {
        // Active
        filters.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        const type = btn.dataset.type;
        cards.forEach(card => {
            card.style.display = (type === 'all' || card.dataset.type === type) ? 'block' : 'none';
        });
    }));
});
</script>

