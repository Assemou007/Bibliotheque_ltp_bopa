<?php
// pages/recherche.php

$query = isset($_GET['q']) ? cleanInput($_GET['q']) : '';
$type = isset($_GET['type']) ? cleanInput($_GET['type']) : 'tout';
$page_num = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$per_page = 12;
$offset = ($page_num - 1) * $per_page;

$resultats = [];
$total = 0;

if (!empty($query) && strlen($query) >= 2) {
    // Recherche dans les documents
    $sql = "
        SELECT d.*,
               m.nom as matiere_nom,
               f.nom as filiere_nom,
               f.slug as filiere_slug,
               'document' as type_resultat,
               MATCH(d.titre, d.description) AGAINST(? IN NATURAL LANGUAGE MODE) as score
        FROM documents d
        JOIN matieres m ON d.matiere_id = m.id
        JOIN filieres f ON m.filiere_id = f.id
        WHERE MATCH(d.titre, d.description) AGAINST(? IN NATURAL LANGUAGE MODE)
          AND d.est_public = 1
    ";
   
    $params = [$query, $query];
   
    // Compter le total
    $count_sql = "
        SELECT COUNT(*) as total
        FROM documents d
        WHERE MATCH(d.titre, d.description) AGAINST(? IN NATURAL LANGUAGE MODE)
          AND d.est_public = 1
    ";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute([$query]);
    $total = $count_stmt->fetch()->total;
   
    // Récupérer les résultats paginés
    $sql .= " ORDER BY score DESC LIMIT ? OFFSET ?";
    $params[] = $per_page;
    $params[] = $offset;
   
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $resultats = $stmt->fetchAll();
   
    // Loguer la recherche
    logAction($pdo, 'recherche', 'recherche');
    updateDailyStats($pdo, 'recherches_total');
}

$total_pages = $total > 0 ? ceil($total / $per_page) : 0;
?>

<div class="search-page">
    <h1>Rechercher des documents</h1>
   
    <div class="search-form-container">
        <form action="" method="get" class="search-form">
            <input type="hidden" name="page" value="recherche">
            <div class="search-large">
                <input type="search"
                       name="q"
                       value="<?= htmlspecialchars($query) ?>"
                       placeholder="Tapez votre recherche (cours, TD, exercices...)"
                       minlength="2"
                       required>
                <button type="submit">Rechercher</button>
            </div>
           
            <?php if (!empty($query)): ?>
            <div class="search-filters">
                <span class="filter-label">Filtrer par :</span>
                <select name="type" onchange="this.form.submit()">
                    <option value="tout" <?= $type == 'tout' ? 'selected' : '' ?>>Tous les types</option>
                    <option value="cours" <?= $type == 'cours' ? 'selected' : '' ?>>📚 Cours</option>
                    <option value="td" <?= $type == 'td' ? 'selected' : '' ?>>✏️ TD</option>
                    <option value="tp" <?= $type == 'tp' ? 'selected' : '' ?>>🔧 TP</option>
                    <option value="exercices" <?= $type == 'exercices' ? 'selected' : '' ?>>📝 Exercices</option>
                    <option value="examen" <?= $type == 'examen' ? 'selected' : '' ?>>📄 Examens</option>
                </select>
            </div>
            <?php endif; ?>
        </form>
    </div>
   
    <?php if (!empty($query)): ?>
        <div class="search-results-info">
            <p>
                <?php if ($total > 0): ?>
                    <strong><?= $total ?></strong> résultat(s) pour "<strong><?= htmlspecialchars($query) ?></strong>"
                <?php else: ?>
                    Aucun résultat pour "<strong><?= htmlspecialchars($query) ?></strong>"
                <?php endif; ?>
            </p>
        </div>
       
        <?php if (!empty($resultats)): ?>
            <div class="search-results-grid">
                <?php foreach ($resultats as $doc):
                    $type = getDocumentTypeLabel($doc->type_document);
                ?>
                <div class="search-result-card">
                    <div class="result-header">
                        <span class="document-type"><?= $type['icon'] ?> <?= $type['label'] ?></span>
                        <span class="document-format">📄 <?= strtoupper($doc->format_fichier) ?></span>
                    </div>
                   
                    <h3><?= htmlspecialchars($doc->titre) ?></h3>
                   
                    <div class="document-meta">
                        <span class="meta-item">📁 <?= htmlspecialchars($doc->filiere_nom) ?></span>
                        <span class="meta-item">📘 <?= htmlspecialchars($doc->matiere_nom) ?></span>
                    </div>
                   
                    <p class="document-description">
                        <?= htmlspecialchars(substr($doc->description, 0, 120)) ?>...
                    </p>
                   
                    <div class="result-footer">
                        <a href="assets/uploads/<?= $doc->chemin_fichier ?>" class="btn-view" target="_blank">
                            Consulter le document
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
           
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page_num > 1): ?>
                    <a href="?page=recherche&q=<?= urlencode($query) ?>&p=<?= $page_num - 1 ?>" class="page-link">←</a>
                <?php endif; ?>
               
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=recherche&q=<?= urlencode($query) ?>&p=<?= $i ?>"
                       class="page-link <?= $i == $page_num ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
               
                <?php if ($page_num < $total_pages): ?>
                    <a href="?page=recherche&q=<?= urlencode($query) ?>&p=<?= $page_num + 1 ?>" class="page-link">→</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
       
    <?php else: ?>
        <div class="search-tips">
            <h2>💡 Conseils de recherche</h2>
            <ul>
                <li>Utilisez des mots-clés précis (ex: "algorithmique", "marketing")</li>
                <li>Cherchez par type de document (ex: "cours", "TD", "examen")</li>
                <li>Associez une matière et un type (ex: "mathématiques exercices")</li>
            </ul>
        </div>
    <?php endif; ?>
</div>