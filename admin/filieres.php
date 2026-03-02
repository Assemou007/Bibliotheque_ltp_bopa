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
                    <li><a href="index.php" class="active">🏠 Dashboard</a></li>
                    <li><a href="documents.php">📄 Documents</a></li>
                    <li><a href="filieres.php">🏫 Filières</a></li>
                    <li><a href="matieres.php">📘 Matières</a></li>
                    <li><a href="messages.php">💬 Messages publics</a></li>
                    <li><a href="contacts.php">📧 Contacts</a></li>
                    <li><a href="faq.php">❓ FAQ</a></li>
                    <li><a href="statistiques.php">📊 Statistiques</a></li>
                    <li><a href="parametres.php">⚙️ Paramètres</a></li>
                    <li><a href="logout.php">🚪 Déconnexion</a></li>
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
                            <a href="filiere_editer.php?id=<;?= $f->id ?>" class="btn-edit">✏️</a>
                            <a href="filiere_supprimer.php?id=<;?= $f->id ?>" class="btn-delete" onclick="return confirm('Supprimer cette filière supprimera aussi toutes ses matières et documents associés. Confirmer ?')">🗑️</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>