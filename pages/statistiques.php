<?php
// pages/statistiques.php

// Statistiques générales
$stats = [
    'documents' => $pdo->query("SELECT COUNT(*) as count FROM documents WHERE est_public = 1")->fetch()->count,
    'filieres' => $pdo->query("SELECT COUNT(*) as count FROM filieres")->fetch()->count,
    'matieres' => $pdo->query("SELECT COUNT(*) as count FROM matieres")->fetch()->count,
    'messages' => $pdo->query("SELECT COUNT(*) as count FROM messages_publics WHERE statut = 'approuve'")->fetch()->count,
    'vues_total' => $pdo->query("SELECT SUM(vue_count) as total FROM documents")->fetch()->total ?? 0,
    'telechargements_total' => $pdo->query("SELECT SUM(telechargement_count) as total FROM documents")->fetch()->total ?? 0
];

// Documents par type
$docs_par_type = $pdo->query("
    SELECT type_document, COUNT(*) as count
    FROM documents
    WHERE est_public = 1
    GROUP BY type_document
    ORDER BY count DESC
")->fetchAll();

// Documents par filière
$docs_par_filiere = $pdo->query("
    SELECT f.nom, f.couleur, COUNT(d.id) as count
    FROM filieres f
    LEFT JOIN matieres m ON f.id = m.filiere_id
    LEFT JOIN documents d ON m.id = d.matiere_id AND d.est_public = 1
    GROUP BY f.id
    ORDER BY count DESC
")->fetchAll();

// Top documents consultés
$top_documents = $pdo->query("
    SELECT d.titre, d.vue_count, d.telechargement_count, m.nom as matiere_nom
    FROM documents d
    JOIN matieres m ON d.matiere_id = m.id
    WHERE d.est_public = 1
    ORDER BY d.vue_count DESC
    LIMIT 10
")->fetchAll();

// Activité récente (30 derniers jours)
$activite_recente = $pdo->query("
    SELECT DATE(created_at) as date, COUNT(*) as count
    FROM logs_acces
    WHERE created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(created_at)
    ORDER BY date DESC
")->fetchAll();

// Statistiques journalières
$journalier = $pdo->query("
    SELECT * FROM statistiques_journalieres
    ORDER BY date_jour DESC
    LIMIT 7
")->fetchAll();

logAction($pdo, 'statistiques', 'vue');
?>

<div class="statistiques-page">
    <div class="stats-header">
        <h1>📊 Statistiques de la bibliothèque</h1>
        <p class="subtitle">Découvrez les chiffres clés et l'activité de la plateforme</p>
    </div>
   
    <!-- Cartes statistiques principales -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon">📚</div>
            <div class="stat-detail">
                <span class="stat-value"><?= number_format($stats['documents'], 0, ',', ' ') ?></span>
                <span class="stat-label">Documents</span>
            </div>
        </div>
       
        <div class="stat-card">
            <div class="stat-icon">🏫</div>
            <div class="stat-detail">
                <span class="stat-value"><?= $stats['filieres'] ?></span>
                <span class="stat-label">Filières</span>
            </div>
        </div>
       
        <div class="stat-card">
            <div class="stat-icon">📘</div>
            <div class="stat-detail">
                <span class="stat-value"><?= $stats['matieres'] ?></span>
                <span class="stat-label">Matières</span>
            </div>
        </div>
       
        <div class="stat-card">
            <div class="stat-icon">💬</div>
            <div class="stat-detail">
                <span class="stat-value"><?= $stats['messages'] ?></span>
                <span class="stat-label">Messages</span>
            </div>
        </div>
       
        <div class="stat-card">
            <div class="stat-icon">👁️</div>
            <div class="stat-detail">
                <span class="stat-value"><?= number_format($stats['vues_total'], 0, ',', ' ') ?></span>
                <span class="stat-label">Vues totales</span>
            </div>
        </div>
       
        <div class="stat-card">
            <div class="stat-icon">📥</div>
            <div class="stat-detail">
                <span class="stat-value"><?= number_format($stats['telechargements_total'], 0, ',', ' ') ?></span>
                <span class="stat-label">Téléchargements</span>
            </div>
        </div>
    </div>
   
    <div class="stats-grid">
        <!-- Graphique répartition par type -->
        <div class="stats-chart-card">
            <h2>📊 Répartition par type de document</h2>
            <div class="chart-container">
                <?php
                $total = array_sum(array_column($docs_par_type, 'count'));
                $colors = ['#3498db', '#2ecc71', '#f39c12', '#e74c3c', '#9b59b6', '#1abc9c'];
                $i = 0;
                ?>
                <div class="pie-chart">
                    <?php foreach ($docs_par_type as $type):
                        $pourcentage = round(($type->count / $total) * 100, 1);
                        $type_label = getDocumentTypeLabel($type->type_document);
                        $color = $colors[$i % count($colors)];
                        $i++;
                    ?>
                    <div class="chart-legend-item">
                        <span class="color-dot" style="background-color: <?= $color ?>"></span>
                        <span class="legend-label"><?= $type_label['icon'] ?> <?= $type_label['label'] ?></span>
                        <span class="legend-value"><?= $type->count ?> (<?= $pourcentage ?>%)</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
       
        <!-- Documents par filière -->
        <div class="stats-chart-card">
            <h2>📚 Documents par filière</h2>
            <div class="bar-chart">
                <?php foreach ($docs_par_filiere as $filiere):
                    $max = max(array_column($docs_par_filiere, 'count'));
                    $largeur = $max > 0 ? ($filiere->count / $max) * 100 : 0;
                ?>
                <div class="bar-item">
                    <span class="bar-label" style="color: <?= $filiere->couleur ?>">
                        <?= htmlspecialchars(substr($filiere->nom, 0, 30)) ?>...
                    </span>
                    <div class="bar-container">
                        <div class="bar" style="width: <?= $largeur ?>%; background-color: <?= $filiere->couleur ?>">
                            <span class="bar-value"><?= $filiere->count ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
   
    <!-- Top documents -->
    <div class="top-documents-card">
        <h2>🔥 Top 10 des documents les plus consultés</h2>
        <div class="top-list">
            <?php foreach ($top_documents as $index => $doc): ?>
            <div class="top-item">
                <span class="top-rank">#<?= $index + 1 ?></span>
                <span class="top-title"><?= htmlspecialchars($doc->titre) ?></span>
                <span class="top-meta"><?= htmlspecialchars($doc->matiere_nom) ?></span>
                <span class="top-stats">
                    <span class="stat" title="Vues">👁️ <?= $doc->vue_count ?></span>
                    <span class="stat" title="Téléchargements">📥 <?= $doc->telechargement_count ?></span>
                </span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
   
    <!-- Activité récente -->
    <div class="activity-card">
        <h2>📈 Activité des 30 derniers jours</h2>
        <div class="activity-chart">
            <div class="activity-grid">
                <?php
                $max_activity = !empty($activite_recente) ? max(array_column($activite_recente, 'count')) : 1;
                foreach ($activite_recente as $i => $jour):
                    $hauteur = ($jour->count / $max_activity) * 100;
                ?>
                <div class="activity-bar-container" title="<?= $jour->date ?> - <?= $jour->count ?> actions">
                    <div class="activity-bar" style="height: <?= $hauteur ?>%"></div>
                    <div class="activity-label"><?= date('d/m', strtotime($jour->date)) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
   
    <!-- Derniers 7 jours -->
    <?php if (!empty($journalier)): ?>
    <div class="daily-stats-card">
        <h2>📅 Statistiques des 7 derniers jours</h2>
        <table class="daily-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Vues</th>
                    <th>Téléchargements</th>
                    <th>Recherches</th>
                    <th>Messages</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($journalier as $jour): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($jour->date_jour)) ?></td>
                    <td><?= $jour->vues_total ?></td>
                    <td><?= $jour->telechargements_total ?></td>
                    <td><?= $jour->recherches_total ?></td>
                    <td><?= $jour->messages_publics_total ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
