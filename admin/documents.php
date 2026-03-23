<?php
// admin/documents.php
require_once 'config.php';

$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

$total = $pdo->query("SELECT COUNT(*) FROM documents")->fetchColumn();
$total_pages = ceil($total / $per_page);

$documents = $pdo->query("
    SELECT d.*, m.nom as matiere_nom, f.nom as filiere_nom
    FROM documents d
    JOIN matieres m ON d.matiere_id = m.id
    JOIN filieres f ON m.filiere_id = f.id
    ORDER BY d.created_at DESC
    LIMIT $offset, $per_page
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des documents - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2>📚 LTP-BOPA</h2>
                <p>Administration</p>
            </div>
            <nav class="sidebar-nav">
                <ul>
<li><a href="index.php" class="active"><i class="fas fa-house"></i> Dashboard</a></li>
                    <li><a href="documents.php"><i class="fas fa-file-pdf"></i> Documents</a></li>
                    <li><a href="filieres.php"><i class="fas fa-school"></i> Filières</a></li>
                    <li><a href="matieres.php"><i class="fas fa-book"></i> Matières</a></li>
                    <li><a href="messages.php"><i class="fas fa-comment"></i> Messages publics</a></li>
                    <li><a href="contacts.php"><i class="fas fa-envelope"></i> Contacts</a></li>
                    <li><a href="faq.php"><i class="fas fa-question-circle"></i> FAQ</a></li>
                    <li><a href="statistiques.php"><i class="fas fa-chart-bar"></i> Statistiques</a></li>
                    <li><a href="parametres.php"><i class="fas fa-cog"></i> Paramètres</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                </ul>
            </nav>
        </aside>
       
        <main class="admin-main">
            <div class="admin-header">
                <h1>Gestion des documents</h1>
                <a href="document_ajouter.php" class="btn-primary">➕ Nouveau document</a>
            </div>
           
            <!-- Filtres -->
            <div class="admin-filters">
                <form method="GET" class="filter-form">
                    <input type="text" name="search" placeholder="Rechercher un document...">
                    <select name="filiere">
                        <option value="">Toutes filières</option>
                        <?php
                        $filieres = $pdo->query("SELECT * FROM filieres")->fetchAll();
                        foreach ($filieres as $f): ?>
                        <option value="<?= $f->id ?>"><?= htmlspecialchars($f->nom) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn-filter">Filtrer</button>
                </form>
            </div>
           
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Filière</th>
                        <th>matière</th>
                        <th>Type</th>
                        <th>Format</th>
                        <th>Vues</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documents as $doc): ?>
                    <tr>
                        <td><?= $doc->id ?></td>
                        <td><?= htmlspecialchars($doc->titre) ?></td>
                        <td><?= htmlspecialchars($doc->filiere_nom) ?></td>
                        <td><?= htmlspecialchars($doc->matiere_nom) ?></td>
                        <td><?= $doc->type_document ?></td>
                        <td><?= strtoupper($doc->format_fichier) ?></td>
                        <td><?= $doc->vue_count ?></td>
                        <td><?= date('d/m/Y', strtotime($doc->created_at)) ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="document_editer.php?id=<?= $doc->id ?>" class="btn-action btn-edit" title="Éditer">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="supprimer.php?id=<?= $doc->id ?>" class="btn-action btn-delete" onclick="return confirm('Supprimer ?')" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
           
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i=1; $i<=$total_pages; $i++): ?>
                <a href="?p=<?= $i ?>" class="page-link <?= $i==$page ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>