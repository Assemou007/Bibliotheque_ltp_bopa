<?php
// admin/filieres.php
require_once 'config.php';

$filieres = $pdo->query("
    SELECT f.*, COUNT(m.id) as nb_matieres
    FROM filieres f
    LEFT JOIN matieres m ON f.id = m.filiere_id
    GROUP BY f.id
    ORDER BY f.ordre
")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Gestion des filières - Admin</title>
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
                <h1>Gestion des filières</h1>
                <a href="filiere_ajouter.php" class="btn-primary">➕ Nouvelle filière</a>
            </div>
           
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Icône</th>
                        <th>Nom</th>
                        <th>Couleur</th>
                        <th>Matières</th>
                        <th>Ordre</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filieres as $f): ?>
                    <tr>
                        <td><?= $f->id ?></td>
                        <td style="font-size:1.5em;"><?= $f->icone ?></td>
                        <td><?= htmlspecialchars($f->nom) ?></td>
                        <td><span style="display:inline-block; width:20px; height:20px; background:<?= $f->couleur ?>; border-radius:4px;"></span> <?= $f->couleur ?></td>
                        <td><?= $f->nb_matieres ?></td>
                        <td><?= $f->ordre ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="filiere_editer.php?id=<?= $f->id ?>" class="btn-action btn-edit" title="Éditer">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="filiere_supprimer.php?id=<?= $f->id ?>" class="btn-action btn-delete" onclick="return confirm('Supprimer cette filière supprimera aussi toutes ses matières et documents associés. Confirmer ?')" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>